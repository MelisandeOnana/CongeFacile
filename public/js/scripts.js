// public/js/scripts.js

document.addEventListener('DOMContentLoaded', function() {
    const togglePassword = document.getElementById('toggle-password');
    const passwordField = document.getElementById('password');

    togglePassword.addEventListener('click', function() {
        const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordField.setAttribute('type', type);
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
