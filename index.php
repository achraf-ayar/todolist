<?php require_once 'includes/header.php'; ?>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div>
    </div>
    <div class="d-flex align-items-center gap-2">
        <button class="btn btn-outline-primary" id="quickSearchBtn" aria-label="Rechercher"><i class="fas fa-magnifying-glass"></i></button>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTaskModal">
            <i class="fas fa-plus"></i> Nouvelle tâche
        </button>
    </div>
</div>

<div class="card mb-4 border-0 shadow-sm">
    <div class="card-body">
        <form method="GET" id="filterForm" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label label-muted"><i class="fas fa-folder"></i> Projet</label>
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
                <label class="form-label label-muted"><i class="fas fa-exclamation-circle"></i> Priorité</label>
                <select name="priorite" class="form-select" id="filterPriorite">
                    <option value="">Toutes</option>
                    <option value="basse" <?= (isset($_GET['priorite']) && $_GET['priorite'] == 'basse') ? 'selected' : '' ?>>Basse</option>
                    <option value="normale" <?= (isset($_GET['priorite']) && $_GET['priorite'] == 'normale') ? 'selected' : '' ?>>Normale</option>
                    <option value="haute" <?= (isset($_GET['priorite']) && $_GET['priorite'] == 'haute') ? 'selected' : '' ?>>Haute</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label label-muted"><i class="fas fa-check-circle"></i> Statut</label>
                <select name="statut" class="form-select" id="filterStatut">
                    <option value="">Tous</option>
                    <option value="a_faire" <?= (isset($_GET['statut']) && $_GET['statut'] == 'a_faire') ? 'selected' : '' ?>>À faire</option>
                    <option value="en_cours" <?= (isset($_GET['en_cours']) && $_GET['statut'] == 'en_cours') ? 'selected' : '' ?>>En cours</option>
                    <option value="termine" <?= (isset($_GET['termine']) && $_GET['statut'] == 'termine') ? 'selected' : '' ?>>Terminé</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-filter"></i> Filtrer
                </button>
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

$tags = $pdo->query("SELECT * FROM etiquettes ORDER BY nom")->fetchAll();

$tacheEtiquettes = [];
if (!empty($taches)) {
    $ids = array_column($taches, 'id');
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmtTags = $pdo->prepare("
        SELECT te.tache_id, e.id as tag_id, e.nom, e.couleur
        FROM taches_etiquettes te
        JOIN etiquettes e ON te.etiquette_id = e.id
        WHERE te.tache_id IN ($placeholders)
        ORDER BY e.nom
    ");
    $stmtTags->execute($ids);
    foreach ($stmtTags->fetchAll() as $row) {
        $tacheEtiquettes[$row['tache_id']][] = $row;
    }
}
?>

<!-- Liste des tâches -->
<?php if (empty($taches)): ?>
    <div class="alert-empty">
        <div class="mb-2" style="font-size: 2rem;">
            <i class="fas fa-inbox"></i>
        </div>
        <p class="mb-2 fw-semibold">Aucune tâche pour le moment</p>
        <p class="text-muted-foreground mb-3">Créez votre première tâche ou importez vos actions prioritaires.</p>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTaskModal">Nouvelle tâche</button>
    </div>
<?php else: ?>
    <div class="task-grid">
        <?php foreach ($taches as $tache): ?>
            <?php
            $prioriteBadge = [
                'basse' => 'badge-priorite-basse',
                'normale' => 'badge-priorite-normale',
                'haute' => 'badge-priorite-haute'
            ];

            $statutIcon = [
                'a_faire' => 'fa-circle-dot',
                'en_cours' => 'fa-clock',
                'termine' => 'fa-check-circle'
            ];

            $statutText = [
                'a_faire' => 'À faire',
                'en_cours' => 'En cours',
                'termine' => 'Terminé'
            ];

            $projectColor = isset($tache['projet_couleur']) ? ltrim($tache['projet_couleur'], '#') : '';
            $projR = $projectColor ? hexdec(substr($projectColor, 0, 2)) : 51;
            $projG = $projectColor ? hexdec(substr($projectColor, 2, 2)) : 65;
            $projB = $projectColor ? hexdec(substr($projectColor, 4, 2)) : 85;
            ?>

            <article class="task-card">
                <div class="task-actions">
                    <button class="btn btn-sm btn-outline-secondary edit-task" 
                            data-id="<?= $tache['id'] ?>"
                            data-titre="<?= htmlspecialchars($tache['titre']) ?>"
                            data-description="<?= htmlspecialchars($tache['description']) ?>"
                            data-projet="<?= $tache['projet_id'] ?>"
                            data-priorite="<?= $tache['priorite'] ?>"
                            data-statut="<?= $tache['statut'] ?>"
                            data-date="<?= $tache['date_echeance'] ?>"
                            data-tags="<?= !empty($tacheEtiquettes[$tache['id']]) ? htmlspecialchars(implode(',', array_column($tacheEtiquettes[$tache['id']], 'tag_id'))) : '' ?>">
                        <i class="fas fa-pen"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger delete-task" 
                            data-id="<?= $tache['id'] ?>"
                            data-titre="<?= htmlspecialchars($tache['titre']) ?>">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>

                <?php if ($tache['projet_nom']): ?>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="tag-badge-muted" style="background: rgba(<?= $projR ?>, <?= $projG ?>, <?= $projB ?>, 0.15); color: <?= htmlspecialchars($tache['projet_couleur']) ?>;">
                            <i class="fas fa-folder"></i> <?= htmlspecialchars($tache['projet_nom']) ?>
                        </span>
                    </div>
                <?php endif; ?>

                <h5 class="mb-2"><?= htmlspecialchars($tache['titre']) ?></h5>

                <?php if ($tache['description']): ?>
                    <p class="text-subtle leading-relaxed mb-3">
                        <?= nl2br(htmlspecialchars(substr($tache['description'], 0, 140))) ?>
                        <?= strlen($tache['description']) > 140 ? '...' : '' ?>
                    </p>
                <?php endif; ?>

                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <span class="badge-soft <?= $prioriteBadge[$tache['priorite']] ?>">
                        <i class="fas fa-flag"></i> <?= ucfirst($tache['priorite']) ?>
                    </span>
                    <span class="badge-soft badge-status">
                        <i class="fas <?= $statutIcon[$tache['statut']] ?>"></i> <?= $statutText[$tache['statut']] ?>
                    </span>
                </div>

                <?php if (!empty($tacheEtiquettes[$tache['id']])): ?>
                    <div class="d-flex flex-wrap gap-2 mb-2">
                        <?php foreach ($tacheEtiquettes[$tache['id']] as $tag): ?>
                            <span class="tag-pill" style="border-color: <?= htmlspecialchars($tag['couleur']) ?>; color: <?= htmlspecialchars($tag['couleur']) ?>;">
                                <i class="fas fa-tag"></i> <?= htmlspecialchars($tag['nom']) ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if ($tache['date_echeance']): ?>
                    <div class="d-flex align-items-center gap-2 text-subtle">
                        <i class="fas fa-calendar"></i>
                        <span class="relative-date" data-date="<?= htmlspecialchars($tache['date_echeance']) ?>"><?= date('d/m/Y', strtotime($tache['date_echeance'])) ?></span>
                    </div>
                <?php endif; ?>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>


<?php 
$page_scripts = ['js/tasks.js'];
require_once 'includes/modals/task_modals.php'; 
require_once 'includes/footer.php'; 
?>
