// Ce script permet de basculer la visibilité du mot de passe lorsque l'utilisateur clique sur l'icône de l'œil.
document.addEventListener('DOMContentLoaded', function() {
    const togglePasswordIcons = document.querySelectorAll('.toggle-password');
    
    togglePasswordIcons.forEach(function(togglePassword) {
        const passwordFieldId = togglePassword.getAttribute('data-target');
        const passwordField = document.getElementById(passwordFieldId);

        togglePassword.addEventListener('click', function() {
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            this.classList.toggle('bi-eye');
            this.classList.toggle('bi-eye-slash');
        });
    });

    const passwordFields = document.querySelectorAll('input[type="password"]');
    passwordFields.forEach(field => {
        field.addEventListener('input', function () {
            const target = document.querySelector(`.toggle-password[data-target="${field.id}"]`);
            if (field.value) {
                target.classList.remove('hidden');
            } else {
                target.classList.add('hidden');
            }
        });
    });

    // filtrage équipes 
    const filters = ['filter_lastName', 'filter_firstName', 'filter_email', 'filter_position', 'filter_vacationDays'];

    filters.forEach(function(filter) {
        document.getElementById(filter).addEventListener('input', function() {
            updateResults();
        });
    });

    function updateResults() {
        const lastName = document.getElementById('filter_lastName').value.toLowerCase();
        const firstName = document.getElementById('filter_firstName').value.toLowerCase();
        const email = document.getElementById('filter_email').value.toLowerCase();
        const position = document.getElementById('filter_position').value.toLowerCase();
        const vacationDays = document.getElementById('filter_vacationDays').value.toLowerCase();

        // Vérifier si tous les champs de filtre sont vides
        if (!lastName && !firstName && !email && !position && !vacationDays) {
            // Si tous les champs sont vides, afficher toutes les lignes et supprimer le message "Aucun résultat trouvé"
            document.querySelectorAll('tbody tr').forEach(function(row) {
                row.style.display = '';
            });
            const noResultsRow = document.getElementById('no-results-row');
            if (noResultsRow) {
                noResultsRow.remove();
            }
            return;
        }

        let hasResults = false;

        document.querySelectorAll('tbody tr').forEach(function(row) {
            const rowLastName = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
            const rowFirstName = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
            const rowEmail = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
            const rowPosition = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
            const rowVacationDays = row.querySelector('td:nth-child(5)').textContent.toLowerCase();

            if (rowLastName.includes(lastName) && rowFirstName.includes(firstName) && rowEmail.includes(email) && rowPosition.includes(position) && rowVacationDays.includes(vacationDays)) {
                row.style.display = '';
                hasResults = true;
            } else {
                row.style.display = 'none';
            }
        });

        const noResultsRow = document.getElementById('no-results-row');
        if (!hasResults) {
            if (!noResultsRow) {
                const tbody = document.querySelector('tbody');
                const tr = document.createElement('tr');
                tr.id = 'no-results-row';
                const td = document.createElement('td');
                td.colSpan = 6;
                td.className = 'text-center text-gray-700';
                td.textContent = 'Aucun résultat trouvé';
                tr.appendChild(td);
                tbody.appendChild(tr);
            }
        } else {
            if (noResultsRow) {
                noResultsRow.remove();
            }
        }
    }

    const getCellValue = (tr, idx) => tr.children[idx].innerText || tr.children[idx].textContent;

    const comparer = (idx, asc) => (a, b) => ((v1, v2) =>
        v1 !== '' && v2 !== '' && !isNaN(v1) && !isNaN(v2) ? v1 - v2 : v1.toString().localeCompare(v2)
    )(getCellValue(asc ? a : b, idx), getCellValue(asc ? b : a, idx));

    document.querySelectorAll('th[data-column]').forEach(th => th.addEventListener('click', function() {
        const table = th.closest('table');
        const columnIndex = th.getAttribute('data-column');
        Array.from(table.querySelectorAll('tr:nth-child(n+3)'))
            .sort(comparer(columnIndex, this.asc = !this.asc))
            .forEach(tr => table.appendChild(tr));
    }));

    // Gestion des chevrons pour le tri des colonnes
    const chevronUpFirstName = document.getElementById('chevron-up-firstName');
    const chevronDownFirstName = document.getElementById('chevron-down-firstName');
    const chevronUpLastName = document.getElementById('chevron-up-lastName');
    const chevronDownLastName = document.getElementById('chevron-down-lastName');
    const chevronUpEmail = document.getElementById('chevron-up-email');
    const chevronDownEmail = document.getElementById('chevron-down-email');
    const chevronUpPosition = document.getElementById('chevron-up-position');
    const chevronDownPosition = document.getElementById('chevron-down-position');
    const chevronUpVacationDays = document.getElementById('chevron-up-vacationDays');
    const chevronDownVacationDays = document.getElementById('chevron-down-vacationDays');

    chevronUpFirstName.addEventListener('click', function() {
        sortTable(1, 'asc');
    });

    chevronDownFirstName.addEventListener('click', function() {
        sortTable(1, 'desc');
    });

    chevronUpLastName.addEventListener('click', function() {
        sortTable(0, 'asc');
    });

    chevronDownLastName.addEventListener('click', function() {
        sortTable(0, 'desc');
    });

    chevronUpEmail.addEventListener('click', function() {
        sortTable(2, 'asc');
    });

    chevronDownEmail.addEventListener('click', function() {
        sortTable(2, 'desc');
    });

    chevronUpPosition.addEventListener('click', function() {
        sortTable(3, 'asc');
    });

    chevronDownPosition.addEventListener('click', function() {
        sortTable(3, 'desc');
    });

    chevronUpVacationDays.addEventListener('click', function() {
        sortTable(4, 'asc');
    });

    chevronDownVacationDays.addEventListener('click', function() {
        sortTable(4, 'desc');
    });

    function sortTable(columnIndex, order) {
        const table = document.querySelector('table tbody');
        const rows = Array.from(table.querySelectorAll('tr'));

        rows.sort((a, b) => {
            const cellA = a.children[columnIndex].innerText.toLowerCase();
            const cellB = b.children[columnIndex].innerText.toLowerCase();

            if (order === 'asc') {
                return cellA.localeCompare(cellB);
            } else {
                return cellB.localeCompare(cellA);
            }
        });

        rows.forEach(row => table.appendChild(row));
    }
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
