// Logout
function handleLogout(e) {
    e.preventDefault();
    if (confirm('Apakah Anda yakin ingin logout?')) {
        window.location.href = 'logout.php?confirm=yes';
    }
}

// Confirm
function handleConfirm(e, form) {
    e.preventDefault();
    fetch(form.action, {
        method: 'POST',
        body: new FormData(form)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.message || 'Gagal konfirmasi');
        }
    });
    return false;
}

// Ganti jumlah entries
function changeEntries(value) {
    let currentUrl = new URL(window.location.href);
    currentUrl.searchParams.set('entries', value);
    currentUrl.searchParams.set('page', '1');
    window.location.href = currentUrl.toString();
}

// Hamburger menu
function toggleMenu(event) {
    event.stopPropagation();
    const menu = document.querySelector('.hamburger-menu-container');
    const overlay = document.getElementById('menuOverlay');
    menu.classList.toggle('active');
    if (menu.classList.contains('active')) {
        overlay.classList.add('active');
        document.addEventListener('click', closeMenuOnClickOutside);
    } else {
        overlay.classList.remove('active');
    }
}
function closeMenu(event) {
    event.stopPropagation();
    document.querySelector('.hamburger-menu-container').classList.remove('active');
    document.getElementById('menuOverlay').classList.remove('active');
    document.removeEventListener('click', closeMenuOnClickOutside);
}
function closeMenuOnClickOutside(e) {
    const menu = document.querySelector('.hamburger-menu-container');
    const overlay = document.getElementById('menuOverlay');
    if (!menu.contains(e.target)) {
        menu.classList.remove('active');
        overlay.classList.remove('active');
        document.removeEventListener('click', closeMenuOnClickOutside);
    }
}

// Animasi halaman dan tombol navigasi
window.addEventListener('DOMContentLoaded', function() {
    document.body.classList.add('page-fade-in');

    // Fade out saat klik "Lihat Data"
    const lihatDataBtn = document.getElementById('lihatDataBtn');
    if (lihatDataBtn) {
        lihatDataBtn.addEventListener('click', function(e) {
            e.preventDefault();
            document.body.classList.remove('page-fade-in');
            document.body.classList.add('page-fade-out');
            setTimeout(() => {
                window.location.href = this.href;
            }, 600);
        });
    }

    // Fade out saat klik "Kembali ke Halaman Input"
    const kembaliBtn = document.getElementById('kembaliInputBtn');
    if (kembaliBtn) {
        kembaliBtn.addEventListener('click', function(e) {
            e.preventDefault();
            document.body.classList.remove('page-fade-in');
            document.body.classList.add('page-fade-out');
            setTimeout(() => {
                window.location.href = this.href;
            }, 600);
        });
    }
});

const slider = document.getElementById("entries");
const valueDisplay = document.getElementById("entryValue");

slider.addEventListener("input", () => {
    valueDisplay.textContent = slider.value;
});

slider.addEventListener("change", () => {
    // Reload halaman dengan parameter entries baru
    const url = new URL(window.location.href);
    url.searchParams.set('entries', slider.value);
    url.searchParams.set('page', 1); // reset ke halaman 1
    window.location.href = url.toString();
});