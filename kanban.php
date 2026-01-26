<?php require_once 'includes/header.php'; ?>
<div class="row mb-4">
    <div class="col-12">
        <h2><i class="fas fa-columns"></i> Kanban Board</h2>
    </div>
</div>

<?php

$statuts = ['a_faire' => 'À faire', 'en_cours' => 'En cours', 'termine' => 'Terminé'];
$tachesByStatus = [];

foreach (array_keys($statuts) as $statut) {
    $stmt = $pdo->prepare("
        SELECT t.*, p.nom as projet_nom, p.couleur as projet_couleur
        FROM taches t
        LEFT JOIN projets p ON t.projet_id = p.id
        WHERE t.statut = ?
        ORDER BY t.ordre ASC, t.date_creation ASC
    ");
    $stmt->execute([$statut]);
    $tachesByStatus[$statut] = $stmt->fetchAll();
}


$prioriteBadge = [
    'basse' => 'bg-success',
    'normale' => 'bg-primary',
    'haute' => 'bg-danger'
];
?>

<div class="row g-0">
    <?php foreach ($statuts as $statut => $label): ?>
        <div class="col-lg-4">
            <div class="card" style="border-radius: 0; border-left: 1px solid #dee2e6; border-right: 0;">
                <div class="card-header bg-<?= $statut === 'a_faire' ? 'secondary' : ($statut === 'en_cours' ? 'warning' : 'success') ?>">
                    <h5 class="mb-0 text-white">
                        <?= $label ?>
                        <span class="badge bg-light text-dark ms-2"><?= count($tachesByStatus[$statut]) ?></span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="kanban-column" data-status="<?= $statut ?>" style="min-height: 600px; max-height: 600px; overflow-y: auto;">
                        <?php foreach ($tachesByStatus[$statut] as $tache): ?>
                            <div class="kanban-card card mb-1 ms-1 me-1 mt-1" data-id="<?= $tache['id'] ?>" draggable="true" style="cursor: move;">
                                <?php if ($tache['projet_nom']): ?>
                                    <div class="card-header py-0 px-2" style="background-color: <?= htmlspecialchars($tache['projet_couleur']) ?>; color: white; font-size: 0.75rem;">
                                        <small><i class="fas fa-folder"></i> <?= htmlspecialchars($tache['projet_nom']) ?></small>
                                    </div>
                                <?php endif; ?>
                                <div class="card-body py-1 px-2">
                                    <h6 class="card-title mb-1" style="font-size: 0.9rem;"><?= htmlspecialchars($tache['titre']) ?></h6>
                                    <?php if ($tache['description']): ?>
                                        <p class="card-text text-muted small mb-1" style="font-size: 0.75rem;">
                                            <?= htmlspecialchars(substr($tache['description'], 0, 60)) ?>
                                            <?= strlen($tache['description']) > 60 ? '...' : '' ?>
                                        </p>
                                    <?php endif; ?>
                                    <div class="d-flex gap-1 flex-wrap mb-1">
                                        <span class="badge <?= $prioriteBadge[$tache['priorite']] ?>" style="font-size: 0.65rem;">
                                            <?= ucfirst($tache['priorite']) ?>
                                        </span>
                                    </div>
                                    <?php if ($tache['date_echeance']): ?>
                                        <small class="text-muted d-block" style="font-size: 0.7rem;">
                                            <i class="fas fa-calendar"></i> 
                                            <?= date('d/m/Y', strtotime($tache['date_echeance'])) ?>
                                        </small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>

document.querySelectorAll('.kanban-column').forEach(column => {
    Sortable.create(column, {
        group: 'tasks',
        animation: 150,
        ghostClass: 'opacity-50',
        onEnd: function(evt) {
            const tacheId = evt.item.dataset.id;
            const newStatut = evt.to.dataset.status;
            const newOrdre = Array.from(evt.to.children).indexOf(evt.item);
            
           
            fetch('ajax/tasks.php?action=updateOrder', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    updates: [{ id: tacheId, statut: newStatut, ordre: newOrdre }]
                })
            })
            .then(r => r.json())
            .then(data => {
                if (!data.success) {
                    alert('Erreur : ' + data.message);
                    location.reload();
                }
            });
        }
    });
});
</script>

<?php
$page_scripts = [];
?>


<?php require_once 'includes/footer.php'; ?>
