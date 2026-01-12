<?php
$session = $this->session->userdata('fullname');
$applicable_origins = $session["applicable_origins"];

$site_lang = $this->load->helper('language');
$wz_lang = $site_lang->session->userdata('site_lang');

?>
<div class="card mb-3">
	<div class="card-header table-responsive">
		<div class="row flex-between-end">
			<div class="col-auto align-self-center">
				<h3> <?php echo $this->lang->line('extraction_cost'); ?> </h3>
			</div>
			<div class="col-auto ms-auto">
				<button class="btn btn-success btn-md" title="<?php echo $this->lang->line('add'); ?>" type="button" id="btn_add_extraction_cost">
					<span class="fas fa-plus" data-fa-transform="shrink-3 down-2"></span><span class="ms-1"><?php echo $this->lang->line('add'); ?></span></button>
			</div>

		</div>
	</div>

	<div class="card-body pt-0">
		<div class="row mb-5">
			<div class="col-md-4 align-self-center">
				<label for="origin"><?php echo $this->lang->line("origin"); ?></label>
				<select class="form-control" name="origin" id="origin" data-plugin="select_erp">
					<?php foreach ($applicable_origins as $origin) { ?>
						<option value="<?php echo $origin->id; ?>"><?php echo $origin->origin_name; ?></option>
					<?php } ?>
				</select>
			</div>
		</div>

		<table class="datatables-demo table table-striped table-bordered" id="xin_table_extractions" style="width: 100% !important;">
			<thead>
				<tr>
					<th width="150px"><?php echo $this->lang->line('action'); ?></th>
					<th><?php echo $this->lang->line('supplier_name'); ?></th>
					<th><?php echo $this->lang->line('contract_code'); ?></th>
					<th><?php echo $this->lang->line('extraction_date'); ?></th>
					<th><?php echo $this->lang->line('total_trees'); ?></th>
					<th><?php echo $this->lang->line('total_no_of_pieces'); ?></th>
					<th><?php echo $this->lang->line('total_volume'); ?></th>
					<th><?php echo $this->lang->line('total_value'); ?></th>
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

<script>
	var extractionTable;

	$(document).ready(function() {
		extractionTable = $('#xin_table_extractions').DataTable({
			"bDestroy": true,
			"lengthMenu": [
				[50, 100, 200, -1],
				[50, 100, 200, "All"]
			],
			"ajax": {
				url: BASE_URL_SUBFOLDER + "forestry/extractioncost/extraction_list?originid=" + $("#origin").val(),
				type: 'GET'
			},
			//dom: 'lBfrtip',
			"sScrollX": "100%",
			"scrollCollapse": true,
			"bPaginate": true,
			"sPaginationType": "full_numbers",
			paging: true,
			searching: true,
			fixedColumns: true,
			responsive: true,
			"order": [
				[0, "asc"]
			],
			"language": {
				"url": datatable_language
			}
		});
	});
</script>