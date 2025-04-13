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
<?php $attributes = array('name' => 'action_button', 'id' => 'action_button', 'autocomplete' => 'off', 'class' => '"m-b-1'); ?>
<?php echo form_open_multipart($actionurl, $attributes, $hidden); ?>
<div class="modal-body farm-modal">
    <input type="hidden" id="hdnInputId" name="hdnInputId" value="<?php echo $inputid; ?>">
    <input type="hidden" id="hdnInputId1" name="hdnInputId1" value="<?php echo $inputid1; ?>">
    <input type="hidden" id="hdnInputId2" name="hdnInputId2" value="<?php echo $inputid2; ?>">
    <input type="hidden" id="hdnActionType" name="hdnActionType" value="<?php echo $actiontype; ?>">
    <h5 class="origin_farms"><?php echo $pagemessage; ?></h5>
</div>
</div>
<div class="modal-footer">
    <?php echo form_button(array('data-bs-dismiss' => 'modal', 'type' => 'button', 'class' => 'btn btn-secondary', 'content' => $this->lang->line('close'))); ?>
    <?php echo form_button(array('name' => 'cgrerp_form_origin', 'type' => 'submit', 'class' => 'btn btn-danger action_button', 'content' => $this->lang->line('delete'))); ?>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    var common_error = "<?php echo $this->lang->line('common_error'); ?>";

    $(document).ready(function() {

        $("#action_button").submit(function(e) {

            e.preventDefault();
            var inputId = $("#hdnInputId").val().trim();
            var inputId1 = $("#hdnInputId1").val().trim();
            var inputId2 = $("#hdnInputId2").val().trim();
            var actionType = $("#hdnActionType").val().trim();

            if (inputId > 0) {
                var fd = new FormData(this);

                $(".action_button").prop('disabled', true);
                toastr.info(processing_request);
                $("#loading").show();

                $.ajax({
                    type: "GET",
                    url: e.target.action,
                    data: 'jd=1&is_ajax=3&mode=modal&type=' + actionType + '&inputid=' + inputId + '&inputid1=' + inputId1 + '&inputid2=' + inputId2,
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(JSON) {
                        $("#loading").hide();
                        if (JSON.redirect == true) {
                            window.location.replace(login_url);
                        } else if (JSON.error != '') {
                            toastr.clear();
                            toastr.error(JSON.error);
                            $('.action_button').prop('disabled', false);
                            $('input[name="csrf_cgrerp"]').val(JSON.csrf_hash);
                        } else {
                            toastr.clear();
                            toastr.success(JSON.result);
                            $('.action_button').prop('disabled', false);
                            $('input[name="csrf_cgrerp"]').val(JSON.csrf_hash);
                            $("#add-modal-data").modal('hide');
                            $("#add-modal-data-bd1").modal('hide');

                            $('<?php echo $xin_table; ?>').DataTable().ajax.reload(null, false);
                        }
                    }
                });
            } else {
                toastr.clear();
                toastr.error(common_error);
            }
        });
    });
</script>