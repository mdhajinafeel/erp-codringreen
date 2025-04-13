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
<?php echo form_open_multipart('suppliers/add', $attributes, $hidden); ?>
<div class="modal-body supplier-modal">
	<input type="hidden" id="pagetype" name="pagetype" value="<?php echo $pagetype; ?>">
	<input type="hidden" id="hdnsupplierid" name="hdnsupplierid" value="<?php echo $supplierid;  ?>">
	<input type="hidden" id="hdnsuppliercode" name="hdnsuppliercode" value="<?php echo isset($get_supplier_details[0]->supplier_code) ? $get_supplier_details[0]->supplier_code : ''; ?>">
	<input type="hidden" id="hdnoriginid" name="hdnoriginid" value="<?php echo isset($get_supplier_details[0]->origin_id) ? $get_supplier_details[0]->origin_id : ''; ?>">

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
			<label for="companyname"><?php echo $this->lang->line('company_name'); ?></label>
			<input class="form-control" placeholder="<?php echo $this->lang->line('company_name'); ?>" name="companyname" id="companyname" type="text" value="<?php echo isset($get_supplier_details[0]->company_name) ? $get_supplier_details[0]->company_name : ''; ?>">
		</div>
		<div class="col-md-6">
			<label for="companyid"><?php echo $this->lang->line('company_id'); ?></label>
			<input class="form-control" placeholder="<?php echo $this->lang->line('company_id'); ?>" name="companyid" id="companyid" type="text" value="<?php echo isset($get_supplier_details[0]->company_id) ? $get_supplier_details[0]->company_id : ''; ?>">
		</div>
	</div>

	<div class="row mb-3">
		<div class="col-md-6">
			<label for="address"><?php echo $this->lang->line('address'); ?></label>
			<textarea name="address" id="address" maxlength="400" rows="3" class="form-control" placeholder="<?php echo $this->lang->line('address'); ?>"><?php echo isset($get_supplier_details[0]->supplier_address) ? htmlspecialchars($get_supplier_details[0]->supplier_address) : ''; ?></textarea>
			<label id="error-address" class="error-text"><?php echo $this->lang->line('error_address'); ?></label>
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
		<div class="col-md-12">
			<label for="bank"><?php echo $this->lang->line('bank_detail'); ?></label>
			<div class="table-responsive scrollbar dynamic_fields">

				<?php
				echo "<script type='text/javascript'>";
				echo "var i = 1";
				echo "</script>";
				?>

				<?php if ($pagetype == "edit") { ?>

					<table class="table" id="bank_fields">
						<?php $i = 1;

						foreach ($supplier_bank_details as $bank) {
							echo "<script type='text/javascript'>";
							echo "i=" . $i;
							echo "</script>";
						?>
							<?php if ($i == 1) { ?>
								<tr class="DataRow">
									<td class="form-inline" width="150px">
										<input type="text" name="bankname[]" id="bankname[]" onkeypress="return isAlphabets(event)" placeholder="<?php echo $this->lang->line('enter_bankname'); ?>" class="form-control bankName" value="<?php echo $bank->bank_name; ?>" />
									</td>
									<td class="form-inline" width="150px">
										<input type="text" name="bankaccountnumber[]" id="bankaccountnumber[]" onkeypress="return isNumberKey(this, event)" placeholder="<?php echo $this->lang->line('enter_accountnumber'); ?>" class="form-control bankAccountNumber" value="<?php echo $bank->bank_accountnumber; ?>" />
									</td>
									<td class="form-inline" width="150px">
										<input type="text" name="bankholdername[]" onkeypress="return isAlphabets(event)" id="bankholdername[]" placeholder="<?php echo $this->lang->line('enter_holder'); ?>" class="form-control bankHolderName" value="<?php echo $bank->bank_holdername; ?>" />
									</td>
									<td class="form-inline" width="150px">
										<select class="form-control accountType" name="accounttype[]" id="accounttype[]" oninput="this.setCustomValidity('')">
											<option value="0"><?php echo $this->lang->line('select_account_type'); ?></option>
											<option value="1" <?php if ($bank->bank_accounttype == 1) : ?> selected <?php endif; ?>><?php echo $this->lang->line('current'); ?></option>
											<option value="2" <?php if ($bank->bank_accounttype == 2) : ?> selected <?php endif; ?>><?php echo $this->lang->line('savings'); ?></option>
											<option value="3" <?php if ($bank->bank_accounttype == 3) : ?> selected <?php endif; ?>><?php echo $this->lang->line('others'); ?></option>
										</select>
									</td>
									<td class="form-inline">
										<button type="button" name="add_bank_fields" id="add_bank_fields" class="btn btn-success addicon"><i class="fas fa-plus"></i></button>
									</td>
								</tr>
							<?php } else {  ?>
								<?php $bankid = "row_removebank_" . $i;
								$removebankid = "removebank_" . $i; ?>
								<tr class="DataRow" id="<?php echo $bankid; ?>">
									<td class="form-inline" width="150px">
										<input type="text" name="bankname[]" id="bankname[]" onkeypress="return isAlphabets(event)" placeholder="<?php echo $this->lang->line('enter_bankname'); ?>" class="form-control bankName" value="<?php echo $bank->bank_name; ?>" />
									</td>
									<td class="form-inline" width="150px">
										<input type="text" name="bankaccountnumber[]" id="bankaccountnumber[]" onkeypress="return isNumberKey(this, event)" placeholder="<?php echo $this->lang->line('enter_accountnumber'); ?>" class="form-control bankAccountNumber" value="<?php echo $bank->bank_accountnumber; ?>" />
									</td>
									<td class="form-inline" width="150px">
										<input type="text" name="bankholdername[]" onkeypress="return isAlphabets(event)" id="bankholdername[]" placeholder="<?php echo $this->lang->line('enter_holder'); ?>" class="form-control bankHolderName" value="<?php echo $bank->bank_holdername; ?>" />
									</td>
									<td class="form-inline" width="150px">
										<select class="form-control accountType" name="accounttype[]" id="accounttype[]" oninput="this.setCustomValidity('')">
											<option value="0"><?php echo $this->lang->line('select_account_type'); ?></option>
											<option value="1" <?php if ($bank->bank_accounttype == 1) : ?> selected <?php endif; ?>><?php echo $this->lang->line('current'); ?></option>
											<option value="2" <?php if ($bank->bank_accounttype == 2) : ?> selected <?php endif; ?>><?php echo $this->lang->line('savings'); ?></option>
											<option value="3" <?php if ($bank->bank_accounttype == 3) : ?> selected <?php endif; ?>><?php echo $this->lang->line('others'); ?></option>
										</select>
									</td>
									<td class="form-inline">
										<button type="button" name="remove" id="<?php echo $removebankid; ?>" class="btn btn-danger btn_remove_bank addicon"><i class="fas fa-remove"></i></button>
									</td>
								</tr>
							<?php } ?>
						<?php $i++;
						} ?>
					</table>
				<?php } else { ?>
					<table class="table" id="bank_fields">
						<tr class="DataRow">
							<td class="form-inline" width="150px">
								<input type="text" name="bankname[]" id="bankname[]" onkeypress="return isAlphabets(event)" placeholder="<?php echo $this->lang->line('enter_bankname'); ?>" class="form-control bankName" />
							</td>
							<td class="form-inline" width="150px">
								<input type="text" name="bankaccountnumber[]" id="bankaccountnumber[]" onkeypress="return isNumberKey(this, event)" placeholder="<?php echo $this->lang->line('enter_accountnumber'); ?>" class="form-control bankAccountNumber" />
							</td>
							<td class="form-inline" width="150px">
								<input type="text" name="bankholdername[]" onkeypress="return isAlphabets(event)" id="bankholdername[]" placeholder="<?php echo $this->lang->line('enter_holder'); ?>" class="form-control bankHolderName" />
							</td>
							<td class="form-inline" width="150px">
								<select class="form-control accountType" name="accounttype[]" id="accounttype[]" oninput="this.setCustomValidity('')">
									<option value="0"><?php echo $this->lang->line('select_account_type'); ?></option>
									<option value="1"><?php echo $this->lang->line('current'); ?></option>
									<option value="2"><?php echo $this->lang->line('savings'); ?></option>
									<option value="3"><?php echo $this->lang->line('others'); ?></option>
								</select>
							</td>
							<td class="form-inline">
								<button type="button" name="add_bank_fields" id="add_bank_fields" class="btn btn-success addicon"><i class="fas fa-plus"></i></button>
							</td>
						</tr>
					</table>
				<?php } ?>
			</div>
		</div>
	</div>

	<div class="row mb-3">
		<div class="col-md-12">
			<label for="product"><?php echo $this->lang->line('wood_details'); ?></label>
			<div class="table-responsive scrollbar dynamic_fields">

				<?php


				echo "<script type='text/javascript'>";
				echo "var j = 1";
				echo "</script>";
				?>

				<?php if ($pagetype == "edit") {
					$product_byorigin = $this->Master_model->get_product_byorigin($get_supplier_details[0]->origin_id);
				?>

					<table class="table" id="product_fields">
						<?php $j = 1;
						foreach ($supplier_product_details as $supplierproduct) {
							echo "<script type='text/javascript'>";
							echo "j=" . $j;
							echo "</script>"; ?>

							<?php if ($j == 1) { ?>
								<tr class="DataRow">
									<td class="form-inline" width="200px">
										<select class="form-control col-md-4 productId" name="woodspecies" id="woodspecies">
											<option value="0"><?php echo $this->lang->line("select"); ?></option>

											<?php foreach ($product_byorigin as $product) { ?>
												<option value="<?php echo $product->product_id; ?>" <?php if ($supplierproduct->product_name == $product->product_id) : ?> selected <?php endif; ?>>
													<?php echo $product->product_name; ?>
												</option>
											<?php } ?>
										</select>
									</td>
									<td class="form-inline" width="200px">
										<div>
											<input type="radio" class="woodType" id="radiowoodtype_s" name="radiowoodtype_1" <?php if ($supplierproduct->product_type == 1) : ?> checked <?php endif; ?> value="1"><label for="radiowoodtype_s"><?php echo $this->lang->line('Square Blocks'); ?></label>
										</div>
										<div>
											<input type="radio" class="woodType" id="radiowoodtype_r" name="radiowoodtype_1" <?php if ($supplierproduct->product_type == 2) : ?> checked <?php endif; ?> value="2"><label for="radiowoodtype_r"><?php echo $this->lang->line('Round Logs'); ?></label>
										</div>
										<div>
											<input type="radio" class="woodType" id="radiowoodtype_o" name="radiowoodtype_1" <?php if ($supplierproduct->product_type == 3) : ?> checked <?php endif; ?> value="3"><label for="radiowoodtype_o"><?php echo $this->lang->line('Both'); ?></label>
										</div>
									</td>
									<td>
										<button type="button" name="add_product_fields" id="add_product_fields" class="btn btn-success addicon"><i class="fas fa-plus"></i></button>
									</td>
								</tr>
							<?php } else {
								$productid = "row_removeproduct_" . $j;
								$removeproductid = "removeproduct_" . $j; ?>

								<tr class="DataRow" id="<?php echo $productid; ?>">
									<td class="form-inline" width="200px">
										<select class="form-control col-md-4 productId" name="woodspecies" id="woodspecies">
											<option value="0"><?php echo $this->lang->line('select'); ?></option>

											<?php foreach ($product_byorigin as $product) { ?>
												<option value="<?php echo $product->product_id; ?>" <?php if ($supplierproduct->product_name == $product->product_id) : ?> selected <?php endif; ?>>
													<?php echo $product->product_name; ?>
												</option>
											<?php } ?>
										</select>
									</td>
									<td class="form-inline" width="200px">
										<div>
											<input type="radio" class="woodType" id="radiowoodtype_s_<?php echo $j; ?>" name="radiowoodtype_<?php echo $j; ?>" <?php if ($supplierproduct->product_type == 1) : ?> checked <?php endif; ?> value="1"><label for="radiowoodtype_s_<?php echo $j; ?>"><?php echo $this->lang->line('Square Blocks'); ?></label>
										</div>
										<div>
											<input type="radio" class="woodType" id="radiowoodtype_r_<?php echo $j; ?>" name="radiowoodtype_<?php echo $j; ?>" <?php if ($supplierproduct->product_type == 2) : ?> checked <?php endif; ?> value="2"><label for="radiowoodtype_r_<?php echo $j; ?>"><?php echo $this->lang->line('Round Logs'); ?></label>
										</div>
										<div>
											<input type="radio" class="woodType" id="radiowoodtype_o_<?php echo $j; ?>" name="radiowoodtype_<?php echo $j; ?>" <?php if ($supplierproduct->product_type == 3) : ?> checked <?php endif; ?> value="3"><label for="radiowoodtype_o_<?php echo $j; ?>"><?php echo $this->lang->line('Both'); ?></label>
										</div>
									</td>
									<td>
										<button type="button" name="remove" id="<?php echo $removeproductid; ?>" class="btn btn-danger btn_remove_product addicon"><i class="fas fa-remove"></i></button>
									</td>
								</tr>


							<?php } ?>

						<?php $j++;
						} ?>
					</table>
				<?php } else { ?>
					<table class="table" id="product_fields">
						<tr class="DataRow">
							<td class="form-inline" width="200px">
								<select class="form-control col-md-4 productId" name="woodspecies" id="woodspecies">
									<option value="0"><?php echo $this->lang->line("select"); ?></option>

									<!-- <?php //foreach ($products as $product) { 
											?>
										<option value="<?php //echo $product->product_id; 
														?>"> <?php //echo $product->product_name; 
																?></option>
									<?php //} 
									?> -->
								</select>
							</td>
							<td class="form-inline" width="200px">
								<div>
									<input type="radio" class="woodType" id="radiowoodtype_s" name="radiowoodtype_1" checked value="1"><label for="radiowoodtype_s"><?php echo $this->lang->line('Square Blocks'); ?></label>
								</div>
								<div>
									<input type="radio" class="woodType" id="radiowoodtype_r" name="radiowoodtype_1" value="2"><label for="radiowoodtype_r"><?php echo $this->lang->line('Round Logs'); ?></label>
								</div>
								<div>
									<input type="radio" class="woodType" id="radiowoodtype_o" name="radiowoodtype_1" value="3"><label for="radiowoodtype_o"><?php echo $this->lang->line('Both'); ?></label>
								</div>
							</td>
							<td>
								<button type="button" name="add_product_fields" id="add_product_fields" class="btn btn-success addicon"><i class="fas fa-plus"></i></button>
							</td>
						</tr>

					</table>
				<?php } ?>
			</div>
		</div>
	</div>

	<div class="row mb-3">
		<div class="col-md-12">
			<div class="col-md-3 form-check">
				<input class="form-check-input" id="enablesupplierrole" name="enablesupplierrole" type="checkbox" value="1" <?php if ($supplier_role_enabled == true) : ?> checked <?php endif; ?> onChange="handleSupplierRoleCheckboxChange(this);">
				<label for="enablesupplierrole"><?php echo $this->lang->line('enable_supplier'); ?></label>
			</div>

			<?php if ($pagetype == "edit" && $supplier_role_enabled == true) {
				echo "<script type='text/javascript'>";
				echo "$('#idSupplierTaxes').show();";
				echo "</script>";
			} else {
				echo "<script type='text/javascript'>";
				echo "$('#idSupplierTaxes').hide();";
				echo "</script>";
			} ?>

			<div id="idSupplierTaxes">
				<label class="label-underline"><?php echo $this->lang->line('supplier_tax'); ?></label>
				<div class="form-group row" id="dynamicTaxes">
					<?php if ($pagetype == "edit") { echo $supplier_taxes; ?>
					<?php } ?>
				</div>
			</div>

			<?php if ($pagetype == "edit") {

				echo "<script type='text/javascript'>";
				if ($is_iva_enabled == true) {
					echo '$("#checksupplieriva").attr("checked", "checked");';
					echo '$("#supplieriva").removeAttr("disabled");';
					echo '$("#supplieriva").val(' . $iva_value . ');';
				} else {
					echo '$("#supplieriva").attr("disabled", "disabled");';
					echo '$("#supplieriva").val("");';
				}
				echo "</script>";
			} else {
				echo "<script type='text/javascript'>";
				echo '$("#supplieriva").attr("disabled", "disabled");';
				echo '$("#supplierretention").attr("disabled", "disabled");';
				echo '$("#supplierretica").attr("disabled", "disabled");';
				echo "</script>";
			} ?>
		</div>
	</div>

	<div class="row mb-3">
		<div class="col-md-12">
			<div class="form-check">
				<input class="form-check-input" id="enableproviderrole" name="enableproviderrole" type="checkbox" value="2" onChange="handleProviderRoleCheckboxChange(this);" <?php if ($provider_role_enabled == true) : ?> checked <?php endif; ?>>
				<label for="enableproviderrole"><?php echo $this->lang->line('enable_provider'); ?></label>
			</div>

			<?php if ($pagetype == "edit" && $provider_role_enabled == true) {
				echo "<script type='text/javascript'>";
				echo "$('#idProviderTaxes').show();";
				echo "</script>";
			} else {
				echo "<script type='text/javascript'>";
				echo "$('#idProviderTaxes').hide();";
				echo "</script>";
			} ?>

			<div id="idProviderTaxes">
				<label class="label-underline"><?php echo $this->lang->line('provider_tax'); ?></label>
				<div class="form-group row" id="dynamicProviderTaxes">
					<?php if ($pagetype == "edit") { echo $provider_taxes; ?>
					<?php } ?>
				</div>
			</div>

			<?php if ($pagetype == "edit") {

				echo "<script type='text/javascript'>";
				if ($is_iva_provider_enabled == true) {
					echo '$("#checkprovideriva").attr("checked", "checked");';
					echo '$("#provideriva").removeAttr("disabled");';
					echo '$("#provideriva").val(' . $iva_provider_value . ');';
				} else {
					echo '$("#provideriva").attr("disabled", "disabled");';
					echo '$("#provideriva").val("");';
				}

				if ($is_retencion_provider_enabled == true) {
					echo '$("#checkproviderretention").attr("checked", "checked");';
					echo '$("#providerretention").removeAttr("disabled");';
					echo '$("#providerretention").val(' . $retencion_provider_value . ');';
				} else {
					echo '$("#providerretention").attr("disabled", "disabled");';
					echo '$("#providerretention").val("");';
				}

				if ($is_reteica_provider_enabled == true) {
					echo '$("#checkproviderretica").attr("checked", "checked");';
					echo '$("#providerretica").removeAttr("disabled");';
					echo '$("#providerretica").val(' . $reteica_provider_value . ');';
				} else {
					echo '$("#providerretica").attr("disabled", "disabled");';
					echo '$("#providerretica").val("");';
				}

				echo "</script>";
			} else {
				echo "<script type='text/javascript'>";
				echo '$("#provideriva").attr("disabled", "disabled");';
				echo '$("#providerretention").attr("disabled", "disabled");';
				echo '$("#providerretica").attr("disabled", "disabled");';
				echo "</script>";
			} ?>

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
					<option value="1" <?php if ($get_supplier_details[0]->isactive == 1) : ?> selected="selected" <?php endif; ?>><?php echo $this->lang->line('active'); ?></option>
					<option value="0" <?php if ($get_supplier_details[0]->isactive == 0) : ?> selected="selected" <?php endif; ?>><?php echo $this->lang->line('inactive'); ?></option>
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

<script type="text/javascript">
	var products_byorigin = "";
	var productfields = "";
	var supplierTaxes = [];
	var providerTaxes = [];
	var arrSupplierTaxes = [];
	var arrProviderTaxes = [];

	function isNumberKeyWithoutNegative(evt) {
		var charCode = (evt.which) ? evt.which : event.keyCode;
		return !(charCode > 31 && (charCode < 48 || charCode > 57));
	}

	function isNumberKey(txt, evt) {
		var charCode = (evt.which) ? evt.which : evt.keyCode;
		if (charCode == 46) {
			if (txt.value.indexOf('.') === -1) {
				return true;
			} else {
				return false;
			}
		} else {
			if (charCode > 31 &&
				(charCode < 48 || charCode > 57))
				return false;
		}
		return true;
	}

	function isAlphabets(e) {
		var key = e.keyCode;
		if (key >= 48 && key <= 57) {
			e.preventDefault();
		}
	}

	function handleSupplierRoleCheckboxChange(e) {
		if (e.checked) {
			$("#idSupplierTaxes").show();
		} else {
			$("#idSupplierTaxes").hide();
		}
	}

	function handleProviderRoleCheckboxChange(e) {
		if (e.checked) {
			$("#idProviderTaxes").show();
		} else {
			$("#idProviderTaxes").hide();
		}
	}

	$(document).ready(function() {

		var selectedIVAValue = "";
		var selectedRetencionValue = "";
		var selectedReteicaValue = "";
		var selectedIVAProviderValue = "";
		var selectedRetencionProviderValue = "";
		var selectedReteicaProviderValue = "";

		var enter_bank = "<?php echo $this->lang->line('enter_bankname'); ?>";
		var enter_account = "<?php echo $this->lang->line('enter_accountnumber'); ?>";
		var enter_holder = "<?php echo $this->lang->line('enter_holder'); ?>";
		var select_account = "<?php echo $this->lang->line('select_account_type'); ?>";
		var current = "<?php echo $this->lang->line('current'); ?>";
		var savings = "<?php echo $this->lang->line('savings'); ?>";
		var others = "<?php echo $this->lang->line('others'); ?>";
		var select = "<?php echo $this->lang->line("select"); ?>";
		var square_blocks = "<?php echo $this->lang->line('Square Blocks'); ?>";
		var round_logs = "<?php echo $this->lang->line('Round Logs'); ?>";
		var both = "<?php echo $this->lang->line('Both'); ?>";

		var error_bankname = "<?php echo $this->lang->line('error_bankname'); ?>";
		var error_bankaccount = "<?php echo $this->lang->line('error_bankaccount'); ?>";
		var error_bankholder = "<?php echo $this->lang->line('error_bankholder'); ?>";
		var error_selectaccount = "<?php echo $this->lang->line('error_selectaccount'); ?>";
		var error_selectwoodtype = "<?php echo $this->lang->line('error_selectwoodtype'); ?>";
		var error_selectwoodspecies = "<?php echo $this->lang->line('error_selectwoodspecies'); ?>";
		var error_enablerole = "<?php echo $this->lang->line('error_enablerole'); ?>";
		var error_value = "<?php echo $this->lang->line('error_value'); ?>";
		var text_for = "<?php echo $this->lang->line('for'); ?>";
		var error_zero_value = "<?php echo $this->lang->line('error_zero_value'); ?>";
		var error_duplicate_entry = "<?php echo $this->lang->line('error_duplicate_entry'); ?>";
		var enable_origin = "<?php echo $this->lang->line('enable_origin'); ?>";

		products_byorigin = "";
		productfields = "";

		$("#error-name").hide();
		$("#error-supplierid").hide();
		$("#error-address").hide();
		$("#error-origin").hide();

		$("#error-provideriva").hide();
		$("#error-providerretention").hide();
		$("#error-providerretica").hide();
		$("#error-supplieriva").hide();
		$("#error-supplierretention").hide();
		$("#error-supplierretica").hide();

		var editPageType = "<?php echo $pagetype; ?>";

		if (editPageType == "add") {
			$("#add_product_fields").attr("title", enable_origin);
			$("#add_product_fields").attr("disabled", "disabled");
			$("#add_product_fields").css({
				"pointer-events": "auto"
			});

			$("#enablesupplierrole").attr("title", enable_origin);
			$("#enablesupplierrole").attr("disabled", "disabled");
			$("#enablesupplierrole").css({
				"pointer-events": "auto"
			});

			$("#enableproviderrole").attr("title", enable_origin);
			$("#enableproviderrole").attr("disabled", "disabled");
			$("#enableproviderrole").css({
				"pointer-events": "auto"
			});

			$("#enablesupplierrole").prop('checked', false);
			$("#enableproviderrole").prop('checked', false);

			$("#idSupplierTaxes").hide();
			$("#idProviderTaxes").hide();
		} else {
			$("#add_product_fields").attr("title", "");
			$("#add_product_fields").attr("disabled", false);
			$("#add_product_fields").css({
				"pointer-events": "auto"
			});

			$.ajax({
				url: base_url + "/products_byorigin?originid=" + $("#hdnoriginid").val(),
				cache: false,
				method: "GET",
				dataType: 'json',
				success: function(JSON) {

					if (JSON.redirect == true) {
						window.location.replace(login_url);
					} else if (JSON.result != '') {
						products_byorigin = JSON.result;
					}
				}
			});
		}

		// $('#checksupplieriva').change(function() {
		// 	if (this.checked) {
		// 		$('#supplieriva').removeAttr("disabled", "disabled");
		// 		if (selectedIVAValue != "") {
		// 			$("#supplieriva").val((selectedIVAValue * 1).toString());
		// 		} else {
		// 			$("#supplieriva").val("");
		// 		}
		// 	} else {
		// 		$("#supplieriva").attr("disabled", "disabled");
		// 		$("#supplieriva").val("");
		// 	}
		// });

		// $('#checksupplierretention').change(function() {
		// 	if (this.checked) {
		// 		$('#supplierretention').removeAttr("disabled", "disabled");
		// 		if (selectedRetencionValue != "") {
		// 			$("#supplierretention").val((selectedRetencionValue * 1).toString());
		// 		} else {
		// 			$("#supplierretention").val("");
		// 		}
		// 	} else {
		// 		$("#supplierretention").attr("disabled", "disabled");
		// 		$("#supplierretention").val("");
		// 	}
		// });

		// $('#checksupplierretica').change(function() {
		// 	if (this.checked) {
		// 		$('#supplierretica').removeAttr("disabled", "disabled");
		// 		if (selectedReteicaValue != "") {
		// 			$("#supplierretica").val((selectedReteicaValue * 1).toString());
		// 		} else {
		// 			$("#supplierretica").val("");
		// 		}
		// 	} else {
		// 		$("#supplierretica").attr("disabled", "disabled");
		// 		$("#supplierretica").val("");
		// 	}
		// });

		// $('#checkprovideriva').change(function() {
		// 	if (this.checked) {
		// 		$('#provideriva').removeAttr("disabled", "disabled");
		// 		if (selectedIVAValue != "") {
		// 			$("#provideriva").val((selectedIVAProviderValue * 1).toString());
		// 		} else {
		// 			$("#provideriva").val("");
		// 		}
		// 	} else {
		// 		$("#provideriva").attr("disabled", "disabled");
		// 		$("#provideriva").val("");
		// 	}
		// });

		// $('#checkproviderretention').change(function() {
		// 	if (this.checked) {
		// 		$('#providerretention').removeAttr("disabled", "disabled");
		// 		if (selectedRetencionValue != "") {
		// 			$("#providerretention").val((selectedRetencionProviderValue * 1).toString());
		// 		} else {
		// 			$("#providerretention").val("");
		// 		}
		// 	} else {
		// 		$("#providerretention").attr("disabled", "disabled");
		// 		$("#providerretention").val("");
		// 	}
		// });

		// $('#checkproviderretica').change(function() {
		// 	if (this.checked) {
		// 		$('#providerretica').removeAttr("disabled", "disabled");
		// 		if (selectedReteicaValue != "") {
		// 			$("#providerretica").val((selectedReteicaProviderValue * 1).toString());
		// 		} else {
		// 			$("#providerretica").val("");
		// 		}
		// 	} else {
		// 		$("#providerretica").attr("disabled", "disabled");
		// 		$("#providerretica").val("");
		// 	}
		// });

		//$('#enablesupplierrole').change(function() {
		// if (this.checked) {
		// 	$("#idSupplierTaxes").show();
		// } else {
		// 	$("#idSupplierTaxes").hide();
		// }
		//});

		// $('#enableproviderrole').change(function() {
		// 	if (this.checked) {
		// 		$("#idProviderTaxes").show();
		// 	} else {
		// 		$("#idProviderTaxes").hide();
		// 	}
		// });

		$("#origin").change(function() {
			if ($("#origin").val() == 0) {
				$("#add_product_fields").attr("title", enable_origin);
				$("#add_product_fields").attr("disabled", "disabled");
				$("#add_product_fields").css({
					"pointer-events": "auto"
				});

				$("#enablesupplierrole").attr("title", enable_origin);
				$("#enablesupplierrole").attr("disabled", "disabled");
				$("#enablesupplierrole").css({
					"pointer-events": "auto"
				});

				$("#enableproviderrole").attr("title", enable_origin);
				$("#enableproviderrole").attr("disabled", "disabled");
				$("#enableproviderrole").css({
					"pointer-events": "auto"
				});
			} else {
				$("#add_product_fields").removeAttr("title");
				$("#add_product_fields").removeAttr("disabled");
				$("#add_product_fields").css({
					"pointer-events": "auto"
				});

				$("#enablesupplierrole").removeAttr("title");
				$("#enablesupplierrole").removeAttr("disabled");
				$("#enablesupplierrole").css({
					"pointer-events": "auto"
				});

				$("#enableproviderrole").removeAttr("title");
				$("#enableproviderrole").removeAttr("disabled");
				$("#enableproviderrole").css({
					"pointer-events": "auto"
				});
			}

			$("#enablesupplierrole").prop('checked', false);
			$("#enableproviderrole").prop('checked', false);

			$("#idSupplierTaxes").hide();
			$("#idProviderTaxes").hide();
			get_products_by_origin();
			get_supplier_taxes_by_origin();
		});

		$('#add_bank_fields').click(function() {
			if (i > 6) {
				toastr.clear();
				toastr.warning("<?php echo $this->lang->line('bank_limit'); ?>");
				return false;
			}
			i++;

			var bankfields = '<tr class="DataRow" id="row_removebank_' + i + '">';
			bankfields = bankfields + '<td class="form-inline" width="150px">';
			bankfields = bankfields + '<input type="text" name="bankname[]" id="bankname[]" onkeypress="return isAlphabets(event)" placeholder="' + enter_bank + '" class="form-control bankName" /></td>';
			bankfields = bankfields + '<td class="form-inline" width="150px"><input type="text" name="bankaccountnumber[]" id="bankaccountnumber[]" onkeypress="return isNumberKey(this, event)" placeholder="' + enter_account + '" class="form-control bankAccountNumber" /></td>';
			bankfields = bankfields + '<td class="form-inline" width="150px"><input type="text" name="bankholdername[]" onkeypress="return isAlphabets(event)" id="bankholdername[]" placeholder="' + enter_holder + '" class="form-control bankHolderName" /></td>';
			bankfields = bankfields + '<td class="form-inline" width="150px"><select class="form-control accountType" name="accounttype[]" id="accounttype[]"> <option value="0">' + select_account + '</option> <option value="1">' + current + '</option> <option value="2">' + savings + '</option> <option value="3">' + others + '</option> </select></td>';
			bankfields = bankfields + '<td class="form-inline"><button type="button" name="remove" id="removebank_' + i + '" class="btn btn-danger btn_remove_bank addicon"><i class="fas fa-remove"></i></button>';
			bankfields = bankfields + '</td>';
			bankfields = bankfields + '</tr>';

			$('#bank_fields').append(bankfields);
		});

		$(document).on('click', '.btn_remove_bank', function() {
			var button_id = $(this).attr("id");
			$('#row_' + button_id + '').remove();
			i--;
		});

		$('#add_product_fields').click(function() {
			if (j > 9) {
				toastr.clear();
				toastr.warning("<?php echo $this->lang->line('product_limit'); ?>");
				return false;
			}
			j++;

			productfields = '<tr class="DataRow" id="row_removeproduct_' + j + '">';
			productfields = productfields + '<td class="form-inline" width="200px">';
			productfields = productfields + '<select class="form-control col-md-4 productId" name="woodspecies" id="woodspecies">' + products_byorigin + '</select></td>';
			productfields = productfields + '<td class="form-inline" width="200px">';
			productfields = productfields + '<div> <input type="radio" class="woodType" id="radiowoodtype_s_' + j + '" name="radiowoodtype_' + j + '" checked value="1"><label for="radiowoodtype_s_' + j + '">' + square_blocks + '</label> </div> <div> <input type="radio" class="woodType" id="radiowoodtype_r_' + j + '" name="radiowoodtype_' + j + '" value="2"><label for="radiowoodtype_r_' + j + '">' + round_logs + '</label> </div> <div> <input type="radio" class="woodType" id="radiowoodtype_o_' + j + '" name="radiowoodtype_' + j + '" value="3"><label for="radiowoodtype_o_' + j + '">' + both + '</label> </div> </td>';
			productfields = productfields + '<td>';
			productfields = productfields + '<button type="button" name="remove" id="removeproduct_' + j + '" class="btn btn-danger btn_remove_product addicon"><i class="fas fa-remove"></i></button>';
			productfields = productfields + '</td>';
			productfields = productfields + '</tr>';

			$('#product_fields').append(productfields);
		});

		$(document).on('click', '.btn_remove_product', function() {
			var button_id = $(this).attr("id");
			$('#row_' + button_id + '').remove();
			j--;
		});

		$("#add_supplier").submit(function(e) {

			e.preventDefault();

			var fd = new FormData(this);
			var pagetype = $("#pagetype").val().trim();
			var supplierid_db = $("#hdnsupplierid").val().trim();
			var suppliercode = $("#hdnsuppliercode").val().trim();
			var name = $("#name").val().trim();
			var supplierid = $("#supplierid").val().trim();
			var address = $("#address").val().trim();
			var supplier_origin = $("#origin").val();

			arrSupplierTaxes = [];
			arrProviderTaxes = [];
			var isValid1 = true,
				isValid2 = true,
				isValid3 = true,
				isValid4 = true,
				isValid5 = true,
				isValid6 = true,
				isValid7 = true,
				isValid8 = true,
				isValid9 = true,
				isValid10 = true,
				isValid11 = true,
				isValid12 = true,
				isValid13 = true,
				isValid14 = true,
				isValid15 = true;

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

			if (address.length == 0) {
				$("#error-address").show();
				isValid3 = false;
			} else {
				$("#error-address").hide();
				isValid3 = true;
			}

			if (supplier_origin == 0) {
				$("#error-origin").show();
				isValid15 = false;
			} else {
				$("#error-origin").hide();
				isValid15 = true;
			}

			if (isValid1 && isValid2 && isValid3 && isValid15) {

				var bank_array = [];
				$("[id*=bank_fields] .DataRow").each(function() {

					var bankName = $(this).closest('tr').find('.bankName').val().trim();
					var bankAccountNumber = $(this).closest('tr').find('.bankAccountNumber').val().trim();
					var bankHolderName = $(this).closest('tr').find('.bankHolderName').val().trim();
					var accountType = $(this).closest('tr').find('.accountType').val().trim();
					if (bankName != "") {
						if (bankAccountNumber != "") {
							if (bankHolderName != "") {
								if (accountType > 0) {
									var firstTableData = {};
									firstTableData.bankName = bankName;
									firstTableData.bankAccountNumber = bankAccountNumber;
									firstTableData.bankHolderName = bankHolderName;
									firstTableData.accountType = accountType;
									bank_array.push(firstTableData);
								} else {
									toastr.warning(error_selectaccount);
									$(this).closest('tr').find('.accountType').focus();
									isValid4 = false;
									return false;
								}
							} else {
								toastr.warning(error_bankholder);
								$(this).closest('tr').find('.bankHolderName').focus();
								isValid4 = false;
								return false;
							}
						} else {
							toastr.warning(error_bankaccount);
							$(this).closest('tr').find('.bankAccountNumber').focus();
							isValid4 = false;
							return false;
						}
					} else {
						toastr.warning(error_bankname);
						$(this).closest('tr').find('.bankName').focus();
						isValid4 = false;
						return false;
					}
				});

				if (isValid4) {

					var product_array = [];
					var i1 = 1;
					$("[id*=product_fields] .DataRow").each(function() {

						var productId = $(this).closest('tr').find('.productId').val().trim();
						var woodType = $(this).closest('tr').find('input[name=radiowoodtype_' + i1 + ']:checked').val();
						if (productId > 0) {
							if (woodType > 0) {
								var productTableData = {};
								productTableData.productId = productId;
								productTableData.woodType = woodType;
								product_array.push(productTableData);
							} else {
								toastr.warning(error_selectwoodspecies);
								$(this).closest('tr').find('.productId').focus();
								isValid5 = false;
								return false;
							}
						} else {
							toastr.warning(error_selectwoodtype);
							$(this).closest('tr').find('.woodType').focus();
							isValid5 = false;
							return false;
						}

						i1++;
					});

					if (isValid5) {

						if ($("#enablesupplierrole").prop('checked') == true) {
							isValid6 = true;
						} else {
							isValid6 = false;
						}

						if ($("#enableproviderrole").prop('checked') == true) {
							isValid7 = true;
						} else {
							isValid7 = false;
						}

						var supplierIvaEnabled = 0;
						var supplierRetentionEnabled = 0;
						var supplierReticaEnabled = 0;
						var supplierTax4Enabled = 0;
						var supplierTax5Enabled = 0;
						var supplierIvaValue = 0;
						var supplierRetentionValue = 0;
						var supplierReticaValue = 0;
						var supplierIvaValue = 0;
						var supplierTax4Value = 0;
						var supplierTax5Value = 0;

						var providerIvaEnabled = 0;
						var providerRetentionEnabled = 0;
						var providerReticaEnabled = 0;
						var providerTax4Enabled = 0;
						var providerTax5Enabled = 0;
						var providerIvaValue = 0;
						var providerRetentionValue = 0;
						var providerReticaValue = 0;
						var providerTax4Value = 0;
						var providerTax5Value = 0;

						if (isValid6 == false && isValid7 == false) {
							toastr.warning("Please enable the supplier or provider role");
							return false;
						} else {
							if ($("#enablesupplierrole").prop('checked') == true) {

								fd.append("supplier_role", 1);

								$.each(supplierTaxes, function(i, item) {

									var checksupplier = 'checksupplier' + item.id;
									var inputsupplier = 'supplier' + item.id;
									var errorsupplier = 'error-supplier' + item.id;

									var checksupplierField = document.getElementById(checksupplier);
									var inputsupplierField = document.getElementById(inputsupplier);
									//var errorsupplierField = document.getElementById(errorsupplier);


									if (checksupplierField != null && inputsupplierField != null) {
										if (checksupplierField.checked) {
											if (inputsupplierField.value.length == 0) {
												toastr.clear();
												toastr.error(error_value + " " + text_for + " " + item.tax_name);
												isValid8 = false;
												return false;
											}
										}

										if (isValid8) {
											arrSupplierTaxes.push({
												'taxid': item.id,
												'taxenabled': checksupplierField.checked,
												'taxvalue': inputsupplierField.value,
											});
										}

									} else {
										//NOTHING
									}
								});

								// var supplieriva = $("#supplieriva").val().trim();
								// var supplierretention = $("#supplierretention").val().trim();
								// var supplierretica = $("#supplierretica").val().trim();

								// var supplieriva = $("#supplieriva").val().trim();
								// var supplierretention = $("#supplierretention").val().trim();
								// var supplierretica = $("#supplierretica").val().trim();

							} else {
								fd.append("supplier_role", 0);
							}

							if (isValid8) {
								if ($("#enableproviderrole").prop('checked') == true) {
									fd.append("provider_role", 1);

									// var provideriva = $("#provideriva").val().trim();
									// var providerretention = $("#providerretention").val().trim();
									// var providerretica = $("#providerretica").val().trim();

									$.each(providerTaxes, function(i, item) {

										var checkprovider = 'checkprovider' + item.id;
										var inputprovider = 'provider' + item.id;
										var errorprovider = 'error-provider' + item.id;

										var checkproviderField = document.getElementById(checkprovider);
										var inputproviderField = document.getElementById(inputprovider);
										//var errorproviderField = document.getElementById(errorprovider);

										if (checkproviderField != null && inputproviderField != null) {
											if (checkproviderField.checked) {
												if (inputproviderField.value.length == 0) {
													toastr.clear();
													toastr.error(error_value + " " + text_for + " " + item.tax_name);
													isValid9 = false;
													return true;
												}
											}

											if (isValid9) {
												arrProviderTaxes.push({
													'taxid': item.id,
													'taxenabled': checkproviderField.checked,
													'taxvalue': inputproviderField.value,
												});
											}

										} else {
											//NOTHING
										}
									});

								} else {
									fd.append("provider_role", 0);
								}

								var sl = product_array;
								var out = [];

								for (var i = 0, l = sl.length; i < l; i++) {
									var unique = true;
									for (var j = 0, k = out.length; j < k; j++) {
										if ((sl[i].productId === out[j].productId) && (sl[i].woodType === out[j].woodType)) {
											unique = false;
										}
									}
									if (unique) {
										out.push(sl[i]);
									}
								}
								if (out.length == sl.length) {
									isValid14 = true;
								} else {
									toastr.warning(error_duplicate_entry);
									isValid14 = false;
								}

								if (isValid8 && isValid9 && isValid10 && isValid11 && isValid12 && isValid13 && isValid14) {

									fd.append("name", name);
									fd.append("supplierid", supplierid);
									fd.append("address", address);
									fd.append("is_ajax", 2);
									fd.append("form", action);
									fd.append("add_type", "suppliers");
									fd.append("arrSupplierTaxes", JSON.stringify(arrSupplierTaxes));
									fd.append("arrProviderTaxes", JSON.stringify(arrProviderTaxes));

									if (pagetype == "edit") {
										fd.append("suppliercode", suppliercode);
									} else {
										fd.append("suppliercode", "");
									}

									fd.append("action_type", pagetype);
									fd.append("supplier_id", supplierid_db);
									fd.append('bank_details', JSON.stringify(bank_array));
									fd.append('product_details', JSON.stringify(product_array));
									fd.append('supplier_origin', supplier_origin);

									fd.append("supplierivaenabled", supplierIvaEnabled);
									fd.append("supplierretentionenabled", supplierRetentionEnabled);
									fd.append("supplierreticaenabled", supplierReticaEnabled);
									fd.append("supplierivavalue", supplierIvaValue);
									fd.append("supplierretentionvalue", supplierRetentionValue);
									fd.append("supplierreticavalue", supplierReticaValue);

									fd.append("providerivaenabled", providerIvaEnabled);
									fd.append("providerretentionenabled", providerRetentionEnabled);
									fd.append("providerreticaenabled", providerReticaEnabled);
									fd.append("providerivavalue", providerIvaValue);
									fd.append("providerretentionvalue", providerRetentionValue);
									fd.append("providerreticavalue", providerReticaValue);

									toastr.info(processing_request);
									var obj = $(this),
										action = obj.attr('name'),
										form_table = obj.data('form-table');

									//$('.addsupplier').prop('disabled', true);
									//$("#loading").show();

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
							}
						}
					}
				}
			}
		});
	});

	function get_products_by_origin() {
		$.ajax({
			url: base_url + "/products_byorigin?originid=" + $("#origin").val(),
			cache: false,
			method: "GET",
			dataType: 'json',
			success: function(JSON) {

				if (JSON.redirect == true) {
					window.location.replace(login_url);
				} else if (JSON.result != '') {
					$("#woodspecies").empty();
					$("#woodspecies").append(JSON.result);

					products_byorigin = JSON.result;

					productfields = "";
					$("#product_fields tr[id*=row_removeproduct_]").detach();
				}
			}
		});
	}

	function get_supplier_taxes_by_origin() {
		$.ajax({
			url: base_url + "/supplier_taxes_origin?originid=" + $("#origin").val(),
			cache: false,
			method: "GET",
			dataType: 'json',
			success: function(JSON) {

				if (JSON.redirect == true) {
					window.location.replace(login_url);
				} else if (JSON.result != '') {
					$("#dynamicTaxes").empty();
					$("#dynamicTaxes").append(JSON.result["supplierTaxes"]);

					$("#dynamicProviderTaxes").empty();
					$("#dynamicProviderTaxes").append(JSON.result["providerTaxes"]);
				} else {
					$("#dynamicTaxes").empty();
				}
			}
		});
	}
</script>