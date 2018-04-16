<?php
/**
 * Created by PhpStorm.
 * User: Richard
 * Date: 25/01/2018
 * Time: 17:07
 */
require_once(dirname(__DIR__).'/WarbandManager.php');
$warband_manager = WarbandManager::get_instance();
?>
<h3><?php echo $warband_manager->debug_output() ?></h3>
<h4>Region: <?php echo $warband_manager->get_warband_region() ?> </h4>
<h4>Deity: <?php echo $warband_manager->get_warband_deity_name() ?></h4>
<div class="leg-wb-deity-description">
    <?php echo $warband_manager->get_warband_deity_description() ?>
</div>
<h4>Full Description</h4>
<div id="warband-description"> <?php echo $warband_manager->get_warband_description() ?></div>
<div style="display:none;">Warband iD = <?php echo $warband_manager->get_warband_id() ?></div>
