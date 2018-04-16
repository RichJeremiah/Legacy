<?php

/**
 * Created by PhpStorm.
 * User: Richard
 * Date: 25/01/2018
 * Time: 17:12
 */
class WarbandManager
{

    private static $instance;

    /**
     * Returns an instance of this class.
     */
    public static function get_instance()
    {

        if (null == self::$instance) {
            self::$instance = new WarbandManager();
        }

        return self::$instance;

    }

    protected $is_owner;
    protected $is_wb_member;
    protected $warband_id;
    protected $warband_data;
    protected $warband_region = '';
    protected $warband_diety_post;
    private $debug_text;

    public function debug_output(){
        return $this->debug_text;
    }

    private function add_debug($debug_string) {
        $this->debug_text .= $debug_string . '
        ';
    }

    private function __construct()
    {
        $this->warband_setup();
        add_action('wp_enqueue_scripts', array($this, 'enqueue_warband_js'));
    }

    public function is_warband_owner()
    {
        return $this->is_owner;
    }

    public function is_warband_member()
    {
        return $this->is_wb_member;
    }

    public function get_warband_id()
    {
        return $this->warband_id;
    }

    public function get_warband_code()
    {
        return get_post_meta($this->warband_id, 'leg_wb_code', true);
    }

    public function is_warband_open()
    {
        $value = get_post_meta($this->warband_id, 'leg_wb_publicly_open', true);
        if (!isset($value) || $value == 0) return false;
        return true;
    }

    public function get_warband_description()
    {
        return $this->warband_data->post_content;
    }

    public function get_warband_title()
    {
        return $this->warband_data->post_title;
    }

    public function get_warband_region()
    {
        if (isset($this->warband_region) && $this->warband_region != '') {
            return $this->warband_region;
        } else {
            return "No Region Details Found";
        }
    }

    public function get_warband_deity_name()
    {
        if (isset($this->warband_diety_post)) {
            return $this->warband_diety_post->post_title;
        } else {
            return 'NO DEITY!';
        }
    }

    public function get_warband_deity_description()
    {
        if (isset($this->warband_diety_post)) {
            return $this->warband_diety_post->post_content;
        }
        return '';
    }

    public function get_users_awaiting_approval()
    {
        $meta_query_args = array(
            'relation' => 'AND', // Optional, defaults to "AND"
            array(
                'key' => 'leg_char_warband_code',
                'value' => $this->get_warband_code(),
                'compare' => '='
            ),
            array(
                'relation' => 'OR',
                array(
                    'key' => 'leg_char_wb_approve',
                    'compare' => 'NOT EXISTS'
                ),
                array(
                    'key' => 'leg_char_wb_approve',
                    'value' => $this->get_warband_code(),
                    'compare' => '!='
                )
            )
        );

        $args = array(
            'meta_query' => $meta_query_args
        );

        $users = get_users($args);
        //$this->debug_text .=  "Found This many users" . count($users);
        return $users;
    }

    public function get_current_member_count()
    {
        //'leg_char_warband', $wb_id
        $args = array(
            'nopaging' => true,
            'meta_key' => 'leg_char_warband',
            'meta_value' => $this->warband_id
        );

        $users = get_users($args);
        return count($users);
    }

    function enqueue_warband_js()
    {
        wp_enqueue_script('ajax-script', plugins_url('/js/warband-manager.js', __FILE__), array('jquery'));

        // in JavaScript, object properties are accessed as ajax_object.ajax_url, ajax_object.we_value
        wp_localize_script('ajax-script', 'ajax_object',
            array('ajax_url' => admin_url('admin-ajax.php')));
    }

    public function warband_setup()
    {
        $current_user = wp_get_current_user()->user_login;
        //if user is admin, this should be the query
        $args = array(
            'nopaging' => true,
            'post_type' => 'warband',
            'post_status' => 'publish',
            'meta_key' => 'leg_wb_owner',
            'meta_value' => $current_user);

        $warbands = get_posts($args);
        $this->add_debug('Warbands Count:' . sizeof($warbands));
        if (sizeof($warbands) >= 1) {
            $this->set_warband($warbands[0]->ID);
            //$this->is_owner = false;
            $this->is_wb_member = true;
        } else {
            $this->is_owner = false;
            $user_id = wp_get_current_user()->ID;
            $user_warband_id = get_user_meta($user_id, "leg_char_warband", true);
            if ($user_warband_id > 0) {
                $this->set_warband($user_warband_id);
                $this->is_wb_member = true;
            } else {
                $this->is_wb_member = false;
            }
        }
        $this->set_relationship_data();
    }

    private function set_warband($warband_id){
        $warband_id = (string) $warband_id;
        $warband = get_post($warband_id);
        if (isset($warband) && $warband->post_type == "warband") {
            $this->warband_data = $warband;
            $this->warband_id = (int)$this->warband_data->ID;
        }
    }

    private function set_relationship_data()
    {
        $REGION_NAME = "region";
        $DEITY_NAME = "deity";
        if ($this->warband_id > 0) {

            $this->add_debug('User Warband ID: "' . json_encode ($this->warband_id).'"');
            $region = get_the_terms($this->warband_id, (string)$REGION_NAME);
//            $this->add_debug('$region:' . json_encode ($region));
            if ($region && !is_wp_error($region)) {
                $this->warband_region = $region[0]->name;
            }

            $deity = get_the_terms($this->warband_id, (string)$DEITY_NAME);
//            $this->add_debug('$deity:' . json_encode ($deity));
            if ($deity && !is_wp_error($deity)) {
                $deity_post = get_post($deity[0]->term_id);
                $this->warband_diety_post = $deity_post;
            }
        }
    }

    public static function set_user_approved()
    {
        if (!$_POST) return;
        $userId = $_POST['userId'];
        $wb_code = $_POST['wbcode'];
        $wb_id = $_POST['wbid'];
        $response = "You sent: " . $userId;
        $response = update_user_meta($userId, 'leg_char_wb_approve', $wb_code);

        if ($response > 0) {
            $response = update_user_meta($userId, 'leg_char_warband', $wb_id);
        }

        $responseArray = array(
            'success' => $response > 0
        );

        $myJSON = json_encode($responseArray);

        echo $myJSON;
        wp_die(); // this is required to terminate immediately and return a proper response
    }

    public static function set_user_rejected()
    {
        if (!$_POST) return;
        $userId = $_POST["userId"];
        $email = "";
        $existing_approvals = get_user_meta($userId, 'leg_char_wb_approve');
        //only try to delete what already exists...
        $delete_existing_approvals = (count($existing_approvals) > 0 ? delete_user_meta($userId, 'leg_char_wb_approve') : true);
        //Warband Code should already exist and should fail if it is deleted when it doesn't
        $delete_warband_code = delete_user_meta($userId, 'leg_char_warband_code');
        if (!$delete_existing_approvals || !$delete_warband_code) {
            $email = (!$delete_existing_approvals ? 'Failed to Delete Existing Approvals' : 'Deleted Existing Approvals');
            $email .= (!$delete_warband_code ? 'Failed to Delete Warband Cdode' : 'Deleted Warband Code');
            $response = 0;
        } else {
            $email = WarbandManager::send_rejection_email($userId);
            $response = 1;
        }

//        $email = WarbandManager::send_rejection_email($userId);
//        $response = 1;

        $responseArray = array(
            'success' => ($response > 0),
            'email' => $email
        );

        $myJSON = json_encode($responseArray);

        echo $myJSON;
        wp_die(); // this is required to terminate immediately and return a proper response
    }


    private static function send_rejection_email($userId)
    {
        $rejected_user = get_user_by('id', $userId);
        $post_id = url_to_postid(wp_get_referer());
        $email_content = get_field("leg_wb_no_approval_email", $post_id);
        if (!empty($rejected_user)) {
            $user_email = $rejected_user->user_email;
            if (!empty($email_content)) {
                $subject = "Your Warband Membership Request";
                $email_body = "Hi " . $rejected_user->first_name . ",\r\n\r\n";
                $email_body .= $email_content . "\r\n";
                //return $user_email . "BODY: " . $email_body;
                wp_mail($user_email, $subject, $email_body);
                return "email sent";

            } else {
                return "no Email body found for ID: " . $post_id;
            }
            //
            //$value = get_field( "leg_wb_no_approval_email" );
        }
        return "no user found";
    }

    public static function set_warband_member_public()
    {
        if (!$_POST) return;
        $wb_id = $_POST['wbid'];
        $wb_name = $_POST['wbname'];
        $wb_code = get_post_meta($wb_id, 'leg_wb_code', true);
        //If there isn't a Warband code then it will need one
        $wb_code_response = true;
        if (!isset($wb_code) || trim($wb_code) === '') {
            $wb_code = WarbandForm::generate_warband_code($wb_id, $wb_name);
            $wb_code_response = update_post_meta($wb_id, 'leg_wb_code', $wb_code);
        }
        $response = 0;
        if ($wb_code_response) {
            $response = update_post_meta($wb_id, 'leg_wb_publicly_open', 1);
            $message = ($response ? 'complete' : 'Failed to update Public Membership Flag');
        } else {
            $message = 'Failed to generate or set Warband Code';
        }
        $responseArray = array(
            'success' => ($response > 0),
            'message' => $message
        );

        $myJSON = json_encode($responseArray);

        echo $myJSON;
        wp_die(); // this is required to terminate immediately and return a proper response
    }

    public static function set_warband_member_private()
    {
        $wb_id = $_POST['wbid'];
        $wb_name = $_POST['wbname'];
        $wb_code = get_post_meta($wb_id, 'leg_wb_code', true);
        //If there isn't a Warband code then it will need one
        $wb_code_response = true;
        if (!isset($wb_code) || trim($wb_code) === '') {
            $wb_code = WarbandForm::generate_warband_code($wb_id, $wb_name);
            $wb_code_response = update_post_meta($wb_id, 'leg_wb_code', $wb_code);
        }
        $response = 0;
        if ($wb_code_response) {
            $response = update_post_meta($wb_id, 'leg_wb_publicly_open', 0);
            $message = ($response ? 'complete' : 'Failed to update Public Membership Flag');
        } else {
            $message = 'Failed to generate or set Warband Code';
        }
        $responseArray = array(
            'success' => ($response > 0),
            'message' => $message
        );

        $myJSON = json_encode($responseArray);

        echo $myJSON;
        wp_die(); // this is required to terminate immediately and return a proper response
    }

    public static function initialise_plugin()
    {
//        if (is_plugin_active('advanced-custom-fields/acf.php')) {
//            //plugin is activated
//            //does Warband Manager page exist?
//            $args = array(
//                'nopaging' => true,
//                'post_type' => 'acf',
//                'post_status' => 'publish',
//                'post_title' => 'Warband Manager',
//                'post_name' => 'acf_warband-manager',
//            );
//            $acf_template = get_posts($args);
//            if (count($acf_template) > 0) {
//                //if it exists, grab the ID
//                $acf_post_id = $acf_template->ID;
//            } else {
//                //create it
//                $fields = array(
//                    'post_type' => 'acf',
//                    'post_status' => 'publish',
//                    'post_title' => 'Warband Manager',
//                    'post_name' => 'acf_warband-manager',
//                    'post_status' => 'publish',
//                    'comment_status' => 'closed',
//                    'ping_status' => 'closed',
//                );
//                $acf_post_id = wp_insert_post($fields, false);
//            }
//
//            if ($acf_post_id > 0) {
//                //post exists (or does now).  Time to validate/create the meta fields
//
//            }
//        } else {
//            //ACF NEEDED - please install!
//        }

    }
}