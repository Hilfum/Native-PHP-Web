<?php
session_start();
include 'koneksi.php';

if (!isset($_GET['id'])) {
    header('Location: show.php');
    exit;
}

$id = mysqli_real_escape_string($conn, $_GET['id']);

// Hanya handler yang sesuai yang boleh menghapus
$query = "SELECT current_handler FROM dokumen WHERE id = '$id'";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

if (!$row || ($_SESSION['role'] !== 'monitoring' && $row['current_handler'] !== $_SESSION['role'])) {
    // Tidak berhak menghapus
    header('Location: show.php?msg=forbidden');
    exit;
}

// Hapus data
$delete = mysqli_query($conn, "DELETE FROM dokumen WHERE id = '$id'");

if ($delete) {
    header('Location: show.php?msg=deleted');
} else {
    header('Location: show.php?msg=delete_failed');
}
exit;

mysqli_close($conn);
?>