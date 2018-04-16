<?php
/**
 * Template Name: Warband Manager
 * Created by PhpStorm.
 * User: Richard
 * Date: 25/01/2018
 * Time: 17:07
 */
require_once(dirname(__DIR__).'/warband-manager/WarbandManager.php');
$warband_manager = WarbandManager::get_instance();
get_header(); ?>

<div id="primary" class="content-area">
    <div id="main" class="site-main" role="main">
        <?php include dirname(__FILE__).'/common/includes/bootstrap-links.html' ?>
<!--        --><?php //include '../common/includes/nivo-slider-links.html' ?>
        <link rel="stylesheet" id="warband-styles"
              href="<?php echo get_option('siteurl') ?>/wp-content/plugins/legacy-lrp/css/warband-manager.css"
              type="text/css" media="all">
        <div style="display: none;">
            <!--    CURRENTLY HIDDEN FOR REASONS OF LAYOUT-->
            <?php while (have_posts()) : the_post();

                do_action('storefront_page_before');

                get_template_part('content', 'page');

                //do_action('storefront_page_after');
                echo 'WORDS';
            endwhile; ?>
        </div>

        <div>Hello <?= wp_get_current_user()->user_login ?></div>
        <?php if (!$warband_manager->is_warband_owner() && !$warband_manager->is_warband_member()) : ?>
            <div>You are not a member of any Warbands - please consider joining a warband as they can provide a lot
                of opportunities to interact with and enjoy the Legacy game world
            </div>
        <?php else : ?>
            <div id="main-container">
                <?php if ($warband_manager->is_warband_owner()) : ?>
                    <h2>Warband Management</h2>
                    <h3>Warband Name: <?= $warband_manager->get_warband_title() ?></h3>
                <?php else : ?>
                    <h2> <?= $warband_manager->get_warband_title() ?> </h2>
                    <h3>You are a member of this Warband</h3>
                <?php endif; ?>
                <div id="container">
                    <ul class="nav nav-tabs nav-fix" id="wbTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="warband-tab" data-toggle="tab" href="#warband" role="tab"
                               aria-controls="warband" aria-selected="true">Warband Info</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link " id="deity-tab" data-toggle="tab" href="#deity" role="tab"
                               aria-controls="deity" aria-selected="false">Deity Info</a>
                        </li>
                        <?php if ($warband_manager->is_warband_owner()) : ?>
                            <li class="nav-item">
                                <a class="nav-link " id="membership-tab" data-toggle="tab" href="#membership" role="tab"
                                   aria-controls="membership" aria-selected="false">Membership</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="settlements-tab" data-toggle="tab" href="#settlements"
                                   role="tab" aria-controls="settlements" aria-selected="false">Settlements</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                    <div class="tab-content" id="wbTabContent">
                        <div class="tab-pane fade show active" id="warband" role="tabpanel" aria-labelledby="warband-tab">
                            <div id="wb-content-1" class="panel container-fluid">
                                <?php include dirname(__DIR__).'/warband-manager/includes/warband-info.php' ?>
                            </div>
                        </div>
                        <div class="tab-pane fade " id="deity" role="tabpanel" aria-labelledby="deity-tab">
                            <div id="wb-content-2" class="panel container-fluid">

                                <?php include dirname(__DIR__).'/warband-manager/includes/deity-info.php' ?>
                            </div>
                        </div>
                        <?php if ($warband_manager->is_warband_owner()) : ?>
                            <div class="tab-pane fade" id="membership" role="tabpanel" aria-labelledby="membership-tab">
                                <div id="wb-content-3" class="panel container-fluid">

                                    <?php include dirname(__DIR__).'/warband-manager/includes/membership-info.php' ?>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="settlements" role="tabpanel"
                                 aria-labelledby="settlements-tab">
                                <div id="wb-content-4" class="panel container-fluid">

                                    <?php include dirname(__DIR__).'/warband-manager/includes/settlement-info.php' ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.2.1.min.js"
        integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
        crossorigin="anonymous"></script>
<script src="<?php echo get_option('siteurl') ?>/wp-content/plugins/nivo-slider-lite/assets/js/jquery.nivo.slider.pack.js?ver=2.1.0"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js"
        integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh"
        crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js"
        integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ"
        crossorigin="anonymous"></script>
<?php
do_action('storefront_sidebar');
get_footer(); ?>
