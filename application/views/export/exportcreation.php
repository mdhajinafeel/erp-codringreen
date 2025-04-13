<?php
$session = $this->session->userdata('fullname');
$applicable_origins = $session["applicable_origins"];
?>
<?php $site_lang = $this->load->helper('language'); ?>
<?php $wz_lang = $site_lang->session->userdata('site_lang'); ?>
<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
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
                <div class="sa-icon sa-warning fadeIn animated" style="display: block;">
                    <span class="sa-body"></span>
                    <span class="sa-dot"></span>
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
        <input type="hidden" id="hdnCsrf" name="hdnCsrf" value="<?php echo $csrfhash; ?>">
        <div class="card-header table-responsive">
            <div class="row flex-between-end">
                <div class="col-auto align-self-center">
                    <h3> <?php echo $this->lang->line("readyforexport_title"); ?> </h3>
                </div>
            </div>
        </div>
        <div class="card-body pt-0">
            <div class="row mb-3">
                <div class="col-md-4 align-self-center">
                    <label for="origin"><?php echo $this->lang->line("origin"); ?></label>
                    <select class="form-control" name="origin" id="origin" data-plugin="select_erp">
                        <?php $i = 0;
                        foreach ($applicable_origins as $origin) { ?>
                            <option value="<?php echo $origin->id; ?>" <?php if ($i == 0) { ?> selected="selected" ; <?php } ?>><?php echo $origin->origin_name; ?></option>
                        <?php $i++;
                        } ?>
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4 align-self-center">
                    <label for="product_name"><?php echo $this->lang->line("product_name"); ?></label>
                    <select class="form-control" name="product_name" id="product_name" data-plugin="select_erp">
                        <option value="0"><?php echo $this->lang->line("all"); ?></option>
                        <?php foreach ($products as $product) { ?>
                            <option value="<?php echo $product->product_id; ?>"><?php echo $product->product_name; ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div class="col-md-4 align-self-center">
                    <label for="product_type"><?php echo $this->lang->line("product_type"); ?></label>
                    <select class="form-control" name="product_type" id="product_type" data-plugin="select_erp">
                        <option value="0"><?php echo $this->lang->line("all"); ?></option>
                        <?php foreach ($producttypes as $producttype) { ?>
                            <option value="<?php echo $producttype->type_id; ?>"><?php echo $producttype->product_type_name; ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div class="col-auto ms-auto mt-2 align-self-center">
                    <button class="btn btn-primary btn-md btn-right-margin" title="<?php echo $this->lang->line('download_summary'); ?>" type="button" id="generate_summary_report">
                        <span class="fas fa-file-excel" data-fa-transform="shrink-3 down-2"></span><span class="ms-1">
                            <?php echo $this->lang->line('download_summary'); ?></span>
                    </button>
                    <button class="btn btn-success btn-md" title="<?php echo $this->lang->line('create_export'); ?>" type="button" id="btn_create_export">
                        <span class="fas fa-plus" data-fa-transform="shrink-3 down-2"></span><span class="ms-1">
                            <?php echo $this->lang->line('create_export'); ?></span>
                    </button>
                </div>
            </div>

            <table class="datatables-demo table table-striped table-bordered" id="xin_table_exportcreation" style="width: 100% !important;">
                <thead>
                    <tr>
                        <th class="align-middle white-space-nowrap">
                            <div class="form-check1">
                                <input class="form-check-input" type="checkbox" id="selectall" onClick="china_toggle(this);" />
                            </div>
                        </th>
                        <th><?php echo $this->lang->line("container_number"); ?></th>
                        <th><?php echo $this->lang->line("shipping_line"); ?></th>
                        <th><?php echo $this->lang->line("product"); ?></th>
                        <th><?php echo $this->lang->line("warehouse"); ?></th>
                        <th><?php echo $this->lang->line("pieces"); ?></th>
                        <th><?php echo $this->lang->line("volume"); ?></th>
                        <th><?php echo $this->lang->line("origin"); ?></th>
                    </tr>
                </thead>

            </table>
        </div>
    </div>
    <script src="<?php echo base_url() . "assets/js/jquery341.min.js"; ?>"></script>
    <script type="text/javascript">
        var error_select_container = "<?php echo $this->lang->line("error_select_container"); ?>"
    </script>
    <script src="<?php echo base_url() . "assets/js/jquery.dataTables.min.js"; ?>"></script>
    <script src="<?php echo base_url() . "assets/js/dataTables.bootstrap.min.js"; ?>"></script>
    <link rel="stylesheet" href="<?php echo base_url() . "assets/css/jquery-ui.css"; ?>">
    <script src="<?php echo base_url() . "assets/js/jquery-ui.js"; ?>"></script>

    <script src="<?php echo base_url() . "assets/js/typeahead.js"; ?>"></script>