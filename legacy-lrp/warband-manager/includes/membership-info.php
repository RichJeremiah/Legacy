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

<div>
    <p>There are currently <?= $warband_manager->get_current_member_count() ?> users recorded as members</p>
    <?php if ($warband_manager->is_warband_open()) : ?>
        <p>Your Warband is currently open for anyone to join &nbsp;
            <span><button value="Make Private" class="wb-make-private btn-sm"
                          data-name=" <?= $warband_manager->get_warband_title() ?>"
                          data-wbid="<?= $warband_manager->get_warband_id() ?>">Make Private</button>
            </span>
        </p>
    <?php else : ?>
    <p>Membership of your warband is by request and approval only. &nbsp;
        <span><button value="Make Public" class="wb-make-public btn-sm"
                      data-name=" <?= $warband_manager->get_warband_title() ?>"
                      data-wbid="<?= $warband_manager->get_warband_id() ?>">Make Public</button></span></p>
    <p>For a user to request to join your warband they must perform the following steps:
    <ol>
        <li>Go to the Edit Profile Page</li>
        <li>In the Warband drop down choose \'Other (Warband Code)\'</li>
        <li>In the Warband Code box enter the following code: <?= $warband_manager->get_warband_code() ?> (This is a
            unique
            code for the <?= $warband_manager->get_warband_title() ?> Warband)
        </li>
    </ol>
    They will then appear in the list below where you can choose to Approve or Reject their request </p>
    <p> There are currently <?= count($warband_manager->get_users_awaiting_approval()) ?> users awaiting approval </p>
</div>
<div class="warband-user-table ">
    <div class="row">
        <div class="col"> User</div>
        <div class="col"> Approve ?</div>
    </div>

    <?php foreach ($warband_manager->get_users_awaiting_approval() as $user) : ?>
        <div id="req-<?= $user->ID ?>" class="row alert alert-fix alert-dismissible fade show" role="alert">
            <div class="col"> <?= $user->display_name ?></div>
            <div class="col">
                <button value="Approve" class="wb-user-approve btn btn-success"
                        data-id="<?= $user->ID ?>"
                        data-wbcode="<?= $warband_manager->get_warband_code() ?>"
                        data-wbid="<?= $warband_manager->get_warband_Id() ?>">Approve
                </button>
                <button value="Reject" class="wb-user-reject btn btn-danger"
                        data-id="<?= $user->ID ?>">Reject
                </button>
            </div>
        </div>
    <?php endforeach; ?>
    <?php endif ?>
</div>
