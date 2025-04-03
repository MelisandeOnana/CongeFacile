function calculateBusinessDays() {
    var startDate = new Date(document.getElementById("request_form_startAt").value);
    var endDate = new Date(document.getElementById("request_form_endAt").value);
    var Days = 0;

    // Vérifier si les dates sont valides
    if (startDate == "Invalid Date" || endDate == "Invalid Date") {
        document.getElementById("result").value = "Veuillez entrer des dates valides";
        return;
    }

    // Si la date de fin est avant la date de début, on affiche un message d'erreur
    if (endDate < startDate) {
        document.getElementById("result").value = "La date de début doit être avant la date de fin";
        return;
    }

        // Créer une copie de startDate pour éviter de modifier l'original
    var tempStartDate = new Date(startDate);
    tempStartDate.setHours(0, 0, 0, 0);

    var tempEndDate = new Date(endDate);
    tempEndDate.setHours(0, 0, 0, 0);

    var Days = 0;

    // Boucle sur chaque jour entre les deux dates
    while (tempStartDate <= tempEndDate) {
        var day = tempStartDate.getDay();
        // 1 à 5 : jours ouvrés (du lundi au vendredi)
        if (day !== 0 && day !== 6) {
            Days++;
        }
        // Passer au jour suivant
        tempStartDate.setDate(tempStartDate.getDate() + 1);
    }
    
    var halfDays = 0;

    // Vérifie si le premier jour est une après-midi ouvrable
    if (startDate.getHours() >= 12 && startDate.getDay() > 0 && startDate.getDay() < 6) {
        halfDays += 0.5;
    }

    // Vérifie si le dernier jour est une matinée ouvrable
    if (endDate.getHours() <= 12 && endDate.getDay() > 0 && endDate.getDay() < 6) {
        halfDays += 0.5;
    }
    

    // Ajuste le nombre total de jours
    Days -= halfDays;

        // Afficher le résultat dans la zone de texte
        document.getElementById("days").value = Days;
    }

document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('request_form_file');
    if (fileInput) {
        fileInput.addEventListener('change', function() {
            const fileName = this.files.length > 0 ? this.files[0].name : 'Aucun fichier sélectionné';
            document.getElementById('file-name').textContent = fileName;
            document.getElementById('file-name').style.color = '#000';
        });
    }
});