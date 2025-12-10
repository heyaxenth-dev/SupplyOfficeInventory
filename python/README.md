# Formal Verification Module - Setup Guide

## Requirements

### Python Dependencies

Install the required Python package for MySQL connectivity:

```bash
# Option 1: Using pip (recommended)
pip install pymysql

# Option 2: Using mysql-connector-python (alternative)
pip install mysql-connector-python
```

### Python Version

- Python 3.6 or higher is required
- Verify installation: `python3 --version` or `python --version`

## Database Configuration

The script uses the following database configuration (from `admin/config/conn.php`):
- Host: `localhost`
- User: `root`
- Password: `` (empty by default)
- Database: `soi_db`

To modify these settings, edit the `DB_CONFIG` dictionary in `verify_inventory.py`.

## File Permissions

Ensure the Python script is executable:

```bash
chmod +x python/verify_inventory.py
```

## Testing the Script

You can test the script directly from the command line:

```bash
cd python
python3 verify_inventory.py
```

The script should output JSON to stdout. If there are errors, they will be printed to stderr.

## Verification Checks Performed

The script performs the following checks:

1. **Negative Stock** - Items with quantity < 0
2. **Duplicate SKUs** - Multiple items with the same stock number
3. **Missing Categories** - Items without category assignments
4. **Orphan Transactions** - Transactions referencing non-existent items
5. **Stock Mismatch** - Discrepancy between current quantity and computed quantity from transactions
6. **Invalid Status** - Status inconsistencies (e.g., "Out of Stock" with quantity > 0)
7. **Low Stock Warnings** - Items below threshold that should be marked as "Low Stock"
8. **Missing Stock Numbers** - Items without stock number assignments
9. **Orphan Reservations** - Reservations referencing non-existent items (if reservations table exists)

## Output Format

The script outputs JSON with the following structure:

```json
{
  "status": "PASS" | "WARN" | "FAIL" | "ERROR",
  "summary": {
    "total_items": 0,
    "total_transactions": 0,
    "error_count": 0,
    "warning_count": 0
  },
  "errors": [
    {
      "type": "ERROR_TYPE",
      "severity": "ERROR",
      "message": "Human-readable message",
      ...
    }
  ],
  "warnings": [
    {
      "type": "WARNING_TYPE",
      "severity": "WARNING",
      "message": "Human-readable message",
      ...
    }
  ]
}
```

## Troubleshooting

### "MySQL connector not found"
- Install pymysql: `pip install pymysql`

### "Database connection failed"
- Verify database credentials in `verify_inventory.py`
- Ensure MySQL server is running
- Check database name exists

### "Verification script not found"
- Verify the file path in `admin/api/run_verification.php`
- Ensure `python/verify_inventory.py` exists relative to project root

### "Python command not found"
- Ensure Python 3 is installed and in PATH
- On Windows, you may need to use `py` instead of `python3`

