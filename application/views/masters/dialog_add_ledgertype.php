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
<?php $attributes = array('name' => 'add_ledgertype', 'id' => 'add_ledgertype', 'autocomplete' => 'off', 'class' => '"m-b-1'); ?>
<?php $hidden = array('_method' => $pagetype); ?>
<?php echo form_open_multipart('ledgertypes/add', $attributes, $hidden); ?>
<div class="modal-body">
    <input type="hidden" id="pagetype" name="pagetype" value="<?php echo $pagetype; ?>">
    <input type="hidden" id="hdnledgertypeid" name="hdnledgertypeid" value="<?php echo $ledgertypeid;  ?>">
    <div class="row">
        <div class="col-md-6">
            <label for="ledger_name"><?php echo $this->lang->line('ledger_type_name'); ?></label>
            <input class="form-control" placeholder="<?php echo $this->lang->line('ledger_type_name'); ?>" name="ledger_name" id="ledger_name" type="text" value="<?php echo isset($get_ledger_details[0]->ledger_name) ? $get_ledger_details[0]->ledger_name : ''; ?>">
            <label id="error-ledgername" class="error-text"><?php echo $this->lang->line('error_name'); ?></label>
        </div>
        <div class="col-md-6">
            <label for="origin"><?php echo $this->lang->line('origin'); ?></label>
            <select class="form-control" name="origin" id="origin" data-plugin="select_erp" <?php if ($pagetype == "edit") { ?> disabled <?php } ?>>
                <option value="0"><?php echo $this->lang->line("select"); ?></option>
                <?php foreach ($applicable_origins as $origin) { ?>
                    <?php if ($get_ledger_details[0]->origin_id == $origin->id) { ?>
                        <option value="<?php echo $origin->id; ?>" selected="selected"><?php echo $origin->origin_name; ?></option>
                    <?php } else { ?>
                        <option value="<?php echo $origin->id; ?>"><?php echo $origin->origin_name; ?></option>
                    <?php } ?>
                <?php } ?>
            </select>
            <label id="error-origin" class="error-text"><?php echo $this->lang->line('error_origin_screen'); ?></label>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <label for="status"><?php echo $this->lang->line('status'); ?></label>
            <select class="form-control" name="status" id="status" data-plugin="select_erp">
                <?php if ($pagetype == 'add') { ?>
                    <option value="1"><?php echo $this->lang->line('active'); ?></option>
                    <option value="0"><?php echo $this->lang->line('inactive'); ?></option>
                <?php } else { ?>
                    <option value="1" <?php if ($get_ledger_details[0]->is_active == 1) : ?> selected="selected" <?php endif; ?>><?php echo $this->lang->line('active'); ?></option>
                    <option value="0" <?php if ($get_ledger_details[0]->is_active == 0) : ?> selected="selected" <?php endif; ?>><?php echo $this->lang->line('inactive'); ?></option>
                <?php } ?>
            </select>
        </div>
    </div>
</div>
<div class="modal-footer">
    <?php echo form_button(array('data-bs-dismiss' => 'modal', 'type' => 'button', 'class' => 'btn btn-secondary', 'content' => $this->lang->line('close'))); ?>
    <?php echo form_button(array('name' => 'cgrerp_form_origin', 'type' => 'submit', 'class' => 'btn btn-success addledgertype', 'content' => $pagetype == 'edit' ? $this->lang->line('update') : $this->lang->line('add'))); ?>
</div>
<?php echo form_close(); ?>
<link rel="stylesheet" href="<?php echo base_url() . 'assets/css/bootstrap-multiselect.css'; ?>">
<script src="<?php echo base_url() . 'assets/js/bootstrap-multiselect.js'; ?>"></script>

<script type="text/javascript">
    $(document).ready(function() {

        $("#error-ledgername").hide();
        $("#error-origin").hide();

        $("#add_ledgertype").submit(function(e) {
            e.preventDefault();
            var pagetype = $("#pagetype").val().trim();
            var ledgertypeid = $("#hdnledgertypeid").val();
            var name = $("#ledger_name").val().trim();
            var origin = $("#origin").val();

            var isValid1 = true,
                isValid2 = true;

            if (name.length == 0) {
                $("#error-ledgername").show();
                isValid1 = false;
            } else {
                $("#error-ledgername").hide();
                isValid1 = true;
            }

            if (origin == 0) {
                $("#error-origin").show();
                isValid2 = false;
            } else {
                $("#error-origin").hide();
                isValid2 = true;
            }

            if (isValid1 && isValid2) {

                var fd = new FormData(this);
                fd.append("ledger_type_name", name);
                fd.append("is_ajax", 2);
                fd.append("form", action);
                fd.append("add_type", "ledgertypes");
                fd.append("action_type", pagetype);
                fd.append("origin", origin);
                fd.append("ledgertype_id", $("#hdnledgertypeid").val());

                $(".addledgertype").prop('disabled', true);
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
                            $('.addledgertype').prop('disabled', false);
                            $('input[name="csrf_cgrerp"]').val(JSON.csrf_hash);
                        } else {
                            toastr.clear();
                            toastr.success(JSON.result);
                            $('.addledgertype').prop('disabled', false);
                            $('input[name="csrf_cgrerp"]').val(JSON.csrf_hash);
                            $("#add-modal-data-lg-bd").modal('hide');

                            $('#xin_table_ledgertypes').DataTable().ajax.reload(null, false);
                        }
                    }
                });
            }
        });
    });
</script>