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
                   placeholder="74.71.770.006.003.1146.0"
                   autocomplete="off">

            <label>Kelurahan Objek Pajak</label>
            <input type="text" 
                   name="kelurahan_objek_pajak" 
                   value="<?php echo htmlspecialchars($row['kelurahan_objek_pajak']); ?>" 
                   required
                   placeholder="Contoh: Kadia">

            <label>Kecamatan Objek Pajak:</label>
            <input type="text" name="kecamatan_objek_pajak" value="<?php echo htmlspecialchars($row['kecamatan_objek_pajak']); ?>" required>

            <label>Alamat Wajib Pajak:</label>
            <input type="text" name="alamat_wajib_pajak" value="<?php echo htmlspecialchars($row['alamat_wajib_pajak']); ?>" required>

            <label>Alamat Objek Pajak:</label>
            <input type="text" name="alamat_objek_pajak" value="<?php echo htmlspecialchars($row['alamat_objek_pajak']); ?>" required>

            <label>Tipe Berkas:</label>
            <div class="checkbox-group">
                <?php
                $tipe_berkas = explode(", ", $row['tipe_berkas']);
                $options = ["BPHTB", "Objek Pajak Baru", "Mutasi Bagian", "Mutasi Nama & Pembetulan"];
                foreach ($options as $option) {
                    $checked = in_array($option, $tipe_berkas) ? "checked" : "";
                    echo "<label><input type='checkbox' name='tipe_berkas[]' value='$option' $checked> $option</label>";
                }
                ?>
            </div>

            <!-- Tambahkan input tanggal masuk seperti input.php -->
            <label for="tanggal_masuk" style="margin-top:10px;">Tanggal Masuk Berkas</label>
            <div class="input-date-group">
                <span class="calendar-icon">&#128197;</span>
                <input type="date" id="tanggal_masuk" name="tanggal_masuk" required value="<?php echo htmlspecialchars($row['tanggal_masuk']); ?>">
            </div>

            <div class="button-group">
                <button type="submit">Update</button>
                <button type="button" id="cancelButton" class="button-secondary">Cancel</button>
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
        
        this.value = formattedValue;
    });

    // Replace the existing cancel button handler
    document.getElementById('cancelButton').addEventListener('click', function(e) {
        // Cek apakah ada perubahan pada form
        const formChanged = isFormChanged();
        
        if (formChanged) {
            if (alert('Ada perubahan yang belum disimpan. Yakin ingin membatalkan?')) {
                window.location.href = 'show.php';
            }
        } else {
            window.location.href = 'show.php';
        }
    });

    // Fungsi untuk mengecek perubahan pada form
    function isFormChanged() {
        const form = document.querySelector('form');
        const inputs = form.querySelectorAll('input[type="text"]');
        const checkboxes = form.querySelectorAll('input[type="checkbox"]');
        let changed = false;

        // Cek perubahan pada input text
        inputs.forEach(input => {
            if (input.value !== input.defaultValue) {
                changed = true;
            }
        });

        // Cek perubahan pada checkbox
        const originalTipeBerkas = <?php echo json_encode(explode(", ", $row['tipe_berkas'])); ?>;
        const currentTipeBerkas = Array.from(checkboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value);

        if (JSON.stringify(originalTipeBerkas.sort()) !== JSON.stringify(currentTipeBerkas.sort())) {
            changed = true;
        }

        return changed;
    }
    </script>

    <!-- Tambahkan CSS jika belum ada -->
    <style>
    .input-date-group {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }
    .input-date-group .calendar-icon {
        font-size: 1.5em;
        margin-right: 8px;
        color: #3498db;
    }
    .input-date-group input[type="date"] {
        padding: 8px 12px;
        font-size: 1.1em;
        border-radius: 6px;
        border: 1px solid #ccc;
        transition: border-color 0.2s;
    }
    .input-date-group input[type="date"]:focus {
        border-color: #3498db;
        outline: none;
    }
    </style>
</body>
</html>

<?php
$conn->close();
?>