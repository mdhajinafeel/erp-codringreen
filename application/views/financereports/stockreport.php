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
                <input type="hidden" id="hdnCsrf" name="hdnCsrf" value="<?php echo $csrf_cgrerp; ?>" />
                <h3> <?php echo $this->lang->line('stockreport_title'); ?> </h3>
            </div>
        </div>
    </div>
    <div class="card-body pt-5">
        <div class="mb-3 row">
            <label class="col-sm-2 col-form-label lbl-font" for="origin_stockreport"><?php echo $this->lang->line('origin'); ?></label>
            <div class="col-sm-10">
                <select class="form-control" name="origin_stockreport" id="origin_stockreport" data-plugin="select_erp">
                        <option value="0"><?php echo $this->lang->line('select'); ?></option>
                        <?php foreach ($applicable_origins as $origin) { ?>
                            <option value="<?php echo $origin->id; ?>"><?php echo $origin->origin_name; ?></option>
                        <?php } ?>
                </select>
                <div class="mb-4 row"></div>
            </div>

            <div class="row flex-between-end">
                <div class="col-md-10 ms-auto">
                    <button class="btn btn-danger btn-block ml-10" title="<?php echo $this->lang->line("reset"); ?>" type="button" id="btn_reset">
                        <span class="ms-1"><?php echo $this->lang->line("reset"); ?></span></button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-3" id="divDataAvailable">
    <div class="card-body">
        <div class="row text-center">
            <div class="sa-icon sa-error fadeIn animated" style="display: block;">
                <span class="sa-line sa-left"></span>
                <span class="sa-line sa-right"></span>
            </div>
            <h4><span><?php echo $this->lang->line("no_data_available"); ?></span></h4>
        </div>
    </div>
</div>

<div class="card mb-3" id="divStockDetails">
    <div class="card-body">
        <div class="row">
            <div class="col-lg-3 border-lg-end border-bottom border-lg-0 pb-3 pb-lg-0">
                <div class="d-flex flex-between-center mb-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-item icon-item-md bg-soft-primary shadow-none me-2 bg-soft-primary"><span class="fs-1 ti ti-truck text-primary"></span></div>
                        <h6 class="mb-0"><?php echo $this->lang->line("total_inventory"); ?></h6>
                    </div>
                </div>
                <div class="d-flex">
                    <div class="d-flex">
                        <p class="font-sans-serif lh-1 mb-1 fs-2 fw-medium pe-2" id="txtTotalInventory"></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 border-lg-end border-bottom border-lg-0 py-3 py-lg-0">
                <div class="d-flex flex-between-center mb-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-item icon-item-md bg-soft-primary shadow-none me-2 bg-soft-info"><span class="fs-1 ti ti-package text-info"></span></div>
                        <h6 class="mb-0"><?php echo $this->lang->line("total_no_of_pieces"); ?></h6>
                    </div>
                </div>
                <div class="d-flex">
                    <div class="d-flex">
                        <p class="font-sans-serif lh-1 mb-1 fs-2 fw-medium pe-2" id="txtTotalPieces"></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 border-lg-end border-bottom border-lg-0 py-3 py-lg-0">
                <div class="d-flex flex-between-center mb-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-item icon-item-md bg-soft-primary shadow-none me-2 bg-soft-warning"><span class="fs-1 ti ti-dashboard text-warning"></span></div>
                        <h6 class="mb-0"><?php echo $this->lang->line("total_volume"); ?></h6>
                    </div>
                </div>
                <div class="d-flex">
                    <div class="d-flex">
                        <p class="font-sans-serif lh-1 mb-1 fs-2 fw-medium pe-2" id="txtTotalVolume"></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 pt-3 pt-lg-0">
                <div class="d-flex flex-between-center mb-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-item icon-item-md bg-soft-primary shadow-none me-2 bg-soft-success"><span class="fs-1 ti ti-money text-success"></span></div>
                        <h6 class="mb-0"><?php echo $this->lang->line("total_cost"); ?></h6>
                    </div>
                </div>
                <div class="d-flex">
                    <div class="d-flex">
                        <p class="font-sans-serif lh-1 mb-1 fs-2 fw-medium pe-2" id="txtTotalCosts"></p>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-3" id="divTransactions">
    <div class="card-body">

        <div class="row mb-3">
            <div class="col-auto ms-auto">
                <button class="btn btn-primary btn-block" title="<?php echo $this->lang->line("download_reports"); ?>" type="button" id="btn_download_reports">
                    <span class="fas fa-file-excel" data-fa-transform="shrink-3 down-2"></span><span class="ms-1"><?php echo $this->lang->line("download_reports"); ?></span></button>
            </div>
        </div>

        <div class="row">
            <table class="datatables-demo table table-responsive table-striped table-bordered" id="xin_table_stocks" style="width: 100% !important;">
                <thead>
                    <tr>
                        <th><?php echo $this->lang->line("inventory_order"); ?></th>
                        <th><?php echo $this->lang->line("supplier_name"); ?></th>
                        <th><?php echo $this->lang->line("remaining_pieces"); ?></th>
                        <th><?php echo $this->lang->line("remaining_volume"); ?></th>
                        <th><?php echo $this->lang->line("cost_of_wood"); ?></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<script src="<?php echo base_url() . 'assets/js/jquery341.min.js'; ?>"></script>
<script src="<?php echo base_url() . "assets/js/jquery.dataTables.min.js"; ?>"></script>
<script src="<?php echo base_url() . "assets/js/dataTables.bootstrap.min.js"; ?>"></script>