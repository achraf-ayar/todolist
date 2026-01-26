<?php
require_once '../config/init.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'add':
            $nom = trim($_POST['nom'] ?? '');
            $couleur = trim($_POST['couleur'] ?? '#007bff');
            
            if (empty($nom)) {
                throw new Exception('Le nom du projet est requis');
            }
            
            $stmt = $pdo->prepare("INSERT INTO projets (nom, couleur) VALUES (?, ?)");
            $stmt->execute([$nom, $couleur]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Projet créé avec succès',
                'id' => $pdo->lastInsertId()
            ]);
            break;
            
        case 'edit':
            $id = intval($_POST['id'] ?? 0);
            $nom = trim($_POST['nom'] ?? '');
            $couleur = trim($_POST['couleur'] ?? '#007bff');
            
            if (empty($nom)) {
                throw new Exception('Le nom du projet est requis');
            }
            
            $stmt = $pdo->prepare("UPDATE projets SET nom = ?, couleur = ? WHERE id = ?");
            $stmt->execute([$nom, $couleur, $id]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Projet modifié avec succès'
            ]);
            break;
            
        case 'delete':
            $data = json_decode(file_get_contents('php://input'), true);
            $id = intval($data['id'] ?? 0);
            
            if ($id <= 0) {
                throw new Exception('ID invalide');
            }
            
            $stmt = $pdo->prepare("DELETE FROM projets WHERE id = ?");
            $stmt->execute([$id]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Projet supprimé avec succès'
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
