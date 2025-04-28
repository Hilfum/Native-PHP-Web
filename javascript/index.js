function showContainer() {
    document.getElementById('mainContainer').classList.add('show');
}
function hideContainer(callback) {
    const container = document.getElementById('mainContainer');
    container.classList.remove('show');
    setTimeout(callback, 400); // waktu sesuai transition CSS
}
window.addEventListener('DOMContentLoaded', function() {
    // Reset animasi tombol jika kembali dari halaman lain
    document.getElementById('loginBtn').classList.remove('bounceOut');
    setTimeout(showContainer, 80);
    // Interaktif animasi keluar saat klik masuk
    document.getElementById('loginBtn').addEventListener('click', function(e) {
        e.preventDefault();
        this.classList.add('bounceOut');
        hideContainer(function() {
            window.location.href = 'login.php';
        });
    });
});
window.onpageshow = function(event) {
    if (event.persisted) {
        showContainer();
        // Reset animasi tombol jika kembali dengan back/forward
        document.getElementById('loginBtn').classList.remove('bounceOut');
    }
};