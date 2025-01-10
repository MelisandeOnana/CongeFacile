// public/js/scripts.js

document.addEventListener('DOMContentLoaded', function() {
    const togglePassword = document.getElementById('toggle-password');
    const passwordField = document.getElementById('password');

    togglePassword.addEventListener('click', function() {
        const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordField.setAttribute('type', type);
    });
});