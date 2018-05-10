<?php
define("FILE_TYPES_IMAGE", "gif,GIF,png,PNG,jpg,JPG,jpeg,JPEG");
define("FILE_TYPES_SCAN", "pdf,PDF");
define("FILE_TYPES_DOCS", "doc,DOC,xls,XLS,docX,DOCX,xlsx,XLSX");
$pb_submit_btn_text = array(
        'completed_off' => 'Uložit si pro budoucí editaci',
        'completed_on'  => 'Odeslat návrh ke schválení',
    );
/**
 * 20.01
 * Add additional fields to imc_issues
 *
 */
// $voting_enabled = $comments_enabled = false;
$generaloptions     = get_option( 'general_settings' );
$voting_enabled     = ( empty($generaloptions["imc_ratings"]))  ? false : $generaloptions["imc_ratings"];
$comments_enabled   = ( empty($generaloptions["imc_comments"])) ? false : $generaloptions["imc_comments"];

 class informacekprojektuMetabox {
 	private $screen = array(
 		'imc_issues',
 	);
    private $cond_url ;
 	private $meta_fields;
 	private function set_meta_fields()
    {
        $this->meta_fields = array(
     	// 	'title' => array(
     	// 		'label' => 'Název návrhu',
     	// 		'id' => 'pb_project_nazev',
     	// 		'type' => 'text',
            //     'mandatory' => true,
            //     'placeholder' => 'Vyplňte název návrhu',
            //     'title' => "title",
     	// 	),
     	// 	'description' => array(
     	// 		'label' => 'Popis návrhu',
     	// 		'id' => 'pb_project_popis',
     	// 		'type' => 'textarea',
            //     'mandatory' => true,
            //     'placeholder' => 'Vyplňte popis projektu',
            //     'title' => "descrition",
     	// 	),
            'actions' => array(
                'label'     => 'Co by se mělo udělat',
                'id'        => 'pb_project_akce',
                'type'      => 'textarea',
                'mandatory' => true,
                'placeholder' => 'Popište aktivity, které je potřeba vykonat',
                'title'     => "Actions",
            ),
     		'goals' => array(
     			'label'     => 'Proč je projekt důležitý, co je jeho cílem',
     			'id'        => 'pb_project_cile',
     			'type'      => 'textarea',
                'mandatory' => true,
                'placeholder' => 'Popište cíle projekt',
                'title'     => "goals",
                'help'      => 'Nebojte se trochu více rozepsat',
     		),
            'profits' => array(
                'label'         => 'Kdo bude mít z projektu prospěch',
                'id'            => 'pb_project_prospech',
                'type'          => 'textarea',
                'mandatory'     => true,
                'placeholder'   => 'Popište kdo a jaký bude mít z projektu prospěch',
                'title'         => 'profit',
                'help'          => '',
            ),
     		'parcel' => array(
     			'label'       => 'Parcelní číslo',
     			'id'          => 'pb_project_parcely',
     			'type'        => 'textarea',
                'mandatory'   => true,
                'placeholder' => 'Vyplňte číslo parcely ve formátu NNNN/NNNN',
                'title'       => "parcel",
                'help'        => 'Pro usnadnění kontroly zadejte prosím, každé číslo na samostatný řádek',
     		),
     	// 	'photo' => array(
     	// 		'label' => 'Ilustrační fotografie/obrázek (povinná příloha) ',
     	// 		'id'    => 'pb_project_foto',
     	// 		'type'  => 'media',
            //     'title' => 'photo',
            //     'mandatory'     => true,
            //     'material_icon' => 'image',
            //     'AddBtnLabel'   => 'Vložit fotku',
            //     'DelBtnLabel'   => 'Smazat fotku',
     	// 	),
     		'map' => array(
     			'label'     => 'Mapa (situační nákres) místa, kde se má návrh realizovat (povinná příloha)',
     			'id'        => 'pb_project_mapa',
     			'type'      => 'media',
                'title'     => "map",
                'mandatory' => true,
                'material_icon' => 'file_upload',
                // 'material_icon' => 'language',
                'AddBtnLabel'   => 'Vložit',
                'DelBtnLabel'   => 'Smazat',
                'help'          => 'Povolené typy příloh: gif, png, jpg, jpeg, pdf'
     		),
     		'cost' => array(
     			'label'         => 'Předpokládané náklady (povinná příloha)',
     			'id'            => 'pb_project_naklady',
     			'type'          => 'media',
                'title'         => "cost",
                'mandatory'     => true,
                'material_icon' => 'file_upload',
                // 'material_icon' => 'credit_card',
                'AddBtnLabel'   => 'Vložit',
                'DelBtnLabel'   => 'Smazat',
                'help'          => 'Povolené typy příloh: gif, png, jpg, jpeg, pdf, doc, docx, xls, xlsx'
     		),
            'budget_total' => array(
     			'label'     => 'Celkové náklady',
     			'id'        => 'pb_project_naklady_celkem',
     			'type'      => 'text',
                // 'options'   => 'min="100000" max="2000000" step="1000" style="text-align:right" ',
                'mandatory' => true,
                'placeholder' => 'Vyplňte celkové náklady projektu',
                'title'     => "Celkove naklady",
                'columns'   => 5,
     		),
            'budget_increase' => array(
     			'label'     => 'Náklady byly navýšeny o rezervu 10%',
     			'id'        => 'pb_project_naklady_navyseni',
     			'default'   => 'no',
     			'type'      => 'checkbox',
                'title'     => "budget_increase",
                'mandatory' => true,
                'columns'   => 6,
     		),
     		'attach1' => array(
     			'label'         => 'Vizualizace, výkresy, fotodokumentace… 1',
     			'id'            => 'pb_project_dokumentace1',
     			'type'          => 'media',
                'title'         => "attach1",
                'mandatory'     => false,
                'material_icon' => 'file_upload',
                // 'material_icon' => 'content_copy',
                'AddBtnLabel'   => 'Vložit',
                'DelBtnLabel'   => 'Smazat',
                'help'          => 'Povolené typy příloh: gif, png, jpg, jpeg, pdf',
     		),
     		'attach2' => array(
     			'label'         => 'Vizualizace, výkresy, fotodokumentace… 2',
     			'id'            => 'pb_project_dokumentace2',
     			'type'          => 'media',
                'title'         => "attach2",
                'mandatory'     => false,
                'material_icon' => 'file_upload',
                // 'material_icon' => 'content_copy',
                'AddBtnLabel'   => 'Vložit',
                'DelBtnLabel'   => 'Smazat',
                'help'          => 'Povolené typy příloh: gif, png, jpg, jpeg, pdf',
     		),
     		'attach3' => array(
     			'label'         => 'Vizualizace, výkresy, fotodokumentace… 3',
     			'id'            => 'pb_project_dokumentace3',
     			'type'          => 'media',
                'title'         => "attach3",
                'mandatory'     => false,
                'material_icon' => 'file_upload',
                // 'material_icon' => 'content_copy',
                'AddBtnLabel'   => 'Vložit',
                'DelBtnLabel'   => 'Smazat',
                'help'          => 'Povolené typy příloh: gif, png, jpg, jpeg, pdf',
     		),
            'name' => array(
                'label'     => 'Jméno a příjmení navrhovatele',
                'id'        => 'pb_project_navrhovatel_jmeno',
                'type'      => 'text',
                'default'   => '',
                'mandatory' => true,
                'placeholder' => 'Vyplňte jméno',
                'title'     => "Proposer Name",
                'columns'   => 5,
                'help'      => 'Jméno navrhovatele je povinné',
            ),
            'phone' => array(
                'label'     => 'Tel. číslo',
                'id'        => 'pb_project_navrhovatel_telefon',
                'type'      => 'tel',
                // 'options'   => 'pattern="^(\+420)? ?[1-9][0-9]{2} ?[0-9]{3} ?[0-9]{3}$"',
                'mandatory' => false,
                'placeholder' => '(+420) 999 999 999',
                'title' => "phone",
                'columns' => 3,
                'help'      => 'Číslo zadejte ve formátu (+420) 999 999 999',
            ),
            'email' => array(
                'label'     => 'E-mail',
                'id'        => 'pb_project_navrhovatel_email',
                'type'      => 'text',
                'mandatory' => true,
                'placeholder' => '',
                'title'     => "email",
                'columns'   => 4,
                'help'      => 'E-mailová adresa je povinný údaj',
            ),
            'address' => array(
                'label'     => 'Adresa (název ulice, číslo popisné, část Prahy 8)',
                'id'        => 'pb_project_navrhovatel_adresa',
                'type'      => 'text',
                'mandatory' => true,
                'placeholder' => 'Vyplňte adresu navrhovatele',
                'title'     => "address",
                'help'      => '',
            ),
            'signatures' => array(
                'label'     => 'Podpisový arch (povinná příloha)',
                'id'        => 'pb_project_podporovatele',
                'type'      => 'media',
                'title'     => "signatures",
                'mandatory' => true,
                'material_icon' => 'file_upload',
                // 'material_icon' => 'list',
                'AddBtnLabel'   => 'Vložit',
                'DelBtnLabel'   => 'Smazat',
                'help'          => 'Povolené typy příloh: gif, png, jpg, jpeg, pdf',
            ),
            'age_conf' => array(
                'label'     => 'Prohlašuji, že jsem starší 15 let',
                'id'        => 'pb_project_prohlaseni_veku',
                'default'   => 'no',
                'type'      => 'checkbox',
                'mandatory' => true,
                'title'     => "age_conf",
            ),
            'agreement'     => array(
                'label'     => 'Souhlasím s <a href="'. site_url("podminky-pouziti-a-ochrana-osobnich-udaju/") . '" target="_blank" title="Přejít na stránku s podmínkami">podmínkami použití</a>',
                'id'        => 'pb_project_podminky_souhlas',
                'default'   => 'no',
                'type'      => 'checkbox',
                'title'     => "Agreement",
                'mandatory' => true,
                'help'      => 'K podání projektu musíte souhlasit s podmínkami'
            ),
            'completed'     => array(
                'label'     => 'Popis projektu je úplný a chci ho poslat k vyhodnocení',
                'id'        => 'pb_project_edit_completed',
                'default'   => 'no',
                'type'      => 'checkbox',
                'title'     => "completed",
                'mandatory' => false,
                'help'      => 'Pokud necháte nezaškrtnuté, můžete po uložení dat popis projektu doplnit',
            ),
     	);
    }
 	public function __construct() {
 		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
 		add_action( 'admin_footer', array( $this, 'media_fields' ) );
 		add_action( 'save_post', array( $this, 'save_fields' ) );
        $this->set_meta_fields();
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
                    $link = pb_render_file_link_metabox($meta_value, $meta_field['id']);
 					$input = sprintf(
 						'<input style="width: 70%%;" id="%s" name="%s" type="text"
                            value="%s"><p style="width:2%%; display:inline-block"></p><input
                            style="width: 15%%; padding-left: 10px;display:inline-block;" class="button informacekprojektu-media"
                            id="%s_button" name="%s_button" type="button" value="Upload" /><p style="width:2%%; display:inline-block"></p>'.$link,
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

function pb_render_file_link_metabox($url, $id)
{
    $display = 'Zobrazit';

    if (! empty($url)) {
        return '<a id="'.$id.'Link" href="'.$url.'" target="_blank" data-toggle="tooltip" title="Zobrazit přílohu" class="u-pull-right
        imc-SingleHeaderLinkStyle" style="width:15%%">'.$display.'</a>';
        // <i class="material-icons md-36 imc-SingleHeaderIconStyle">open_in_browser</i></a>';
    } else {
        return '';
    }
}

function pb_new_project_mandatory_fields_js_validation()
{
    $output = pb_get_custom_fields_form_validation();
    return $output;

    return "
    [{
        name: 'postTitle',
        display: 'Název',
        rules: 'required|min_length[5]|max_length[255]'
    }, {
        name: 'my_custom_taxonomy',
        display: 'Kategorie',
        rules: 'required'
    }, {
        name: 'postContent',
        display: 'Popis',
        rules: 'required'
    }, {
        name: 'pb_project_akce',
        display: '\"Co by se mělo udělat\"',
        rules: 'required',
        depends: 'pb_project_js_validate_required'
    }, {
        name: 'pb_project_cile',
        display: '\"Proč je projekt důležitý\"',
        rules: 'required',
        depends: 'pb_project_js_validate_required'
    }, {
        name: 'pb_project_prospech',
        display: '\"Kdo bude mít z projektu prospěch\"',
        rules: 'required',
        depends: 'pb_project_js_validate_required'
    }, {
        name: 'postAddress',
        display: 'Adresa',
        rules: 'required',
        depends: 'pb_project_js_validate_required'
    }, {
        name: 'pb_project_parcely',
        display: 'Parcelní číslo',
        rules: 'required',
        depends: 'pb_project_js_validate_required'
    }, {
        name: 'featured_image',
        display: 'Fotografie',
        rules: 'is_file_type[gif,GIF,png,PNG,jpg,JPG,jpeg,JPEG]'
    }, {
        name: 'pb_project_mapaName',
        display: 'Mapa (situační nákres)',
        rules: 'required',
        depends: 'pb_project_js_validate_required'
    }, {
        name: 'pb_project_mapa',
        display: 'Mapa (situační nákres)',
        rules: 'is_file_type[gif,GIF,png,PNG,jpg,JPG,jpeg,JPEG,pdf,PDF]'
    }, {
        name: 'pb_project_nakladyName',
        display: 'Předpokládané náklady',
        rules: 'required',
        depends: 'pb_project_js_validate_required'
    }, {
        name: 'pb_project_naklady',
        display: 'Předpokládané náklady',
        rules: 'is_file_type[gif,GIF,png,PNG,jpg,JPG,jpeg,JPEG,pdf,PDF,doc,DOC,xls,XLS]'
    }, {
        name: 'pb_project_naklady_celkem',
        display: 'Celkové náklady',
        rules: 'required|integer|greater_than[99999]|less_than[2000001]',
        depends: 'pb_project_js_validate_required'
    }, {
        name: 'pb_project_naklady_navyseni',
        display: 'Navýšení rozpočtu',
        rules: 'required',
        depends: 'pb_project_js_validate_required'
    }, {
        name: 'pb_project_navrhovatel_jmeno',
        display: 'Jméno navrhovatele',
        rules: 'required',
        depends: 'pb_project_js_validate_required'
    }, {
        name: 'pb_project_navrhovatel_telefon',
        display: 'Telefonický kontakt',
        rules: 'valid_phone'
    }, {
        name: 'pb_project_navrhovatel_email',
        display: 'email',
        rules: 'required|valid_email',
        depends: 'pb_project_js_validate_required'
    }, {
        name: 'pb_project_navrhovatel_adresa',
        display: 'Adresa navrhovatele',
        rules: 'required',
        depends: 'pb_project_js_validate_required'
    }, {
        name: 'pb_project_podporovateleName',
        display: 'Podpisový arch',
        rules: 'required',
        depends: 'pb_project_js_validate_required'
    }, {
        name: 'pb_project_podporovatele',
        display: 'Podpisový arch',
        rules: 'is_file_type[gif,GIF,png,PNG,jpg,JPG,jpeg,JPEG,pdf,PDF]'
    }, {
        name: 'pb_project_prohlaseni_veku',
        display: 'Prohlašení věku',
        rules: 'required',
        depends: 'pb_project_js_validate_required'
    }, {
        name: 'pb_project_podminky_souhlas',
        display: 'Souhlas s podmínkami',
        rules: 'required',
        depends: 'pb_project_js_validate_required'
    }]
    ";
}
/**
 * 6.09
 * Function that checks if user can edit an issue
 * basically checks if current user is issue's author
 * and if status changed
 */

function pb_user_can_edit($post_id, $current_user) {

	$status_terms = get_terms( 'imcstatus' , array( 'hide_empty' => 0 , 'orderby' => 'id', 'order' => 'ASC') );
    $terms_count = count($status_terms);
    if ( $terms_count > 2 ) {
        $edit_statuses = array($status_terms[0]->term_id, $status_terms[1]->term_id);
    } elseif ($terms_count > 1) {
        $edit_statuses = array($status_terms[0]->term_id, );
    } else {
        $edit_statuses = array();
    }

	// Issue is not current user's
	$my_issue = get_post($post_id);
	$author_id = intval($my_issue ->post_author, 10); // Author's id of current #post

	if($author_id == $current_user) {
		return in_array( getCurrentImcStatusID($post_id), $edit_statuses , true);
	}

	return false;
}
