<?php
/**
 * PB 1.00
 * Renders part of the form with PB Project additional fields
 * Used both by insert and edit page
 * class pbProjectEdit renders form
 * class pbProjectSaveData saves data
 *
 */
class pbProjectEdit {
    private $file_type_image = "gif, png, jpg, jpeg";
    private $file_type_scan  = "pdf" ;
    private $file_type_docs  = "doc, xls, docx, xlsx";
    private $pb_submit_btn_text = array(
            'completed_off' => 'Uložit si pro budoucí editaci',
            'completed_on'  => 'Odeslat návrh ke schválení',
        );

    /*
    * Renders the form part with additional fields
    */
    public function template_project_edit( $latlng = array(), $data = null)
    {
        $pb_project_meta_fields = new informacekprojektuMetabox();
        $fields = $pb_project_meta_fields->get_fields();

        ob_start();
        $this->render_field( 4,  $fields['actions'],    $this->render_field_get_value( $fields['actions']['id'], $data ) );
        $this->render_field( 5,  $fields['goals'],      $this->render_field_get_value( $fields['goals']['id'], $data ));
        $this->render_field( 6,  $fields['profits'],    $this->render_field_get_value( $fields['profits']['id'], $data ) );
        $this->render_map( '7. ' );
        $this->render_link_katastr( $latlng);
        $this->render_field( 8,  $fields['parcel'],     $this->render_field_get_value( $fields['parcel']['id'], $data ) );
        $this->render_image('9. ', $issue_image = (! empty($data['issue_image']) ? esc_url($data['issue_image']) : ''));
        $this->render_field( 10, $fields['map'],        $this->render_field_get_value( $fields['map']['id'], $data ) );
        $this->render_field( 11, $fields['cost'],       $this->render_field_get_value( $fields['cost']['id'], $data ) );
        echo '<div class="imc-row">';
        $this->render_field( 12, $fields['budget_total'],    $this->render_field_get_value( $fields['budget_total']['id'], $data ) );
        $this->render_field( 13, $fields['budget_increase'], $this->render_field_get_value( $fields['budget_increase']['id'], $data ) );
        echo '</div>';
        $this->render_field( 14, $fields['attach1'],    $this->render_field_get_value( $fields['attach1']['id'], $data ) );
        $this->render_field( 15, $fields['attach2'],    $this->render_field_get_value( $fields['attach2']['id'], $data ) );
        $this->render_field( 16, $fields['attach3'],    $this->render_field_get_value( $fields['attach3']['id'], $data ) );
        echo '<div class="imc-row">';
        $this->render_field( 17, $fields['name'],       $this->render_field_get_value( $fields['name']['id'], $data ) );
        $this->render_field( 18, $fields['phone'],      $this->render_field_get_value( $fields['phone']['id'], $data ) );
        $this->render_field( 19, $fields['email'],      $this->render_field_get_value( $fields['email']['id'], $data ) );
        echo '</div>';
        $this->render_field( 20, $fields['address'],    $this->render_field_get_value( $fields['address']['id'], $data ) );
        $this->render_field( 21, $fields['signatures'], $this->render_field_get_value( $fields['signatures']['id'], $data ) );
        $this->render_field( 22, $fields['age_conf'],   $this->render_field_get_value( $fields['age_conf']['id'], $data ) );
        $this->render_field( 23, $fields['agreement'],  $this->render_field_get_value( $fields['agreement']['id'], $data ) );
        $this->render_field( 24, $fields['completed'],  $this->render_field_get_value( $fields['completed']['id'], $data ) );

        return ob_get_clean();
    }

    private function render_field_get_value( $id, $values)
    {
        if (! empty($values[ $id][0])) {
            return $values[ $id][0];
        } else {
            return '';
        }
    }

    /*
    * Core functin for field renderingRenders the form part with additional fields
    */
    private function render_field( $order = '' , $field, $value = '' )
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
                $this->render_file_attachment( $order, $field, $value, $help);
                break;
            case 'checkbox':
                $this->render_checkbox( $order, $field, $value, $help);
                break;
            case 'textarea':
                $this->render_textarea( $order, $field, $value, $help);
                break;
            case 'email':
                $this->render_text( $order, $field, $value, $help);
                break;
            default:
                $this->render_text( $order, $field, $value, $help);
        }
    }

    private function render_textarea( $order, $input = null, $value = '', $help = '' )
    {
        if ( !empty( $input['mandatory']) && $input['mandatory'] ) {
            $mandatory = $this->render_mandatory( $input['mandatory']) ;
        } else {
            $mandatory = $this->render_mandatory( false);
        }
        if ( ! empty($input['rows'])) {
            $rows = $input['rows'];
        } else {
            $rows = 3;
        }

        $output = '<div class="imc-row">
            <h3 class="u-pull-left imc-SectionTitleTextStyle">%s%s %s'.$this->render_tooltip( $help ).'</h3>
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
    private function render_text( $order, $input = null, $value = '', $help = '' )
    {
        if ( !empty( $input['mandatory']) && $input['mandatory'] ) {
            $mandatory = $this->render_mandatory( $input['mandatory']) ;
        } else {
            $mandatory = $this->render_mandatory( false);
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

        $output = '<h3 class="imc-SectionTitleTextStyle">%s%s %s'.$this->render_tooltip( $help ).'</h3><input type="%s" %s autocomplete="off"
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

    private function render_file_attachment( $order, $input, $value = '', $help = '')
    {
        if ( !empty( $input['mandatory']) && $input['mandatory'] ) {
            $mandatory = $this->render_mandatory( $input['mandatory']) ;
        } else {
            $mandatory = $this->render_mandatory( false);
        }
        $options = ' readonly="readonly" ';
        if ($value) {
            $filename = basename($value);
        } else {
            $filename = $value;
        }

        $link = $this->render_file_link($value, $input['id']);
        // <span id="%sName" class="imc-ReportGenericLabelStyle imc-TextColorSecondary">'. __('Vyberte soubor','participace-projekty') .'</span>
        $output = '<div class="imc-row" id="pbProjectSection%s">
                    <div class="imc-row">
                        <h3 class="u-pull-left imc-SectionTitleTextStyle">%s%s %s'.$this->render_tooltip( $help ).'</h3>
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

    /*
    * Renders HTML link for opening an file attachment
    */
    private function render_file_link($url, $id )
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
    private function render_checkbox( $order, $input, $value = '', $help = '')
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
            $mandatory = $this->render_mandatory( $input['mandatory']) ;
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

        $output = '<h3 class="imc-SectionTitleTextStyle" style="display:inline-block;"><label id="%sName" for="%s">%s%s</label>'. $this->render_tooltip($help) .'
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

    public function render_mandatory( $mandatory = false)
    {
        if ( $mandatory ) {
            return '';
        } else {
            return ' ( ' . __('volitelné','participace-projekty') .' )';
            return '<span class="imc-OptionalTextLabelStyle">" " (' . __('optional','participace-projekty') .')></span>';
        }
    }

    private function render_map( $order = '')
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

    /*
    * Renders featured_image
    */
    private function render_image( $order = '', $issue_image = '')
    {
        $output = '
            <div class="imc-row" id="imcImageSection">
                <h3 class="u-pull-left imc-SectionTitleTextStyle">%s%s' . $this->render_mandatory(false) .'</h3>
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

    /*
    * Renders link to katastr with
    */
    private function render_link_katastr($latlng)
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

    /*
    * Definition of rules for FormValidator in validate.js
    */
    public function render_fields_js_validation()
    {
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

    /*
    * Renders link to katastr with
    */
    private function render_tooltip( $text = "")
    {
        if (! empty( $text)) {
            return '<span class="pb_tooltip"><i class="material-icons md-24" style="margin-left:2px;">help_outline</i>
            <span class="pb_tooltip_text" >' . $text . '</span></span>' ;
        } else {
            return '';
        }
    }
}

/*
* Class pbProjectSaveData - save data for insert and update
*/
class pbProjectSaveData {
    private $file_type_image = "gif, png, jpg, jpeg";
    private $file_type_scan  = "pdf" ;
    private $post_id         = null;
    private $post_data       = null;
    private $status_taxo     = 'imcstatus';

    /*
    * Create new project
    */
    public function project_insert()
    {
        $imccategory_id = esc_attr(strip_tags($_POST['my_custom_taxonomy']));

    	// Check options if the status of new issue is pending or publish

    	$generaloptions = get_option( 'general_settings' );
    	$moderateOption = $generaloptions["moderate_new"];

    	//CREATE THE ISSUE TO DB

    	$this->post_data = array(
    		'post_title' => esc_attr(strip_tags($_POST['postTitle'])),
    		'post_content' => esc_attr(strip_tags($_POST['postContent'])),
    		'post_type' => 'imc_issues',
    		'post_status' => ($moderateOption == 2) ? 'publish' : 'pending',
    		'post_name'   => sanitize_title( $_POST['postTitle']),
    		'tax_input' => array( 'imccategory' => $imccategory_id ),
    	);

        $this->get_metadata_from_request( $_POST, false);

    	$this->post_id = wp_insert_post( $this->post_data, true);

    	if ( $this->post_id && ( ! is_wp_error($this->post_id)) ) {
    		$this->insert_attachments( $_FILES );
    	}

    	// Choose the imcstatus with smaller id
    	// zmenit order by imc_term_order

    	$pb_edit_completed = (! empty( $_POST['pb_project_edit_completed']) ) ?  $_POST['pb_project_edit_completed'] : 0;
    	$all_status_terms = get_terms( $this->status_taxo , array( 'hide_empty' => 0 , 'orderby' => 'id', 'order' => 'ASC') );
    	if ( $pb_edit_completed ) {
    		$first_status = $all_status_terms[1];
    	} else {
    		$first_status = $all_status_terms[0];
    	}

    	wp_set_object_terms($this->post_id, $first_status->name, $this->status_taxo);

    	//Create Log if moderate is OFF

    	if($moderateOption == 2) {

    		imcplus_crelog_frontend_nomoder($this->post_id, $first_status->term_id, get_current_user_id());

    	}

    	$this->project_insert_image();

        imcplus_mailnotify_4submit($this->post_id,$imccategory_id, $this->post_data['meta_input']['imc_address']);

        return $this->post_id;
    }

    /*
    * Update existing project
    */
    public function update_project()
    {
    	$this->post_id = intval( sanitize_text_field( $_GET['myparam'] ));
    	$issue_id = $this->post_id ;

    	$lat = esc_attr(strip_tags($_POST['imcLatValue']));
    	$lng = esc_attr(strip_tags($_POST['imcLngValue']));


    	$imccategory_id = esc_attr(strip_tags($_POST['my_custom_taxonomy']));
    	$address = esc_attr(strip_tags($_POST['postAddress']));

    	//UPDATE THE ISSUE TO DB
    	$thia->post_data = array(
    		'ID' => $issue_id,
            'post_title' => esc_attr(strip_tags($_POST['postTitle'])),
            'post_content' => esc_attr(strip_tags($_POST['postContent'])),
            'tax_input' => array( 'imccategory' => $imccategory_id ),
        );

    	$post_id = wp_update_post( $thia->post_data, true );

    	if (is_wp_error($post_id)) {
            return $post_id;
    	}
        $this->get_metadata_from_request( $_POST, true);

    	$this->update_postmeta();

        $this->project_update_image();

    	$this->update_attachments( $_FILES );
        $this->update_project_status();

        return $this->post_id;
    }

    /*
    * read post_metadata from $_POST
    */
    public function get_metadata_from_request( $data, $update = false )
    {
        $this->post_data['meta_input'] = array(
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
            $this->post_data['meta_input']['imc_likes'] = '0';
            $this->post_data['meta_input']['modality'] = '0';
        }
    }

    /*
    * save featured_image for new project
    */
    private function project_insert_image()
    {

    	$image =  $_FILES['featured_image'];

    	$orientation = intval(strip_tags($_POST['imcPhotoOri']), 10);

    	if ($orientation !== 0) {
    		$attachment_id = imc_upload_img( $image, $this->post_id, $this->post_data['post_title'], $orientation);
    	} else {
    		$attachment_id = imc_upload_img( $image, $this->post_id, $this->post_data['post_title'], null);
    	}

    	set_post_thumbnail( $this->post_id, $attachment_id );

    }

    /*
    * save featured_image for updated project
    */
    private function project_update_image()
    {

        $imageScenario = intval(strip_tags($_POST['imcImgScenario']), 10);

        if ( $imageScenario === 1) {
            delete_post_thumbnail( $this->post_id );
        }

        if ( $imageScenario === 2) {
            $this->project_insert_image();
        }
    }

    /*
    * save all file attachment for new project
    */
    private function insert_attachments( $files)
    {
        if (! empty( $this->post_id)) {
            // $_FILE['id'], fields - error, name, size, tmp_name, type, pro prazdne je error = 4 ostatni prazdne,
            $this->insert_attachment_1($files['pb_project_podporovatele'],  'pb_project_podporovatele');
            $this->insert_attachment_1($files['pb_project_mapa'],           'pb_project_mapa');
            $this->insert_attachment_1($files['pb_project_naklady'],        'pb_project_naklady');
            $this->insert_attachment_1($files['pb_project_dokumentace1'],   'pb_project_dokumentace1');
            $this->insert_attachment_1($files['pb_project_dokumentace2'],   'pb_project_dokumentace2');
            $this->insert_attachment_1($files['pb_project_dokumentace3'],   'pb_project_dokumentace3');
        }
    }

    private function insert_attachment_1 ($file, $attachment_type = '' )
    {
        if (( $file['error'] == '0') && (! empty($attachment_type)) &&
                ( $this->check_file_type($file['name'],$attachment_type))) {
            $attachment_id = imc_upload_img( $file, $this->post_id, $this->post_id . '-' . $attachment_type, null);
            if ( $attachment_id) {
              $url = wp_get_attachment_url( $attachment_id);
              update_post_meta( $this->post_id, $attachment_type, $url);
            }
            return $attachment_id;
        } elseif ($this->post_id) {
            delete_post_meta( $this->post_id, $attachment_type );
            return true;
        } else {
            return false;
        }
    }

    /*
    * save all file attachment updated project
    */
    private function update_attachments( $files )
    {
        if (! $this->post_id) {
            return false;
        }

        $list = array(
            'pb_project_podporovatele',
            'pb_project_mapa',
            'pb_project_naklady',
            'pb_project_dokumentace1',
            'pb_project_dokumentace2',
            'pb_project_dokumentace3',
        );
        foreach ($list as $key) {
            $this->update_attachment_1($files[ $key ], $key, $_POST[ $key.'Name']);
        }
    }

    private function update_attachment_1( $file, $attachment_type, $meta_value )
    {
        if (( $file['error'] == '0') && (! empty($attachment_type))  &&
                ( $this->check_file_type($file['name'],$attachment_type)) ) {
            $attachment_id = imc_upload_img( $file, $this->post_id, $this->post_id . '-' . $attachment_type, null);
            if ( $attachment_id) {
                $url = wp_get_attachment_url( $attachment_id);
                update_post_meta( $this->post_id, $attachment_type, $url);
            }
            return $attachment_id;
        } elseif ( empty( $meta_value) ) {
            delete_post_meta( $this->post_id, $attachment_type );
            return true;
        } else {
            return false;
        }
    }

    /*
    * check allowed file types
    */
    private function check_file_type( $file, $attach_type)
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

    /*
    * update post_metadata for updated project
    */
    private function update_postmeta()
    {
        foreach ($this->post_data['meta_input'] as $key => $value) {
            update_post_meta($this->post_id, $key, $value);
        }
    }

    /*
    * update terms imcstatus
    */
    private function update_project_status()
    {
        /********************** About changing Project status  ************************/
        $pb_edit_completed = (! empty( $_POST['pb_project_edit_completed']) ) ?  $_POST['pb_project_edit_completed'] : 0;
        $all_status_terms = get_terms( $this->status_taxo , array( 'hide_empty' => 0 , 'orderby' => 'id', 'order' => 'ASC') );
        if ( $pb_edit_completed ) {
            $set_status = $all_status_terms[1];
        } else {
            $set_status = $all_status_terms[0];
        }
        $pb_project_status = wp_get_object_terms( $this->post_id, $this->status_taxo);

        if ( $set_status->slug != $pb_project_status[0]->slug) {
            wp_delete_object_term_relationships( $this->post_id, $this->status_taxo );
            wp_set_object_terms( $this->post_id, array($set_status->term_id,), $this->status_taxo, false);
            $this->change_project_status_log( $set_status, $this->post_id, 'Změna stavu navrhovatelem' );
        }

    }

    public function change_project_status_log( $new_step_term, $post_id, $description = 'Změna stavu')
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

}

 ?>
