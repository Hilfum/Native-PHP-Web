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

document.addEventListener('DOMContentLoaded', function() {
    const slider = document.getElementById("entries");
    const valueDisplay = document.getElementById("entryValue");
    const tableArea = document.getElementById("tableArea");

    // Untuk slider
    slider.addEventListener("input", () => {
        valueDisplay.textContent = slider.value;
    });

    slider.addEventListener("change", () => {
        loadTable(1, slider.value);
    });

    // Untuk pagination (delegasi event)
    tableArea.addEventListener('click', function(e) {
        if (e.target.classList.contains('page-link')) {
            e.preventDefault();
            const url = new URL(e.target.href, window.location.origin);
            const page = url.searchParams.get('page') || 1;
            const entries = slider.value;
            loadTable(page, entries);
        }
    });

    function loadTable(page, entries) {
        const params = new URLSearchParams(window.location.search);
        params.set('page', page);
        params.set('entries', entries);

        // Slide up (hilang ke atas)
        tableArea.style.opacity = 0;
        tableArea.style.transform = "translateY(-30px)";

        fetch('show_data.php?' + params.toString())
            .then(res => res.text())
            .then(html => {
                setTimeout(() => {
                    tableArea.innerHTML = html;
                    // Slide down (muncul dari atas)
                    tableArea.style.transition = "none";
                    tableArea.style.opacity = 0;
                    tableArea.style.transform = "translateY(-30px)";
                    // Force reflow
                    void tableArea.offsetWidth;
                    tableArea.style.transition = "opacity 0.3s, transform 0.3s";
                    tableArea.style.opacity = 1;
                    tableArea.style.transform = "translateY(0)";
                }, 200);
            });
    }
});