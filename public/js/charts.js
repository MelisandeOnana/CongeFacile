document.addEventListener('DOMContentLoaded', function () {
    // Données récupérées depuis les attributs HTML
    const requestTypes = JSON.parse(document.getElementById('chart-data').dataset.requestTypes);
    const countRequest = JSON.parse(document.getElementById('chart-data').dataset.countRequest);
    const acceptancePercentage = JSON.parse(document.getElementById('chart-data').dataset.acceptancePercentage);

    // Fonction pour générer des couleurs aléatoires
    function generateColors(count, opacity = 0.5) {
        const colors = [];
        for (let i = 0; i < count; i++) {
            const r = Math.floor(Math.random() * 256); // Rouge
            const g = Math.floor(Math.random() * 256); // Vert
            const b = Math.floor(Math.random() * 256); // Bleu
            colors.push(`rgba(${r}, ${g}, ${b}, ${opacity})`);
        }
        return colors;
    }

    // Générer des couleurs pour le graphique Doughnut
    const doughnutColors = generateColors(requestTypes.length);

    // Graphique Doughnut
    const ctxDoughnut = document.getElementById('doughnutChart').getContext('2d');
    new Chart(ctxDoughnut, {
        type: 'doughnut',
        data: {
            labels: requestTypes,
            datasets: [{
                label: 'Nombre de demandes',
                data: countRequest,
                backgroundColor: doughnutColors, // Utilisation des couleurs générées
                borderColor: 'rgb(255, 255, 255)', // Bordures blanches
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            aspectRatio: 2,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        font: {
                            size: 14
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            let label = context.label || '';
                            let value = context.raw || 0;
                            return `${label}: ${value}`;
                        }
                    }
                }
            }
        }
    });

    // Graphique Line (Pourcentage d'acceptation)
    const ctxLine = document.getElementById('lineChart').getContext('2d');
    new Chart(ctxLine, {
        type: 'line',
        data: {
            labels: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
            datasets: [{
                label: 'Pourcentage d\'acceptation',
                data: acceptancePercentage,
                borderColor: 'rgba(75, 192, 192, 1)', // Couleur de la ligne
                backgroundColor: 'rgba(255, 255, 255, 0.2)', // Couleur de fond
                borderWidth: 2,
                tension: 0.4,
                fill: true,
            }]
        },
        options: {
            responsive: true,
            aspectRatio: 2,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        padding: 20,
                        font: {
                            size: 14
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            let value = context.raw || 0;
                            return `${value.toFixed(2)}%`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function (value) {
                            return `${value}%`;
                        }
                    }
                }
            }
        }
    });
});