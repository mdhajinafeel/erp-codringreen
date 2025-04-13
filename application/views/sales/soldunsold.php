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
                <h3> <?php echo $this->lang->line("sold_unsold"); ?> </h3>
            </div>
        </div>
    </div>
    <div class="card-body pt-5">

        <div class="row mb-5">
            <div class="col-auto ms-auto">
                <a href="<?php echo base_url() . 'assets/templates/Template_Sales_Details.xlsx'; ?>" class="btn btn-info btn-block" download=""><i class="fa fa-cloud-download"></i><span style="padding-left: 10px;"><?php echo $this->lang->line('download_template'); ?></span></a>
                
                <button class="btn btn-upload btn-md ml-10" title="<?php echo $this->lang->line('upload_sales_data'); ?>" type="button" id="btn_upload_sales_data">
                    <span class="fas fa-upload" data-fa-transform="shrink-3 down-2"></span><span class="ms-1"><?php echo $this->lang->line('upload_sales_data'); ?></span>
                </button>

                <button class="btn btn-success btn-block ml-10" title="<?php echo $this->lang->line("generate_report"); ?>" type="button" id="btn_download_salesreports">
                        <span class="fas fa-file-excel"  data-fa-transform="shrink-3 down-2"></span><span class="ms-1"><?php echo $this->lang->line("generate_report"); ?></span>
                </button>
            </div>
        </div>

        <div class="mb-3 row">
            <label class="col-sm-2 col-form-label lbl-font" for="origin_salesreport"><?php echo $this->lang->line("origin"); ?></label>
            <div class="col-sm-10">
                <select class="form-control" name="origin_salesreport" id="origin_salesreport" data-plugin="select_erp">
                    <option value="0"><?php echo $this->lang->line("select"); ?></option>
                    <?php foreach ($applicable_origins as $origin) { ?>
                        <option value="<?php echo $origin->id; ?>"><?php echo $origin->origin_name; ?></option>
                    <?php } ?>
                </select>
                <label id="error-origin" class="error-text"><?php echo $this->lang->line("error_origin_screen"); ?></label>
                <div class="mb-4 row"></div>
            </div>

            <label class="col-sm-2 col-form-label lbl-font" for="report_downloadby"><?php echo $this->lang->line("download_by"); ?></label>
            <div class="col-sm-10">
                <select class="form-control" name="report_downloadby" id="report_downloadby" data-plugin="select_erp" disabled>
                    <option value="0"><?php echo $this->lang->line("select"); ?></option>
                    <option value="2"><?php echo $this->lang->line("costsummary_export"); ?></option>
                </select>
                <label id="error-selectoption" class="error-text"><?php echo $this->lang->line("error_select_option"); ?></label>
                <div class="mb-4 row"></div>
            </div>

            <label class="col-sm-2 col-form-label lbl-font" id="lbl_product_type" for="product_type"><?php echo $this->lang->line("sa_number"); ?></label>
            <div class="col-sm-10" id="div_product_type">
                <select class="form-control" multiple data-placeholder="<?php echo $this->lang->line("all"); ?>" name="product_type[]" id="product_type" data-plugin="select_erp">
                </select>
                <div class="mb-4 row"></div>
            </div>

            <div class="row flex-between-end">
                <div class="col-md-10 ms-auto">
                    <button class="btn btn-primary btn-block ml-10" title="<?php echo $this->lang->line("download_reports"); ?>" type="button" id="btn_view_salesreports">
                        <span class="ms-1"><?php echo $this->lang->line("download_reports"); ?></span></button>

                    <button class="btn btn-danger btn-block ml-10" title="<?php echo $this->lang->line("reset"); ?>" type="button" id="btn_reset_salesreports">
                        <span class="ms-1"><?php echo $this->lang->line("reset"); ?></span></button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url() . 'assets/js/jquery341.min.js'; ?>"></script>
<script src="<?php echo base_url() . "assets/js/jquery.dataTables.min.js"; ?>"></script>
<script src="<?php echo base_url() . "assets/js/dataTables.bootstrap.min.js"; ?>"></script>
<link rel="stylesheet" href="<?php echo base_url() . "assets/css/jquery-ui.css"; ?>">
<script src="<?php echo base_url() . "assets/js/jquery-ui.js"; ?>"></script>
<script src="<?php echo base_url() . 'assets/js/i18n/datepicker-' . $wz_lang . '.js'; ?>"></script>

<script type="text/javascript">
    var export_sa = "<?php echo $this->lang->line("export_sa"); ?>";
    var product_type = "<?php echo $this->lang->line("product_type"); ?>";
    var select_text = "<?php echo $this->lang->line("select"); ?>";
    var select_text = "<?php echo $this->lang->line("select"); ?>";
    var error_selectwoodtype = "<?php echo $this->lang->line("error_selectwoodtype"); ?>";
    var error_selectsa = "<?php echo $this->lang->line("error_selectsa"); ?>";

    var productTypes = <?php echo json_encode($productTypes); ?>;

    $("#lbl_product_type").css("display", "none");
    $("#div_product_type").css("display", "none");
    $("#error-origin").hide();
    $("#error-selectoption").hide();

    $(function() {
        // $("#report_from_date").datepicker({
        //     dateFormat: "dd/mm/yy",
        //     changeMonth: true,
        //     changeYear: true,
        //     minDate: "-2y",
        //     maxDate: "3m",
        //     onSelect: function(date) {
        //         var selectedDate = $("#report_from_date").val().split("/");
        //         var dateval = new Date(selectedDate[1] + "/" + selectedDate[0] + "/" + selectedDate[2]);
        //         var endDate = new Date(dateval);
        //         $("#report_to_date").datepicker("option", "minDate", endDate);
        //     }
        // });

        // $("#report_to_date").datepicker({
        //     dateFormat: "dd/mm/yy",
        //     changeMonth: true,
        //     changeYear: true,
        //     minDate: "-2y",
        //     maxDate: "3m",
        //     onSelect: function(date) {}
        // });

        // $('.ui-datepicker').addClass('notranslate');
    });

    $(document).ready(function() {

        $("#report_downloadby").change(function() {

            if ($("#report_downloadby").val() == 2) {

                $("#lbl_product_type").css("display", "block");
                $("#div_product_type").css("display", "block");
                $("#divstartdate").css("display", "none");
                $("#divenddate").css("display", "none");


                fetchExportSANumber($("#origin_salesreport").val());
            } else {
                $("#lbl_product_type").css("display", "none");
                $("#lbl_product_type").css("display", "none");
                $("#div_product_type").css("display", "none");
            }

        });
    });

    function fetchExportSANumber(originid) {
        $("#loading").show();
        $.ajax({
            url: base_url + "/fetch_export_sa_number?originid=" + originid,
            cache: false,
            method: "GET",
            dataType: "json",
            success: function(JSON) {
                $("#loading").hide();
                if (JSON.redirect == true) {
                    window.location.replace(login_url);
                } else if (JSON.result != '') {
                    $('#product_type').empty();
                    //$('#product_type').append($('<option/>').attr("value", "0").text(select_text));
                    $.each(JSON.result, function(i, option) {
                        $('#product_type').append($('<option/>').attr("value", option.sa_no).text(option.sa_details));
                    });
                }
            }
        });
    }
</script>