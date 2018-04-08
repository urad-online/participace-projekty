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
 		'name' => array(
 			'label' => 'Jméno a příjmení navrhovatele',
 			'id' => 'pb_project_navrhovatel_jmeno',
 			'type' => 'text',
            'default' => '',
            'mandatory' => true,
            'placeholder' => 'Vyplňte jméno',
            'title' => "Proposer Name",
            'columns' => 6,
 		),
 		'address' => array(
 			'label' => 'Adresa (název ulice, číslo popisné, část Prahy 8)',
 			'id' => 'pb_project_navrhovatel_adresa',
 			'type' => 'text',
            'mandatory' => true,
            'placeholder' => 'Vyplňte adresu navrhovatele',
            'title' => "address",
 		),
 		'phone' => array(
 			'label' => 'Telefonický kontakt',
 			'id' => 'pb_project_navrhovatel_telefon',
 			'type' => 'tel',
            'mandatory' => false,
            'placeholder' => 'Vyplňte telefonní číslo',
            'title' => "phone",
            'columns' => 3,
 		),
 		'email' => array(
 			'label' => 'E-mail',
 			'id' => 'pb_project_navrhovatel_email',
 			'type' => 'email',
            'mandatory' => true,
            'placeholder' => 'Vyplňte e-mailovou adresu',
            'title' => "email",
            'columns' => 3,
 		),
 		'age_conf' => array(
 			'label' => 'Prohlašuji, že jsem starší 15 let ',
 			'id' => 'pb_project_prohlaseni_veku',
 			'default' => 'yes',
 			'type' => 'checkbox',
            'mandatory' => true,
            'title' => "age_conf",
 		),
 		'title' => array(
 			'label' => 'Název návrhu',
 			'id' => 'pb_project_nazev',
 			'type' => 'text',
            'mandatory' => true,
            'placeholder' => 'Vyplňte název návrhu',
            'title' => "title",
 		),
 		'description' => array(
 			'label' => 'Popis návrhu',
 			'id' => 'pb_project_popis',
 			'type' => 'textarea',
            'mandatory' => true,
            'placeholder' => 'Vyplňte popis projektu',
            'title' => "descrition",
 		),
 		'goals' => array(
 			'label' => 'Proč je projekt důležitý, co je jeho cílem',
 			'id' => 'pb_project_cile',
 			'type' => 'textarea',
            'mandatory' => true,
            'placeholder' => 'Popište cíle projekt',
            'title' => "goals",
 		),
 		'actions' => array(
 			'label' => 'Co by se mělo udělat',
 			'id' => 'pb_project_akce',
 			'type' => 'textarea',
            'mandatory' => true,
            'placeholder' => 'Popište aktivity, které je pottřeba vykonat',
            'title' => "Actions",
 		),
 		'parcel' => array(
 			'label' => 'Parcelní číslo',
 			'id' => 'pb_project_parcely',
 			'type' => 'text',
            'mandatory' => true,
            'placeholder' => 'Vyplňte číslo parcely ve formátu NNNN/NNNN',
            'title' => "parcel",
 		),
 		'profits' => array(
 			'label' => 'Kdo bude mít z projektu prospěch',
 			'id' => 'pb_project_prospech',
 			'type' => 'textarea',
            'mandatory' => true,
            'placeholder' => 'Popište kdo a jaký bude mít z projektu prospěch',
            'title' => 'profit',
 		),
 		'agreement' => array(
 			'label' => 'Souhlasím s podmínkami',
 			'id' => 'pb_project_podminky_souhlas',
 			'default' => 'no',
 			'type' => 'checkbox',
            'title' => "Agreement",
            'mandatory' => true,
 		),
 		'signatures' => array(
 			'label'     => 'Podpisový arch (povinná příloha)',
 			'id'        => 'pb_project_podporovatele',
 			'type'      => 'media',
            'title'     => "signatures",
            'mandatory' => true,
            'material_icon' => 'list',
            'AddBtnLabel'   => 'Vložit arch',
            'DelBtnLabel'   => 'Smazat arch',
 		),
 		'photo' => array(
 			'label' => 'Ilustrační fotografie/obrázek (povinná příloha) ',
 			'id'    => 'pb_project_foto',
 			'type'  => 'media',
            'title' => 'photo',
            'mandatory'     => true,
            'material_icon' => 'image',
            'AddBtnLabel'   => 'Vložit fotku',
            'DelBtnLabel'   => 'Smazat fotku',
 		),
 		'map' => array(
 			'label' => 'Mapa (situační nákres) místa, kde se má návrh realizovat (povinná příloha)',
 			'id'    => 'pb_project_mapa',
 			'type'  => 'media',
            'title' => "map",
            'mandatory'     => true,
            'material_icon' => 'language',
            'AddBtnLabel'   => 'Vložit mapu',
            'DelBtnLabel'   => 'Smazat mapu',
 		),
 		'cost' => array(
 			'label' => 'Předpokládané náklady (povinná příloha)',
 			'id' => 'pb_project_naklady',
 			'type' => 'media',
            'title' => "cost",
            'mandatory'     => true,
            'material_icon' => 'credit_card',
            'AddBtnLabel'   => 'Vložit náklady',
            'DelBtnLabel'   => 'Smazat náklady',
 		),
 		'attach1' => array(
 			'label' => 'Vizualizace, výkresy, fotodokumentace… 1 (nepovinné přílohy)',
 			'id' => 'pb_project_dokumentace1',
 			'type' => 'media',
            'title' => "attach1",
            'mandatory'     => false,
            'material_icon' => 'content_copy',
            'AddBtnLabel'   => 'Vložit přílohu',
            'DelBtnLabel'   => 'Smazat přílohu',
 		),
 		'attach2' => array(
 			'label' => 'Vizualizace, výkresy, fotodokumentace… 2 (nepovinné přílohy)',
 			'id' => 'pb_project_dokumentace2',
 			'type' => 'media',
            'title' => "attach2",
            'mandatory'     => false,
            'material_icon' => 'content_copy',
            'AddBtnLabel'   => 'Vložit přílohu',
            'DelBtnLabel'   => 'Smazat přílohu',
 		),
 		'attach3' => array(
 			'label' => 'Vizualizace, výkresy, fotodokumentace… 3 (nepovinné přílohy)',
 			'id' => 'pb_project_dokumentace3',
 			'type' => 'media',
            'title' => "attach3",
            'mandatory'     => false,
            'material_icon' => 'content_copy',
            'AddBtnLabel'   => 'Vložit přílohu',
            'DelBtnLabel'   => 'Smazat přílohu',
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
 			if ( empty( $meta_value )  && ! empty( $meta_field[ 'default'] )) {
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
    public function get_fields()
    {
        return $this->meta_fields;
    }
 }
 if (class_exists('informacekprojektuMetabox')) {
 	new informacekprojektuMetabox;
 };

function pb_template_part_new_project( $data = null)
{
    $pb_project_meta_fields = new informacekprojektuMetabox();
    $fields = $pb_project_meta_fields->get_fields();

    ob_start();
    pb_render_field( $fields['goals'], pb_render_field_get_value( $fields['goals']['id'], $data ));
    pb_render_field( $fields['actions'] );
    pb_render_field( $fields['profits'] );
    pb_render_field( $fields['parcel'] );

    echo '<div class="imc-row">';
    pb_render_field( $fields['name'] );
    pb_render_field( $fields['phone'] );
    pb_render_field( $fields['email'] );
    echo '</div>';
    pb_render_field( $fields['address'] );
    pb_render_field( $fields['age_conf'] );

    pb_render_field( $fields['signatures'] );
    pb_render_field( $fields['map'] );
    pb_render_field( $fields['cost'] );
    pb_render_field( $fields['attach1'] );
    pb_render_field( $fields['attach2'] );
    pb_render_field( $fields['attach3'] );
    pb_render_field( $fields['agreement'] );

    ?>

    <script>
         "use strict";
        function pbProjectAddFile(e){
            jQuery("#"+e.id+"Name").html(e.files[0].name);
        };
        function imcDeleteAttachedFile( id ){
            document.getElementById(id).value = "";

            jQuery("#"+id+"Name").html("Vyberte soubor");
        };
    </script>

     <?php
     return ob_get_clean();
}
function pb_render_field_get_value( $id, $values)
{
    if (! empty($values[ $id][0])) {
        return $values[ $id][0];
    } else {
        return '';
    }
}

function pb_render_field( $field, $value = '' )
{
    switch ( $field['type'] ) {
        case 'media':
            pb_render_file_attachemnt( $field, $value);
            break;
        case 'checkbox':
            pb_render_checkbox($field, $value);
            break;
        case 'textarea':
            pb_render_textarea($field, $value);
            break;
        default:
            pb_render_text($field, $value);
    }
}

function pb_render_textarea( $input = null, $value = '' )
{
    if ( !empty( $input['mandatory'])) {
        $mandatory = pb_render_mandatory( $input['mandatory']) ;
        $required = " required ";
    } else {
        $mandatory = '';
        $required = "";
    }
    if ( ! empty($input['rows'])) {
        $rows = $input['rows'];
    } else {
        $rows = 3;
    }

    $output = '<div class="imc-row">
        <h3 class="u-pull-left imc-SectionTitleTextStyle">%s</h3>%s
        <textarea %s placeholder="%s" rows="%d"
             class="imc-InputStyle" title="%s" name="%s"
             id="%s">%s</textarea></div>';
    if ( empty( $input ) ) {
        return $output;
    } else {
        printf( $output,
            $input['label'],
            $mandatory,
            $required,
            $input['placeholder'],
            $rows,
            $input['title'],
            $input['id'],
            $input['id'],
            $value
        );
    }
}
function pb_render_text( $input = null, $value = '', $columns = null )
{
    if ( !empty( $input['mandatory'])) {
        $mandatory = pb_render_mandatory( $input['mandatory']) ;
        $required = " required ";
    } else {
        $mandatory = '';
        $required = "";
    }
    if ( ! empty($input['columns'])) {
        $columns = $input['columns'];
    } else {
        $columns = '';
    }

    $output = '<h3 class="imc-SectionTitleTextStyle">%s</h3>%s<input %s autocomplete="off"
        placeholder="%s" type="text" name="%s" id="%s" class="imc-InputStyle" value="%s"/>
        <label id="%sLabel" class="imc-ReportFormErrorLabelStyle imc-TextColorPrimary"></label>';
    if ( ! empty($columns) ) {
        $output = '<div class="imc-grid-'.$columns.' imc-columns">' . $output . '</div>';
    }
    if ( empty( $input ) ) {
        return $output;
    } else {
        printf( $output,
            $input['label'],
            $mandatory,
            $required,
            $input['placeholder'],
            $input['id'],
            $input['id'],
            $value,
            $input['id']
        );
    }
}
function pb_render_file_attachemnt( $input, $value = '')
{
    if ( ! empty( $input['mandatory'])) {
        $mandatory = pb_render_mandatory( $input['mandatory']) ;
        $required = " required ";
    } else {
        $mandatory = '';
        $required = "";
    }

    $output = '<div class="imc-row" id="pbProjectSection%s">
                <div class="imc-row">
                    <h3 class="u-pull-left imc-SectionTitleTextStyle">%s</h3>%s
                </div>
                <div class="imc-row">
                    <div class="imc-grid-4 imc-columns">
                    <span id="%sName" class="imc-ReportGenericLabelStyle imc-TextColorSecondary">'. __('Vyberte soubor','participace-projekty') .'</span>
                </div>
                <div class="imc-grid-8 imc-columns">
                    <div class="u-cf">
                        <div class="imc-row">
                            <input %s autocomplete="off" class="imc-ReportAddImgInputStyle" id="%s" type="file" name="%s" onchange="pbProjectAddFile(this)" />
                            <label for="%s">
                                <i class="material-icons md-24 imc-AlignIconToButton">%s</i>%s
                            </label>
                            <button type="button" class="imc-button" onclick="imcDeleteAttachedFile(\'%s\');">
                                <i class="material-icons md-24 imc-AlignIconToButton">delete</i>%s</button>
                </div></div></div></div></div>';
    if ( empty( $input ) ) {
        return $output;
    } else {
        printf( $output,
            $input['title'],
            $input['label'],
            $mandatory,
            $input['id'],
            $required,
            $input['id'],
            $input['id'],
            $input['id'],
            $input['material_icon'],
            $input['AddBtnLabel'],
            $input['id'],
            $input['DelBtnLabel']
        );
    }
}
function pb_render_checkbox( $input, $value = '')
{
    if ( empty( $value) ){
        if (! empty($input['default']) && ( $input['default'] != 'no')) {
            $value = 1;
            $required = " required ";
        } else {
            $value = 0;
            $required = "";
        }
    }
    $output = '<div class="imc-row">
    <h3 class="imc-SectionTitleTextStyle"><label id="%sLabel" for="%s">%s</label>
        <input type="checkbox"  %s name="%s" id="%s" class="imc-InputStyle" value="%s" style="width:20px; height:20px; display:inline-block"/></h3></div>' ;
    if ( empty( $input ) ) {
        return $output;
    } else {
        printf( $output,
            $input['id'],
            $input['id'],
            $input['label'],
            $required,
            $input['id'],
            $input['id'],
            $value
        );
    }
}

function pb_render_mandatory( $mandatory = false)
{
    if ( $mandatory ) {
        return '';
    } else {
        return '<span class="imc-OptionalTextLabelStyle">" "(' . __('optional','participace-projekty') .')></span>';
    }
}

function pb_new_project_meta_save_prep( $data)
{
    $output = array(
		'imc_lat'		=> esc_attr(sanitize_text_field($data['imcLatValue'])),
		'imc_lng'		=> esc_attr(sanitize_text_field($data['imcLngValue'])),
		'imc_address'	=> esc_attr(sanitize_text_field($data['postAddress'])),
		'imc_likes'		=> '0',
		'modality'		=> '0',
		'pb_project_navrhovatel_jmeno'   => esc_attr(sanitize_text_field($data['pb_project_navrhovatel_jmeno'])),
		'pb_project_navrhovatel_adresa'  => esc_attr(sanitize_text_field($data['pb_project_navrhovatel_adresa'])),
		'pb_project_navrhovatel_telefon' => esc_attr(sanitize_text_field($data['pb_project_navrhovatel_telefon'])),
        'pb_project_parcely'             => esc_attr(sanitize_text_field($data['pb_project_parcely'])),
        'pb_project_prohlaseni_veku'     => esc_attr(sanitize_text_field($data['pb_project_prohlaseni_veku'])),
        'pb_project_podminky_souhlas'    => esc_attr(sanitize_text_field($data['pb_project_podminky_souhlas'])),
		'pb_project_navrhovatel_email'   => esc_attr(sanitize_email($data['pb_project_navrhovatel_email'])),
        'pb_project_cile'                => esc_attr(sanitize_textarea_field($data['pb_project_cile'])),
        'pb_project_akce'                => esc_attr(sanitize_textarea_field($data['pb_project_akce'])),
        'pb_project_prospech'            => esc_attr(sanitize_textarea_field($data['pb_project_prospech'])),
		);
    return $output;
}

function pb_new_project_insert_attachments( $post_id, $files)
{
    // $_FILE['id'], fields - error, name, size, tmp_name, type, pro prazdne je error = 4 ostatni prazdne,
    pb_new_project_insert_attachment_1($files['pb_project_podporovatele'], $post_id, 'pb_project_podporovatele');
    pb_new_project_insert_attachment_1($files['pb_project_mapa'], $post_id, 'pb_project_mapa');
    pb_new_project_insert_attachment_1($files['pb_project_naklady'], $post_id, 'pb_project_naklady');
    pb_new_project_insert_attachment_1($files['pb_project_dokumentace1'], $post_id, 'pb_project_dokumentace1');
    pb_new_project_insert_attachment_1($files['pb_project_dokumentace2'], $post_id, 'pb_project_dokumentace2');
    pb_new_project_insert_attachment_1($files['pb_project_dokumentace3'], $post_id, 'pb_project_dokumentace3');
}

function pb_new_project_insert_attachment_1 ($file, $post_id, $attachment_type )
{
    if (( $file['error'] == '0') && (! empty($post_id)) && (! empty($attachment_type))) {
        $attachment_id = imc_upload_img( $file, $post_id, $post_id . '-' . $attachment_type, null);
        if ( $attachment_id) {
            $url = wp_get_attachment_url( $attachment_id);
            update_post_meta( $post_id, $attachment_type, $url);
        }
        return $attachment_id;
    } elseif ($post_id) {
        delete_post_meta( $post_id, $attachment_type );
        return true;
    } else {
        return false;
    }
}
