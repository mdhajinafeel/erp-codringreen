<?php
$session = $this->session->userdata('fullname');
$applicable_origins = $session["applicable_origins"];
?>
<?php $site_lang = $this->load->helper('language'); ?>
<?php $wz_lang = $site_lang->session->userdata('site_lang'); ?>
<?php $attributes = array('name' => 'save_parameter_settings', 'id' => 'save_parameter_settings', 'autocomplete' => 'off', 'class' => '"m-b-1'); ?>
<?php echo form_open_multipart('inputparametersettings/add', $attributes, $hidden); ?>

<div class="card mb-3">
    <div class="card-header table-responsive">
        <div class="row flex-between-end">
            <div class="col-auto align-self-center">
                <h3> <?php echo $this->lang->line('inputparam_title'); ?> </h3>
            </div>
        </div>
    </div>
    <div class="card-body pt-0">

        <div class="row mb-4">
            <div class="col-md-6 align-self-center">
                <label for="origin_inputparametersettings"><?php echo $this->lang->line("origin"); ?></label>
                <select class="form-control" name="origin_inputparametersettings" id="origin_inputparametersettings" data-plugin="select_erp">
                    <option value="0"><?php echo $this->lang->line("select"); ?></option>
                        <?php foreach ($applicable_origins as $origin) { ?>
                            <option value="<?php echo $origin->id; ?>"><?php echo $origin->origin_name; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <div id="divProduct" style="display: none;">
            <div class="row mb-4">
                <div class="col-md-6">
                    <label for="product_name"><?php echo $this->lang->line('product_name'); ?></label>
                    <select class="form-control" name="product_name" id="product_name" data-plugin="select_erp">
                        <option value="0"><?php echo $this->lang->line("select"); ?></option>
                    </select>
                </div>
                <div class="col-md-6" id="divProductType" style="display: none;">
                    <label for="product_type"><?php echo $this->lang->line('product_type'); ?></label>
                    <select class="form-control" name="product_type" id="product_type" data-plugin="select_erp">
                        <option value="0"><?php echo $this->lang->line("select"); ?></option>
                    </select>
                </div>
            </div>
        </div>

        <div id="parameterdata" style="display: none;">
        </div>

        <div class="row mb-5" id="savebutton" style="display: none;">
            <div class="col-md-6 align-self-center">
                <?php echo form_button(array('name' => 'cgrerp_form_origin', 'id' => 'save_parameters', 'type' => 'submit', 'class' => 'btn btn-success save_parameters', 'content' => $this->lang->line('save'))); ?>
            </div>
        </div>
    </div>
</div>

<?php echo form_close(); ?>
<script src="<?php echo base_url() . 'assets/js/jquery341.min.js'; ?>"></script>

<script src="<?php echo base_url() . 'assets/js/jquery.dataTables.min.js'; ?>"></script>
<script src="<?php echo base_url() . 'assets/js/dataTables.bootstrap.min.js'; ?>"></script>

<script type="text/javascript">
    $(document).ready(function() {

        var enter_min_range = "<?php echo $this->lang->line('enter_min_range'); ?>";
        var enter_max_range = "<?php echo $this->lang->line('enter_max_range'); ?>";
        var minimum_maximum_range = "<?php echo $this->lang->line('minimum_maximum_range'); ?>";

        $("#divProduct").css({
            "display": "none"
        });

        $("#divProductType").css({
            "display": "none"
        });

        $("#parameterdata").css({
            "display": "none"
        });

        $("#savebutton").css({
            "display": "none"
        });

        $("#product_name").select2({ dropdownCssClass: "myFont"});
        $("#product_type").select2({ dropdownCssClass: "myFont"});

        $("#origin_inputparametersettings").change(function() {

            if ($("#origin_inputparametersettings").val() == 0) {
                $("#divProduct").css({
                    "display": "none"
                });

                $("#divProductType").css({
                    "display": "none"
                });

                $("#parameterdata").css({
                    "display": "none"
                });

                $("#savebutton").css({
                    "display": "none"
                });
            } else {
                $("#divProduct").css({
                    "display": "block"
                });

                $("#divProductType").css({
                    "display": "none"
                });
            }

            $("#loading").show();

            $.ajax({
                url: base_url + "/get_product_by_origin?originid=" + $("#origin_inputparametersettings").val(),
                cache: false,
                method: "GET",
                dataType: 'json',
                success: function(JSON) {

                    if (JSON.redirect == true) {
                        window.location.replace(login_url);
                    } else if (JSON.result != '') {
                        $("#product_name").empty();
                        $("#product_name").append(JSON.result);
                    }

                    $("#loading").hide();
                }
            });

            $("#loading").show();

            $.ajax({
                url: base_url + "/get_product_type_origin?originid=" + $("#origin_inputparametersettings").val(),
                cache: false,
                method: "GET",
                dataType: 'json',
                success: function(JSON) {

                    if (JSON.redirect == true) {
                        window.location.replace(login_url);
                    } else if (JSON.result != '') {
                        $("#product_type").empty();
                        $("#product_type").append(JSON.result);
                    }

                    $("#loading").hide();
                }
            });
        });

        $("#product_name").change(function() {

            if ($("#product_name").val() == 0) {
                $("#divProductType").css({
                    "display": "none"
                });

                $("#parameterdata").css({
                    "display": "none"
                });

                $("#savebutton").css({
                    "display": "none"
                });
            } else {
                $("#divProductType").css({
                    "display": ""
                });

                $("#parameterdata").css({
                    "display": "none"
                });

                $("#savebutton").css({
                    "display": "none"
                });
            }

            $("#product_type").val('0').change();
        });

        $("#product_type").change(function() {

            if ($("#product_type").val() == 0) {

                $("#parameterdata").css({
                    "display": "none"
                });

                $("#savebutton").css({
                    "display": "none"
                });
            } else {
                $("#parameterdata").css({
                    "display": "block"
                });

                $("#savebutton").css({
                    "display": "block"
                });
            }

            $("#loading").show();

            $.ajax({
                url: base_url + "/get_input_parameters_by_origin?originid=" + $("#origin_inputparametersettings").val() + "&producttypeid=" + $("#product_type").val() + "&productid=" + $("#product_name").val(),
                cache: false,
                method: "GET",
                dataType: 'json',
                success: function(JSON) {

                    if (JSON.redirect == true) {
                        window.location.replace(login_url);
                    } else if (JSON.result != '') {
                        $("#parameterdata").html(JSON.result);
                    }

                    $("#loading").hide();
                }
            });
        });

        $("#save_parameter_settings").submit(function(e) {

            e.preventDefault();

            var isValid1 = true,
                isValid2 = true;
            var parameterData = [];
            var i1 = 1;
            $("[id*=parameterdata] .DataRow").each(function() {

                var minRange = $(this).find('.min_range').val().trim();
                var maxRange = $(this).find('.max_range').val().trim();
                var parameterName = $(this).find('#inputParameterName').val().trim();
                var inputParameterId = $(this).find('#inputParameterId').val().trim();
                var enableValidation = $(this).find('input[name=enablevalidation_' + i1 + ']').prop("checked");

                if (minRange.length > 0) {
                    if (maxRange.length == 0) {
                        toastr.warning(enter_max_range + " " + parameterName);
                        $(this).find('.max_range').focus();
                        isValid1 = false;
                        return false;
                    }
                } else if (maxRange.length > 0) {
                    if (minRange.length == 0) {
                        toastr.warning(enter_min_range + " " + parameterName);
                        $(this).find('.min_range').focus();
                        isValid1 = false;
                        return false;
                    }
                }

                if (minRange.length > 0 && maxRange.length > 0) {
                    if (Number(minRange) > Number(maxRange)) {
                        toastr.warning(minimum_maximum_range + " - " + parameterName);
                        $(this).find('.min_range').focus();
                        isValid2 = false;
                        return false;
                    }
                }

                if (isValid1 && isValid2) {
                    var data = {};
                    data.minRange = minRange;
                    data.maxRange = maxRange;
                    data.enableValidation = enableValidation;
                    data.inputParameterId = inputParameterId;
                    parameterData.push(data);
                }
                i1++;
            });

            if (isValid1 && isValid2) {

                var origin_id = $("#origin_inputparametersettings").val();
                var product = $("#product_name").val();
                var product_type = $("#product_type").val().trim();

                var fd = new FormData(this);

                $("#loading").show();
                fd.append("parameterData", JSON.stringify(parameterData));
                fd.append("originId", origin_id);
                fd.append("productId", product);
                fd.append("productTypeId", product_type);
                fd.append("add_type", "ip_settings");
                fd.append("action_type", "add");

                toastr.info(processing_request);

                var obj = $(this),
                    action = obj.attr('name'),
                    form_table = obj.data('form-table');

                $('.save_parameters').prop('disabled', true);

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
                            $('.save_parameters').prop('disabled', false);
                            $('input[name="csrf_cgrerp"]').val(JSON.csrf_hash);
                        } else {
                            toastr.clear();
                            toastr.success(JSON.result);
                            $('.save_parameters').prop('disabled', false);
                            $('input[name="csrf_cgrerp"]').val(JSON.csrf_hash);
                        }
                    }
                });
            }

        });
    });
</script>