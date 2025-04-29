<?php
include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $nop = $_POST['nop'];
    $kelurahan_objek_pajak = $_POST['kelurahan_objek_pajak'];
    $kecamatan_objek_pajak = $_POST['kecamatan_objek_pajak'];
    $alamat_wajib_pajak = $_POST['alamat_wajib_pajak'];
    $alamat_objek_pajak = $_POST['alamat_objek_pajak'];
    $tanggal_masuk = $_POST['tanggal_masuk']; // Tambahkan ini

    // Check if tipe_berkas is set and is array
    if (!isset($_POST['tipe_berkas']) || !is_array($_POST['tipe_berkas'])) {
        echo "<script>
            alert('Error: Pilih minimal satu tipe berkas!');
            history.back();
        </script>";
        exit();
    }
    
    $tipe_berkas = implode(", ", $_POST['tipe_berkas']);

    // Update data in the database, tambahkan tanggal_masuk
    $query = $conn->prepare("UPDATE dokumen SET nama = ?, nop = ?, kelurahan_objek_pajak = ?, kecamatan_objek_pajak = ?, alamat_wajib_pajak = ?, alamat_objek_pajak = ?, tipe_berkas = ?, tanggal_masuk = ? WHERE id = ?");
    $query->bind_param("ssssssssi", $nama, $nop, $kelurahan_objek_pajak, $kecamatan_objek_pajak, $alamat_wajib_pajak, $alamat_objek_pajak, $tipe_berkas, $tanggal_masuk, $id);

    if ($query->execute()) {
        echo "<script>
            alert('Data berhasil diupdate');
            window.location.href = 'show.php';
        </script>";
        exit();
    } else {
        echo "<script>
            alert('Error: " . $query->error . "');
            history.back();
        </script>";
    }

    $query->close();
} else {
    $id = $_GET['id'];
    $query = $conn->prepare("SELECT * FROM dokumen WHERE id = ?");
    $query->bind_param("i", $id);
    $query->execute();
    $result = $query->get_result();
    $row = $result->fetch_assoc();

    if (!$row) {
        die("Data not found.");
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Berkas</title>
    <link rel="stylesheet" href="css/edit.css">
</head>
<body class="input-body">
    <div class="header">
        <div class="logo-container">
            <img src="gambar/Lambang_Kota_Kendari.png" alt="Logo Kota Kendari">
            <h2>Bapenda Kota Kendari</h2>
        </div>
    </div>

    <div class="container-input">
        <h2>EDIT BERKAS</h2>
        <form action="edit.php" method="POST">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id']); ?>">

            <label>Nama Wajib Pajak</label>
            <input type="text" 
                   name="nama" 
                   value="<?php echo htmlspecialchars($row['nama']); ?>" 
                   required 
                   placeholder="Masukkan nama wajib pajak">

            <label>NOP</label>
            <input type="text" 
                   name="nop" 
                   value="<?php echo htmlspecialchars($row['nop']); ?>" 
                   required
                   maxlength="24"
                   placeholder="74.71.000.000.000.0000.0"
                   autocomplete="off">

            <label>Kelurahan Objek Pajak</label>
            <input type="text" 
                   name="kelurahan_objek_pajak" 
                   value="<?php echo htmlspecialchars($row['kelurahan_objek_pajak']); ?>" 
                   required
                   placeholder="Contoh: Kadia">

            <label>Kecamatan Objek Pajak</label>
            <input type="text" 
                   name="kecamatan_objek_pajak" 
                   value="<?php echo htmlspecialchars($row['kecamatan_objek_pajak']); ?>" 
                   required
                   placeholder="Contoh: Kadia">

            <label>Alamat Wajib Pajak</label>
            <input type="text" 
                   name="alamat_wajib_pajak" 
                   value="<?php echo htmlspecialchars($row['alamat_wajib_pajak']); ?>" 
                   required
                   placeholder="Masukkan alamat lengkap wajib pajak">

            <label>Alamat Objek Pajak</label>
            <input type="text" 
                   name="alamat_objek_pajak" 
                   value="<?php echo htmlspecialchars($row['alamat_objek_pajak']); ?>" 
                   required
                   placeholder="Masukkan alamat lengkap objek pajak">

            <label>Tipe Berkas</label>
            <div class="checkbox-group">
                <?php
                $tipe_berkas = explode(", ", $row['tipe_berkas']);
                $checked1 = in_array("BPHTB", $tipe_berkas) ? "checked" : "";
                $checked2 = in_array("Mutasi Nama & Pembetulan", $tipe_berkas) ? "checked" : "";
                $checked3 = in_array("Objek Pajak Baru", $tipe_berkas) ? "checked" : "";
                $checked4 = in_array("Mutasi Bagian", $tipe_berkas) ? "checked" : "";
                ?>
                <label><input type="checkbox" name="tipe_berkas[]" value="BPHTB" <?php echo $checked1; ?>> BPHTB</label>
                <label><input type="checkbox" name="tipe_berkas[]" value="Mutasi Nama & Pembetulan" <?php echo $checked2; ?>> Mutasi Nama & Pembetulan</label>
                <label><input type="checkbox" name="tipe_berkas[]" value="Objek Pajak Baru" <?php echo $checked3; ?>> Objek Pajak Baru</label>
                <label><input type="checkbox" name="tipe_berkas[]" value="Mutasi Bagian" <?php echo $checked4; ?>> Mutasi Bagian</label>
            </div>

            <label for="tanggal_masuk" style="margin-top:10px;">Tanggal Masuk Berkas</label>
            <div class="input-date-group">
                <span class="calendar-icon">&#128197;</span>
                <input type="date" id="tanggal_masuk" name="tanggal_masuk" required 
                    value="<?php
                        $tanggal = $row['tanggal_masuk'] ?? '';
                        // Jika ada waktu, ambil hanya tanggalnya
                        if ($tanggal && strpos($tanggal, '-') !== false) {
                            $tanggal = substr($tanggal, 0, 10);
                        }
                        echo htmlspecialchars($tanggal ?: date('Y-m-d'));
                    ?>">
            </div>

            <div class="button-group">
                <button type="submit" class="button-animated">Simpan</button>
                <button type="button" id="cancelButton" class="button-secondary-animated">Batal</button>
            </div>
        </form>
    </div>  
    <script src="javascript/edit.js"></script>
</body>
</html>

<?php
$conn->close();
?>