<?php
session_start();

if(isset($_GET['confirm']) && $_GET['confirm'] == 'yes') {
    $username = $_SESSION['username'];
    session_destroy();
    echo "<script>
        alert('Akun " . $username . " telah berhasil logout.');
        window.location.href = 'index.php';
    </script>";
    exit();
}
?>