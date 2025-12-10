# How to Activate the Formal Verification Module

## Step 1: Install Python Dependencies

The verification script requires a MySQL connector library. Install it using one of these methods:

### Option A: Using pip (Recommended)

```bash
pip install pymysql
```

### Option B: Using pip3 (if pip doesn't work)

```bash
pip3 install pymysql
```

### Option C: Using python -m pip

```bash
python -m pip install pymysql
```

### Option D: Alternative MySQL connector

If pymysql doesn't work, try:

```bash
pip install mysql-connector-python
```

**Note:** On Windows with XAMPP, you may need to use the full path to pip:

```bash
C:\Python3x\Scripts\pip.exe install pymysql
```

## Step 2: Verify Python Installation

Check if Python is installed and accessible:

```bash
# Check Python version
python --version
# OR
python3 --version
```

You should see something like: `Python 3.x.x`

## Step 3: Test the Python Script Manually

Navigate to your project directory and test the script:

```bash
cd C:\xampp\htdocs\SupplyOfficeInventory
python python/verify_inventory.py
```

**Expected Output:** You should see JSON output with verification results. If you see an error, note it for troubleshooting.

## Step 4: Verify File Paths

Make sure the following files exist:

- ✅ `python/verify_inventory.py` (should exist)
- ✅ `admin/api/run_verification.php` (should exist)
- ✅ `admin/verification_dashboard.php` (should exist)

## Step 5: Access the Verification Dashboard

1. **Start your XAMPP server** (Apache and MySQL)
2. **Open your browser** and navigate to:
   ```
   http://localhost/SupplyOfficeInventory/admin/verification_dashboard.php
   ```
3. **Login** with your admin credentials
4. **Click "Run Verification"** button

## Step 6: Troubleshooting

### Issue: "MySQL connector not found"

**Solution:** Install pymysql (see Step 1)

### Issue: "Python command not found"

**Solution:**

- On Windows, you may need to add Python to PATH
- Or modify `admin/api/run_verification.php` line 40-45 to use full path:
  ```php
  $pythonCmd = 'C:\\Python3x\\python.exe'; // Replace with your Python path
  ```

### Issue: "Verification script not found"

**Solution:**

- Check that `python/verify_inventory.py` exists
- Verify the path in `admin/api/run_verification.php` (line 26-27)

### Issue: "Database connection failed"

**Solution:**

- Ensure MySQL is running in XAMPP
- Check database credentials in `python/verify_inventory.py` (lines 20-25)
- Verify database name is `soi_db`

### Issue: "Permission denied" or script won't execute

**Solution:**

- On Linux/Mac: `chmod +x python/verify_inventory.py`
- On Windows: Usually not needed, but ensure file isn't blocked

## Quick Test Checklist

- [ ] Python is installed (`python --version` works)
- [ ] pymysql is installed (`pip list | grep pymysql`)
- [ ] MySQL is running in XAMPP
- [ ] Database `soi_db` exists
- [ ] Can access `verification_dashboard.php` in browser
- [ ] Can click "Run Verification" button
- [ ] See results (even if empty)

## Still Having Issues?

1. **Check browser console** (F12) for JavaScript errors
2. **Check PHP error logs** in XAMPP (usually in `xampp/apache/logs/error.log`)
3. **Test Python script directly** from command line first
4. **Verify database connection** by testing with a simple PHP script

## Need Help?

If the verification still doesn't work, check:

- PHP error logs
- Browser developer console (F12)
- Command line output when running Python script directly
