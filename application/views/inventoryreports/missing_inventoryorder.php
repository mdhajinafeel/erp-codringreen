<?php
$session = $this->session->userdata('fullname');
$applicable_origins = $session["applicable_origins"];
?>
<?php $site_lang = $this->load->helper('language'); ?>
<?php $wz_lang = $site_lang->session->userdata('site_lang'); ?>
<div class="card mb-3">
    <div class="card-header">
        <div class="row flex-between-end">
            <div class="col-auto align-self-center">
                <h3> <?php echo $this->lang->line('missinginventory_title'); ?> </h3>
            </div>
            <div class="col-auto ms-auto">
            <button class="btn btn-primary btn-md btn-right-margin" title="<?php echo $this->lang->line('download_excel'); ?>" type="button" id="generate_report">
					<span class="fas fa-download" data-fa-transform="shrink-3 down-2"></span><span class="ms-1">
						<?php echo $this->lang->line('download_excel'); ?></span>
					</button>
			</div>
        </div>

    </div>
    <div class="card-body pt-0">
        <div class="row mb-5">
            <div class="col-md-4 align-self-center">
                <label for="origin"><?php echo $this->lang->line('origin'); ?></label>
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

        <div class="row mb-5">
            <div class="tab-content">
                <div class="tab-pane preview-tab-pane active" role="tabpanel" aria-labelledby="tab-dom-ec0fa1e3-6325-4caf-a468-7691ef065d01" id="dom-ec0fa1e3-6325-4caf-a468-7691ef065d01">
                    <div class="accordion" id="accordionExample">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading1">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="true" aria-controls="collapse1">
                                    <?php echo $this->lang->line('farmreception'); ?>
                                </button>
                            </h2>
                            <div class="accordion-collapse collapse" id="collapse1" aria-labelledby="heading1" data-bs-parent="#accordionExample">
                                <div class="accordion-body table-responsive">
                                    <table class="table table-striped table-bordered" id="xin_table_missingfarms" style="width: 100% !important;">
                                        <thead>
                                            <tr>
                                                <th><?php echo $this->lang->line("inventory_order"); ?></th>
                                                <th><?php echo $this->lang->line("supplier_name"); ?></th>
                                                <th><?php echo $this->lang->line("product"); ?></th>
                                                <th><?php echo $this->lang->line("purchase_date"); ?></th>
                                                <th><?php echo $this->lang->line("total_no_of_pieces"); ?></th>
                                                <th><?php echo $this->lang->line("origin"); ?></th>
                                                <th><?php echo $this->lang->line("uploaded_by"); ?></th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading2">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2" aria-expanded="true" aria-controls="collapse2">
                                    <?php echo $this->lang->line('receptionfarm'); ?>
                                </button>
                            </h2>
                            <div class="accordion-collapse collapse" id="collapse2" aria-labelledby="heading2" data-bs-parent="#accordionExample">
                                <div class="accordion-body table-responsive">
                                    <table class="table table-striped table-bordered" id="xin_table_missingreceptions" style="width: 100% !important;">
                                        <thead>
                                            <tr>
                                                <th><?php echo $this->lang->line("inventory_order"); ?></th>
                                                <th><?php echo $this->lang->line("supplier_name"); ?></th>
                                                <th><?php echo $this->lang->line("product"); ?></th>
                                                <th><?php echo $this->lang->line("received_date"); ?></th>
                                                <th><?php echo $this->lang->line("total_no_of_pieces"); ?></th>
                                                <th><?php echo $this->lang->line("origin"); ?></th>
                                                <th><?php echo $this->lang->line("uploaded_by"); ?></th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo base_url() . 'assets/js/jquery341.min.js'; ?>"></script>
<script src="<?php echo base_url() . 'assets/js/jquery.dataTables.min.js'; ?>"></script>
<script src="<?php echo base_url() . 'assets/js/dataTables.bootstrap.min.js'; ?>"></script>