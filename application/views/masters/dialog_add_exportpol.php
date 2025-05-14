<?php
$session = $this->session->userdata('fullname');
$applicable_origins = $session["applicable_origins"];
?>
<?php $site_lang = $this->load->helper('language'); ?>
<?php $wz_lang = $site_lang->session->userdata('site_lang'); ?>
<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<div class="modal-header">
	<h4 class="modal-title" id="add-modal-data"><?php echo $pageheading; ?></h4>
	<?php echo form_button(array('aria-label' => 'Close', 'data-bs-dismiss' => 'modal', 'type' => 'button', 'class' => 'close', 'content' => '<span aria-hidden="true">×</span>')); ?>

</div>
<?php $attributes = array('name' => 'add_pol', 'id' => 'add_pol', 'autocomplete' => 'off', 'class' => '"m-b-1'); ?>
<?php $hidden = array('_method' => $pagetype); ?>
<?php echo form_open_multipart('exportpol/add', $attributes, $hidden); ?>
<div class="modal-body">
	<input type="hidden" id="pagetype" name="pagetype" value="<?php echo $pagetype; ?>">
	<input type="hidden" id="hdnexportpolid" name="hdnexportpolid" value="<?php echo $exportid;  ?>">
	<div class="row">
		<div class="col-md-6">
			<label for="pol_name"><?php echo $this->lang->line('pol_name'); ?></label>
			<input class="form-control" placeholder="<?php echo $this->lang->line('pol_name'); ?>" name="pol_name" id="pol_name" type="text" value="<?php echo $get_export_details[0]->pol_name; ?>">
			<label id="error-name" class="error-text"><?php echo $this->lang->line('error_name'); ?></label>
		</div>
		<div class="col-md-6">
			<label for="pol_short_name"><?php echo $this->lang->line('pol_short_name'); ?></label>
			<input class="form-control" placeholder="<?php echo $this->lang->line('pol_short_name'); ?>" name="pol_short_name" id="pol_short_name" type="text" value="<?php echo $get_export_details[0]->pol_short_name; ?>">
			<label id="error-shortname" class="error-text"><?php echo $this->lang->line('error_pol_short_name'); ?></label>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6">
			<label for="latitude"><?php echo $this->lang->line('latitude'); ?></label>
			<input class="form-control" placeholder="<?php echo $this->lang->line('latitude'); ?>" name="latitude" id="latitude" type="number" step="any" value="<?php echo $get_export_details[0]->latitude + 0; ?>">
			<label id="error-latitude" class="error-text"><?php echo $this->lang->line('error_latitude'); ?></label>
		</div>
		<div class="col-md-6">
			<label for="longitude"><?php echo $this->lang->line('longitude'); ?></label>
			<input class="form-control" placeholder="<?php echo $this->lang->line('longitude'); ?>" name="longitude" id="longitude" type="number" step="any" value="<?php echo $get_export_details[0]->longitude + 0; ?>">
			<label id="error-longitude" class="error-text"><?php echo $this->lang->line('error_latitude'); ?></label>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6">
			<label for="origin_pol"><?php echo $this->lang->line('origin'); ?></label>
			<select class="form-control" name="origin_pol" id="origin_pol" data-plugin="select_erp">
				<option value="0"><?php echo $this->lang->line("select"); ?></option>
				<?php foreach ($applicable_origins as $origin) { ?>
					<?php if ($get_export_details[0]->origin_id == $origin->id) { ?>
						<option value="<?php echo $origin->id; ?>" selected="selected"><?php echo $origin->origin_name; ?></option>
					<?php } else { ?>
						<option value="<?php echo $origin->id; ?>"><?php echo $origin->origin_name; ?></option>
					<?php } ?>
				<?php } ?>
			</select>
			<label id="error-origin" class="error-text"><?php echo $this->lang->line('error_origin_screen'); ?></label>
		</div>
		<div class="col-md-6">
			<label for="status"><?php echo $this->lang->line('status'); ?></label>
			<select class="form-control" name="status" id="status" data-plugin="select_erp">
				<?php if ($pagetype == 'add') { ?>
					<option value="1"><?php echo $this->lang->line('active'); ?></option>
					<option value="0"><?php echo $this->lang->line('inactive'); ?></option>
				<?php } else { ?>
					<option value="1" <?php if ($get_export_details[0]->is_active == 1) : ?> selected="selected" <?php endif; ?>><?php echo $this->lang->line('active'); ?></option>
					<option value="0" <?php if ($get_export_details[0]->is_active == 0) : ?> selected="selected" <?php endif; ?>><?php echo $this->lang->line('inactive'); ?></option>
				<?php } ?>
			</select>
		</div>
	</div>
</div>
<div class="modal-footer">
	<?php echo form_button(array('data-bs-dismiss' => 'modal', 'type' => 'button', 'class' => 'btn btn-secondary', 'content' => $this->lang->line('close'))); ?>
	<?php echo form_button(array('name' => 'cgrerp_form_origin', 'type' => 'submit', 'class' => 'btn btn-success addexportpol', 'content' => $pagetype == 'edit' ? $this->lang->line('update') : $this->lang->line('add'))); ?>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">

	$(document).ready(function() {
		
		$("#error-name").hide();
		$("#error-shortname").hide();
		$("#error-origin").hide();
		$("#error-latitude").hide();
		$("#error-longitude").hide();

		$("#add_pol").submit(function(e) {
			e.preventDefault();
			var pagetype = $("#pagetype").val().trim();
			var exportpolid = $("#hdnexportpolid").val().trim();
			var name = $("#pol_name").val().trim().trim();
			var polshortname = $("#pol_short_name").val().trim();
			var latitude = $("#latitude").val();
			var longitude = $("#longitude").val();
			var origin = $("#origin_pol").val();
			var status = $("#status").val();
			
			var isValid1 = true, isValid2 = true, isValid3 = true, isValid4 = true, isValid5 = true;

			if (name.length == 0) {
				$("#error-name").show();
				isValid1 = false;
			} else {
				$("#error-name").hide();
				isValid1 = true;
			}

			if (origin == 0) {
				$("#error-origin").show();
				isValid2 = false;
			} else {
				$("#error-origin").hide();
				isValid2 = true;
			}

			if (latitude.length == 0) {
				$("#error-latitude").show();
				isValid3 = false;
			} else {
				$("#error-latitude").hide();
				isValid3 = true;
			}

			if (longitude.length == 0) {
				$("#error-longitude").show();
				isValid4 = false;
			} else {
				$("#error-longitude").hide();
				isValid4 = true;
			}

			if (polshortname.length == 0) {
				$("#error-shortname").show();
				isValid5 = false;
			} else {
				$("#error-shortname").hide();
				isValid5 = true;
			}

			if (isValid1 && isValid2 && isValid3 && isValid4 && isValid5) {

				var fd = new FormData(this);
				fd.append("is_ajax", 2);
				fd.append("add_type", "exportpol");
				fd.append("action_type", pagetype);
				fd.append("exportpolid", exportpolid);
				fd.append("name", name);
				fd.append("shortname", polshortname);
				fd.append("latitude", latitude);
				fd.append("longitude", longitude);
				fd.append("origin", origin);

				$(".addexportpol").prop('disabled', true);
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
						if (JSON.redirect == true) {
							window.location.replace(login_url);
						} else if (JSON.error != '') {
							toastr.clear();
							toastr.error(JSON.error);
							$('.addexportpol').prop('disabled', false);
							$('input[name="csrf_cgrerp"]').val(JSON.csrf_hash);
						} else {
							toastr.clear();
							toastr.success(JSON.result);
							$('.addexportpol').prop('disabled', false);
							$('input[name="csrf_cgrerp"]').val(JSON.csrf_hash);
							$("#add-modal-data-lg-bd").modal('hide');

							$('#xin_table_pol').DataTable().ajax.reload(null, false);
						}
					}
				});
			}
		});

		const colorInput = document.getElementById('color_code');
		const colorText = document.getElementById('color_code_text');

		// Sync color picker to text input
		colorInput.addEventListener('input', function () {
			colorText.value = colorInput.value;
		});

		// Sync text input to color picker
		colorText.addEventListener('input', function () {
			if(/^#([0-9A-F]{3}){1,2}$/i.test(colorText.value)) {
			colorInput.value = colorText.value;
			}
		});

		// Initialize value on load
		window.addEventListener('DOMContentLoaded', function () {
			colorText.value = colorInput.value;
		});
	});

	
</script>
