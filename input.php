<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
include 'koneksi.php';

// Basic login check
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Batasi akses hanya untuk role loket
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'loket') {
    header("Location: show.php");
    exit();
}

// Get user's role
$stmt = $conn->prepare("SELECT r.name as role FROM users u 
                       JOIN roles r ON u.role_id = r.id 
                       WHERE u.username = ?");
$stmt->bind_param("s", $_SESSION['username']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$_SESSION['role'] = $user['role'];

// For now, all roles can access input page
// Later this can be modified to restrict access based on roles

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $nop = str_replace('.', '', $_POST['nop']);
    
    // Convert form data to JSON response
    header('Content-Type: application/json');
    
    // Validation checks
    if (strlen($nop) != 18 || !ctype_digit($nop)) {
        echo json_encode(['success' => false, 'message' => 'NOP harus 18 digit angka!']);
        exit();
    }
    
    if(!isset($_POST['tipe_berkas']) || !is_array($_POST['tipe_berkas'])) {
        echo json_encode(['success' => false, 'message' => 'Pilih minimal satu tipe berkas!']);
        exit();
    }
    
    $kelurahan_objek_pajak = $_POST['kelurahan_objek_pajak'];
    $kecamatan_objek_pajak = $_POST['kecamatan_objek_pajak'];
    $alamat_wajib_pajak = $_POST['alamat_wajib_pajak'];
    $alamat_objek_pajak = $_POST['alamat_objek_pajak'];
    
    $tipe_berkas = implode(', ', $_POST['tipe_berkas']); // Menggabungkan tipe berkas yang dicentang
    $tanggal_masuk = $_POST['tanggal_masuk']; // Ambil tanggal masuk

    $query = "INSERT INTO dokumen (nama, nop, kelurahan_objek_pajak, kecamatan_objek_pajak, alamat_wajib_pajak, alamat_objek_pajak, tipe_berkas, tanggal_masuk)
              VALUES ('$nama', '$nop', '$kelurahan_objek_pajak', '$kecamatan_objek_pajak', '$alamat_wajib_pajak', '$alamat_objek_pajak', '$tipe_berkas', '$tanggal_masuk')";

    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true, 'message' => 'Data berhasil disimpan']);
        exit();
    } else {
        echo json_encode(['success' => false, 'message' => 'Error: ' . mysqli_error($conn)]);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, maximum-scale=1.0, user-scalable=no">
    <title>Input Data Berkas</title>
    <link rel="stylesheet" href="css/input.css">
</head>
<body class="input-body">
    <div class="header">
        <div class="logo-container">
            <img src="gambar/Lambang_Kota_Kendari.png" alt="Logo Kota Kendari">
            <h2>Bapenda Kota Kendari</h2>
        </div>
        <div class="hamburger-menu-container">
            <div class="hamburger-icon" onclick="toggleMenu(event)">
                <span></span>
                <span></span>
                <span></span>
            </div>
            <div class="hamburger-dropdown" id="hamburgerDropdown">
                <button class="close-hamburger" onclick="closeMenu(event)" aria-label="Tutup">&#10005;</button>
                <div class="menu-row">
                    <div class="user-info highlight-user">
                        <span class="user-label">👤 Login sebagai :</span>
                        <?php
                        $role_labels = [
                            'petugas_loket' => 'Petugas Loket',
                            'verlap'        => 'Petugas Verifikasi Lapangan',
                            'kabid'         => 'Kepala Bidang',
                            'penetapan'     => 'Petugas Penetapan',
                            'op_baru'       => 'Petugas OP Baru',
                            'mutasi1'       => 'Petugas Mutasi Bagian',
                            'mutasi2'       => 'Petugas Mutasi Nama & Pembetulan'
                        ];
                        $display_name = isset($role_labels[$_SESSION['username']])
                            ? $role_labels[$_SESSION['username']]
                            : htmlspecialchars($_SESSION['username']);
                        ?>
                        <span class="user-name"><?php echo $display_name; ?></span>
                    </div>
                    <a href="#" class="logout-button" onclick="handleLogout(event)">
                        Keluar (Logout)
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="menu-overlay" id="menuOverlay"></div>
    <div class="container-input">
        <h2>INPUT BERKAS</h2>
        <form action="" method="POST">
            <label>Nama Wajib Pajak</label>
            <input type="text" 
                   name="nama" 
                   required 
                   placeholder="Masukkan nama wajib pajak">

            <label>NOP</label>
            <input type="text" 
                   name="nop" 
                   required 
                   maxlength="24"
                   placeholder="74.71.000.000.000.0000.0"
                   autocomplete="off">

            <label>Kelurahan Objek Pajak</label>
            <input type="text" 
                   name="kelurahan_objek_pajak" 
                   required
                   placeholder="Contoh: Kadia">

            <label>Kecamatan Objek Pajak</label>
            <input type="text" 
                   name="kecamatan_objek_pajak" 
                   required
                   placeholder="Contoh: Kadia">

            <label>Alamat Wajib Pajak</label>
            <input type="text" 
                   name="alamat_wajib_pajak" 
                   required
                   placeholder="Masukkan alamat lengkap wajib pajak">

            <label>Alamat Objek Pajak</label>
            <input type="text" 
                   name="alamat_objek_pajak" 
                   required
                   placeholder="Masukkan alamat lengkap objek pajak">

            <label for="tanggal_masuk" style="margin-top:10px;">Tanggal Masuk Berkas</label>
            <div class="input-date-group">
                <span class="calendar-icon">&#128197;</span>
                <input type="date" id="tanggal_masuk" name="tanggal_masuk" required
                       value="<?php echo date('Y-m-d'); ?>">
            </div>

            <label class="form-label"></label>
            <div class="checkbox-group">
                <div class="checkbox-item">
                    <input type="checkbox" id="bphtb" name="tipe_berkas[]" value="BPHTB">
                    <label for="bphtb">BPHTB</label>
                </div>
                <div class="checkbox-item">
                    <input type="checkbox" id="mutasi_nama" name="tipe_berkas[]" value="Mutasi Nama & Pembetulan">
                    <label for="mutasi_nama">Mutasi Nama & Pembetulan</label>
                </div>
                <div class="checkbox-item">
                    <input type="checkbox" id="op_baru" name="tipe_berkas[]" value="Objek Pajak Baru">
                    <label for="op_baru">Objek Pajak Baru</label>
                </div>
                <div class="checkbox-item">
                    <input type="checkbox" id="mutasi_bagian" name="tipe_berkas[]" value="Mutasi Bagian">
                    <label for="mutasi_bagian">Mutasi Bagian</label>
                </div>
            </div>

            <div class="button-group">
                <button type="submit" class="button-animated">Simpan Data</button>
                <a href="show.php" class="button-secondary-animated" id="lihatDataBtn">Lihat Data</a>
            </div>
        </form>
    </div>

    <script src="javascript/input.js"></script>
</body>
</html>