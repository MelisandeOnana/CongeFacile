// public/js/scripts.js

document.addEventListener('DOMContentLoaded', function() {
    const togglePasswordIcons = document.querySelectorAll('[id^="toggle-password"]');
    
    togglePasswordIcons.forEach(function(togglePassword) {
        const passwordFieldId = togglePassword.getAttribute('data-target');
        const passwordField = document.getElementById(passwordFieldId);

        togglePassword.addEventListener('click', function() {
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
        });
    });
});

//  Cette fonction permet de basculer la visibilité d'un élément HTML en fonction de son identifiant (id).
function toggleLinks(id) {
    var element = document.getElementById(id);
    if (element.classList.contains('hidden')) {
        element.classList.remove('hidden');
    } else {
        element.classList.add('hidden');
    }
}

function updateUrl(param, value) {
    const url = new URL(window.location.href);
    url.searchParams.set(param, value);
    url.searchParams.delete('page'); // Supprime le paramètre 'page'
    window.history.pushState({}, '', url);
    window.location.reload(); 
}

document.querySelector('form').addEventListener('submit', function() {
    document.querySelectorAll('select[disabled]').forEach(function(select) {
        select.disabled = false;
    });
});