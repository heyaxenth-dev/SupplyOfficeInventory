<?php
/**
 * Formal Verification API Endpoint
 * Executes Python verification script and returns JSON results
 */

header('Content-Type: application/json');

// Include authentication first (it will check and exit if not authenticated)
require_once '../authentication.php';

// Include database connection
require_once '../config/conn.php';

// Get the base path (adjust based on your server setup)
// __DIR__ is admin/api, so we go up two levels to get project root
$basePath = dirname(dirname(__DIR__));
$pythonScript = $basePath . DIRECTORY_SEPARATOR . 'python' . DIRECTORY_SEPARATOR . 'verify_inventory.py';

// Check if Python script exists
if (!file_exists($pythonScript)) {
    echo json_encode([
        'status' => 'ERROR',
        'message' => 'Verification script not found. Please ensure verify_inventory.py exists in the python directory.',
        'summary' => ['total_items' => 0, 'total_transactions' => 0, 'error_count' => 0, 'warning_count' => 0],
        'errors' => [],
        'warnings' => []
    ]);
    exit;
}

// Determine Python command (Windows-compatible detection)
$pythonCmd = null;

// Try different Python commands
$pythonCommands = ['python', 'python3', 'py'];
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    // On Windows, try 'py' launcher first, then 'python'
    $pythonCommands = ['py', 'python', 'python3'];
}

foreach ($pythonCommands as $cmd) {
    // Test if command exists
    $testOutput = @shell_exec("$cmd --version 2>&1");
    if (!empty($testOutput) && strpos($testOutput, 'Python') !== false) {
        $pythonCmd = $cmd;
        break;
    }
}

// If still not found, default to 'python'
if (empty($pythonCmd)) {
    $pythonCmd = 'python';
}

// Sanitize command to prevent injection
$pythonScript = escapeshellarg($pythonScript);
$command = "$pythonCmd $pythonScript 2>&1";

// Execute Python script with timeout
$descriptorspec = [
    0 => ['pipe', 'r'],  // stdin
    1 => ['pipe', 'w'],  // stdout
    2 => ['pipe', 'w']   // stderr
];

$process = proc_open($command, $descriptorspec, $pipes, $basePath);

if (!is_resource($process)) {
    echo json_encode([
        'status' => 'ERROR',
        'message' => 'Failed to execute verification script',
        'summary' => ['total_items' => 0, 'total_transactions' => 0, 'error_count' => 0, 'warning_count' => 0],
        'errors' => [],
        'warnings' => []
    ]);
    exit;
}

// Set timeout (30 seconds)
$timeout = 30;
$startTime = time();

// Read output
$stdout = '';
$stderr = '';

// Close stdin
fclose($pipes[0]);

// Read stdout and stderr
stream_set_blocking($pipes[1], false);
stream_set_blocking($pipes[2], false);

while (true) {
    $read = [$pipes[1], $pipes[2]];
    $write = null;
    $except = null;
    
    $changed = stream_select($read, $write, $except, 1);
    
    if ($changed === false) {
        break;
    }
    
    if (time() - $startTime > $timeout) {
        proc_terminate($process);
        echo json_encode([
            'status' => 'ERROR',
            'message' => 'Verification script timed out after ' . $timeout . ' seconds',
            'summary' => ['total_items' => 0, 'total_transactions' => 0, 'error_count' => 0, 'warning_count' => 0],
            'errors' => [],
            'warnings' => []
        ]);
        exit;
    }
    
    foreach ($read as $stream) {
        if ($stream === $pipes[1]) {
            $data = fread($stream, 8192);
            if ($data !== false) {
                $stdout .= $data;
            }
        } elseif ($stream === $pipes[2]) {
            $data = fread($stream, 8192);
            if ($data !== false) {
                $stderr .= $data;
            }
        }
    }
    
    // Check if process has terminated
    $status = proc_get_status($process);
    if (!$status['running']) {
        // Read any remaining output
        while (!feof($pipes[1])) {
            $stdout .= fread($pipes[1], 8192);
        }
        while (!feof($pipes[2])) {
            $stderr .= fread($pipes[2], 8192);
        }
        break;
    }
}

// Close pipes
fclose($pipes[1]);
fclose($pipes[2]);

// Get exit code
$exitCode = proc_close($process);

// Parse JSON output
$output = trim($stdout);
$errorOutput = trim($stderr);

// If there's stderr output, it might contain the JSON (Python errors go to stderr)
if (empty($output) && !empty($errorOutput)) {
    $output = $errorOutput;
}

// Try to parse JSON
$result = json_decode($output, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    // JSON parsing failed - provide more debugging info
    $debugInfo = [
        'status' => 'ERROR',
        'message' => 'Failed to parse verification results.',
        'summary' => ['total_items' => 0, 'total_transactions' => 0, 'error_count' => 0, 'warning_count' => 0],
        'errors' => [],
        'warnings' => [],
        'debug' => [
            'python_command' => $pythonCmd,
            'script_path' => $pythonScript,
            'exit_code' => $exitCode,
            'stdout_length' => strlen($output),
            'stderr_length' => strlen($errorOutput),
            'stdout_preview' => substr($output, 0, 500),
            'stderr_preview' => substr($errorOutput, 0, 500),
            'json_error' => json_last_error_msg()
        ]
    ];
    
    // If we have output but it's not JSON, show it
    if (!empty($output) || !empty($errorOutput)) {
        $debugInfo['message'] = 'Python script executed but returned invalid JSON. Check debug info below.';
    } else {
        $debugInfo['message'] = 'Python script produced no output. Check if Python and pymysql are installed correctly.';
    }
    
    echo json_encode($debugInfo, JSON_PRETTY_PRINT);
    exit;
}

// Return the parsed result
echo json_encode($result, JSON_PRETTY_PRINT);
?>