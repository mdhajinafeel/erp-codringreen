<?php
$session = $this->session->userdata("fullname");
$applicable_origins = $session["applicable_origins"];
?>
<?php $site_lang = $this->load->helper("language"); ?>
<?php $wz_lang = $site_lang->session->userdata("site_lang"); ?>
<div class="card mb-3">
    <div class="card-header table-responsive">
        <div class="row flex-between-end">
            <div class="col-auto align-self-center">
                <input type="hidden" id="hdnCsrf" name="hdnCsrf" value="<?php echo $csrf_cgrerp; ?>" />
                <h3> <?php echo $this->lang->line("liquidationreport_title"); ?> </h3>
            </div>
        </div>
    </div>
    <div class="card-body pt-2">
        <div class="mb-3 row">
            <label class="col-sm-2 col-form-label lbl-font" for="origin_liquidationreport"><?php echo $this->lang->line("origin"); ?></label>
            <div class="col-sm-10">
                <select class="form-control" name="origin_liquidationreport" id="origin_liquidationreport" data-plugin="select_erp">
                    <?php if (count($applicable_origins) > 1) { ?>
                        <?php foreach ($applicable_origins as $origin) { ?>
                            <option value="<?php echo $origin->id; ?>"><?php echo $origin->origin_name; ?></option>
                        <?php } ?>
                    <?php } else { ?>
                        <?php foreach ($applicable_origins as $origin) { ?>
                            <option value="<?php echo $origin->id; ?>"><?php echo $origin->origin_name; ?></option>
                        <?php } ?>
                    <?php } ?>
                </select>
                <div class="mb-4 row"></div>
            </div>

            <!-- <div class="row flex-between-end">
                <div class="col-md-10 ms-auto">
                    <button class="btn btn-danger btn-block ml-10" title="<?php echo $this->lang->line("reset"); ?>" type="button" id="btn_reset_liquidation">
                        <span class="ms-1"><?php echo $this->lang->line("reset"); ?></span></button>
                </div>
            </div> -->
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        
        <div class="row flex-between-end">
            <div class="col-auto ms-auto">
                <button class="btn btn-primary btn-md btn-right-margin" title="<?php echo $this->lang->line('download_reports'); ?>" type="button" id="generate_liquidation_report">
                    <span class="fas fa-download" data-fa-transform="shrink-3 down-2"></span><span class="ms-1">
                        <?php echo $this->lang->line('download_reports'); ?></span>
                </button>
            </div>
        </div>
        
        <div class="tab-margin">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="warehouse-tab" data-bs-toggle="tab" href="#tab-warehouse" role="tab" aria-controls="tab-home" aria-selected="true">
                        <?php echo $this->lang->line("warehouse"); ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="fieldpurchase-tab" data-bs-toggle="tab" href="#tab-fieldpurchase" role="tab" aria-controls="tab-profile" aria-selected="false">
                        <?php echo $this->lang->line("field_purchase"); ?></a>
                </li>
            </ul>
            <div class="tab-content border-x border-bottom p-3" id="myTabContent">
                <div class="tab-pane fade show active" id="tab-warehouse" role="tabpanel" aria-labelledby="warehouse-tab">
                    <table class="table table-striped table-bordered" id="xin_table_wh_liquidation" style="width: 100% !important;">
                        <thead>
                            <tr>
                                <th><?php echo $this->lang->line("action"); ?></th>
                                <th><?php echo $this->lang->line("contract_code"); ?></th>
                                <th><?php echo $this->lang->line("supplier_name"); ?></th>
                                <th><?php echo $this->lang->line("product"); ?></th>
                                <th><?php echo $this->lang->line("total_volume"); ?></th>
                                <th><?php echo $this->lang->line("origin"); ?></th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div class="tab-pane fade" id="tab-fieldpurchase" role="tabpanel" aria-labelledby="fieldpurchase-tab">
                    <table class="table table-striped table-bordered" id="xin_table_field_liquidation" style="width: 100% !important;">
                        <thead>
                            <tr>
                                <th><?php echo $this->lang->line("action"); ?></th>
                                <th><?php echo $this->lang->line("contract_code"); ?></th>
                                <th><?php echo $this->lang->line("purchasemanagername_title"); ?></th>
                                <th><?php echo $this->lang->line("product"); ?></th>
                                <th><?php echo $this->lang->line("total_volume"); ?></th>
                                <th><?php echo $this->lang->line("origin"); ?></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url() . 'assets/js/jquery341.min.js'; ?>"></script>
<script src="<?php echo base_url() . "assets/js/jquery.dataTables.min.js"; ?>"></script>
<script src="<?php echo base_url() . "assets/js/dataTables.bootstrap.min.js"; ?>"></script>
<link rel="stylesheet" href="<?php echo base_url() . 'assets/css/jquery-ui.css'; ?>">
<script src="<?php echo base_url() . 'assets/js/jquery-ui.js'; ?>"></script>