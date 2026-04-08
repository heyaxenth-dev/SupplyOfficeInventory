#!/usr/bin/env python3
"""
Formal Verification Algorithm for Supply Office Inventory System
Performs comprehensive checks on inventory data integrity
"""

import json
import sys
from datetime import datetime
from collections import defaultdict

try:
    import pymysql
except ImportError:
    try:
        import mysql.connector as pymysql
    except ImportError:
        print(json.dumps({
            "status": "ERROR",
            "message": "MySQL connector not found. Install pymysql or mysql-connector-python",
            "summary": {"total_items": 0, "total_transactions": 0, "error_count": 0, "warning_count": 0},
            "errors": [],
            "warnings": []
        }), file=sys.stderr)
        sys.exit(1)

# Database configuration
DB_CONFIG = {
    'host': 'localhost',
    'user': 'root',
    'password': '',
    'database': 'soi_db',
    'charset': 'utf8mb4'
}

def get_db_connection():
    """Establish database connection"""
    try:
        if 'mysql.connector' in sys.modules:
            conn = pymysql.connect(**DB_CONFIG)
        else:
            conn = pymysql.connect(
                host=DB_CONFIG['host'],
                user=DB_CONFIG['user'],
                password=DB_CONFIG['password'],
                database=DB_CONFIG['database'],
                charset=DB_CONFIG['charset']
            )
        return conn
    except Exception as e:
        print(json.dumps({
            "status": "ERROR",
            "message": f"Database connection failed: {str(e)}",
            "summary": {"total_items": 0, "total_transactions": 0, "error_count": 0, "warning_count": 0},
            "errors": [],
            "warnings": []
        }), file=sys.stderr)
        sys.exit(1)

def check_table_exists(cursor, table_name):
    """Check if a table exists in the database"""
    cursor.execute(f"SHOW TABLES LIKE '{table_name}'")
    return cursor.fetchone() is not None

def check_negative_stock(cursor):
    """Check for items with negative stock quantities"""
    errors = []
    cursor.execute("SELECT id, item_name, stock_number, quantity FROM inventory WHERE quantity < 0")
    results = cursor.fetchall()
    
    for row in results:
        errors.append({
            "type": "NEGATIVE_STOCK",
            "severity": "ERROR",
            "message": f"Item '{row[1]}' (Stock: {row[2]}) has negative quantity: {row[3]}",
            "item_id": row[0],
            "item_name": row[1],
            "stock_number": row[2],
            "quantity": row[3]
        })
    
    return errors

def check_duplicate_skus(cursor):
    """Check for duplicate stock numbers (SKUs)"""
    errors = []
    cursor.execute("""
        SELECT stock_number, COUNT(*) as count, GROUP_CONCAT(id) as ids, GROUP_CONCAT(item_name) as names
        FROM inventory 
        WHERE stock_number IS NOT NULL AND stock_number != ''
        GROUP BY stock_number 
        HAVING count > 1
    """)
    results = cursor.fetchall()
    
    for row in results:
        errors.append({
            "type": "DUPLICATE_SKU",
            "severity": "ERROR",
            "message": f"Duplicate stock number '{row[0]}' found in {row[1]} items",
            "stock_number": row[0],
            "count": row[1],
            "item_ids": row[2].split(',') if row[2] else [],
            "item_names": row[3].split(',') if row[3] else []
        })
    
    return errors

def check_missing_categories(cursor):
    """Check for items without categories"""
    warnings = []
    cursor.execute("SELECT id, item_name, stock_number FROM inventory WHERE category IS NULL OR category = ''")
    results = cursor.fetchall()
    
    for row in results:
        warnings.append({
            "type": "MISSING_CATEGORY",
            "severity": "WARNING",
            "message": (
                f"{row[1]} (stock {row[2]}): No category is set. "
                f"Choose a category when you edit this item."
            ),
            "item_id": row[0],
            "item_name": row[1],
            "stock_number": row[2]
        })
    
    return warnings

def check_orphan_transactions(cursor):
    """Check for transactions referencing non-existent items"""
    errors = []
    
    if check_table_exists(cursor, 'transactions'):
        cursor.execute("""
            SELECT t.id, t.item_id, t.transaction_type, t.quantity, t.transaction_date
            FROM transactions t
            LEFT JOIN inventory i ON t.item_id = i.id
            WHERE i.id IS NULL
        """)
        results = cursor.fetchall()
        
        for row in results:
            errors.append({
                "type": "ORPHAN_TRANSACTION",
                "severity": "ERROR",
                "message": f"Transaction #{row[0]} references non-existent item_id: {row[1]}",
                "transaction_id": row[0],
                "item_id": row[1],
                "transaction_type": row[2],
                "quantity": row[3],
                "transaction_date": str(row[4]) if row[4] else None
            })
    
    return errors

def check_stock_mismatch(cursor):
    """Check for stock quantity mismatches (if transactions table exists)"""
    errors = []
    
    if check_table_exists(cursor, 'transactions'):
        cursor.execute("""
            SELECT 
                i.id,
                i.item_name,
                i.stock_number,
                i.quantity as current_quantity,
                COALESCE(SUM(CASE WHEN t.transaction_type = 'IN' THEN t.quantity ELSE -t.quantity END), 0) as computed_quantity
            FROM inventory i
            LEFT JOIN transactions t ON i.id = t.item_id
            GROUP BY i.id, i.item_name, i.stock_number, i.quantity
            HAVING ABS(i.quantity - computed_quantity) > 0.01
        """)
        results = cursor.fetchall()
        
        for row in results:
            errors.append({
                "type": "STOCK_MISMATCH",
                "severity": "ERROR",
                "message": f"Item '{row[1]}' (Stock: {row[2]}) quantity mismatch: Current={row[3]}, Computed={row[4]}",
                "item_id": row[0],
                "item_name": row[1],
                "stock_number": row[2],
                "current_quantity": row[3],
                "computed_quantity": row[4]
            })
    
    return errors

# Must match admin/inventory.php ($lowStockThreshold) and verification UI (quantity -> status preview)
LOW_STOCK_THRESHOLD = 10


def check_status_quantity_consistency(cursor):
    """
    Quantity-based health checks for each inventory item.

    Since the `status` column was removed from the database, this function now
    focuses on simple, quantity-only rules that are easy for staff to read:
    - Items with 1–LOW_STOCK_THRESHOLD units left are flagged as \"Low stock\".
    """
    errors = []
    warnings = []

    cursor.execute(
        "SELECT id, item_name, stock_number, quantity FROM inventory"
    )
    for row in cursor.fetchall():
        item_id, name, sn, qty_raw = row
        try:
            quantity = int(qty_raw)
        except (TypeError, ValueError):
            quantity = 0

        if quantity < 0:
            # Negative quantities are already reported by check_negative_stock
            continue

        if 1 <= quantity <= LOW_STOCK_THRESHOLD:
            warnings.append({
                "type": "LOW_STOCK_WARNING",
                "severity": "WARNING",
                "message": (
                    f"{name} (stock {sn}): Only {quantity} unit(s) left. "
                    f"Consider reordering so this item does not run out."
                ),
                "item_id": item_id,
                "item_name": name,
                "stock_number": sn,
                "quantity": quantity,
                "threshold": LOW_STOCK_THRESHOLD,
            })

    return errors, warnings

def check_missing_stock_numbers(cursor):
    """Check for items without stock numbers"""
    warnings = []
    
    cursor.execute("SELECT id, item_name FROM inventory WHERE stock_number IS NULL OR stock_number = ''")
    results = cursor.fetchall()
    
    for row in results:
        warnings.append({
            "type": "MISSING_STOCK_NUMBER",
            "severity": "WARNING",
            "message": (
                f"{row[1]}: No stock number is assigned. Add one when you edit this item."
            ),
            "item_id": row[0],
            "item_name": row[1]
        })
    
    return warnings

def check_orphan_reservations(cursor):
    """Check for reservations referencing non-existent items"""
    errors = []
    
    if check_table_exists(cursor, 'reservations'):
        cursor.execute("""
            SELECT r.id, r.item_id, r.quantity, r.status, r.reservation_date
            FROM reservations r
            LEFT JOIN inventory i ON r.item_id = i.id
            WHERE i.id IS NULL
        """)
        results = cursor.fetchall()
        
        for row in results:
            errors.append({
                "type": "ORPHAN_RESERVATION",
                "severity": "ERROR",
                "message": f"Reservation #{row[0]} references non-existent item_id: {row[1]}",
                "reservation_id": row[0],
                "item_id": row[1],
                "quantity": row[2],
                "status": row[3],
                "reservation_date": str(row[4]) if row[4] else None
            })
    
    return errors

def main():
    """Main verification function"""
    conn = None
    try:
        conn = get_db_connection()
        cursor = conn.cursor()
        
        # Initialize result structure
        all_errors = []
        all_warnings = []
        
        # Get total counts
        cursor.execute("SELECT COUNT(*) FROM inventory")
        total_items = cursor.fetchone()[0]
        
        total_transactions = 0
        if check_table_exists(cursor, 'transactions'):
            cursor.execute("SELECT COUNT(*) FROM transactions")
            total_transactions = cursor.fetchone()[0]
        
        # Run all verification checks
        all_errors.extend(check_negative_stock(cursor))
        all_errors.extend(check_duplicate_skus(cursor))
        all_warnings.extend(check_missing_categories(cursor))
        all_errors.extend(check_orphan_transactions(cursor))
        all_errors.extend(check_stock_mismatch(cursor))
        
        status_errors, status_warnings = check_status_quantity_consistency(cursor)
        all_errors.extend(status_errors)
        all_warnings.extend(status_warnings)
        all_warnings.extend(check_missing_stock_numbers(cursor))
        all_errors.extend(check_orphan_reservations(cursor))
        
        # Determine overall status
        error_count = len(all_errors)
        warning_count = len(all_warnings)
        
        if error_count > 0:
            status = "FAIL"
        elif warning_count > 0:
            status = "WARN"
        else:
            status = "PASS"
        
        # Build result JSON
        result = {
            "status": status,
            "summary": {
                "total_items": total_items,
                "total_transactions": total_transactions,
                "error_count": error_count,
                "warning_count": warning_count
            },
            "errors": all_errors,
            "warnings": all_warnings
        }
        
        # Output JSON result
        print(json.dumps(result, indent=2))
        
    except Exception as e:
        print(json.dumps({
            "status": "ERROR",
            "message": f"Verification failed: {str(e)}",
            "summary": {"total_items": 0, "total_transactions": 0, "error_count": 0, "warning_count": 0},
            "errors": [],
            "warnings": []
        }), file=sys.stderr)
        sys.exit(1)
    finally:
        if conn:
            conn.close()

if __name__ == "__main__":
    main()

