<?php require_once 'includes/header.php'; ?>

<div class="row mb-4">
    <div class="col-md-6">
        <h2><i class="fas fa-tasks"></i> Mes Taches</h2>
    </div>
    <div class="col-md-6 text-end">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTaskModal">
            <i class="fas fa-plus"></i> Nouvelle Tache
        </button>
    </div>
</div>

<div class="card mb-4 border-0">
    <div class="card-body">
        <form method="GET" id="filterForm" class="row g-3">
            <div class="col-md-3">
                <label class="form-label"><i class="fas fa-folder"></i> Projet</label>
                <select name="projet" class="form-select" id="filterProjet">
                    <option value="">Tous les projets</option>
                    <?php
                    $projets = $pdo->query("SELECT * FROM projets ORDER BY nom")->fetchAll();
                    foreach ($projets as $p) {
                        $selected = (isset($_GET['projet']) && $_GET['projet'] == $p['id']) ? 'selected' : '';
                        echo "<option value='{$p['id']}' $selected>" . htmlspecialchars($p['nom']) . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label"><i class="fas fa-exclamation-circle"></i> Priorité</label>
                <select name="priorite" class="form-select" id="filterPriorite">
                    <option value="">Toutes</option>
                    <option value="basse" <?= (isset($_GET['priorite']) && $_GET['priorite'] == 'basse') ? 'selected' : '' ?>>Basse</option>
                    <option value="normale" <?= (isset($_GET['priorite']) && $_GET['priorite'] == 'normale') ? 'selected' : '' ?>>Normale</option>
                    <option value="haute" <?= (isset($_GET['priorite']) && $_GET['priorite'] == 'haute') ? 'selected' : '' ?>>Haute</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label"><i class="fas fa-check-circle"></i> Statut</label>
                <select name="statut" class="form-select" id="filterStatut">
                    <option value="">Tous</option>
                    <option value="a_faire" <?= (isset($_GET['statut']) && $_GET['statut'] == 'a_faire') ? 'selected' : '' ?>>À faire</option>
                    <option value="en_cours" <?= (isset($_GET['en_cours']) && $_GET['statut'] == 'en_cours') ? 'selected' : '' ?>>En cours</option>
                    <option value="termine" <?= (isset($_GET['termine']) && $_GET['statut'] == 'termine') ? 'selected' : '' ?>>Terminé</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter"></i> Filtrer
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php

$where = [];
$params = [];

if (!empty($_GET['projet'])) {
    $where[] = "t.projet_id = ?";
    $params[] = $_GET['projet'];
}

if (!empty($_GET['priorite'])) {
    $where[] = "t.priorite = ?";
    $params[] = $_GET['priorite'];
}

if (!empty($_GET['statut'])) {
    $where[] = "t.statut = ?";
    $params[] = $_GET['statut'];
}

$whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

$sql = "
    SELECT 
        t.*,
        p.nom as projet_nom,
        p.couleur as projet_couleur
    FROM taches t
    LEFT JOIN projets p ON t.projet_id = p.id
    $whereClause
    ORDER BY 
        FIELD(t.priorite, 'haute', 'normale', 'basse'),
        FIELD(t.statut, 'en_cours', 'a_faire', 'termine'),
        t.date_echeance ASC
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$taches = $stmt->fetchAll();
?>

<!-- Liste des tâches -->
<div class="row">
    <?php foreach ($taches as $tache): ?>
            <?php
          
            $prioriteBadge = [
                'basse' => 'bg-success',
                'normale' => 'bg-primary',
                'haute' => 'bg-danger'
            ];
            
          
            $statutBadge = [
                'a_faire' => 'bg-secondary',
                'en_cours' => 'bg-warning',
                'termine' => 'bg-success'
            ];
            
            $statutText = [
                'a_faire' => 'À faire',
                'en_cours' => 'En cours',
                'termine' => 'Terminé'
            ];
            
          
            $enRetard = false;
            if ($tache['date_echeance'] && $tache['statut'] != 'termine') {
                $enRetard = strtotime($tache['date_echeance']) < time();
            }
            ?>
            
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card h-100 shadow-sm">
                    <?php if ($tache['projet_nom']): ?>
                        <div class="card-header py-1" style="background-color: <?= htmlspecialchars($tache['projet_couleur']) ?>; color: white;">
                            <small><i class="fas fa-folder"></i> <?= htmlspecialchars($tache['projet_nom']) ?></small>
                        </div>
                    <?php endif; ?>
                    
                    <div class="card-body">
                        <h5 class="card-title">
                            <?= htmlspecialchars($tache['titre']) ?>
                        </h5>
                        
                        <?php if ($tache['description']): ?>
                            <p class="card-text text-muted small">
                                <?= nl2br(htmlspecialchars(substr($tache['description'], 0, 100))) ?>
                                <?= strlen($tache['description']) > 100 ? '...' : '' ?>
                            </p>
                        <?php endif; ?>
                        
                        <div class="mb-2">
                            <span class="badge <?= $prioriteBadge[$tache['priorite']] ?>">
                                <?= ucfirst($tache['priorite']) ?>
                            </span>
                            <span class="badge <?= $statutBadge[$tache['statut']] ?>">
                                <?= $statutText[$tache['statut']] ?>
                            </span>
                        </div>
                        
                        <?php if ($tache['date_echeance']): ?>
                            <p class="card-text small">
                                <i class="fas fa-calendar"></i> 
                                <?= date('d/m/Y', strtotime($tache['date_echeance'])) ?>
                            </p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="card-footer bg-white">
                        <div class="btn-group w-100" role="group">
                            <button class="btn btn-sm btn-outline-primary edit-task" 
                                    data-id="<?= $tache['id'] ?>"
                                    data-titre="<?= htmlspecialchars($tache['titre']) ?>"
                                    data-description="<?= htmlspecialchars($tache['description']) ?>"
                                    data-projet="<?= $tache['projet_id'] ?>"
                                    data-priorite="<?= $tache['priorite'] ?>"
                                    data-statut="<?= $tache['statut'] ?>"
                                    data-date="<?= $tache['date_echeance'] ?>">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger delete-task" 
                                    data-id="<?= $tache['id'] ?>"
                                    data-titre="<?= htmlspecialchars($tache['titre']) ?>">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
</div>


<?php 
$page_scripts = ['js/tasks.js'];
require_once 'includes/modals/task_modals.php'; 
require_once 'includes/footer.php'; 
?>
