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
	<?php /*$fmt = new NumberFormatter($locale = $currency_abbreviation, NumberFormatter::CURRENCY);*/ ?>
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
		<?php if ($farm_details[0]->currency == 1) { ?>
			<div class="col-md-6">
				<label class="head-label"><?php echo $this->lang->line('conversion_rate'); ?></label>
				<input class="form-control text-uppercase" placeholder="<?php echo $this->lang->line('conversion_rate'); ?>" name="conversion_rate" id="conversion_rate" type="text" value="<?php echo isset($farm_details[0]->exchange_rate) ? ($farm_details[0]->exchange_rate + 0) : ''; ?>">
				<label id="error-conversionrate" class="error-text"><?php echo $this->lang->line('error_value'); ?></label>
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
	<div class="row mb-3">
		<div class="col-md-6 mb-2">
			<label for="service_cost"><?php echo $this->lang->line('service_cost'); ?></label>
			<input type="number" id="service_cost" step="any" maxlength="10" name="service_cost" class="form-control" value="<?php echo ($farm_details[0]->service_cost + 0); ?>" placeholder="<?php echo $this->lang->line('service_cost'); ?>">
			<label id="error-servicecost" class="error-text"><?php echo $this->lang->line('error_zero_value'); ?></label>
		</div>
		<div class="col-md-6">
			<label for="service_payto"><?php echo $this->lang->line('service_payto'); ?></label>
			<select class="form-control" name="service_payto" id="service_payto" data-plugin="select_erp">
				<option value="0"><?php echo $this->lang->line("select"); ?></option>
				<?php foreach ($farm_providers as $provider) { ?>
					<option value="<?php echo $provider->supplier_id; ?>" <?php if ($farm_details[0]->pay_service_to == $provider->supplier_id) {
																				echo "selected";
																			} ?>><?php echo $provider->supplier_name; ?></option>
				<?php } ?>
			</select>
			<label id="error-servicepayto" class="error-text"><?php echo $this->lang->line('error_select_name'); ?></label>
		</div>
	</div>

	<div class="row mb-3">
		<div class="col-md-6 mb-2">
			<label for="logistic_cost"><?php echo $this->lang->line('logistic_cost'); ?></label>
			<input type="number" id="logistic_cost" step="any" maxlength="10" name="logistic_cost" class="form-control" value="<?php echo ($farm_details[0]->logistic_cost + 0); ?>" placeholder="<?php echo $this->lang->line('logistic_cost'); ?>">
			<label id="error-logisticcost" class="error-text"><?php echo $this->lang->line('error_zero_value'); ?></label>
		</div>
		<div class="col-md-6">
			<label for="logistic_payto"><?php echo $this->lang->line('logistic_payto'); ?></label>
			<select class="form-control" name="logistic_payto" id="logistic_payto" data-plugin="select_erp">
				<option value="0"><?php echo $this->lang->line("select"); ?></option>
				<?php foreach ($farm_providers as $provider) { ?>
					<option value="<?php echo $provider->supplier_id; ?>" <?php if ($farm_details[0]->pay_logistics_to == $provider->supplier_id) {
																				echo "selected";
																			} ?>><?php echo $provider->supplier_name; ?></option>
				<?php } ?>
			</select>
			<label id="error-logisticpayto" class="error-text"><?php echo $this->lang->line('error_select_name'); ?></label>
		</div>
	</div>

	<div class="row mb-3">
		<div class="col-md-6 mb-2">
			<label for="adjustment"><?php echo $this->lang->line('adjustment'); ?></label>
			<input type="number" id="adjustment" step="any" maxlength="10" name="adjustment" class="form-control" value="<?php echo ($farm_details[0]->adjustment + 0); ?>" placeholder="<?php echo $this->lang->line('adjustment'); ?>">
		</div>

		<div class="col-md-6">
			<label for="adjustment_tax"><?php echo $this->lang->line('adjustment_taxes'); ?></label>
			<select class="form-control" name="adjustment_tax[]" id="adjustment_tax" data-plugin="select_erp" multiple>
				<?php foreach ($supplier_taxes as $tax) { ?>
					<option value="<?php echo $tax->id; ?>" <?php if (in_array($tax->id, $adjustTax)) : ?> selected="selected" <?php endif; ?>><?php echo $tax->tax_name; ?></option>
				<?php } ?>
			</select>
		</div>

	</div>
	<div class="row mb-3">
		<div class="col-md-6 mb-2">
			<label class="head-label"><?php echo $this->lang->line('wood_value'); ?></label>
			<div class="input-group">
				<!-- <label class="control-label"><?php //echo $fmt->format(($farm_details[0]->wood_value + 0)); //echo isset($farm_details[0]->wood_value) ? $farm_details[0]->wood_value : ''; 
													?></label> -->
				<label class="control-label"><?php echo isset($farm_details[0]->wood_value) ? $farm_details[0]->wood_value : '';
												?></label>
			</div>
		</div>
		<div class="col-md-6">
			<label class="head-label"><?php echo $this->lang->line('total_taxes'); ?></label>
			<div class="input-group">
				<!-- <label class="control-label"><?php //echo $fmt->format(($farm_details[0]->total_taxes + 0)); //echo isset($farm_details[0]->total_taxes) ? ($farm_details[0]->total_taxes == 0 ? '---' : $farm_details[0]->total_taxes) : '---'; 
													?></label> -->
				<label class="control-label"><?php echo isset($farm_details[0]->total_taxes) ? ($farm_details[0]->total_taxes == 0 ? '---' : $farm_details[0]->total_taxes) : '---';
												?></label>
			</div>
		</div>
	</div>
	<div class="row mb-3">
		<div class="col-md-6 mb-2">
			<label class="head-label"><?php echo $this->lang->line('total_payment'); ?></label>
			<div class="input-group">
				<!-- <label class="control-label"><?php //echo $fmt->format(($farm_details[0]->total_payment + 0)); //echo isset($farm_details[0]->total_payment) ? ($farm_details[0]->total_payment == 0 ? '---' : $farm_details[0]->total_payment) : '---'; 
													?></label> -->
				<label class="control-label"><?php echo isset($farm_details[0]->total_payment) ? ($farm_details[0]->total_payment == 0 ? '---' : $farm_details[0]->total_payment) : '---';
												?></label>
			</div>
		</div>
		<div class="col-md-6">
			<label class="head-label"><?php echo $this->lang->line('uploaded_by'); ?></label>
			<div class="input-group">
				<label class="control-label"><?php echo isset($farm_details[0]->uploaded_by) ? $farm_details[0]->uploaded_by : ''; ?></label>
			</div>
		</div>
	</div>
	<div class="row mb-3">
		<div class="col-md-6">
			<label class="head-label"><?php echo $this->lang->line('origin'); ?></label>
			<div class="input-group">
				<label class="control-label"><?php echo isset($farm_details[0]->origin) ? $farm_details[0]->origin : ''; ?></label>
			</div>
		</div>

		<?php if($farm_details[0]->origin_id == 1) { ?>

			<div class="col-md-6">
				<label for="process"><?php echo $this->lang->line('process'); ?></label>
				<select class="form-control" name="process" id="process" data-plugin="select_erp">
					<option value="0"><?php echo $this->lang->line("select"); ?></option>
					<option value="1" <?php if ($farm_details[0]->process_type == 1) {
																				echo "selected";
																			} ?>><?php echo $this->lang->line("sawmill"); ?></option>
					<option value="2" <?php if ($farm_details[0]->process_type == 2) {
																				echo "selected";
																			} ?>><?php echo $this->lang->line("local_sales"); ?></option>
				</select>
			</div>

		<?php } ?>
	</div>
</div>
<div class="modal-footer">
	<?php echo form_button(array('data-bs-dismiss' => 'modal', 'type' => 'button', 'class' => 'btn btn-secondary', 'content' => $this->lang->line('close'))); ?>
	<?php echo form_button(array('name' => 'cgrerp_form_origin', 'type' => 'submit', 'class' => 'btn btn-primary action_button', 'content' => $this->lang->line('update'))); ?>
</div>
<?php echo form_close(); ?>
<script src="<?php echo base_url() . 'assets/js/i18n/datepicker-' . $wz_lang . '.js'; ?>"></script>
<link rel="stylesheet" href="<?php echo base_url() . 'assets/css/bootstrap-multiselect.css'; ?>">
<script src="<?php echo base_url() . 'assets/js/bootstrap-multiselect.js'; ?>"></script>
<script type="text/javascript">
	var error_inventory_order = "<?php echo $this->lang->line('error_inventory_order'); ?>";
	var error_value = "<?php echo $this->lang->line('error_value'); ?>";
	var error_zero_value = "<?php echo $this->lang->line('error_zero_value'); ?>";
	var currencyid = "<?php echo $farm_details[0]->currency; ?>";
	var selecttext = "<?php echo $this->lang->line("select"); ?>";

	$("#error-inventoryorder").hide();
	$("#error-truckplatenumber").hide();
	$("#error-conversionrate").hide();
	$("#error-servicecost").hide();
	$("#error-servicepayto").hide();
	$("#error-logisticcost").hide();
	$("#error-logisticpayto").hide();

	$(document).ready(function() {

		$('#adjustment_tax').multiselect({
			placeholder: selecttext,
			search: false,
			selectAll: false,
		});

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
			if (currencyid == 1) {
				var inputconversionrate = $("#conversion_rate").val();
			} else {
				var inputconversionrate = 0;
			}
			var servicecost = $("#service_cost").val().trim();
			var servicepayto = $("#service_payto").val();
			var logisticcost = $("#logistic_cost").val().trim();
			var logisticpayto = $("#logistic_payto").val();
			var farmadjustment = $("#adjustment").val().trim();
			var adjustrf = $("#adjust_rf").is(':checked');
			var process = $("#process").val();

			var isValid1 = true,
				isValid2 = true,
				isValid3 = true,
				isValid8 = true,
				isValid9 = true, 
				isValid10 = true,
				isValid11 = true;

			if (inputinventoryorder.length == 0) {
				$("#error-inventoryorder").show();
				isValid1 = false;
			} else {
				$("#error-inventoryorder").hide();
				isValid1 = true;
			}

			if (currencyid == 1) {
				if (inputconversionrate.length == 0) {
					$("#error-conversionrate").show();
					isValid3 = false;
				} else {
					$("#error-conversionrate").hide();
					isValid3 = true;
				}
			}

			if (servicecost != 0) {
				$("#error-servicecost").hide();
				if (servicepayto > 0) {
					$("#error-servicepayto").hide();
					isValid8 = true;
				} else {
					$("#error-servicepayto").show();
					isValid8 = false;
				}
			}

			// if (servicepayto > 0) {
			// 	$("#error-servicepayto").hide();
			// 	if (servicecost.length == 0) {
			// 		$("#error-servicecost").show();
			// 		$("#error-servicecost").text(error_value);
			// 		isValid9 = false;
			// 	} else {
			// 		$("#error-servicecost").hide();
			// 		isValid9 = true;
			// 	}
			// } else {
			// 	$("#error-servicepayto").hide();
			// 	$("#error-servicecost").hide();
			// }

			if (logisticcost != 0) {
				$("#error-logisticcost").hide();
				if (logisticpayto > 0) {
					$("#error-logisticpayto").hide();
					isValid10 = true;
				} else {
					$("#error-logisticpayto").show();
					isValid10 = false;
				}
			}

			// if (logisticpayto > 0) {
			// 	$("#error-logisticpayto").hide();
			// 	if (logisticcost.length == 0) {
			// 		$("#error-logisticcost").show();
			// 		$("#error-logisticcost").text(error_value);
			// 		isValid11 = false;
			// 	} else {
			// 		$("#error-logisticcost").hide();
			// 		isValid11 = true;
			// 	}
			// } else {
			// 	$("#error-logisticpayto").hide();
			// 	$("#error-logisticcost").hide();
			// }

			if (isValid1 && isValid2 && isValid3 && isValid8 && isValid9 && isValid10 && isValid11) {
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
				fd.append("input_conversion_rate", inputconversionrate);
				fd.append("currency_id", currencyid);
				fd.append("servicecost", servicecost);
                fd.append("servicepayto", servicepayto);
                fd.append("logisticcost", logisticcost);
                fd.append("logisticpayto", logisticpayto);
                fd.append("farmadjustment", farmadjustment);
                fd.append("adjustrf", $("#adjustment_tax").val());
				fd.append("processType", process);

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
							$("#add-modal-data-bd").modal('hide');

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