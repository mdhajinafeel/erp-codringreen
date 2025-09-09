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
<?php $attributes = array('name' => 'add_machine', 'id' => 'add_machine', 'autocomplete' => 'off', 'class' => '"m-b-1'); ?>
<?php $hidden = array('_method' => $pagetype); ?>
<?php echo form_open_multipart('machines/add', $attributes, $hidden); ?>
<div class="modal-body">
    <input type="hidden" id="pagetype" name="pagetype" value="<?php echo $pagetype; ?>">
    <input type="hidden" id="hdnmachineid" name="hdnmachineid" value="<?php echo $machineid;  ?>">
    <div class="row mb-4">
        <div class="col-md-6">
            <label for="origin"><?php echo $this->lang->line('origin'); ?></label>
            <select class="form-control" name="origin" id="origin" data-plugin="select_erp" <?php if ($pagetype == "edit") { ?> disabled <?php } ?>>
                <option value="0"><?php echo $this->lang->line("select"); ?></option>
                <?php foreach ($applicable_origins as $origin) { ?>
                    <?php if ($get_machine_details[0]->origin_id == $origin->id) { ?>
                        <option value="<?php echo $origin->id; ?>" selected="selected"><?php echo $origin->origin_name; ?></option>
                    <?php } else { ?>
                        <option value="<?php echo $origin->id; ?>"><?php echo $origin->origin_name; ?></option>
                    <?php } ?>
                <?php } ?>
            </select>
            <label id="error-origin" class="error-text"><?php echo $this->lang->line('error_origin_screen'); ?></label>
        </div>
        <div class="col-md-6">
            <label for="supplier_name"><?php echo $this->lang->line('supplier_name'); ?></label>
            <select class="form-control" name="supplier_name" id="supplier_name" data-plugin="select_erp">
                <option value="0"><?php echo $this->lang->line("select"); ?></option>
                <?php foreach ($suppliers as $supplier) { ?>
                    <?php if ($get_machine_details[0]->supplier_id == $supplier->id) { ?>
                        <option value="<?php echo $supplier->id; ?>" selected="selected"><?php echo $supplier->supplier_name; ?></option>
                    <?php } else { ?>
                        <option value="<?php echo $supplier->id; ?>"><?php echo $supplier->supplier_name; ?></option>
                    <?php } ?>
                <?php } ?>
            </select>
            <label id="error-supplier" class="error-text"><?php echo $this->lang->line('error_select_supplier'); ?></label>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-md-6">
            <label for="machine_name"><?php echo $this->lang->line('machine_name'); ?></label>
            <input class="form-control" placeholder="<?php echo $this->lang->line('machine_name'); ?>" name="machine_name" id="machine_name" type="text" value="<?php echo isset($get_machine_details[0]->machine_type) ? $get_machine_details[0]->machine_type : ''; ?>">
            <label id="error-name" class="error-text"><?php echo $this->lang->line('error_machine_name'); ?></label>
        </div>
        <div class="col-md-6">
            <label for="chassis_model"><?php echo $this->lang->line('chassis_model'); ?></label>
            <input class="form-control" placeholder="<?php echo $this->lang->line('chassis_model'); ?>" name="chassis_model" id="chassis_model" type="text" value="<?php echo isset($get_machine_details[0]->chassis_no) ? $get_machine_details[0]->chassis_no : ''; ?>">
            <label id="error-chassis_model" class="error-text"><?php echo $this->lang->line('error_chassis_model'); ?></label>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-md-6">
			<label for="status"><?php echo $this->lang->line('status'); ?></label>
			<select class="form-control" name="status" id="status" data-plugin="select_erp">
				<?php if ($pagetype == 'add') { ?>
					<option value="1"><?php echo $this->lang->line('active'); ?></option>
					<option value="0"><?php echo $this->lang->line('inactive'); ?></option>
				<?php } else { ?>
					<option value="1" <?php if ($get_machine_details[0]->is_active == 1) : ?> selected="selected" <?php endif; ?>><?php echo $this->lang->line('active'); ?></option>
					<option value="0" <?php if ($get_machine_details[0]->is_active == 0) : ?> selected="selected" <?php endif; ?>><?php echo $this->lang->line('inactive'); ?></option>
				<?php } ?>
			</select>
		</div>
    </div>
</div>
<div class="modal-footer">
    <?php echo form_button(array('data-bs-dismiss' => 'modal', 'type' => 'button', 'class' => 'btn btn-secondary', 'content' => $this->lang->line('close'))); ?>
    <?php echo form_button(array('name' => 'cgrerp_form_origin', 'type' => 'submit', 'class' => 'btn btn-success addmachine', 'content' => $pagetype == 'edit' ? $this->lang->line('update') : $this->lang->line('add'))); ?>
</div>
<?php echo form_close(); ?>
<link rel="stylesheet" href="<?php echo base_url() . 'assets/css/bootstrap-multiselect.css'; ?>">
<script src="<?php echo base_url() . 'assets/js/bootstrap-multiselect.js'; ?>"></script>

<script type="text/javascript">
    $(document).ready(function() {

        $("#error-name").hide();
        $("#error-chassis_model").hide();
        $("#error-origin").hide();
        $("#error-supplier").hide();

        $("#add_machine").submit(function(e) {
            e.preventDefault();
            var pagetype = $("#pagetype").val().trim();
            var machineid = $("#hdnmachineid").val().trim();
            var machine_name = $("#machine_name").val().trim();
            var chassis_model = $("#chassis_model").val().trim();
            var supplier = $("#supplier_name").val();
            var origin = $("#origin").val();
            var status = $("#status").val();

            var isValid1 = true,
                isValid2 = true,
                isValid3 = true,
                isValid4 = true;

            if (machine_name.length == 0) {
                $("#error-name").show();
                isValid1 = false;
            } else {
                $("#error-name").hide();
                isValid1 = true;
            }

            if (chassis_model.length == 0) {
                $("#error-chassis_model").show();
                isValid2 = false;
            } else {
                $("#error-chassis_model").hide();
                isValid2 = true;
            }

            if (supplier == 0) {
                $("#error-supplier").show();
                isValid3 = false;
            } else {
                $("#error-supplier").hide();
                isValid3 = true;
            }

            if (origin == 0) {
                $("#error-origin").show();
                isValid4 = false;
            } else {
                $("#error-origin").hide();
                isValid4 = true;
            }

            if (isValid1 && isValid2 && isValid3 && isValid4) {

                var fd = new FormData(this);
                fd.append("machine_name", machine_name);
                fd.append("chassis_model", chassis_model);
                fd.append("origin", origin);
                fd.append("supplier", supplier);
                fd.append("is_ajax", 2);
                fd.append("form", action);
                fd.append("add_type", "machines");
                fd.append("action_type", pagetype);
                fd.append("machineid", machineid);
                fd.append("origin", origin);
                fd.append("status", status);

                $(".addmachine").prop('disabled', true);
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
                            $('.addmachine').prop('disabled', false);
                            $('input[name="csrf_cgrerp"]').val(JSON.csrf_hash);
                        } else {
                            toastr.clear();
                            toastr.success(JSON.result);
                            $('.addmachine').prop('disabled', false);
                            $('input[name="csrf_cgrerp"]').val(JSON.csrf_hash);
                            $("#add-modal-data-lg-bd").modal('hide');

                            $('#xin_table_machines').DataTable().ajax.reload(null, false);
                        }
                    }
                });
            }
        });
    });

    $("#origin").change(function() {
        var originid = $(this).val();
        if (originid != 0) {
            fetchSuppliers(originid);
        } else {
            $("#supplier_name").empty();
            $("#supplier_name").append('<option value="0"><?php echo $this->lang->line("select"); ?></option>');
            $("#supplier_name").select2({
                dropdownCssClass: "myFont",
                dropdownParent: $('#ajax_modal_lg_bd')
            });
        }
    });

    function fetchSuppliers(originid) {
        $("#loading").show();
        $.ajax({
            url: base_url + "/fetch_suppliers?originid=" + originid,
            cache: false,
            method: "GET",
            dataType: 'json',
            success: function(JSON) {
                $("#loading").hide();
                if (JSON.redirect == true) {
                    window.location.replace(login_url);
                } else if (JSON.result != '') {
                    $("#supplier_name").empty();
                    $("#supplier_name").append(JSON.result);
                    $("#supplier_name").select2({
                        dropdownCssClass: "myFont",
                        dropdownParent: $('#ajax_modal_lg_bd')
                    });
                }
            }
        });
    }
</script>