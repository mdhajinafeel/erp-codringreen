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
    <input type="hidden" id="hdnDispatchId" name="hdnDispatchId" value="<?php echo $dispatchid; ?>">
    <input type="hidden" id="hdnCsrf" name="hdnCsrf" value="<?php echo $csrf; ?>">

    <?php if (count($openusers) > 0) { ?>
        <div class="row mb-3">
            <div class="col-md-4 mb-3">
                <button type="button" class="btn btn-sm btn-danger mb-1" name="close_dispatch" id="close_dispatch"><?php echo $this->lang->line("close_dispatch"); ?></button>
                <label for="error_select_user" id="error-closeuser" style="display: none;" class="error-text mb-2"><?php echo $this->lang->line("error_select_user"); ?></label>

                <?php foreach ($openusers as $openuser) { ?>
                    <div class="form-check">
                        <input class="form-check-input" id="userid_<?php echo $openuser->user_id; ?>" name="closedispatchuser" type="checkbox" value="<?php echo $openuser->user_id; ?>">
                        <label for="userid_<?php echo $openuser->user_id; ?>"><?php echo $openuser->fullname; ?></label>
                    </div>
                <?php } ?>
            </div>
        </div>
    <?php } ?>

    <?php if (count($closedusers) > 0) { ?>
        <div class="row mb-3">
            <div class="col-md-4 mb-3">
                <button type="button" class="btn btn-sm btn-success mb-1" name="open_dispatch" id="open_dispatch"><?php echo $this->lang->line("open_dispatch"); ?></button>
                <label for="error_select_user" id="error-openuser" style="display: none;" class="error-text mb-2"><?php echo $this->lang->line("error_select_user"); ?></label>

                <?php foreach ($closedusers as $closeduser) { ?>
                    <div class="form-check">
                        <input class="form-check-input" id="userid_<?php echo $closeduser->user_id; ?>" name="opendispatchuser" type="checkbox" value="<?php echo $closeduser->user_id; ?>">
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

        $("#close_dispatch").click(function(e) {

            e.preventDefault();
            var arrUserid = [];
            $.each($("input[name='closedispatchuser']:checked"), function() {
                arrUserid.push($(this).val());
            });
            var dispatchid = $("#hdnDispatchId").val();

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
                fd.append("actiontype", "closedispatch");
                fd.append("dispatchid", dispatchid);
                fd.append("userid", arrUserid.join(", "));
                fd.append("csrf_cgrerp", $("#hdnCsrf").val());

                toastr.info(processing_request);

                $("#loading").show();
                $.ajax({
                    type: "POST",
                    url: base_url + "/dispatch_update_tracking",
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

                            $('#xin_table_dispatchtracking').DataTable().ajax.reload(null, false);
                        }
                    },
                    error: function(jqXHR, exception) {
                        toastr.clear();
                    }
                });
            }
        });

        $("#open_dispatch").click(function(e) {

            e.preventDefault();
            var arrUserid = [];
            $.each($("input[name='opendispatchuser']:checked"), function() {
                arrUserid.push($(this).val());
            });

            var dispatchid = $("#hdnDispatchId").val();

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
                fd.append("actiontype", "opendispatch");
                fd.append("dispatchid", dispatchid);
                fd.append("userid", arrUserid.join(", "));
                fd.append("csrf_cgrerp", $("#hdnCsrf").val());

                toastr.info(processing_request);

                $("#loading").show();
                $.ajax({
                    type: "POST",
                    url: base_url + "/dispatch_update_tracking",
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

                            $('#xin_table_dispatchtracking').DataTable().ajax.reload(null, false);
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