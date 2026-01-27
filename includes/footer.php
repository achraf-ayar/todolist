
        </main>
        <footer class="app-footer">
            <div class="container-xl d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <img src="images/jikko-logo.png" alt="Jikko Logo" class="footer-logo" />
                    <span class="text-muted-foreground small">· 2026</span>
                </div>
                <div class="d-flex align-items-center gap-3 small text-muted-foreground">
                    <span>⌘K palette</span>
                    
                </div>
            </div>
        </footer>
    </div>

    <div class="toast-stack" aria-live="polite" aria-atomic="true"></div>

    <div class="command-palette" id="commandPalette" role="dialog" aria-modal="true" aria-label="Palette de commandes" hidden>
        <div class="command-palette__panel">
            <div class="command-palette__input">
                <i class="fas fa-magnifying-glass"></i>
                <input type="text" id="commandPaletteInput" placeholder="Rechercher ou naviguer..." autocomplete="off" />
                <button class="icon-btn" id="commandPaletteClose" aria-label="Fermer">
                    <i class="fas fa-xmark"></i>
                </button>
            </div>
            <div class="command-palette__list" id="commandPaletteList" role="listbox"></div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script src="js/app.js"></script>
    
    <?php if (isset($page_scripts)): ?>
        <?php foreach ($page_scripts as $script): ?>
            <script src="<?= $script ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>

