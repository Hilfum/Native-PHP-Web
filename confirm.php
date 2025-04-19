<?php
include 'koneksi.php';

function keterangan_handler($handler) {
    $map = [
        'verlap'   => 'Verifikasi Lapangan',
        'mutasi1'  => 'Mutasi Bagian',
        'mutasi2'  => 'Mutasi Nama dan Pembetulan',
        'kabid'    => 'Kepala Bidang',
        'loket'    => 'Petugas Loket',
        'bphtb'    => 'BPHTB',
        'op_baru'  => 'Objek Pajak Baru',
        'penetapan'=> 'Penetapan'
    ];
    return $map[$handler] ?? $handler;
}

$id = $_POST['id'] ?? null;
if (!$id) {
    echo json_encode(['success' => false, 'message' => 'ID tidak ditemukan']);
    exit;
}

// Ambil data dokumen
$query = "SELECT tipe_berkas, current_handler FROM dokumen WHERE id = $id";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

$current_handler = $row['current_handler'];
$tipe_berkas = $row['tipe_berkas'];

// Handler umum
$handler_umum = ['loket', 'verlap', 'penetapan', 'kabid'];

// Handler akhir sesuai prioritas
$prioritas = ['bphtb', 'mutasi1', 'mutasi2', 'op_baru'];
$handler_akhir = [];
$tipe_list = array_map('trim', explode(',', $tipe_berkas));
foreach ($tipe_list as $tipe) {
    if (stripos($tipe, 'BPHTB') !== false) $handler_akhir[] = 'bphtb';
    if (stripos($tipe, 'Mutasi Bagian') !== false) $handler_akhir[] = 'mutasi1';
    if (stripos($tipe, 'Mutasi Nama & Pembetulan') !== false) $handler_akhir[] = 'mutasi2';
    if (stripos($tipe, 'Objek Pajak Baru') !== false) $handler_akhir[] = 'op_baru';
}
// Urutkan handler akhir sesuai prioritas dan hilangkan duplikat
$handler_akhir = array_values(array_unique(array_intersect($prioritas, $handler_akhir)));

// Gabungkan semua handler: umum lalu akhir
$all_handler = array_merge($handler_umum, $handler_akhir);

// Cari handler berikutnya
$idx = array_search($current_handler, $all_handler);
$next_handler = null;
if ($idx !== false && isset($all_handler[$idx + 1])) {
    $next_handler = $all_handler[$idx + 1];
}

if ($next_handler) {
    // Update ke handler berikutnya
    $update = "UPDATE dokumen SET current_handler = '$next_handler' WHERE id = $id";
    mysqli_query($conn, $update);
    $response = [
        'success' => true,
        'next_handler' => $next_handler,
        'keterangan' => keterangan_handler($next_handler)
    ];
} else {
    // Jika sudah di handler terakhir, set status selesai
    $update = "UPDATE dokumen SET status = 'Selesai' WHERE id = $id";
    mysqli_query($conn, $update);
    $response = [
        'success' => true,
        'keterangan' => 'Selesai'
    ];
}

header('Content-Type: application/json');
echo json_encode($response);
?>