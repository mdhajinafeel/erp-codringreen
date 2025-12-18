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
                <label id="error-origin" class="error-text"><?php echo $this->lang->line("error_origin_screen"); ?></label>
                <div class="mb-4 row"></div>
            </div>

            <label class="col-sm-2 col-form-label lbl-font" for="user_name"><?php echo $this->lang->line('name'); ?></label>
            <div class="col-sm-10">
                <select class="form-control" name="user_name" id="user_name" data-plugin="select_erp">
                    <option value="0"><?php echo $this->lang->line('select'); ?></option>
                </select>
                <label id="error-name" class="error-text"><?php echo $this->lang->line("error_select_name"); ?></label>
                <div class="mb-4 row"></div>
            </div>

            <label class="col-sm-2 col-form-label lbl-font" for="concept_general"><?php echo $this->lang->line('concept_general'); ?></label>
            <div class="col-sm-10">
                <select class="form-control" name="concept_general" id="concept_general" data-plugin="select_erp">
                    <option value="0"><?php echo $this->lang->line('select'); ?></option>
                </select>
                <div class="mb-4 row"></div>
            </div>

            <label class="col-sm-2 col-form-label lbl-font" for="accounthead"><?php echo $this->lang->line('accounthead_title'); ?></label>
            <div class="col-sm-10">
                <select class="form-control" name="accounthead" id="accounthead" data-plugin="select_erp">
                    <option value="0"><?php echo $this->lang->line('select'); ?></option>
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
                <label id="error-to_date" class="error-text"><?php echo $this->lang->line("error_date"); ?></label>
                <div class="mb-4 row"></div>
            </div>

            <div class="row flex-between-end">
                <div class="col-md-10 ms-auto">
                    <button class="btn btn-primary btn-block" title="<?php echo $this->lang->line("download_reports"); ?>" type="button" id="btn_download_reports">
                        <span class="ms-1"><?php echo $this->lang->line("download_reports"); ?></span></button>

                    <button class="btn btn-primary btn-success ml-10" title="<?php echo $this->lang->line("download_invoices"); ?>" type="button" id="btn_download_files">
                        <span class="ms-1"><?php echo $this->lang->line("download_invoices"); ?></span></button>

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

        $("#error-origin").hide();
        $("#error-name").hide();
        $("#error-to_date").hide();

        $("#origin_report").change(function() {
            fetchExpenseLedgerUsers($("#origin_report").val());
            fetchAccountHeads($("#origin_report").val());
            fetchCreditTransactions($("#origin_report").val(), 0);
        });

        $("#user_name").change(function() {
            fetchCreditTransactions($("#origin_report").val(), $("#user_name").val());
        });

        $("#report_from_date").datepicker({
            dateFormat: "dd/mm/yy",
            changeMonth: true,
            changeYear: true,
            minDate: "-5y",
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
            minDate: "-5y",
            maxDate: "0d",
            onSelect: function(date) {}
        });

        $("#btn_download_reports").click(function() {
            var isValid = true;
            var originReport = $("#origin_report").val();
            var userName = $("#user_name").val();
            var fromDate = $("#report_from_date").val();
            var toDate = $("#report_to_date").val();

            if (originReport == 0) {
                $("#error-origin").show();
                isValid = false;
            } else {
                $("#error-origin").hide();
            }

            if (userName == 0) {
                $("#error-name").show();
                isValid = false;
            } else {
                $("#error-name").hide();
            }

            if(fromDate.length > 0 && toDate.length == 0) {
                $("#error-to_date").show();
                isValid = false;
            } else {
                $("#error-to_date").hide();
            }

            if (isValid) {
                var originId = $("#origin_report").val();
                var userId = $("#user_name").val();

                var conceptGeneral = $("#concept_general").val();
                var accountHead = $("#accounthead").val();

                $("#loading").show();
                var fd = new FormData();
                fd.append("type", "ledgerreport");

                fd.append("originId", originId);
                fd.append("userId", userId);
                fd.append("fromDate", fromDate);
                fd.append("toDate", toDate);
                fd.append("conceptGeneral", conceptGeneral);
                fd.append("accountHead", accountHead);

                fd.append("csrf_cgrerp", $("#hdnCsrf").val());
                toastr.info(processing_request);
                $.ajax({
                    url: base_url + "/generate_expense_ledger_reports",
                    type: "POST",
                    data: fd,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        $("#loading").hide();
                        if (response.redirect == true) {
                            window.location.replace(login_url);
                        } else if (response.error != '') {
                            toastr.error(response.error);
                            $('input[name="csrf_cgrerp"]').val(response.csrf_hash);
                        } else {
                            toastr.success(response.successmessage);
                            $('input[name="csrf_cgrerp"]').val(response.csrf_hash);
                            window.location = response.result;
                        }
                    }
                });
            }
        });

        $("#btn_download_files").click(function() {
            var isValid = true;
            var originReport = $("#origin_report").val();
            var userName = $("#user_name").val();
            var fromDate = $("#report_from_date").val();
            var toDate = $("#report_to_date").val();

            if (originReport == 0) {
                $("#error-origin").show();
                isValid = false;
            } else {
                $("#error-origin").hide();
            }

            if (userName == 0) {
                $("#error-name").show();
                isValid = false;
            } else {
                $("#error-name").hide();
            }

            if(fromDate.length > 0 && toDate.length == 0) {
                $("#error-to_date").show();
                isValid = false;
            } else {
                $("#error-to_date").hide();
            }

            if (isValid) {
                var originId = $("#origin_report").val();
                var userId = $("#user_name").val();

                var conceptGeneral = $("#concept_general").val();
                var accountHead = $("#accounthead").val();

                $("#loading").show();
                var fd = new FormData();
                fd.append("type", "ledgerreport_files");

                fd.append("originId", originId);
                fd.append("userId", userId);
                fd.append("fromDate", fromDate);
                fd.append("toDate", toDate);
                fd.append("conceptGeneral", conceptGeneral);
                fd.append("accountHead", accountHead);

                fd.append("csrf_cgrerp", $("#hdnCsrf").val());
                toastr.info(processing_request);
                $.ajax({
                    url: base_url + "/download_expense_ledger_files",
                    type: "POST",
                    data: fd,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        $("#loading").hide();
                        if (response.redirect == true) {
                            window.location.replace(login_url);
                        } else if (response.error != '') {
                            toastr.error(response.error);
                            $('input[name="csrf_cgrerp"]').val(response.csrf_hash);
                        } else {
                            toastr.success(response.result);
                            $('input[name="csrf_cgrerp"]').val(response.csrf_hash);
                            window.location = response.downloadfile;
                        }
                    }
                });
            }
        });
    });

    $(document).ready(function() {

        $("#origin_report").change(function() {
            fetchExpenseLedgerUsers($("#origin_report").val());
            fetchAccountHeads($("#origin_report").val());
            fetchCreditTransactions($("#origin_report").val(), 0);
        });

        $("#btn_reset").click(function() {
            $("#origin_report").select2("val", "0");
            $("#report_from_date").val("");
            $("#report_to_date").val("");

            $("#error-origin").hide();
            $("#error-name").hide();
            $("#error-to_date").hide();
        });
    });

    function fetchExpenseLedgerUsers(originid) {
        $("#loading").show();
        $.ajax({
            url: base_url + "/get_expense_ledger_users?originid=" + originid,
            cache: false,
            method: "GET",
            dataType: "json",
            success: function(JSON) {
                $("#loading").hide();
                if (JSON.redirect == true) {
                    window.location.replace(login_url);
                } else if (JSON.result != '') {
                    $("#user_name").empty();
                    $("#user_name").append(JSON.result);
                }
            }
        });
    }

    function fetchAccountHeads(originid) {
        $("#loading").show();
        $.ajax({
            url: base_url + "/get_account_heads?originid=" + originid,
            cache: false,
            method: "GET",
            dataType: "json",
            success: function(JSON) {
                $("#loading").hide();
                if (JSON.redirect == true) {
                    window.location.replace(login_url);
                } else if (JSON.result != '') {
                    $("#accounthead").empty();
                    $("#accounthead").append(JSON.result);
                }
            }
        });
    }

    function fetchCreditTransactions(originid, userid) {
        $("#loading").show();
        $.ajax({
            url: base_url + "/get_credit_transactions?originid=" + originid + "&userid=" + userid,
            cache: false,
            method: "GET",
            dataType: "json",
            success: function(JSON) {
                $("#loading").hide();
                if (JSON.redirect == true) {
                    window.location.replace(login_url);
                } else if (JSON.result != '') {
                    $("#concept_general").empty();
                    $("#concept_general").append(JSON.result);
                }
            }
        });
    }
</script>