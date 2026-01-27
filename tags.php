<?php require_once 'includes/header.php'; ?>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div>

    </div>
    <div class="d-flex align-items-center gap-2">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTagModal">
            <i class="fas fa-plus"></i> Nouveau tag
        </button>
    </div>
</div>

<?php
$stmt = $pdo->query("
    SELECT e.*, COUNT(te.tache_id) as nb_taches
    FROM etiquettes e
    LEFT JOIN taches_etiquettes te ON e.id = te.etiquette_id
    GROUP BY e.id
    ORDER BY e.nom ASC
");
$etiquettes = $stmt->fetchAll();
?>

<?php if (empty($etiquettes)): ?>
    <div class="alert-empty">
        <div class="mb-2" style="font-size: 2rem;">
            <i class="fas fa-tags"></i>
        </div>
        <p class="mb-2 fw-semibold">Aucun tag pour le moment</p>
        <p class="text-muted-foreground mb-3">Ajoutez des tags pour catégoriser vos tâches.</p>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTagModal">Nouveau tag</button>
    </div>
<?php else: ?>
    <div class="row">
        <?php foreach ($etiquettes as $tag): ?>
            <div class="col-md-4 col-lg-3 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header" style="background-color: <?= htmlspecialchars($tag['couleur']) ?>; color: white;">
                        <h5 class="mb-0">
                            <i class="fas fa-tag"></i> <?= htmlspecialchars($tag['nom']) ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-1 text-muted">Taches liées : <?= $tag['nb_taches'] ?></p>
                    </div>
                    <div class="card-footer bg-white d-flex justify-content-between">
                        <button class="btn btn-sm btn-outline-warning edit-tag"
                                data-id="<?= $tag['id'] ?>"
                                data-nom="<?= htmlspecialchars($tag['nom']) ?>"
                                data-couleur="<?= htmlspecialchars($tag['couleur']) ?>">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger delete-tag"
                                data-id="<?= $tag['id'] ?>"
                                data-nom="<?= htmlspecialchars($tag['nom']) ?>">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php 
$page_scripts = ['js/tags.js'];
require_once 'includes/modals/tag_modals.php';
require_once 'includes/footer.php';
?>
