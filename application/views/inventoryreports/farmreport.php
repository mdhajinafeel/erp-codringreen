<?php
$session = $this->session->userdata('fullname');
$applicable_origins = $session["applicable_origins"];
?>
<?php $site_lang = $this->load->helper('language'); ?>
<?php $wz_lang = $site_lang->session->userdata('site_lang'); ?>
<div class="card mb-3">
    <div class="card-header">
        <div class="row flex-between-end">
            <div class="col-auto align-self-center">
                <input type="hidden" id="hdnCsrf" name="hdnCsrf" value="<?php echo $csrf_hash; ?>">
                <h3> <?php echo $this->lang->line("farmreport_title"); ?> </h3>
            </div>
        </div>
    </div>

    <div class="card-body pt-0 pb-5">
        <div class="row mb-4">
            <div class="col-md-4 align-self-center">
                <label for="origin_farmreport"><?php echo $this->lang->line("origin"); ?></label>
                <select class="form-control" name="origin_farmreport" id="origin_farmreport" data-plugin="select_erp">
                    <?php if (count($applicable_origins) > 1) { ?>
                        <option value="0"><?php echo $this->lang->line("select"); ?></option>
                        <?php foreach ($applicable_origins as $origin) { ?>
                            <option value="<?php echo $origin->id; ?>"><?php echo $origin->origin_name; ?></option>
                        <?php } ?>
                    <?php } else { ?>
                        <?php foreach ($applicable_origins as $origin) { ?>
                            <option value="<?php echo $origin->id; ?>" selected="selected"><?php echo $origin->origin_name; ?></option>
                        <?php } ?>
                    <?php } ?>
                </select>
                <label id="error-farmreportorigin" class="error-text"><?php echo $this->lang->line("error_origin_screen"); ?></label>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-md-4 mb-2 align-self-center">
                <label for="download_criteria_farmreport"><?php echo $this->lang->line("download_criteria"); ?></label>
                <select class="form-control" name="download_criteria_farmreport" id="download_criteria_farmreport" data-plugin="select_erp">
                    <option value="0"><?php echo $this->lang->line("select"); ?></option>
                    <option value="1"><?php echo $this->lang->line("suppliercredit_title"); ?></option>
                    <option value="2"><?php echo $this->lang->line("purchase_date"); ?></option>
                    <option value="3"><?php echo $this->lang->line("product_text"); ?></option>
                    <option value="4"><?php echo $this->lang->line("product_type"); ?></option>
                    <option value="5"><?php echo $this->lang->line("inventory_order"); ?></option>
                </select>
                <label id="error-farmreportdownload" class="error-text"><?php echo $this->lang->line("error_select_criteria"); ?></label>
            </div>

            <div class="col-md-4 mb-2 align-self-center" id="divSupplierNameFarmReports">
                <label for="supplier_name_farmrepport"><?php echo $this->lang->line("supplier_name"); ?></label>
                <select class="form-control" name="supplier_name_farmrepport" id="supplier_name_farmrepport" data-plugin="select_erp">
                    <option value="0"><?php echo $this->lang->line("select"); ?></option>
                </select>
                <label id="error-farmreportsupplier" class="error-text"><?php echo $this->lang->line("error_select_name"); ?></label>
            </div>

            <div class="col-md-4 mb-2 align-self-center" id="divInventoryOrderFarmReports">
                <label for="inventory_order_farmrepport"><?php echo $this->lang->line("inventory_order"); ?></label>
                <select class="form-control" name="inventory_order_farmrepport" id="inventory_order_farmrepport" data-plugin="select_erp">
                    <option value="0"><?php echo $this->lang->line("all"); ?></option>
                </select>
                <label id="error-farmreportinventory" class="error-text"><?php echo $this->lang->line("error_select_inventory_order"); ?></label>
            </div>

            <div class="col-md-4 mb-2 align-self-center" id="divStartDateFarmReports">
                <label for="start_date_farmreport"><?php echo $this->lang->line("start_date"); ?></label>
                <input type="text" id="start_date_farmreport" name="start_date_farmreport" class="form-control" placeholder="<?php echo $this->lang->line("start_date"); ?>" readonly />
                <label id="error-farmreportstartdate" class="error-text"><?php echo $this->lang->line("error_date"); ?></label>
            </div>

            <div class="col-md-4 mb-2 align-self-center" id="divEndDateFarmReports">
                <label for="end_date_farmreport"><?php echo $this->lang->line("end_date"); ?></label>
                <input type="text" id="end_date_farmreport" name="end_date_farmreport" class="form-control" placeholder="<?php echo $this->lang->line("end_date"); ?>" readonly />
                <label id="error-farmreportenddate" class="error-text"><?php echo $this->lang->line("error_date"); ?></label>
            </div>

            <div class="col-md-4 mb-2 align-self-center" id="divProductFarmReports">
                <label for="product_name_farmreport"><?php echo $this->lang->line("product_name"); ?></label>
                <select class="form-control" name="product_name_farmreport" id="product_name_farmreport" data-plugin="select_erp">
                    <option value="0"><?php echo $this->lang->line("select"); ?></option>
                </select>
                <label id="error-farmreportproduct" class="error-text"><?php echo $this->lang->line("error_product_name"); ?></label>
            </div>

            <div class="col-md-4 mb-2 align-self-center" id="divProductTypeFarmReports">
                <label for="product_type_farmreport"><?php echo $this->lang->line("product_type"); ?></label>
                <select class="form-control" name="product_type_farmreport" id="product_type_farmreport" data-plugin="select_erp">
                    <option value="0"><?php echo $this->lang->line("select"); ?></option>
                </select>
                <label id="error-farmreportproducttype" class="error-text"><?php echo $this->lang->line("error_selectwoodtype"); ?></label>
            </div>

            <div class="col-md-4 mb-2 align-self-center" id="divInputInventoryOrderFarmReports">
                <label for="input_inventory_order_farmreport"><?php echo $this->lang->line("inventory_order"); ?></label>
                <input type="text" id="input_inventory_order_farmreport" name="input_inventory_order_farmreport" class="form-control" placeholder="<?php echo $this->lang->line("inventory_order"); ?>" />
                <label id="error-farmreportinputinventory" class="error-text"><?php echo $this->lang->line("error_inventory_order"); ?></label>
            </div>
        </div>

        <div class="row mb-2 mt-5 flex-between-end">
            <div class="col-md-12 ms-auto">
                <button class="btn btn-primary btn-block" title="<?php echo $this->lang->line("download_reports"); ?>" type="button" id="btn_download_farm_report">
                    <span class="ms-1"><?php echo $this->lang->line("download_reports"); ?></span></button>
            </div>

        </div>
    </div>
</div>
<script src="<?php echo base_url() . 'assets/js/jquery341.min.js'; ?>"></script>
<link rel="stylesheet" href="<?php echo base_url() . "assets/css/jquery-ui.css"; ?>">
<script src="<?php echo base_url() . "assets/js/jquery-ui.js"; ?>"></script>
<script src="<?php echo base_url() . 'assets/js/i18n/datepicker-' . $wz_lang . '.js'; ?>"></script>
<script>
    $(function() {
        $("#start_date_farmreport").datepicker({
            dateFormat: "dd/mm/yy",
            changeMonth: true,
            changeYear: true,
            minDate: "-2y",
            maxDate: "1y",
            onSelect: function(date) {
                var selectedDate = $("#start_date_farmreport").val().split("/");
                var dateval = new Date(selectedDate[1] + "/" + selectedDate[0] + "/" + selectedDate[2]);
                var endDate = new Date(dateval);
                $("#end_date_farmreport").datepicker("option", "minDate", endDate);
            }
        });

        $("#end_date_farmreport").datepicker({
            dateFormat: "dd/mm/yy",
            changeMonth: true,
            changeYear: true,
            minDate: "-2y",
            maxDate: "1y",
            onSelect: function(date) {}
        });
        
        $('.ui-datepicker').addClass('notranslate');
    });
</script>