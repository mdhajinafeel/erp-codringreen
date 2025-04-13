<?php
$session = $this->session->userdata('fullname');
$applicable_origins = $session["applicable_origins"];
?>
<?php $site_lang = $this->load->helper('language'); ?>
<?php $wz_lang = $site_lang->session->userdata('site_lang'); ?>
<div class="card mb-3">
	<div class="card-header table-responsive">
		<div class="row flex-between-end">
			<div class="col-auto align-self-center">
				<h3> <?php echo $this->lang->line('reception_title'); ?> </h3>
			</div>
			<div class="col-auto ms-auto">
				<button class="btn btn-success btn-md" title="<?php echo $this->lang->line('new'); ?>" type="button" id="btn_new_reception">
					<span class="fas fa-plus" data-fa-transform="shrink-3 down-2"></span><span class="ms-1"><?php echo $this->lang->line('new'); ?></span></button>
			</div>

		</div>

	</div>
	<div class="card-body pt-0">

		<div class="row mb-5">
			<div class="col-md-4 align-self-center">
				<label for="origin"><?php echo $this->lang->line("origin"); ?></label>
				<select class="form-control" name="origin" id="origin" data-plugin="select_erp">
					<?php if (count($applicable_origins) > 1) { ?>
						<option value="0"><?php echo $this->lang->line('all'); ?></option>
						<?php foreach ($applicable_origins as $origin) { ?>
							<option value="<?php echo $origin->id; ?>"><?php echo $origin->origin_name; ?></option>
						<?php } ?>
					<?php } else { ?>
						<?php foreach ($applicable_origins as $origin) { ?>
							<option value="<?php echo $origin->id; ?>" selected="selected"><?php echo $origin->origin_name; ?></option>
						<?php } ?>
					<?php } ?>
				</select>
			</div>
		</div>

		<table class="datatables-demo table table-striped table-bordered" id="xin_table_receptions" style="width: 100% !important;">

			<thead>
				<tr>
					<th width="100px"><?php echo $this->lang->line('action'); ?></th>
					<th><?php echo $this->lang->line('inventory_order'); ?></th>
					<th><?php echo $this->lang->line('supplier_name'); ?></th>
					<th><?php echo $this->lang->line('product'); ?></th>
                    <th><?php echo $this->lang->line('received_date'); ?></th>
					<th><?php echo $this->lang->line('warehouse'); ?></th>
					<th><?php echo $this->lang->line('volume'); ?></th>
					<th><?php echo $this->lang->line('origin'); ?></th>
					<th><?php echo $this->lang->line('uploaded_by'); ?></th>
				</tr>
			</thead>

		</table>
	</div>
</div>
<script src="<?php echo base_url() . 'assets/js/jquery341.min.js'; ?>"></script>

<script src="<?php echo base_url() . 'assets/js/jquery.dataTables.min.js'; ?>"></script>
<script src="<?php echo base_url() . 'assets/js/dataTables.bootstrap.min.js'; ?>"></script>
<link rel="stylesheet" href="<?php echo base_url() . 'assets/css/jquery-ui.css'; ?>">
<script src="<?php echo base_url() . 'assets/js/jquery-ui.js'; ?>"></script>