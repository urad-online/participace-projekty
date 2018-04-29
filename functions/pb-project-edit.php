<?php
class pbProjectEdit {
    private $file_type_image = "gif, png, jpg, jpeg";
    private $file_type_scan  = "pdf" ;
    private $file_type_docs  = "doc, xls, docx, xlsx";
    private $pb_submit_btn_text = array(
            'completed_off' => 'Uložit si pro budoucí editaci',
            'completed_on'  => 'Odeslat návrh ke schválení',
        );

    public function template_project_edit( $latlng = array(), $data = null)
    {
        global $pb_submit_btn_text;

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

    private function render_mandatory( $mandatory = false)
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
    private function render_tooltip( $text = "")
    {
        if (! empty( $text)) {
            return '<span class="pb_tooltip"><i class="material-icons md-24" style="margin-left:2px;">help_outline</i>
            <span class="pb_tooltip_text" >' . $text . '</span></span>' ;
        } else {
            return '';
        }
    }
    public function render_condition_link()
    {
        // nuni asi neni potreba a jde to smazat
        $page_conditions = get_page_by_path( 'podminky-pouziti-a-ochrana-osobnich-udaju');
        $page_link = get_page_link( $page_conditions->ID);
        $output = '<h3 class="imc-SectionTitleTextStyle" style="display:inline-block; margin-left:20px;">S podmínkami se můžete seznámit
            <a id="pb_link_to_conditions" target="_blank" href="'.$page_link.'" title="Přejít na stránku s podmínkami">zde</a></h3>';
        return $output;
        // return '<span style="display:inline-block; margin-left:20px;">S podmínkami se můžete seznámit </span>';
    }
}

 ?>
