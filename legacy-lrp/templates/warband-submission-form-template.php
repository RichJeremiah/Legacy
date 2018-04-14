<?php
/* Template Name: WarbandSubmissionForm
 * Created by PhpStorm.
 * User: Richard
 * Date: 17/12/2017
 * Time: 21:35
 */
get_header(); ?>

    <div id="primary" class="content-area">
        <main id="main" class="site-main" role="main">
<!--            --><?php //include './common/includes/bootstrap-links.html' ?>
<!--            <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css"-->
<!--                  integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb"-->
<!--                  crossorigin="anonymous">-->
<!--            <link rel="stylesheet" id="nivoslider-theme-bar-css"-->
<!--                  href="--><?php //echo get_option('siteurl') ?><!--/wp-content/plugins/nivo-slider-lite/assets/themes/bar/bar.css?ver=2.1.0"-->
<!--                  type="text/css" media="all">-->
<!--            <link rel="stylesheet" id="nivo-ns-nivoslider-css"-->
<!--                  href="--><?php //echo get_option('siteurl') ?><!--/wp-content/plugins/nivo-slider-lite/assets/css/nivo-slider.css?ver=2.1.0"-->
<!--                  type="text/css" media="all">-->

<!--            --><?php //include './common/includes/bootstrap-links.html' ?>
            <?php while (have_posts()) : the_post();

                do_action('storefront_page_before');

                get_template_part('content', 'page');

                /**
                 * Functions hooked in to storefront_page_after action
                 *
                 * @hooked storefront_display_comments - 10
                 */
                do_action('storefront_page_after');

            endwhile; // End of the loop.

            //output of successful post
            if (isset($_REQUEST['warband_add_notice'])) {
                if ($_REQUEST['warband_add_notice'] === "success") {
                    $html = '<div class="alert alert-success">
							Your warband request has been successfully sent';
                    $html .= '</div>';
                    echo $html;
                } else {
                    $html = '<div class="alert alert-danger">
							The request was not successful.  If this issue persists, contact an Administrator';
                    $html .= '</div>';
                    echo $html;
                }

                // handle other types of form notices
                //echo esc_url( admin_url( 'admin-post.php' ) );
            } else {
            //wp.ajax.settings.url
            ?>

            <style>
                .existing-name-warning {
                    display: none;
                    margin-top: .25rem;
                    font-size: .875rem;
                    color: #dc3545;
                }

            </style>
            <script>
                (function () {
                    'use strict';

                    window.addEventListener('load', function () {

                        var warbandName = $('#warband-name');
                        var message = $('.existing-name-warning');
                        warbandName.on('change', function () {
                            var name = warbandName.val();
                            if (typeof wp === 'undefined') {
                                if (name === "Warband Test" || name === '') {
                                    warbandName[0].setCustomValidity('');
                                    warbandName.removeClass('is-invalid').addClass('is-valid');
                                    message.hide();
                                }
                                else {
                                    warbandName[0].setCustomValidity("Invalid Warband Name");
                                    warbandName.removeClass('is-valid').addClass('is-invalid');
                                    message.show();
                                }
                            } else {
                                var data = {
                                    action: 'check_warband_name',
                                    warband_name: warbandName.val()
                                };
                                jQuery.post(ajaxurl, data, function (response) {
                                    console.log('Result: ' + response);
                                    if (response === "exists") {
                                        warbandName[0].setCustomValidity("Invalid Warband Name");
                                        warbandName.removeClass('is-valid').addClass('is-invalid');
                                        message.show();
                                    }
                                    else {
                                        warbandName[0].setCustomValidity('');
                                        warbandName.removeClass('is-invalid').addClass('is-valid');
                                        message.hide();
                                    }
                                });
                            }
                        });

                        var deityDD = $('#warband-deity');
                        deityDD.on('change', function () {
                            var choice = deityDD.val();
                            var deitySection = $('#deity-section');
                            if (choice === "-1") {
                                $('#warband-deity-name').prop('required', true);
                                $('#warband-deity-desc').prop('required', true);
                                $('#warband-deity-smdesc').prop('required', true);
                                deitySection.show();
                            } else {
                                $('#warband-deity-name').prop('required', false);
                                $('#warband-deity-desc').prop('required', false);
                                $('#warband-deity-smdesc').prop('required', false);
                                deitySection.hide()
                            }
                        });

                        var form = document.getElementById('warband-form');
                        form.addEventListener('submit', function (event) {
                            if (form.checkValidity() === false) {
                                event.preventDefault();
                                event.stopPropagation();
                            }
                            form.classList.add('was-validated');
                        }, false);
                    }, false);


                })();

            </script>

            <div class="container content-area ">
                <form method="POST" action="" id="warband-form" class="" novalidate>
                    <h3>
                        IC Details
                    </h3>
                    <input type="hidden" name="action" value="warband_submission_hook"/>
                    <?php $warband_add_meta_nonce = wp_create_nonce('leg_add_warband_meta_form_nonce'); ?>
                    <input type="hidden" name="warband_add_meta_nonce"
                           value="<?php echo $warband_add_meta_nonce ?>"/>
                    <div class="form-group">
                        <label for="warband-name">Warband Name</label>
                        <small id="warbandNameHelpBlock" class="form-text text-muted">
                            Please enter a name for the warband - this must be unique in the setting
                        </small>
                        <input class="form-control" type="text" id="warband-name" name="warband-name"
                               aria-describedby="warbandNameHelpBlock"
                               required>
                        <div class="invalid-feedback">
                            Please provide a valid Warband Name.
                        </div>
                        <div class="existing-name-warning">
                            That Warband Name is already taken
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="warband-desc">Warband Description</label>
                        <small id="warbandDescHelpBlock" class="form-text text-muted">
                            Please include all relevant details in the description e.g. the warband’s leadership
                            structure, how the warband fits into the setting, alignments, allies, enemies
                        </small>
                        <textarea class="form-control" rows="10" cols="100" id="warband-desc"
                                  aria-describedby="warbandDescHelpBlock"
                                  name="warband-desc" required maxlength="5000" minlength="100"></textarea>
                        <div class="invalid-feedback">
                            Please provide a description for your warband (Min 100 character, Max 5000 characters)
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="warband-smdesc">Warband Public Description</label>
                        <small id="warbandSmDescHelpBlock" class="form-text text-muted">
                            Please provide a short description for your warband which can be used as part of the public
                            description available on the Warbands page
                        </small>
                        <textarea class="form-control" rows="5" cols="100" id="warband-smdesc"
                                  aria-describedby="warbandSmDescHelpBlock"
                                  name="warband-smdesc" required maxlength="1000" minlength="50"></textarea>
                        <div class="invalid-feedback">
                            Please provide a short public description for your warband (Min 50 characters, Max 1000
                            characters).
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="warband-region">Select Region</label>
                        <small id="warbandRegionHelpBlock" class="form-text text-muted">
                            Please select the approximate location for the warband i.e. North, East, South or West
                        </small>
                        <select class="form-control" id="warband-region" name="warband-region"
                                aria-describedby="warbandRegionHelpBlock"
                                required>
                            <option value="">Please select Region</option>
                            <?php
                            $regions = getRegions();
                            foreach ($regions as $key => $value):
                                echo '
                <option value="' . $key . '">' . $value . '</option>
                ';
                            endforeach;
                            ?>
                        </select>
                        <div class="invalid-feedback">
                            Please select a region for the Warband
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="warband-deity">Select Deity</label>
                        <small id="warbandDeityHelpBlock" class="form-text text-muted">
                            Please select the initial primary deity for the warband, or choose other to include
                            information for a new deity
                        </small>
                        <select class="form-control" id="warband-deity" name="warband-deity"
                                aria-describedby="warbandDeityHelpBlock" required>
                            <option value="">Please select a Deity</option>
                            <option value="0">None</option>
                            <?php
                            $deities = getDeities();
                            // echo $deities;
                            foreach ($deities as $key => $value):
                                echo '
                <option value="' . $key . '">' . $value . '</option>
                '; //close your tags!!
                            endforeach;
                            ?>
                            <option value="-1">Other - Please Specify</option>
                        </select>
                    </div>
                    <div class="collapse" id="deity-section">
                        <div class="card card-body">
                            <h4 class="card-title">Deity Details</h4>

                            <div class="form-group">
                                <label for="warband-deity-name">Name</label>
                                <small id="warbandDeityNameHelpBlock" class="form-text text-muted">
                                    Please provide the name your warband uses to refer to this Deity
                                </small>
                                <input class="form-control" type="text" id="warband-deity-name"
                                       aria-describedby="warbandDeityNameHelpBlock"
                                       name="warband-deity-name">
                                <div class="invalid-feedback">
                                    Please provide a name for your deity
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="warband-deity-desc">Description</label>
                                <small id="warbandDeityDescHelpBlock" class="form-text text-muted">
                                    Please provide a description about the deity including any relevant details
                                </small>
                                <textarea class="form-control" rows="10" cols="100" id="warband-deity-desc"
                                          name="warband-deity-desc" maxlength="2500"
                                          aria-describedby="warbandDeityDescHelpBlock"></textarea>
                                <div class="invalid-feedback">
                                    Please provide a description for your deity (Max 2500 characters)
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="warband-deity-smdesc">Deity Public Description</label>
                                <small id="warbandDeitySmDescHelpBlock" class="form-text text-muted">
                                    Please provide a short description for your deity which can be used as part of the
                                    public description available on the Deities page
                                </small>
                                <textarea class="form-control" rows="5" cols="100" id="warband-deity-smdesc"
                                          aria-describedby="warbandDeitySmDescHelpBlock"
                                          name="warband-deity-smdesc" maxlength="1000" minlength="50"></textarea>
                                <div class="invalid-feedback">
                                    Please provide a short public description for your deity (Min 50 characters, Max
                                    1000 characters).
                                </div>
                            </div>
                        </div>
                    </div>
                    <h3>
                        Out of Character Details
                    </h3>
                    <div class="form-group">
                        <label for="warband-p-cont">Primary OOC Contact</label>
                        <input class="form-control" type="text" id="warband-p-cont" name="warband-p-cont" required>
                        <div class="invalid-feedback">
                            Please provide a primary named contact
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="warband-s-cont">Seconday OOC Contact</label>
                        <input class="form-control" type="text" id="warband-s-cont" name="warband-s-cont" required>
                        <div class="invalid-feedback">
                            Please provide a secondary named contact
                        </div>
                    </div>
                    <div class="form-check">
                        <label class="form-check-label">
                            <input type="checkbox" class="form-check-input" id="warband-isPublic"
                                   name="warband-isPublic"> Check this box to make information about this Warband
                            publicly available
                        </label>
                    </div>
                    <div class="form-check">
                        <label class="form-check-label">
                            <input type="checkbox" class="form-check-input" id="warband-isOpen"
                                   name="warband-isOpen"> Check this box to make this Warband available for anyone to
                            join.
                        </label>
                    </div>
                    <button class="btn btn-primary" type="submit">Submit form</button>
                </form>
            </div>

        </main><!-- #main -->
    </div><!-- #primary -->

    <?php
} ?>
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
//get_footer();
?>