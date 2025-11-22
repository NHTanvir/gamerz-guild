( function( wp ) {
    if ( ! wp || ! wp.plugins || ! wp.editPost || ! wp.components || ! wp.element || ! wp.data ) {
        console.error('Required Gutenberg APIs are not available');
        return;
    }

    const { registerPlugin } = wp.plugins;
    const { PluginSidebar } = wp.editPost;
    const { PanelBody } = wp.components;
    const { useSelect, useDispatch } = wp.data;
    const { __ } = wp.i18n;
    const { TextControl, TextareaControl, SelectControl } = wp.components;
    const { Fragment } = wp.element;

    const GuildSidebarPlugin = () => {
        // Get post title and allow updating it
        const postTitle = useSelect( ( select ) =>
            select( 'core/editor' ).getEditedPostAttribute( 'title' ), []
        );

        const { editPost, savePost } = useDispatch( 'core/editor' );

        // Get and update meta fields
        const [ meta, setMeta ] = wp.data.useEntityProp( 'postType', 'guild', 'meta' );

        const updateMetaField = ( key, value ) => {
            setMeta( {
                ...meta,
                [ key ]: value
            } );
        };

        return wp.element.createElement(
            Fragment,
            {},
            wp.element.createElement(
                PluginSidebar,
                {
                    name: 'guild-sidebar',
                    icon: 'groups',
                    title: __( 'Guild Details', 'gamerz-guild' ),
                },
                wp.element.createElement(
                    PanelBody,
                    {
                        title: __( 'Guild Information', 'gamerz-guild' ),
                        initialOpen: true,
                    },
                    wp.element.createElement( TextControl, {
                        label: __( 'Guild Name', 'gamerz-guild' ),
                        value: postTitle || '',
                        onChange: ( value ) => editPost( { title: value } ),
                        help: __( 'Enter the name of the guild', 'gamerz-guild' )
                    }),
                    wp.element.createElement( TextControl, {
                        label: __( 'Guild Tagline', 'gamerz-guild' ),
                        value: meta._guild_tagline || '',
                        onChange: ( value ) => updateMetaField( '_guild_tagline', value ),
                        help: __( 'Enter the tagline for the guild', 'gamerz-guild' )
                    }),
                    wp.element.createElement( TextareaControl, {
                        label: __( 'Description', 'gamerz-guild' ),
                        value: meta._guild_description || '',
                        onChange: ( value ) => updateMetaField( '_guild_description', value ),
                        help: __( 'Describe the guild', 'gamerz-guild' )
                    }),
                    wp.element.createElement( TextControl, {
                        label: __( 'Max Members', 'gamerz-guild' ),
                        type: 'number',
                        value: meta._guild_max_members || 20,
                        onChange: ( value ) => updateMetaField( '_guild_max_members', value ),
                        help: __( 'Maximum number of members allowed in the guild', 'gamerz-guild' )
                    }),
                    wp.element.createElement( TextControl, {
                        label: __( 'Guild Creator ID', 'gamerz-guild' ),
                        type: 'number',
                        value: meta._guild_creator_id || '',
                        onChange: ( value ) => updateMetaField( '_guild_creator_id', value ),
                        help: __( 'ID of the user who created this guild', 'gamerz-guild' )
                    }),
                    wp.element.createElement( SelectControl, {
                        label: __( 'Status', 'gamerz-guild' ),
                        value: meta._guild_status || 'active',
                        options: [
                            { label: __( 'Active', 'gamerz-guild' ), value: 'active' },
                            { label: __( 'Inactive', 'gamerz-guild' ), value: 'inactive' },
                            { label: __( 'Closed', 'gamerz-guild' ), value: 'closed' }
                        ],
                        onChange: ( value ) => updateMetaField( '_guild_status', value )
                    })
                )
            )
        );
    };

    registerPlugin( 'guild-sidebar-plugin', {
        render: GuildSidebarPlugin,
    } );

} )( window.wp );