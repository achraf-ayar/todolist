<?php require_once __DIR__ . '/../config/init.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionnaire de Taches</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="app-body">
    <div class="app-shell">
        <header class="app-topbar container-fluid px-4 py-3">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <a class="brand" href="index.php" aria-label="Accueil Todo List">
                        <img src="images/jikko-logo.png" alt="Jikko Logo" class="brand-logo" />
                        
                    </a>
                </div>
                <div class="d-flex align-items-center gap-2 topbar-nav">
                    <a class="topbar-link" href="index.php"><i class="fas fa-list"></i><span>Mes tâches</span></a>
                    <a class="topbar-link" href="projects.php"><i class="fas fa-folder"></i><span>Projets</span></a>
                    <a class="topbar-link" href="kanban.php"><i class="fas fa-columns"></i><span>Kanban</span></a>
                    <a class="topbar-link" href="stats.php"><i class="fas fa-chart-line"></i><span>Stats</span></a>
                    <a class="topbar-link" href="tags.php"><i class="fas fa-hashtag"></i><span>Tags</span></a>
                    <button class="icon-btn" id="commandPaletteBtn" aria-label="Ouvrir la palette de commandes (⌘K)">
                        <i class="fas fa-magnifying-glass"></i><span class="shortcut">⌘K</span>
                    </button>
                    <button class="icon-btn" id="darkModeToggle" aria-label="Basculer le thème">
                        <i class="fas fa-moon"></i>
                    </button>
                </div>
            </div>
        </header>
        <main class="app-main container-xl py-4">
