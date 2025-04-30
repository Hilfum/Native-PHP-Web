// Fungsi untuk menampilkan container dengan animasi
function showContainer() {
    const container = document.getElementById('mainContainer');
    requestAnimationFrame(() => {
        container.classList.add('show');
    });
}

// Fungsi untuk menyembunyikan container dengan animasi
function hideContainer(callback) {
    const container = document.getElementById('mainContainer');
    container.classList.add('fade-out');
    container.classList.remove('show');
    
    setTimeout(callback, 400); // Waktu sesuai dengan durasi transisi
}

// Event listener saat halaman dimuat
window.addEventListener('DOMContentLoaded', function() {
    const loginBtn = document.getElementById('loginBtn');
    
    // Reset animasi jika kembali dari halaman lain
    loginBtn.classList.remove('bounceOut');
    
    // Tunda sedikit untuk memastikan transisi berjalan
    setTimeout(showContainer, 100);

    // Tambahkan event listener untuk tombol login
    loginBtn.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Animasi tombol
        this.classList.add('bounceOut');
        
        // Animasi container
        hideContainer(() => {
            window.location.href = 'login.php';
        });
    });
});

// Handle kembali ke halaman (back/forward browser)
window.onpageshow = function(event) {
    if (event.persisted) {
        // Reset animasi
        const loginBtn = document.getElementById('loginBtn');
        loginBtn.classList.remove('bounceOut');
        
        // Tampilkan container
        const container = document.getElementById('mainContainer');
        container.classList.remove('fade-out');
        showContainer();
    }
};

// Tambahkan preload untuk background image
window.addEventListener('load', function() {
    const img = new Image();
    img.src = '../gambar/BALAI-KOTA.jpg';
});