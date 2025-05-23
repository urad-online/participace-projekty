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
        $pb_form_settings = get_option('pb_form_settings');
        // Ensure defaults are available if settings haven't been saved yet.
        // This primarily helps if the plugin is activated and settings page hasn't been visited.
        if (false === $pb_form_settings) {
            // Manually define defaults similar to ImcSettingsPage::load_settings()
            // This is a subset for brevity, ideally, these would be pulled from a shared source or helper
            $pb_form_settings = array(
                'pb_terms_url' => site_url("podminky-pouziti-a-ochrana-osobnich-udaju/"),
                'pb_actions_enabled' => true, 'pb_actions_mandatory' => true, 'pb_actions_label' => 'Co by se mělo udělat',
                'pb_goals_enabled' => true, 'pb_goals_mandatory' => true, 'pb_goals_label' => 'Proč je projekt důležitý, co je jeho cílem',
                'pb_profits_enabled' => true, 'pb_profits_mandatory' => true, 'pb_profits_label' => 'Kdo bude mít z projektu prospěch',
                'pb_parcel_enabled' => true, 'pb_parcel_mandatory' => true, 'pb_parcel_label' => 'Parcelní číslo',
                'pb_map_enabled' => true, 'pb_map_mandatory' => true, 'pb_map_label' => 'Mapa (situační nákres) místa, kde se má návrh realizovat (povinná příloha)',
                'pb_cost_enabled' => true, 'pb_cost_mandatory' => true, 'pb_cost_label' => 'Předpokládané náklady (povinná příloha)',
                'pb_budget_total_enabled' => true, 'pb_budget_total_mandatory' => true, 'pb_budget_total_label' => 'Celkové náklady',
                'pb_budget_increase_enabled' => true, 'pb_budget_increase_mandatory' => true, 'pb_budget_increase_label' => 'Náklady byly navýšeny o rezervu 10%',
                'pb_attach1_enabled' => true, 'pb_attach1_mandatory' => false, 'pb_attach1_label' => 'Vizualizace, výkresy, fotodokumentace… 1',
                'pb_attach2_enabled' => true, 'pb_attach2_mandatory' => false, 'pb_attach2_label' => 'Vizualizace, výkresy, fotodokumentace… 2',
                'pb_attach3_enabled' => true, 'pb_attach3_mandatory' => false, 'pb_attach3_label' => 'Vizualizace, výkresy, fotodokumentace… 3',
                'pb_name_enabled' => true, 'pb_name_mandatory' => true, 'pb_name_label' => 'Jméno a příjmení navrhovatele',
                'pb_phone_enabled' => true, 'pb_phone_mandatory' => false, 'pb_phone_label' => 'Tel. číslo',
                'pb_email_enabled' => true, 'pb_email_mandatory' => true, 'pb_email_label' => 'E-mail',
                'pb_address_enabled' => true, 'pb_address_mandatory' => true, 'pb_address_label' => 'Adresa (název ulice, číslo popisné, část Prahy 8)',
                'pb_signatures_enabled' => true, 'pb_signatures_mandatory' => true, 'pb_signatures_label' => 'Podpisový arch (povinná příloha)',
                'pb_age_conf_enabled' => true, 'pb_age_conf_mandatory' => true, 'pb_age_conf_label' => 'Prohlašuji, že jsem starší 15 let',
                'pb_agreement_enabled' => true, 'pb_agreement_mandatory' => true, 'pb_agreement_label' => 'Souhlasím s <a href="%s" target="_blank" title="Přejít na stránku s podmínkami">podmínkami použití</a>',
                'pb_completed_enabled' => true, 'pb_completed_mandatory' => false, 'pb_completed_label' => 'Popis projektu je úplný a chci ho poslat k vyhodnocení',
            );
        }

        $base_meta_fields = array(
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
                'help'          => 'Povolené typy příloh: gif, png, jpg, jpeg, pdf'
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
                'help'          => 'Povolené typy příloh: gif, png, jpg, jpeg, pdf, doc, docx, xls, xlsx',
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
                'help'          => 'Povolené typy příloh: gif, png, jpg, jpeg, pdf, doc, docx, xls, xlsx',
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
                'help'          => 'Povolené typy příloh: gif, png, jpg, jpeg, pdf, doc, docx, xls, xlsx',
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
                'title'     => "completed", // This is used as the key for settings
                'mandatory' => false,
                'help'      => 'Pokud necháte nezaškrtnuté, můžete po uložení dat popis projektu doplnit',
            ),
        );

        $this->meta_fields = array();
        foreach ($base_meta_fields as $field_key => $field_definition) {
            $setting_enabled_key = "pb_{$field_key}_enabled";
            $setting_label_key = "pb_{$field_key}_label";
            $setting_mandatory_key = "pb_{$field_key}_mandatory";

            // Check if field is enabled
            if (isset($pb_form_settings[$setting_enabled_key]) && !$pb_form_settings[$setting_enabled_key]) {
                continue; // Skip this field
            }

            // Update label if set in settings
            if (isset($pb_form_settings[$setting_label_key]) && !empty($pb_form_settings[$setting_label_key])) {
                if ($field_key === 'agreement') {
                    $terms_url = isset($pb_form_settings['pb_terms_url']) ? esc_url($pb_form_settings['pb_terms_url']) : '#';
                    $field_definition['label'] = sprintf($pb_form_settings[$setting_label_key], $terms_url);
                } else {
                    $field_definition['label'] = $pb_form_settings[$setting_label_key];
                }
            } elseif ($field_key === 'agreement') { // Ensure default agreement label uses default terms URL if not customized
                 $terms_url = isset($pb_form_settings['pb_terms_url']) ? esc_url($pb_form_settings['pb_terms_url']) : site_url("podminky-pouziti-a-ochrana-osobnich-udaju/");
                 $field_definition['label'] = sprintf($field_definition['label'], $terms_url);
            }


            // Update mandatory status if set in settings (except for 'agreement' which is always mandatory)
            if ($field_key !== 'agreement' && isset($pb_form_settings[$setting_mandatory_key])) {
                $field_definition['mandatory'] = (bool)$pb_form_settings[$setting_mandatory_key];
            }

            $this->meta_fields[$field_key] = $field_definition;
        }
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
		// Check capabilities
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}
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
					case 'textarea':
						$_POST[ $meta_field['id'] ] = sanitize_textarea_field( $_POST[ $meta_field['id'] ] );
						break;
					case 'media': // Assuming media stores a URL
						$_POST[ $meta_field['id'] ] = esc_url_raw( $_POST[ $meta_field['id'] ] );
						break;
					case 'tel':
						// sanitize_text_field is okay, or use a regex for more specific validation if needed
						$_POST[ $meta_field['id'] ] = sanitize_text_field( $_POST[ $meta_field['id'] ] );
						break;
 					case 'text':
						// Special handling for numeric text fields
						if ( $meta_field['id'] === 'pb_project_naklady_celkem' ) {
							$_POST[ $meta_field['id'] ] = intval( sanitize_text_field( $_POST[ $meta_field['id'] ] ) );
						} else {
							$_POST[ $meta_field['id'] ] = sanitize_text_field( $_POST[ $meta_field['id'] ] );
						}
 						break;
					default:
						// For any other types, default to sanitize_text_field if it's a string
						if ( is_string( $_POST[ $meta_field['id'] ] ) ) {
							$_POST[ $meta_field['id'] ] = sanitize_text_field( $_POST[ $meta_field['id'] ] );
						}
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

function pb_template_part_new_project( $latlng = array(), $data = null)
{
    global $pb_submit_btn_text;

    $pb_project_meta_fields = new informacekprojektuMetabox();
    $fields = $pb_project_meta_fields->get_fields();

    ob_start();
    pb_render_field( 4,  $fields['actions'],    pb_render_field_get_value( $fields['actions']['id'], $data ) );
    pb_render_field( 5,  $fields['goals'],      pb_render_field_get_value( $fields['goals']['id'], $data ));
    pb_render_field( 6,  $fields['profits'],    pb_render_field_get_value( $fields['profits']['id'], $data ) );
    pb_new_project_template_part_map( '7. ' );
    pb_new_project_template_part_link_katastr( $latlng);
    pb_render_field( 8,  $fields['parcel'],     pb_render_field_get_value( $fields['parcel']['id'], $data ) );
    pb_new_project_template_part_image('9. ', $issue_image = (! empty($data['issue_image']) ? esc_url($data['issue_image']) : ''));
    pb_render_field( 10, $fields['map'],        pb_render_field_get_value( $fields['map']['id'], $data ) );
    pb_render_field( 11, $fields['cost'],       pb_render_field_get_value( $fields['cost']['id'], $data ) );
    echo '<div class="imc-row">';
    pb_render_field( 12, $fields['budget_total'],    pb_render_field_get_value( $fields['budget_total']['id'], $data ) );
    pb_render_field( 13, $fields['budget_increase'], pb_render_field_get_value( $fields['budget_increase']['id'], $data ) );
    echo '</div>';
    pb_render_field( 14, $fields['attach1'],    pb_render_field_get_value( $fields['attach1']['id'], $data ) );
    pb_render_field( 15, $fields['attach2'],    pb_render_field_get_value( $fields['attach2']['id'], $data ) );
    pb_render_field( 16, $fields['attach3'],    pb_render_field_get_value( $fields['attach3']['id'], $data ) );
    echo '<div class="imc-row">';
    pb_render_field( 17, $fields['name'],       pb_render_field_get_value( $fields['name']['id'], $data ) );
    pb_render_field( 18, $fields['phone'],      pb_render_field_get_value( $fields['phone']['id'], $data ) );
    pb_render_field( 19, $fields['email'],      pb_render_field_get_value( $fields['email']['id'], $data ) );
    echo '</div>';
    pb_render_field( 20, $fields['address'],    pb_render_field_get_value( $fields['address']['id'], $data ) );
    pb_render_field( 21, $fields['signatures'], pb_render_field_get_value( $fields['signatures']['id'], $data ) );
    pb_render_field( 22, $fields['age_conf'],   pb_render_field_get_value( $fields['age_conf']['id'], $data ) );
    pb_render_field( 23, $fields['agreement'],  pb_render_field_get_value( $fields['agreement']['id'], $data ) );
    pb_render_field( 24, $fields['completed'],  pb_render_field_get_value( $fields['completed']['id'], $data ) );

    ?>

    <script>
         "use strict";
        function pbProjectAddFile(e){
            jQuery("#"+e.id+"Name").val(e.files[0].name);
        };
        function imcDeleteAttachedFile( id ){
            document.getElementById(id).value = "";

            jQuery("#"+id+"Name").html("");
            jQuery("#"+id+"Name").val("");
            jQuery("#"+id+"Link").hide();
        };
        document.getElementById("pb_link_to_katastr").onclick = function() {
                var lt = document.getElementById('imcLatValue').value;
                var url = "https://www.ikatastr.cz/ikatastr.htm#zoom=19&lat="+
                    document.getElementById('imcLatValue').value+"&lon="+
                    document.getElementById('imcLngValue').value+"&layers_3=000B00FFTFFT&ilat="+document.getElementById('imcLatValue').value+"&ilon="+
                    document.getElementById('imcLngValue').value;
                var win = window.open( url, '_blank');
                win.focus();
                return false;
            };
        jQuery("#pb_project_edit_completed").change( function(){
            if (jQuery("#pb_project_edit_completed").prop("checked")) {
                jQuery(".pb-project-submit-btn").val( "<?PHP echo $pb_submit_btn_text["completed_on"]?>");
            } else {
                jQuery(".pb-project-submit-btn").val( "<?PHP echo $pb_submit_btn_text["completed_off"]?>");
            }
        });
    </script>
    <style>
        .pb_tooltip {
            position: relative;
            display: inline-block;
        }
        .pb_tooltip .pb_tooltip_text {
            visibility: hidden;
            width: 250px;
            background-color: #555;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 5px 0;
            position: absolute;
            z-index: 1;
            bottom: 125%;
            left: 50%;
            margin-left: -60px;
            opacity: 0;
            transition: opacity 0.3s;
        }
        .pb_tooltip:hover .pb_tooltip_text {
            visibility: visible;
            opacity: 1;
        }
    </style>
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

function pb_render_field( $order = '' , $field, $value = '' )
{
    if (! empty( $order )) {
        $order = $order . ". ";
    }
    if (! empty( $field['help'])) {
        $help = $field['help'];
    } else {
        $help = '';
    }


    switch ( $field['type'] ) {
        case 'media':
            pb_render_file_attachment( $order, $field, $value, $help);
            break;
        case 'checkbox':
            pb_render_checkbox( $order, $field, $value, $help);
            break;
        case 'textarea':
            pb_render_textarea( $order, $field, $value, $help);
            break;
        case 'email':
            pb_render_text( $order, $field, $value, $help);
            break;
        default:
            pb_render_text( $order, $field, $value, $help);
    }
}

function pb_render_textarea( $order, $input = null, $value = '', $help = '' )
{
    if ( !empty( $input['mandatory']) && $input['mandatory'] ) {
        $mandatory = pb_render_mandatory( $input['mandatory']) ;
    } else {
        $mandatory = pb_render_mandatory( false);
    }
    if ( ! empty($input['rows'])) {
        $rows = $input['rows'];
    } else {
        $rows = 3;
    }

    $output = '<div class="imc-row">
        <h3 class="u-pull-left imc-SectionTitleTextStyle">%s%s %s'.pb_project_tooltip( $help ).'</h3>
        <textarea placeholder="%s" rows="%d"
             class="imc-InputStyle" title="%s" name="%s"
             id="%s">%s</textarea>
        <label id="%sLabel" class="imc-ReportFormErrorLabelStyle imc-TextColorPrimary"></label>
        </div>';
    if ( empty( $input ) ) {
        return $output;
    } else {
        printf( $output,
            $order,
            $input['label'],
            $mandatory,
            $input['placeholder'],
            $rows,
            $input['title'],
            $input['id'],
            $input['id'],
            $value,
            $input['id']
        );
    }
}
function pb_render_text( $order, $input = null, $value = '', $help = '' )
{
    if ( !empty( $input['mandatory']) && $input['mandatory'] ) {
        $mandatory = pb_render_mandatory( $input['mandatory']) ;
    } else {
        $mandatory = pb_render_mandatory( false);
    }
    $options = '';
    if ( ! empty($input['options'])) {
        $options = " ".$input['options'];
    }

    if ( ! empty($input['columns'])) {
        $columns = $input['columns'];
    } else {
        $columns = '';
    }

    $output = '<h3 class="imc-SectionTitleTextStyle">%s%s %s'.pb_project_tooltip( $help ).'</h3><input type="%s" %s autocomplete="off"
        data-tip="zde kliknete" placeholder="%s" name="%s" id="%s" class="imc-InputStyle" value="%s" ></input>
        <label id="%sLabel" class="imc-ReportFormErrorLabelStyle imc-TextColorPrimary"></label>';
    if ( ! empty($columns) ) {
        $output = '<div class="imc-grid-'.$columns.' imc-columns">' . $output . '</div>';
    } else {
        $output = '<div class="imc-row">' . $output . '</div>';
    }

    if ( empty( $input ) ) {
        return $output;
    } else {
        printf( $output,
            $order,
            $input['label'],
            $mandatory,
            $input['type'],
            $options,
            $input['placeholder'],
            $input['id'],
            $input['id'],
            $value,
            $input['id']
        );
    }
}
function pb_render_file_attachment( $order, $input, $value = '', $help = '')
{
    if ( !empty( $input['mandatory']) && $input['mandatory'] ) {
        $mandatory = pb_render_mandatory( $input['mandatory']) ;
    } else {
        $mandatory = pb_render_mandatory( false);
    }
    $options = ' readonly="readonly" ';
    if ($value) {
        $filename = basename($value);
    } else {
        $filename = $value;
    }

    $link = pb_render_file_link($value, $input['id']);
    // <span id="%sName" class="imc-ReportGenericLabelStyle imc-TextColorSecondary">'. __('Vyberte soubor','participace-projekty') .'</span>
    $output = '<div class="imc-row" id="pbProjectSection%s">
                <div class="imc-row">
                    <h3 class="u-pull-left imc-SectionTitleTextStyle">%s%s %s'.pb_project_tooltip( $help ).'</h3>
                </div>
                <div class="imc-row">
                    <div class="imc-grid-5 imc-columns">
                        <input %s autocomplete="off"
                            placeholder="Vyberte soubor" type="text" name="%sName" id="%sName" class="imc-InputStyle" value="%s"/>
                        <label id="%sNameLabel" class="imc-ReportFormErrorLabelStyle imc-TextColorPrimary"></label>
                    </div>

                    <div class="imc-grid-6 imc-columns">
                    <div class="u-cf">
                        <div class="imc-row">%s
                            <input autocomplete="off" class="imc-ReportAddImgInputStyle" id="%s" type="file" name="%s" onchange="pbProjectAddFile(this)" />
                            <label for="%s">
                                <i class="material-icons md-24 imc-AlignIconToButton">%s</i>%s
                            </label>
                            <button type="button" class="imc-button" onclick="imcDeleteAttachedFile(\'%s\');">
                                <i class="material-icons md-24 imc-AlignIconToButton">delete</i>%s</button>
                        </div>
                    </div>
                    </div>
                </div></div>';
    if ( empty( $input ) ) {
        return $output;
    } else {
        printf( $output,
            $input['title'],
            $order,
            $input['label'],
            $mandatory,
            $options,
            $input['id'],
            $input['id'],
            $filename,
            $input['id'],
            $link,
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

function pb_render_file_link($url, $id )
{
    if (! empty($url)) {
        return '<a id="'.$id.'Link" href="'.$url.'" target="_blank" data-toggle="tooltip" title="Zobrazit přílohu" class="u-pull-right
            imc-SingleHeaderLinkStyle"><i class="material-icons md-36 imc-SingleHeaderIconStyle">file_download</i></a>';
                    // <i class="material-icons md-36 imc-SingleHeaderIconStyle">open_in_browser</i></a>';
    } else {
        return '<a hidden id="'.$id.'Link" data-toggle="tooltip" title="Chybí příloha" class="u-pull-right
            imc-SingleHeaderLinkStyle"><i class="material-icons md-36 imc-SingleHeaderIconStyle">file_download</i></a>';
    }
}
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

function pb_render_checkbox( $order, $input, $value = '', $help = '')
{
    $checked = '';
    $required = '';
    $mandatory = '';
    if ( ! empty( $value) ){
        if ( $value ) {
            $checked = 'checked';
        }
    } else {
        if (! empty($input['default']) && ( $input['default'] != 'no') && ( $input['default'] != '0') ) {
            $checked = 'checked';
        }
    }
    if ( ! empty( $input['mandatory'])) {
        $mandatory = pb_render_mandatory( $input['mandatory']) ;
        if ($input['mandatory']) {
            $required = "required";
            $required = "";
        }
    }

    if ( ! empty($input['columns'])) {
        $columns = $input['columns'];
    } else {
        $columns = '';
    }

    // if (! empty( $input['text_after_checkbox']) && function_exists($input['text_after_checkbox'])) {
    //     $text_after = $input['text_after_checkbox']();
    // } else {
    //     $text_after = '';
    // }

    $output = '<h3 class="imc-SectionTitleTextStyle" style="display:inline-block;"><label id="%sName" for="%s">%s%s</label>'. pb_project_tooltip($help) .'
        </h3><input type="checkbox"  %s %s name="%s" id="%s" class="imc-InputStyle" value="1"
        style="width:20px; height:20px; display:inline-block;margin-left:10px"/>
        <label id="%sLabel" class="imc-ReportFormErrorLabelStyle imc-TextColorPrimary"></label>';

    if ( ! empty($columns) ) {
        $output = '<div class="imc-grid-'.$columns.' imc-columns">' . $output . '</div>';
    } else {
        $output = '<div class="imc-row">' . $output . '</div>';
    }

    if ( empty( $input ) ) {
        return $output;
    } else {
        printf( $output,
            $input['id'],
            $input['id'],
            $order,
            $input['label'],
            $checked,
            $required,
            $input['id'],
            $input['id'],
            $input['id']
        );
    }
}

function pb_render_mandatory( $mandatory = false)
{
    if ( $mandatory ) {
        return '';
    } else {
        return ' ( ' . __('volitelné','participace-projekty') .' )';
        return '<span class="imc-OptionalTextLabelStyle">" " (' . __('optional','participace-projekty') .')></span>';
    }
}

function pb_new_project_meta_save_prep( $data, $update = false )
{
    $output = array(
		'imc_lat'		=> esc_attr(sanitize_text_field($data['imcLatValue'])),
		'imc_lng'		=> esc_attr(sanitize_text_field($data['imcLngValue'])),
		'imc_address'	=> esc_attr(sanitize_text_field($data['postAddress'])),
		'pb_project_navrhovatel_jmeno'   => esc_attr(sanitize_text_field($data['pb_project_navrhovatel_jmeno'])),
		'pb_project_navrhovatel_adresa'  => esc_attr(sanitize_text_field($data['pb_project_navrhovatel_adresa'])),
		'pb_project_navrhovatel_telefon' => esc_attr(sanitize_text_field($data['pb_project_navrhovatel_telefon'])),
        'pb_project_parcely'             => esc_attr(sanitize_textarea_field($data['pb_project_parcely'])),
        'pb_project_prohlaseni_veku'     => (! empty($data['pb_project_prohlaseni_veku'])) ? esc_attr(sanitize_text_field($data['pb_project_prohlaseni_veku'])) : '0',
        'pb_project_podminky_souhlas'    => (! empty($data['pb_project_podminky_souhlas'])) ? esc_attr(sanitize_text_field($data['pb_project_podminky_souhlas'])) : '0',
		'pb_project_navrhovatel_email'   => esc_attr(sanitize_email($data['pb_project_navrhovatel_email'])),
        'pb_project_cile'                => esc_attr(sanitize_textarea_field($data['pb_project_cile'])),
        'pb_project_akce'                => esc_attr(sanitize_textarea_field($data['pb_project_akce'])),
        'pb_project_prospech'            => esc_attr(sanitize_textarea_field($data['pb_project_prospech'])),
        'pb_project_naklady_celkem'      => esc_attr(sanitize_textarea_field($data['pb_project_naklady_celkem'])),
        'pb_project_naklady_navyseni'    => (! empty($data['pb_project_naklady_navyseni'])) ? esc_attr(sanitize_textarea_field($data['pb_project_naklady_navyseni'])) : '0',
		);
    if ( ! $update ) {
        $output['imc_likes'] = '0';
        $output['modality'] = '0';
    }
    return $output;
}
function pb_project_check_file_type( $file, $attach_type)
{
    switch ($attach_type) {
        case 'featured_image':
            $allowed_file_type = FILE_TYPES_IMAGE;
            break;

        case 'pb_project_mapa':;
        case 'pb_project_podporovatele':
            $allowed_file_type = FILE_TYPES_IMAGE.FILE_TYPES_SCAN;
            break;

        default:
            $allowed_file_type = FILE_TYPES_IMAGE.FILE_TYPES_SCAN.FILE_TYPES_DOCS;
            break;
    }
    $type = wp_check_filetype(basename($file)) ;
    return  strpos( $allowed_file_type, $type['ext']);
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

function pb_new_project_insert_attachment_1 ($file, $post_id, $attachment_type = '' )
{
    if (( $file['error'] == '0') && (! empty($post_id)) && (! empty($attachment_type)) &&
            ( pb_project_check_file_type($file['name'],$attachment_type))) {
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

function pb_new_project_update_attachments( $post_id, $files, $data)
{
    $list = array(
        'pb_project_podporovatele',
        'pb_project_mapa',
        'pb_project_naklady',
        'pb_project_dokumentace1',
        'pb_project_dokumentace2',
        'pb_project_dokumentace3',
    );
    foreach ($list as $key) {
        pb_new_project_update_attachment_1($files[ $key ], $post_id, $key, $data[ $key.'Name']);
    }
}

function pb_new_project_update_attachment_1 ($file, $post_id, $attachment_type, $meta_value )
{
    if (( $file['error'] == '0') && (! empty($post_id)) && (! empty($attachment_type))  &&
            ( pb_project_check_file_type($file['name'],$attachment_type)) ) {
        $attachment_id = imc_upload_img( $file, $post_id, $post_id . '-' . $attachment_type, null);
        if ( $attachment_id) {
            $url = wp_get_attachment_url( $attachment_id);
            update_post_meta( $post_id, $attachment_type, $url);
        }
        return $attachment_id;
    } elseif ( empty( $meta_value) ) {
        delete_post_meta( $post_id, $attachment_type );
        return true;
    } else {
        return false;
    }
}
function pb_new_project_update_postmeta($post_id, $data)
{
    foreach ($data as $key => $value) {
        update_post_meta($post_id, $key, $value);
    }
}
function pb_new_project_template_part_map( $order = '')
{
    $output = '<input required name="postAddress" placeholder="%s" id="imcAddress" class="u-pull-left imc-InputStyle"/>';
    $output = '
        <div class="imc-row-no-margin">
            <h3 class="imc-SectionTitleTextStyle">%s%s</h3>
            <button class="imc-button u-pull-right" type="button" onclick="imcFindAddress(\'imcAddress\', true)">
                <i class="material-icons md-24 imc-AlignIconToButton">search</i>%s</button>
            <div style="padding-right: .5em;" class="imc-OverflowHidden">
                <input name="postAddress" placeholder="%s" id="imcAddress" class="u-pull-left imc-InputStyle"/>
            </div>
            <input title="lat" type="hidden" id="imcLatValue" name="imcLatValue"/>
            <input title="lng" type="hidden" id="imcLngValue" name="imcLngValue"/>
        </div>
        <div class="imc-row">
            <div id="imcReportIssueMapCanvas" class="u-full-width imc-ReportIssueMapCanvasStyle"></div>
        </div>
    ';
    printf( $output,
        $order,
        __('Address','participace-projekty'),
        __('Locate', 'participace-projekty'),
        __('Add an address','participace-projekty')
    );
}
function pb_new_project_template_part_image( $order = '', $issue_image = '')
{
    $output = '
        <div class="imc-row" id="imcImageSection">
            <h3 class="u-pull-left imc-SectionTitleTextStyle">%s%s' . pb_render_mandatory(false) .'</h3>
            <div class="u-cf">
                <input autocomplete="off" class="imc-ReportAddImgInputStyle" id="imcReportAddImgInput" type="file" name="featured_image" />
                <label for="imcReportAddImgInput">
                    <i class="material-icons md-24 imc-AlignIconToButton">photo</i>%s
                </label>
                <button type="button" class="imc-button" onclick="imcDeleteAttachedImage(\'imcReportAddImgInput\');">
                    <i class="material-icons md-24 imc-AlignIconToButton">delete</i>%s</button>
            </div>
            <span id="imcNoPhotoAttachedLabel" class="imc-ReportGenericLabelStyle imc-TextColorSecondary">%s</span>
            <span style="display: none;" id="imcLargePhotoAttachedLabel" class="imc-ReportGenericLabelStyle imc-TextColorSecondary">%s</span>
            <span style="display: none;" id="imcPhotoAttachedLabel" class="imc-ReportGenericLabelStyle imc-TextColorSecondary">%s</span>
            <span class="imc-ReportGenericLabelStyle imc-TextColorPrimary" id="imcPhotoAttachedFilename"></span>
            <br>
            <br>
        </div>
        <input title="orientation" type="hidden" id="imcPhotoOri" name="imcPhotoOri"/>
        ';
    if ( $issue_image) {
        $output .= '<img id="imcPreviousImg" class="u-cf" style="max-height: 200px;" src="%s">';
    } else {
        $output .= "%s";
    }

    printf( $output,
        $order,
        __('Photo','participace-projekty'),
        __('Add photo','participace-projekty'),
        __('Delete Photo', 'participace-projekty'),
        __('No photo attached','participace-projekty'),
        __('Photo size must be smaller in size, please resize it or select a smaller one!','participace-projekty'),
        __('A photo has been selected:','participace-projekty'),
        $issue_image
    );
}

function pb_new_project_template_part_link_katastr($latlng)
{
    if (! empty( $latlng ) ) {
        $url = "https://www.ikatastr.cz/ikatastr.htm#zoom=19&lat=".$latlng['lat']."&lon=".$latlng['lon']."&layers_3=000B00FFTFFT&ilat=".$latlng['lat']."&lon=".$latlng['lon'];
    } else {
        $url = "https://www.ikatastr.cz/ikatastr.htm#zoom=19&lat=50.10766&lon=14.47145&layers_3=000B00FFTFFT";
    }
    $output = '<div class="imc-row" ><span>Kliknutím na tento </span>
        <a id="pb_link_to_katastr" href="#" data-toggle="tooltip" title="Přejít na stránku s katastrální mapou"
            class=""><span>odkaz</span></a><span> zobrazíte katastrální mapu na vámi označeném místě.
        Ve vyskakovacím okně (musíte mít povoleno ve vašem prohlížeči) získáte informace k vybranému pozemku. Nalezněte všechna katastrální čísla týkajících se návrhu, kliknutím do mapy ověřte,
        zda jsou všechny dotčené pozemky ve správě HMP nebo MČ a tedy splňujete podmínky pravidel participativního rozpočtu. Seznam všech dotčených pozemků uveďte do pole níže (jedna položka na jeden řádek).</span>
        </div>';
    printf($output);
}

function pb_new_project_mandatory_fields_js_validation()
{
    $pb_form_settings = get_option('pb_form_settings');
    if (false === $pb_form_settings) {
        // Fallback to some default if settings not saved, though this should ideally not happen
        // if load_settings in ImcSettingsPage works correctly with defaults.
        $pb_form_settings = array(
            'pb_terms_url' => site_url("podminky-pouziti-a-ochrana-osobnich-udaju/"),
            // ... other defaults ...
        );
    }

    $validation_rules = array();

    // Standard fields always required by the form logic itself
    $validation_rules[] = array('name' => 'postTitle', 'display' => 'Název', 'rules' => 'required|min_length[5]|max_length[255]');
    $validation_rules[] = array('name' => 'my_custom_taxonomy', 'display' => 'Kategorie', 'rules' => 'required');
    $validation_rules[] = array('name' => 'postContent', 'display' => 'Popis', 'rules' => 'required');
    // Assuming postAddress (map related) is fundamental and always there if map section is enabled
    // This might need its own enabled/mandatory setting if it's to be fully configurable.
    // For now, keeping its existing logic tied to pb_project_js_validate_required
     $validation_rules[] = array('name' => 'postAddress', 'display' => 'Adresa', 'rules' => 'required', 'depends' => 'pb_project_js_validate_required');


    // Dynamically add rules based on settings
    $configurable_field_map = array(
        'actions' => array('id' => 'pb_project_akce', 'display_name' => '\"Co by se mělo udělat\"', 'rules' => 'required', 'depends' => 'pb_project_js_validate_required'),
        'goals' => array('id' => 'pb_project_cile', 'display_name' => '\"Proč je projekt důležitý\"', 'rules' => 'required', 'depends' => 'pb_project_js_validate_required'),
        'profits' => array('id' => 'pb_project_prospech', 'display_name' => '\"Kdo bude mít z projektu prospěch\"', 'rules' => 'required', 'depends' => 'pb_project_js_validate_required'),
        'parcel' => array('id' => 'pb_project_parcely', 'display_name' => 'Parcelní číslo', 'rules' => 'required', 'depends' => 'pb_project_js_validate_required'),
        // featured_image is handled separately below
        'map' => array('id_name' => 'pb_project_mapaName', 'id_file' => 'pb_project_mapa', 'display_name' => 'Mapa (situační nákres)', 'rules_name' => 'required', 'rules_file' => 'is_file_type[gif,GIF,png,PNG,jpg,JPG,jpeg,JPEG,pdf,PDF]', 'depends' => 'pb_project_js_validate_required'),
        'cost' => array('id_name' => 'pb_project_nakladyName', 'id_file' => 'pb_project_naklady', 'display_name' => 'Předpokládané náklady', 'rules_name' => 'required', 'rules_file' => 'is_file_type[gif,GIF,png,PNG,jpg,JPG,jpeg,JPEG,pdf,PDF,doc,DOC,xls,XLS]', 'depends' => 'pb_project_js_validate_required'),
        'budget_total' => array('id' => 'pb_project_naklady_celkem', 'display_name' => 'Celkové náklady', 'rules' => 'required|integer|greater_than[99999]|less_than[2000001]', 'depends' => 'pb_project_js_validate_required'),
        'budget_increase' => array('id' => 'pb_project_naklady_navyseni', 'display_name' => 'Navýšení rozpočtu', 'rules' => 'required', 'depends' => 'pb_project_js_validate_required'), // Checkbox, always 'required' if enabled by 'depends'
        'name' => array('id' => 'pb_project_navrhovatel_jmeno', 'display_name' => 'Jméno navrhovatele', 'rules' => 'required', 'depends' => 'pb_project_js_validate_required'),
        'phone' => array('id' => 'pb_project_navrhovatel_telefon', 'display_name' => 'Telefonický kontakt', 'rules' => 'valid_phone'), // Not mandatory by default
        'email' => array('id' => 'pb_project_navrhovatel_email', 'display_name' => 'email', 'rules' => 'required|valid_email', 'depends' => 'pb_project_js_validate_required'),
        'address' => array('id' => 'pb_project_navrhovatel_adresa', 'display_name' => 'Adresa navrhovatele', 'rules' => 'required', 'depends' => 'pb_project_js_validate_required'),
        'signatures' => array('id_name' => 'pb_project_podporovateleName', 'id_file' => 'pb_project_podporovatele', 'display_name' => 'Podpisový arch', 'rules_name' => 'required', 'rules_file' => 'is_file_type[gif,GIF,png,PNG,jpg,JPG,jpeg,JPEG,pdf,PDF]', 'depends' => 'pb_project_js_validate_required'),
        'age_conf' => array('id' => 'pb_project_prohlaseni_veku', 'display_name' => 'Prohlašení věku', 'rules' => 'required', 'depends' => 'pb_project_js_validate_required'),
        'agreement' => array('id' => 'pb_project_podminky_souhlas', 'display_name' => 'Souhlas s podmínkami', 'rules' => 'required', 'depends' => 'pb_project_js_validate_required'),
        // 'completed' checkbox doesn't typically have validation rules applied in this way.
    );

    // Featured image validation (not part of configurable_field_map directly as it's standard)
    $validation_rules[] = array('name' => 'featured_image', 'display' => 'Fotografie', 'rules' => 'is_file_type[gif,GIF,png,PNG,jpg,JPG,jpeg,JPEG]');


    foreach ($configurable_field_map as $field_key => $field_attrs) {
        $enabled_key = "pb_{$field_key}_enabled";
        $mandatory_key = "pb_{$field_key}_mandatory";

        if (isset($pb_form_settings[$enabled_key]) && $pb_form_settings[$enabled_key]) {
            $is_mandatory = isset($pb_form_settings[$mandatory_key]) && $pb_form_settings[$mandatory_key];

            if (isset($field_attrs['id_name'])) { // For file fields that have a Name input and a File input
                if ($is_mandatory) {
                    $validation_rules[] = array('name' => $field_attrs['id_name'], 'display' => $field_attrs['display_name'], 'rules' => $field_attrs['rules_name'], 'depends' => $field_attrs['depends']);
                }
                $validation_rules[] = array('name' => $field_attrs['id_file'], 'display' => $field_attrs['display_name'] . ' (soubor)', 'rules' => $field_attrs['rules_file']);
            } else { // For regular fields
                $current_rules = '';
                if ($is_mandatory) {
                    // If rules already exist, prepend 'required|', otherwise just 'required'
                    $current_rules = isset($field_attrs['rules']) && strpos($field_attrs['rules'], 'required') === false ? 'required|' . $field_attrs['rules'] : 'required';
                     if (isset($field_attrs['rules']) && strpos($field_attrs['rules'], 'required') !== false) { // Already contains required
                        $current_rules = $field_attrs['rules'];
                    } elseif (isset($field_attrs['rules'])) {
                        $current_rules = 'required|' . $field_attrs['rules'];
                    } else {
                        $current_rules = 'required';
                    }
                } else {
                     // Remove 'required' or 'required|' if it exists for a non-mandatory field
                    if (isset($field_attrs['rules'])) {
                        $current_rules = str_replace('required|', '', $field_attrs['rules']);
                        $current_rules = str_replace('required', '', $current_rules);
                         if (empty($current_rules)) continue; // Skip if no other rules left
                    } else {
                        continue; // Skip if no rules and not mandatory
                    }
                }
                 $rule_entry = array('name' => $field_attrs['id'], 'display' => $field_attrs['display_name'], 'rules' => $current_rules);
                if (!empty($field_attrs['depends'])) {
                    $rule_entry['depends'] = $field_attrs['depends'];
                }
                $validation_rules[] = $rule_entry;
            }
        }
    }

    return json_encode($validation_rules);
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
function pb_project_tooltip( $text = "")
{
    if (! empty( $text)) {
        return '<span class="pb_tooltip"><i class="material-icons md-24" style="margin-left:2px;">help_outline</i>
        <span class="pb_tooltip_text" >' . $text . '</span></span>' ;
    } else {
        return '';
    }
}
function pb_render_condition_link()
{
    // nuni asi neni potreba a jde to smazat
    $page_conditions = get_page_by_path( 'podminky-pouziti-a-ochrana-osobnich-udaju');
    $page_link = get_page_link( $page_conditions->ID);
    $output = '<h3 class="imc-SectionTitleTextStyle" style="display:inline-block; margin-left:20px;">S podmínkami se můžete seznámit
        <a id="pb_link_to_conditions" target="_blank" href="'.$page_link.'" title="Přejít na stránku s podmínkami">zde</a></h3>';
    return $output;
    // return '<span style="display:inline-block; margin-left:20px;">S podmínkami se můžete seznámit </span>';
}

function pb_template_part_single_project( $data = null)
{
    $pb_project_meta_fields = new informacekprojektuMetabox();
    $fields = $pb_project_meta_fields->get_fields();

    ob_start();
    pb_render_field_single_project( '',  $fields['goals'],      pb_render_field_get_value( $fields['goals']['id'], $data ));
    pb_render_field_single_project( '',  $fields['actions'],    pb_render_field_get_value( $fields['actions']['id'], $data ) );
    pb_render_field_single_project( '',  $fields['profits'],    pb_render_field_get_value( $fields['profits']['id'], $data ) );
    pb_render_field_single_project( '',  $fields['address'],    pb_render_field_get_value( 'imc_address', $data ) );
    pb_render_field_single_project( '',  $fields['parcel'],     pb_render_field_get_value( $fields['parcel']['id'], $data ) );
    pb_render_field_single_project( '',  $fields['map'],        pb_render_field_get_value( $fields['map']['id'], $data ) );
    pb_render_field_single_project( '', $fields['cost'],       pb_render_field_get_value( $fields['cost']['id'], $data ) );
    pb_render_field_single_project( '', $fields['budget_total'],    pb_render_field_get_value( $fields['budget_total']['id'], $data ) );
    pb_render_field_single_project( '', $fields['attach1'],    pb_render_field_get_value( $fields['attach1']['id'], $data ) );
    pb_render_field_single_project( '', $fields['attach2'],    pb_render_field_get_value( $fields['attach2']['id'], $data ) );
    pb_render_field_single_project( '', $fields['attach3'],    pb_render_field_get_value( $fields['attach3']['id'], $data ) );
    pb_render_field_single_project( '', $fields['name'],       pb_render_field_get_value( $fields['name']['id'], $data ) );
    return ob_get_clean();
}
function pb_render_field_single_project( $order = '' , $field, $value = '' )
{
    if ( empty( $value)) {
        return '';
    }
    if (! empty( $order )) {
        $order = $order . ". ";
    }

    switch ( $field['type'] ) {
        case 'media':
            pb_render_single_project_file_field( $order, $field['label'], $value);
            break;
        case 'checkbox':
            pb_render_single_project_text_field( $order, $field['label'], $value);
            break;
        case 'textarea':
            pb_render_single_project_text_field( $order, $field['label'], $value);
            break;
        default:
            pb_render_single_project_text_field( $order, $field['label'], $value);
    }
}

function pb_render_single_project_text_field( $order = '', $label = '', $value = '')
{
    $output = '<div class="imc-row">
        <h3 class="imc-SectionTitleTextStyle">%s%s</h3>
        <div class="imc-SingleDescriptionStyle imc-TextColorSecondary imc-JustifyText">%s</div>
    </div>';
    printf( $output ,
        $order,
        $label,
        $value);
}

function pb_render_single_project_file_field( $order = '', $label = '', $value = '')
{
    $output = '<div class="imc-row">
        <h3 class="imc-SectionTitleTextStyle">%s%s</h3>
        <div><p>Zobrazit přílohu<a href="%s" target="_blank" data-toggle="tooltip"
            title="Zobrazit přílohu" >
                <i class="material-icons md-28 imc-SingleHeaderIconStyle" >file_download</i></a></p>
        </div></div>';
    printf( $output ,
        $order,
        $label,
        $value);
}
function pb_change_project_status_log( $new_step_term, $post_id, $description = 'Změna stavu')
{
    global $wpdb;

    $current_step_name = $new_step_term->name;
    $transition = __( 'Status changed: ', 'participace-projekty' ) . $new_step_term->name;
    $tagid = intval($new_step_term->term_id, 10);
    $theColor = 'tax_imcstatus_color_' . $tagid;
    $term_data = get_option($theColor);
    $currentStatusColor = $term_data;
    $timeline_label = $new_step_term->name;
    $theUser =  get_current_user_id();
    $currentlang = get_bloginfo('language');

    $imc_logs_table_name = $wpdb->prefix . 'imc_logs';

    $wpdb->insert(
        $imc_logs_table_name,
        array(
            'issueid' => $post_id,
            'stepid' => $tagid,
            'transition_title' => $transition,
            'timeline_title' => $timeline_label,
            'theColor' => $currentStatusColor,
            'description' => $description,
            'action' => 'step',
            'state' => 1,
            'created' => gmdate("Y-m-d H:i:s",time()),
            'created_by' => $theUser,
            'language' => $currentlang,
        )
    );

    //fires mail notification
    imcplus_mailnotify_4imcstatuschange($transition, $post_id, $theUser);
}
function pb_change_project_status_log_action( $post_id, $terms, $new_term_ids, $taxo, $append, $old_term_ids)
{
    if ( ! $taxo === 'imcstatus') {
        return;
    }
    $new_term = get_term_by('id', $new_term_ids[0], $taxo);
    $transition = __( 'Status changed: ', 'participace-projekty' );
    $pocet_term = count($new_term_ids);
}
// add_action( 'set_object_terms', 'pb_change_project_status_log_action', 11, 6);
function pb_project_submit_btn_label($edit_completed = false)
{
    global $pb_submit_btn_text;
    if ($edit_completed) {
        return $pb_submit_btn_text['completed_on'];
    } else {
        return $pb_submit_btn_text['completed_off'];
    }

}
