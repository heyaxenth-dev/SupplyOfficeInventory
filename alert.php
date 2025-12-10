<?php
// Check if session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check for status messages (both old format with 'status' and new format with 'status_text')
if (isset($_SESSION['status']) || isset($_SESSION['status_text'])) {
    // Get values with fallbacks
    $title = isset($_SESSION['status']) ? htmlspecialchars($_SESSION['status'], ENT_QUOTES, 'UTF-8') : 'Notification';
    $text = isset($_SESSION['status_text']) ? htmlspecialchars($_SESSION['status_text'], ENT_QUOTES, 'UTF-8') : '';
    $icon = isset($_SESSION['status_code']) ? htmlspecialchars($_SESSION['status_code'], ENT_QUOTES, 'UTF-8') : 'info';
    $button = isset($_SESSION['status_btn']) ? htmlspecialchars($_SESSION['status_btn'], ENT_QUOTES, 'UTF-8') : 'OK';
    
    // Map old icon names to new ones if needed
    $iconMap = [
        'success' => 'success',
        'error' => 'error',
        'warning' => 'warning',
        'info' => 'info'
    ];
    $icon = isset($iconMap[$icon]) ? $iconMap[$icon] : 'info';
    ?>
<script>
// Wait for DOM and SweetAlert2 to be ready
if (typeof Swal !== 'undefined') {
    Swal.fire({
        title: "<?php echo $title; ?>",
        text: "<?php echo $text; ?>",
        icon: "<?php echo $icon; ?>",
        confirmButtonText: "<?php echo $button; ?>",
        confirmButtonColor: '#3085d6'
    });
} else {
    // Fallback if SweetAlert2 is not loaded yet
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: "<?php echo $title; ?>",
                text: "<?php echo $text; ?>",
                icon: "<?php echo $icon; ?>",
                confirmButtonText: "<?php echo $button; ?>",
                confirmButtonColor: '#3085d6'
            });
        } else {
            // Ultimate fallback - use alert
            alert("<?php echo $title; ?>\n\n<?php echo $text; ?>");
        }
    });
}
</script>

<?php
    // Clear all status-related session variables
    unset($_SESSION['status']);
    unset($_SESSION['status_text']);
    unset($_SESSION['status_code']);
    unset($_SESSION['status_btn']);
}
?>