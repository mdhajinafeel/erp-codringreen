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
<?php $attributes = array('name' => 'update_creditdetails', 'id' => 'update_creditdetails', 'autocomplete' => 'off', 'class' => '"m-b-1'); ?>
<?php $hidden = array('_method' => $pagetype); ?>
<?php echo form_open_multipart('expenseledger/update_creditdetails', $attributes, $hidden); ?>
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
                <label class="control-label"><?php echo isset($credit_details[0]->transaction_display_id) ? $credit_details[0]->transaction_display_id : ''; ?></label>
            </div>
        </div>

        <div class="col-md-6">
            <label for="amount"><?php echo $this->lang->line('amount'); ?></label>
            <input class="form-control" placeholder="<?php echo $this->lang->line('amount'); ?>" name="amount" id="amount" type="number" step="any" value="<?php echo isset($credit_details[0]->amount) ? $credit_details[0]->amount + 0 : '0'; ?>">
            <label id="error-amount" class="error-text"><?php echo $this->lang->line('error_value'); ?></label>
        </div>

    </div>
    <div class="row">
        <div class="col-md-6">
            <label for="concept_general"><?php echo $this->lang->line('concept_general'); ?></label>
            <input class="form-control" placeholder="<?php echo $this->lang->line('concept_general'); ?>" name="concept_general" id="concept_general" type="text" value="<?php echo isset($credit_details[0]->concept_general) ? $credit_details[0]->concept_general : ''; ?>">
            <label id="error-conceptgeneral" class="error-text"><?php echo $this->lang->line('error_conceptgeneral'); ?></label>
        </div>
        <div class="col-md-6">
            <label for="transaction_date"><?php echo $this->lang->line('transaction_date'); ?></label>
            <input class="form-control" placeholder="<?php echo $this->lang->line('transaction_date'); ?>" name="transaction_date" id="transaction_date" type="text" readonly value="<?php echo isset($credit_details[0]->transaction_date) ? $credit_details[0]->transaction_date : ''; ?>">
            <label id="error-transactiondate" class="error-text"><?php echo $this->lang->line('error_date'); ?></label>
        </div>
    </div>
</div>
<div class="modal-footer">
    <?php echo form_button(array('data-bs-dismiss' => 'modal', 'type' => 'button', 'class' => 'btn btn-secondary', 'content' => $this->lang->line('close'))); ?>
    <?php echo form_button(array('name' => 'cgrerp_form_origin', 'type' => 'submit', 'class' => 'btn btn-success updatecreditdetails', 'content' => $this->lang->line('update'))); ?>
</div>
<?php echo form_close(); ?>
<script src="<?php echo base_url() . 'assets/js/i18n/datepicker-' . $wz_lang . '.js'; ?>"></script>

<script type="text/javascript">
    $(document).ready(function() {

        $("#error-amount").hide();
        $("#error-conceptgeneral").hide();
        $("#error-transactiondate").hide();

        var error_value = "<?php echo $this->lang->line("error_value"); ?>";
        var error_zero_value = "<?php echo $this->lang->line("error_zero_value"); ?>";

        $("#transaction_date").datepicker({
            dateFormat: "dd/mm/yy",
            changeMonth: true,
            changeYear: true,
            minDate: "-1y",
            maxDate: "1m",
            onSelect: function(date) {}
        });

        $('.ui-datepicker').addClass('notranslate');

        $("#update_creditdetails").submit(function(e) {
            e.preventDefault();
            var transactionId = $("#hdntransactionid").val();
            var displayid = $("#hdndisplayid").val();
            var originid = $("#hdnoriginid").val();
            var userid = $("#hdnuserid").val();
            var amount = $("#amount").val();
            var conceptgeneral = $("#concept_general").val().trim();
            var transactiondate = $("#transaction_date").val();

            var isValid1 = true,
                isValid2 = true,
                isValid3 = true,
                isValid4 = true;

            if (amount.length == 0) {
                $("#error-value").show();
                $("#error-value").text(error_value);
                isValid1 = false;
            } else {
                $("#error-value").hide();
                $("#error-value").text("");
                isValid1 = true;
            }

            if (isValid1 == true) {
                if (amount == 0) {
                    $("#error-value").show();
                    $("#error-value").text(error_zero_value);
                    isValid2 = false;
                } else {
                    $("#error-value").hide();
                    $("#error-value").text("");
                    isValid2 = true;
                }
            }

            if (transactiondate.length == 0) {
                $("#error-date").show();
                isValid3 = false;
            } else {
                $("#error-date").hide();
                isValid3 = true;
            }

            if (conceptgeneral.length == 0) {
                $("#error-conceptgeneral").show();
                isValid4 = false;
            } else {
                $("#error-conceptgeneral").hide();
                isValid4 = true;
            }

            if (isValid1 && isValid2 && isValid3 && isValid4) {

                var fd = new FormData(this);
                fd.append("is_ajax", 2);
                fd.append("originid", originid);
                fd.append("transactionid", transactionId);
                fd.append("displayid", displayid);
                fd.append("userid", userid);
                fd.append("amount", amount);
                fd.append("conceptgeneral", conceptgeneral);
                fd.append("transactiondate", transactiondate);
                fd.append("csrf_hash", $("#hdncsrf").val());
                fd.append("add_type", 'creditdetails');

                $(".updatecreditdetails").prop('disabled', true);
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
                            $('.updatecreditdetails').prop('disabled', false);
                            $('input[name="csrf_cgrerp"]').val(JSON.csrf_hash);
                        } else {
                            toastr.clear();
                            toastr.success(JSON.result);
                            $('.updatecreditdetails').prop('disabled', false);
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

        $("#origin").change(function() {
            fetchLedgerTypes($("#origin").val());
        });
    });

    function fetchLedgerTypes(originid) {
        $("#loading").show();
        $.ajax({
            url: base_url + "/get_ledger_types_by_origin?originid=" + originid,
            cache: false,
            method: "GET",
            dataType: "json",
            success: function(JSON) {

                $("#loading").hide();

                if (JSON.redirect == true) {
                    window.location.replace(login_url);
                } else if (JSON.result != '') {
                    $("#ledger_type").empty();
                    $("#ledger_type").append(JSON.result);
                }
            }
        });
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
</script>