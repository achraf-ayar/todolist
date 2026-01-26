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
    
    // Tâches par projet
    $stmt = $pdo->query("
        SELECT p.nom as projet, COUNT(t.id) as count
        FROM projets p
        LEFT JOIN taches t ON t.projet_id = p.id
        GROUP BY p.id, p.nom
        ORDER BY count DESC
    ");
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
            'taches_par_priorite' => $tachesParPriorite,
            'total_taches' => $totalTaches,
            'taches_en_retard' => $tachesEnRetard
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la récupération des statistiques'
    ]);
}
