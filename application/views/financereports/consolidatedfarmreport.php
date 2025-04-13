<?php
$session = $this->session->userdata("fullname");
$applicable_origins = $session["applicable_origins"];
?>
<?php $site_lang = $this->load->helper("language"); ?>
<?php $wz_lang = $site_lang->session->userdata("site_lang"); ?>
<div class="card mb-3">
    <div class="card-header table-responsive">
        <div class="row flex-between-end">
            <div class="col-auto align-self-center">
                <input type="hidden" id="hdnCsrf" name="hdnCsrf" value="<?php echo $csrf_cgrerp; ?>" />
                <h3> <?php echo $this->lang->line("consolidatedfarmreport"); ?> </h3>
            </div>
        </div>
    </div>
    <div class="card-body pt-2">
        <div class="mb-3 row">
            <label class="col-sm-2 col-form-label lbl-font" for="origin_consolidatedfarmreport"><?php echo $this->lang->line("origin"); ?></label>
            <div class="col-sm-10">
                <select class="form-control" name="origin_consolidatedfarmreport" id="origin_consolidatedfarmreport" data-plugin="select_erp">
                    <?php if (count($applicable_origins) > 1) { ?>
                        <?php foreach ($applicable_origins as $origin) { ?>
                            <option value="<?php echo $origin->id; ?>"><?php echo $origin->origin_name; ?></option>
                        <?php } ?>
                    <?php } else { ?>
                        <?php foreach ($applicable_origins as $origin) { ?>
                            <option value="<?php echo $origin->id; ?>"><?php echo $origin->origin_name; ?></option>
                        <?php } ?>
                    <?php } ?>
                </select>
                <div class="mb-4 row"></div>
            </div>

            <div class="row flex-between-end">
            <div class="col-auto ms-auto">
                <button class="btn btn-primary btn-md btn-right-margin" title="<?php echo $this->lang->line('download_reports'); ?>" type="button" id="generate_farm_report">
                    <span class="fas fa-download" data-fa-transform="shrink-3 down-2"></span><span class="ms-1">
                        <?php echo $this->lang->line('download_reports'); ?></span>
                </button>
            </div>
        </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url() . 'assets/js/jquery341.min.js'; ?>"></script>
<script src="<?php echo base_url() . "assets/js/jquery.dataTables.min.js"; ?>"></script>
<script src="<?php echo base_url() . "assets/js/dataTables.bootstrap.min.js"; ?>"></script>
<link rel="stylesheet" href="<?php echo base_url() . 'assets/css/jquery-ui.css'; ?>">
<script src="<?php echo base_url() . 'assets/js/jquery-ui.js'; ?>"></script>