jQuery(document).ready(function($) {

    // Initialise l'index pour le prochain chapitre, basé sur le nombre de chapitres existants.
    let chapterIndex = $('#dourousi_chapters_container .dourousi-chapter-row').length;

    // --- LOGIQUE D'AJOUT ET DE SUPPRESSION DE CHAPITRES ---

    /**
     * Ajoute un nouveau chapitre à la liste.
     */
    $('#dourousi_add_chapter').on('click', function(e) {
        e.preventDefault();

        const tpl = $('#dourousi_chapter_template').html();
        const newChapterHtml = tpl.replace(/__index__/g, chapterIndex);
        
        $('#dourousi_chapters_container').append(newChapterHtml);
        chapterIndex++;
    });

    /**
     * Supprime le chapitre parent. Utilise la délégation pour les éléments dynamiques.
     */
    $(document).on('click', '.dourousi-remove-chapter', function(e) {
        e.preventDefault();
        
        if (confirm('Supprimer ce chapitre ?')) {
            $(this).closest('.dourousi-chapter-row').remove();
        }
    });

    // --- LOGIQUE DE SÉLECTION DE MÉDIAS (MEDIA FRAME) ---
    
    /**
     * Ouvre le Media Frame et gère la sélection pour les champs audio (chapitres).
     * Crée une nouvelle frame à chaque clic pour garantir le ciblage correct du champ.
     */
    $(document).on('click', '.dourousi-select-audio', function(e) {
        e.preventDefault();
        
        const $button = $(this);
        const $container = $button.closest('.dourousi-chapter-row');
        const $input = $container.find('.dourousi_audio_id');
        const $display = $container.find('.dourousi-audio-display');

        const frame = wp.media({
            title: 'Choisir un fichier audio',
            library: { type: 'audio' },
            button: { text: 'Utiliser ce fichier' },
            multiple: false
        });

        frame.on('select', function() {
            const attachment = frame.state().get('selection').first().toJSON();
            $input.val(attachment.id);
            $display.text(attachment.title || attachment.filename || 'Audio sélectionné');
        });

        frame.open();
    });

    /**
     * Gère la sélection de fichiers PDF. La frame est réutilisée (mise en cache).
     */
    let dourousi_pdf_frame;
    $(document).on('click', '.dourousi-select-pdf', function(e) {
        e.preventDefault();
        
        const $input = $('#dourousi_pdf_id');
        const $display = $('.dourousi-pdf-display');

        if (dourousi_pdf_frame) {
            dourousi_pdf_frame.open();
            return;
        }

        dourousi_pdf_frame = wp.media({
            title: 'Choisir un PDF',
            library: { type: 'application/pdf' },
            button: { text: 'Utiliser ce fichier' },
            multiple: false
        });

        dourousi_pdf_frame.on('select', function() {
            const attachment = dourousi_pdf_frame.state().get('selection').first().toJSON();
            $input.val(attachment.id);
            $display.text(attachment.title || attachment.filename || 'PDF sélectionné');
        });

        dourousi_pdf_frame.open();
    });
});