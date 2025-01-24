function calculateBusinessDays() {
    var startDate = new Date(document.getElementById("request_form_startAt").value);
    var endDate = new Date(document.getElementById("request_form_endAt").value);
    var Days = 0;

    // Vérifier si les dates sont valides
    if (startDate == "Invalid Date" || endDate == "Invalid Date") {
        document.getElementById("result").value = "Veuillez entrer des dates valides";
        return;
    }

    // Si la date de fin est avant la date de début, on échange les valeurs
    if (endDate < startDate) {
        var temp = startDate;
        startDate = endDate;
        endDate = temp;
    }

    // Boucle sur chaque jour entre les deux dates
    while (startDate <= endDate) {
        var day = startDate.getDay();
        // 1 à 5 : jours ouvrés (du lundi au vendredi)
        if (day !== 0 && day !== 6) {
            Days++;
        }
        // Passer au jour suivant
        startDate.setDate(startDate.getDate() + 1);
    }

    // Afficher le résultat dans la zone de texte
    document.getElementById("days").value = Days;
}