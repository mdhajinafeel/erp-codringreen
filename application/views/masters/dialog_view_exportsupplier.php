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
			<label class="head-label"><?php echo $this->lang->line('supplier_id'); ?></label>
			<div class="input-group">
				<label class="control-label"><?php echo $supplier_id; ?></label>
			</div>
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-6">
			<label class="head-label"><?php echo $this->lang->line('export_type'); ?></label>
			<div class="input-group">
				<label class="control-label"><?php echo $export_types; ?></label>
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