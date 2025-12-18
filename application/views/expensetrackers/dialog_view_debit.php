    <?php
    defined('BASEPATH') or exit('No direct script access allowed');
    ?>
    <?php
    $session = $this->session->userdata('fullname');
    $applicable_origins = $session["applicable_origins"];
    ?>
    <?php $site_lang = $this->load->helper('language'); ?>
    <?php $wz_lang = $site_lang->session->userdata('site_lang'); ?>
    <div class="modal-header">
        <h4 class="modal-title" id="add-modal-data"><?php echo $pageheading; ?></h4>
        <?php echo form_button(array('aria-label' => 'Close', 'data-bs-dismiss' => 'modal', 'type' => 'button', 'class' => 'close', 'content' => '<span aria-hidden="true">×</span>')); ?>

    </div>
    <?php $attributes = array('name' => 'update_debitdetails', 'id' => 'update_debitdetails', 'autocomplete' => 'off', 'class' => '"m-b-1'); ?>
    <?php $hidden = array('_method' => $pagetype); ?>
    <?php echo form_open_multipart('expenseledger/update_debitdetails', $attributes, $hidden); ?>
    <div class="modal-body">
        <input type="hidden" id="pagetype" name="pagetype" value="<?php echo $pagetype; ?>">
        <input type="hidden" id="hdntransactionid" name="hdntransactionid" value="<?php echo $transactionId;  ?>">
        <input type="hidden" id="hdndisplayid" name="hdndisplayid" value="<?php echo $displayId;  ?>">
        <input type="hidden" id="hdnoriginid" name="hdnoriginid" value="<?php echo $originId;  ?>">
        <input type="hidden" id="hdnuserid" name="hdnuserid" value="<?php echo $userId;  ?>">
        <input type="hidden" id="hdncsrf" name="hdncsrf" value="<?php echo $csrf_hash;  ?>">
        <div class="row">
            <div class="col-md-6">
                <label class="head-label"><?php echo $this->lang->line("transaction_id"); ?></label>
                <div class="input-group">
                    <label class="control-label"><?php echo isset($debit_details[0]->transaction_display_id) ? $debit_details[0]->transaction_display_id : ''; ?></label>
                </div>
            </div>

            <div class="col-md-6">
                <label for="concept_general"><?php echo $this->lang->line('concept_general'); ?></label>
                <select class="form-control" name="concept_general" id="concept_general" data-plugin="select_erp">
                    <option value="0"><?php echo $this->lang->line("select"); ?></option>
                    <?php foreach ($credit_details as $credit_detail) { ?>
                        <option value="<?php echo $credit_detail->transaction_id; ?>" <?php if (isset($debit_details[0]->credit_transaction_id) && $debit_details[0]->credit_transaction_id == $credit_detail->transaction_id) : ?> selected="selected" <?php endif; ?>><?php echo $credit_detail->concept_general . ' -- ' . $credit_detail->transaction_display_id; ?></option>
                    <?php } ?>
                </select>
                <label id="error-concept_general" class="error-text"><?php echo $this->lang->line('error_purchase_contract'); ?></label>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <label for="account_head"><?php echo $this->lang->line('accounthead_title'); ?></label>
                <select class="form-control" name="account_head" id="account_head" data-plugin="select_erp">
                    <option value="0"><?php echo $this->lang->line("select"); ?></option>
                    <?php foreach ($account_heads as $account_head) { ?>
                        <option value="<?php echo $account_head->id; ?>" <?php if (isset($debit_details[0]->account_head) && $debit_details[0]->account_head == $account_head->id) : ?> selected="selected" <?php endif; ?>><?php echo $account_head->name_in_ledger; ?></option>
                    <?php } ?>
                </select>
                <label id="error-account_head" class="error-text"><?php echo $this->lang->line('error_purchase_contract'); ?></label>
            </div>

            <div class="col-md-6">
                <label for="expense_date"><?php echo $this->lang->line('expense_date'); ?></label>
                <input class="form-control" placeholder="<?php echo $this->lang->line('expense_date'); ?>" name="expense_date" id="expense_date" type="text" readonly value="<?php echo isset($debit_details[0]->expense_date) ? $debit_details[0]->expense_date : ''; ?>">
                <label id="error-expense_date" class="error-text"><?php echo $this->lang->line('error_date'); ?></label>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <label for="beneficiary_name"><?php echo $this->lang->line('beneficiary_name'); ?></label>
                <input class="form-control" autocomplete="off" placeholder="<?php echo $this->lang->line('beneficiary_name'); ?>" name="beneficiary_name" id="beneficiary_name" type="text" value="<?php echo isset($debit_details[0]->beneficiary_name) ? $debit_details[0]->beneficiary_name : ''; ?>">
                <input type="hidden" id="beneficiary_id" name="beneficiary_id">
                <label id="error-beneficiary_name" class="error-text"><?php echo $this->lang->line('error_purchase_contract'); ?></label>
            </div>

            <div class="col-md-6">
                <label for="document_number"><?php echo $this->lang->line('document_number'); ?></label>
                <input class="form-control" placeholder="<?php echo $this->lang->line('document_number'); ?>" name="document_number" id="document_number" type="text" value="<?php echo isset($debit_details[0]->document_number) ? $debit_details[0]->document_number : ''; ?>">
                <label id="error-document_number" class="error-text"><?php echo $this->lang->line('error_purchase_contract'); ?></label>
            </div>

        </div>

        <div class="row mt-4">

            <div class="col-md-6">
                <label for="amount"><?php echo $this->lang->line('amount'); ?></label>
                <input class="form-control" placeholder="<?php echo $this->lang->line('amount'); ?>" name="amount" id="amount" type="number" step="any" value="<?php echo isset($debit_details[0]->amount) ? $debit_details[0]->amount + 0 : '0'; ?>">
                <label id="error-amount" class="error-text"><?php echo $this->lang->line('error_value'); ?></label>
            </div>

            <div class="col-md-3">

                <?php

                $fileUrl = isset($debit_details[0]->expense_uploaded_image) ? $debit_details[0]->expense_uploaded_image : '';
                $fileExt = '';

                if ($fileUrl) {
                    $path = parse_url($fileUrl, PHP_URL_PATH);
                    $fileExt = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                }

                ?>
                <label for="upload_file"><?php echo $this->lang->line('upload_file'); ?></label>
                <input name="expense_attachemnt_file" type="file" accept="*/*" onchange="loadFile(event)" id="expense_attachemnt_file" class="form-control" />
            </div>

            <div class="col-md-3 mt-2">
                <div id="filePreview">
                    <?php if ($fileUrl): ?>

                        <?php if (in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif', 'webp'])): ?>
                            <img src="<?php echo $fileUrl; ?>"
                                class="img-thumbnail"
                                style="max-width:100%;max-height:150px">

                                <a href="<?php echo $fileUrl; ?>" target="_blank"
                                    class="btn btn-sm btn-outline-primary mt-1">
                                    <?php echo $this->lang->line("download"); ?>
                                </a>

                        <?php elseif ($fileExt === 'pdf'): ?>
                            <iframe src="<?php echo $fileUrl; ?>"
                                width="100%"
                                height="150"
                                style="border:1px solid #ccc"></iframe>

                            <a href="<?php echo $fileUrl; ?>" target="_blank"
                                    class="btn btn-sm btn-outline-primary mt-1">
                                    <?php echo $this->lang->line("download"); ?>
                                </a>

                        <?php elseif (in_array($fileExt, ['txt', 'xml', 'json', 'csv'])): ?>
                            <pre style="max-height:200px;overflow:auto;font-size:12px">
                                <?php echo htmlspecialchars(@file_get_contents($fileUrl)); ?>
                            </pre>
                            <a href="<?php echo $fileUrl; ?>" target="_blank"
                                    class="btn btn-sm btn-outline-primary mt-1">
                                    <?php echo $this->lang->line("download"); ?>
                                </a>
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
                                <div class="border p-2 rounded text-center">
                                    <div style="font-size:24px">📎</div>
                                    <div class="small text-muted"><?php echo basename($fileUrl); ?></div>
                                </div>
                            </a>
                        <?php endif; ?>

                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <?php echo form_button(array('data-bs-dismiss' => 'modal', 'type' => 'button', 'class' => 'btn btn-secondary', 'content' => $this->lang->line('close'))); ?>
        <?php echo form_button(array('name' => 'cgrerp_form_origin', 'type' => 'submit', 'class' => 'btn btn-success updatedebitdetails', 'content' => $this->lang->line('update'))); ?>
    </div>
    <?php echo form_close(); ?>

    <script src="<?php echo base_url() . 'assets/js/i18n/datepicker-' . $wz_lang . '.js'; ?>"></script>

    <script type="text/javascript">
        $(document).ready(function() {

            $("#error-concept_general").hide();
            $("#error-account_head").hide();
            $("#error-amount").hide();
            $("#error-expense_date").hide();
            $("#error-beneficiary_name").hide();
            $("#error-document_number").hide();

            var error_value = "<?php echo $this->lang->line("error_value"); ?>";
            var error_zero_value = "<?php echo $this->lang->line("error_zero_value"); ?>";
            var processing_request = "<?php echo $this->lang->line("processing_request"); ?>";
            var preview_not_available = "<?php echo $this->lang->line("preview_not_available"); ?>";
            var existingFileUrl = "<?php echo $fileUrl ? $fileUrl : ''; ?>";

            $("#expense_date").datepicker({
                dateFormat: "dd/mm/yy",
                changeMonth: true,
                changeYear: true,
                minDate: "-5y",
                maxDate: "1m",
                beforeShow: function(input, inst) {
                    setDatepickerPos(input, inst)
                },
                onSelect: function(date) {}
            });

            $('.ui-datepicker').addClass('notranslate');

            $("#update_debitdetails").submit(function(e) {
                e.preventDefault();
                var transactionId = $("#hdntransactionid").val();
                var displayid = $("#hdndisplayid").val();
                var originid = $("#hdnoriginid").val();
                var userid = $("#hdnuserid").val();
                var conceptgeneral = $("#concept_general").val();
                var account_head = $("#account_head").val();
                var expense_date = $("#expense_date").val();
                var beneficiary_name = $("#beneficiary_name").val().trim();
                var document_number = $("#document_number").val().trim();
                var amount = $("#amount").val();
                var fileInput = document.getElementById('expense_attachemnt_file');
                

                var isValid1 = true,
                    isValid2 = true,
                    isValid3 = true,
                    isValid4 = true,
                    isValid5 = true,
                    isValid6 = true,
                    isValid7 = true;

                if (conceptgeneral == 0) {
                    $("#error-concept_general").show();
                    isValid1 = false;
                } else {
                    $("#error-concept_general").hide();
                    isValid1 = true;
                }

                if (account_head == 0) {
                    $("#error-account_head").show();
                    isValid2 = false;
                } else {
                    $("#error-account_head").hide();
                    isValid2 = true;
                }

                if (expense_date.length == 0) {
                    $("#error-expense_date").show();
                    isValid3 = false;
                } else {
                    $("#error-expense_date").hide();
                    isValid3 = true;
                }

                if (beneficiary_name.length == 0) {
                    $("#error-beneficiary_name").show();
                    isValid4 = false;
                } else {
                    $("#error-beneficiary_name").hide();
                    isValid4 = true;
                }

                if (document_number.length == 0) {
                    $("#error-document_number").show();
                    isValid5 = false;
                } else {
                    $("#error-document_number").hide();
                    isValid5 = true;
                }

                if (amount.length == 0) {
                    $("#error-value").show();
                    $("#error-value").text(error_value);
                    isValid6 = false;
                } else {
                    $("#error-value").hide();
                    $("#error-value").text("");
                    isValid6 = true;
                }

                if (isValid6 == true) {
                    if (amount == 0) {
                        $("#error-value").show();
                        $("#error-value").text(error_zero_value);
                        isValid6 = false;
                    } else {
                        $("#error-value").hide();
                        $("#error-value").text("");
                        isValid6 = true;
                    }
                }

                if (fileInput.files.length > 0) {
                    // user selected new file
                    isValid7 = true;
                } else if (existingFileUrl !== '') {
                    // no new file, but old file exists
                    isValid7 = true;
                } else {
                    // no file at all
                    isValid7 = false;
                    toastr.error("<?php echo $this->lang->line('error_select_file'); ?>");
                }

                if (isValid1 && isValid2 && isValid3 && isValid4 && isValid5 && isValid6 && isValid7) {

                    var fd = new FormData(this);
                    fd.append("is_ajax", 2);
                    fd.append("originid", originid);
                    fd.append("transactionid", transactionId);
                    fd.append("displayid", displayid);
                    fd.append("userid", userid);

                    fd.append("concept_general", conceptgeneral);
                    fd.append("account_head", account_head);
                    fd.append("expense_date", expense_date);
                    fd.append("beneficiary_name", beneficiary_name);
                    fd.append("document_number", document_number);
                    fd.append("amount", amount);
                    fd.append("existing_file_url", existingFileUrl);
                    fd.append('expense_attachemnt_file', fileInput.files[0]);

                    fd.append("csrf_hash", $("#hdncsrf").val());
                    fd.append("add_type", 'debitdetails');

                    $(".updatedebitdetails").prop('disabled', true);
                    toastr.info(processing_request);

                    $("#loading").show();

                    $.ajax({
                        type: "POST",
                        url: e.target.action,
                        data: fd,
                        contentType: false,
                        cache: false,
                        processData: false,
                        success: function(JSON) {

                            $("#loading").hide();
                            $("#add-modal-data-bd").modal('hide');

                            if (JSON.redirect == true) {
                                window.location.replace(login_url);
                            } else if (JSON.error != '') {
                                toastr.clear();
                                toastr.error(JSON.error);
                                $('.updatedebitdetails').prop('disabled', false);
                                $('input[name="csrf_cgrerp"]').val(JSON.csrf_hash);
                            } else {
                                toastr.clear();
                                toastr.success(JSON.result);
                                $('.updatedebitdetails').prop('disabled', false);
                                $('input[name="csrf_cgrerp"]').val(JSON.csrf_hash);

                                $.ajax({
                                    url: base_url + "/get_ledger_details_by_user?originid=" + originid + "&userid=" + userid,
                                    cache: false,
                                    method: "GET",
                                    dataType: "json",
                                    success: function(JSON) {
                                        if (JSON.redirect == true) {
                                            window.location.replace(login_url);
                                        } else if (JSON.result != '') {

                                            $("#txtTotalCredits").text(JSON.result["totalCredits"]);
                                            $("#txtTotalDebits").text(JSON.result["totalDebits"]);
                                            $("#txtTotalOutstanding").text(JSON.result["totalOutstanding"]);
                                            parseCreditTransactions(JSON.result["creditTransactions"]);
                                            parseDebitTransactions(JSON.result["debitTransactions"]);
                                        } else {
                                            toastr.clear();
                                            toastr.error(JSON.error);
                                        }
                                    }
                                });
                            }
                        }
                    });
                }
            });
        });

        function setDatepickerPos(input, inst) {
            var rect = input.getBoundingClientRect();
            // use 'setTimeout' to prevent effect overridden by other scripts
            setTimeout(function() {
                var scrollTop = $("body").scrollTop();
                inst.dpDiv.css({
                    top: rect.top + input.offsetHeight + scrollTop
                });
            }, 0);
        }

        function parseCreditTransactions(credittransactions) {
            $('#xin_table_credits').DataTable({
                "bDestroy": true,
                "lengthMenu": [
                    [50, 100, 200, -1],
                    [50, 100, 200, "All"]
                ],
                data: credittransactions,
                columns: [{
                    data: 'action'
                }, {
                    data: 'transactionDisplayId'
                }, {
                    data: 'conceptGeneral'
                }, {
                    data: 'transactionDate'
                }, {
                    data: 'amount'
                }, {
                    data: 'fullName'
                }],
                //dom: 'lBfrtip',
                "sScrollX": "100%",
                "scrollCollapse": true,
                "bPaginate": true,
                "sPaginationType": "full_numbers",
                paging: true,
                searching: true,
                fixedColumns: true,
                responsive: true,
                "columnDefs": [{
                    "searchable": true,
                    "orderable": true,
                    "targets": 4,
                    "type": 'date'
                }],
                "order": [
                    [0, "asc"]
                ],
                "language": {
                    "url": datatable_language
                }
            });
        }

        function parseDebitTransactions(debittransactions) {
            $('#xin_table_debits').DataTable({
                "bDestroy": true,
                "lengthMenu": [
                    [50, 100, 200, -1],
                    [50, 100, 200, "All"]
                ],
                data: debittransactions,
                columns: [{
                    data: 'action'
                }, {
                    data: 'transactionDisplayId'
                }, {
                    data: 'transactionDate'
                }, {
                    data: 'expenseType'
                }, {
                    data: 'beneficiaryName'
                }, {
                    data: 'amount'
                }, {
                    data: 'updatedBy'
                }],
                //dom: 'lBfrtip',
                "sScrollX": "100%",
                "scrollCollapse": true,
                "bPaginate": true,
                "sPaginationType": "full_numbers",
                paging: true,
                searching: true,
                fixedColumns: true,
                responsive: true,
                "columnDefs": [{
                    "searchable": true,
                    "orderable": true,
                    "targets": 4,
                    "type": 'date'
                }],
                "order": [
                    [0, "asc"]
                ],
                "language": {
                    "url": datatable_language
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
                img.style.maxHeight = '150px';
                preview.appendChild(img);
            }

            // 📄 PDF
            else if (file.type === 'application/pdf') {
                const iframe = document.createElement('iframe');
                iframe.src = URL.createObjectURL(file);
                iframe.style.width = '100%';
                iframe.style.height = '150px';
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
            //    preview.innerHTML = `<span class="text-muted">📎 ${file.name}</span>`;
            //}

            existingFileUrl = null; // Clear existing file URL since a new file is selected
        }
    </script>