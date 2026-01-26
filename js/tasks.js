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
        location.reload();
      } else {
        alert("Erreur : " + data.message);
      }
    });
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
          location.reload();
        } else {
          alert("Erreur : " + data.message);
        }
      });
  });

// Gestion des boutons de suppression
document.querySelectorAll(".delete-task").forEach((btn) => {
  btn.addEventListener("click", function () {
    if (confirm("Êtes-vous sûr de vouloir supprimer cette tâche ?")) {
      fetch("ajax/tasks.php?action=delete", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id: this.dataset.id }),
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            location.reload();
          } else {
            alert("Erreur : " + data.message);
          }
        });
    }
  });
});

// Gestion des filtres
document.querySelectorAll("#filterForm select").forEach((select) => {
  select.addEventListener("change", function () {
    document.getElementById("filterForm").submit();
  });
});
