<?php
class pbProjectSaveData {
    private $file_type_image = "gif, png, jpg, jpeg";
    private $file_type_scan  = "pdf" ;
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

}
 ?>
