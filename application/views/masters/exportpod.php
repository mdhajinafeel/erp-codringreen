<?php $site_lang = $this->load->helper('language'); ?>
<?php $wz_lang = $site_lang->session->userdata('site_lang'); ?>
<div class="card mb-3">
	<div class="card-header table-responsive">
		<div class="row flex-between-end">
			<div class="col-auto align-self-center">
				<h3> <?php echo $this->lang->line('export_pod'); ?> </h3>
			</div>
			<div class="col-auto ms-auto">
				<button class="btn btn-success btn-md" type="button" id="btn_new_exportpod">
					<span class="fas fa-plus" data-fa-transform="shrink-3 down-2"></span><span class="ms-1"><?php echo $this->lang->line('new'); ?></span></button>
			</div>
		</div>

	</div>
	<div class="card-body pt-0">

		<table class="datatables-demo table table-striped table-bordered" id="xin_table_pod" style="width: 100% !important;">
			<thead>
				<tr>
					<th><?php echo $this->lang->line('action'); ?></th>
					<th><?php echo $this->lang->line('pod_name'); ?></th>
					<th><?php echo $this->lang->line('country_name'); ?></th>
					<th><?php echo $this->lang->line('status'); ?></th>
				</tr>
			</thead>

		</table>
	</div>
</div>
<script src="<?php echo base_url() . 'assets/js/jquery341.min.js'; ?>"></script>

<script src="<?php echo base_url() . 'assets/js/jquery.dataTables.min.js'; ?>"></script>
<script src="<?php echo base_url() . 'assets/js/dataTables.bootstrap.min.js'; ?>"></script>
