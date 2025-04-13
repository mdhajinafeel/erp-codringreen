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
<?php $attributes = array('name' => 'add_supplier', 'id' => 'add_supplier', 'autocomplete' => 'off', 'class' => '"m-b-1'); ?>
<?php $hidden = array('_method' => $pagetype); ?>
<?php echo form_open_multipart('exportsuppliers/add', $attributes, $hidden); ?>
<div class="modal-body supplier-modal">
	<input type="hidden" id="pagetype" name="pagetype" value="<?php echo $pagetype; ?>">
	<input type="hidden" id="hdnsupplierid" name="hdnsupplierid" value="<?php echo $supplierid;  ?>">
	<input type="hidden" id="hdnoriginid" name="hdnoriginid" value="<?php echo isset($get_supplier_details[0]->origin_id) ? $get_supplier_details[0]->origin_id : ''; ?>">
	<input type="hidden" id="hdnCsrf" name="hdnCsrf" value="<?php echo $csrfhash; ?>">

	<div class="row mb-3">
		<div class="col-md-6">
			<label for="name"><?php echo $this->lang->line('supplier_name'); ?></label>
			<input class="form-control" placeholder="<?php echo $this->lang->line('supplier_name'); ?>" name="name" id="name" type="text" value="<?php echo isset($get_supplier_details[0]->supplier_name) ? $get_supplier_details[0]->supplier_name : ''; ?>">
			<label id="error-name" class="error-text"><?php echo $this->lang->line('error_name'); ?></label>
		</div>
		<div class="col-md-6">
			<label for="supplierid"><?php echo $this->lang->line('supplier_id'); ?></label>
			<input class="form-control" placeholder="<?php echo $this->lang->line('supplier_id'); ?>" name="supplierid" id="supplierid" type="text" value="<?php echo isset($get_supplier_details[0]->supplier_id) ? $get_supplier_details[0]->supplier_id : ''; ?>">
			<label id="error-supplierid" class="error-text"><?php echo $this->lang->line('error_id'); ?></label>
		</div>
	</div>

	<div class="row mb-3">

		<div class="col-md-6">
			<label for="origin"><?php echo $this->lang->line('export_type'); ?></label>
			<select class="form-control" name="export_type_supplier[]" id="export_type_supplier" data-plugin="select_erp" multiple>
				<?php foreach ($exporttypes as $exporttype) { ?>
					<?php if (isset($get_supplier_details[0]->export_type)) { ?>
						<option value="<?php echo $exporttype->id; ?>" <?php if (in_array($exporttype->id, $get_supplier_details[0]->export_type)) : ?> selected="selected" <?php endif; ?>> <?php echo $exporttype->export_type; ?></option>
					<?php } else { ?>
						<option value="<?php echo $exporttype->id; ?>"> <?php echo $exporttype->export_type; ?></option>
					<?php } ?>
				<?php } ?>
			</select>
			<label id="error-exporttype" class="error-text"><?php echo $this->lang->line('error_reception_type'); ?></label>
		</div>

		<div class="col-md-6">
			<label for="origin"><?php echo $this->lang->line('origin'); ?></label>

			<?php if ($pagetype == "edit") { ?>
				<div class="input-group">
					<label class="control-label"><?php echo $get_supplier_details[0]->origin; ?></label>
				</div>
			<?php } else { ?>
				<select class="form-control" name="origin" id="origin" data-plugin="select_erp">
					<option value="0"><?php echo $this->lang->line("select"); ?></option>
					<?php foreach ($applicable_origins as $origin) { ?>
						<?php if ($get_supplier_details[0]->origin_id == $origin->id) { ?>
							<option value="<?php echo $origin->id; ?>" selected="selected"><?php echo $origin->origin_name; ?></option>
						<?php } else { ?>
							<option value="<?php echo $origin->id; ?>"><?php echo $origin->origin_name; ?></option>
						<?php } ?>
					<?php } ?>
				</select>
				<label id="error-origin" class="error-text"><?php echo $this->lang->line('error_origin_screen'); ?></label>
			<?php } ?>
		</div>
	</div>

	<div class="row mb-3">
		<div class="col-md-6">
			<label for="status"><?php echo $this->lang->line('status'); ?></label>
			<select class="form-control" name="status" id="status" data-plugin="select_erp">
				<?php if ($pagetype == 'add') { ?>
					<option value="1"><?php echo $this->lang->line('active'); ?></option>
					<option value="0"><?php echo $this->lang->line('inactive'); ?></option>
				<?php } else { ?>
					<option value="1" <?php if ($get_supplier_details[0]->is_active == 1) : ?> selected="selected" <?php endif; ?>><?php echo $this->lang->line('active'); ?></option>
					<option value="0" <?php if ($get_supplier_details[0]->is_active == 0) : ?> selected="selected" <?php endif; ?>><?php echo $this->lang->line('inactive'); ?></option>
				<?php } ?>
			</select>
		</div>
	</div>
</div>
<div class="modal-footer">
	<?php echo form_button(array('data-bs-dismiss' => 'modal', 'type' => 'button', 'class' => 'btn btn-secondary', 'content' => $this->lang->line('close'))); ?>
	<?php echo form_button(array('name' => 'cgrerp_form_origin', 'type' => 'submit', 'class' => 'btn btn-success addsupplier', 'content' => $pagetype == 'edit' ? $this->lang->line('update') : $this->lang->line('add'))); ?>
</div>
<?php echo form_close(); ?>
<link rel="stylesheet" href="<?php echo base_url() . 'assets/css/bootstrap-multiselect.css'; ?>">
<script src="<?php echo base_url() . 'assets/js/bootstrap-multiselect.js'; ?>"></script>

<script type="text/javascript">
	var selecttext = "<?php echo $this->lang->line("select"); ?>";

	$(document).ready(function() {

		$("#error-name").hide();
		$("#error-supplierid").hide();
		$("#error-exporttype").hide();
		$("#error-origin").hide();

		$('select[multiple]').multiselect({
			placeholder: selecttext,
			search: false,
			selectAll: false,
		});

		var editPageType = "<?php echo $pagetype; ?>";

		$("#add_supplier").submit(function(e) {

			e.preventDefault();

			var fd = new FormData();
			var pagetype = $("#pagetype").val().trim();
			var supplierid_db = $("#hdnsupplierid").val().trim();
			var editoriginid = $("#hdnoriginid").val();
			var name = $("#name").val().trim();
			var supplierid = $("#supplierid").val().trim();
			var supplier_origin = $("#origin").val();
			var export_type_supplier = $("#export_type_supplier").val();

			var isValid1 = true,
				isValid2 = true,
				isValid3 = true,
				isValid4 = true;

			if (name.length == 0) {
				$("#error-name").show();
				isValid1 = false;
			} else {
				$("#error-name").hide();
				isValid1 = true;
			}

			if (supplierid.length == 0) {
				$("#error-supplierid").show();
				isValid2 = false;
			} else {
				$("#error-supplierid").hide();
				isValid2 = true;
			}

			if (export_type_supplier == 0) {
				$("#error-exporttype").show();
				isValid4 = false;
			} else {
				$("#error-exporttype").hide();
				isValid4 = true;
			}

			if (supplier_origin == 0) {
				$("#error-origin").show();
				isValid3 = false;
			} else {
				$("#error-origin").hide();
				isValid3 = true;
			}

			if (isValid1 && isValid2 && isValid3) {

				fd.append("name", name);
				fd.append("supplierid", supplierid);
				fd.append("is_ajax", 2);
				fd.append("add_type", "suppliers");
				fd.append("csrf_cgrerp", $("#hdnCsrf").val());

				if (pagetype == "edit") {
					fd.append('supplier_origin', editoriginid);
				} else {
					fd.append('supplier_origin', supplier_origin);
				}

				fd.append("action_type", pagetype);
				fd.append("supplier_id", supplierid_db);
				fd.append("export_type", export_type_supplier);
				4
				fd.append("status", $("#status").val());

				toastr.info(processing_request);

				$('.addsupplier').prop('disabled', true);
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
							$('.addsupplier').prop('disabled', false);
							$('input[name="csrf_cgrerp"]').val(JSON.csrf_hash);
						} else {
							toastr.clear();
							toastr.success(JSON.result);
							$('.addsupplier').prop('disabled', false);
							$('input[name="csrf_cgrerp"]').val(JSON.csrf_hash);
							$("#add-modal-data-bd").modal('hide');

							$('#xin_table_suppliers').DataTable().ajax.reload(null, false);
						}
					}
				});
			}
		});
	});
</script>