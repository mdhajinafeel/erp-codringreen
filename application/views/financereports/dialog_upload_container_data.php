<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<?php
$session = $this->session->userdata('fullname');
$applicable_origins = $session["applicable_origins"];
?>
<?php $site_lang = $this->load->helper('language'); ?>
<?php $wz_lang = $site_lang->session->userdata('site_lang'); ?>

<div class="modal-header">
    <h4 class="modal-title" id="add-modal-data"><?php echo $pageheading; ?></h4>
    <?php echo form_button(array('aria-label' => 'Close', 'data-bs-dismiss' => 'modal', 'type' => 'button', 'class' => 'close', 'content' => '<span aria-hidden="true">×</span>')); ?>

</div>
<?php $attributes = array('name' => '', 'id' => '', 'autocomplete' => 'off', 'class' => '"m-b-1'); ?>
<?php $hidden = array('_method' => $pagetype); ?>
<?php echo form_open_multipart('', $attributes, $hidden); ?>
<div class="modal-body farm-modal">
    <input type="hidden" id="pagetype" name="pagetype" value="<?php echo $pagetype; ?>">
    <input type="hidden" id="hdnCsrf" name="hdnCsrf" value="<?php echo $csrf_hash; ?>">

    <div class="row mb-3">
        <div class="col-md-6 mb-2">
            <label for="fileContainerExcel"><?php echo $this->lang->line('upload_file'); ?></label>
            <input name="fileContainerExcel" type="file" accept=".xlsx" id="fileContainerExcel" class="form-control">
            <label id="error-selectfile" class="error-text"><?php echo $this->lang->line('error_select_file'); ?></label>
        </div>
    </div>
</div>
</div>
<div class="modal-footer">
    <?php echo form_button(array('data-bs-dismiss' => 'modal', 'type' => 'button', 'class' => 'btn btn-secondary', 'content' => $this->lang->line('close'))); ?>
    <button type="button" class="btn btn-success btn-md" id="btn_upload_data"><?php echo $this->lang->line("text_upload"); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $("#error-selectfile").hide();

    $(document).ready(function() {

        $("#btn_upload_data").click(function() {

            var isValid = true;

            var files = $('#fileContainerExcel')[0].files[0];
            if (files != null && files != "") {
                $("#error-selectfile").hide();

                if (isValid) {
                    var fd = new FormData();
                    fd.append("csrf_cgrerp", $("#hdnCsrf").val());
                    fd.append("fileContainerExcel", files);

                    $("#loading").show();
                    $.ajax({
                        url: base_url + "/upload_container_template_data",
                        cache: false,
                        method: "POST",
                        data: fd,
                        contentType: false,
                        processData: false,
                        success: function(JSON) {
                            $("#loading").hide();
                            if (JSON.redirect == true) {
                                window.location.replace(login_url);
                            } else if (JSON.result != '') {
                                toastr.clear();
                                toastr.success(JSON.result);
                                $("#add-modal-data-lg").modal('hide');
                            } else {
                                toastr.clear();
                                toastr.error(JSON.error);
                            }
                        }
                    });
                }
            } else {
                $("#error-selectfile").show();
                isValid = false;
            }
        });
    });
</script>