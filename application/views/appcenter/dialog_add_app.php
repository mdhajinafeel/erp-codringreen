<?php
defined('BASEPATH') or exit('No direct script access allowed');

$session = $this->session->userdata('fullname');
$applicable_origins = $session["applicable_origins"];
$site_lang = $this->load->helper('language');
$wz_lang = $site_lang->session->userdata('site_lang');
?>

<div class="modal-header">
    <h4 class="modal-title" id="add-modal-data"><?php echo $pageheading; ?></h4>
    <?php echo form_button(array('aria-label' => 'Close', 'data-bs-dismiss' => 'modal', 'type' => 'button', 'class' => 'close', 'content' => '<span aria-hidden="true">×</span>')); ?>
</div>

<div class="modal-body farm-modal">
    <input type="hidden" id="pagetype" name="pagetype" value="<?php echo $pagetype; ?>">
    <input type="hidden" id="hdnAppId" name="hdnAppId" value="0">
    <input type="hidden" id="hdnCsrf" name="hdnCsrf" value="<?php echo $csrfcgr; ?>">

    <div class="row mb-3">
        <div class="col-md-6 mb-2">
            <label for="origin_extraction"><?php echo $this->lang->line('origin'); ?></label>
            <select class="form-control" name="origin_extraction" id="origin_extraction" data-plugin="select_erp">
                <option value="0"><?php echo $this->lang->line("select"); ?></option>
                <?php foreach ($applicable_origins as $origin) { ?>
                    <?php if ($extractionDetails[0]->origin_id == $origin->id) { ?>
                        <option value="<?php echo $origin->id; ?>" selected="selected"><?php echo $origin->origin_name; ?></option>
                    <?php } else { ?>
                        <option value="<?php echo $origin->id; ?>"><?php echo $origin->origin_name; ?></option>
                    <?php } ?>
                <?php } ?>
            </select>
            <label id="error-origin" class="error-text"><?php echo $this->lang->line('error_origin_screen'); ?></label>
        </div>

        <div class="col-md-6">
            <label for="supplier_extraction"><?php echo $this->lang->line('supplier_name'); ?></label>
            <select class="form-control" name="supplier_extraction" id="supplier_extraction" data-plugin="select_erp" disabled>
                <option value="0"><?php echo $this->lang->line("select"); ?></option>

                <?php if ($pagetype == "edit") { ?>
                    <?php foreach ($suppliers as $supplier) { ?>
                        <option value="<?php echo $supplier->id; ?>" <?php if ($extractionDetails[0]->supplier_id == $supplier->id) : ?> selected="selected" <?php endif; ?>><?php echo $supplier->supplier_name; ?></option>
                    <?php } ?>
                <?php } ?>
            </select>
            <label id="error-name" class="error-text"><?php echo $this->lang->line('error_select_name'); ?></label>
        </div>
    </div>
</div>