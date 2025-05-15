document.getElementById('reset-records').addEventListener('click', function(e) {
    e.preventDefault();
    if (confirm('Are you sure you want to reset all post view counts? This action cannot be undone.')) {
        window.location.href = "<?php echo esc_url(admin_url('admin-post.php?action=tp_divi_reset_records')); ?>";
    }
});