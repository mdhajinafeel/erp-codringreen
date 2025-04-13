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
<?php $attributes = array('name' => 'add_accounthead', 'id' => 'add_accounthead', 'autocomplete' => 'off', 'class' => '"m-b-1'); ?>
<?php $hidden = array('_method' => $pagetype); ?>
<?php echo form_open_multipart('accountheads/add', $attributes, $hidden); ?>
<div class="modal-body">
    <input type="hidden" id="pagetype" name="pagetype" value="<?php echo $pagetype; ?>">
    <input type="hidden" id="hdnaccountheadid" name="hdnaccountheadid" value="<?php echo $accountheadid;  ?>">
    <div class="row">
        <div class="col-md-6">
            <label for="app_account_head"><?php echo $this->lang->line('app_account_head'); ?></label>
            <input class="form-control text-uppercase" placeholder="<?php echo $this->lang->line('app_account_head'); ?>" name="app_account_head" id="app_account_head" type="text" value="<?php echo isset($get_accounthead_details[0]->name_in_app) ? $get_accounthead_details[0]->name_in_app : ''; ?>">
            <label id="error-appaccounthead" class="error-text"><?php echo $this->lang->line('error_name'); ?></label>
        </div>
        <div class="col-md-6">
            <label for="ledger_account_head"><?php echo $this->lang->line('ledger_account_head'); ?></label>
            <input class="form-control text-uppercase" placeholder="<?php echo $this->lang->line('ledger_account_head'); ?>" name="ledger_account_head" id="ledger_account_head" type="text" value="<?php echo isset($get_accounthead_details[0]->name_in_ledger) ? $get_accounthead_details[0]->name_in_ledger : ''; ?>">
            <label id="error-ledgeraccounthead" class="error-text"><?php echo $this->lang->line('error_name'); ?></label>
        </div>

    </div>
    <div class="row">
        <div class="col-md-6">
            <label for="origin"><?php echo $this->lang->line('origin'); ?></label>
            <select class="form-control" name="origin" id="origin" data-plugin="select_erp" <?php if ($pagetype == "edit") { ?> disabled <?php } ?>>
                <?php foreach ($applicable_origins as $origin) { ?>
                    <?php if ($get_accounthead_details[0]->origin_id == $origin->id) { ?>
                        <option value="<?php echo $origin->id; ?>" selected="selected"><?php echo $origin->origin_name; ?></option>
                    <?php } else { ?>
                        <option value="<?php echo $origin->id; ?>"><?php echo $origin->origin_name; ?></option>
                    <?php } ?>
                <?php } ?>
            </select>
        </div>
        <div class="col-md-6">
            <label for="ledger_type"><?php echo $this->lang->line('ledger_type'); ?></label>
            <select class="form-control" name="ledger_type" id="ledger_type" data-plugin="select_erp" <?php if ($pagetype == "edit") { ?> disabled <?php } ?>>
                <option value="0"><?php echo $this->lang->line("select"); ?></option>
                <?php foreach ($ledgertypes as $ledgertype) { ?>
                    <?php if ($get_accounthead_details[0]->ledger_type == $ledgertype->id) { ?>
                        <option value="<?php echo $ledgertype->id; ?>" selected="selected"><?php echo $ledgertype->ledger_name; ?></option>
                    <?php } else { ?>
                        <option value="<?php echo $ledgertype->id; ?>"><?php echo $ledgertype->ledger_name; ?></option>
                    <?php } ?>
                <?php } ?>
            </select>
            <label id="error-ledgertype" class="error-text"><?php echo $this->lang->line('error_ledger_type'); ?></label>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <label for="account_head_code"><?php echo $this->lang->line('account_head_code'); ?></label>
            <input class="form-control text-uppercase" placeholder="<?php echo $this->lang->line('account_head_code'); ?>" name="account_head_code" id="account_head_code" type="text" value="<?php echo isset($get_accounthead_details[0]->code) ? $get_accounthead_details[0]->code : ''; ?>">
            <label id="error-accountheadcode" class="error-text"><?php echo $this->lang->line('error_account_head_code'); ?></label>
        </div>
        <div class="col-md-6">
            <label for="status"><?php echo $this->lang->line('status'); ?></label>
            <select class="form-control" name="status" id="status" data-plugin="select_erp">
                <?php if ($pagetype == 'add') { ?>
                    <option value="1"><?php echo $this->lang->line('active'); ?></option>
                    <option value="0"><?php echo $this->lang->line('inactive'); ?></option>
                <?php } else { ?>
                    <option value="1" <?php if ($get_accounthead_details[0]->is_active == 1) : ?> selected="selected" <?php endif; ?>><?php echo $this->lang->line('active'); ?></option>
                    <option value="0" <?php if ($get_accounthead_details[0]->is_active == 0) : ?> selected="selected" <?php endif; ?>><?php echo $this->lang->line('inactive'); ?></option>
                <?php } ?>
            </select>
        </div>
    </div>
</div>
<div class="modal-footer">
    <?php echo form_button(array('data-bs-dismiss' => 'modal', 'type' => 'button', 'class' => 'btn btn-secondary', 'content' => $this->lang->line('close'))); ?>
    <?php echo form_button(array('name' => 'cgrerp_form_origin', 'type' => 'submit', 'class' => 'btn btn-success addaccounthead', 'content' => $pagetype == 'edit' ? $this->lang->line('update') : $this->lang->line('add'))); ?>
</div>
<?php echo form_close(); ?>
<link rel="stylesheet" href="<?php echo base_url() . 'assets/css/bootstrap-multiselect.css'; ?>">
<script src="<?php echo base_url() . 'assets/js/bootstrap-multiselect.js'; ?>"></script>

<script type="text/javascript">
    $(document).ready(function() {

        $("#error-appaccounthead").hide();
        $("#error-ledgeraccounthead").hide();
        $("#error-ledgertype").hide();
        $("#error-accountheadcode").hide();

        $("#add_accounthead").submit(function(e) {
            e.preventDefault();
            var pagetype = $("#pagetype").val().trim();
            var accountheadid = $("#hdnaccountheadid").val();
            var app_account_head = $("#app_account_head").val().trim();
            var ledger_account_head = $("#ledger_account_head").val().trim();
            var origin = $("#origin").val();
            var ledger_type = $("#ledger_type").val();
            var account_head_code = $("#account_head_code").val();

            var isValid1 = true,
                isValid2 = true,
                isValid3 = true,
                isValid4 = true;

            if (app_account_head.length == 0) {
                $("#error-appaccounthead").show();
                isValid1 = false;
            } else {
                $("#error-appaccounthead").hide();
                isValid1 = true;
            }

            if (ledger_account_head.length == 0) {
                $("#error-ledgeraccounthead").show();
                isValid2 = false;
            } else {
                $("#error-ledgeraccounthead").hide();
                isValid2 = true;
            }

            if (ledger_type == 0) {
                $("#error-ledgertype").show();
                isValid3 = false;
            } else {
                $("#error-ledgertype").hide();
                isValid3 = true;
            }

            if (account_head_code.length == 0) {
                $("#error-accountheadcode").show();
                isValid4 = false;
            } else {
                $("#error-accountheadcode").hide();
                isValid4 = true;
            }

            if (isValid1 && isValid2 && isValid3 && isValid4) {

                var fd = new FormData(this);
                fd.append("ledger_type_name", name);
                fd.append("is_ajax", 2);
                fd.append("form", action);
                fd.append("add_type", "accounthead");
                fd.append("action_type", pagetype);
                fd.append("origin", origin);
                fd.append("app_account_head", app_account_head);
                fd.append("ledger_account_head", ledger_account_head);
                fd.append("accountheadid", accountheadid);
                fd.append("accountheadcode", account_head_code);
                fd.append("ledger_type", ledger_type);

                $(".addaccounthead").prop('disabled', true);
                toastr.info(processing_request);
                var obj = $(this),
                    action = obj.attr('name'),
                    form_table = obj.data('form-table');

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
                        if (JSON.redirect == true) {
                            window.location.replace(login_url);
                        } else if (JSON.error != '') {
                            toastr.clear();
                            toastr.error(JSON.error);
                            $('.addaccounthead').prop('disabled', false);
                            $('input[name="csrf_cgrerp"]').val(JSON.csrf_hash);
                        } else {
                            toastr.clear();
                            toastr.success(JSON.result);
                            $('.addaccounthead').prop('disabled', false);
                            $('input[name="csrf_cgrerp"]').val(JSON.csrf_hash);
                            $("#add-modal-data-lg-bd").modal('hide');

                            $('#xin_table_accountheads').DataTable().ajax.reload(null, false);
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
</script>