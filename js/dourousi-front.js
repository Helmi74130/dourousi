document.addEventListener('DOMContentLoaded', function () {
    const playerElement = document.getElementById('main-audio-player');
    const chapterLinks = document.querySelectorAll('.chapter-link');
    const checkboxes = document.querySelectorAll('.chapter-done');
    const progressFill = document.querySelector('.progress-fill');
    const progressText = document.querySelector('.progress-text');

    // Récupère l'ID du CPT à partir de l'attribut data-post-id du lecteur
    const postId = playerElement.dataset.postId;
    // Si l'ID n'est pas défini, on sort pour éviter les bugs
    if (!postId) return; 

    const player = new Plyr(playerElement, {
        controls: ['play', 'progress', 'current-time', 'duration',
                   'mute', 'volume', 'settings', 'download'],
        settings: ['speed'],
        speed: { selected: 1, options: [0.5, 0.75, 1, 1.25, 1.5, 2] }
    });

    let currentChapterId = null;

    function updateProgress() {
        const total = checkboxes.length;
        let done = 0;
        checkboxes.forEach(cb => {
            if (cb.checked) done++;
        });
        const percent = total > 0 ? (done / total) * 100 : 0;
        progressFill.style.width = percent + "%";
        progressText.textContent = done + " / " + total + " cours terminés";
    }

    // --- Restaurer état ---
    checkboxes.forEach(cb => {
        const chapId = cb.dataset.id;
        // Utilisez l'ID du post dans la clé de localStorage pour la rendre unique
        const key = "chapter_done_" + postId + "_" + chapId; 
        const saved = localStorage.getItem(key);
        if (saved === "true") {
            cb.checked = true;
            cb.closest("li").classList.add("completed");
        }

        cb.addEventListener("change", function () {
            localStorage.setItem(key, this.checked);
            if (this.checked) {
                cb.closest("li").classList.add("completed");
            } else {
                cb.closest("li").classList.remove("completed");
            }
            updateProgress();
        });
    });

    // --- Clic chapitre ---
    const titleDisplay = document.getElementById('current-title-display');

    chapterLinks.forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            const newSrc = this.getAttribute('data-audio');
            const chapId = this.getAttribute('data-id');
            const chapTitle = this.textContent.trim(); // Récupère le texte du lien, c'est le titre

            currentChapterId = chapId;

            player.source = {
                type: 'audio',
                sources: [{ src: newSrc, type: 'audio/mp3' }]
            };
            player.play();
            
            // Met à jour le titre affiché à côté du lecteur
            if (titleDisplay) {
                titleDisplay.textContent = chapTitle;
            }

            chapterLinks.forEach(l => l.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // --- Quand un audio se termine ---
    player.on('ended', function () {
        if (currentChapterId !== null) {
            const cb = document.querySelector('.chapter-done[data-id="' + currentChapterId + '"]');
            if (cb && !cb.checked) {
                // Utilisez l'ID du post dans la clé de localStorage
                const key = "chapter_done_" + postId + "_" + currentChapterId; 
                cb.checked = true;
                localStorage.setItem(key, true);
                cb.closest("li").classList.add("completed");
                updateProgress();
            }
        }
    });

    updateProgress();
});







