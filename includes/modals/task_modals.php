<!-- Modal: Ajouter une nouvelle tâche -->
<div class="modal fade" id="addTaskModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus"></i> Nouvelle Tache</h5>
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
                        <label for="tags" class="form-label">Étiquettes</label>
                        <select class="form-select" id="tags" name="tags[]" multiple>
                            <?php foreach ($tags as $tag): ?>
                                <option value="<?= $tag['id'] ?>">#<?= htmlspecialchars($tag['nom']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted">Ctrl/Cmd + clic pour sélectionner plusieurs étiquettes.</small>
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

<!-- Modal: Modifier une tâche -->
<div class="modal fade" id="editTaskModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit"></i> Modifier Tache</h5>
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
                        <label for="edit_tags" class="form-label">Étiquettes</label>
                        <select class="form-select" id="edit_tags" name="tags[]" multiple>
                            <?php foreach ($tags as $tag): ?>
                                <option value="<?= $tag['id'] ?>">#<?= htmlspecialchars($tag['nom']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted">Ctrl/Cmd + clic pour sélectionner plusieurs étiquettes.</small>
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
