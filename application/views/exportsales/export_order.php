<?php
$session = $this->session->userdata('fullname');
$applicable_origins = $session["applicable_origins"];
?>
<?php $site_lang = $this->load->helper('language'); ?>
<?php $wz_lang = $site_lang->session->userdata('site_lang'); ?>
<div class="modal fadeInRight animated" data-bs-keyboard="false" data-bs-backdrop="static" tabindex="-1" id="alert-dialog-info" role="dialog" aria-labelledby="alert-dialog-info" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="titlehead"></h4>
                <?php echo form_button(array('aria-label' => 'Close', 'data-bs-dismiss' => 'modal', 'type' => 'button', 'class' => 'close', 'content' => '<span aria-hidden="true">×</span>')); ?>
            </div>
            <?php $attributes = array('name' => 'action_button', 'id' => 'action_button', 'autocomplete' => 'off', 'class' => '"m-b-1'); ?>
            <?php echo form_open(); ?>
            <div class="modal-body farm-modal">
                <div class="sa-icon sa-error fadeIn animated" style="display: block;">
                    <span class="sa-line sa-left"></span>
                    <span class="sa-line sa-right"></span>
                </div>
                <h5 class="text-center modal-message" id="infomessage"></h5>
            </div>
            <div class="modal-footer">
                <?php echo form_button(array('data-bs-dismiss' => 'modal', 'type' => 'button', 'class' => 'btn btn-secondary', 'content' => $this->lang->line('ok'))); ?>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<div class="card mb-3">

    <input type="hidden" id="hdnCsrf" name="hdnCsrf" value="<?php echo $csrf_hash; ?>">
    <div class="card-header table-responsive">
        <div class="row flex-between-end">
            <div class="col-auto align-self-center">
                <h3> <?php echo $this->lang->line("exportorder_title"); ?> </h3>
            </div>
        </div>
    </div>
    <div class="card-body pt-0">

        <div class="row mb-3">
            <div class="col-md-4 align-self-center">
                <label for="origin_export_order"><?php echo $this->lang->line("origin"); ?></label>
                <select class="form-control" name="origin_export_order" id="origin_export_order" data-plugin="select_erp">
                    <?php $i = 0;
                    foreach ($applicable_origins as $origin) { ?>
                        <option value="<?php echo $origin->id; ?>" <?php if ($i == 0) { ?> selected="selected" ; <?php } ?>><?php echo $origin->origin_name; ?></option>
                    <?php $i++;
                    } ?>
                </select>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-3 align-self-center">
                <label for="product_type"><?php echo $this->lang->line("product_type"); ?></label>
                <select class="form-control" name="product_type" id="product_type" data-plugin="select_erp">
                    <option value="0"><?php echo $this->lang->line("all"); ?></option>
                    <?php foreach ($producttypes as $producttype) { ?>
                        <option value="<?php echo $producttype->type_id; ?>"><?php echo $producttype->product_type_name; ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="col-md-3 align-self-center">
                <label for="shipping_line"><?php echo $this->lang->line("shipping_line"); ?></label>
                <select class="form-control" name="shipping_line" id="shipping_line" data-plugin="select_erp">
                    <option value="0"><?php echo $this->lang->line("all"); ?></option>
                    <?php foreach ($shippinglines as $shippingline) { ?>
                        <option value="<?php echo $shippingline->id; ?>"><?php echo $shippingline->shipping_line; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <table class="datatables-demo table table-striped table-bordered" id="xin_table_export_order" style="width: 100% !important;">
            <thead>
                <tr>
                    <th width="100px"><?php echo $this->lang->line("action"); ?></th>
                    <th><?php echo $this->lang->line("sa_number"); ?></th>
                    <th><?php echo $this->lang->line("product_type"); ?></th>
                    <th><?php echo $this->lang->line("shipping_line"); ?></th>
                    <th><?php echo $this->lang->line("port_of_loading"); ?></th>
                    <th><?php echo $this->lang->line("port_of_discharge"); ?></th>
                    <th><?php echo $this->lang->line("total_containers"); ?></th>
                    <th><?php echo $this->lang->line("total_no_of_pieces"); ?></th>
                    <th><?php echo $this->lang->line("volume"); ?></th>
                    <th><?php echo $this->lang->line("origin"); ?></th>
                </tr>
            </thead>

        </table>
    </div>
</div>
<script src="<?php echo base_url() . "assets/js/jquery341.min.js"; ?>"></script>
<script src="<?php echo base_url() . "assets/js/jquery.dataTables.min.js"; ?>"></script>
<script src="<?php echo base_url() . "assets/js/dataTables.bootstrap.min.js"; ?>"></script>
<link rel="stylesheet" href="<?php echo base_url() . "assets/css/jquery-ui.css"; ?>">
    <script src="<?php echo base_url() . "assets/js/jquery-ui.js"; ?>"></script>