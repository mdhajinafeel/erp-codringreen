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
                <h3> <?php echo $this->lang->line('expense_debit'); ?> </h3>
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

            <label class="col-sm-2 col-form-label lbl-font" for="user_name"><?php echo $this->lang->line('name'); ?></label>
            <div class="col-sm-10">
                <select class="form-control" name="user_name" id="user_name" data-plugin="select_erp">
                    <option value="0"><?php echo $this->lang->line('select'); ?></option>
                </select>
                <label id="error-name" class="error-text"><?php echo $this->lang->line("error_select_name"); ?></label>
                <div class="mb-4 row"></div>
            </div>

            <label class="col-sm-2 col-form-label lbl-font" for="concept_general" id="lblConceptGeneral"><?php echo $this->lang->line('concept_general'); ?></label>
            <div class="col-sm-10">
                <select class="form-control" name="concept_general" id="concept_general" data-plugin="select_erp">
                    <option value="0"><?php echo $this->lang->line('select'); ?></option>
                </select>
                <label id="error-conceptgeneral" class="error-text"><?php echo $this->lang->line("error_conceptgeneral"); ?></label>
                <div class="mb-4 row"></div>
            </div>

            <label class="col-sm-2 col-form-label lbl-font" for="account_head"><?php echo $this->lang->line('accounthead_title'); ?></label>
            <div class="col-sm-10">
                <select class="form-control" name="account_head" id="account_head" data-plugin="select_erp">
                    <option value="0"><?php echo $this->lang->line('select'); ?></option>
                </select>
                <label id="error-account_head" class="error-text"><?php echo $this->lang->line("error_select_name"); ?></label>
                <div class="mb-4 row"></div>
            </div>

            <label class="col-sm-2 col-form-label lbl-font" for="beneficiaryname"><?php echo $this->lang->line('beneficiary_name'); ?></label>
            <div class="col-sm-10">
                <input type="text" id="beneficiaryname" step="any" name="beneficiaryname" class="form-control" placeholder="<?php echo $this->lang->line("beneficiary_name"); ?>" />
                <label id="error-beneficiary_name" class="error-text"><?php echo $this->lang->line("error_name"); ?></label>
                <div class="mb-4 row"></div>
            </div>

            <label class="col-sm-2 col-form-label lbl-font" for="document_number"><?php echo $this->lang->line('document_number'); ?></label>
            <div class="col-sm-10">
                <input type="text" id="document_number" step="any" name="document_number" class="form-control" placeholder="<?php echo $this->lang->line("document_number"); ?>" />
                <label id="error-document_number" class="error-text"><?php echo $this->lang->line("error_value"); ?></label>
                <div class="mb-4 row"></div>
            </div>

            <label class="col-sm-2 col-form-label lbl-font" for="expense_date"><?php echo $this->lang->line('expense_date'); ?></label>
            <div class="col-sm-10">
                <input type="text" id="expense_date" name="expense_date" class="form-control" placeholder="<?php echo $this->lang->line("expense_date"); ?>" readonly />
                <label id="error-date" class="error-text"><?php echo $this->lang->line("error_date"); ?></label>
                <div class="mb-4 row"></div>
            </div>

            <label class="col-sm-2 col-form-label lbl-font" for="amount" id="lblAmount"><?php echo $this->lang->line('amount'); ?></label>
            <div class="col-sm-10">
                <input type="number" id="amount" step="any" name="amount" class="form-control" placeholder="<?php echo $this->lang->line("amount"); ?>" />
                <label id="error-value" class="error-text"><?php echo $this->lang->line("error_value"); ?></label>
                <div class="mb-4 row"></div>
            </div>

            <label class="col-sm-2 col-form-label lbl-font" for="expense_attachemnt_file"><?php echo $this->lang->line('upload_file'); ?></label>
            <div class="col-sm-4">
                <input name="expense_attachemnt_file" type="file" accept="*/*" onchange="loadFile(event)" id="expense_attachemnt_file" class="form-control" />
                <div class="mb-4 row"></div>
            </div>
            <div class="col-sm-3">
                <div id="filePreview">
                    <?php if ($fileUrl): ?>

                        <?php if (in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif', 'webp'])): ?>
                            <img src="<?php echo $fileUrl; ?>"
                                class="img-thumbnail"
                                style="max-width:100%;max-height:150px">

                        <?php elseif ($fileExt === 'pdf'): ?>
                            <iframe src="<?php echo $fileUrl; ?>"
                                width="100%"
                                height="150"
                                style="border:1px solid #ccc"></iframe>

                        <?php elseif (in_array($fileExt, ['txt', 'xml', 'json', 'csv'])): ?>
                            <pre style="max-height:200px;overflow:auto;font-size:12px">
                                <?php echo htmlspecialchars(@file_get_contents($fileUrl)); ?>
                            </pre>
                        <?php elseif (in_array($fileExt, ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'])): ?>
                            <div class="border p-2 rounded text-center">
                                <div style="font-size:24px">📎</div>
                                <div class="small text-muted"><?php echo basename($fileUrl); ?></div>
                                <a href="<?php echo $fileUrl; ?>" target="_blank"
                                    class="btn btn-sm btn-outline-primary mt-1">
                                    <?php echo $this->lang->line("download"); ?>
                                </a>
                            </div>

                        <?php else: ?>
                            <a href="<?php echo $fileUrl; ?>" target="_blank">
                                📎 Download File
                            </a>
                        <?php endif; ?>

                    <?php endif; ?>
                </div>
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
    $("#error-conceptgeneral").hide();
    $("#error-account_head").hide();
    $("#error-beneficiary_name").hide();
    $("#error-document_number").hide();

    var error_value = "<?php echo $this->lang->line("error_value"); ?>";
    var error_zero_value = "<?php echo $this->lang->line("error_zero_value"); ?>";
    var preview_not_available = "<?php echo $this->lang->line("preview_not_available"); ?>";

    $(function() {
        $("#expense_date").datepicker({
            dateFormat: "dd/mm/yy",
            changeMonth: true,
            changeYear: true,
            minDate: "-1y",
            maxDate: "1m",
            onSelect: function(date) {}
        });
    });

    $(document).ready(function() {

        $("#origin").change(function() {
            fetchExpenseLedgerUsers($("#origin").val());
            fetchCurrencyCode($("#origin").val());
            fetchAccountHeads($("#origin").val());
            fetchCreditTransactions($("#origin").val(), 0);
        });

        $("#user_name").change(function() {
            fetchCreditTransactions($("#origin").val(), $("#user_name").val());
        });

        $("#btn_new_registry").click(function() {

            var originid = $("#origin").val();
            var userid = $("#user_name").val();
            var accounthead = $("#account_head").val();
            var beneficaryname = $("#beneficiaryname").val();
            var documentnumber = $("#document_number").val();
            var amount = $("#amount").val().trim();
            var expensedate = $("#expense_date").val().trim();
            var conceptgeneral = $("#concept_general").val().trim();
            var fileInput = document.getElementById('expense_attachemnt_file');

            var isValid1 = true,
                isValid2 = true,
                isValid3 = true,
                isValid4 = true,
                isValid5 = true,
                isValid6 = true,
                isValid7 = true,
                isValid8 = true, isValid9 = true;

            if (originid == 0) {
                $("#error-origin").show();
                isValid1 = false;
            } else {
                $("#error-origin").hide();
                isValid1 = true;
            }

            if (beneficaryname.length == 0) {
                $("#error-beneficiary_name").show();
                isValid2 = false;
            } else {
                $("#error-beneficiary_name").hide();
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
                    isValid3 = false;
                } else {
                    $("#error-value").hide();
                    $("#error-value").text("");
                    isValid3 = true;
                }
            }

            if (expensedate.length == 0) {
                $("#error-date").show();
                isValid4 = false;
            } else {
                $("#error-date").hide();
                isValid4 = true;
            }

            if (conceptgeneral == 0) {
                $("#error-conceptgeneral").show();
                isValid5 = false;
            } else {
                $("#error-conceptgeneral").hide();
                isValid5 = true;
            }

            if (userid == 0) {
                $("#error-name").show();
                isValid6 = false;
            } else {
                $("#error-name").hide();
                isValid6 = true;
            }

            if (accounthead == 0) {
                $("#error-account_head").show();
                isValid7 = false;
            } else {
                $("#error-account_head").hide();
                isValid7 = true;
            }

            if (documentnumber.length == 0) {
                $("#error-document_number").show();
                isValid8 = false;
            } else {
                $("#error-document_number").hide();
                isValid8 = true;
            }


            if (isValid1 && isValid2 && isValid3 && isValid4 && isValid5 && isValid6 && isValid7 && isValid8) {

                if (fileInput.files.length > 0) {
                    // user selected new file
                    isValid9 = true;
                } else {
                    // no file at all
                    isValid9 = false;
                    toastr.error("<?php echo $this->lang->line('error_select_file'); ?>");
                }

                if(isValid9 == false){
                    return;
                }

                var fd = new FormData();
                fd.append("is_ajax", 2);
                fd.append("add_type", "debitregistry");
                fd.append("action_type", "save");
                fd.append("origin_id", originid);
                fd.append("user_id", userid);
                fd.append("account_head", accounthead);
                fd.append("beneficary_name", beneficaryname);
                fd.append("document_number", documentnumber);
                fd.append("amount", amount);
                fd.append("expense_date", expensedate);
                fd.append("concept_general", conceptgeneral);
                fd.append('expense_attachemnt_file', fileInput.files[0]);
                fd.append("csrf_cgrerp", $("#hdnCsrf").val());

                toastr.info(processing_request);

                $('#btn_new_registry').prop('disabled', false);
                $("#loading").show();

                $.ajax({
                    type: "POST",
                    url: base_url + "/save_debit_registry",
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
                            $("#beneficiary_name").select2("val", "0");
                            $("#amount").val("");
                            $("#transaction_date").val("");
                            $("#conceptgeneral").val("");
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
                    $("#account_head").empty();
                    $("#account_head").append(JSON.result);
                }
            }
        });
    }

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

    function loadFile(event) {
        const file = event.target.files[0];
        if (!file) return;

        const preview = document.getElementById('filePreview');
        preview.innerHTML = '';

        const ext = file.name.split('.').pop().toLowerCase();

        // 🖼 IMAGE
        if (file.type.startsWith('image/')) {
            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.className = 'img-thumbnail';
            img.style.maxWidth = '100%';
            img.style.maxHeight = '200px';
            preview.appendChild(img);
        }

        // 📄 PDF
        else if (file.type === 'application/pdf') {
            const iframe = document.createElement('iframe');
            iframe.src = URL.createObjectURL(file);
            iframe.style.width = '100%';
            iframe.style.height = '200px';
            iframe.style.border = '1px solid #ccc';
            preview.appendChild(iframe);
        }

        // 🧾 TEXT / XML / JSON
        else if (['txt', 'xml', 'json', 'csv'].includes(ext)) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const pre = document.createElement('pre');
                pre.style.maxHeight = '200px';
                pre.style.overflow = 'auto';
                pre.style.fontSize = '12px';
                pre.textContent = e.target.result;
                preview.appendChild(pre);
            };
            reader.readAsText(file);
        }

        // 📊 OFFICE FILES (DOC, XLS, PPT)
        else { //if (['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'].includes(ext)) {
            preview.innerHTML = `
            <div class="border p-2 rounded text-center">
                <div style="font-size:24px">📎</div>
                <div class="small text-muted">${file.name}</div>
            </div>`;
        }

        // ❓ UNKNOWN
        //else {
         //   preview.innerHTML = `<span class="text-muted">📎 ${file.name}</span>`;
        //}
    }
</script>