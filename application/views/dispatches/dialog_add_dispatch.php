<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<?php
$session = $this->session->userdata("fullname");
$applicable_origins = $session["applicable_origins"];
?>
<?php $site_lang = $this->load->helper("language"); ?>
<?php $wz_lang = $site_lang->session->userdata("site_lang"); ?>

<div class="modal-header">
    <h4 class="modal-title" id="add-modal-data"><?php echo $pageheading; ?></h4>
    <?php echo form_button(array('aria-label' => 'Close', 'data-bs-dismiss' => 'modal', 'type' => 'button', 'class' => 'close', 'content' => '<span aria-hidden="true">×</span>')); ?>

</div>
<?php $attributes = array('name' => 'add_dispatch', 'id' => 'add_dispatch', 'autocomplete' => 'off', 'class' => '"m-b-1'); ?>
<?php $hidden = array("_method" => $pagetype); ?>
<?php echo form_open_multipart("dispatches/add", $attributes, $hidden); ?>
<div class="modal-body farm-modal">
    <input type="hidden" id="pagetype" name="pagetype" value="<?php echo $pagetype; ?>">
    <div class="row mb-3">
        <div class="col-auto ms-auto">
            <a href="<?php echo base_url() . "assets/templates/Template_Dispatch_SquareBlock.xlsx"; ?>" class="btn btn-info btn-block download-template" download=""><i class="fa fa-cloud-download"></i><span style="padding-left: 10px;"><?php echo $this->lang->line("template") . ' - ' . $this->lang->line("Square Blocks"); ?></span></a>
            <a href="<?php echo base_url() . "assets/templates/Template_Dispatch_RoundLogs.xlsx"; ?>" class="btn btn-info btn-block download-template" download=""><i class="fa fa-cloud-download"></i><span style="padding-left: 10px;"><?php echo $this->lang->line("template") . ' - ' . $this->lang->line("Round Logs"); ?></span></a>
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-md-6 mb-2">
            <label for="origin_dispatch"><?php echo $this->lang->line("origin"); ?></label>
            <select class="form-control" name="origin_dispatch" id="origin_dispatch" data-plugin="select_erp">
                <option value="0"><?php echo $this->lang->line("select"); ?></option>
                <?php foreach ($applicable_origins as $origin) { ?>
                    <?php if ($get_contract_details[0]->origin_id == $origin->id) { ?>
                        <option value="<?php echo $origin->id; ?>" selected="selected"><?php echo $origin->origin_name; ?></option>
                    <?php } else { ?>
                        <option value="<?php echo $origin->id; ?>"><?php echo $origin->origin_name; ?></option>
                    <?php } ?>
                <?php } ?>
            </select>
            <label id="error-origin" class="error-text"><?php echo $this->lang->line("error_origin_screen"); ?></label>
        </div>

        <div class="col-md-6">
            <label for="container_number"><?php echo $this->lang->line("container_number"); ?></label>
            <input type="text" id="container_number" name="container_number" class="form-control text-uppercase" placeholder="<?php echo $this->lang->line("container_number"); ?>" />
            <label id="error-containernumber" class="error-text"><?php echo $this->lang->line("error_containernumber"); ?></label>
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-md-6 mb-2">
            <label for="product_name"><?php echo $this->lang->line("product_name"); ?></label>
            <select class="form-control" name="product_name" id="product_name" data-plugin="select_erp">
                <option value="0"><?php echo $this->lang->line("select"); ?></option>
                <?php if ($pagetype == "edit") { ?>
                    <?php foreach ($products as $product) { ?>
                        <option value="<?php echo $product->product_id; ?>" <?php if ($get_contract_details[0]->product == $product->product_id) : ?> selected="selected" <?php endif; ?>><?php echo $product->product_name; ?></option>
                    <?php } ?>
                <?php } ?>
            </select>
            <label id="error-productname" class="error-text"><?php echo $this->lang->line("error_product_name"); ?></label>
        </div>

        <div class="col-md-6">
            <label for="product_type"><?php echo $this->lang->line("product_type"); ?></label>
            <select class="form-control" name="product_type" id="product_type" data-plugin="select_erp">
                <option value="0"><?php echo $this->lang->line("select"); ?></option>
                <?php if ($pagetype == "edit") { ?>
                    <?php foreach ($product_types as $product_type) { ?>
                        <option value="<?php echo $product_type->type_id; ?>" <?php if ($get_contract_details[0]->product_type == $product_type->type_id) : ?> selected="selected" <?php endif; ?>><?php echo $this->lang->line($product_type->product_type_name); ?></option>
                    <?php } ?>
                <?php } ?>
            </select>
            <label id="error-woodtype" class="error-text"><?php echo $this->lang->line("error_selectwoodtype"); ?></label>
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-md-6 mb-2">
            <label for="dispatch_date"><?php echo $this->lang->line("dispatch_date"); ?></label>
            <input type="text" id="dispatch_date" name="dispatch_date" class="form-control" placeholder="<?php echo $this->lang->line("dispatch_date"); ?>" readonly />
            <label id="error-dispatchdate" class="error-text"><?php echo $this->lang->line("error_date"); ?></label>
        </div>

        <div class="col-md-6">
            <label for="warehouse"><?php echo $this->lang->line("warehouse"); ?></label>
            <select class="form-control" name="wh_name" id="wh_name" data-plugin="select_erp">
                <option value="0"><?php echo $this->lang->line("select"); ?></option>
            </select>
            <label id="error-warehouse" class="error-text"><?php echo $this->lang->line("error_warehouse_farm"); ?></label>
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-md-6 mb-2">
            <label for="shipping_line"><?php echo $this->lang->line("shipping_line"); ?></label>
            <select class="form-control" name="shipping_line" id="shipping_line" data-plugin="select_erp">
                <option value="0"><?php echo $this->lang->line("select"); ?></option>
            </select>
            <label id="error-shippingline" class="error-text"><?php echo $this->lang->line("error_shipping_line"); ?></label>
        </div>

        <div class="col-md-6">
            <label for="seal_number"><?php echo $this->lang->line("seal_number"); ?></label>
            <input type="text" id="seal_number" name="seal_number" class="form-control text-uppercase" placeholder="<?php echo $this->lang->line("seal_number"); ?>" />
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-md-6 mb-2">
            <label for="upload_type"><?php echo $this->lang->line("upload_type"); ?></label>
            <select class="form-control" name="upload_type" id="upload_type" data-plugin="select_erp">
                <option value="0"><?php echo $this->lang->line("select"); ?></option>
                <option value="1"><?php echo $this->lang->line("pieces"); ?></option>
                <!-- <option value="2"><?php echo $this->lang->line("qr_code"); ?></option> -->
            </select>
            <label id="error-uploadtype" class="error-text"><?php echo $this->lang->line("error_reception_type"); ?></label>
        </div>
        
        <div class="col-md-6">
            <label for="container_image_url"><?php echo $this->lang->line("container_image_url"); ?></label>
            <textarea name="container_image_url" id="container_image_url" maxlength="500" rows="3" class="form-control" placeholder="<?php echo $this->lang->line("container_image_url"); ?>"></textarea>
        </div>
    </div>

    <div class="row mb-2" id="divDispatchUpload">
        <div class="col-md-6 mb-2">
            <label for="fileDispatchExcel"><?php echo $this->lang->line("dispatch_upload"); ?></label>
            <input name="fileDispatchExcel" type="file" accept=".xlsx" id="fileDispatchExcel" onchange="loadFile(event)" class="form-control">
            <label id="error-dispatchupload" class="error-text"><?php echo $this->lang->line("error_dispatch_upload"); ?></label>
        </div>

        <div class="col-md-6" id="divUploadedDetails">
            <div class="next-line">
                <label class="label-showdetails" for="lblTotalPieces" id="lblTotalPieces"><?php echo $this->lang->line("total_no_of_pieces") . ": ---"; ?></label>
                <label class="label-showdetails" for="lblVolume" id="lblVolume"><?php echo $this->lang->line("total_volume") . ": ---"; ?></label>
            </div>
        </div>

        <div class="col-md-6 align-self-center" id="divErrorReportButton">
            <button class="btn btn-danger btn-block" id="btnDownloadErrorReport" name="btnDownloadErrorReport"><i class="fa fa-file-excel"></i><span style="padding-left: 10px;"><?php echo $this->lang->line("download_error_report"); ?></span></button>
        </div>
    </div>
</div>
</div>
<div class="modal-footer">
    <?php echo form_button(array("data-bs-dismiss" => "modal", "type" => "button", "class" => "btn btn-secondary", "content" => $this->lang->line("close"))); ?>
    <?php echo form_button(array("name" => "cgrerp_form_origin", "type" => "submit", "class" => "btn btn-success add_dispatch", "content" => $pagetype == "edit" ? $this->lang->line("update") : $this->lang->line("add"))); ?>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    var error_selectwoodtype = "<?php echo $this->lang->line("error_selectwoodtype"); ?>";
    var error_origin_screen = "<?php echo $this->lang->line("error_origin_screen"); ?>";
    var error_container_number = "<?php echo $this->lang->line("error_containernumber"); ?>";
    var error_dispatch_type = "<?php echo $this->lang->line("error_reception_type"); ?>";
    var total_pieces = "<?php echo $this->lang->line("total_no_of_pieces"); ?>";
    var total_volume = "<?php echo $this->lang->line("total_volume"); ?>";
    var common_error = "<?php echo $this->lang->line("common_error"); ?>";
    var template_error = "<?php echo $this->lang->line("template_error"); ?>";
    var error_dispatch_upload = "<?php echo $this->lang->line("error_dispatch_upload"); ?>";

    var templateErrorData = [];
    var dispatch_data_array = [];
    var total_pieces_uploaded = 0;
    var total_volume_uploaded = 0;
    var total_gross_volume_uploaded = 0;
    var measurementSystemId = 0;

    $("#error-origin").hide();
    $("#error-containernumber").hide();
    $("#error-productname").hide();
    $("#error-woodtype").hide();
    $("#error-dispatchdate").hide();
    $("#error-warehouse").hide();
    $("#error-uploadtype").hide();
    $("#error-dispatchupload").hide();
    $("#error-shippingline").hide();
    $("#divUploadedDetails").hide();
    $("#divErrorReportButton").hide();

    $(document).ready(function() {

        $("#origin_dispatch").change(function() {
            if ($("#origin_dispatch").val() == 0) {
                fetchProducts(0);
                fetchProductTypes(0);
                fetchWarehouses(0);
                fetchShippingLines(0);
            } else {
                fetchProducts($("#origin_dispatch").val());
                fetchProductTypes($("#origin_dispatch").val());
                fetchWarehouses($("#origin_dispatch").val());
                fetchShippingLines($("#origin_dispatch").val());
                $("#error-origin").hide();
            }
        });

        $("#upload_type").change(function() {
            templateErrorData = [];
            dispatch_data_array = [];
            total_pieces_uploaded = 0;
            total_volume_uploaded = 0;
            total_gross_volume_uploaded = 0;
            measurementSystemId = 0;
            $("#divUploadedDetails").hide();
            $("#divErrorReportButton").hide();
        });

        $("#add_dispatch").submit(function(e) {

            e.preventDefault();
            var pagetype = $("#pagetype").val().trim();
            var origin = $("#origin_dispatch").val();
            var containernumber = $("#container_number").val().trim();
            var productid = $("#product_name").val();
            var producttypeid = $("#product_type").val();
            var warehouseid = $("#wh_name").val();
            var dispatchdate = $("#dispatch_date").val().trim();
            var uploadtypeid = $("#upload_type").val();
            var containerimageurl = $("#container_image_url").val().trim();
            var sealnumber = $("#seal_number").val().trim();
            var shippingline = $("#shipping_line").val();

            var isValid1 = true,
                isValid2 = true,
                isValid3 = true,
                isValid4 = true,
                isValid5 = true,
                isValid6 = true,
                isValid7 = true,
                isValid8 = true,
                isValid9 = true;

            if (origin == 0) {
                $("#error-origin").show();
                isValid1 = false;
            } else {
                $("#error-origin").hide();
                isValid1 = true;
            }

            if (containernumber.length == 0) {
                $("#error-containernumber").show();
                isValid2 = false;
            } else {
                $("#error-containernumber").hide();
                isValid2 = true;
            }

            if (productid == 0) {
                $("#error-productname").show();
                isValid3 = false;
            } else {
                $("#error-productname").hide();
                isValid3 = true;
            }

            if (producttypeid == 0) {
                $("#error-woodtype").show();
                isValid4 = false;
            } else {
                $("#error-woodtype").hide();
                isValid4 = true;
            }

            if (dispatchdate.length == 0) {
                $("#error-dispatchdate").show();
                isValid5 = false;
            } else {
                $("#error-dispatchdate").hide();
                isValid5 = true;
            }

            if (warehouseid == 0) {
                $("#error-warehouse").show();
                isValid6 = false;
            } else {
                $("#error-warehouse").hide();
                isValid6 = true;
            }

            if (uploadtypeid == 0) {
                $("#error-uploadtype").show();
                isValid7 = false;
            } else {
                $("#error-uploadtype").hide();
                isValid7 = true;
            }

            if (shippingline == 0) {
                $("#error-shippingline").show();
                isValid9 = false;
            } else {
                $("#error-shippingline").hide();
                isValid9 = true;
            }

            if (dispatch_data_array.length == 0 || total_pieces_uploaded <= 0 || total_volume_uploaded <= 0) {
                $("#error-dispatchupload").text(error_dispatch_upload);
                $("#error-dispatchupload").show();
                isValid8 = false;
            } else {
                $("#error-dispatchupload").text("");
                $("#error-dispatchupload").hide();
                isValid8 = true;
            }

            if (isValid1 && isValid2 && isValid3 && isValid4 && isValid5 && isValid6 && isValid7 && isValid8 && isValid9) {

                var fd = new FormData(this);
                fd.append("is_ajax", 2);
                fd.append("form", action);
                fd.append("add_type", "dispatch");
                fd.append("action_type", pagetype);

                fd.append("originid", origin);
                fd.append("containernumber", containernumber);
                fd.append("productid", productid);
                fd.append("producttypeid", producttypeid);
                fd.append("measurementsystemid", measurementSystemId);
                fd.append("warehouseid", warehouseid);
                fd.append("dispatchdate", dispatchdate);
                fd.append("uploadtypeid", uploadtypeid);
                fd.append("dispatchdata", JSON.stringify(dispatch_data_array));
                fd.append("containerimageurl", containerimageurl);
                fd.append("sealnumber", sealnumber);
                fd.append("totalpiecesuploaded", total_pieces_uploaded);
                fd.append("totalvolumeuploaded", total_volume_uploaded);
                fd.append("totalgrossvolumeuploaded", total_gross_volume_uploaded);
                fd.append("shippinglineid", shippingline);

                $(".add_dispatch").prop('disabled', true);
                toastr.info(processing_request);

                var obj = $(this),
                    action = obj.attr('name'),
                    form_table = obj.data('form-table');

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
                            $('.add_dispatch').prop('disabled', false);
                            $('input[name="csrf_cgrerp"]').val(JSON.csrf_hash);
                        } else {
                            toastr.clear();
                            toastr.success(JSON.result);
                            $('.add_dispatch').prop('disabled', false);
                            $('input[name="csrf_cgrerp"]').val(JSON.csrf_hash);
                            $("#add-modal-data-bd").modal('hide');

                            $('#xin_table_dispatches').DataTable().ajax.reload(null, false);
                        }
                    },
                    error: function(jqXHR, exception) {
                        toastr.clear();
                        $('.add_dispatch').prop('disabled', false);
                    }
                });
            }
        });

        $("#btnDownloadErrorReport").click(function() {
            if (templateErrorData.length > 0) {

                var obj = $(this),
                    action = obj.attr('name'),
                    form_table = obj.data('form-table');

                var fd = new FormData();
                fd.append("errorData", JSON.stringify(templateErrorData));
                fd.append('productTypeId', $("#product_type").val());
                fd.append("productid", $("#product_name").val());

                $("#loading").show();
                $.ajax({
                    url: base_url + "/generate_error_report",
                    type: "POST",
                    data: fd,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        $("#loading").hide();
                        if (response.redirect == true) {
                            window.location.replace(login_url);
                        } else if (response.error != '') {
                            toastr.error(response.error);
                            $('input[name="csrf_cgrerp"]').val(response.csrf_hash);
                        } else {
                            toastr.success(response.successmessage);
                            $('input[name="csrf_cgrerp"]').val(response.csrf_hash);
                            window.location = response.result;
                            wait(3000);
                            deletefilesfromfolder();
                        }
                    }
                });
            } else {
                toastr.clear();
                toastr.error(common_error);
            }
        });
    });

    function fetchProducts(originid) {
        $("#loading").show();
        $.ajax({
            url: base_url + "/get_products_origin?originid=" + originid,
            cache: false,
            method: "GET",
            dataType: "json",
            success: function(JSON) {

                $("#loading").hide();

                if (JSON.redirect == true) {
                    window.location.replace(login_url);
                } else if (JSON.result != '') {
                    $("#product_name").empty();
                    $("#product_name").append(JSON.result);
                }
            }
        });
    }

    function fetchProductTypes(originid, supplierid, productid) {
        $("#loading").show();
        $.ajax({
            url: base_url + "/get_product_types?originid=" + originid,
            cache: false,
            method: "GET",
            dataType: "json",
            success: function(JSON) {
                $("#loading").hide();
                if (JSON.redirect == true) {
                    window.location.replace(login_url);
                } else if (JSON.result != '') {
                    $("#product_type").empty();
                    $("#product_type").append(JSON.result);
                }
            }
        });
    }

    function fetchWarehouses(originid) {
        $("#loading").show();
        $.ajax({
            url: base_url + "/get_warehouses_by_origin?originid=" + originid,
            cache: false,
            method: "GET",
            dataType: "json",
            success: function(JSON) {
                $("#loading").hide();
                if (JSON.redirect == true) {
                    window.location.replace(login_url);
                } else if (JSON.result != '') {
                    $("#wh_name").empty();
                    $("#wh_name").append(JSON.result);
                }
            }
        });
    }

    function fetchShippingLines(originid) {
        $("#loading").show();
        $.ajax({
            url: base_url + "/get_shipping_lines_by_origin?originid=" + originid,
            cache: false,
            method: "GET",
            dataType: "json",
            success: function(JSON) {
                $("#loading").hide();
                if (JSON.redirect == true) {
                    window.location.replace(login_url);
                } else if (JSON.result != '') {
                    $("#shipping_line").empty();
                    $("#shipping_line").append(JSON.result);
                }
            }
        });
    }

    var loadFile = function(event) {
        $("#error-dispatchupload").hide();
        var origin = $("#origin_dispatch").val().trim();
        var containernumber = $("#container_number").val().trim();
        var producttypeid = $("#product_type").val().trim();
        var uploadtype = $("#upload_type").val();
        event.preventDefault();

        var isValid1 = true;
        var isValid2 = true;
        var isValid3 = true;
        var isValid4 = true;

        toastr.clear();
        $("#error-dispatchupload").text("");
        $("#error-dispatchupload").hide();
        templateErrorData = [];
        dispatch_data_array = [];
        total_pieces_uploaded = 0;
        total_volume_uploaded = 0;
        total_gross_volume_uploaded = 0;
        measurementSystemId = 0;

        if (origin == 0) {
            toastr.error(error_origin_screen);
            $("#error-origin").show();
            $("#fileDispatchExcel").val("");
            isValid1 = false;
        } else {
            $("#error-origin").hide();
            isValid1 = true;
        }

        if (containernumber.length == 0) {
            toastr.error(error_container_number);
            $("#error-containernumber").show();
            $("#fileDispatchExcel").val("");
            isValid2 = false;
        } else {
            $("#error-containernumber").hide();
            isValid2 = true;
        }

        if (producttypeid == 0) {
            toastr.error(error_selectwoodtype);
            $("#error-woodtype").show();
            $("#fileDispatchExcel").val("");
            isValid3 = false;
        } else {
            $("#error-woodtype").hide();
            isValid3 = true;
        }

        if (uploadtype == 0) {
            toastr.error(error_dispatch_type);
            $("#error-uploadtype").show();
            $("#fileDispatchExcel").val("");
            isValid4 = false;
        } else {
            $("#error-uploadtype").hide();
            isValid4 = true;
        }

        if (isValid1 && isValid2 && isValid3 && isValid4) {
            toastr.clear();
            templateErrorData = [];
            dispatch_data_array = [];
            total_pieces_uploaded = 0;
            total_volume_uploaded = 0;
            total_gross_volume_uploaded = 0;
            measurementSystemId = 0;
            var fd = new FormData();
            var files = $('#fileDispatchExcel')[0].files[0];
            if (files != null && files != "") {
                fd.append('fileDispatchExcel', files);
                fd.append('containerNumber', containernumber);
                fd.append('originId', $("#origin_dispatch").val());
                fd.append('productTypeId', $("#product_type").val());
                fd.append("productid", $("#product_name").val());
                fd.append('uploadType', $("#upload_type").val());

                $('#loading').show();
                $.ajax({
                    url: base_url + "/load_dispatch_template",
                    type: 'post',
                    data: fd,
                    contentType: false,
                    processData: false,
                    success: function(JSON) {
                        $('#loading').hide();
                        $("#fileDispatchExcel").val("");
                        deletefilesfromfolder();
                        if (JSON.redirect == true) {
                            window.location.replace(login_url);
                        } else if (JSON.templateerror == true) {
                            toastr.clear();
                            toastr.error(template_error);
                            $("#divUploadedDetails").hide();
                            $("#divErrorReportButton").show();
                            $("#error-dispatchupload").text(template_error);
                            $("#error-dispatchupload").show();

                            templateErrorData = JSON.templateerrordata;
                        } else if (JSON.result != '') {
                            toastr.clear();
                            if (JSON.result["measurementSystemId"] == "1") {
                                $("#lblVolume").text(total_pie + ": " + JSON.result["totalVolume"]);
                            } else {
                                $("#lblVolume").text(total_volume + ": " + JSON.result["totalVolume"]);
                            }

                            $("#lblTotalPieces").text(total_pieces + ": " + JSON.result["totalPieces"]);
                            dispatch_data_array = JSON.result["dispatchData"];
                            total_pieces_uploaded = JSON.result["totalPieces"];
                            total_volume_uploaded = JSON.result["totalVolume"];
                            total_gross_volume_uploaded = JSON.result["totalGrossVolume"];
                            measurementSystemId = JSON.result["measurementSystemId"];
                            $("#divUploadedDetails").show();
                            $("#divErrorReportButton").hide();
                        } else if (JSON.error != '') {
                            toastr.clear();
                            toastr.error(JSON.error);
                            $("#divUploadedDetails").hide();
                            $("#divErrorReportButton").hide();
                            $("#error-dispatchupload").text(JSON.error);
                            $("#error-dispatchupload").show();
                        } else if (JSON.warning != '') {
                            toastr.clear();
                            toastr.warning(JSON.warning);
                            $("#divUploadedDetails").hide();
                            $("#divErrorReportButton").hide();
                            $("#error-dispatchupload").text(JSON.warning);
                            $("#error-dispatchupload").show();
                        }
                    }
                });
            } else {
                toastr.clear();
                toastr.error(common_error);
                $("#fileDispatchExcel").val("");
            }
        }
    };
</script>

<script src="<?php echo base_url() . 'assets/js/i18n/datepicker-' . $wz_lang . '.js'; ?>"></script>
<script type="text/javascript">
    $(function() {
        $("#dispatch_date").datepicker({
            dateFormat: "dd/mm/yy",
            changeMonth: true,
            changeYear: true,
            minDate: "-2m",
            maxDate: "1m",
            onSelect: function(date) {
                $("#error-dispatchdate").hide();
            }
        });
        $('.ui-datepicker').addClass('notranslate');
    });
</script>