<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'koneksi.php';

// Fetch data from the database
$query = "SELECT * FROM berkas";
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
    <title>Admin Page</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .actions {
            display: flex;
            gap: 10px;
        }
        .actions a {
            padding: 5px 10px;
            text-decoration: none;
            color: white;
            border-radius: 4px;
        }
        .edit {
            background-color: #007bff;
        }
        .delete {
            background-color: #dc3545;
        }
    </style>
</head>
<body>
    <h2>Admin Page</h2>
    <table>
        <tr>
            <th>Nama</th>
            <th>NOP</th>
            <th>Kelurahan</th>
            <th>Alamat Wajib Pajak</th>
            <th>Alamat Objek Pajak</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['nama']); ?></td>
            <td><?php echo htmlspecialchars($row['nop']); ?></td>
            <td><?php echo htmlspecialchars($row['kelurahan']); ?></td>
            <td><?php echo htmlspecialchars($row['alamat_wajib_pajak']); ?></td>
            <td><?php echo htmlspecialchars($row['alamat_objek_pajak']); ?></td>
            <td class="actions">
                <a href="edit.php?id=<?php echo $row['id']; ?>" class="edit">Edit</a>
                <a href="delete.php?id=<?php echo $row['id']; ?>" class="delete" onclick="return confirm('Are you sure you want to delete this record?');">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>

<?php
mysqli_close($conn);
?>