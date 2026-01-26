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
        location.reload();
      } else {
        alert("Erreur : " + data.message);
      }
    });
});

// Remplissage du modal d'edition
// NOTE: Les attributs data-* sur les boutons fournissent le contenu existant
// pour pre-remplir les champs du formulaire.
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
        location.reload();
      } else {
        alert("Erreur : " + data.message);
      }
    });
});

document.querySelectorAll(".delete-tag").forEach((btn) => {
  btn.addEventListener("click", function () {
    if (confirm("Supprimer cette étiquette ?")) {
      fetch("ajax/tags.php?action=delete", {
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
