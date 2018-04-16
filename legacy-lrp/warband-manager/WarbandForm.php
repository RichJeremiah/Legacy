<?php

/**
 * Created by PhpStorm.
 * User: Richard
 * Date: 11/04/2018
 * Time: 22:20
 */
class WarbandForm
{
    public static function check_warband_name()
    {
        if (isset($_POST['warband_name'])) {
            if (WarbandForm::is_warband_name_taken($_POST['warband_name'])) {
                die("exists");
            } else {
                die("");
            }
        }
        die("Invalid_Request! Please supply warband_name to validate uniqueness");
    }

    public static function is_warband_name_taken($title)
    {
        $args = array("post_type" => "warband", "name" => $title);
        $query = get_posts($args);

        if (!empty($query) && $query[0] != null) {
            return true;
        }
        return false;
    }

    public static function generate_warband_code($id, $warband_name)
    {
        $map = str_split("ABCDEFGHJK");
        echo $id;
        $split_id = str_split($id);
        $code = "";
        if (count($split_id) < 5) {
            $code .= strtoupper(substr(preg_replace('/\s+/', '', $warband_name), 0, 5 - count($split_id)));
        }
        foreach ($split_id as &$value) {
            $code .= $map[$value];
        }
        return $code;
    }

    public static function save_warband_submission()
    {
        if (!$_POST) return;

        $requestUrl = get_permalink();
        if (isset($_POST['warband_add_meta_nonce']) && wp_verify_nonce($_POST['warband_add_meta_nonce'], 'leg_add_warband_meta_form_nonce')) {

            // Get field values from form submission
            $warband_name = sanitize_text_field($_POST['warband-name']);
            $warband_desc = sanitize_text_field($_POST['warband-desc']);
            $warband_smdesc = sanitize_text_field($_POST['warband-smdesc']);
            $warband_p_cont = sanitize_text_field($_POST['warband-p-cont']);
            $warband_s_cont = sanitize_text_field($_POST['warband-s-cont']);

            if (isset($_POST['warband-isPublic'])) {
                $warband_is_public = 1;
            } else {
                $warband_is_public = 0;
            }

            if (isset($_POST['warband-isOpen'])) {
                $warband_is_open = 1;
            } else {
                $warband_is_open = 0;
            }
            $current_user = wp_get_current_user();

            $fields = array(
                'post_title' => $warband_name,
                'post_content' => $warband_desc,
                'leg_wb_short_desc' => $warband_smdesc,
                'comment_status' => 'closed',
                'ping_status' => 'closed',
                'leg_wb_primary_ooc_contact' => $warband_p_cont,
                'leg_wb_secondary_ooc_contact' => $warband_s_cont,
                'leg_wb_publicly_visible' => $warband_is_public,
                'leg_wb_publicly_open' => $warband_is_open,
                'leg_wb_owner' => $current_user->user_login
            );

            $new_wb_id = pods('warband')->add($fields);
            if ($new_wb_id) {
                $leg_wb_code = WarbandForm::generate_warband_code($new_wb_id, $warband_name);
                echo $leg_wb_code;
                update_post_meta($new_wb_id, 'leg_wb_code', $leg_wb_code);
                $warband_region = sanitize_text_field($_POST['warband-region']);
                // insert post meta
                add_post_meta($new_wb_id, '_custom_post_type_onomies_relationship', $warband_region);

                $warband_deity = sanitize_text_field($_POST['warband-deity']);
                if ($warband_deity == "-1") {
                    $warband_deity_name = sanitize_text_field($_POST['warband-deity-name']);
                    $warband_deity_desc = sanitize_text_field($_POST['warband-deity-desc']);
                    $warband_deity_smdesc = sanitize_text_field($_POST['warband-deity-smdesc']);
                    $deity_fields = array(
                        'post_title' => $warband_deity_name,
                        'post_content' => $warband_deity_desc,
                        'leg_dei_short_desc' => $warband_deity_smdesc,
                        'comment_status' => 'closed',
                        'ping_status' => 'closed',
                        'leg_dei_publicly_visible' => $warband_is_public
                    );

                    $new_deity_id = pods('deity')->add($deity_fields);
                    add_post_meta($new_wb_id, '_custom_post_type_onomies_relationship', $new_deity_id);
                } elseif ($warband_deity != "0") {
                    add_post_meta($new_wb_id, '_custom_post_type_onomies_relationship', $warband_deity);
                }
                $admin_email = get_option('admin_email');
                $subject = "New Warband Submission - Name: " . $warband_name;
                $current_user = wp_get_current_user();
                $email_body = "A New Warband Submission has been recieved!\r\n";
                $email_body .= "Name: " . $warband_name . "\r\n";
                $email_body .= "Author: " . $current_user->user_firstname . ' ' . $current_user->user_lastname . "\r\n";
                $email_body .= "It is currently in Draft, and cannot be seen by users on the site until it is approved.\r\n";
                $email_body .= "Please follow this link to Review the submission: " . get_option('siteurl') . "/wp-admin/edit.php?post_type=warband";

                wp_mail($admin_email, $subject, $email_body);


                wp_redirect(esc_url(add_query_arg('warband_add_notice', 'success', $requestUrl)));
                exit();

            } else {
                wp_redirect(esc_url(add_query_arg('warband_add_notice', 'fail', $requestUrl)));
                exit();

            }
            return;
        }
    }
}