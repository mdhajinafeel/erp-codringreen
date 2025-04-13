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
                <h3> <?php echo $this->lang->line('purchase_manager_credit_header'); ?> </h3>
            </div>
        </div>
    </div>
    <div class="card-body pt-5">
        <div class="mb-3 row">
            <label class="col-sm-2 col-form-label lbl-font" for="origin"><?php echo $this->lang->line('origin'); ?></label>
            <div class="col-sm-10">
                <select class="form-control" name="origin" id="origin" data-plugin="select_erp">
                    <option value="0"><?php echo $this->lang->line('select'); ?></option>
                    <?php foreach ($applicable_origins as $origin) { ?>
                        <option value="<?php echo $origin->id; ?>"><?php echo $origin->origin_name; ?></option>
                    <?php } ?>
                </select>
                <label id="error-origin" class="error-text"><?php echo $this->lang->line("error_origin_screen"); ?></label>
                <div class="mb-4 row"></div>
            </div>

            <label class="col-sm-2 col-form-label lbl-font" for="purchasemanager_name"><?php echo $this->lang->line('purchasemanagername_title'); ?></label>
            <div class="col-sm-10">
                <select class="form-control" name="purchasemanager_name" id="purchasemanager_name" data-plugin="select_erp">
                    <option value="0"><?php echo $this->lang->line('select'); ?></option>
                </select>
                <label id="error-name" class="error-text"><?php echo $this->lang->line("error_select_name"); ?></label>
                <div class="mb-4 row"></div>
            </div>

            <label class="col-sm-2 col-form-label lbl-font" for="amount" id="lblAmount"><?php echo $this->lang->line('amount'); ?></label>
            <div class="col-sm-10">
                <input type="number" id="amount" step="any" name="amount" class="form-control" placeholder="<?php echo $this->lang->line("amount"); ?>" />
                <label id="error-value" class="error-text"><?php echo $this->lang->line("error_value"); ?></label>
                <div class="mb-4 row"></div>
            </div>

            <label class="col-sm-2 col-form-label lbl-font" for="transaction_date"><?php echo $this->lang->line('transaction_date'); ?></label>
            <div class="col-sm-10">
                <input type="text" id="transaction_date" name="transaction_date" class="form-control" placeholder="<?php echo $this->lang->line("transaction_date"); ?>" readonly />
                <label id="error-date" class="error-text"><?php echo $this->lang->line("error_date"); ?></label>
                <div class="mb-4 row"></div>
            </div>
            <div class="row flex-between-end">
                <div class="col-md-10 ms-auto">
                    <button class="btn btn-primary btn-block" title="<?php echo $this->lang->line("save"); ?>" type="button" id="btn_new_registry">
                        <span class="ms-1"><?php echo $this->lang->line("save"); ?></span></button>
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
    $("#error-origin").hide();
    $("#error-name").hide();
    $("#error-value").hide();
    $("#error-date").hide();

    var error_value = "<?php echo $this->lang->line("error_value"); ?>";
    var error_zero_value = "<?php echo $this->lang->line("error_zero_value"); ?>";

    $(function() {
        $("#transaction_date").datepicker({
            dateFormat: "dd/mm/yy",
            changeMonth: true,
            changeYear: true,
            minDate: "-1m",
            maxDate: "10d",
            onSelect: function(date) {
                //$("#error-dispatchdate").hide();
            }
        });
        
        $('.ui-datepicker').addClass('notranslate');
    });

    $(document).ready(function() {

        $("#origin").change(function() {
            fetchPurchaseManager($("#origin").val());
            fetchCurrencyCode($("#origin").val());
        });

        $("#btn_new_registry").click(function() {

            var originid = $("#origin").val();
            var purchasemanager_name = $("#purchasemanager_name").val();
            var amount = $("#amount").val().trim();
            var transactiondate = $("#transaction_date").val().trim();

            var isValid1 = true,
                isValid2 = true,
                isValid3 = true,
                isValid4 = true,
                isValid5 = true;

            if (originid == 0) {
                $("#error-origin").show();
                isValid1 = false;
            } else {
                $("#error-origin").hide();
                isValid1 = true;
            }

            if (purchasemanager_name == 0) {
                $("#error-name").show();
                isValid2 = false;
            } else {
                $("#error-name").hide();
                isValid2 = true;
            }

            if (amount.length == 0) {
                $("#error-value").show();
                $("#error-value").text(error_value);
                isValid3 = false;
            } else {
                $("#error-value").hide();
                $("#error-value").text("");
                isValid3 = true;
            }

            if (isValid3 == true) {
                if (amount == 0) {
                    $("#error-value").show();
                    $("#error-value").text(error_zero_value);
                    isValid5 = false;
                } else {
                    $("#error-value").hide();
                    $("#error-value").text("");
                    isValid5 = true;
                }
            }

            if (transactiondate.length == 0) {
                $("#error-date").show();
                isValid4 = false;
            } else {
                $("#error-date").hide();
                isValid4 = true;
            }

            if (isValid1 && isValid2 && isValid3 && isValid4 && isValid5) {
                var fd = new FormData();
                fd.append("is_ajax", 2);
                fd.append("add_type", "advanceregistry");
                fd.append("action_type", "save");
                fd.append("origin_id", originid);
                fd.append("purchasemanager_name", purchasemanager_name);
                fd.append("amount", amount);
                fd.append("transaction_date", transactiondate);
                fd.append("csrf_cgrerp", $("#hdnCsrf").val());

                toastr.info(processing_request);

                $('#btn_new_registry').prop('disabled', false);
                $("#loading").show();

                $.ajax({
                    type: "POST",
                    url: base_url + "/save_purchasemanager_credit",
                    data: fd,
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(JSON) {
                        $("#loading").hide();
                        if (JSON.redirect == true) {
                            window.location.replace(login_url);
                        } else if (JSON.error != '') {
                            toastr.clear();
                            toastr.error(JSON.error);
                            $('#btn_new_registry').prop('disabled', false);
                            $('#hdnCsrf').val(JSON.csrf_hash);
                        } else {
                            toastr.clear();
                            toastr.success(JSON.result);
                            $('#btn_new_registry').prop('disabled', false);
                            $('#hdnCsrf').val(JSON.csrf_hash);

                            $("#origin").select2("val", "0");
                            $("#purchasemanager_name option:selected").attr("selectedIndex", 0);
                            $("#amount").val("");
                            $("#transaction_date").val("");
                        }
                    }
                });
            }
        });
    });

    function fetchCurrencyCode(originid) {
        $("#loading").show();
        $.ajax({
            url: base_url + "/get_currency_code?originid=" + originid,
            cache: false,
            method: "GET",
            dataType: "json",
            success: function(JSON) {
                $("#loading").hide();
                if (JSON.redirect == true) {
                    window.location.replace(login_url);
                } else {
                    $("#lblAmount").text(JSON.result);
                }
            }
        });
    }

    function fetchPurchaseManager(originid) {
        $("#loading").show();
        $.ajax({
            url: base_url + "/get_purchasemanager_by_origin?originid=" + originid,
            cache: false,
            method: "GET",
            dataType: "json",
            success: function(JSON) {
                $("#loading").hide();
                if (JSON.redirect == true) {
                    window.location.replace(login_url);
                } else if (JSON.result != '') {
                    $("#purchasemanager_name").empty();
                    $("#purchasemanager_name").append(JSON.result);
                }
            }
        });
    }
</script>