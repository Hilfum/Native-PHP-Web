<?php
include 'koneksi.php';
session_start();

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
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
                <div class="hamburger-menu-header">
                    <span>Menu</span>
                    <button class="close-hamburger" onclick="closeMenu(event)" aria-label="Tutup">&#10005;</button>
                </div>
                <div class="user-info">
                    Login sebagai: <b><?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : ''; ?></b>
                </div>
                <a href="#" class="logout-button" onclick="handleLogout(event)" style="color:#222;font-weight:bold;">
                    <span style="margin-right:6px;">&#x1F511;</span> Keluar (Logout)
                </a>
            </div>
        </div>
    </div>
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

            <label>Tipe Berkas</label>
            <div class="checkbox-group">
                <label><input type="checkbox" name="tipe_berkas[]" value="BPHTB"> BPHTB </label>
                <label><input type="checkbox" name="tipe_berkas[]" value="Mutasi Nama & Pembetulan"> Mutasi Nama & Pembetulan </label>
                <label><input type="checkbox" name="tipe_berkas[]" value="Objek Pajak Baru"> Objek Pajak Baru </label>
                <label><input type="checkbox" name="tipe_berkas[]" value="Mutasi Bagian"> Mutasi Bagian </label>
            </div>

            <label for="tanggal_masuk" style="margin-top:10px;">Tanggal Masuk Berkas</label>
            <div class="input-date-group">
                <span class="calendar-icon">&#128197;</span>
                <input type="date" id="tanggal_masuk" name="tanggal_masuk" required>
            </div>

            <div class="button-group">
                <button type="submit">Simpan Data</button>
                <a href="show.php" class="button-secondary-input">Lihat Data</a>
            </div>
        </form>
    </div>
    <script>
    document.querySelector('input[name="nop"]').addEventListener('input', function(e) {
        // Hapus semua karakter non-digit
        let value = this.value.replace(/\D/g, '');
        
        // Batasi hingga 18 digit
        if (value.length > 18) {
            value = value.slice(0, 18);
        }
        
        // Format NOP dengan titik
        let formattedValue = '';
        for (let i = 0; i < value.length; i++) {
            if (i === 2 || i === 4 || i === 7 || i === 10 || i === 13 || i === 17) {
                formattedValue += '.';
            }
            formattedValue += value[i];
        }
        
        // Update nilai input
        this.value = formattedValue;
    });

    document.querySelector('form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            if (data.success) {
                // Reset form instead of redirecting
                this.reset();
                // Reset NOP field to show placeholder
                document.querySelector('input[name="nop"]').value = '';
            }
        })
        .catch(error => {
            alert('Terjadi kesalahan: ' + error);
        });
    });

    function handleLogout(e) {
        e.preventDefault();
        if(confirm('Apakah Anda yakin ingin logout?')) {
            window.location.href = 'logout.php?confirm=yes';
        }
    }

    function toggleMenu(event) {
        event.stopPropagation();
        const menu = document.querySelector('.hamburger-menu-container');
        menu.classList.toggle('active');
        if (menu.classList.contains('active')) {
            document.addEventListener('click', closeMenuOnClickOutside);
        }
    }

    function closeMenu(event) {
        event.stopPropagation();
        document.querySelector('.hamburger-menu-container').classList.remove('active');
        document.removeEventListener('click', closeMenuOnClickOutside);
    }

    function closeMenuOnClickOutside(e) {
        const menu = document.querySelector('.hamburger-menu-container');
        if (!menu.contains(e.target)) {
            menu.classList.remove('active');
            document.removeEventListener('click', closeMenuOnClickOutside);
        }
    }
    </script>
</body>
</html>