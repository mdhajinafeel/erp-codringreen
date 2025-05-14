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
<?php $attributes = array('name' => 'add_pod', 'id' => 'add_pod', 'autocomplete' => 'off', 'class' => '"m-b-1'); ?>
<?php $hidden = array('_method' => $pagetype); ?>
<?php echo form_open_multipart('exportpod/add', $attributes, $hidden); ?>
<div class="modal-body">
	<input type="hidden" id="pagetype" name="pagetype" value="<?php echo $pagetype; ?>">
	<input type="hidden" id="hdnexportpodid" name="hdnexportpodid" value="<?php echo $exportid;  ?>">
	<div class="row">
		<div class="col-md-6">
			<label for="pod_name"><?php echo $this->lang->line('pod_name'); ?></label>
			<input class="form-control" placeholder="<?php echo $this->lang->line('pod_name'); ?>" name="pod_name" id="pod_name" type="text" value="<?php echo $get_export_details[0]->pod_name; ?>">
			<label id="error-name" class="error-text"><?php echo $this->lang->line('error_name'); ?></label>
		</div>
		<div class="col-md-6">
			<label for="country_name"><?php echo $this->lang->line('country_name'); ?></label>
			<select class="form-control" name="country_name" id="country_name" data-plugin="select_erp">
				<option value="0"><?php echo $this->lang->line("select"); ?></option>
				<?php foreach ($countries as $country) { ?>
					<?php if ($get_export_details[0]->country_id == $country->id) { ?>
						<option value="<?php echo $country->id; ?>" selected="selected"><?php echo $country->name; ?></option>
					<?php } else { ?>
						<option value="<?php echo $country->id; ?>"><?php echo $country->name; ?></option>
					<?php } ?>
				<?php } ?>
			</select>
			<label id="error-country" class="error-text"><?php echo $this->lang->line('error_country_name'); ?></label>
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
			<label for="color_code"><?php echo $this->lang->line('color_code'); ?></label>
			<div class="input-group">
				<input class="form-control" placeholder="<?php echo $this->lang->line('color_code'); ?>" name="color_code_text" id="color_code_text" type="text" value="<?php echo $get_export_details[0]->color_code; ?>";>
				<input class="form-control form-control-color" name="color_code" id="color_code" type="color" value="<?php echo $get_export_details[0]->color_code; ?>";>
				</div>
			<label id="error-color_code" class="error-text"><?php echo $this->lang->line('error_color_code'); ?></label>
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
	<?php echo form_button(array('name' => 'cgrerp_form_origin', 'type' => 'submit', 'class' => 'btn btn-success addexportpod', 'content' => $pagetype == 'edit' ? $this->lang->line('update') : $this->lang->line('add'))); ?>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">

	$(document).ready(function() {
		
		$("#error-name").hide();
		$("#error-country").hide();
		$("#error-latitude").hide();
		$("#error-longitude").hide();
		$("#error-color_code").hide();

		$("#add_pod").submit(function(e) {
			e.preventDefault();
			var pagetype = $("#pagetype").val().trim();
			var exportpodid = $("#hdnexportpodid").val().trim();
			var name = $("#pod_name").val().trim();
			var country = $("#country_name").val();
			var latitude = $("#latitude").val();
			var longitude = $("#longitude").val();
			var color_code_text = $("#color_code_text").val();
			var status = $("#status").val();
			
			var isValid1 = true, isValid2 = true, isValid3 = true, isValid4 = true, isValid5 = true;

			if (name.length == 0) {
				$("#error-name").show();
				isValid1 = false;
			} else {
				$("#error-name").hide();
				isValid1 = true;
			}

			if (country == 0) {
				$("#error-country").show();
				isValid2 = false;
			} else {
				$("#error-country").hide();
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

			if (color_code_text.length == 0) {
				$("#error-color_code").show();
				isValid5 = false;
			} else {
				$("#error-color_code").hide();
				isValid5 = true;
			}

			if (isValid1 && isValid2 && isValid3 && isValid4 && isValid5) {

				var fd = new FormData(this);
				fd.append("is_ajax", 2);
				fd.append("add_type", "exportpod");
				fd.append("action_type", pagetype);
				fd.append("exportpodid", exportpodid);
				fd.append("name", name);
				fd.append("country", country);
				fd.append("latitude", latitude);
				fd.append("longitude", longitude);
				fd.append("colorcode", color_code_text);

				$(".addexportpod").prop('disabled', true);
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
							$('.addexportpod').prop('disabled', false);
							$('input[name="csrf_cgrerp"]').val(JSON.csrf_hash);
						} else {
							toastr.clear();
							toastr.success(JSON.result);
							$('.addexportpod').prop('disabled', false);
							$('input[name="csrf_cgrerp"]').val(JSON.csrf_hash);
							$("#add-modal-data-lg-bd").modal('hide');

							$('#xin_table_pod').DataTable().ajax.reload(null, false);
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
