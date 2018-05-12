<?php
/**
 * PB 1.00
 * Renders part of single archive template with PB Project additional fields
 */
class pbProjectSingle {
    private $field_list = array();

    public function __construct() {
        $this->form_fields = new pbRenderForm();
        $this->fields_definition = $this->form_fields->get_form_fields();
        $this->field_list      = $this->form_fields->get_form_fields_layout_single();
    }

    public function template_part_pb_project( $data = null)
    {

        if ( $this->field_list == 'all') {
            $field_list = array_keys( $this->fields_definition );
        } else {
            $field_list = $this->field_list;
        }
        ob_start();
        if ( count($field_list) > 0 ) {
            foreach ($field_list as $key ) {
                $this->render_field( '', $this->fields_definition[ $key ], $this->render_field_get_value( $this->fields_definition[ $key ]['id'], $data ));
                // code...
            }
        }

        return ob_get_clean();
    }

    private function render_field( $order = '' , $field, $value = '' )
    {
        if ( empty( $value)) {
            return '';
        }
        if (! empty( $order )) {
            $order = $order . ". ";
        }

        switch ( $field['type'] ) {
            case 'media':
                $this->render_field_file( $order, $field['label'], $value);
                break;
            case 'checkbox':
                $this->render_field_text( $order, $field['label'], $value);
                break;
            case 'textarea':
                $this->render_field_text( $order, $field['label'], $value);
                break;
            default:
                $this->render_field_text( $order, $field['label'], $value);
        }
    }

    private function render_field_file( $order = '', $label = '', $value = '')
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

    private function render_field_text( $order = '', $label = '', $value = '')
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

    private function render_field_get_value( $id, $values)
    {
        if (! empty($values[ $id][0])) {
            return $values[ $id][0];
        } else {
            return '';
        }
    }

}
