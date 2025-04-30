// Format NOP otomatis
document.querySelector('input[name="nop"]').addEventListener('input', function(e) {
    let value = this.value.replace(/\D/g, '');
    if (value.length > 18) value = value.slice(0, 18);
    let formattedValue = '';
    for (let i = 0; i < value.length; i++) {
        if (i === 2 || i === 4 || i === 7 || i === 10 || i === 13 || i === 17) {
            formattedValue += '.';
        }
        formattedValue += value[i];
    }
    this.value = formattedValue;
});

// Submit form via fetch
document.querySelector('form').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    fetch('', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if (data.success) {
            this.reset();
            document.querySelector('input[name="nop"]').value = '';
        }
    })
    .catch(error => {
        alert('Terjadi kesalahan: ' + error);
    });
});

// Menu & overlay
function handleLogout(e) {
    e.preventDefault();
    if(confirm('Apakah Anda yakin ingin logout?')) {
        window.location.href = 'logout.php?confirm=yes';
    }
}
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

// Animasi halaman dan tombol lihat data
window.addEventListener('DOMContentLoaded', function() {
    document.body.classList.add('page-fade-in');
    document.querySelector('.container-input').classList.add('page-fade-in');
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
});

document.addEventListener('DOMContentLoaded', function() {
    var container = document.querySelector('.container-input');
    if (container) {
        // Trigger reflow for transition
        setTimeout(function() {
            container.classList.add('show');
        }, 10);
    }
});