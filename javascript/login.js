document.addEventListener('DOMContentLoaded', function() {
    const loginContainer = document.getElementById('loginContainer');
    const loginForm = document.getElementById('loginForm');
    const errorMessage = document.getElementById('errorMessage');
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');

    // Animasi munculnya form
    setTimeout(() => {
        loginContainer.classList.add('show');
    }, 100);

    // Toggle password visibility
    togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.classList.toggle('show');
    });

    // Handle form submission
    loginForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const submitButton = this.querySelector('button[type="submit"]');
        submitButton.classList.add('loading');
        
        const formData = new FormData(this);

        try {
            const response = await fetch('login.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Success animation sequence
                submitButton.classList.add('success');
                submitButton.querySelector('.button-text').textContent = 'Berhasil!';
                
                // Animate container
                setTimeout(() => {
                    document.body.classList.add('fade-out');
                    loginContainer.classList.add('slide-out');
                }, 600);

                // Background blur increase
                document.querySelector('.background').style.filter = 'blur(20px)';
                
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 1400);
            } else {
                errorMessage.textContent = data.message;
                errorMessage.classList.add('show');
                
                loginContainer.classList.add('shake');
                setTimeout(() => {
                    loginContainer.classList.remove('shake');
                }, 650);
            }
        } catch (error) {
            console.error('Error:', error);
            errorMessage.textContent = 'Terjadi kesalahan. Silakan coba lagi.';
            errorMessage.classList.add('show');
        } finally {
            if (!data?.success) {
                submitButton.classList.remove('loading');
            }
        }
    });

    // Add input focus effects
    document.querySelectorAll('.input-group input').forEach(input => {
        input.addEventListener('focus', () => {
            input.parentElement.classList.add('focused');
            // Subtle scale effect on container
            loginContainer.style.transform = 'scale(1.01)';
        });
        
        input.addEventListener('blur', () => {
            if (!input.value) {
                input.parentElement.classList.remove('focused');
            }
            loginContainer.style.transform = 'scale(1)';
        });
    });
});