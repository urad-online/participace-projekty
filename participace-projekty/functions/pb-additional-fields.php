<?php

/**
 * 20.01
 * Add additional fields to imc_issues
 *
 */



 class informacekprojektuMetabox {
 	private $screen = array(
 		'imc_issues',
 	);
 	private $meta_fields = array(
 		array(
 			'label' => 'Jméno a příjmení navrhovatele',
 			'id' => 'pb_project_navrhovatel_jmeno',
 			'type' => 'text',
 		),
 		array(
 			'label' => 'Adresa (název ulice, číslo popisné, část Prahy 8)',
 			'id' => 'pb_project_navrhovatel_adresa',
 			'type' => 'text',
 		),
 		array(
 			'label' => 'Telefonický kontakt',
 			'id' => 'pb_project_navrhovatel_telefon',
 			'type' => 'tel',
 		),
 		array(
 			'label' => 'E-mail',
 			'id' => 'pb_project_navrhovatel_email',
 			'type' => 'email',
 		),
 		array(
 			'label' => 'Prohlašuji, že jsem starší 15 let ',
 			'id' => 'pb_project_prohlaseni_veku',
 			'default' => 'yes',
 			'type' => 'checkbox',
 		),
 		array(
 			'label' => 'Název návrhu',
 			'id' => 'pb_project_nazev',
 			'type' => 'text',
 		),
 		array(
 			'label' => 'Popis návrhu',
 			'id' => 'pb_project_popis',
 			'type' => 'textarea',
 		),
 		array(
 			'label' => 'Proč je projekt důležitý, co je jeho cílem',
 			'id' => 'pb_project_cile',
 			'type' => 'textarea',
 		),
 		array(
 			'label' => 'Co by se mělo udělat',
 			'id' => 'pb_project_akce',
 			'type' => 'textarea',
 		),
 		array(
 			'label' => 'Parcelní číslo',
 			'id' => 'pb_project_parcely',
 			'type' => 'text',
 		),
 		array(
 			'label' => 'Kdo bude mít z projektu prospěch',
 			'id' => 'pb_project_prospech',
 			'type' => 'textarea',
 		),
 		array(
 			'label' => 'Souhlas s podmínkami',
 			'id' => 'pb_project_podminky_souhlas',
 			'default' => 'no',
 			'type' => 'checkbox',
 		),
 		array(
 			'label' => 'Podpisový arch (povinná příloha)',
 			'id' => 'pb_project_podporovatele',
 			'type' => 'media',
 		),
 		array(
 			'label' => 'Ilustrační fotografie/obrázek (povinná příloha) ',
 			'id' => 'pb_project_foto',
 			'type' => 'media',
 		),
 		array(
 			'label' => 'Mapa (situační nákres) místa, kde se má návrh realizovat (povinná příloha)',
 			'id' => 'pb_project_mapa',
 			'type' => 'media',
 		),
 		array(
 			'label' => 'Předpokládané náklady (povinná příloha)',
 			'id' => 'pb_project_naklady',
 			'type' => 'number',
 		),
 		array(
 			'label' => 'Vizualizace, výkresy, fotodokumentace… (nepovinné přílohy)',
 			'id' => 'pb_project_dokumentace1',
 			'type' => 'media',
 		),
 		array(
 			'label' => 'Vizualizace, výkresy, fotodokumentace… (nepovinné přílohy)',
 			'id' => 'pb_project_dokumentace2',
 			'type' => 'media',
 		),
 		array(
 			'label' => 'Vizualizace, výkresy, fotodokumentace… (nepovinné přílohy)',
 			'id' => 'pb_project_dokumentace3',
 			'type' => 'media',
 		),
 	);
 	public function __construct() {
 		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
 		add_action( 'admin_footer', array( $this, 'media_fields' ) );
 		add_action( 'save_post', array( $this, 'save_fields' ) );
 	}
 	public function add_meta_boxes() {
 		foreach ( $this->screen as $single_screen ) {
 			add_meta_box(
 				'informacekprojektu',
 				__( 'Informace k projektu', 'pb-hlasovani' ),
 				array( $this, 'meta_box_callback' ),
 				$single_screen,
 				'normal',
 				'default'
 			);
 		}
 	}
 	public function meta_box_callback( $post ) {
 		wp_nonce_field( 'informacekprojektu_data', 'informacekprojektu_nonce' );
 		echo 'Detaily projektu';
 		$this->field_generator( $post );
 	}
 	public function media_fields() {
 		?><script>
 			jQuery(document).ready(function($){
 				if ( typeof wp.media !== 'undefined' ) {
 					var _custom_media = true,
 					_orig_send_attachment = wp.media.editor.send.attachment;
 					$('.informacekprojektu-media').click(function(e) {
 						var send_attachment_bkp = wp.media.editor.send.attachment;
 						var button = $(this);
 						var id = button.attr('id').replace('_button', '');
 						_custom_media = true;
 							wp.media.editor.send.attachment = function(props, attachment){
 							if ( _custom_media ) {
 								$('input#'+id).val(attachment.url);
 							} else {
 								return _orig_send_attachment.apply( this, [props, attachment] );
 							};
 						}
 						wp.media.editor.open(button);
 						return false;
 					});
 					$('.add_media').on('click', function(){
 						_custom_media = false;
 					});
 				}
 			});
 		</script><?php
 	}
 	public function field_generator( $post ) {
 		$output = '';
 		foreach ( $this->meta_fields as $meta_field ) {
 			$label = '<label for="' . $meta_field['id'] . '">' . $meta_field['label'] . '</label>';
 			$meta_value = get_post_meta( $post->ID, $meta_field['id'], true );
 			if ( empty( $meta_value ) ) {
 				$meta_value = $meta_field['default']; }
 			switch ( $meta_field['type'] ) {
 				case 'media':
 					$input = sprintf(
 						'<input style="width: 80%%" id="%s" name="%s" type="text" value="%s"> <input style="width: 19%%" class="button informacekprojektu-media" id="%s_button" name="%s_button" type="button" value="Upload" />',
 						$meta_field['id'],
 						$meta_field['id'],
 						$meta_value,
 						$meta_field['id'],
 						$meta_field['id']
 					);
 					break;
 				case 'checkbox':
 					$input = sprintf(
 						'<input %s id=" % s" name="% s" type="checkbox" value="1">',
 						$meta_value === '1' ? 'checked' : '',
 						$meta_field['id'],
 						$meta_field['id']
 						);
 					break;
 				case 'textarea':
 					$input = sprintf(
 						'<textarea style="width: 100%%" id="%s" name="%s" rows="5">%s</textarea>',
 						$meta_field['id'],
 						$meta_field['id'],
 						$meta_value
 					);
 					break;
 				default:
 					$input = sprintf(
 						'<input %s id="%s" name="%s" type="%s" value="%s">',
 						$meta_field['type'] !== 'color' ? 'style="width: 100%"' : '',
 						$meta_field['id'],
 						$meta_field['id'],
 						$meta_field['type'],
 						$meta_value
 					);
 			}
 			$output .= $this->format_rows( $label, $input );
 		}
 		echo '<table class="form-table"><tbody>' . $output . '</tbody></table>';
 	}
 	public function format_rows( $label, $input ) {
 		return '<tr><th>'.$label.'</th><td>'.$input.'</td></tr>';
 	}
 	public function save_fields( $post_id ) {
 		if ( ! isset( $_POST['informacekprojektu_nonce'] ) )
 			return $post_id;
 		$nonce = $_POST['informacekprojektu_nonce'];
 		if ( !wp_verify_nonce( $nonce, 'informacekprojektu_data' ) )
 			return $post_id;
 		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
 			return $post_id;
 		foreach ( $this->meta_fields as $meta_field ) {
 			if ( isset( $_POST[ $meta_field['id'] ] ) ) {
 				switch ( $meta_field['type'] ) {
 					case 'email':
 						$_POST[ $meta_field['id'] ] = sanitize_email( $_POST[ $meta_field['id'] ] );
 						break;
 					case 'text':
 						$_POST[ $meta_field['id'] ] = sanitize_text_field( $_POST[ $meta_field['id'] ] );
 						break;
 				}
 				update_post_meta( $post_id, $meta_field['id'], $_POST[ $meta_field['id'] ] );
 			} else if ( $meta_field['type'] === 'checkbox' ) {
 				update_post_meta( $post_id, $meta_field['id'], '0' );
 			}
 		}
 	}
 }
 if (class_exists('informacekprojektuMetabox')) {
 	new informacekprojektuMetabox;
 };
