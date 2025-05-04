document.addEventListener('DOMContentLoaded', function () {
    const departmentSelect = document.getElementById('user_department');
    const managerSelect = document.getElementById('user_manager');

    // Fonction pour charger les managers d'un département
    function loadManagers(departmentId) {
        // Réinitialiser les options du champ manager
        managerSelect.innerHTML = '';

        if (!departmentId) {
            return;
        }

        fetch(`/managers/by-department?department=${departmentId}`)
            .then(response => response.json())
            .then(data => {
                if (data.length === 0) {
                    // Pas de manager trouvé pour ce département
                    return;
                }

                data.forEach(function (manager) {
                    const option = document.createElement('option');
                    option.value = manager.id;
                    option.textContent = manager.name;
                    managerSelect.appendChild(option);
                });

                // Activer seulement s'il y a des managers
                //managerSelect.disabled = false;

                // Sélectionner automatiquement le premier manager
                //managerSelect.value = data[0].id;
            })
            .catch(error => {
                console.error('Erreur:', error);
            });
    }

    // Charger les managers au changement de département
    departmentSelect.addEventListener('change', function () {
        loadManagers(this.value);
    });

    // Charger les managers au chargement initial de la page
    loadManagers(departmentSelect.value);
});