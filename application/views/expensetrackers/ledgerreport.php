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
                <h3> <?php echo $this->lang->line('ledgerreport_title'); ?> </h3>
            </div>
        </div>
    </div>
    <div class="card-body pt-5">
        <div class="mb-3 row">
            <label class="col-sm-2 col-form-label lbl-font" for="origin_report"><?php echo $this->lang->line('origin'); ?></label>
            <div class="col-sm-10">
                <select class="form-control" name="origin_report" id="origin_report" data-plugin="select_erp">
                    <option value="0"><?php echo $this->lang->line('select'); ?></option>
                    <?php foreach ($applicable_origins as $origin) { ?>
                        <option value="<?php echo $origin->id; ?>"><?php echo $origin->origin_name; ?></option>
                    <?php } ?>
                </select>
                <div class="mb-4 row"></div>
            </div>

            <label class="col-sm-2 col-form-label lbl-font" for="report_from_date"><?php echo $this->lang->line('from_date'); ?></label>
            <div class="col-sm-10">
                <input type="text" id="report_from_date" name="report_from_date" class="form-control" placeholder="<?php echo $this->lang->line("from_date"); ?>" readonly />
                <div class="mb-4 row"></div>
            </div>

            <label class="col-sm-2 col-form-label lbl-font" for="report_to_date"><?php echo $this->lang->line('to_date'); ?></label>
            <div class="col-sm-10">
                <input type="text" id="report_to_date" name="report_to_date" class="form-control" placeholder="<?php echo $this->lang->line("to_date"); ?>" readonly />
                <div class="mb-4 row"></div>
            </div>

            <div class="row flex-between-end">
                <div class="col-md-10 ms-auto">
                    <button class="btn btn-primary btn-block" title="<?php echo $this->lang->line("download_reports"); ?>" type="button" id="btn_download_reports">
                        <span class="ms-1"><?php echo $this->lang->line("download_reports"); ?></span></button>

                    <button class="btn btn-danger btn-block ml-10" title="<?php echo $this->lang->line("reset"); ?>" type="button" id="btn_reset">
                        <span class="ms-1"><?php echo $this->lang->line("reset"); ?></span></button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url() . 'assets/js/jquery341.min.js'; ?>"></script>
<link rel="stylesheet" href="<?php echo base_url() . "assets/css/jquery-ui.css"; ?>">
<script src="<?php echo base_url() . "assets/js/jquery-ui.js"; ?>"></script>
<script src="<?php echo base_url() . 'assets/js/i18n/datepicker-' . $wz_lang . '.js'; ?>"></script>
<script type="text/javascript">
    $(function() {
        $("#report_from_date").datepicker({
            dateFormat: "dd/mm/yy",
            changeMonth: true,
            changeYear: true,
            minDate: "-2y",
            maxDate: "0d",
            onSelect: function(date) {
                var selectedDate = $("#report_from_date").val().split("/");
                var dateval = new Date(selectedDate[1] + "/" + selectedDate[0] + "/" + selectedDate[2]);
                var endDate = new Date(dateval);
                $("#report_to_date").datepicker("option", "minDate", endDate);
            }
        });

        $("#report_to_date").datepicker({
            dateFormat: "dd/mm/yy",
            changeMonth: true,
            changeYear: true,
            minDate: "-2y",
            maxDate: "0d",
            onSelect: function(date) {}
        });
    });

    $(document).ready(function() {

        $("#origin_report").change(function() {
            //fetchExpenseTypes($("#origin_report").val());
        });

        $("#btn_reset").click(function() {
            $("#origin_report").select2("val", "0");
            $("#report_from_date").val("");
            $("#report_to_date").val("");
        });
    });

    function fetchExpenseTypes(originid) {
        $("#loading").show();
        $.ajax({
            url: base_url + "/get_expense_types?originid=" + originid,
            cache: false,
            method: "GET",
            dataType: "json",
            success: function(JSON) {
                $("#loading").hide();
                if (JSON.redirect == true) {
                    window.location.replace(login_url);
                } else if (JSON.result != '') {
                    $("#beneficiary_name_ledger").empty();
                    $("#beneficiary_name_ledger").append(JSON.result);
                }
            }
        });
    }
</script>