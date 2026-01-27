<!-- Modal: Ajouter une nouvelle étiquette -->
<div class="modal fade" id="addTagModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-tag"></i> Nouvelle étiquette</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addTagForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="tag_nom" class="form-label">Nom *</label>
                        <input type="text" class="form-control" id="tag_nom" name="nom" required>
                    </div>
                    <div class="mb-3">
                        <label for="tag_couleur" class="form-label">Couleur</label>
                        <input type="color" class="form-control form-control-color" id="tag_couleur" name="couleur" value="#6c757d">
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

<!-- Modal: Modifier une étiquette -->
<div class="modal fade" id="editTagModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-pen"></i> Modifier l'étiquette</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editTagForm">
                <input type="hidden" id="edit_tag_id" name="id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_tag_nom" class="form-label">Nom *</label>
                        <input type="text" class="form-control" id="edit_tag_nom" name="nom" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_tag_couleur" class="form-label">Couleur</label>
                        <input type="color" class="form-control form-control-color" id="edit_tag_couleur" name="couleur">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save"></i> Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Confirmer suppression tag -->
<div class="modal fade" id="deleteTagModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle"></i> Supprimer le tag</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Voulez-vous vraiment supprimer le tag <strong id="delete_tag_name"></strong> ?</p>
                <p class="text-muted"><i class="fas fa-info-circle"></i> Ce tag sera retiré de toutes les tâches associées.</p>
                <input type="hidden" id="delete_tag_id">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteTag">
                    <i class="fas fa-trash"></i> Supprimer
                </button>
            </div>
        </div>
    </div>
</div>
