document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    const form = document.getElementById('loginForm');
    const username = document.getElementById('username');
    const password = document.getElementById('password');
    const togglePassword = document.querySelector('.toggle-password');
    
    // Toggle password visibility
    if (togglePassword) {
        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });
    }
    
    // Real-time validation
    function validateField(field) {
        const invalidFeedback = field.nextElementSibling;
        if(field.value.trim() === '') {
            field.classList.add('is-invalid');
            if (invalidFeedback) invalidFeedback.style.display = 'block';
            return false;
        } else {
            field.classList.remove('is-invalid');
            if (invalidFeedback) invalidFeedback.style.display = 'none';
            return true;
        }
    }
    
    if (username) username.addEventListener('input', () => validateField(username));
    if (password) password.addEventListener('input', () => validateField(password));
    
    // Form submission
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent default full-page submission initially
            
            const isValidUsername = validateField(username);
            const isValidPassword = validateField(password);
            
            if(isValidUsername && isValidPassword) {
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    const spinner = submitBtn.querySelector('.spinner-border');
                    if (spinner) spinner.classList.remove('d-none');
                }
                
                // Simulate AJAX submission (this will still cause a full page reload via form.submit())
                // As discussed, for a true AJAX solution, you would use fetch() API here.
                setTimeout(() => {
                    if (form) form.submit(); // This performs the actual full page POST submission
                }, 1500);
            }
        });
    }
    
    // Accessibility enhancements
    document.querySelectorAll('input').forEach(input => {
        input.addEventListener('keydown', function(e) {
            if(e.key === 'Enter') {
                const form = this.closest('form');
                if(form) {
                    const submitButton = form.querySelector('button[type="submit"]');
                    if(submitButton && !submitButton.disabled) {
                        submitButton.click();
                    }
                }
            }
        });
    });
});