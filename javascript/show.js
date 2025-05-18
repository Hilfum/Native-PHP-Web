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
    
    const tableArea = document.getElementById('tableArea');
    const btnConfirm = form.querySelector('.btn-confirm');
    const originalText = btnConfirm.textContent;
    
    // Tambah loading state ke button
    btnConfirm.textContent = 'Processing...';
    btnConfirm.disabled = true;
    
    // Animasi slide up (hilang ke atas)
    tableArea.style.opacity = '0';
    tableArea.style.transform = 'translateY(-30px)';

    // Ambil parameter URL saat ini
    const params = new URLSearchParams(window.location.search);
    const currentPage = params.get('page') || 1;
    const entries = params.get('entries') || 10;
    
    fetch(form.action, {
        method: 'POST',
        body: new FormData(form)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update tabel dengan data terbaru
            fetch(`show_data.php?page=${currentPage}&entries=${entries}`)
                .then(res => res.text())
                .then(html => {
                    setTimeout(() => {
                        tableArea.innerHTML = html;
                        
                        // Reset dan animasi slide down (muncul dari atas)
                        tableArea.style.transition = 'none';
                        tableArea.style.opacity = '0';
                        tableArea.style.transform = 'translateY(-30px)';
                        
                        // Force reflow
                        void tableArea.offsetWidth;
                        
                        // Aktifkan animasi
                        tableArea.style.transition = 'opacity 0.3s, transform 0.3s';
                        tableArea.style.opacity = '1';
                        tableArea.style.transform = 'translateY(0)';
                        
                        // Reset button state
                        btnConfirm.textContent = originalText;
                        btnConfirm.disabled = false;
                    }, 200);
                });
        } else {
            alert(data.message || 'Gagal konfirmasi');
            btnConfirm.textContent = originalText;
            btnConfirm.disabled = false;
            tableArea.style.opacity = '1';
            tableArea.style.transform = 'translateY(0)';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat memproses konfirmasi');
        btnConfirm.textContent = originalText;
        btnConfirm.disabled = false;
        tableArea.style.opacity = '1';
        tableArea.style.transform = 'translateY(0)';
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

    // Event untuk form search/filter AJAX
    const searchForm = document.querySelector('.search-container form');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(searchForm);
            const params = new URLSearchParams(formData);
            // Tambahkan entries dan page
            params.set('entries', document.getElementById('entries').value);
            params.set('page', 1);
            const tableArea = document.getElementById('tableArea');
            // Animasi slide up
            tableArea.style.opacity = 0;
            tableArea.style.transform = "translateY(-30px)";
            fetch('show_data.php?' + params.toString())
                .then(res => res.text())
                .then(html => {
                    setTimeout(() => {
                        tableArea.innerHTML = html;
                        // Reset dan animasi slide down
                        tableArea.style.transition = "none";
                        tableArea.style.opacity = 0;
                        tableArea.style.transform = "translateY(-30px)";
                        void tableArea.offsetWidth;
                        tableArea.style.transition = "opacity 0.3s, transform 0.3s";
                        tableArea.style.opacity = 1;
                        tableArea.style.transform = "translateY(0)";
                    }, 200);
                });
        });
    }

    // Tambahkan event listener untuk tombol edit
    document.querySelectorAll('.btn-edit').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const href = this.getAttribute('href');
            
            // Tambahkan class untuk animasi fade out
            document.body.classList.add('page-fade-out');
            
            // Tunggu animasi selesai baru pindah halaman
            setTimeout(() => {
                window.location.href = href;
            }, 600); // 600ms sesuai dengan durasi animasi CSS
        });
    });
});