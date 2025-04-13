<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<?php $site_lang = $this->load->helper('language'); ?>
<?php $wz_lang = $site_lang->session->userdata('site_lang'); ?>
<div class="modal-header">
    <h4 class="modal-title" id="add-modal-data"><?php echo $pageheading; ?></h4>
    <?php echo form_button(array('aria-label' => 'Close', 'data-bs-dismiss' => 'modal', 'type' => 'button', 'class' => 'close', 'content' => '<span aria-hidden="true">×</span>')); ?>
</div>
<?php $attributes = array('name' => 'update', 'id' => 'update', 'autocomplete' => 'off', 'class' => '"m-b-1'); ?>
<?php $hidden = array('_method' => $pagetype); ?>
<?php echo form_open("", $attributes, $hidden); ?>
<div class="modal-body tracking-modal">
    <input type="hidden" id="pagetype" name="pagetype" value="<?php echo $pagetype; ?>">
    <input type="hidden" id="hdnReceptionId" name="hdnReceptionId" value="<?php echo $receptionid; ?>">
    <input type="hidden" id="hdnCsrf" name="hdnCsrf" value="<?php echo $csrf; ?>">

    <?php if (count($openusers) > 0) { ?>
        <div class="row mb-3">
            <div class="col-md-4 mb-3">
                <button type="button" class="btn btn-sm btn-danger mb-1" name="close_reception" id="close_reception"><?php echo $this->lang->line("close_reception"); ?></button>
                <label for="error_select_user" id="error-closeuser" style="display: none;" class="error-text mb-2"><?php echo $this->lang->line("error_select_user"); ?></label>

                <?php foreach ($openusers as $openuser) { ?>
                    <div class="form-check">
                        <input class="form-check-input" id="userid_<?php echo $openuser->user_id; ?>" name="closereceptionuser" type="checkbox" value="<?php echo $openuser->user_id; ?>">
                        <label for="userid_<?php echo $openuser->user_id; ?>"><?php echo $openuser->fullname; ?></label>
                    </div>
                <?php } ?>
            </div>
        </div>
    <?php } ?>

    <?php if (count($closedusers) > 0) { ?>
        <div class="row mb-3">
            <div class="col-md-4 mb-3">
                <button type="button" class="btn btn-sm btn-success mb-1" name="open_reception" id="open_reception"><?php echo $this->lang->line("open_reception"); ?></button>
                <label for="error_select_user" id="error-openuser" style="display: none;" class="error-text mb-2"><?php echo $this->lang->line("error_select_user"); ?></label>

                <?php foreach ($closedusers as $closeduser) { ?>
                    <div class="form-check">
                        <input class="form-check-input" id="userid_<?php echo $closeduser->user_id; ?>" name="openreceptionuser" type="checkbox" value="<?php echo $closeduser->user_id; ?>">
                        <label for="userid_<?php echo $closeduser->user_id; ?>"><?php echo $closeduser->fullname; ?></label>
                    </div>
                <?php } ?>
            </div>
        </div>
    <?php } ?>
</div>
<div class="modal-footer">
    <?php echo form_button(array('data-bs-dismiss' => 'modal', 'type' => 'button', 'class' => 'btn btn-secondary', 'content' => $this->lang->line("close"))); ?>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function() {
        $("#error-closeuser").hide();
        $("#error-openuser").hide();

        $("#close_reception").click(function(e) {

            e.preventDefault();
            var arrUserid = [];
            $.each($("input[name='closereceptionuser']:checked"), function() {
                arrUserid.push($(this).val());
            });
            var receptionid = $("#hdnReceptionId").val();

            var isValid = true;
            if (arrUserid.length == 0) {
                $("#error-closeuser").show();
                isValid = false;
            } else {
                $("#error-closeuser").hide();
                isValid = true;
            }

            if (isValid) {
                var fd = new FormData();
                fd.append("actiontype", "closereception");
                fd.append("receptionid", receptionid);
                fd.append("userid", arrUserid.join(", "));
                fd.append("csrf_cgrerp", $("#hdnCsrf").val());

                toastr.info(processing_request);

                $("#loading").show();
                $.ajax({
                    type: "POST",
                    url: base_url + "/reception_update_tracking",
                    data: fd,
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
                            $('input[name="csrf_cgrerp"]').val(JSON.csrf_hash);
                        } else {
                            toastr.clear();
                            toastr.success(JSON.result);
                            $('input[name="csrf_cgrerp"]').val(JSON.csrf_hash);
                            $("#add-modal-data").modal('hide');

                            $('#xin_table_receptiontracking').DataTable().ajax.reload(null, false);
                        }
                    },
                    error: function(jqXHR, exception) {
                        toastr.clear();
                    }
                });
            }
        });

        $("#open_reception").click(function(e) {

            e.preventDefault();
            var arrUserid = [];
            $.each($("input[name='openreceptionuser']:checked"), function() {
                arrUserid.push($(this).val());
            });

            var receptionid = $("#hdnReceptionId").val();

            var isValid = true;
            if (arrUserid.length == 0) {
                $("#error-openuser").show();
                isValid = false;
            } else {
                $("#error-openuser").hide();
                isValid = true;
            }

            if (isValid) {
                var fd = new FormData();
                fd.append("actiontype", "openreception");
                fd.append("receptionid", receptionid);
                fd.append("userid", arrUserid.join(", "));
                fd.append("csrf_cgrerp", $("#hdnCsrf").val());

                toastr.info(processing_request);

                $("#loading").show();
                $.ajax({
                    type: "POST",
                    url: base_url + "/reception_update_tracking",
                    data: fd,
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
                            $('input[name="csrf_cgrerp"]').val(JSON.csrf_hash);
                        } else {
                            toastr.clear();
                            toastr.success(JSON.result);
                            $('input[name="csrf_cgrerp"]').val(JSON.csrf_hash);
                            $("#add-modal-data").modal('hide');

                            $('#xin_table_receptiontracking').DataTable().ajax.reload(null, false);
                        }
                    },
                    error: function(jqXHR, exception) {
                        toastr.clear();
                    }
                });
            }
        });
    });
</script>