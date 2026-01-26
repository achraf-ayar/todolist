<?php
require_once 'config/init.php';
$pageTitle = 'Statistiques';
include 'includes/header.php';
?>

<div class="container-fluid mt-4">
    <div class="row mb-4">
        <div class="col">
            <h2><i class="fas fa-chart-line me-2"></i>Statistiques</h2>
        </div>
    </div>
    
    <!-- Cartes de statistiques -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-muted">Total Tâches</h5>
                    <h2 class="mb-0" id="stat-total">0</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-muted">Complétées cette semaine</h5>
                    <h2 class="mb-0 text-success" id="stat-semaine">0</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-muted">En retard</h5>
                    <h2 class="mb-0 text-danger" id="stat-retard">0</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-muted">Taux de complétion</h5>
                    <h2 class="mb-0 text-primary" id="stat-completion">0%</h2>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Graphiques -->
    <div class="row">
        <!-- Tâches par statut (Camembert) -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Tâches par Statut</h5>
                </div>
                <div class="card-body">
                    <canvas id="chartStatut" style="max-height: 300px;"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Tâches par projet (Barres) -->
        <div class="col-md-8 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Tâches par Projet</h5>
                </div>
                <div class="card-body">
                    <canvas id="chartProjet" style="max-height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Tâches par priorité (Donut) -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-exclamation-circle me-2"></i>Tâches par Priorité</h5>
                </div>
                <div class="card-body">
                    <canvas id="chartPriorite" style="max-height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Configuration des couleurs
const couleurStatut = {
    'a_faire': '#007bff',
    'en_cours': '#ffc107',
    'termine': '#28a745'
};

const couleurPriorite = {
    'basse': '#28a745',
    'normale': '#007bff',
    'haute': '#dc3545'
};

// Charger les statistiques
fetch('ajax/stats.php')
    .then(response => response.json())
    .then(result => {
        if (!result.success) {
            throw new Error(result.message);
        }
        
        const data = result.data;
        
        // Mettre à jour les cartes de statistiques
        document.getElementById('stat-total').textContent = data.total_taches;
        document.getElementById('stat-semaine').textContent = data.taches_completees_semaine;
        document.getElementById('stat-retard').textContent = data.taches_en_retard;
        
        // Calculer le taux de complétion
        const terminees = data.taches_par_statut.find(s => s.statut === 'termine')?.count || 0;
        const tauxCompletion = data.total_taches > 0 
            ? Math.round((terminees / data.total_taches) * 100) 
            : 0;
        document.getElementById('stat-completion').textContent = tauxCompletion + '%';
        
        // Graphique des statuts (Camembert)
        const ctxStatut = document.getElementById('chartStatut').getContext('2d');
        const labelsStatut = {
            'a_faire': 'À faire',
            'en_cours': 'En cours',
            'termine': 'Terminé'
        };
        
        new Chart(ctxStatut, {
            type: 'pie',
            data: {
                labels: data.taches_par_statut.map(s => labelsStatut[s.statut] || s.statut),
                datasets: [{
                    data: data.taches_par_statut.map(s => s.count),
                    backgroundColor: data.taches_par_statut.map(s => couleurStatut[s.statut] || '#6c757d'),
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((context.parsed / total) * 100);
                                return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
        
        // Graphique des projets (Barres)
        const ctxProjet = document.getElementById('chartProjet').getContext('2d');
        new Chart(ctxProjet, {
            type: 'bar',
            data: {
                labels: data.taches_par_projet.map(p => p.projet),
                datasets: [{
                    label: 'Nombre de tâches',
                    data: data.taches_par_projet.map(p => p.count),
                    backgroundColor: '#007bff',
                    borderColor: '#0056b3',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
        
        // Graphique des priorités (Donut)
        const ctxPriorite = document.getElementById('chartPriorite').getContext('2d');
        const labelsPriorite = {
            'basse': 'Basse',
            'normale': 'Normale',
            'haute': 'Haute'
        };
        
        new Chart(ctxPriorite, {
            type: 'doughnut',
            data: {
                labels: data.taches_par_priorite.map(p => labelsPriorite[p.priorite] || p.priorite),
                datasets: [{
                    data: data.taches_par_priorite.map(p => p.count),
                    backgroundColor: data.taches_par_priorite.map(p => couleurPriorite[p.priorite] || '#6c757d'),
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((context.parsed / total) * 100);
                                return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
        
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors du chargement des statistiques');
    });
</script>

<?php include 'includes/footer.php'; ?>
