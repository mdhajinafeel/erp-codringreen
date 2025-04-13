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
<?php echo form_open($farm_submit, $attributes, $hidden); ?>
<div class="modal-body">
	<?php $currency_abbreviation = $farm_details[0]->currency_abbreviation; ?>
	<?php $fmt = new NumberFormatter($locale = $currency_abbreviation, NumberFormatter::CURRENCY); ?>
	<input type="hidden" id="pagetype" name="pagetype" value="<?php echo $pagetype; ?>">
	<input type="hidden" id="hdnFarmId" name="hdnFarmId" value="<?php echo $farmid; ?>">
	<input type="hidden" id="hdnContractId" name="hdnContractId" value="<?php echo $contractid; ?>">
	<input type="hidden" id="hdnInventoryOrder" name="hdnInventoryOrder" value="<?php echo $inventoryorder; ?>">
	<input type="hidden" id="hdnOriginId" name="hdnOriginId" value="<?php echo $farm_details[0]->origin_id; ?>">
	
	<div class="row mb-3">
		<div class="col-md-6">
			<label for="inventoryorder"><?php echo $this->lang->line('inventory_order'); ?></label>
			<input class="form-control text-uppercase" placeholder="<?php echo $this->lang->line('inventory_order'); ?>" name="inventoryorder" id="inventoryorder" type="text" value="<?php echo isset($farm_details[0]->inventory_order) ? $farm_details[0]->inventory_order : ''; ?>">
			<label id="error-inventoryorder" class="error-text"><?php echo $this->lang->line('error_inventory_order'); ?></label>
		</div>
		<div class="col-md-6">
			<label class="head-label"><?php echo $this->lang->line('contract_code'); ?></label>
			<div class="input-group">
				<label class="control-label"><?php echo isset($farm_details[0]->contract_code) ? $farm_details[0]->contract_code : ''; ?></label>
			</div>
		</div>
	</div>
	<div class="row mb-3">
		<div class="col-md-6">
			<label class="head-label"><?php echo $this->lang->line('supplier_name'); ?></label>
			<div class="input-group">
				<label class="control-label"><?php echo isset($farm_details[0]->supplier_name) ? $farm_details[0]->supplier_name : ''; ?></label>
			</div>
		</div>
		<div class="col-md-6">
			<label class="head-label"><?php echo $this->lang->line('supplier_code'); ?></label>
			<div class="input-group">
				<label class="control-label"><?php echo isset($farm_details[0]->supplier_code) ? $farm_details[0]->supplier_code : ''; ?></label>
			</div>
		</div>
	</div>
	<div class="row mb-3">
		<div class="col-md-6">
			<label class="head-label"><?php echo $this->lang->line('product'); ?></label>
			<div class="input-group">
				<label class="control-label"><?php echo $farm_details[0]->product_name . ' - ' . $this->lang->line($farm_details[0]->product_type_name); ?></label>
			</div>
		</div>
		<div class="col-md-6">
			<label class="head-label"><?php echo $this->lang->line('purchase_date'); ?></label>
			<input class="form-control text-uppercase" placeholder="<?php echo $this->lang->line('purchase_date'); ?>" name="purchasedate" id="purchasedate" type="text" value="<?php echo isset($farm_details[0]->purchase_date) ? $farm_details[0]->purchase_date : ''; ?>" readonly>
		</div>
	</div>
	<div class="row mb-3">
		<div class="col-md-6">
			<label class="head-label"><?php echo $this->lang->line('truck_plate_number'); ?></label>
			<input class="form-control text-uppercase" placeholder="<?php echo $this->lang->line('truck_plate_number'); ?>" name="truckplatenumber" id="truckplatenumber" type="text" value="<?php echo isset($farm_details[0]->plate_number) ? $farm_details[0]->plate_number : ''; ?>">
			<label id="error-truckplatenumber" class="error-text"><?php echo $this->lang->line('error_truck_plate_number'); ?></label>
		</div>
		<?php if ($farm_details[0]->exchange_rate > 1) { ?>
			<div class="col-md-6">
				<label class="head-label"><?php echo $this->lang->line('conversion_rate'); ?></label>
				<div class="input-group">
					<label class="control-label"><?php echo $fmt->format(($farm_details[0]->exchange_rate + 0)); //echo isset($farm_details[0]->exchange_rate) ? $farm_details[0]->exchange_rate : ''; 
													?></label>
				</div>
			</div>
		<?php } ?>
	</div>
	<div class="row mb-3">
		<div class="col-md-6">
			<label class="head-label"><?php echo $this->lang->line('total_no_of_pieces'); ?></label>
			<div class="input-group">
				<label class="control-label"><?php echo isset($farm_details[0]->total_pieces) ? $farm_details[0]->total_pieces : ''; ?></label>
			</div>
		</div>
		<div class="col-md-6">
			<label class="head-label"><?php echo $this->lang->line('total_volume'); ?></label>
			<div class="input-group">
				<label class="control-label"><?php echo isset($farm_details[0]->total_volume) ? ($farm_details[0]->total_volume + 0) : ''; ?></label>
			</div>
		</div>
	</div>
	<?php if ($farm_details[0]->service_cost != 0) { ?>
		<div class="row mb-3">
			<div class="col-md-6">
				<label class="head-label"><?php echo $this->lang->line('service_cost'); ?></label>
				<div class="input-group">
					<label class="control-label"><?php echo $fmt->format(($farm_details[0]->service_cost + 0)); //echo isset($farm_details[0]->service_cost) ? $farm_details[0]->service_cost : ''; 
													?></label>
				</div>
			</div>
			<div class="col-md-6">
				<label class="head-label"><?php echo $this->lang->line('service_payto'); ?></label>
				<div class="input-group">
					<label class="control-label"><?php echo isset($farm_details[0]->service_pay_to) ? $farm_details[0]->service_pay_to : ''; ?></label>
				</div>
			</div>
		</div>
	<?php } ?>
	<?php if ($farm_details[0]->logistic_cost != 0) { ?>
		<div class="row mb-3">
			<div class="col-md-6">
				<label class="head-label"><?php echo $this->lang->line('logistic_cost'); ?></label>
				<div class="input-group">
					<!-- <label class="control-label"><?php echo $fmt->format(($farm_details[0]->logistic_cost + 0)); //echo isset($farm_details[0]->logistic_cost) ? $farm_details[0]->logistic_cost : ''; 
													?></label> -->
					<label class="control-label"><?php echo $fmt->format(($farm_details[0]->logistic_cost + 0));
													?></label>
				</div>
			</div>
			<div class="col-md-6">
				<label class="head-label"><?php echo $this->lang->line('logistic_payto'); ?></label>
				<div class="input-group">
					<label class="control-label"><?php echo isset($farm_details[0]->logistic_pay_to) ? $farm_details[0]->logistic_pay_to : ''; ?></label>
				</div>
			</div>
		</div>
	<?php } ?>
	<div class="row mb-3">
		<div class="col-md-6">
			<label class="head-label"><?php echo $this->lang->line('adjustment'); ?></label>
			<div class="input-group">
				<!-- <label class="control-label"><?php echo $fmt->format(($farm_details[0]->adjustment + 0)); //echo isset($farm_details[0]->adjustment) ? ($farm_details[0]->adjustment == 0 ? '---' : $farm_details[0]->adjustment) : '---'; 
												?></label> -->
				<label class="control-label"><?php echo $fmt->format(($farm_details[0]->adjustment + 0));
												?></label>
			</div>
		</div>
		<div class="col-md-6">
			<label class="head-label"><?php echo $this->lang->line('wood_value'); ?></label>
			<div class="input-group">
				<!-- <label class="control-label"><?php echo $fmt->format(($farm_details[0]->wood_value + 0)); //echo isset($farm_details[0]->wood_value) ? $farm_details[0]->wood_value : ''; 
												?></label> -->
				<label class="control-label"><?php echo $fmt->format(($farm_details[0]->wood_value + 0)); 
												?></label>
			</div>
		</div>
	</div>
	<div class="row mb-3">
		<div class="col-md-6">
			<label class="head-label"><?php echo $this->lang->line('total_taxes'); ?></label>
			<div class="input-group">
				<!-- <label class="control-label"><?php echo $fmt->format(($farm_details[0]->total_taxes + 0)); //echo isset($farm_details[0]->total_taxes) ? ($farm_details[0]->total_taxes == 0 ? '---' : $farm_details[0]->total_taxes) : '---'; 
												?></label> -->
				<label class="control-label"><?php echo $fmt->format(($farm_details[0]->total_taxes + 0)); 
												?></label>
			</div>
		</div>
		<div class="col-md-6">
			<label class="head-label"><?php echo $this->lang->line('total_payment'); ?></label>
			<div class="input-group">
				<!-- <label class="control-label"><?php echo $fmt->format(($farm_details[0]->total_payment + 0)); //echo isset($farm_details[0]->total_payment) ? ($farm_details[0]->total_payment == 0 ? '---' : $farm_details[0]->total_payment) : '---'; 
												?></label> -->
				<label class="control-label"><?php echo $fmt->format(($farm_details[0]->total_payment + 0)); 
												?></label>
			</div>
		</div>
	</div>
	<div class="row mb-3">
		<div class="col-md-6">
			<label class="head-label"><?php echo $this->lang->line('uploaded_by'); ?></label>
			<div class="input-group">
				<label class="control-label"><?php echo isset($farm_details[0]->uploaded_by) ? $farm_details[0]->uploaded_by : ''; ?></label>
			</div>
		</div>
		<div class="col-md-6">
			<label class="head-label"><?php echo $this->lang->line('origin'); ?></label>
			<div class="input-group">
				<label class="control-label"><?php echo isset($farm_details[0]->origin) ? $farm_details[0]->origin : ''; ?></label>
			</div>
		</div>
	</div>
</div>
<div class="modal-footer">
	<?php echo form_button(array('data-bs-dismiss' => 'modal', 'type' => 'button', 'class' => 'btn btn-secondary', 'content' => $this->lang->line('close'))); ?>
	<?php echo form_button(array('name' => 'cgrerp_form_origin', 'type' => 'submit', 'class' => 'btn btn-primary action_button', 'content' => $this->lang->line('update'))); ?>
</div>
<?php echo form_close(); ?>
<script src="<?php echo base_url() . 'assets/js/i18n/datepicker-' . $wz_lang . '.js'; ?>"></script>
<script type="text/javascript">
	var error_inventory_order = "<?php echo $this->lang->line('error_inventory_order'); ?>";

	$("#error-inventoryorder").hide();
	$("#error-truckplatenumber").hide();

	$(document).ready(function() {

		$("#update").submit(function(e) {

			e.preventDefault();
			var pagetype = $("#pagetype").val().trim();
			var farmid = $("#hdnFarmId").val().trim();
			var contractid = $("#hdnContractId").val().trim();
			var originid = $("#hdnOriginId").val().trim();
			var inventoryorder = $("#hdnInventoryOrder").val().trim();
			var inputinventoryorder = $("#inventoryorder").val().trim();
			var inputtruckplatenumber = $("#truckplatenumber").val().trim();
			var inputpurchasedate = $("#purchasedate").val().trim();

			var isValid1 = true,
				isValid2 = true;

			if (inputinventoryorder.length == 0) {
				$("#error-inventoryorder").show();
				isValid1 = false;
			} else {
				$("#error-inventoryorder").hide();
				isValid1 = true;
			}

			if (inputtruckplatenumber.length == 0) {
				$("#error-truckplatenumber").show();
				isValid2 = false;
			} else {
				$("#error-truckplatenumber").hide();
				isValid2 = true;
			}

			if (isValid1 && isValid2) {
				var fd = new FormData(this);
				fd.append("is_ajax", 2);
				fd.append("form", action);
				fd.append("add_type", "farm");
				fd.append("action_type", pagetype);
				fd.append("farm_id", farmid);
				fd.append("contract_id", contractid);
				fd.append("origin_id", originid);
				fd.append("inventory_order", inventoryorder);
				fd.append("input_inventory_order", inputinventoryorder);
				fd.append("input_truck_plate_number", inputtruckplatenumber);
				fd.append("input_purchase_date", inputpurchasedate);

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

							$('#xin_table_farms').DataTable().ajax.reload(null, false);
						}
					}
				});
			}
		});
	});
	$(function() {
		$("#purchasedate").datepicker({
			dateFormat: "dd/mm/yy",
			changeMonth: true,
			changeYear: true,
			minDate: '-1y',
			maxDate: '0d',
			onSelect: function(date) {
				//$("#error-purchasedate").hide();
			}
		});
		
		$('.ui-datepicker').addClass('notranslate');
	});
</script>