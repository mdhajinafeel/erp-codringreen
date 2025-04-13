<?php
$session = $this->session->userdata('fullname');
$applicable_origins = $session["applicable_origins"];
?>
<?php $site_lang = $this->load->helper('language'); ?>
<?php $wz_lang = $site_lang->session->userdata('site_lang'); ?>
<div class="card mb-3">
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
                    <?php $i=0; foreach ($applicable_origins as $origin) { ?>
                        <option value="<?php echo $origin->id; ?>" <?php if($i == 0) {?> selected="selected";  <?php } ?>><?php echo $origin->origin_name; ?></option>
                    <?php $i++; } ?>
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
        </div>

        <table class="datatables-demo table table-striped table-bordered" id="xin_table_exportready" style="width: 100% !important;">
            <thead>
                <tr>
                    <th width="100px"><?php echo $this->lang->line("action"); ?></th>
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

<script src="<?php echo base_url() . "assets/js/jquery.dataTables.min.js"; ?>"></script>
<script src="<?php echo base_url() . "assets/js/dataTables.bootstrap.min.js"; ?>"></script>