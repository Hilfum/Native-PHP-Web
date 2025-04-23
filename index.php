<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang di Pengimputan Data Bapenda</title>
    <link rel="stylesheet" href="css/index.css">
</head>
<body>
    <div class="overlay"></div>
    <div class="container" id="mainContainer">
        <div class="logo-row">
            <img src="gambar/Lambang_Kota_Kendari.png" alt="Logo Kota Kendari" class="logo-kendari">
            <img src="gambar/logo_jakpa-removebg-preview.png" alt="Logo Besar" class="logo-besar">
        </div>
        <h1>Selamat Datang</h1>
        <h2>Website Pengimputan Data Pajak Bapenda Kota Kendari</h2>
        <a href="login.php" class="login-button" id="loginBtn">Login</a>
    </div>
    <script>
    // Animasi masuk saat halaman dimuat
    function showContainer() {
        document.getElementById('mainContainer').classList.add('show');
    }
    function hideContainer(callback) {
        const container = document.getElementById('mainContainer');
        container.classList.remove('show');
        setTimeout(callback, 400); // waktu sesuai transition CSS
    }
    window.addEventListener('DOMContentLoaded', function() {
        setTimeout(showContainer, 80);
        // Interaktif animasi keluar saat klik login
        document.getElementById('loginBtn').addEventListener('click', function(e) {
            e.preventDefault();
            hideContainer(function() {
                window.location.href = 'login.php';
            });
        });
    });
    // Jika user kembali dari halaman lain, animasi tetap berjalan
    window.onpageshow = function(event) {
        if (event.persisted) {
            showContainer();
        }
    };
    </script>
</body>
</html>