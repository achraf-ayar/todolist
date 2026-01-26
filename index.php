<?php require_once 'includes/header.php'; ?>

<div class="row mb-4">
    <div class="col-md-6">
        <h2><i class="fas fa-tasks"></i> Mes Tâches</h2>
    </div>
    <div class="col-md-6 text-end">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTaskModal">
            <i class="fas fa-plus"></i> Nouvelle Tâche
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
                <div class="card h-100 shadow-sm <?= $enRetard ? 'border-danger' : '' ?>">
                    <?php if ($tache['projet_nom']): ?>
                        <div class="card-header py-1" style="background-color: <?= htmlspecialchars($tache['projet_couleur']) ?>; color: white;">
                            <small><i class="fas fa-folder"></i> <?= htmlspecialchars($tache['projet_nom']) ?></small>
                        </div>
                    <?php endif; ?>
                    
                    <div class="card-body">
                        <h5 class="card-title">
                            <?= htmlspecialchars($tache['titre']) ?>
                            <?php if ($enRetard): ?>
                                <i class="fas fa-exclamation-triangle text-danger" title="En retard"></i>
                            <?php endif; ?>
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


<div class="modal fade" id="addTaskModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus"></i> Nouvelle Tâche</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addTaskForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="titre" class="form-label">Titre *</label>
                        <input type="text" class="form-control" id="titre" name="titre" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="projet_id" class="form-label">Projet</label>
                        <select class="form-select" id="projet_id" name="projet_id">
                            <option value="">Aucun projet</option>
                            <?php foreach ($projets as $p): ?>
                                <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nom']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="priorite" class="form-label">Priorité</label>
                            <select class="form-select" id="priorite" name="priorite">
                                <option value="basse">Basse</option>
                                <option value="normale" selected>Normale</option>
                                <option value="haute">Haute</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="statut" class="form-label">Statut</label>
                            <select class="form-select" id="statut" name="statut">
                                <option value="a_faire" selected>À faire</option>
                                <option value="en_cours">En cours</option>
                                <option value="termine">Terminé</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="date_echeance" class="form-label">Date d'échéance</label>
                        <input type="date" class="form-control" id="date_echeance" name="date_echeance">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Créer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="editTaskModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit"></i> Modifier Tâche</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editTaskForm">
                <input type="hidden" id="edit_id" name="id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_titre" class="form-label">Titre *</label>
                        <input type="text" class="form-control" id="edit_titre" name="titre" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Description</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit_projet_id" class="form-label">Projet</label>
                        <select class="form-select" id="edit_projet_id" name="projet_id">
                            <option value="">Aucun projet</option>
                            <?php foreach ($projets as $p): ?>
                                <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nom']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_priorite" class="form-label">Priorité</label>
                            <select class="form-select" id="edit_priorite" name="priorite">
                                <option value="basse">Basse</option>
                                <option value="normale">Normale</option>
                                <option value="haute">Haute</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_statut" class="form-label">Statut</label>
                            <select class="form-select" id="edit_statut" name="statut">
                                <option value="a_faire">À faire</option>
                                <option value="en_cours">En cours</option>
                                <option value="termine">Terminé</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_date_echeance" class="form-label">Date d'échéance</label>
                        <input type="date" class="form-control" id="edit_date_echeance" name="date_echeance">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save"></i> Modifier
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>

document.getElementById('addTaskForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch('ajax/tasks.php?action=add', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Erreur : ' + data.message);
        }
    });
});


document.querySelectorAll('.edit-task').forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('edit_id').value = this.dataset.id;
        document.getElementById('edit_titre').value = this.dataset.titre;
        document.getElementById('edit_description').value = this.dataset.description;
        document.getElementById('edit_projet_id').value = this.dataset.projet || '';
        document.getElementById('edit_priorite').value = this.dataset.priorite;
        document.getElementById('edit_statut').value = this.dataset.statut;
        document.getElementById('edit_date_echeance').value = this.dataset.date || '';
        new bootstrap.Modal(document.getElementById('editTaskModal')).show();
    });
});

document.getElementById('editTaskForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch('ajax/tasks.php?action=edit', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Erreur : ' + data.message);
        }
    });
});


document.querySelectorAll('.delete-task').forEach(btn => {
    btn.addEventListener('click', function() {
        fetch('ajax/tasks.php?action=delete', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({id: this.dataset.id})
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur : ' + data.message);
            }
        });
    });
});


document.querySelectorAll('#filterForm select').forEach(select => {
    select.addEventListener('change', function() {
        document.getElementById('filterForm').submit();
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
