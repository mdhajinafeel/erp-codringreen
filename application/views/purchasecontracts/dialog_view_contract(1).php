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
			<label class="head-label"><?php echo $this->lang->line('origin'); ?></label>
			<div class="input-group">
				<label class="control-label"><?php echo $origin; ?></label>
			</div>
		</div>
		<div class="col-md-6">
			<label class="head-label"><?php echo $this->lang->line('contract_code'); ?></label>
			<div class="input-group">
				<label class="control-label"><?php echo $contract_code; ?></label>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-6">
			<?php if ($contract_type_id == 1) { ?>
				<label class="head-label"><?php echo $this->lang->line('supplier_name'); ?></label>
			<?php } else { ?>
				<label class="head-label"><?php echo $this->lang->line('purchasemanagername_title'); ?></label>
			<?php } ?>
			<div class="input-group">
				<label class="control-label"><?php echo $supplier_name; ?></label>
			</div>
		</div>
		<div class="col-md-6">
			<label class="head-label"><?php echo $this->lang->line('contract_type'); ?></label>
			<div class="input-group">
				<label class="control-label"><?php echo $contract_type; ?></label>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-6">
			<label class="head-label"><?php echo $this->lang->line('product'); ?></label>
			<div class="input-group">
				<label class="control-label"><?php echo $product_name; ?></label>
			</div>
		</div>
		<div class="col-md-6">
			<label class="head-label"><?php echo $this->lang->line('product_type'); ?></label>
			<div class="input-group">
				<label class="control-label"><?php echo $this->lang->line($product_type_name); ?></label>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-6">
			<label class="head-label"><?php echo $this->lang->line('measuremet_system'); ?></label>
			<div class="input-group">
				<label class="control-label"><?php echo $this->lang->line($purchase_unit); ?></label>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-12">
			<label class="head-label"><?php echo $this->lang->line('contract_price'); ?></label>
			<div class="row mb-2">
				<?php $fmt = new NumberFormatter($locale = $currency_abbreviation, NumberFormatter::CURRENCY); if ($product_type == 1 || $product_type == 3) { ?>
					<div class="col-md-3">
						<label class="col-form-label font-template-heading text-decoration-underline"><?php echo $this->lang->line('grade1'); ?></label>
					</div>
					<div class="col-md-3">
						<label class="col-form-label font-template-heading text-decoration-underline"><?php echo $this->lang->line('grade2'); ?></label>
					</div>
					<div class="col-md-3">
						<label class="col-form-label font-template-heading text-decoration-underline"><?php echo $this->lang->line('grade3'); ?></label>
					</div>
				<?php } else { ?>
					<div class="col-md-3">
						<label class="col-form-label font-template-heading text-decoration-underline"><?php echo $this->lang->line('min_range'); ?></label>
					</div>
					<div class="col-md-3">
						<label class="col-form-label font-template-heading text-decoration-underline"><?php echo $this->lang->line('max_range'); ?></label>
					</div>
					<div class="col-md-3">
						<label class="col-form-label font-template-heading text-decoration-underline"><?php echo $this->lang->line('range_price'); ?></label>
					</div>
				<?php } ?>
			</div>
			<?php foreach ($contract_price as $price) { ?>
				<div class="row">
				    <?php if ($product_type == 1 || $product_type == 3) { ?>
					<div class="col-md-3">
						<label class="col-form-label"><?php echo $fmt->format(($price->minrange_grade1 + 0)); ?></label>
					</div>
					<div class="col-md-3">
						<label class="col-form-label"><?php echo $fmt->format(($price->maxrange_grade2 + 0)); ?></label>
					</div>
					<?php } else { ?>
					<div class="col-md-3">
						<label class="col-form-label"><?php echo ($price->minrange_grade1 + 0); ?></label>
					</div>
					<div class="col-md-3">
						<label class="col-form-label"><?php echo ($price->maxrange_grade2 + 0); ?></label>
					</div>
					<?php } ?>
					<div class="col-md-3">
						<label class="col-form-label"><?php echo $fmt->format(($price->pricerange_grade3 + 0)); ?></label>
					</div>
				</div>
			<?php } ?>
		</div>
	</div>

	<div class="row">
		<div class="col-md-6">
			<label class="head-label"><?php echo $this->lang->line('currency'); ?></label>
			<div class="input-group">
				<label class="control-label"><?php echo $currency; ?></label>
			</div>
		</div>

		<div class="col-md-6">
			<label class="head-label"><?php echo $this->lang->line('payment_method'); ?></label>
			<div class="input-group">
				<label class="control-label"><?php echo $this->lang->line($payment_method); ?></label>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-6">
			<label class="head-label"><?php echo $this->lang->line('start_date'); ?></label>
			<div class="input-group">
				<label class="control-label"><?php echo $start_date; ?></label>
			</div>
		</div>

		<div class="col-md-6">
			<label class="head-label"><?php echo $this->lang->line('end_date'); ?></label>
			<div class="input-group">
				<label class="control-label"><?php echo $end_date; ?></label>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-6">
			<label class="head-label"><?php echo $this->lang->line('total_volume'); ?></label>
			<div class="input-group">
				<label class="control-label"><?php echo $total_volume; ?></label>
			</div>
		</div>

		<div class="col-md-6">
			<label class="head-label"><?php echo $this->lang->line('remaining_volume'); ?></label>
			<div class="input-group">
				<label class="control-label"><?php echo $remaining_volume; ?></label>
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