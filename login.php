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

    $query = "SELECT * FROM Users WHERE username = ?";
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
    <title>LOGIN</title>
    <link rel="stylesheet" href="css/login.css">
</head>

<body>
    <div class="background"></div>
    <div class="overlay"></div>
    <div class="header">
        <a href="index.php" class="logo-container">
            <img src="gambar/Lambang_Kota_Kendari.png" alt="Logo Kota Kendari">
            <h2>Bapenda Kota Kendari</h2>
        </a>
    </div>
    <div class="login-container">
        <h2>LOGIN</h2>
        <div class="error" style="color:red; margin-bottom:10px; display:none;"></div>
        <form action="login.php" method="POST">
            <label>Username</label>
            <input type="text" name="username" required placeholder="Masukkan username">
            <label>Password</label>
            <input type="password" name="password" required placeholder="Masukkan password">
            <button type="submit">Login</button>
        </form>
    </div>
    <script>
        document.querySelector('form').addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch('login.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        window.location.href = data.redirect;
                    } else {
                        const errorDiv = document.querySelector('.error');
                        errorDiv.textContent = data.message;
                        errorDiv.style.display = 'block';
                    }
                });
        });

        // Check for logout parameter
        window.onload = function () {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('logout') === 'success') {
                const username = urlParams.get('username');
                alert('Akun ' + username + ' telah berhasil logout.');
                // Clean URL after showing alert
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        }

        // Add this where your logout button handler is defined
        function handleLogout(e) {
            e.preventDefault();
            if (confirm('Apakah Anda yakin ingin logout?')) {
                window.location.href = 'logout.php?confirm=yes';
            }
        }
    </script>
    <script>
        window.onload = function () {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('logout') === 'yes') {
                const username = urlParams.get('username');
                alert('Akun ' + username + ' telah berhasil logout.');
                // Clean URL after showing alert
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        }
    </script>
</body>

</html>