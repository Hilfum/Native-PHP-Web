// Format NOP otomatis
document.querySelector('input[name="nop"]').addEventListener('input', function(e) {
    // Hapus semua karakter non-digit
    let value = this.value.replace(/\D/g, '');
    // Batasi hingga 18 digit
    if (value.length > 18) {
        value = value.slice(0, 18);
    }
    // Format NOP dengan titik
    let formattedValue = '';
    for (let i = 0; i < value.length; i++) {
        if (i === 2 || i === 4 || i === 7 || i === 10 || i === 13 || i === 17) {
            formattedValue += '.';
        }
        formattedValue += value[i];
    }
    this.value = formattedValue;
});

// Fungsi untuk mengecek perubahan pada form
function isFormChanged() {
    const form = document.querySelector('form');
    const inputs = form.querySelectorAll('input[type="text"]');
    const checkboxes = form.querySelectorAll('input[type="checkbox"]');
    let changed = false;

    // Cek perubahan pada input text
    inputs.forEach(input => {
        if (input.value !== input.defaultValue) {
            changed = true;
        }
    });

    // Cek perubahan pada checkbox
    // Ambil data original dari atribut data-original-tipe-berkas pada form
    const originalTipeBerkas = JSON.parse(form.getAttribute('data-original-tipe-berkas'));
    const currentTipeBerkas = Array.from(checkboxes)
        .filter(cb => cb.checked)
        .map(cb => cb.value);

    if (JSON.stringify(originalTipeBerkas.sort()) !== JSON.stringify(currentTipeBerkas.sort())) {
        changed = true;
    }

    return changed;
}

// Animasi masuk halaman
window.addEventListener('DOMContentLoaded', function() {
    document.body.classList.add('page-fade-in');

    // Handler tombol batal
    var cancelBtn = document.getElementById('cancelButton');
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('Apakah Anda yakin ingin membatalkan perubahan dan kembali?')) {
                window.location.href = 'show.php';
            }
        });
    }

    // Handler tombol simpan (submit)
    var form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!confirm('Apakah Anda yakin ingin menyimpan perubahan?')) {
                e.preventDefault();
            }
        });
    }
});