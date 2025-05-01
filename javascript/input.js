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

// Form validation dengan animasi
document.querySelector('form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    // Reset previous errors
    document.querySelectorAll('.input-error').forEach(el => {
        el.classList.remove('input-error');
    });
    document.querySelectorAll('.error-label').forEach(el => el.remove());
    
    let hasError = false;
    
    // Check each required input
    this.querySelectorAll('input[required]').forEach(input => {
        if (!input.value.trim()) {
            hasError = true;
            input.classList.add('input-error');
            
            // Add error message
            const errorLabel = document.createElement('div');
            errorLabel.className = 'error-label';
            errorLabel.textContent = 'Bidang ini harus diisi';
            input.parentNode.appendChild(errorLabel);
            
            // Show error message with animation
            setTimeout(() => errorLabel.classList.add('show'), 10);
            
            // Remove error state after animation
            setTimeout(() => {
                input.classList.remove('input-error');
            }, 400);
        }
    });
    
    // Check checkboxes
    const checkboxes = document.querySelectorAll('input[type="checkbox"][name="tipe_berkas[]"]');
    const isChecked = Array.from(checkboxes).some(cb => cb.checked);
    
    if (!isChecked) {
        hasError = true;
        const checkboxGroup = document.querySelector('.checkbox-group');
        checkboxGroup.classList.add('error');
        
        // Add error message for checkboxes
        const errorLabel = document.createElement('div');
        errorLabel.className = 'error-label';
        errorLabel.textContent = 'Pilih minimal satu tipe berkas';
        checkboxGroup.parentNode.appendChild(errorLabel);
        
        // Show error message with animation
        setTimeout(() => errorLabel.classList.add('show'), 10);
        
        // Remove error state after animation
        setTimeout(() => {
            checkboxGroup.classList.remove('error');
        }, 400);
    }
    
    if (hasError) {
        // Scroll to first error
        const firstError = document.querySelector('.input-error, .checkbox-group.error');
        if (firstError) {
            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        return;
    }

    // If no errors, proceed with form submission
    const submitBtn = this.querySelector('.button-animated');
    submitBtn.classList.add('loading');
    
    try {
        const formData = new FormData(this);
        const response = await fetch('', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Remove loading state
            submitBtn.classList.remove('loading');
            submitBtn.classList.add('success');
            
            // Create success message
            const successMessage = document.createElement('div');
            successMessage.className = 'success-message';
            successMessage.innerHTML = `
                <div class="success-icon">
                    <svg viewBox="0 0 24 24">
                        <path fill="none" d="M4,12 l5,5 l10,-10" />
                    </svg>
                </div>
                <span>Data berhasil disimpan!</span>
            `;
            document.body.appendChild(successMessage);
            
            // Add success class to container
            document.querySelector('.container-input').classList.add('success-animation');
            
            // Reset form with animation
            setTimeout(() => {
                // Fade out success message
                successMessage.style.opacity = '0';
                successMessage.style.transform = 'translateY(-20px)';
                
                // Reset form
                this.reset();
                document.querySelector('input[name="nop"]').value = '';
                document.querySelector('input[name="tanggal_masuk"]').value = 
                    new Date().toISOString().split('T')[0];
                
                // Remove success states
                submitBtn.classList.remove('success');
                document.querySelector('.container-input')
                    .classList.remove('success-animation');
                
                // Remove success message from DOM
                setTimeout(() => successMessage.remove(), 300);
            }, 2000);
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        submitBtn.classList.remove('loading');
        showError(error.message);
    }
});

// Remove error state on input focus
document.querySelectorAll('input[required]').forEach(input => {
    input.addEventListener('focus', function() {
        this.classList.remove('input-error');
        const errorLabel = this.parentNode.querySelector('.error-label');
        if (errorLabel) {
            errorLabel.classList.remove('show');
            setTimeout(() => errorLabel.remove(), 300);
        }
    });
});

// Remove checkbox error on change
document.querySelectorAll('input[type="checkbox"][name="tipe_berkas[]"]').forEach(cb => {
    cb.addEventListener('change', function() {
        const checkboxGroup = document.querySelector('.checkbox-group');
        checkboxGroup.classList.remove('error');
        const errorLabel = checkboxGroup.parentNode.querySelector('.error-label');
        if (errorLabel) {
            errorLabel.classList.remove('show');
            setTimeout(() => errorLabel.remove(), 300);
        }
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

// Form animations and interactions
document.addEventListener('DOMContentLoaded', function() {
    // Input focus effects
    document.querySelectorAll('.input-group input').forEach(input => {
        input.addEventListener('focus', () => {
            input.parentElement.classList.add('focused');
        });
        
        input.addEventListener('blur', () => {
            input.parentElement.classList.remove('focused');
        });
    });

    // Show error message with animation
    function showError(message) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.textContent = message;
        
        form.insertBefore(errorDiv, form.firstChild);
        
        setTimeout(() => {
            errorDiv.classList.add('show');
        }, 10);
        
        setTimeout(() => {
            errorDiv.classList.remove('show');
            setTimeout(() => errorDiv.remove(), 300);
        }, 3000);
    }
});

// Smooth scroll to inputs on focus
document.querySelectorAll('input, select').forEach(element => {
    element.addEventListener('focus', function() {
        const rect = this.getBoundingClientRect();
        const offset = rect.top + window.scrollY - 120;
        
        window.scrollTo({
            top: offset,
            behavior: 'smooth'
        });
    });
});