<?php
/**
 * Created by PhpStorm.
 * User: Richard
 * Date: 11/04/2018
 * Time: 20:57
 */
require_once(dirname(__DIR__).'/WarbandManager.php');
$warband_manager = WarbandManager::get_instance();
?>

<h4>Deity: <?php echo $warband_manager->get_warband_deity_name() ?></h4>
<div class="leg-wb-deity-description">
    <?php echo $warband_manager->get_warband_deity_description() ?>
</div>