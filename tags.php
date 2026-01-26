<?php require_once 'includes/header.php'; ?>

<div class="row mb-4">
    <div class="col-md-8">
        <h2><i class="fas fa-tags"></i> Mes Étiquettes</h2>
    </div>
    <div class="col-md-4 text-end">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTagModal">
            <i class="fas fa-plus"></i> Nouvelle étiquette
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

<div class="row">
    <?php if (empty($etiquettes)): ?>
        <div class="col-12">
            <div class="alert alert-info">
                Aucune étiquette pour le moment. Ajoutez votre première étiquette !
            </div>
        </div>
    <?php else: ?>
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
    <?php endif; ?>
</div>

<?php 
$page_scripts = ['js/tags.js'];
require_once 'includes/modals/tag_modals.php';
require_once 'includes/footer.php';
?>
