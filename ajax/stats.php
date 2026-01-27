<?php
require_once '../config/init.php';

header('Content-Type: application/json');

try {
    // Tâches par statut
    $stmt = $pdo->query("
        SELECT statut, COUNT(*) as count
        FROM taches
        GROUP BY statut
    ");
    $tachesParStatut = $stmt->fetchAll();
    
    $tachesParProjet = $stmt->fetchAll();
    
    // Tâches sans projet
    $stmt = $pdo->query("
        SELECT COUNT(*) as count
        FROM taches
        WHERE projet_id IS NULL
    ");
    $tachesSansProjet = $stmt->fetch()['count'];
    
    if ($tachesSansProjet > 0) {
        $tachesParProjet[] = [
            'projet' => 'Sans projet',
            'count' => $tachesSansProjet
        ];
    }
    
    // Tâches complétées cette semaine
    $stmt = $pdo->query("
        SELECT COUNT(*) as count
        FROM taches
        WHERE statut = 'termine'
        AND date_creation >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    ");
    $tachesCompleteesSemaine = $stmt->fetch()['count'];
    
    // Tâches par priorité
    $stmt = $pdo->query("
        SELECT priorite, COUNT(*) as count
        FROM taches
        GROUP BY priorite
    ");
    $tachesParPriorite = $stmt->fetchAll();
    
    // Tâches terminées par jour 
    $stmt = $pdo->query("\n        SELECT DATE(date_creation) as jour, COUNT(*) as count\n        FROM taches\n        WHERE statut = 'termine'\n          AND date_creation >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)\n        GROUP BY DATE(date_creation)\n        ORDER BY jour ASC\n    ");
    $tachesTermineesParJour = $stmt->fetchAll();

    // Total des tâches
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM taches");
    $totalTaches = $stmt->fetch()['count'];
    
    // Tâches en retard
    $stmt = $pdo->query("
        SELECT COUNT(*) as count
        FROM taches
        WHERE statut != 'termine'
        AND date_echeance < CURDATE()
    ");
    $tachesEnRetard = $stmt->fetch()['count'];
    
    echo json_encode([
        'success' => true,
        'data' => [
            'taches_par_statut' => $tachesParStatut,
            'taches_par_projet' => $tachesParProjet,
            'taches_completees_semaine' => $tachesCompleteesSemaine,
            'total_taches' => $totalTaches,
            'taches_en_retard' => $tachesEnRetard,
            'taches_terminees_par_jour' => $tachesTermineesParJour
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la récupération des statistiques'
    ]);
}
