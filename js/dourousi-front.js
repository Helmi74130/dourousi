document.addEventListener('DOMContentLoaded', () => {
    const playerElement = document.getElementById('main-audio-player');
    
    // Quitte si l'élément ou l'ID du post est manquant
    const postId = playerElement?.dataset.postId;
    if (!playerElement || !postId) return;

    const checkboxes = document.querySelectorAll('.chapter-done');
    const progressFill = document.querySelector('.progress-fill');
    const progressText = document.querySelector('.progress-text');
    const chapters = document.querySelectorAll('.dourousi-chapter');
    const titleDisplay = document.getElementById('current-title-display');

    let currentChapterId = null;

    // --- PLYR INITIALIZATION ---

    const isDesktop = window.innerWidth > 768; 
    let controlsList = [
        'play', 
        'progress', 
        'current-time', 
        'duration',
        'download'
    ];
    
    // Ajoute les contrôles de paramètres et de volume uniquement sur les grands écrans
    if (isDesktop) {
        controlsList.push('settings', 'mute', 'volume');
    }

    const player = new Plyr(playerElement, {
        controls: controlsList,
        settings: ['speed'], 
        speed: { selected: 1, options: [0.5, 0.75, 1, 1.25, 1.5, 2] }
    });
    
    // --- PROGRESS & STORAGE HELPERS ---

    /**
     * Calcule et met à jour l'affichage de la barre de progression.
     */
    const updateProgress = () => {
        const total = checkboxes.length;
        let done = 0;
        
        checkboxes.forEach(cb => {
            if (cb.checked) done++;
        });

        const percent = total > 0 ? (done / total) * 100 : 0;
        progressFill.style.width = `${percent}%`;
        progressText.textContent = `${done} / ${total} cours terminés`;
    };

    /**
     * Persiste l'état de complétion dans localStorage.
     * @param {HTMLElement} checkbox 
     */
    const handleChapterCompletion = (checkbox) => {
        const chapId = checkbox.dataset.id;
        const key = `chapter_done_${postId}_${chapId}`;
        const listItem = checkbox.closest('li');
        
        localStorage.setItem(key, checkbox.checked);
        listItem.classList.toggle('completed', checkbox.checked);
        
        updateProgress();
    };

    // --- CHECKBOX INITIALIZATION AND LISTENERS ---

    checkboxes.forEach(cb => {
        const chapId = cb.dataset.id;
        const key = `chapter_done_${postId}_${chapId}`;
        const saved = localStorage.getItem(key);

        // Restaure l'état
        if (saved === 'true') {
            cb.checked = true;
            cb.closest('li').classList.add('completed');
        }

        // Ajoute l'écouteur de changement
        cb.addEventListener('change', () => handleChapterCompletion(cb));
    });

    // --- CHAPTER CLICK HANDLER ---

    chapters.forEach(chapter => {
        chapter.addEventListener('click', (e) => {
            // Empêche le lancement de l'audio si l'utilisateur clique sur la checkbox ou son label.
            if (e.target.closest('input') || e.target.closest('label')) return;

            const audioUrl = chapter.dataset.audio;
            const chapId = chapter.dataset.id;
            const chapTitle = chapter.querySelector('.chapter-title').textContent.trim();
            const currentTitleElement = chapter.querySelector('.chapter-title');

            currentChapterId = chapId;

            // Charge et lance l'audio
            player.source = {
                type: 'audio',
                sources: [{ src: audioUrl, type: 'audio/mp3' }]
            };
            player.play();

            // Met à jour le titre affiché
            if (titleDisplay) {
                titleDisplay.textContent = chapTitle;
            }

            // Gère la classe active
            document.querySelectorAll('.chapter-title').forEach(t => t.classList.remove('active'));
            currentTitleElement?.classList.add('active');
        });
    });

    // --- AUDIO ENDED EVENT ---
    
    player.on('ended', () => {
        if (currentChapterId) {
            const cb = document.querySelector(`.chapter-done[data-id="${currentChapterId}"]`);
            
            if (cb && !cb.checked) {
                // Marque le chapitre comme terminé et met à jour le stockage
                cb.checked = true;
                handleChapterCompletion(cb);
            }
        }
    });

    // Initialisation
    updateProgress();
});