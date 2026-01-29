function setMultiSelectValues(selectEl, values) {
  const valueSet = new Set(values);
  Array.from(selectEl.options).forEach((opt) => {
    opt.selected = valueSet.has(opt.value);
  });
}

function setCheckboxValues(listEl, values) {
  const valueSet = new Set(values);
  listEl.querySelectorAll(".tag-checkbox").forEach((cb) => {
    cb.checked = valueSet.has(cb.value);
  });
}

function syncSelectFromChecks(listEl, selectEl) {
  const selected = [];
  listEl.querySelectorAll(".tag-checkbox").forEach((cb) => {
    if (cb.checked) selected.push(cb.value);
  });
  setMultiSelectValues(selectEl, selected);
}

function renderTagList(listEl, tags, selectedIds, query) {
  listEl.innerHTML = "";
  const filtered = tags.filter((t) =>
    t.nom.toLowerCase().includes(query.toLowerCase()),
  );

  if (filtered.length === 0) {
    const empty = document.createElement("small");
    empty.className = "text-muted";
    empty.textContent = query
      ? "Aucun résultat."
      : "Tapez pour rechercher une étiquette.";
    listEl.appendChild(empty);
    return;
  }

  filtered.forEach((tag) => {
    const wrapper = document.createElement("div");
    wrapper.className = "form-check mb-1";

    const input = document.createElement("input");
    input.type = "checkbox";
    input.className = "form-check-input tag-checkbox";
    input.id = `${listEl.id}_${tag.id}`;
    input.value = String(tag.id);
    input.checked = selectedIds.has(String(tag.id));

    const label = document.createElement("label");
    label.className = "form-check-label";
    label.htmlFor = input.id;
    label.style.color = tag.couleur;
    label.textContent = `#${tag.nom}`;

    wrapper.appendChild(input);
    wrapper.appendChild(label);
    listEl.appendChild(wrapper);
  });
}

function wireTagPicker({ searchInputId, listId, selectId }) {
  const searchInput = document.getElementById(searchInputId);
  const listEl = document.getElementById(listId);
  const selectEl = document.getElementById(selectId);
  if (!searchInput || !listEl || !selectEl) return;

  const tags = JSON.parse(listEl.dataset.tags || "[]");

  const getSelectedSet = () =>
    new Set(Array.from(selectEl.selectedOptions).map((o) => o.value));

  // Initial placeholder only (no options shown until search)
  listEl.innerHTML = "";
  const placeholder = document.createElement("small");
  placeholder.className = "text-muted";
  placeholder.textContent = "Tapez pour rechercher une étiquette.";
  listEl.appendChild(placeholder);

  searchInput.addEventListener("input", function () {
    const q = this.value.trim();
    renderTagList(listEl, tags, getSelectedSet(), q);
    listEl.querySelectorAll(".tag-checkbox").forEach((cb) => {
      cb.addEventListener("change", () =>
        syncSelectFromChecks(listEl, selectEl),
      );
    });
  });

  // When list renders (initially or after preselect), bind change handlers
  listEl.querySelectorAll(".tag-checkbox").forEach((cb) => {
    cb.addEventListener("change", () => syncSelectFromChecks(listEl, selectEl));
  });

  // Expose a helper to refresh list with current selections (used on edit open)
  return {
    refreshWithSelection(selectedIds) {
      const selectedSet = new Set(selectedIds);
      setMultiSelectValues(selectEl, selectedIds);

      // If no search query, render only selected tags; otherwise render filtered
      const q = searchInput.value.trim();
      const source = q
        ? tags
        : tags.filter((t) => selectedSet.has(String(t.id)));
      renderTagList(listEl, source, selectedSet, q);
      listEl.querySelectorAll(".tag-checkbox").forEach((cb) => {
        cb.addEventListener("change", () =>
          syncSelectFromChecks(listEl, selectEl),
        );
      });
    },
  };
}

const addPicker = wireTagPicker({
  searchInputId: "add_tag_search",
  listId: "add_tag_list",
  selectId: "tags",
});
const editPicker = wireTagPicker({
  searchInputId: "edit_tag_search",
  listId: "edit_tag_list",
  selectId: "edit_tags",
});

// Gestion du formulaire d'ajout de tâche
document.getElementById("addTaskForm").addEventListener("submit", function (e) {
  e.preventDefault();
  const formData = new FormData(this);

  fetch("ajax/tasks.php?action=add", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        window.Jikko?.showToast(
          "Tâche ajoutée",
          "Elle apparaît dans la liste.",
        );
        setTimeout(() => location.reload(), 500);
      } else {
        window.Jikko?.showToast(
          "Erreur",
          data.message || "Impossible d'ajouter la tâche",
        );
      }
    })
    .catch(() => window.Jikko?.showToast("Erreur", "Veuillez réessayer."));
});

// Gestion des boutons d'édition
document.querySelectorAll(".edit-task").forEach((btn) => {
  btn.addEventListener("click", function () {
    document.getElementById("edit_id").value = this.dataset.id;
    document.getElementById("edit_titre").value = this.dataset.titre;
    document.getElementById("edit_description").value =
      this.dataset.description;
    document.getElementById("edit_projet_id").value = this.dataset.projet || "";
    document.getElementById("edit_priorite").value = this.dataset.priorite;
    document.getElementById("edit_statut").value = this.dataset.statut;
    document.getElementById("edit_date_echeance").value =
      this.dataset.date || "";
    const tagSelect = document.getElementById("edit_tags");
    if (tagSelect) {
      const tags = (this.dataset.tags || "").split(",").filter((t) => t !== "");
      setMultiSelectValues(tagSelect, tags);
      if (editPicker) {
        editPicker.refreshWithSelection(tags);
      }
    }
    new bootstrap.Modal(document.getElementById("editTaskModal")).show();
  });
});

// Gestion du formulaire de modification de tâche
document
  .getElementById("editTaskForm")
  .addEventListener("submit", function (e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch("ajax/tasks.php?action=edit", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          window.Jikko?.showToast(
            "Tâche mise à jour",
            "Modifications enregistrées.",
          );
          setTimeout(() => location.reload(), 500);
        } else {
          window.Jikko?.showToast(
            "Erreur",
            data.message || "Impossible de modifier la tâche",
          );
        }
      })
      .catch(() => window.Jikko?.showToast("Erreur", "Veuillez réessayer."));
  });

// Gestion des boutons de suppression
let taskToDelete = null;
document.querySelectorAll(".delete-task").forEach((btn) => {
  btn.addEventListener("click", function () {
    taskToDelete = this.dataset.id;
    const taskTitle =
      this.closest(".card, .list-group-item, tr")?.querySelector(
        ".task-title, .fw-bold",
      )?.textContent || "cette tâche";
    document.getElementById("deleteTaskTitle").textContent = taskTitle;
    const deleteModal = new bootstrap.Modal(
      document.getElementById("deleteTaskModal"),
    );
    deleteModal.show();
  });
});

// Confirmer la suppression
document
  .getElementById("confirmDeleteTask")
  ?.addEventListener("click", function () {
    if (!taskToDelete) return;

    fetch("ajax/tasks.php?action=delete", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ id: taskToDelete }),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          window.Jikko?.showToast("Tâche supprimée", "Elle a été retirée.");
          bootstrap.Modal.getInstance(
            document.getElementById("deleteTaskModal"),
          ).hide();
          setTimeout(() => location.reload(), 450);
        } else {
          window.Jikko?.showToast(
            "Erreur",
            data.message || "Suppression impossible",
          );
        }
      })
      .catch(() => window.Jikko?.showToast("Erreur", "Veuillez réessayer."));
  });

// Gestion des filtres
document.querySelectorAll("#filterForm select").forEach((select) => {
  select.addEventListener("change", function () {
    document.getElementById("filterForm").submit();
  });
});
