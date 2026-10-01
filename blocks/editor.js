/**
 * Generic editor UI for the server-rendered SystemScripts blocks.
 *
 * Every block declares `ss.fields` in its block.json; PHP passes them here as window.ssBlocks.
 * Controls live in the sidebar and the canvas shows a live ServerSideRender preview.
 * No build step: this file uses the WordPress script globals directly.
 */
( function ( wp ) {
	var registerBlockType = wp.blocks.registerBlockType;
	var el = wp.element.createElement;
	var Fragment = wp.element.Fragment;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var MediaUpload = wp.blockEditor.MediaUpload;
	var MediaUploadCheck = wp.blockEditor.MediaUploadCheck;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;
	var TextareaControl = wp.components.TextareaControl;
	var SelectControl = wp.components.SelectControl;
	var ToggleControl = wp.components.ToggleControl;
	var Button = wp.components.Button;
	var BaseControl = wp.components.BaseControl;
	var useSelect = wp.data.useSelect;
	var ServerSideRender = wp.serverSideRender;
	var __ = wp.i18n.__;
	var DOMAIN = 'systemscripts';

	function ImageField( props ) {
		var value = props.value;
		var media = useSelect(
			function ( select ) {
				return value ? select( 'core' ).getMedia( value ) : null;
			},
			[ value ]
		);
		var preview = '';

		if ( media ) {
			var sizes = media.media_details && media.media_details.sizes;
			preview = ( sizes && sizes.thumbnail && sizes.thumbnail.source_url ) || media.source_url || '';
		}

		return el(
			BaseControl,
			{ label: props.field.label, __nextHasNoMarginBottom: true },
			el(
				MediaUploadCheck,
				null,
				el( MediaUpload, {
					allowedTypes: [ 'image' ],
					value: value,
					onSelect: function ( selected ) {
						props.onChange( selected.id );
					},
					render: function ( args ) {
						return el(
							Fragment,
							null,
							preview ? el( 'img', { src: preview, alt: '', style: { display: 'block', maxWidth: '100%', marginBottom: 8 } } ) : null,
							el( Button, { variant: 'secondary', onClick: args.open }, value ? __( 'Replace image', DOMAIN ) : __( 'Select image', DOMAIN ) ),
							value
								? el( Button, { variant: 'link', isDestructive: true, style: { marginLeft: 8 }, onClick: function () { props.onChange( 0 ); } }, __( 'Remove image', DOMAIN ) )
								: null
						);
					},
				} )
			)
		);
	}

	function RepeaterField( props ) {
		var field = props.field;
		var items = Array.isArray( props.value ) ? props.value : [];
		var itemLabel = field.itemLabel || __( 'Item', DOMAIN );

		function update( index, name, value ) {
			props.onChange(
				items.map( function ( item, i ) {
					if ( i !== index ) {
						return item;
					}
					var next = Object.assign( {}, item );
					next[ name ] = value;
					return next;
				} )
			);
		}

		function move( index, direction ) {
			var target = index + direction;
			if ( target < 0 || target >= items.length ) {
				return;
			}
			var copy = items.slice();
			var moved = copy[ index ];
			copy[ index ] = copy[ target ];
			copy[ target ] = moved;
			props.onChange( copy );
		}

		return el(
			BaseControl,
			{ label: field.label, __nextHasNoMarginBottom: true },
			items.map( function ( item, index ) {
				return el(
					PanelBody,
					{ key: index, title: itemLabel + ' ' + ( index + 1 ), initialOpen: items.length === 1 },
					( field.fields || [] ).map( function ( sub ) {
						return renderField( sub, item[ sub.name ], function ( value ) { update( index, sub.name, value ); }, sub.name );
					} ),
					el(
						'div',
						{ style: { display: 'flex', flexWrap: 'wrap', gap: 8 } },
						el( Button, { variant: 'secondary', size: 'small', disabled: index === 0, onClick: function () { move( index, -1 ); } }, __( 'Move up', DOMAIN ) ),
						el( Button, { variant: 'secondary', size: 'small', disabled: index === items.length - 1, onClick: function () { move( index, 1 ); } }, __( 'Move down', DOMAIN ) ),
						el( Button, { variant: 'secondary', size: 'small', isDestructive: true, onClick: function () { props.onChange( items.filter( function ( _, i ) { return i !== index; } ) ); } }, __( 'Remove', DOMAIN ) )
					)
				);
			} ),
			el( Button, { variant: 'primary', onClick: function () { props.onChange( items.concat( [ {} ] ) ); } }, __( 'Add', DOMAIN ) + ' ' + itemLabel.toLowerCase() )
		);
	}

	function renderField( field, value, onChange, key ) {
		var common = { key: key, label: field.label, value: value, onChange: onChange, __nextHasNoMarginBottom: true };

		switch ( field.type ) {
			case 'textarea':
				return el( TextareaControl, Object.assign( {}, common, { rows: 4 } ) );
			case 'url':
				return el( TextControl, Object.assign( {}, common, { type: 'url' } ) );
			case 'select':
				return el( SelectControl, Object.assign( {}, common, { options: field.options || [] } ) );
			case 'toggle':
				return el( ToggleControl, { key: key, label: field.label, checked: !! value, onChange: onChange, __nextHasNoMarginBottom: true } );
			case 'image':
				return el( ImageField, { key: key, field: field, value: value, onChange: onChange } );
			case 'repeater':
				return el( RepeaterField, { key: key, field: field, value: value, onChange: onChange } );
			default:
				return el( TextControl, common );
		}
	}

	function makeEdit( name ) {
		return function Edit( props ) {
			var fields = window.ssBlocks[ name ].fields || [];
			var blockProps = useBlockProps();

			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __( 'Content and settings', DOMAIN ), initialOpen: true },
						fields.map( function ( field ) {
							return renderField(
								field,
								props.attributes[ field.name ],
								function ( value ) {
									var patch = {};
									patch[ field.name ] = value;
									props.setAttributes( patch );
								},
								field.name
							);
						} )
					)
				),
				el(
					'div',
					blockProps,
					el( ServerSideRender, {
						block: name,
						attributes: props.attributes,
						EmptyResponsePlaceholder: function () {
							return el( 'p', null, __( 'Nothing to preview yet. Fill in the block settings in the sidebar.', DOMAIN ) );
						},
					} )
				)
			);
		};
	}

	Object.keys( window.ssBlocks || {} ).forEach( function ( name ) {
		// Title, category, attributes and supports are merged from the server-side block.json.
		registerBlockType( name, {
			edit: makeEdit( name ),
			save: function () {
				return null;
			},
		} );
	} );
} )( window.wp );
