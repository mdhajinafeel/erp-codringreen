<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<div class="modal-header">
	<h4 class="modal-title" id="add-modal-data"><?php echo $pageheading; ?></h4>
	<?php echo form_button(array('aria-label' => 'Close', 'data-bs-dismiss' => 'modal', 'type' => 'button', 'class' => 'close', 'content' => '<span aria-hidden="true">×</span>')); ?>

</div>
<?php echo form_open(); ?>
<div class="modal-body">
	<div class="row">
		<div class="col-md-6">
			<label class="head-label"><?php echo $this->lang->line('supplier_name'); ?></label>
			<div class="input-group">
				<label class="control-label"><?php echo $supplier_name; ?></label>
			</div>
		</div>
		<div class="col-md-6">
			<label class="head-label"><?php echo $this->lang->line('supplier_code'); ?></label>
			<div class="input-group">
				<label class="control-label"><?php echo $supplier_code; ?></label>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-6">
			<label class="head-label"><?php echo $this->lang->line('supplier_id'); ?></label>
			<div class="input-group">
				<label class="control-label"><?php echo $supplier_id; ?></label>
			</div>
		</div>
		<div class="col-md-6">
			<label class="head-label"><?php echo $this->lang->line('company_name'); ?></label>
			<div class="input-group">
				<label class="control-label"><?php echo $company_name; ?></label>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-6">
			<label class="head-label"><?php echo $this->lang->line('company_id'); ?></label>
			<div class="input-group">
				<label class="control-label"><?php echo $company_id; ?></label>
			</div>
		</div>
		<div class="col-md-6">
			<label class="head-label"><?php echo $this->lang->line('address'); ?></label>
			<div class="input-group">
				<label class="control-label"><?php echo $supplier_address; ?></label>
			</div>
		</div>
	</div>

	<?php if ($supplier_role_enabled == true && count($supplier_taxes) > 0) { ?>
		<div class="row">
			<div class="col-md-12">
				<label class="head-label"><?php echo $this->lang->line('supplier_taxes'); ?></label>
				<?php foreach ($origin_supplier_taxes as $originsuppliertax) {
					$isTaxEnabled = false;
					$taxValue = 0;
				?>
					<?php foreach ($supplier_taxes as $suppliertax) {
						if ($suppliertax->tax_id + 0 == $originsuppliertax->id + 0) {
							$isTaxEnabled = true;
							break;
						}
					} ?>
					<div class="input-group">
						<?php if ($isTaxEnabled == true) { ?>
							<?php if ($suppliertax->number_format == 1) { ?>
								<label class="control-label"><?php echo $suppliertax->tax_name; ?>: <?php echo $suppliertax->arithmetic_type == 2 ? (($suppliertax->tax_value * 1) + 0) : ($suppliertax->tax_value + 0); ?></label>
							<?php } else { ?>
								<label class="control-label"><?php echo $suppliertax->tax_name . " (%)"; ?>: <?php echo $suppliertax->arithmetic_type == 2 ? (($suppliertax->tax_value * 1) + 0) : ($suppliertax->tax_value + 0); ?></label>
							<?php } ?>
						<?php } else { ?>
							<?php if ($originsuppliertax->number_format == 1) { ?>
								<label class="control-label"><?php echo $originsuppliertax->tax_name; ?>: <?php echo $this->lang->line('not_applicable') ?></label>
							<?php } else { ?>
								<label class="control-label"><?php echo $originsuppliertax->tax_name . " (%)"; ?>: <?php echo $this->lang->line('not_applicable') ?></label>
							<?php } ?>
						<?php } ?>
					</div>

				<?php } ?>
			</div>
		</div>
	<?php } ?>

	<?php if ($provider_role_enabled == true && count($provider_taxes) > 0) { ?>
		<div class="row">
			<div class="col-md-12">
				<label class="head-label"><?php echo $this->lang->line('provider_taxes'); ?></label>
				<?php foreach ($origin_provider_taxes as $originprovidertax) {
					$ispTaxEnabled = false;
					$ptaxValue = 0;
				?>
					<?php foreach ($provider_taxes as $providertax) {
						if ($providertax->tax_id + 0 == ($originprovidertax->id + 0)) {
							$ispTaxEnabled = true;
							break;
						}
					} ?>
					<div class="input-group">
						<?php if ($ispTaxEnabled == true) { ?>
							<?php if ($providertax->number_format == 1) { ?>
								<label class="control-label"><?php echo $providertax->tax_name; ?>: <?php echo $providertax->arithmetic_type == 2 ? (($providertax->tax_value * 1) + 0) : ($providertax->tax_value + 0); ?></label>
							<?php } else { ?>
								<label class="control-label"><?php echo $providertax->tax_name . " (%)"; ?>: <?php echo $providertax->arithmetic_type == 2 ? (($providertax->tax_value * 1) + 0) : ($providertax->tax_value + 0); ?></label>
							<?php } ?>
						<?php } else { ?>
							<?php if ($originprovidertax->number_format == 1) { ?>
								<label class="control-label"><?php echo $originprovidertax->tax_name; ?>: <?php echo $this->lang->line('not_applicable') ?></label>
							<?php } else { ?>
								<label class="control-label"><?php echo $originprovidertax->tax_name . " (%)"; ?>: <?php echo $this->lang->line('not_applicable') ?></label>
							<?php } ?>
						<?php } ?>
					</div>
				<?php } ?>
			</div>
		</div>
	<?php } ?>

	<div class="row">
		<div class="col-md-12">
			<label class="head-label"><?php echo $this->lang->line('view_products'); ?></label>
			<?php foreach ($products as $product) { ?>
				<div class="input-group">
					<label class="control-label"><i class="fas fa-arrow-right" style="margin-right: 5px;"></i><?php echo $product; ?></label>
				</div>
			<?php } ?>
		</div>
	</div>

	<?php if(count($banks) > 0) { ?>
		<div class="row">
			<div class="col-md-12">
				<label class="head-label"><?php echo $this->lang->line('view_banks'); ?></label>
				<?php foreach ($banks as $bank) { ?>
					<div class="input-group">
						<label class="control-label"><i class="fas fa-arrow-right" style="margin-right: 5px;"></i><?php echo str_replace('--', '<br/>&nbsp;&nbsp;&nbsp;&nbsp;', $bank); ?></label>
					</div>
				<?php } ?>
			</div>
		</div>
	<?php } ?>

	<div class="row">
		<div class="col-md-6">
			<label class="head-label"><?php echo $this->lang->line('roles'); ?></label>
			<div class="input-group">
				<label class="control-label"><?php echo $roles; ?></label>
			</div>
		</div>

		<div class="col-md-6">
			<label class="head-label"><?php echo $this->lang->line('origin'); ?></label>
			<div class="input-group">
				<label class="control-label"><?php echo $origin; ?></label>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-6">
			<label class="head-label"><?php echo $this->lang->line('status'); ?></label>
			<div class="input-group">
				<label class="control-label"><?php echo $status; ?></label>
			</div>
		</div>
	</div>
</div>
<div class="modal-footer">
	<?php echo form_button(array('data-bs-dismiss' => 'modal', 'type' => 'button', 'class' => 'btn btn-secondary', 'content' => $this->lang->line('close'))); ?>
</div>
<?php echo form_close(); ?>