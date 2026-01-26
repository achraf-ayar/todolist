<?php
require_once '../config/init.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

function syncTags(PDO $pdo, int $tacheId, array $tagIds): void {
    $cleanIds = array_values(array_unique(array_filter(array_map('intval', $tagIds), function ($id) {
        return $id > 0;
    })));

    $pdo->prepare('DELETE FROM taches_etiquettes WHERE tache_id = ?')->execute([$tacheId]);

    if (empty($cleanIds)) {
        return;
    }

    $stmt = $pdo->prepare('INSERT INTO taches_etiquettes (tache_id, etiquette_id) VALUES (?, ?)');
    foreach ($cleanIds as $tagId) {
        $stmt->execute([$tacheId, $tagId]);
    }
}

try {
    switch ($action) {
        case 'add':
            $titre = trim($_POST['titre'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $projet_id = !empty($_POST['projet_id']) ? intval($_POST['projet_id']) : null;
            $priorite = $_POST['priorite'] ?? 'normale';
            $statut = $_POST['statut'] ?? 'a_faire';
            $date_echeance = !empty($_POST['date_echeance']) ? $_POST['date_echeance'] : null;
            $tags = $_POST['tags'] ?? [];
            
            if (empty($titre)) {
                throw new Exception('Le titre de la tâche est requis');
            }
            
            $stmt = $pdo->prepare("
                INSERT INTO taches (titre, description, projet_id, priorite, statut, date_echeance) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$titre, $description, $projet_id, $priorite, $statut, $date_echeance]);
            $tacheId = (int) $pdo->lastInsertId();
            syncTags($pdo, $tacheId, $tags);
            
            echo json_encode([
                'success' => true,
                'message' => 'Tache créée avec succès',
                'id' => $tacheId
            ]);
            break;
            
        case 'edit':
            $id = intval($_POST['id'] ?? 0);
            $titre = trim($_POST['titre'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $projet_id = !empty($_POST['projet_id']) ? intval($_POST['projet_id']) : null;
            $priorite = $_POST['priorite'] ?? 'normale';
            $statut = $_POST['statut'] ?? 'a_faire';
            $date_echeance = !empty($_POST['date_echeance']) ? $_POST['date_echeance'] : null;
            $tags = $_POST['tags'] ?? [];
            
            if (empty($titre)) {
                throw new Exception('Le titre de la tache est requis');
            }
            
            $stmt = $pdo->prepare("
                UPDATE taches 
                SET titre = ?, description = ?, projet_id = ?, priorite = ?, statut = ?, date_echeance = ?
                WHERE id = ?
            ");
            $stmt->execute([$titre, $description, $projet_id, $priorite, $statut, $date_echeance, $id]);
            syncTags($pdo, $id, $tags);
            
            echo json_encode([
                'success' => true,
                'message' => 'Tache modifiée avec succès'
            ]);
            break;
            
        case 'delete':
            $data = json_decode(file_get_contents('php://input'), true);
            $id = intval($data['id'] ?? 0);
            
            if ($id <= 0) {
                throw new Exception('ID invalide');
            }
            
            $stmt = $pdo->prepare("DELETE FROM taches WHERE id = ?");
            $stmt->execute([$id]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Tache supprimée avec succès'
            ]);
            break;
            
        case 'updateStatus':
            $data = json_decode(file_get_contents('php://input'), true);
            $id = intval($data['id'] ?? 0);
            $statut = $data['statut'] ?? '';
            
            if ($id <= 0 || !in_array($statut, ['a_faire', 'en_cours', 'termine'])) {
                throw new Exception('Données invalides');
            }
            
            $stmt = $pdo->prepare("UPDATE taches SET statut = ? WHERE id = ?");
            $stmt->execute([$statut, $id]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Statut mis à jour'
            ]);
            break;

        case 'updateOrder':
            $data = json_decode(file_get_contents('php://input'), true);
            $updates = $data['updates'] ?? [];
            
            if (empty($updates)) {
                throw new Exception('Pas de mises à jour');
            }
            
            $stmt = $pdo->prepare("UPDATE taches SET statut = ?, ordre = ? WHERE id = ?");
            foreach ($updates as $item) {
                $id = intval($item['id'] ?? 0);
                $statut = $item['statut'] ?? '';
                $ordre = intval($item['ordre'] ?? 0);
                
                if ($id <= 0 || !in_array($statut, ['a_faire', 'en_cours', 'termine'])) {
                    continue;
                }
                
                $stmt->execute([$statut, $ordre, $id]);
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Ordre mis à jour'
            ]);
            break;
            
        default:
            throw new Exception('Action non reconnue');
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
