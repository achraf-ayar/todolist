<?php require_once 'includes/header.php'; ?>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div>
    </div>
    <div class="d-flex align-items-center gap-2">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProjectModal">
            <i class="fas fa-plus"></i> Nouveau projet
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

<?php if (empty($projets)): ?>
    <div class="alert-empty">
        <div class="mb-2" style="font-size: 2rem;">
            <i class="fas fa-folder-open"></i>
        </div>
        <p class="mb-2 fw-semibold">Aucun projet pour le moment</p>
        <p class="text-muted-foreground mb-3">Créez votre premier projet pour organiser vos tâches.</p>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProjectModal">Nouveau projet</button>
    </div>
<?php else: ?>
    <div class="row">
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
                            <p class="mb-1 project-stat">
                                <strong>Taches totales :</strong> <?= $projet['nb_taches'] ?>
                            </p>
                            <p class="mb-1 project-stat">
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
                    <div class="card-footer">
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
    </div>
<?php endif; ?>

<?php require_once 'includes/modals/project_modals.php'; ?>

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
            window.Jikko?.showToast('Projet créé', 'Il apparait dans la liste.');
            setTimeout(() => location.reload(), 500);
        } else {
            window.Jikko?.showToast('Erreur', data.message || 'Impossible de créer le projet');
        }
    })
    .catch(() => window.Jikko?.showToast('Erreur', 'Veuillez réessayer.'));
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
            window.Jikko?.showToast('Projet modifié', 'Les modifications sont enregistrées.');
            setTimeout(() => location.reload(), 500);
        } else {
            window.Jikko?.showToast('Erreur', data.message || 'Impossible de modifier le projet');
        }
    })
    .catch(() => window.Jikko?.showToast('Erreur', 'Veuillez réessayer.'));
});

document.querySelectorAll('.delete-project').forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('delete_project_id').value = this.dataset.id;
        document.getElementById('delete_project_name').textContent = this.dataset.nom;
        new bootstrap.Modal(document.getElementById('deleteProjectModal')).show();
    });
});

document.getElementById('confirmDeleteProject')?.addEventListener('click', function() {
    const projectId = document.getElementById('delete_project_id').value;
    
    fetch('ajax/projects.php?action=delete', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({id: projectId})
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.Jikko?.showToast('Projet supprimé', 'Il a été retiré.');
            setTimeout(() => location.reload(), 500);
        } else {
            window.Jikko?.showToast('Erreur', data.message || 'Suppression impossible');
        }
    })
    .catch(() => window.Jikko?.showToast('Erreur', 'Veuillez réessayer.'));
});
</script>

<?php require_once 'includes/footer.php'; ?>
