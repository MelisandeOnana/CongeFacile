document.addEventListener('DOMContentLoaded', function () {
    const submitButton = document.getElementById('submit-button');
    const form = document.querySelector('form');

    if (submitButton && form) {
        form.addEventListener('submit', function () {
            // Désactiver le bouton pour éviter les clics multiples
            submitButton.disabled = true;
            submitButton.textContent = 'Ajout en cours...'; // Optionnel : changer le texte du bouton
        });
    }
});