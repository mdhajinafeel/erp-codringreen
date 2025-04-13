<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<?php $site_lang = $this->load->helper('language'); ?>
<?php $wz_lang = $site_lang->session->userdata('site_lang'); ?>
<div class="modal-header">
    <h4 class="modal-title" id="add-modal-data"><?php echo $pageheading; ?></h4>
    <?php echo form_button(array('aria-label' => 'Close', 'data-bs-dismiss' => 'modal', 'type' => 'button', 'class' => 'close', 'content' => '<span aria-hidden="true">×</span>')); ?>

</div>
<?php $attributes = array('name' => 'update', 'id' => 'update', 'autocomplete' => 'off', 'class' => '"m-b-1'); ?>
<?php $hidden = array('_method' => $pagetype); ?>
<?php echo form_open($dispatch_submit, $attributes, $hidden); ?>
<div class="modal-body">
    <input type="hidden" id="pagetype" name="pagetype" value="<?php echo $pagetype; ?>">

    <div class="row">
        <div class="col-md-6 mb-2">
            <label for="container_number"><?php echo $this->lang->line("container_number"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo isset($dispatch_details[0]->container_number) ? $dispatch_details[0]->container_number : ''; ?></label>
            </div>
        </div>
        <div class="col-md-6">
            <label class="head-label"><?php echo $this->lang->line("shipping_line"); ?></label>

            <div class="input-group">
                <label class="control-label"><?php echo isset($dispatch_details[0]->shipping_line) ? $dispatch_details[0]->shipping_line : ''; ?></label>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-2">
            <label class="head-label"><?php echo $this->lang->line("warehouse"); ?></label>

            <div class="input-group">
                <label class="control-label"><?php echo isset($dispatch_details[0]->warehouse_name) ? $dispatch_details[0]->warehouse_name : ''; ?></label>
            </div>
        </div>
        <div class="col-md-6">
            <label class="head-label"><?php echo $this->lang->line("product_title"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo $dispatch_details[0]->product_name . ' - ' . $this->lang->line($dispatch_details[0]->product_type_name); ?></label>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-2">
            <label class="head-label"><?php echo $this->lang->line("dispatch_date"); ?></label>

            <div class="input-group">
                <label class="control-label"><?php echo isset($dispatch_details[0]->dispatch_date) ? $dispatch_details[0]->dispatch_date : ''; ?></label>
            </div>
        </div>
        <div class="col-md-6">
            <label for="seal_number"><?php echo $this->lang->line("seal_number"); ?></label>

            <div class="input-group">
                <label class="control-label"><?php echo isset($dispatch_details[0]->seal_number) ? $dispatch_details[0]->seal_number : ''; ?></label>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-2">
            <label for="container_image_url"><?php echo $this->lang->line("container_image_url"); ?></label>
            <textarea name="container_image_url" id="container_image_url" maxlength="500" rows="3" class="form-control" placeholder="<?php echo $this->lang->line("container_image_url"); ?>" readonly><?php echo isset($dispatch_details[0]->container_pic_url) ? htmlspecialchars($dispatch_details[0]->container_pic_url) : ''; ?></textarea>
        </div>
        <div class="col-md-6">
            <label class="head-label"><?php echo $this->lang->line("upload_type"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo isset($dispatch_details[0]->is_special_uploaded) ? $dispatch_details[0]->is_special_uploaded : ''; ?></label>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-2">
            <label class="head-label"><?php echo $this->lang->line("total_no_of_pieces"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo isset($dispatch_details[0]->total_pieces) ? $dispatch_details[0]->total_pieces : ''; ?></label>
            </div>
        </div>
        <div class="col-md-6">
            <label class="head-label"><?php echo $this->lang->line("total_volume"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo isset($dispatch_details[0]->total_volume) ? ($dispatch_details[0]->total_volume + 0) : ''; ?></label>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-2">
            <label class="head-label"><?php echo $this->lang->line("uploaded_by"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo isset($dispatch_details[0]->uploadedby) ? $dispatch_details[0]->uploadedby : ''; ?></label>
            </div>
        </div>
        <div class="col-md-6">
            <label class="head-label"><?php echo $this->lang->line("origin"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo isset($dispatch_details[0]->origin) ? $dispatch_details[0]->origin : ''; ?></label>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <?php echo form_button(array('data-bs-dismiss' => 'modal', 'type' => 'button', 'class' => 'btn btn-secondary', 'content' => $this->lang->line("close"))); ?>
</div>
<?php echo form_close(); ?>