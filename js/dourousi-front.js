document.addEventListener('DOMContentLoaded', function () {
    const playerElement = document.getElementById('main-audio-player');
    const checkboxes = document.querySelectorAll('.chapter-done');
    const progressFill = document.querySelector('.progress-fill');
    const progressText = document.querySelector('.progress-text');

    const postId = playerElement.dataset.postId;
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

    // --- Restaurer état des checkboxes ---
    checkboxes.forEach(cb => {
        const chapId = cb.dataset.id;
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

    // --- Gestion du clic sur toute la ligne du chapitre ---
    const chapters = document.querySelectorAll(".dourousi-chapter");
    const titleDisplay = document.getElementById('current-title-display');

    chapters.forEach(chapter => {
        chapter.addEventListener("click", function (e) {
            // Si on clique sur la checkbox ou le label, ne pas lancer l'audio
            if (e.target.closest('input') || e.target.closest('label')) return;

            const audioUrl = this.dataset.audio;
            const chapId = this.dataset.id;
            const chapTitle = this.querySelector(".chapter-title").textContent.trim();

            currentChapterId = chapId;

            // Lancer l'audio
            player.source = {
                type: 'audio',
                sources: [{ src: audioUrl, type: 'audio/mp3' }]
            };
            player.play();

            // Mettre à jour le titre affiché
            if (titleDisplay) titleDisplay.textContent = chapTitle;

            // Gérer la classe active
            // Retire la classe active de tous les titres
            document.querySelectorAll(".chapter-title").forEach(t => t.classList.remove("active"));

            // Ajoute active au titre du chapitre cliqué
            const title = this.querySelector(".chapter-title");
            if (title) title.classList.add("active");
        });
    });

    // --- Quand un audio se termine ---
    player.on('ended', function () {
        if (currentChapterId !== null) {
            const cb = document.querySelector('.chapter-done[data-id="' + currentChapterId + '"]');
            if (cb && !cb.checked) {
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






