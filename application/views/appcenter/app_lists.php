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
				<h3> <?php echo $this->lang->line('app_lists'); ?> </h3>
			</div>
			<div class="col-auto ms-auto">
				<button class="btn btn-primary btn-md" style="margin-right: 10px;" title="<?php echo $this->lang->line('generate'); ?>" type="button" id="btn_dowload_app_lists">
					<span class="fas fa-download" data-fa-transform="shrink-3 down-2"></span><span class="ms-1"><?php echo $this->lang->line('generate_report'); ?></span></button>
				<button class="btn btn-success btn-md" title="<?php echo $this->lang->line('add'); ?>" type="button" id="btn_add_apps">
					<span class="fas fa-plus" data-fa-transform="shrink-3 down-2"></span><span class="ms-1"><?php echo $this->lang->line('add'); ?></span></button>
			</div>
		</div>
	</div>
</div>