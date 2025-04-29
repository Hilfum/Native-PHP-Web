<?php
session_start(); // Tambahkan ini di baris paling atas
include 'koneksi.php';

// Tambahkan fungsi ini di bagian atas file setelah include
function formatNOP($nop)
{
    return substr($nop, 0, 2) . '.' .
        substr($nop, 2, 2) . '.' .
        substr($nop, 4, 3) . '.' .
        substr($nop, 7, 3) . '.' .
        substr($nop, 10, 3) . '.' .
        substr($nop, 13, 4) . '.' .
        substr($nop, 17, 1);
}

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

// Replace the existing query with this
$query = "SELECT id, nama, nop, kelurahan_objek_pajak, kecamatan_objek_pajak, 
          alamat_wajib_pajak, alamat_objek_pajak, tipe_berkas, status, tanggal_masuk, current_handler 
          FROM dokumen WHERE 1=1";

if ($_SESSION['role'] !== 'monitoring') {
    $handler = mysqli_real_escape_string($conn, $_SESSION['role']);
    $query .= " AND current_handler = '$handler'";

    // Mapping role ke tipe berkas yang boleh di-handle
    $role_berkas_map = [
        'loket'      => ['BPHTB', 'Objek Pajak Baru', 'Mutasi Bagian', 'Mutasi Nama & Pembetulan'],
        'verlap'     => ['BPHTB', 'Objek Pajak Baru', 'Mutasi Bagian', 'Mutasi Nama & Pembetulan'],
        'penetapan'  => ['BPHTB', 'Objek Pajak Baru', 'Mutasi Bagian', 'Mutasi Nama & Pembetulan'],
        'kabid'      => ['BPHTB', 'Objek Pajak Baru', 'Mutasi Bagian', 'Mutasi Nama & Pembetulan'],
        'bphtb'      => ['BPHTB'],
        'mutasi1'    => ['Mutasi Bagian'],
        'mutasi2'    => ['Mutasi Nama & Pembetulan'],
        'op_baru'    => ['Objek Pajak Baru']
    ];
    $role = $_SESSION['role'];
    if (isset($role_berkas_map[$role])) {
        $allowed_types = array_map(function($t) use ($conn) {
            return "'%" . mysqli_real_escape_string($conn, $t) . "%'";
        }, $role_berkas_map[$role]);
        $query .= " AND (";
        foreach ($allowed_types as $i => $type) {
            if ($i > 0) $query .= " OR ";
            $query .= "tipe_berkas LIKE $type";
        }
        $query .= ")";
    }
}

if (isset($_GET['search_nama']) && !empty($_GET['search_nama'])) {
    $search_nama = mysqli_real_escape_string($conn, $_GET['search_nama']);
    $query .= " AND nama LIKE '%$search_nama%'";
}
if (isset($_GET['search_nop']) && !empty($_GET['search_nop'])) {
    $search_nop = mysqli_real_escape_string($conn, $_GET['search_nop']);
    $query .= " AND nop LIKE '%$search_nop%'";
}
if (isset($_GET['search_kelurahan']) && !empty($_GET['search_kelurahan'])) {
    $search_kelurahan = mysqli_real_escape_string($conn, $_GET['search_kelurahan']);
    $query .= " AND kelurahan_objek_pajak LIKE '%$search_kelurahan%'";
}
if (isset($_GET['search_kecamatan']) && !empty($_GET['search_kecamatan'])) {
    $search_kecamatan = mysqli_real_escape_string($conn, $_GET['search_kecamatan']);
    $query .= " AND kecamatan_objek_pajak LIKE '%$search_kecamatan%'";
}
if (isset($_GET['search_alamat_wp']) && !empty($_GET['search_alamat_wp'])) {
    $search_alamat_wp = mysqli_real_escape_string($conn, $_GET['search_alamat_wp']);
    $query .= " AND alamat_wajib_pajak LIKE '%$search_alamat_wp%'";
}
if (isset($_GET['search_alamat_op']) && !empty($_GET['search_alamat_op'])) {
    $search_alamat_op = mysqli_real_escape_string($conn, $_GET['search_alamat_op']);
    $query .= " AND alamat_objek_pajak LIKE '%$search_alamat_op%'";
}
if (isset($_GET['search_tipe']) && !empty($_GET['search_tipe'])) {
    $search_tipe = mysqli_real_escape_string($conn, $_GET['search_tipe']);
    $query .= " AND tipe_berkas LIKE '%$search_tipe%'";
}
if (isset($_GET['search_status']) && !empty($_GET['search_status'])) {
    $search_status = mysqli_real_escape_string($conn, $_GET['search_status']);
    $query .= " AND status LIKE '%$search_status%'";
}

if ($_SESSION['role'] === 'monitoring' && isset($_GET['search_handler']) && !empty($_GET['search_handler'])) {
    $search_handler = mysqli_real_escape_string($conn, $_GET['search_handler']);
    $query .= " AND current_handler = '$search_handler'";
}

// Replace the existing date search condition with this
// Date search conditions
if (isset($_GET['search_year']) && !empty($_GET['search_year'])) {
    $search_year = mysqli_real_escape_string($conn, $_GET['search_year']);
    $query .= " AND YEAR(tanggal_masuk) = '$search_year'";

    if (isset($_GET['search_month']) && !empty($_GET['search_month'])) {
        $search_month = mysqli_real_escape_string($conn, $_GET['search_month']);
        $query .= " AND MONTH(tanggal_masuk) = '$search_month'";
    }
}
// Specific date search takes precedence if provided
if (isset($_GET['search_date_start']) && !empty($_GET['search_date_start'])) {
    $search_date = mysqli_real_escape_string($conn, $_GET['search_date_start']);
    $query .= " AND DAY(tanggal_masuk) = '$search_date'";
}

// Pagination
$entries = isset($_GET['entries']) ? (int) $_GET['entries'] : 10;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$start = ($page - 1) * $entries;

// Get total records for pagination
$count_conditions = str_replace("SELECT *", "", $query);
$total_query = "SELECT COUNT(*) as total FROM dokumen " . substr($count_conditions, strpos($count_conditions, "WHERE"));
$total_result = mysqli_query($conn, $total_query);

if (!$total_result) {
    die("Count Query Error: " . mysqli_error($conn));
}

$total_row = mysqli_fetch_assoc($total_result);
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $entries);

// Add ORDER BY before LIMIT
$query .= " ORDER BY id DESC LIMIT $start, $entries";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query Error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Berkas</title>
    <link rel="stylesheet" href="css/show.css">
</head>

<body class="show-body">
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
                <div class="menu-row">
                    <div class="user-info">
                        Login sebagai: <b><?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : ''; ?></b>
                    </div>
                    <a href="#" class="logout-button" onclick="handleLogout(event)">
                        Keluar (Logout)
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="menu-overlay" id="menuOverlay"></div>
    <div class="content">
        <div class="table-container">
            <div class="table-header">
                <?php if ($_SESSION['role'] == 'loket'): ?>
                    <a href="input.php" class="back-button" id="kembaliInputBtn">Kembali ke Halaman Input</a>
                <?php endif; ?> 
                <div class="entries-container">
                    <label>Show
                        <select name="entries" id="entries" onchange="changeEntries(this.value)">
                            <option value="10" <?php echo isset($_GET['entries']) && $_GET['entries'] == '10' ? 'selected' : ''; ?>>10</option>
                            <option value="25" <?php echo isset($_GET['entries']) && $_GET['entries'] == '25' ? 'selected' : ''; ?>>25</option>
                            <option value="50" <?php echo isset($_GET['entries']) && $_GET['entries'] == '50' ? 'selected' : ''; ?>>50</option>
                            <option value="100" <?php echo isset($_GET['entries']) && $_GET['entries'] == '100' ? 'selected' : ''; ?>>100</option>
                        </select>
                        entries
                    </label>
                </div>
            </div>

            <div class="search-container">
                <form action="" method="GET">
                    <table class="search-table">
                        <tr>
                            <td style="width:170px;">
                                <div class="tanggal-group">
                                    <select name="search_date_start" class="date-select">
                                        <option value="">Tanggal</option>
                                        <?php for ($day = 1; $day <= 31; $day++): 
                                            $day_padded = str_pad($day, 2, '0', STR_PAD_LEFT);
                                            $selected = (isset($_GET['search_date_start']) && $_GET['search_date_start'] == $day_padded) ? 'selected' : '';
                                            echo "<option value='$day_padded' $selected>$day_padded</option>";
                                        endfor; ?>
                                    </select>
                                    <select name="search_month" class="date-select">
                                        <option value="">Bulan</option>
                                        <?php
                                        $months = [
                                            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
                                            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
                                            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                                        ];
                                        foreach ($months as $num => $name) {
                                            $selected = (isset($_GET['search_month']) && $_GET['search_month'] == $num) ? 'selected' : '';
                                            echo "<option value='$num' $selected>$name</option>";
                                        }
                                        ?>
                                    </select>
                                    <select name="search_year" class="date-select">
                                        <option value="">Tahun</option>
                                        <?php
                                        $current_year = date('Y');
                                        $min_year = $current_year - 99; // 100 tahun ke belakang
                                        $max_year = $current_year + 100; // 100 tahun ke depan
                                        for ($year = $max_year; $year >= $min_year; $year--) {
                                            $selected = (isset($_GET['search_year']) && $_GET['search_year'] == $year) ? 'selected' : '';
                                            echo "<option value='$year' $selected>$year</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </td>
                            <td style="width:140px;">
                                <input type="text" name="search_nama" placeholder="Cari Nama..." value="<?php echo isset($_GET['search_nama']) ? htmlspecialchars($_GET['search_nama']) : ''; ?>">
                            </td>
                            <td style="width:120px;">
                                <input type="text" name="search_nop" placeholder="Cari NOP..." value="<?php echo isset($_GET['search_nop']) ? htmlspecialchars($_GET['search_nop']) : ''; ?>">
                            </td>
                            <td style="width:120px;">
                                <input type="text" name="search_kelurahan" placeholder="Cari Kelurahan..." value="<?php echo isset($_GET['search_kelurahan']) ? htmlspecialchars($_GET['search_kelurahan']) : ''; ?>">
                            </td>
                            <td style="width:120px;">
                                <input type="text" name="search_kecamatan" placeholder="Cari Kecamatan..." value="<?php echo isset($_GET['search_kecamatan']) ? htmlspecialchars($_GET['search_kecamatan']) : ''; ?>">
                            </td>
                        </tr>
                        <tr>
                            <td style="width:170px;">
                                <input type="text" name="search_alamat_wp" placeholder="Cari Alamat WP..." value="<?php echo isset($_GET['search_alamat_wp']) ? htmlspecialchars($_GET['search_alamat_wp']) : ''; ?>">
                            </td>
                            <td style="width:140px;">
                                <input type="text" name="search_alamat_op" placeholder="Cari Alamat OP..." value="<?php echo isset($_GET['search_alamat_op']) ? htmlspecialchars($_GET['search_alamat_op']) : ''; ?>">
                            </td>
                            <td style="width:120px;">
                                <select name="search_tipe" style="width:100%;">
                                    <option value="">Tipe Berkas...</option>
                                    <option value="BPHTB" <?php echo (isset($_GET['search_tipe']) && $_GET['search_tipe'] == 'BPHTB') ? 'selected' : ''; ?>>BPHTB</option>
                                    <option value="Objek Pajak Baru" <?php echo (isset($_GET['search_tipe']) && $_GET['search_tipe'] == 'Objek Pajak Baru') ? 'selected' : ''; ?>>Objek Pajak Baru</option>
                                    <option value="Mutasi Bagian" <?php echo (isset($_GET['search_tipe']) && $_GET['search_tipe'] == 'Mutasi Bagian') ? 'selected' : ''; ?>>Mutasi Bagian</option>
                                    <option value="Mutasi Nama & Pembetulan" <?php echo (isset($_GET['search_tipe']) && $_GET['search_tipe'] == 'Mutasi Nama & Pembetulan') ? 'selected' : ''; ?>>Mutasi Nama & Pembetulan</option>
                                </select>
                            </td>
                            <td style="width:120px;">
                                <select name="search_status" style="width:100%;">
                                    <option value="">Status...</option>
                                    <option value="Pending" <?php echo (isset($_GET['search_status']) && $_GET['search_status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                    <option value="Selesai" <?php echo (isset($_GET['search_status']) && $_GET['search_status'] == 'Selesai') ? 'selected' : ''; ?>>Selesai</option>
                                </select>
                            </td>
                            <?php if ($_SESSION['role'] === 'monitoring'): ?>
                            <td style="width:120px;">
                                <div style="display:flex;gap:8px;">
                                    <select name="search_handler" style="width:60%;">
                                        <option value="">Handler</option>
                                        <?php
                                        $handlers = ['loket', 'verlap', 'penetapan', 'kabid', 'bphtb', 'op_baru', 'mutasi1', 'mutasi2'];
                                        foreach ($handlers as $handler) {
                                            $selected = (isset($_GET['search_handler']) && $_GET['search_handler'] == $handler) ? 'selected' : '';
                                            echo "<option value='$handler' $selected>" . ucfirst($handler) . "</option>";
                                        }
                                        ?>
                                    </select>
                                    <button type="submit" class="search-btn-big" style="width:40%;">Cari</button>
                                </div>
                            </td>
                            <?php else: ?>
                            <td style="width:120px;">
                                <div style="display:flex;align-items:stretch;height:40px;">
                                    <button type="submit" class="search-btn-big" style="width:100%;height:40px;">Cari</button>
                                </div>
                            </td>
                            <?php endif; ?>
                        </tr>
                    </table>
                </form>
            </div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Tanggal Masuk</th>
                        <th class="nama-wajib-pajak">Nama Wajib Pajak</th>
                        <th>NOP</th>
                        <th class="kelurahan-objek-pajak">Kelurahan Objek Pajak</th>
                        <th class="kecamatan-objek-pajak">Kecamatan Objek Pajak</th>
                        <th class="alamat-wajib-pajak">Alamat Wajib Pajak</th>
                        <th class="alamat-objek-pajak">Alamat Objek Pajak</th>
                        <th class="tipe-berkas">Tipe Berkas</th>
                        <th>Status Berkas</th>
                        <?php if ($_SESSION['role'] === 'monitoring'): ?>
                            <th>Handler</th>
                        <?php else: ?>
                            <th>Action</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td class="tanggal-masuk">
                                <?php
                                if (!empty($row['tanggal_masuk'])) {
                                    $tanggal = new DateTime($row['tanggal_masuk']);
                                    echo $tanggal->format('d-m-Y');
                                } else {
                                    echo '-';
                                }
                                ?>
                            </td>
                            <td class="nama-wajib-pajak"><?php echo htmlspecialchars($row['nama']); ?></td>
                            <td><?php echo htmlspecialchars(formatNOP($row['nop'])); ?></td>
                            <td class="kelurahan-objek-pajak"><?php echo htmlspecialchars($row['kelurahan_objek_pajak']); ?></td>
                            <td class="kecamatan-objek-pajak"><?php echo htmlspecialchars($row['kecamatan_objek_pajak']); ?></td>
                            <td class="alamat-wajib-pajak"><?php echo htmlspecialchars($row['alamat_wajib_pajak']); ?></td>
                            <td class="alamat-objek-pajak"><?php echo htmlspecialchars($row['alamat_objek_pajak']); ?></td>
                            <td class="tipe-berkas">
                                <?php echo nl2br(str_replace(', ', "\n", htmlspecialchars($row['tipe_berkas']))); ?>
                            </td>
                            <td class="status-cell">
                                <?php if (strtolower($row['status']) === 'selesai'): ?>
                                    <span class="status-selesai">
                                        Selesai
                                    </span>
                                <?php else: ?>
                                    <span class="status-<?php echo strtolower($row['status']); ?>">
                                        <?php echo htmlspecialchars($row['status']); ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <?php if ($_SESSION['role'] === 'monitoring'): ?>
                                <td class="handler">
                                    <?php
                                    echo isset($row['current_handler']) && $row['current_handler']
                                        ? htmlspecialchars(keterangan_handler($row['current_handler']))
                                        : '-';
                                    ?>
                                </td>
                            <?php else: ?>
                                <td>
                                    <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn-edit">Edit</a>
                                    <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn-delete" onclick="return confirm('Hapus data ini?')">Hapus</a>
                                    <?php if (strtolower($row['status']) !== 'selesai'): ?>
                                        <form action="confirm.php" method="POST" style="display:inline;" onsubmit="return handleConfirm(event, this);">
                                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                            <button type="submit" class="btn-confirm">Confirm</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="pagination-container">
        <div class="pagination-info">
            Showing <?php echo $start + 1; ?> to <?php echo min($start + $entries, $total_records); ?> of
            <?php echo $total_records; ?> entries
        </div>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=1<?php echo isset($_GET['entries']) ? '&entries=' . $entries : ''; ?>"
                    class="page-link">First</a>
                <a href="?page=<?php echo $page - 1; ?><?php echo isset($_GET['entries']) ? '&entries=' . $entries : ''; ?>"
                    class="page-link">Previous</a>
            <?php endif; ?>

            <?php
            $start_page = max(1, $page - 2);
            $end_page = min($total_pages, $page + 2);

            // Display page numbers
            for ($i = $start_page; $i <= $end_page; $i++): ?>
                <a href="?page=<?php echo $i; ?><?php echo isset($_GET['entries']) ? '&entries=' . $entries : ''; ?>"
                    class="page-link <?php echo $page == $i ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <a href="?page=<?php echo $page + 1; ?><?php echo isset($_GET['entries']) ? '&entries=' . $entries : ''; ?>"
                    class="page-link">Next</a>
                <a href="?page=<?php echo $total_pages; ?><?php echo isset($_GET['entries']) ? '&entries=' . $entries : ''; ?>"
                    class="page-link">Last</a>
            <?php endif; ?>
        </div>
    </div>
    <script src="javascript/show.js"></script>
</body>
</html>

<?php
mysqli_close($conn);
?>