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
<?php $attributes = array('name' => 'add_taxsettings', 'id' => 'add_taxsettings', 'autocomplete' => 'off', 'class' => '"m-b-1'); ?>
<?php $hidden = array('_method' => $pagetype); ?>
<?php echo form_open_multipart('taxsettings/add', $attributes, $hidden); ?>
<div class="modal-body tax-modal">
    <input type="hidden" id="pagetype" name="pagetype" value="<?php echo $pagetype; ?>">
    <input type="hidden" id="hdntaxid" name="hdntaxid" value="<?php echo $taxid;  ?>">
    <input type="hidden" id="hdncsrf" name="hdncsrf" value="<?php echo $csrf_hash;  ?>">
    <div class="row mb-3">
        <div class="col-md-6 mb-3">
            <label for="origin_taxsettings"><?php echo $this->lang->line('origin'); ?></label>
            <select class="form-control" name="origin_taxsettings" id="origin_taxsettings" data-plugin="select_erp">
                <option value="0"><?php echo $this->lang->line("select"); ?></option>
                <?php foreach ($applicable_origins as $origin) { ?>
                    <?php if ($get_tax_details[0]->origin_id == $origin->id) { ?>
                        <option value="<?php echo $origin->id; ?>" selected="selected"><?php echo $origin->origin_name; ?></option>
                    <?php } else { ?>
                        <option value="<?php echo $origin->id; ?>"><?php echo $origin->origin_name; ?></option>
                    <?php } ?>
                <?php } ?>
            </select>
            <label id="error-origin" class="error-text"><?php echo $this->lang->line('error_origin_screen'); ?></label>
        </div>
        <div class="col-md-6">
            <label for="tax_name"><?php echo $this->lang->line('tax_name'); ?></label>
            <input class="form-control text-uppercase" placeholder="<?php echo $this->lang->line('tax_name'); ?>" name="tax_name" id="tax_name" type="text" value="<?php echo isset($get_tax_details[0]->tax_name) ? $get_tax_details[0]->tax_name : ''; ?>">
            <label id="error-name" class="error-text"><?php echo $this->lang->line('error_name'); ?></label>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-6 mb-3">
            <label for="number_format"><?php echo $this->lang->line('number_format'); ?></label>
            <select class="form-control" name="number_format" id="number_format" data-plugin="select_erp">
                <option value="0"><?php echo $this->lang->line("select"); ?></option>
                <?php if ($pagetype == 'add') { ?>
                    <option value="1"><?php echo $this->lang->line('numeric_text'); ?></option>
                    <option value="2"><?php echo $this->lang->line('percentage_text'); ?></option>
                <?php } else { ?>
                    <option value="1" <?php if ($get_tax_details[0]->number_format == 1) : ?> selected="selected" <?php endif; ?>><?php echo $this->lang->line('numeric_text'); ?></option>
                    <option value="2" <?php if ($get_tax_details[0]->number_format == 2) : ?> selected="selected" <?php endif; ?>><?php echo $this->lang->line('percentage_text'); ?></option>
                <?php } ?>
            </select>
            <label id="error-numberformat" class="error-text"><?php echo $this->lang->line('error_number_format'); ?></label>
        </div>
        <div class="col-md-6 mb-3">
            <label for="operands"><?php echo $this->lang->line('operands'); ?></label>
            <select class="form-control" name="operands" id="operands" data-plugin="select_erp">
                <option value="0"><?php echo $this->lang->line("select"); ?></option>
                <?php if ($pagetype == 'add') { ?>
                    <option value="1"><?php echo $this->lang->line('addition_text'); ?></option>
                    <option value="2"><?php echo $this->lang->line('deduction_text'); ?></option>
                <?php } else { ?>
                    <option value="1" <?php if ($get_tax_details[0]->arithmetic_type == 1) : ?> selected="selected" <?php endif; ?>><?php echo $this->lang->line('addition_text'); ?></option>
                    <option value="2" <?php if ($get_tax_details[0]->arithmetic_type == 2) : ?> selected="selected" <?php endif; ?>><?php echo $this->lang->line('deduction_text'); ?></option>
                <?php } ?>
            </select>
            <label id="error-operand" class="error-text"><?php echo $this->lang->line('error_operand'); ?></label>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-4 form-check" style="display: flex; align-items: center;">
            <input class="form-check-input" id="checkenablesupplier" name="checkenablesupplier" type="checkbox" value="" <?php echo $get_tax_details[0]->is_enabled_supplier == 1 ? "checked" : ""; ?>>
            <label for="checkenablesupplier"><?php echo $this->lang->line('enable_supplier'); ?></label>
        </div>
        <div class="col-md-4">
            <label for="default_tax_value_supplier"><?php echo $this->lang->line('default_tax_value'); ?></label>
            <input class="form-control" <?php if($pagetype == "edit" && $get_tax_details[0]->is_enabled_supplier == 1) { echo ""; } else { echo "readonly"; } ?> placeholder="<?php echo $this->lang->line('default_tax_value'); ?>" id="default_tax_value_supplier" name="default_tax_value_supplier" type="number" step="any" value="<?php echo isset($get_tax_details[0]->default_tax_value_supplier) ? ($get_tax_details[0]->default_tax_value_supplier + 0) : ''; ?>">
            <label id="error-value-supplier" class="error-text"><?php echo $this->lang->line('error_value'); ?></label>
        </div>
        <div class="col-md-4 form-check" style="display: flex; align-items: center;">
            <input class="form-check-input" id="applysupplier" name="applysupplier" type="checkbox" value="">
            <label for="applysupplier"><?php echo $this->lang->line('apply_supplier'); ?></label>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-4 form-check" style="display: flex; align-items: center;">
            <input class="form-check-input" id="checkenabletransporter" name="checkenabletransporter" type="checkbox" value="" <?php echo $get_tax_details[0]->is_enabled_transporter == 1 ? "checked" : ""; ?>>
            <label for="checkenabletransporter"><?php echo $this->lang->line('enable_transporter'); ?></label>
        </div>
        <div class="col-md-4">
            <label for="default_tax_value_transporter"><?php echo $this->lang->line('default_tax_value'); ?></label>
            <input class="form-control" <?php if($pagetype == "edit" && $get_tax_details[0]->is_enabled_transporter == 1) { echo ""; } else { echo "readonly"; } ?> placeholder="<?php echo $this->lang->line('default_tax_value'); ?>" id="default_tax_value_transporter" name="default_tax_value_transporter" type="number" step="any" value="<?php echo isset($get_tax_details[0]->default_tax_value_provider) ? ($get_tax_details[0]->default_tax_value_provider + 0) : ''; ?>">
            <label id="error-value-transporter" class="error-text"><?php echo $this->lang->line('error_value'); ?></label>
        </div>
        <div class="col-md-4 form-check" style="display: flex; align-items: center;">
            <input class="form-check-input" id="applytransporter" name="applytransporter" type="checkbox" value="">
            <label for="applytransporter"><?php echo $this->lang->line('apply_transporter'); ?></label>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-4 mb-3 form-check" style="display: flex; align-items: center;">
            <input class="form-check-input" id="enablepurchasemanager" name="enablepurchasemanager" type="checkbox" value="" <?php echo $get_tax_details[0]->is_applicable_purchase_manager == 1 ? "checked" : ""; ?>>
            <label for="enablepurchasemanager"><?php echo $this->lang->line('enable_purchase_manager'); ?></label>
        </div>
        <div class="col-md-6 mb-3">
            <label for="status_taxsettings"><?php echo $this->lang->line('status'); ?></label>
            <select class="form-control" name="status_taxsettings" id="status_taxsettings" data-plugin="select_erp">
                <?php if ($pagetype == 'add') { ?>
                    <option value="1"><?php echo $this->lang->line('active'); ?></option>
                    <option value="0"><?php echo $this->lang->line('inactive'); ?></option>
                <?php } else { ?>
                    <option value="1" <?php if ($get_tax_details[0]->is_active == 1) : ?> selected="selected" <?php endif; ?>><?php echo $this->lang->line('active'); ?></option>
                    <option value="0" <?php if ($get_tax_details[0]->is_active == 0) : ?> selected="selected" <?php endif; ?>><?php echo $this->lang->line('inactive'); ?></option>
                <?php } ?>
            </select>
        </div>
    </div>
</div>
<div class="modal-footer">
    <?php echo form_button(array('data-bs-dismiss' => 'modal', 'type' => 'button', 'class' => 'btn btn-secondary', 'content' => $this->lang->line('close'))); ?>
    <?php echo form_button(array('name' => 'cgrerp_form_origin', 'type' => 'submit', 'class' => 'btn btn-success addtaxsettings', 'content' => $pagetype == 'edit' ? $this->lang->line('update') : $this->lang->line('add'))); ?>
</div>
<?php echo form_close(); ?>
<link rel="stylesheet" href="<?php echo base_url() . 'assets/css/bootstrap-multiselect.css'; ?>">
<script src="<?php echo base_url() . 'assets/js/bootstrap-multiselect.js'; ?>"></script>

<script type="text/javascript">
    var processing_request = "<?php echo $this->lang->line("processing_request") ?>";
    var error_select_role = "<?php echo $this->lang->line('error_select_role'); ?>";

    $(document).ready(function() {

        $("#error-name").hide();
        $("#error-origin").hide();
        $("#error-numberformat").hide();
        $("#error-operand").hide();
        $("#error-value-supplier").hide();
        $("#error-value-transporter").hide();

        $("#checkenablesupplier").change(function() {
            if (this.checked) {
                $("#default_tax_value_supplier").removeAttr("readonly");
            } else {
                $("#default_tax_value_supplier").attr("readonly", "readonly");
            }
        });

        $("#checkenabletransporter").change(function() {
            if (this.checked) {
                $("#default_tax_value_transporter").removeAttr("readonly");
            } else {
                $("#default_tax_value_transporter").attr("readonly", "readonly");
            }
        });

        $("#add_taxsettings").submit(function(e) {
            e.preventDefault();
            var pagetype = $("#pagetype").val().trim();
            var taxid = $("#hdntaxid").val().trim();
            var tax_name = $("#tax_name").val().trim();
            var number_format = $("#number_format").val();
            var operands = $("#operands").val();
            var tax_origin = $("#origin_taxsettings").val();
            var isValid1 = true,
                isValid2 = true,
                isValid3 = true,
                isValid4 = true,
                isValid5 = true,
                isValid6 = true,
                isValid7 = true,
                isValid8 = true;

            if (tax_origin == 0) {
                $("#error-origin").show();
                isValid1 = false;
            } else {
                $("#error-origin").hide();
                isValid1 = true;
            }

            if (tax_name.length == 0) {
                $("#error-name").show();
                isValid1 = false;
            } else {
                $("#error-name").hide();
                isValid1 = true;
            }

            if (number_format == 0) {
                $("#error-numberformat").show();
                isValid3 = false;
            } else {
                $("#error-numberformat").hide();
                isValid3 = true;
            }

            if (operands == 0) {
                $("#error-operand").show();
                isValid4 = false;
            } else {
                $("#error-operand").hide();
                isValid4 = true;
            }

            if (isValid1 && isValid2 && isValid3 && isValid4) {

                if ($("#checkenablesupplier").prop('checked') == true) {
                    isValid5 = true;
                } else {
                    isValid5 = false;
                }

                if ($("#checkenabletransporter").prop('checked') == true) {
                    isValid6 = true;
                } else {
                    isValid6 = false;
                }

                if (isValid5 == false && isValid6 == false) {

                    toastr.clear();
                    toastr.warning(error_select_role);
                    return false;

                } else {

                    var default_tax_value_supplier = $("#default_tax_value_supplier").val().trim();
                    var default_tax_value_transporter = $("#default_tax_value_transporter").val().trim();

                    if (isValid5 == true && default_tax_value_supplier.length == 0) {
                        $("#error-value-supplier").show();
                        isValid7 = false;
                    } else {
                        $("#error-value-supplier").hide();
                        isValid7 = true;
                    }

                    if (isValid6 == true && default_tax_value_transporter.length == 0) {
                        $("#error-value-transporter").show();
                        isValid8 = false;
                    } else {
                        $("#error-value-transporter").hide();
                        isValid8 = true;
                    }

                    if (isValid7 && isValid8) {

                        var fd = new FormData();
                        fd.append("name", name);
                        fd.append("add_type", "taxsettings");
                        fd.append("action_type", pagetype);
                        fd.append("tax_id", taxid);
                        fd.append("csrf_cgrerp", $("#hdncsrf").val());
                        fd.append("tax_origin", tax_origin);
                        fd.append("tax_name", tax_name);
                        fd.append("number_format", number_format);
                        fd.append("operands", operands);
                        fd.append("enable_supplier", $("#checkenablesupplier").prop('checked'));
                        fd.append("enable_transporter", $("#checkenabletransporter").prop('checked'));
                        fd.append("default_tax_value_supplier", default_tax_value_supplier);
                        fd.append("default_tax_value_transporter", default_tax_value_transporter);
                        fd.append("status_taxsettings", $("#status_taxsettings").val());
                        fd.append("apply_supplier", $("#applysupplier").prop('checked'));
                        fd.append("apply_transporter", $("#applytransporter").prop('checked'));
                        fd.append("enable_purchase_manager", $("#enablepurchasemanager").prop('checked'));

                        $(".addtaxsettings").prop('disabled', true);
                        toastr.info(processing_request);

                        $("#loading").show();

                        $.ajax({
                            type: "POST",
                            url: e.target.action,
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
                                    $('.addtaxsettings').prop('disabled', false);
                                    $('input[name="csrf_cgrerp"]').val(JSON.csrf_hash);
                                } else {
                                    toastr.clear();
                                    toastr.success(JSON.result);
                                    $('.addtaxsettings').prop('disabled', false);
                                    $('input[name="csrf_cgrerp"]').val(JSON.csrf_hash);
                                    $("#add-modal-data-lg-bd").modal('hide');
                                    $('#xin_tax_settings').DataTable().ajax.reload(null, false);
                                }
                            }
                        });
                    }
                }
            }
        });
    });
</script>