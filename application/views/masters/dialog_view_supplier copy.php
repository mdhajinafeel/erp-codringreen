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

				<?php foreach($supplier_taxes as $suppliertax) { ?>

				<div class="input-group">
					<label class="control-label"><?php echo $this->lang->line('iva'); ?>: <?php echo $is_iva_enabled == true ? $iva_value : 'Not Applicable'; ?></label>
				</div>
				<div class="input-group">
					<label class="control-label"><?php echo $this->lang->line('retention'); ?>: <?php echo $is_retencion_enabled == true ? $retencion_value : 'Not Applicable'; ?></label>
				</div>
				<div class="input-group">
					<label class="control-label"><?php echo $this->lang->line('reteica'); ?>: <?php echo $is_reteica_enabled == true ? $reteica_value : 'Not Applicable'; ?></label>
				</div>
				<?php } ?>
			</div>
		</div>
	<?php } ?>

	<?php if ($provider_role_enabled == true) { ?>
		<div class="row">
			<div class="col-md-12">
				<label class="head-label"><?php echo $this->lang->line('provider_taxes'); ?></label>
				<div class="input-group">
					<label class="control-label"><?php echo $this->lang->line('iva'); ?>: <?php echo $is_iva_provider_enabled == true ? $iva_provider_value : 'Not Applicable'; ?></label>
				</div>
				<div class="input-group">
					<label class="control-label"><?php echo $this->lang->line('retention'); ?>: <?php echo $is_retencion_provider_enabled == true ? $retencion_provider_value : 'Not Applicable'; ?></label>
				</div>
				<div class="input-group">
					<label class="control-label"><?php echo $this->lang->line('reteica'); ?>: <?php echo $is_reteica_provider_enabled == true ? $reteica_provider_value : 'Not Applicable'; ?></label>
				</div>
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
