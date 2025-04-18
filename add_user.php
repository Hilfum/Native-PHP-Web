<?php
include 'koneksi.php';

// Disable foreign key checks temporarily
$conn->query("SET FOREIGN_KEY_CHECKS = 0");

// Drop existing tables
$conn->query("DROP TABLE IF EXISTS users");
$conn->query("DROP TABLE IF EXISTS roles");

// Re-enable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 1");

// Create roles table
$create_roles = "CREATE TABLE roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(100) NOT NULL
)";

if (!$conn->query($create_roles)) {
    die("Error creating roles table: " . $conn->error);
}

// Create users table with role relationship
$create_users = "CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role_id INT,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE RESTRICT
)";

if (!$conn->query($create_users)) {
    die("Error creating users table: " . $conn->error);
}

// Define roles array with descriptions
$roles = [
    ['name' => 'loket', 'desc' => 'Petugas Loket'],
    ['name' => 'verlap', 'desc' => 'Verifikasi Lapangan'],
    ['name' => 'penetapan', 'desc' => 'Penetapan'],
    ['name' => 'kabid', 'desc' => 'Kepala Bidang'],
    ['name' => 'op_baru', 'desc' => 'Objek Pajak Baru'],
    ['name' => 'mutasi1', 'desc' => 'Mutasi 1'],
    ['name' => 'mutasi2', 'desc' => 'Mutasi 2'],
    ['name' => 'bphtb', 'desc' => 'BPHTB'],
    ['name' => 'monitoring', 'desc' => 'Pemantauan'] // Tambahkan ini
];

// Insert roles
$role_stmt = $conn->prepare("INSERT INTO roles (name, description) VALUES (?, ?)");
foreach ($roles as $role) {
    $role_stmt->bind_param("ss", $role['name'], $role['desc']);
    $role_stmt->execute();
}

// Define users array
$users = [
    ['username' => 'petugas_loket', 'password' => 'loket123', 'role' => 'loket'],
    ['username' => 'verlap', 'password' => 'verlap123', 'role' => 'verlap'],
    ['username' => 'penetapan', 'password' => 'penetapan123', 'role' => 'penetapan'],
    ['username' => 'kabid', 'password' => 'kabid123', 'role' => 'kabid'],
    ['username' => 'op_baru', 'password' => 'opbaru123', 'role' => 'op_baru'],
    ['username' => 'mutasi1', 'password' => 'mutasi1123', 'role' => 'mutasi1'],
    ['username' => 'mutasi2', 'password' => 'mutasi2123', 'role' => 'mutasi2'],
    ['username' => 'bphtb', 'password' => 'bphtb123', 'role' => 'bphtb'],
    ['username' => 'monitoring', 'password' => 'monitor123', 'role' => 'monitoring'] // Tambahkan ini
];

// Insert users
$user_stmt = $conn->prepare("INSERT INTO users (username, password, role_id) 
    SELECT ?, ?, r.id 
    FROM roles r 
    WHERE r.name = ?");

foreach ($users as $user) {
    $hashed_password = password_hash($user['password'], PASSWORD_DEFAULT);
    $user_stmt->bind_param("sss", $user['username'], $hashed_password, $user['role']);
    
    if ($user_stmt->execute()) {
        echo "User {$user['username']} added successfully.<br>";
    } else {
        echo "Error adding user {$user['username']}: " . $user_stmt->error . "<br>";
    }
}

$role_stmt->close();
$user_stmt->close();
$conn->close();
?>