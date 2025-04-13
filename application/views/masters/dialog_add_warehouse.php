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
<?php $attributes = array('name' => 'add_warehouse', 'id' => 'add_warehouse', 'autocomplete' => 'off', 'class' => '"m-b-1'); ?>
<?php $hidden = array('_method' => $pagetype); ?>
<?php echo form_open_multipart('warehouses/add', $attributes, $hidden); ?>
<div class="modal-body">
	<input type="hidden" id="pagetype" name="pagetype" value="<?php echo $pagetype; ?>">
	<input type="hidden" id="hdnwarehouseid" name="hdnwarehouseid" value="<?php echo $warehouseid;  ?>">
	<div class="row">
		<div class="col-md-6">
			<label for="name"><?php echo $this->lang->line('wh_name'); ?></label>
			<input class="form-control" placeholder="<?php echo $this->lang->line('wh_name'); ?>" name="name" id="name" type="text" value="<?php echo isset($get_warehouse_details[0]->warehouse_name) ? $get_warehouse_details[0]->warehouse_name : ''; ?>">
			<label id="error-name" class="error-text"><?php echo $this->lang->line('error_name'); ?></label>
		</div>
		<div class="col-md-6">
			<label for="ownersname"><?php echo $this->lang->line('owner_name'); ?></label>
			<input class="form-control" placeholder="<?php echo $this->lang->line('owner_name'); ?>" name="ownersname" id="ownersname" type="text" value="<?php echo isset($get_warehouse_details[0]->warehouse_ownername) ? $get_warehouse_details[0]->warehouse_ownername : ''; ?>">
			<label id="error-ownersname" class="error-text"><?php echo $this->lang->line('error_owner'); ?></label>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6">
			<label for="address"><?php echo $this->lang->line('address'); ?></label>
			<textarea name="address" id="address" maxlength="400" rows="3" class="form-control" placeholder="<?php echo $this->lang->line('address'); ?>"><?php echo isset($get_warehouse_details[0]->warehouse_address) ? htmlspecialchars($get_warehouse_details[0]->warehouse_address) : ''; ?></textarea>
			<label id="error-address" class="error-text"><?php echo $this->lang->line('error_address'); ?></label>
		</div>
		<div class="col-md-6">
			<label for="origin"><?php echo $this->lang->line('origin'); ?></label>
			<select class="form-control" name="origin" id="origin" data-plugin="select_erp" <?php if($pagetype == "edit") { ?> disabled <?php } ?>>
				<option value="0"><?php echo $this->lang->line("select"); ?></option>
				<?php foreach ($applicable_origins as $origin) { ?>
					<?php if ($get_warehouse_details[0]->origin_id == $origin->id) { ?>
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
			<label for="port_of_loading"><?php echo $this->lang->line('port_of_loading'); ?></label>
			<select class="form-control" name="port_of_loading" id="port_of_loading" data-plugin="select_erp">
				<option value="0"><?php echo $this->lang->line("select"); ?></option>
				<?php foreach ($export_pol as $pol) { ?>
					<?php if ($get_warehouse_details[0]->pol == $pol->id) { ?>
						<option value="<?php echo $pol->id; ?>" selected="selected"><?php echo $pol->pol_name; ?></option>
					<?php } else { ?>
						<option value="<?php echo $pol->id; ?>"><?php echo $pol->pol_name; ?></option>
					<?php } ?>
				<?php } ?>
			</select>
			<label id="error-pol" class="error-text"><?php echo $this->lang->line('error_select_name'); ?></label>
		</div>
		<div class="col-md-6">
			<label for="status"><?php echo $this->lang->line('status'); ?></label>
			<select class="form-control" name="status" id="status" data-plugin="select_erp">
				<?php if ($pagetype == 'add') { ?>
					<option value="1"><?php echo $this->lang->line('active'); ?></option>
					<option value="0"><?php echo $this->lang->line('inactive'); ?></option>
				<?php } else { ?>
					<option value="1" <?php if ($get_warehouse_details[0]->is_active == 1) : ?> selected="selected" <?php endif; ?>><?php echo $this->lang->line('active'); ?></option>
					<option value="0" <?php if ($get_warehouse_details[0]->is_active == 0) : ?> selected="selected" <?php endif; ?>><?php echo $this->lang->line('inactive'); ?></option>
				<?php } ?>
			</select>
		</div>
	</div>
</div>
<div class="modal-footer">
	<?php echo form_button(array('data-bs-dismiss' => 'modal', 'type' => 'button', 'class' => 'btn btn-secondary', 'content' => $this->lang->line('close'))); ?>
	<?php echo form_button(array('name' => 'cgrerp_form_origin', 'type' => 'submit', 'class' => 'btn btn-success addwarehouse', 'content' => $pagetype == 'edit' ? $this->lang->line('update') :$this->lang->line('add'))); ?>
</div>
<?php echo form_close(); ?>
<link rel="stylesheet" href="<?php echo base_url() . 'assets/css/bootstrap-multiselect.css'; ?>">
<script src="<?php echo base_url() . 'assets/js/bootstrap-multiselect.js'; ?>"></script>

<script type="text/javascript">

	$(document).ready(function() {

		$("#error-name").hide();
		$("#error-ownersname").hide();
		$("#error-address").hide();
		$("#error-origin").hide();
		$("#error-pol").hide();

		$("#add_warehouse").submit(function(e) {
			e.preventDefault();
			var pagetype = $("#pagetype").val().trim();
			var warehouseid = $("#hdnwarehouseid").val().trim();
			var name = $("#name").val().trim();
			var ownersname = $("#ownersname").val().trim();
			var address = $("#address").val().trim();
			var whorigin = $("#origin").val();
			var port_of_loading = $("#port_of_loading").val();
			
			var isValid1 = true,
				isValid2 = true, isValid3 = true, isValid4 = true, isValid5 = true;

			if (name.length == 0) {
				$("#error-name").show();
				isValid1 = false;
			} else {
				$("#error-name").hide();
				isValid1 = true;
			}

			if (ownersname.length == 0) {
				$("#error-ownersname").show();
				isValid2 = false;
			} else {
				$("#error-ownersname").hide();
				isValid2 = true;
			}

			if (address.length == 0) {
				$("#error-address").show();
				isValid3 = false;
			} else {
				$("#error-address").hide();
				isValid3 = true;
			}

			if (whorigin == 0) {
				$("#error-origin").show();
				isValid4 = false;
			} else {
				$("#error-origin").hide();
				isValid4 = true;
			}

			if(port_of_loading == 0) {
				$("#error-pol").show();
				isValid5 = false;
			} else {
				$("#error-pol").hide();
				isValid5 = true;
			}

			if (isValid1 && isValid2 && isValid3 && isValid4 && isValid5) {

				var fd = new FormData(this);
				fd.append("wh_name", name);
				fd.append("wh_owners_name", ownersname);
				fd.append("wh_address", address);
				fd.append("is_ajax", 2);
				fd.append("form", action);
				fd.append("add_type", "warehouses");
				fd.append("action_type", pagetype);
				fd.append("warehouse_id", warehouseid);
				fd.append("whorigin", whorigin);
				fd.append("port_of_loading", port_of_loading);

				$(".addwarehouse").prop('disabled', true);
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
							$('.addwarehouse').prop('disabled', false);
							$('input[name="csrf_cgrerp"]').val(JSON.csrf_hash);
						} else {
							toastr.clear();
							toastr.success(JSON.result);
							$('.addwarehouse').prop('disabled', false);
							$('input[name="csrf_cgrerp"]').val(JSON.csrf_hash);
							$("#add-modal-data-lg-bd").modal('hide');

							$('#xin_table_warehouse').DataTable().ajax.reload(null, false);
						}
					}
				});
			}
		});

		$("#origin").change(function() {
			if ($("#origin").val() == 0) {
				$("#port_of_loading").attr("disabled", true);
				fetchExportPolByOrigin(0);
			} else {
				fetchExportPolByOrigin($("#origin").val());
			}
		});
	});

	function fetchExportPolByOrigin(originid) {
        $("#loading").show();
        $.ajax({
            url: base_url + "/get_export_pol_by_origin?originid=" + originid,
            cache: false,
            method: "GET",
            dataType: 'json',
            success: function(JSON) {
                $("#loading").hide();
                if (JSON.redirect == true) {
                    window.location.replace(login_url);
                } else if (JSON.result != '') {
                    $("#port_of_loading").empty();
                    $("#port_of_loading").append(JSON.result);
					$("#port_of_loading").attr("disabled", false);
                }
            }
        });
    }
</script>
