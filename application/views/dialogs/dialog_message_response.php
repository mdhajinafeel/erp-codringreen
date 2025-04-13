<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<?php
$session = $this->session->userdata('fullname');
$applicable_origins = $session["applicable_origins"];
?>
<?php $site_lang = $this->load->helper('language'); ?>
<?php $wz_lang = $site_lang->session->userdata('site_lang'); ?>
<div class="modal fadeInRight animated" data-bs-keyboard="false" data-bs-backdrop="static" tabindex="-1" id="add-modal-data-bd1" role="dialog" aria-labelledby="add-modal-data-bd1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="titlehead"></h4>
                <?php echo form_button(array('aria-label' => 'Close', 'data-bs-dismiss' => 'modal', 'type' => 'button', 'class' => 'close', 'content' => '<span aria-hidden="true">×</span>')); ?>

            </div>
            <?php $attributes = array('name' => 'action_button', 'id' => 'action_button', 'autocomplete' => 'off', 'class' => '"m-b-1'); ?>
            <?php echo form_open(); ?>
            <div class="modal-body farm-modal">
                <div class="sa-icon sa-warning fadeIn animated" style="display: block;">
                    <span class="sa-body"></span>
                    <span class="sa-dot"></span>
                </div>
                <h5 class="text-center modal-message" id="infomessage"></h5>
            </div>
        </div>
        <div class="modal-footer">
            <?php echo form_button(array('data-bs-dismiss' => 'modal', 'type' => 'button', 'class' => 'btn btn-secondary', 'content' => $this->lang->line('ok'))); ?>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>