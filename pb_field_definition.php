<?php
class pbRenderForm {
    private $fields_order;
    private $fields;

    public function __construct()
    {
        $this->read_form_fields();
        $this->read_form_fields_order();
    }
    private function read_form_fields()
    {
        delete_transient( 'pb_custom_fields_definition');

        if ( false === ( $this->fields = get_transient( 'pb_custom_fields_definition' ) ) ) {
            $this->fields = pb_get_custom_fields();
            set_transient( 'pb_custom_fields_definition', json_encode( $this->fields, JSON_UNESCAPED_UNICODE), 12 * HOUR_IN_SECONDS );
        } else {
            $this->fields = json_decode( $this->fields, true);
        }

    }

    private function read_form_fields_order()
    {
        $this->fields_order = array(
            array( 'type' => 'row', 'data' => array(
                array('type' => 'field', 'data' => array( 'field' => 'title', 'columns' => 6)),
                array('type' => 'field', 'data' => array( 'field' => 'category', 'columns' => 6)),
            )),
            array( 'type' => 'field', 'data' => array( 'field' => 'content', 'columns' => 0)),
            array( 'type' => 'field', 'data' => array( 'field' => 'actions', 'columns' => 0)),
            array( 'type' => 'field', 'data' => array( 'field' => 'goals', 'columns' => 0)),
            array( 'type' => 'field', 'data' => array( 'field' => 'profits', 'columns' => 0)),
            array( 'type' => 'field', 'data' => array( 'field' => 'postAddress', 'columns' => 0)),
            array( 'type' => 'field', 'data' => array( 'field' => 'parcel', 'columns' => 0)),
            // array( 'type' => 'field', 'data' => array( 'field' => 'issue_image', 'columns' => 0)),
            array( 'type' => 'field', 'data' => array( 'field' => 'map', 'columns' => 0)),
            array( 'type' => 'field', 'data' => array( 'field' => 'cost', 'columns' => 0)),
            array( 'type' => 'row', 'data' => array(
                array('type' => 'field', 'data' => array( 'field' => 'budget_total', 'columns' => 5)),
                array('type' => 'field', 'data' => array( 'field' => 'budget_increase', 'columns' => 6)),
            )),
            array( 'type' => 'field', 'data' => array( 'field' => 'attach1', 'columns' => 0)),
            array( 'type' => 'field', 'data' => array( 'field' => 'attach2', 'columns' => 0)),
            array( 'type' => 'field', 'data' => array( 'field' => 'attach3', 'columns' => 0)),
            array( 'type' => 'row', 'data' => array(
                array('type' => 'field', 'data' => array( 'field' => 'name', 'columns' => 5)),
                array('type' => 'field', 'data' => array( 'field' => 'phone', 'columns' => 3)),
                array('type' => 'field', 'data' => array( 'field' => 'email', 'columns' => 4)),
            )),
            array( 'type' => 'field', 'data' => array( 'field' => 'address', 'columns' => 0)),
            array( 'type' => 'field', 'data' => array( 'field' => 'signatures', 'columns' => 0)),
            array( 'type' => 'field', 'data' => array( 'field' => 'age_conf', 'columns' => 0)),
            array( 'type' => 'field', 'data' => array( 'field' => 'agreement', 'columns' => 0)),
            array( 'type' => 'field', 'data' => array( 'field' => 'completed', 'columns' => 0)),
        );

    }

    public function get_form_fields()
    {
        return $this->fields;
    }

    public function get_form_fields_order()
    {
        return $this->fields_order;
    }

}
function pb_get_custom_fields()
{
    $custom_fields = array(
        'title' => array(
            'label'     => 'Název',
            'id'        => 'postTitle',
            'type'      => 'text',
            'mandatory' => true,
            'placeholder' => 'Zadejte krátký název projektu',
            'show_mtbx' => false,
            'show_form' => false,
            'js_rules'  => array(
                'rules' => 'required|min_length[5]|max_length[255]',),
        ),
        'category' => array(
            'label'     => 'Kategorie',
            'id'        => 'my_custom_taxonomy',
            'type'      => 'category',
            'mandatory' => true,
            'show_mtbx' => false,
            'show_form' => false,
            'js_rules'  => array(
                'rules' => 'required',),
        ),
        'content' => array(
            'label'     => 'Popis (volitelné)',
            'id'        => 'postContent',
            'type'      => 'text',
            'mandatory' => true,
            'placeholder' => 'Vyplňte obsah svého projektu',
            'show_mtbx' => false,
            'show_form' => false,
            'js_rules'  => array(
                'rules' => 'required',),
        ),
        'actions' => array(
            'label'     => 'Co by se mělo udělat',
            'id'        => 'pb_project_akce',
            'type'      => 'textarea',
            'mandatory' => true,
            'placeholder' => 'Popište aktivity, které je potřeba vykonat',
            'title'     => "Actions",
            'show_mtbx' => true,
            'show_form' => true,
            'js_rules'  => array(
                'rules' => 'required',
                'depends' => 'pb_project_js_validate_required',
                ),
        ),
        'goals' => array(
            'label'     => 'Proč je projekt důležitý, co je jeho cílem',
            'id'        => 'pb_project_cile',
            'type'      => 'textarea',
            'mandatory' => true,
            'placeholder' => 'Popište cíle projektu',
            'title'     => "goals",
            'help'      => 'Nebojte se trochu více rozepsat',
            'show_mtbx' => true,
            'show_form' => true,
            'js_rules'  => array(
                'rules' => 'required',
                'depends' => 'pb_project_js_validate_required',
            ),
        ),
        'profits' => array(
            'label'         => 'Kdo bude mít z projektu prospěch',
            'id'            => 'pb_project_prospech',
            'type'          => 'textarea',
            'mandatory'     => true,
            'placeholder'   => 'Popište kdo a jaký bude mít z projektu prospěch',
            'title'         => 'profit',
            'help'          => '',
            'show_mtbx' => true,
            'show_form' => true,
            'js_rules'  => array(
                'rules' => 'required',
                'depends' => 'pb_project_js_validate_required',
            ),
        ),
        'postAddress' => array(
            'label'     => 'Adresa',
            'id'        => 'postAddress',
            'type'      => 'imcmap',
            'show_mtbx' => true,
            'show_form' => true,
            'js_rules'  => array(
                'rules' => 'required',
                'depends' => 'pb_project_js_validate_required',
            ),
        ),
        'parcel' => array(
            'label'       => 'Parcelní číslo',
            'id'          => 'pb_project_parcely',
            'type'        => 'textarea',
            'mandatory'   => true,
            'placeholder' => 'Vyplňte číslo parcely ve formátu NNNN/NNNN',
            'title'       => "parcel",
            'help'        => 'Pro usnadnění kontroly zadejte prosím, každé číslo na samostatný řádek',
            'show_mtbx' => true,
            'show_form' => true,
            'js_rules'  => array(
                'rules' => 'required',
                'depends' => 'pb_project_js_validate_required',
            ),
        ),
        'photo' => array(
    		'label'       => 'Fotografie',
    		'id'          => 'featured_image',
    		'type'        => 'media',
            'mandatory'     => true,
            'material_icon' => 'image',
            'AddBtnLabel'   => 'Vložit fotku',
            'DelBtnLabel'   => 'Smazat fotku',
            'show_mtbx'     => true,
            'show_form'     => true,
            'js_rules'      => array(
                'rules' => 'is_file_type[gif,GIF,png,PNG,jpg,JPG,jpeg,JPEG]',
                ),
        ),
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
            'help'          => 'Povolené typy příloh: gif, png, jpg, jpeg, pdf',
            'show_mtbx'     => true,
            'show_form'     => true,
            'js_rules'      => array(
                'rules' => 'is_file_type[gif,GIF,png,PNG,jpg,JPG,jpeg,JPEG,pdf,PDF]',
                'depends' => 'pb_project_js_validate_required',
            ),
        ),
        'mapName' => array(
            'label'     => 'Mapa (situační nákres)',
            'id'        => 'pb_project_mapaName',
            'js_rules'      => array(
                'rules' => 'required',
                'depends' => 'pb_project_js_validate_required',
            ),
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
            'help'          => 'Povolené typy příloh: gif, png, jpg, jpeg, pdf, doc, docx, xls, xlsx',
            'show_mtbx'     => true,
            'show_form'     => true,
            'js_rules'      => array(
                'rules' => 'is_file_type[gif,GIF,png,PNG,jpg,JPG,jpeg,JPEG,pdf,PDF,doc,DOC,xls,XLS]',
            ),
        ),
        'costName' => array(
            'label'     => 'Předpokládané náklady',
            'id'        => 'pb_project_nakladyName',
            'js_rules'      => array(
                'rules' => 'required',
            ),
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
            'show_mtbx'   => true,
            'show_form'   => true,
            'js_rules'    => array(
                'rules'   => 'required|integer|greater_than[99999]|less_than[2000001]',
                'depends' => 'pb_project_js_validate_required',
            ),
        ),
        'budget_increase' => array(
            'label'     => 'Náklady byly navýšeny o rezervu 10%',
            'id'        => 'pb_project_naklady_navyseni',
            'default'   => 'no',
            'type'      => 'checkbox',
            'title'     => "budget_increase",
            'mandatory' => true,
            'columns'   => 6,
            'show_mtbx'   => true,
            'show_form'   => true,
            'js_rules'    => array(
                'rules'   => 'required',
                'depends' => 'pb_project_js_validate_required',
            ),
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
            'show_mtbx'   => true,
            'show_form'   => true,
            'js_rules'    => array(
                'rules'   => 'is_file_type[gif,GIF,png,PNG,jpg,JPG,jpeg,JPEG,pdf,PDF]',
            ),
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
            'show_mtbx'   => true,
            'show_form'   => true,
            'js_rules'    => array(
                'rules'   => 'is_file_type[gif,GIF,png,PNG,jpg,JPG,jpeg,JPEG,pdf,PDF]',
            ),
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
            'show_mtbx'   => true,
            'show_form'   => true,
            'js_rules'    => array(
                'rules'   => 'is_file_type[gif,GIF,png,PNG,jpg,JPG,jpeg,JPEG,pdf,PDF]',
            ),
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
            'show_mtbx'   => true,
            'show_form'   => true,
            'js_rules'    => array(
                'rules'   => 'required',
                'depends' => 'pb_project_js_validate_required',
            ),
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
            'show_mtbx'   => true,
            'show_form'   => true,
            'js_rules'    => array(
                'rules'   => 'valid_phone',
            ),
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
            'show_mtbx'   => true,
            'show_form'   => true,
            'js_rules'    => array(
                'rules'   => 'required|valid_email',
                'depends' => 'pb_project_js_validate_required',
            ),
        ),
        'address' => array(
            'label'     => 'Adresa (název ulice, číslo popisné, část Prahy 8)',
            'id'        => 'pb_project_navrhovatel_adresa',
            'type'      => 'text',
            'mandatory' => true,
            'placeholder' => 'Vyplňte adresu navrhovatele',
            'title'     => "address",
            'help'      => '',
            'show_mtbx'   => true,
            'show_form'   => true,
            'js_rules'    => array(
                'rules'   => 'required',
                'depends' => 'pb_project_js_validate_required',
            ),
        ),
        'signatureName' => array(
            'label'     => 'Podpisový arch',
            'id'        => 'pb_project_podporovateleName',
            'js_rules'      => array(
                'rules'   => 'required',
                'depends' => 'pb_project_js_validate_required',
            ),
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
            'show_mtbx'   => true,
            'show_form'   => true,
            'js_rules'    => array(
                'rules'   => 'is_file_type[gif,GIF,png,PNG,jpg,JPG,jpeg,JPEG,pdf,PDF]',
            ),
        ),
        'age_conf' => array(
            'label'     => 'Prohlašuji, že jsem starší 15 let',
            'id'        => 'pb_project_prohlaseni_veku',
            'default'   => 'no',
            'type'      => 'checkbox',
            'mandatory' => true,
            'title'     => "age_conf",
            'show_mtbx'   => true,
            'show_form'   => true,
            'js_rules'    => array(
                'rules'   => 'required]',
                'depends' => 'pb_project_js_validate_required',
            ),
        ),
        'agreement'     => array(
            'label'     => 'Souhlasím s <a href="'. site_url("podminky-pouziti-a-ochrana-osobnich-udaju/") . '" target="_blank" title="Přejít na stránku s podmínkami">podmínkami použití</a>',
            'id'        => 'pb_project_podminky_souhlas',
            'default'   => 'no',
            'type'      => 'checkbox',
            'title'     => "Agreement",
            'mandatory' => true,
            'help'      => 'K podání projektu musíte souhlasit s podmínkami',
            'show_mtbx'   => true,
            'show_form'   => true,
            'js_rules'    => array(
                'name'    => 'Souhlas s podmínkami',
                'rules'   => 'required]',
                'depends' => 'pb_project_js_validate_required',
            ),
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
    return $custom_fields;
}

function pb_get_custom_fields_metabox()
{
    $all_fields = pb_get_custom_fields();
    $output = array();
    foreach ($all_fields as $key => $value) {
        if ((!empty($value['show_mtbx'] )) && ($value['show_mtbx'] )) {
            $output[ $key] = array(
                'label' => $value['label'],
                'id'    => $value['id'],
                'type'  => $value['type'],
            );
            if (!empty( $value['default'])) {
                $output[ $key] = array(
                    'default' => $value['default'],
                );
            }
        }
    }
    return $output;
}

function pb_get_custom_fields_form()
{
    $all_fields = pb_get_custom_fields();
    $output = array();
    foreach ($all_fields as $key => $value) {
        if ((!empty($value['show_form'] )) && ($value['show_form'] )) {
            $output[ $key] = $value;
            $validation_rules = ( $value['mandatory'] ) ? "required" : "";
            if ( ! empty( $value['validation'])) {
                $validation_rules .= (!empty($validation_rules)) ? "|".$value['validation'] : $value['validation'];
            }
            $output[ $key]['validation_rules'] = $validation_rules;
            if ( ! empty( $value['validation_depends'])) {
                $output[ $key]['validation_depends'] = $value['validation_depends'];
            }
        }
    }
    return $output;
}

function pb_get_custom_fields_all()
{
    $all_fields = pb_get_custom_fields();
    $output = array();
    foreach ($all_fields as $key => $value) {
        if ((!empty($value['show_form'] )) && ($value['show_form'] )) {
            $output[ $key] = $value;
            $validation_rules = ( $value['mandatory'] ) ? "required" : "";
            if ( ! empty( $value['validation'])) {
                $validation_rules .= (!empty($validation_rules)) ? "|".$value['validation'] : $value['validation'];
            }
            $output[ $key]['validation_rules'] = $validation_rules;
            if ( ! empty( $value['validation_depends'])) {
                $output[ $key]['validation_depends'] = $value['validation_depends'];
            }
        }
    }
    return $output;
}

function pb_get_custom_fields_form_validation()
{
    $fields = pb_get_custom_fields();
    $output = array();
    foreach ($fields as $key => $value) {
        if (! empty( $value['js_rules'] )) {
            $rule = array(
                'name'    => $value['id'],
                'display' => $value['label'],
                'rules'   => $value['js_rules']['rules'],
            );
            if (! empty( $value['js_rules']['depends'] )) {
                $rule['depends'] = $value['js_rules']['depends'];
            }
            if (! empty( $value['js_rules']['name'] )) {
                $rule['name'] = $value['js_rules']['name'];
            }
            array_push( $output, $rule);
        }
    }
    return json_encode( $output );
}
function get_form_field_order()
{
    return array(
        'actions',
        'goals',
        'profits',
        'postAddress',
    );
}