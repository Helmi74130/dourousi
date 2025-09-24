jQuery(document).ready(function($) {

    // Détermine le prochain index pour les chapitres, en se basant sur le nombre de chapitres existants.
    // Cette initialisation se fait une seule fois au chargement de la page.
    let chapterIndex = $('#dourousi_chapters_container .dourousi-chapter-row').length;

    // Ajouter un nouveau chapitre
    $('#dourousi_add_chapter').on('click', function(e){
        e.preventDefault();
        
        // Récupère le template du HTML, le remplace l'index par le nouveau, puis l'ajoute au conteneur.
        var tpl = $('#dourousi_chapter_template').html();
        tpl = tpl.replace(/__index__/g, chapterIndex);
        $('#dourousi_chapters_container').append(tpl);
        
        // Incrémente l'index pour le prochain chapitre
        chapterIndex++;
    });

    // Supprimer un chapitre
    // La délégation d'événement sur 'document' est correcte ici pour les éléments ajoutés dynamiquement.
    $(document).on('click', '.dourousi-remove-chapter', function(e){
        e.preventDefault();
        if ( confirm('Supprimer ce chapitre ?') ) {
            $(this).closest('.dourousi-chapter-row').remove();
        }
    });

    // Sélection audio (media library)
    // Ce code est la version corrigée pour éviter le bug des références.
    // Chaque clic crée une nouvelle frame pour lier la sélection au bon chapitre.
    $(document).on('click', '.dourousi-select-audio', function(e){
        e.preventDefault();
        
        var $button = $(this);
        var $container = $button.closest('.dourousi-chapter-row');
        var $input = $container.find('.dourousi_audio_id');
        var $display = $container.find('.dourousi-audio-display');

        // Création de la frame média
        var dourousi_audio_frame = wp.media({
            title: 'Choisir un fichier audio',
            library: { type: 'audio' },
            button: { text: 'Utiliser ce fichier' },
            multiple: false
        });

        // Callback quand un fichier est sélectionné
        dourousi_audio_frame.on('select', function(){
            var attachment = dourousi_audio_frame.state().get('selection').first().toJSON();
            $input.val(attachment.id);
            $display.text( attachment.title || attachment.filename || 'Audio sélectionné' );
        });

        dourousi_audio_frame.open();
    });

    // Sélection PDF (media library)
    // Pour le PDF, la variable 'dourousi_pdf_frame' peut être réutilisée car il n'y a qu'un seul champ PDF.
    var dourousi_pdf_frame;
    $(document).on('click', '.dourousi-select-pdf', function(e){
        e.preventDefault();
        
        var $button = $(this);
        var $input = $('#dourousi_pdf_id');
        var $display = $('.dourousi-pdf-display');

        if ( dourousi_pdf_frame ) {
            dourousi_pdf_frame.open();
            return;
        }

        dourousi_pdf_frame = wp.media({
            title: 'Choisir un PDF',
            library: { type: 'application/pdf' },
            button: { text: 'Utiliser ce fichier' },
            multiple: false
        });

        dourousi_pdf_frame.on('select', function(){
            var attachment = dourousi_pdf_frame.state().get('selection').first().toJSON();
            $input.val(attachment.id);
            $display.text( attachment.title || attachment.filename || 'PDF sélectionné' );
        });

        dourousi_pdf_frame.open();
    });
    

});

