<?php
session_start();
// Redirect ke input.php jika user sudah login
if (isset($_SESSION['username'])) {
    header("Location: input.php");
    exit();
}
include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        // Ambil role user
        $role_query = "SELECT r.name FROM users u JOIN roles r ON u.role_id = r.id WHERE u.id = ?";
        $role_stmt = mysqli_prepare($conn, $role_query);
        mysqli_stmt_bind_param($role_stmt, "i", $user['id']);
        mysqli_stmt_execute($role_stmt);
        $role_result = mysqli_stmt_get_result($role_stmt);
        $role_row = mysqli_fetch_assoc($role_result);
        $role = $role_row['name'];

        if (password_verify($password, $user['password'])) {
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $role;

            if ($role == 'loket') {
                echo json_encode([
                    'success' => true,
                    'message' => 'Login berhasil! Selamat datang, ' . $user['username'],
                    'redirect' => 'input.php'  // Redirects to input.php for loket role
                ]);
                exit();
            } else {
                echo json_encode([
                    'success' => true,
                    'message' => 'Login berhasil! Selamat datang, ' . $user['username'],
                    'redirect' => 'show.php'   // Redirects to show.php for other roles
                ]);
                exit();
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Password salah']);
            exit();
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Username tidak ditemukan']);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOGIN - Bapenda Kota Kendari</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div class="background"></div>
    <div class="overlay"></div>
    
    <div class="login-container" id="loginContainer">
        <div class="login-header">
            <img src="gambar/Lambang_Kota_Kendari.png" alt="Logo" class="login-logo">
            <h2>Selamat Datang</h2>
            <p class="login-subtitle">Silahkan login untuk melanjutkan</p>
        </div>

        <div class="error-message" id="errorMessage"></div>

        <form id="loginForm" action="login.php" method="POST">
            <div class="input-group">
                <span class="icon-user">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </span>
                <input type="text" 
                       id="username" 
                       name="username" 
                       required 
                       placeholder="Username"
                       autocomplete="off">
            </div>

            <div class="input-group">
                <span class="icon-lock">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                </span>
                <input type="password" 
                       id="password" 
                       name="password" 
                       required 
                       placeholder="Password">
                <button type="button" class="toggle-password" id="togglePassword">
                    <svg class="icon-eye" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                </button>
            </div>

            <button type="submit" class="login-button">
                <span class="button-text">Login</span>
                <span class="button-loader"></span>
            </button>
        </form>
    </div>

    <script src="javascript/login.js"></script>
</body>
</html>