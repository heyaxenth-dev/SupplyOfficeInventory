<?php
/**
 * Diagnostic script to test verification setup
 * Access this file directly to check if everything is configured correctly
 */

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>

<head>
    <title>Verification Setup Test</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        margin: 20px;
    }

    .success {
        color: green;
    }

    .error {
        color: red;
    }

    .warning {
        color: orange;
    }

    pre {
        background: #f5f5f5;
        padding: 10px;
        border-radius: 5px;
    }

    .test-item {
        margin: 10px 0;
        padding: 10px;
        border-left: 3px solid #ccc;
    }
    </style>
</head>

<body>
    <h1>Verification Setup Diagnostic</h1>

    <?php
    $basePath = dirname(dirname(__DIR__));
    $pythonScript = $basePath . DIRECTORY_SEPARATOR . 'python' . DIRECTORY_SEPARATOR . 'verify_inventory.py';
    
    echo "<h2>1. File Paths</h2>";
    echo "<div class='test-item'>";
    echo "<strong>Project Root:</strong> " . htmlspecialchars($basePath) . "<br>";
    echo "<strong>Python Script Path:</strong> " . htmlspecialchars($pythonScript) . "<br>";
    
    if (file_exists($pythonScript)) {
        echo "<span class='success'>✓ Python script file exists</span><br>";
        echo "<strong>File Size:</strong> " . filesize($pythonScript) . " bytes<br>";
    } else {
        echo "<span class='error'>✗ Python script file NOT found!</span><br>";
    }
    echo "</div>";
    
    echo "<h2>2. Python Detection</h2>";
    echo "<div class='test-item'>";
    $pythonCommands = ['python', 'python3', 'py'];
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $pythonCommands = ['py', 'python', 'python3'];
    }
    
    $foundPython = false;
    foreach ($pythonCommands as $cmd) {
        $testOutput = @shell_exec("$cmd --version 2>&1");
        if (!empty($testOutput) && strpos($testOutput, 'Python') !== false) {
            echo "<span class='success'>✓ Found Python: <strong>$cmd</strong></span><br>";
            echo "<strong>Version:</strong> " . htmlspecialchars(trim($testOutput)) . "<br>";
            $foundPython = $cmd;
            break;
        } else {
            echo "<span class='warning'>✗ Command '$cmd' not found or not Python</span><br>";
        }
    }
    
    if (!$foundPython) {
        echo "<span class='error'><strong>ERROR: No Python installation found!</strong></span><br>";
        echo "Please install Python 3.x and ensure it's in your system PATH.<br>";
    }
    echo "</div>";
    
    echo "<h2>3. Python Dependencies</h2>";
    echo "<div class='test-item'>";
    if ($foundPython) {
        // Test if pymysql is installed
        $testScript = "import sys; ";
        $testScript .= "try: import pymysql; print('pymysql: OK'); ";
        $testScript .= "except ImportError: ";
        $testScript .= "try: import mysql.connector; print('mysql.connector: OK'); ";
        $testScript .= "except ImportError: print('ERROR: No MySQL connector found'); sys.exit(1)";
        
        $testFile = tempnam(sys_get_temp_dir(), 'test_python_');
        file_put_contents($testFile . '.py', $testScript);
        
        $output = @shell_exec("$foundPython \"$testFile.py\" 2>&1");
        unlink($testFile . '.py');
        
        if (strpos($output, 'OK') !== false) {
            echo "<span class='success'>✓ MySQL connector found: " . htmlspecialchars(trim($output)) . "</span><br>";
        } else {
            echo "<span class='error'>✗ MySQL connector NOT found!</span><br>";
            echo "<strong>Output:</strong> " . htmlspecialchars($output) . "<br>";
            echo "<strong>Solution:</strong> Run: <code>pip install pymysql</code><br>";
        }
    } else {
        echo "<span class='warning'>Skipped (Python not found)</span><br>";
    }
    echo "</div>";
    
    echo "<h2>4. Database Connection Test</h2>";
    echo "<div class='test-item'>";
    require_once '../config/conn.php';
    
    if ($conn->connect_error) {
        echo "<span class='error'>✗ Database connection failed: " . htmlspecialchars($conn->connect_error) . "</span><br>";
    } else {
        echo "<span class='success'>✓ Database connection successful</span><br>";
        echo "<strong>Database:</strong> soi_db<br>";
        
        // Check if inventory table exists
        $result = $conn->query("SHOW TABLES LIKE 'inventory'");
        if ($result->num_rows > 0) {
            echo "<span class='success'>✓ Inventory table exists</span><br>";
            
            $count = $conn->query("SELECT COUNT(*) as count FROM inventory");
            $row = $count->fetch_assoc();
            echo "<strong>Total Items:</strong> " . $row['count'] . "<br>";
        } else {
            echo "<span class='warning'>⚠ Inventory table does not exist</span><br>";
        }
    }
    echo "</div>";
    
    echo "<h2>5. Script Execution Test</h2>";
    echo "<div class='test-item'>";
    if ($foundPython && file_exists($pythonScript)) {
        echo "<strong>Attempting to run verification script...</strong><br><br>";
        
        $command = escapeshellarg($foundPython) . " " . escapeshellarg($pythonScript) . " 2>&1";
        $output = @shell_exec($command);
        
        if (!empty($output)) {
            echo "<strong>Output:</strong><br>";
            echo "<pre>" . htmlspecialchars($output) . "</pre>";
            
            // Try to parse as JSON
            $json = json_decode($output, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                echo "<span class='success'>✓ Script executed successfully and returned valid JSON</span><br>";
                echo "<strong>Status:</strong> " . htmlspecialchars($json['status'] ?? 'Unknown') . "<br>";
            } else {
                echo "<span class='error'>✗ Script output is not valid JSON</span><br>";
                echo "<strong>JSON Error:</strong> " . json_last_error_msg() . "<br>";
            }
        } else {
            echo "<span class='error'>✗ Script produced no output</span><br>";
            echo "This usually means Python couldn't execute the script or there was a fatal error.<br>";
        }
    } else {
        echo "<span class='warning'>Skipped (Python or script not found)</span><br>";
    }
    echo "</div>";
    
    echo "<h2>Summary</h2>";
    echo "<div class='test-item'>";
    if ($foundPython && file_exists($pythonScript)) {
        echo "<p><strong>Setup Status:</strong> ";
        if (!empty($output) && json_last_error() === JSON_ERROR_NONE) {
            echo "<span class='success'>✓ READY - Verification should work!</span></p>";
        } else {
            echo "<span class='warning'>⚠ PARTIAL - Some issues detected. Check above for details.</span></p>";
        }
    } else {
        echo "<p><strong>Setup Status:</strong> <span class='error'>✗ NOT READY - Fix the issues above first.</span></p>";
    }
    echo "</div>";
    ?>

    <hr>
    <p><a href="verification_dashboard.php">← Back to Verification Dashboard</a></p>
</body>

</html>