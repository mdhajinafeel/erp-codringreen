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
                <input type="hidden" id="hdnCsrf" name="hdnCsrf" value="<?php echo $csrf_cgrerp; ?>" />
                <h3> <?php echo $rates; ?> </h3>
            </div>
        </div>
    </div>
    <div class="card-body pt-5">
        <div class="mb-3 row">
            <label class="col-sm-2 col-form-label lbl-font" for="origin_gcreport"><?php echo $this->lang->line('origin'); ?></label>
            <div class="col-sm-10">
                <select class="form-control" name="origin_gcreport" id="origin_gcreport" data-plugin="select_erp">
                    <option value="0"><?php echo $this->lang->line('select'); ?></option>
                    <?php foreach ($applicable_origins as $origin) { ?>
                        <option value="<?php echo $origin->id; ?>"><?php echo $origin->origin_name; ?></option>
                    <?php } ?>
                </select>
                <label id="error-origin" class="error-text"><?php echo $this->lang->line("error_origin_screen"); ?></label>
                <div class="mb-4 row"></div>
            </div>

            <label class="col-sm-2 col-form-label lbl-font" for="buyer_name"><?php echo $this->lang->line('buyer_name'); ?></label>
            <div class="col-sm-10">
                <select class="form-control" name="buyer_name" id="buyer_name" data-plugin="select_erp">
                    <option value="0"><?php echo $this->lang->line('select'); ?></option>
                </select>
                <div class="mb-4 row"></div>
            </div>

            <div class="row flex-between-end">
                <div class="col-md-10 ms-auto">
                    <button class="btn btn-success btn-block ml-10" title="<?php echo $this->lang->line("generate_report"); ?>" type="button" id="btn_download_gcreports">
                        <span class="ms-1"><?php echo $this->lang->line("generate_report"); ?></span></button>

                    <button class="btn btn-danger btn-block ml-10" title="<?php echo $this->lang->line("reset"); ?>" type="button" id="btn_reset">
                        <span class="ms-1"><?php echo $this->lang->line("reset"); ?></span></button>
                </div>
            </div>
        </div>
    </div>
</div>


<script src="<?php echo base_url() . 'assets/js/jquery341.min.js'; ?>"></script>