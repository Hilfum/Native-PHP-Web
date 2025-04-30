<?php
session_start();
include 'koneksi.php';

// Ambil parameter filter dan entries dari GET
$entries = isset($_GET['entries']) ? (int)$_GET['entries'] : 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $entries;

// Copy query filter dari show.php sesuai kebutuhan
$query = "SELECT id, nama, nop, kelurahan_objek_pajak, kecamatan_objek_pajak, 
          alamat_wajib_pajak, alamat_objek_pajak, tipe_berkas, status, tanggal_masuk, current_handler 
          FROM dokumen WHERE 1=1";
// ...tambahkan filter lain jika perlu...

$query .= " ORDER BY id DESC LIMIT $start, $entries";
$result = mysqli_query($conn, $query);

function formatNOP($nop) {
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

$total_records_query = "SELECT COUNT(*) as total FROM dokumen WHERE 1=1";
$total_records_result = mysqli_query($conn, $total_records_query);
$total_records_row = mysqli_fetch_assoc($total_records_result);
$total_records = $total_records_row['total'];
$total_pages = ceil($total_records / $entries);
?>

<div id="tableArea">
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
        <tbody id="dataTbody">
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td class="tanggal-masuk" data-label="Tanggal Masuk">
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
                            <span class="status-selesai">Selesai</span>
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
                            <!-- Action buttons, copy dari show.php -->
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
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
</div>
<?php
mysqli_close($conn);
?>