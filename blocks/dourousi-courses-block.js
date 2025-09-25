( function( blocks, element, editor, components ) {
    var el = element.createElement;
    var InspectorControls = editor.InspectorControls;
    var PanelBody = components.PanelBody;
    var TextControl = components.TextControl;
    var SelectControl = components.SelectControl;

    blocks.registerBlockType( 'dourousi/courses', {
        title: 'Cours Dourousi',
        icon: 'welcome-learn-more',
        category: 'widgets',
        attributes: {
            number: { type: 'number', default: 5 },
            savant: { type: 'string', default: '' },
            layout: { type: 'string', default: 'grid' }
        },

        edit: function( props ) {
            var attrs = props.attributes;

            return [
                el( InspectorControls, {},
                    el( PanelBody, { title: 'Paramètres du bloc' },
                        el( TextControl, {
                            label: 'Nombre de cours',
                            type: 'number',
                            value: attrs.number,
                            onChange: function( value ) {
                                props.setAttributes( { number: parseInt( value ) || 5 } );
                            }
                        }),
                        el( TextControl, {
                            label: 'Savant (slug)',
                            value: attrs.savant,
                            onChange: function( value ) {
                                props.setAttributes( { savant: value } );
                            }
                        }),
                        el( SelectControl, {
                            label: 'Layout',
                            value: attrs.layout,
                            options: [
                                { label: 'Grille', value: 'grid' },
                                { label: 'Liste', value: 'list' },
                                { label: 'Carousel', value: 'carousel' }
                            ],
                            onChange: function( value ) {
                                props.setAttributes( { layout: value } );
                            }
                        })
                    )
                ),
                el( 'p', {}, 
                    'Affichage des cours : ' + attrs.number + ' cours, layout = ' + attrs.layout
                )
            ];
        },

        save: function( props ) {
            var attrs = props.attributes;
            var shortcode = '[dourousi_courses number="' + attrs.number + '" savant="' + attrs.savant + '" layout="' + attrs.layout + '"]';
            return el( 'p', {}, shortcode );
        }
    });
} )(
    window.wp.blocks,
    window.wp.element,
    window.wp.blockEditor || window.wp.editor,
    window.wp.components
);
