document.getElementById("addTagForm").addEventListener("submit", function (e) {
  e.preventDefault();
  const formData = new FormData(this);

  fetch("ajax/tags.php?action=add", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        window.Jikko?.showToast("Tag créé", "Il apparait dans la liste.");
        setTimeout(() => location.reload(), 500);
      } else {
        window.Jikko?.showToast(
          "Erreur",
          data.message || "Impossible de créer le tag",
        );
      }
    })
    .catch(() => window.Jikko?.showToast("Erreur", "Veuillez réessayer."));
});

document.querySelectorAll(".edit-tag").forEach((btn) => {
  btn.addEventListener("click", function () {
    document.getElementById("edit_tag_id").value = this.dataset.id;
    document.getElementById("edit_tag_nom").value = this.dataset.nom;
    document.getElementById("edit_tag_couleur").value = this.dataset.couleur;
    new bootstrap.Modal(document.getElementById("editTagModal")).show();
  });
});

document.getElementById("editTagForm").addEventListener("submit", function (e) {
  e.preventDefault();
  const formData = new FormData(this);

  fetch("ajax/tags.php?action=edit", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        window.Jikko?.showToast(
          "Tag modifié",
          "Les modifications sont enregistrées.",
        );
        setTimeout(() => location.reload(), 500);
      } else {
        window.Jikko?.showToast(
          "Erreur",
          data.message || "Impossible de modifier le tag",
        );
      }
    })
    .catch(() => window.Jikko?.showToast("Erreur", "Veuillez réessayer."));
});

document.querySelectorAll(".delete-tag").forEach((btn) => {
  btn.addEventListener("click", function () {
    document.getElementById("delete_tag_id").value = this.dataset.id;
    document.getElementById("delete_tag_name").textContent = this.dataset.nom;
    new bootstrap.Modal(document.getElementById("deleteTagModal")).show();
  });
});

document
  .getElementById("confirmDeleteTag")
  ?.addEventListener("click", function () {
    const tagId = document.getElementById("delete_tag_id").value;

    fetch("ajax/tags.php?action=delete", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ id: tagId }),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          window.Jikko?.showToast("Tag supprimé", "Il a été retiré.");
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
