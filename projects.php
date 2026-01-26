<?php require_once 'includes/header.php'; ?>

<div class="row mb-4">
    <div class="col-md-8">
        <h2><i class="fas fa-folder"></i> Mes Projets</h2>
    </div>
    <div class="col-md-4 text-end">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProjectModal">
            <i class="fas fa-plus"></i> Nouveau Projet
        </button>
    </div>
</div>

<?php
$stmt = $pdo->query("
    SELECT 
        p.*, 
        COUNT(t.id) as nb_taches,
        SUM(CASE WHEN t.statut = 'termine' THEN 1 ELSE 0 END) as nb_terminees
    FROM projets p
    LEFT JOIN taches t ON p.id = t.projet_id
    GROUP BY p.id
    ORDER BY p.nom ASC
");
$projets = $stmt->fetchAll();
?>

<div class="row">
    <?php if (empty($projets)): ?>
        <div class="col-12">
            <div class="alert alert-info">
                Aucun projet pour le moment. Créez votre premier projet !
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($projets as $projet): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header" style="background-color: <?= htmlspecialchars($projet['couleur']) ?>; color: white;">
                        <h5 class="mb-0">
                            <i class="fas fa-folder"></i> <?= htmlspecialchars($projet['nom']) ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <p class="mb-1">
                                <strong>Taches totales :</strong> <?= $projet['nb_taches'] ?>
                            </p>
                            <p class="mb-1">
                                <strong>Terminées :</strong> <?= $projet['nb_terminees'] ?>
                            </p>
                            <?php if ($projet['nb_taches'] > 0): ?>
                                <div class="progress" style="height: 20px;">
                                    <?php $pourcentage = round(($projet['nb_terminees'] / $projet['nb_taches']) * 100); ?>
                                    <div class="progress-bar" role="progressbar" 
                                         style="width: <?= $pourcentage ?>%; background-color: <?= htmlspecialchars($projet['couleur']) ?>;"
                                         aria-valuenow="<?= $pourcentage ?>" aria-valuemin="0" aria-valuemax="100">
                                        <?= $pourcentage ?>%
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-footer bg-white">
                        <a href="index.php?projet=<?= $projet['id'] ?>" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-eye"></i> Voir les tâches
                        </a>
                        <button class="btn btn-sm btn-outline-warning edit-project" 
                                data-id="<?= $projet['id'] ?>"
                                data-nom="<?= htmlspecialchars($projet['nom']) ?>"
                                data-couleur="<?= htmlspecialchars($projet['couleur']) ?>">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger delete-project" 
                                data-id="<?= $projet['id'] ?>"
                                data-nom="<?= htmlspecialchars($projet['nom']) ?>">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>


<div class="modal fade" id="addProjectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-folder-plus"></i> Nouveau Projet</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addProjectForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom du projet *</label>
                        <input type="text" class="form-control" id="nom" name="nom" required>
                    </div>
                    <div class="mb-3">
                        <label for="couleur" class="form-label">Couleur</label>
                        <input type="color" class="form-control form-control-color" id="couleur" name="couleur" value="#007bff">
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


<div class="modal fade" id="editProjectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit"></i> Modifier Projet</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editProjectForm">
                <input type="hidden" id="edit_id" name="id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_nom" class="form-label">Nom du projet *</label>
                        <input type="text" class="form-control" id="edit_nom" name="nom" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_couleur" class="form-label">Couleur</label>
                        <input type="color" class="form-control form-control-color" id="edit_couleur" name="couleur">
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

document.getElementById('addProjectForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch('ajax/projects.php?action=add', {
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


document.querySelectorAll('.edit-project').forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('edit_id').value = this.dataset.id;
        document.getElementById('edit_nom').value = this.dataset.nom;
        document.getElementById('edit_couleur').value = this.dataset.couleur;
        new bootstrap.Modal(document.getElementById('editProjectModal')).show();
    });
});

document.getElementById('editProjectForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch('ajax/projects.php?action=edit', {
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


document.querySelectorAll('.delete-project').forEach(btn => {
    btn.addEventListener('click', function() {
        if (confirm('Voulez-vous vraiment supprimer le projet "' + this.dataset.nom + '" ?\nToutes les tâches associées seront également supprimées.')) {
            fetch('ajax/projects.php?action=delete', {
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
        }
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
