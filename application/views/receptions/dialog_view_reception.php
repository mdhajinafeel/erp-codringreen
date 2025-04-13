<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<?php $site_lang = $this->load->helper('language'); ?>
<?php $wz_lang = $site_lang->session->userdata('site_lang'); ?>
<div class="modal-header">
	<h4 class="modal-title" id="add-modal-data"><?php echo $pageheading; ?></h4>
	<?php echo form_button(array('aria-label' => 'Close', 'data-bs-dismiss' => 'modal', 'type' => 'button', 'class' => 'close', 'content' => '<span aria-hidden="true">×</span>')); ?>

</div>
<?php $attributes = array('name' => 'update', 'id' => 'update', 'autocomplete' => 'off', 'class' => '"m-b-1'); ?>
<?php $hidden = array('_method' => $pagetype); ?>
<?php echo form_open($reception_submit, $attributes, $hidden); ?>
<div class="modal-body">
	<input type="hidden" id="pagetype" name="pagetype" value="<?php echo $pagetype; ?>">
	<input type="hidden" id="hdnReceptionId" name="hdnReceptionId" value="<?php echo $receptionid; ?>">
	<input type="hidden" id="hdnInventoryOrder" name="hdnInventoryOrder" value="<?php echo $inventoryorder; ?>">
	<input type="hidden" id="hdnOriginId" name="hdnOriginId" value="<?php echo $reception_details[0]->origin_id; ?>">

	<div class="row">
		<div class="col-md-6 mb-3">
			<label for="inventoryorder"><?php echo $this->lang->line('inventory_order'); ?></label>

			<?php if ($reception_details[0]->isclosed == 0) { ?>
				<input class="form-control text-uppercase" placeholder="<?php echo $this->lang->line('inventory_order'); ?>" name="inventoryorder" id="inventoryorder" type="text" value="<?php echo isset($reception_details[0]->salvoconducto) ? $reception_details[0]->salvoconducto : ''; ?>">
				<label id="error-viewinventoryorder" class="error-text"><?php echo $this->lang->line('error_inventory_order'); ?></label>
			<?php } else { ?>
				<div class="input-group">
					<label class="control-label"><?php echo isset($reception_details[0]->salvoconducto) ? $reception_details[0]->salvoconducto : ''; ?></label>
				</div>
			<?php } ?>
		</div>
		<div class="col-md-6">
			<label class="head-label"><?php echo $this->lang->line('warehouse'); ?></label>
			<?php if ($reception_details[0]->isclosed == 0) { ?>
				<select class="form-control" name="wh_name" id="wh_name" data-plugin="select_erp">
					<option value="0"><?php echo $this->lang->line("select"); ?></option>

					<?php foreach ($warehouses as $warehouse) { ?>
						<option value="<?php echo $warehouse->whid; ?>" <?php if ($reception_details[0]->warehouse_id == $warehouse->whid) : ?> selected="selected" <?php endif; ?>><?php echo $warehouse->warehouse_name; ?></option>
					<?php } ?>
				</select>
				<label id="error-warehouse" class="error-text"><?php echo $this->lang->line('error_warehouse_farm'); ?></label>
			<?php } else { ?>
				<div class="input-group">
					<label class="control-label"><?php echo isset($reception_details[0]->warehouse_name) ? $reception_details[0]->warehouse_name : ''; ?></label>
				</div>
			<?php } ?>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6 mb-3">
			<label class="head-label"><?php echo $this->lang->line('supplier_name'); ?></label>
			<?php if ($reception_details[0]->isclosed == 0) { ?>
				<select class="form-control" name="supplier_name" id="supplier_name" data-plugin="select_erp" disabled>
					<option value="0"><?php echo $this->lang->line("select"); ?></option>

					<?php foreach ($suppliers as $supplier) { ?>
						<option value="<?php echo $supplier->id; ?>" <?php if ($reception_details[0]->supplier_id == $supplier->id) : ?> selected="selected" <?php endif; ?>><?php echo $supplier->supplier_name; ?></option>
					<?php } ?>
				</select>
				<label id="error-suppliername" class="error-text"><?php echo $this->lang->line('error_select_name'); ?></label>
			<?php } else { ?>
				<div class="input-group">
					<label class="control-label"><?php echo isset($reception_details[0]->supplier_name) ? ($reception_details[0]->supplier_name) : ''; ?></label>
				</div>
			<?php } ?>
		</div>
		<div class="col-md-6">
			<label class="head-label"><?php echo $this->lang->line('product_title'); ?></label>
			<div class="input-group">
				<label class="control-label"><?php echo $reception_details[0]->product_name . ' - ' . $this->lang->line($reception_details[0]->product_type_name); ?></label>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6 mb-3">
			<label class="head-label"><?php echo $this->lang->line('measuremet_system'); ?></label>
			<div class="input-group">
				<label class="control-label"><?php echo $this->lang->line($reception_details[0]->measurement_name); ?></label>
			</div>
		</div>
		<div class="col-md-6">
			<label class="head-label"><?php echo $this->lang->line('received_date'); ?></label>
			<?php if ($reception_details[0]->isclosed == 0) { ?>
			<input class="form-control text-uppercase" placeholder="<?php echo $this->lang->line('received_date'); ?>" name="receiveddate" id="receiveddate" type="text" value="<?php echo isset($reception_details[0]->received_date) ? $reception_details[0]->received_date : ''; ?>" readonly>
			<?php } else { ?>
				<div class="input-group">
					<label class="control-label"><?php echo isset($reception_details[0]->received_date) ? $reception_details[0]->received_date : ''; ?></label>
				</div>
			<?php } ?>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6 mb-3">
			<label class="head-label"><?php echo $this->lang->line('total_no_of_pieces'); ?></label>
			<div class="input-group">
				<label class="control-label"><?php echo isset($reception_details[0]->total_pieces) ? $reception_details[0]->total_pieces : ''; ?></label>
			</div>
		</div>
		<div class="col-md-6">
			<label class="head-label"><?php echo $this->lang->line('total_volume'); ?></label>
			<div class="input-group">
				<label class="control-label"><?php echo isset($reception_details[0]->total_volume) ? ($reception_details[0]->total_volume + 0) : ''; ?></label>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6 mb-3">
			<label class="head-label"><?php echo $this->lang->line('remaining_pieces'); ?></label>
			<div class="input-group">
				<label class="control-label"><?php echo isset($reception_details[0]->remaining_pieces) ? $reception_details[0]->remaining_pieces : ''; ?></label>
			</div>
		</div>
		<div class="col-md-6">
			<label class="head-label"><?php echo $this->lang->line('remaining_volume'); ?></label>
			<div class="input-group">
				<label class="control-label"><?php echo isset($reception_details[0]->remaining_volume) ? ($reception_details[0]->remaining_volume + 0) : ''; ?></label>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6 mb-3">
			<label class="head-label"><?php echo $this->lang->line('uploaded_by'); ?></label>
			<div class="input-group">
				<label class="control-label"><?php echo isset($reception_details[0]->uploaded_by) ? $reception_details[0]->uploaded_by : ''; ?></label>
			</div>
		</div>
		<div class="col-md-6">
			<label class="head-label"><?php echo $this->lang->line('origin'); ?></label>
			<div class="input-group">
				<label class="control-label"><?php echo isset($reception_details[0]->origin) ? $reception_details[0]->origin : ''; ?></label>
			</div>
		</div>
	</div>
</div>
<div class="modal-footer">
	<?php echo form_button(array('data-bs-dismiss' => 'modal', 'type' => 'button', 'class' => 'btn btn-secondary', 'content' => $this->lang->line('close'))); ?>

	<?php if ($reception_details[0]->isclosed == 0) { ?>
		<?php echo form_button(array('name' => 'cgrerp_form_origin', 'type' => 'submit', 'class' => 'btn btn-primary action_button', 'content' => $this->lang->line('update'))); ?>
	<?php } ?>
</div>
<?php echo form_close(); ?>
<script src="<?php echo base_url() . 'assets/js/i18n/datepicker-' . $wz_lang . '.js'; ?>"></script>
<script type="text/javascript">
	var error_inventory_order = "<?php echo $this->lang->line('error_inventory_order'); ?>";

	$("#error-viewinventoryorder").hide();
	$("#error-suppliername").hide();
	$("#error-warehouse").hide();

	$(document).ready(function() {

		$("#update").submit(function(e) {

			e.preventDefault();
			var pagetype = $("#pagetype").val().trim();
			var receptionid = $("#hdnReceptionId").val();
			var originid = $("#hdnOriginId").val().trim();
			var inventoryorder = $("#hdnInventoryOrder").val().trim();
			var inputinventoryorder = $("#inventoryorder").val().trim();
			var inputwarehouse = $("#wh_name").val();
			var inputreceiveddate = $("#receiveddate").val().trim();

			var isValid1 = true,
				isValid2 = true;

			if (inputinventoryorder.length == 0) {
				$("#error-viewinventoryorder").show();
				isValid1 = false;
			} else {
				$("#error-viewinventoryorder").hide();
				isValid1 = true;
			}

			if (inputwarehouse == 0) {
				$("#error-warehouse").show();
				isValid2 = false;
			} else {
				$("#error-warehouse").hide();
				isValid2 = true;
			}

			if (isValid1 && isValid2) {
				var fd = new FormData(this);
				fd.append("is_ajax", 2);
				fd.append("form", action);
				fd.append("add_type", "reception");
				fd.append("action_type", pagetype);
				fd.append("reception_id", receptionid);
				fd.append("origin_id", originid);
				fd.append("inventory_order", inventoryorder);
				fd.append("input_inventory_order", inputinventoryorder);
				fd.append("warehouse_id", inputwarehouse);
				fd.append("received_date", inputreceiveddate);

				$(".action_button").prop('disabled', true);
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
							$('.action_button').prop('disabled', false);
							$('input[name="csrf_cgrerp"]').val(JSON.csrf_hash);
						} else {
							toastr.clear();
							toastr.success(JSON.result);
							$('.action_button').prop('disabled', false);
							$('input[name="csrf_cgrerp"]').val(JSON.csrf_hash);
							$("#add-modal-data").modal('hide');

							$('#xin_table_receptions').DataTable().ajax.reload(null, false);
						}
					}
				});
			}
		});
	});
	$(function() {
		$("#receiveddate").datepicker({
			dateFormat: "dd/mm/yy",
			changeMonth: true,
			changeYear: true,
			minDate: '-1y',
			maxDate: '0d',
			onSelect: function(date) {
				//$("#error-purchasedate").hide();
			}
		});
	});
</script>