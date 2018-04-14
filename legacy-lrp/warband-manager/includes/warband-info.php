<?php
/**
 * Created by PhpStorm.
 * User: Richard
 * Date: 25/01/2018
 * Time: 17:07
 */
require_once('WarbandManager.php');
$warband_manager = WarbandManager::get_instance();//WarbandManager::get_instance();
?>
<h4>Region: <?= $warband_manager->get_warband_region() ?> </h4>
<h4>Deity: <?= $warband_manager->get_warband_deity_name() ?></h4>
<div class="leg-wb-deity-description">
    <?= $warband_manager->get_warband_deity_description() ?>
</div>
<h4>Full Description</h4>
<div id="warband-description"> <?= $warband_manager->get_warband_description() ?></div>
<div style="display:none;">Warband iD = <?= $warband_manager->get_warband_id() ?></div>
