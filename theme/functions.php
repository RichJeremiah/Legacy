<?php
/**
 * Created by PhpStorm.
 * User: Richard
 * Date: 16/12/2017
 * Time: 20:59
 */

add_action('wp_enqueue_scripts', 'enqueue_child_theme_styles', PHP_INT_MAX);
add_action('wp_enqueue_scripts', 'enque');
add_action('wp_ajax_test_ajax', 'test_ajax');

function test_ajax($value)
{
    echo "You sent" . $value;
    wp_die();
}

function enque()
{
    wp_localize_script('ajax-script', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
}

function enqueue_child_theme_styles()
{
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
}

add_action('storefront_header', 'jk_storefront_header_content', 40);
function jk_storefront_header_content()
{ ?>
    <div style="clear: both;">
        <?php nivo_slider(391); ?>
    </div>
    <?php
}

function getRegions()
{
    $args = array(
        'nopaging' => true,
        'post_type' => 'region',
        'post_status' => 'publish'
    );

    $regions = get_posts($args);

    $items = array();
    //Adding post titles to the items array
    foreach ($regions as $post)
        $items[$post->ID] = $post->post_title;
    return $items;
}

function getDeities()
{
    $args = array(
        'nopaging' => true,
        'post_type' => 'deity',
        'post_status' => 'publish'
    );

    $deities = get_posts($args);

    $items = array();
    //Adding post titles to the items array
    foreach ($deities as $post)
        $items[$post->ID] = $post->post_title;
    return $items;
}

function getWarbands()
{
    $is_admin = current_user_can('administrator');
    if($is_admin){
        //if user is admin, this should be the query
        $args = array(
            'nopaging' => true,
            'post_type' => 'warband',
            'post_status' => 'publish',
           );
    } else {
        $args = array(
            'nopaging' => true,
            'post_type' => 'warband',
            'post_status' => 'publish',
            'meta_key' => 'leg_wb_publicly_open',
            'meta_value' => '1');
    }
    $warbands = get_posts($args);

    $items = array();
    //Adding post titles to the items array
    foreach ($warbands as $post)
        $items[$post->ID] = $post->post_title;

    if(!$is_admin) {

        //if Current User leg_char_warband_code = Current user leg_char_wb_approve then find ID
        $user_id = wp_get_current_user()->ID;
        //$items[$user_id] = $user_id;

        $wb_code = get_user_meta($user_id, 'leg_char_warband_code', true);
        $wb_approve = get_user_meta($user_id, 'leg_char_wb_approve', true);
        if (isset($wb_code) && trim($wb_code) !== '') {
            //$items[$wb_code] = $wb_code;
            //$items[$wb_approve] = $wb_approve;
            if ($wb_code === $wb_approve) {
                $warbandId = get_user_meta($user_id, 'leg_char_warband', true);
                if (isset($warbandId) && $warbandId > 0) {
                    $privateWarband = get_post($warbandId);
                    $items[$warbandId] = $privateWarband->post_title;
                }
            }
        }
    }

    $items[-1] = "Other (Warband Code)";
    return $items;
}

function does_warband_code_exist($warband_code)
{
    $args = array(
        'post_type' => 'warband',
        'post_status' => 'publish',
        'meta_key' => 'leg_wb_code',
        'meta_value' => $warband_code);

    $warbands = get_posts($args);
    return (count($warbands) > 0);
}

