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
<?php $attributes = array('name' => 'add_reception', 'id' => 'add_reception', 'autocomplete' => 'off', 'class' => '"m-b-1'); ?>
<?php $hidden = array('_method' => $pagetype); ?>
<?php echo form_open_multipart('receptions/add', $attributes, $hidden); ?>
<div class="modal-body farm-modal">
    <input type="hidden" id="pagetype" name="pagetype" value="<?php echo $pagetype; ?>">
    <div class="row mb-3">
        <div class="col-auto ms-auto">
            <a href="<?php echo base_url() . 'assets/templates/Template_Reception_SquareBlock.xlsx'; ?>" class="btn btn-info btn-block download-template" download=""><i class="fa fa-cloud-download"></i><span style="padding-left: 10px;"><?php echo $this->lang->line('template') . ' - ' . $this->lang->line('Square Blocks'); ?></span></a>
            <a href="<?php echo base_url() . 'assets/templates/Template_Reception_RoundLogs.xlsx'; ?>" class="btn btn-info btn-block download-template" download=""><i class="fa fa-cloud-download"></i><span style="padding-left: 10px;"><?php echo $this->lang->line('template') . ' - ' . $this->lang->line('Round Logs'); ?></span></a>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-6 mb-2">
            <label for="origin_reception"><?php echo $this->lang->line('origin'); ?></label>
            <select class="form-control" name="origin_reception" id="origin_reception" data-plugin="select_erp">
                <option value="0"><?php echo $this->lang->line("select"); ?></option>
                <?php foreach ($applicable_origins as $origin) { ?>
                    <?php if ($get_contract_details[0]->origin_id == $origin->id) { ?>
                        <option value="<?php echo $origin->id; ?>" selected="selected"><?php echo $origin->origin_name; ?></option>
                    <?php } else { ?>
                        <option value="<?php echo $origin->id; ?>"><?php echo $origin->origin_name; ?></option>
                    <?php } ?>
                <?php } ?>
            </select>
            <label id="error-origin" class="error-text"><?php echo $this->lang->line('error_origin_screen'); ?></label>
        </div>

        <div class="col-md-6">
            <label for="supplier_name"><?php echo $this->lang->line('supplier_name'); ?></label>
            <select class="form-control" name="supplier_name" id="supplier_name" data-plugin="select_erp" disabled>
                <option value="0"><?php echo $this->lang->line("select"); ?></option>

                <?php if ($pagetype == "edit") { ?>
                    <?php foreach ($suppliers as $supplier) { ?>
                        <option value="<?php echo $supplier->id; ?>" <?php if ($get_contract_details[0]->supplier_id == $supplier->id) : ?> selected="selected" <?php endif; ?>><?php echo $supplier->supplier_name; ?></option>
                    <?php } ?>
                <?php } ?>
            </select>
            <label id="error-name" class="error-text"><?php echo $this->lang->line('error_select_name'); ?></label>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6 mb-2">
            <label for="product_name"><?php echo $this->lang->line('product_name'); ?></label>
            <select class="form-control" name="product_name" id="product_name" data-plugin="select_erp" disabled>
                <option value="0"><?php echo $this->lang->line("select"); ?></option>
                <?php if ($pagetype == "edit") { ?>
                    <?php foreach ($products as $product) { ?>
                        <option value="<?php echo $product->product_id; ?>" <?php if ($get_contract_details[0]->product == $product->product_id) : ?> selected="selected" <?php endif; ?>><?php echo $product->product_name; ?></option>
                    <?php } ?>
                <?php } ?>
            </select>
            <label id="error-productname" class="error-text"><?php echo $this->lang->line('error_product_name'); ?></label>
        </div>

        <div class="col-md-6">
            <label for="product_type"><?php echo $this->lang->line('product_type'); ?></label>
            <select class="form-control" name="product_type" id="product_type" data-plugin="select_erp" disabled>
                <option value="0"><?php echo $this->lang->line("select"); ?></option>
                <?php if ($pagetype == "edit") { ?>
                    <?php foreach ($product_types as $product_type) { ?>
                        <option value="<?php echo $product_type->type_id; ?>" <?php if ($get_contract_details[0]->product_type == $product_type->type_id) : ?> selected="selected" <?php endif; ?>><?php echo $this->lang->line($product_type->product_type_name); ?></option>
                    <?php } ?>
                <?php } ?>
            </select>
            <label id="error-woodtype" class="error-text"><?php echo $this->lang->line('error_selectwoodtype'); ?></label>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6 mb-2">
            <label for="inventory_order"><?php echo $this->lang->line('inventory_order'); ?></label>
            <input type="text" id="inventory_order" name="inventory_order" class="form-control text-uppercase" placeholder="<?php echo $this->lang->line('inventory_order'); ?>">
            <label id="error-inventoryorder" class="error-text"><?php echo $this->lang->line('error_inventory_order'); ?></label>
        </div>

        <div class="col-md-6">
            <label for="measurement_system"><?php echo $this->lang->line('measuremet_system'); ?></label>
            <select class="form-control" name="measurement_system" id="measurement_system" data-plugin="select_erp">
                <option value="0"><?php echo $this->lang->line("select"); ?></option>
            </select>
            <label id="error-measuremetsystem" class="error-text"><?php echo $this->lang->line('error_measuremet_system'); ?></label>
        </div>

    </div>

    <div class="row mb-3">
        <div class="col-md-6 mb-2">
            <label for="wh_name_add"><?php echo $this->lang->line('wh_name'); ?></label>
            <select class="form-control" name="wh_name_add" id="wh_name_add" data-plugin="select_erp">
                <option value="0"><?php echo $this->lang->line("select"); ?></option>
            </select>
            <label id="error-warehouse_add" class="error-text"><?php echo $this->lang->line('error_warehouse_farm'); ?></label>
        </div>

        <div class="col-md-6">
            <label for="reception_date"><?php echo $this->lang->line('received_date'); ?></label>
            <input type="text" id="reception_date" name="reception_date" class="form-control" readonly placeholder="<?php echo $this->lang->line('received_date'); ?>" />
            <label id="error-receptiondate" class="error-text"><?php echo $this->lang->line('error_date'); ?></label>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6 mb-2">
            <label for="upload_type"><?php echo $this->lang->line('upload_type'); ?></label>
            <select class="form-control" name="upload_type" id="upload_type" data-plugin="select_erp">
                <option value="0"><?php echo $this->lang->line("select"); ?></option>
                <option value="1"><?php echo $this->lang->line('pieces'); ?></option>
                <!-- <option value="2"><?php echo $this->lang->line('qr_code'); ?></option> -->
            </select>
            <label id="error-uploadtype" class="error-text"><?php echo $this->lang->line('error_reception_type'); ?></label>
        </div>
    </div>

    <div class="row mb-3" id="divReceptionUpload">
        <div class="col-md-6 mb-2">
            <label for="fileReceptionExcel"><?php echo $this->lang->line('reception_upload'); ?></label>
            <input name="fileReceptionExcel" type="file" accept=".xlsx" id="fileReceptionExcel" onchange="loadFile(event)" class="form-control">
            <label id="error-receptionupload" class="error-text"><?php echo $this->lang->line('error_reception_upload'); ?></label>
        </div>

        <div class="col-md-6" id="divUploadedDetails">
            <div class="next-line">
                <label class="label-showdetails" for="lblTotalPieces" id="lblTotalPieces"><?php echo $this->lang->line('total_pieces') . ': ---'; ?></label>
                <label class="label-showdetails" for="lblVolume" id="lblVolume"><?php echo $this->lang->line('total_volume') . ': ---'; ?></label>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <?php echo form_button(array('data-bs-dismiss' => 'modal', 'type' => 'button', 'class' => 'btn btn-secondary', 'content' => $this->lang->line('close'))); ?>
    <?php echo form_button(array('name' => 'cgrerp_form_origin', 'type' => 'submit', 'class' => 'btn btn-success add_reception', 'content' => $pagetype == 'edit' ? $this->lang->line('update') : $this->lang->line('add'))); ?>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    var error_value = "<?php echo $this->lang->line('error_value'); ?>";
    var error_zero_value = "<?php echo $this->lang->line('error_zero_value'); ?>";
    var error_inventory_order = "<?php echo $this->lang->line('error_inventory_order'); ?>";
    var error_reception_type = "<?php echo $this->lang->line('error_reception_type'); ?>";
    var error_purchase_contract = "<?php echo $this->lang->line('error_purchase_contract'); ?>";
    var total_pieces = "<?php echo $this->lang->line('total_no_of_pieces'); ?>";
    var total_volume = "<?php echo $this->lang->line('total_volume'); ?>";
    var total_pie = "<?php echo $this->lang->line('total_pie'); ?>";
    var common_error = "<?php echo $this->lang->line("common_error"); ?>";
    var error_selectwoodtype = "<?php echo $this->lang->line("error_selectwoodtype"); ?>";
    var error_origin_screen = "<?php echo $this->lang->line("error_origin_screen"); ?>";
    var error_measuremet_system = "<?php echo $this->lang->line("error_measuremet_system"); ?>";

    var reception_data_array = [];
    var total_pieces_uploaded = 0;
    var total_volume_uploaded = 0;

    $("#error-origin").hide();
    $("#error-name").hide();
    $("#error-productname").hide();
    $("#error-woodtype").hide();
    $("#error-purchasecontract").hide();
    $("#error-inventoryorder").hide();
    $("#error-truckplatenumber").hide();
    $("#error-servicecost").hide();
    $("#error-servicepayto").hide();
    $("#error-logisticcost").hide();
    $("#error-logisticpayto").hide();
    $("#error-conversionrate").hide();
    $("#error-receptionupload").hide();
    $("#error-receptiondate").hide();
    $("#error-adjustment").hide();
    $("#divReceptionDetail").hide();
    $("#divShowDetails").hide();
    $("#divConversionRate").hide();
    $("#divUploadedDetails").hide();
    $("#error-warehouse_add").hide();
    $("#error-measuremetsystem").hide();
    $("#error-uploadtype").hide();

    $(document).ready(function() {

        $("#origin_reception").change(function() {
            if ($("#origin_reception").val() == 0) {
                $("#supplier_name").attr("disabled", true);
                $("#product_name").attr("disabled", true);
                $("#product_type").attr("disabled", true);
                fetchSuppliers(0);
                fetchProducts(0, 0);
                fetchProductTypes(0, 0, 0);
                fetchWarehouses(0);
            } else {
                fetchSuppliers($("#origin_reception").val());
                $("#supplier_name").attr("disabled", false);
                $("#product_name").attr("disabled", true);
                $("#product_type").attr("disabled", true);
                fetchProducts(0, 0);
                fetchProductTypes(0, 0, 0);
                fetchWarehouses($("#origin_reception").val());
                $("#error-origin").hide();
            }
        });

        $("#supplier_name").change(function() {

            if ($("#supplier_name").val() == 0) {
                $("#product_name").attr("disabled", true);
                $("#product_type").attr("disabled", true);
                fetchProducts(0, 0);
                fetchProductTypes(0, 0, 0);
            } else {
                fetchProducts($("#origin_reception").val(), $("#supplier_name").val());
                $("#product_name").attr("disabled", false);
                $("#product_type").attr("disabled", true);
                fetchProductTypes(0, 0, 0);
                $("#error-name").hide();
            }
        });

        $("#product_name").change(function() {

            if ($("#product_name").val() == 0) {
                $("#product_type").attr("disabled", true);
                fetchProductTypes(0, 0, 0);
            } else {
                fetchProductTypes($("#origin_reception").val(), $("#supplier_name").val(), $("#product_name").val());
                $("#product_type").attr("disabled", false);
                $("#error-productname").hide();
            }
        });

        $("#product_type").change(function() {

            if ($("#product_type").val() == 0) {
                fetchMeasurementSystems(0, 0);
            } else {
                fetchMeasurementSystems($("#origin_reception").val(), $("#product_type").val());
                $("#error-woodtype").hide();
            }
        });

        $("#upload_type").change(function() {

            if ($("#upload_type").val() == 0) {} else {
                $("#error-uploadtype").hide();
            }
        });

        $("#wh_name_add").change(function() {

            if ($("#wh_name_add").val() == 0) {

            } else {
                $("#error-warehouse_add").hide();
            }
        });

        $("#measurement_system").change(function() {

            if ($("#measurement_system").val() == 0) {
                $("#divReceptionUpload *").attr("disabled", "disabled").off('click');
            } else {
                $("#divReceptionUpload *").removeAttr("disabled");
                $("#error-measuremetsystem").hide();
            }
        });

        $("#add_reception").submit(function(e) {

            e.preventDefault();
            var pagetype = $("#pagetype").val().trim();
            var origin = $("#origin_reception").val();
            var supplierid = $("#supplier_name").val();
            var suppliername = $("#supplier_name option:selected").text();
            var productid = $("#product_name").val();
            var producttypeid = $("#product_type").val();
            var inventoryorder = $("#inventory_order").val().trim();
            var measurementsystem = $("#measurement_system").val();
            var warehouseid = $("#wh_name_add").val();
            var receiveddate = $("#reception_date").val();
            var uploadtypeid = $("#upload_type").val();

            var isValid1 = true,
                isValid2 = true,
                isValid3 = true,
                isValid4 = true,
                isValid5 = true,
                isValid6 = true,
                isValid7 = true,
                isValid8 = true,
                isValid9 = true,
                isValid10 = true,
                isValid11 = true;

            if (origin == 0) {
                $("#error-origin").show();
                isValid1 = false;
            } else {
                $("#error-origin").hide();
                isValid1 = true;
            }

            if (supplierid == 0) {
                $("#error-name").show();
                isValid2 = false;
            } else {
                $("#error-name").hide();
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

            if (inventoryorder.length == 0) {
                $("#error-inventoryorder").show();
                isValid5 = false;
            } else {
                $("#error-inventoryorder").hide();
                isValid5 = true;
            }

            if (measurementsystem == 0) {
                $("#error-measuremetsystem").show();
                isValid6 = false;
            } else {
                $("#error-measuremetsystem").hide();
                isValid6 = true;
            }

            if (warehouseid == 0) {
                $("#error-warehouse_add").show();
                isValid7 = false;
            } else {
                $("#error-warehouse_add").hide();
                isValid7 = true;
            }

            if (receiveddate.length == 0) {
                $("#error-receptiondate").show();
                isValid8 = false;
            } else {
                $("#error-receptiondate").hide();
                isValid8 = true;
            }

            if (uploadtypeid == 0) {
                $("#error-uploadtype").show();
                isValid9 = false;
            } else {
                $("#error-uploadtype").hide();
                isValid9 = true;
            }

            if (reception_data_array.length == 0) {
                $("#error-receptionupload").show();
                isValid10 = false;
            } else {
                $("#error-receptionupload").hide();
                isValid10 = true;
            }

            if (isValid1 && isValid2 && isValid3 && isValid4 && isValid5 && isValid6 && isValid7 && isValid8 && isValid9 && isValid10) {

                var fd = new FormData(this);
                fd.append("is_ajax", 2);
                fd.append("form", action);
                fd.append("add_type", "reception");
                fd.append("action_type", pagetype);
                fd.append("originid", origin);
                fd.append("supplierid", supplierid);
                fd.append("suppliername", suppliername);
                fd.append("productid", productid);
                fd.append("producttypeid", producttypeid);
                fd.append("inventoryorder", inventoryorder);
                fd.append("measurementsystemid", measurementsystem);
                fd.append("warehouseid", warehouseid);
                fd.append("receiveddate", receiveddate);
                fd.append("uploadtypeid", uploadtypeid);
                fd.append("receptiondata", JSON.stringify(reception_data_array));
                fd.append("totalpiecesuploaded", total_pieces_uploaded);
                fd.append("totalvolumeuploaded", total_volume_uploaded);

                $(".add_reception").prop('disabled', true);
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
                            $('.add_reception').prop('disabled', false);
                            $('input[name="csrf_cgrerp"]').val(JSON.csrf_hash);
                        } else {
                            toastr.clear();
                            toastr.success(JSON.result);
                            $('.add_reception').prop('disabled', false);
                            $('input[name="csrf_cgrerp"]').val(JSON.csrf_hash);
                            $("#add-modal-data-bd").modal('hide');

                            $('#xin_table_receptions').DataTable().ajax.reload(null, false);
                        }
                    },
                    error: function(jqXHR, exception) {
                        toastr.clear();
                        $('.add_reception').prop('disabled', false);
                    }
                });
            }
        });
    });

    function fetchSuppliers(originid) {
        $("#loading").show();
        $.ajax({
            url: base_url + "/fetch_suppliers?originid=" + originid,
            cache: false,
            method: "GET",
            dataType: 'json',
            success: function(JSON) {
                $("#loading").hide();
                if (JSON.redirect == true) {
                    window.location.replace(login_url);
                } else if (JSON.result != '') {
                    $("#supplier_name").empty();
                    $("#supplier_name").append(JSON.result);
                }
            }
        });
    }

    function fetchProducts(originid, supplierid) {
        $("#loading").show();
        $.ajax({
            url: base_url + "/get_product_by_supplier_origin?originid=" + originid + "&supplierid=" + supplierid,
            cache: false,
            method: "GET",
            dataType: 'json',
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
            url: base_url + "/get_product_type_by_supplier?originid=" + originid + "&supplierid=" + supplierid + "&productid=" + productid,
            cache: false,
            method: "GET",
            dataType: 'json',
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
            url: base_url + "/get_warehouse_by_origin?originid=" + originid,
            cache: false,
            method: "GET",
            dataType: 'json',
            success: function(JSON) {
                $("#loading").hide();
                if (JSON.redirect == true) {
                    window.location.replace(login_url);
                } else if (JSON.result != '') {
                    $("#wh_name_add").empty();
                    $("#wh_name_add").append(JSON.result);
                }
            }
        });
    }

    function fetchMeasurementSystems(originid, producttypeid) {
        $("#loading").show();
        $.ajax({
            url: base_url + "/get_measurement_system?originid=" + originid + "&producttypeid=" + producttypeid,
            cache: false,
            method: "GET",
            dataType: 'json',
            success: function(JSON) {
                $("#loading").hide();
                if (JSON.redirect == true) {
                    window.location.replace(login_url);
                } else if (JSON.result != '') {
                    $("#measurement_system").empty();
                    $("#measurement_system").append(JSON.result);
                }
            }
        });
    }

    var loadFile = function(event) {
        $("#error-receptionupload").hide();
        var origin = $("#origin_reception").val().trim();
        var inventoryorder = $("#inventory_order").val().trim();
        var uploadtype = $("#upload_type").val();
        var producttypeid = $("#product_type").val().trim();
        var measurementsystemid = $("#measurement_system").val().trim();
        event.preventDefault();

        var isValid1 = true;
        var isValid2 = true;
        var isValid3 = true;
        var isValid4 = true;
        var isValid5 = true;
        toastr.clear();

        if (origin == 0) {
            toastr.error(error_origin_screen);
            $("#error-origin").show();
            $("#fileReceptionExcel").val("");
            isValid1 = false;
        } else {
            $("#error-origin").hide();
            isValid1 = true;
        }

        if (producttypeid == 0) {
            toastr.error(error_selectwoodtype);
            $("#error-woodtype").show();
            $("#fileReceptionExcel").val("");
            isValid2 = false;
        } else {
            $("#error-woodtype").hide();
            isValid2 = true;
        }

        if (inventoryorder.length == 0) {
            toastr.error(error_inventory_order);
            $("#error-inventoryorder").show();
            $("#fileReceptionExcel").val("");
            isValid3 = false;
        } else {
            $("#error-inventoryorder").hide();
            isValid3 = true;
        }

        if (measurementsystemid == 0) {
            toastr.error(error_measuremet_system);
            $("#error-measuremetsystem").show();
            $("#fileReceptionExcel").val("");
            isValid4 = false;
        } else {
            $("#error-measuremetsystem").hide();
            isValid4 = true;
        }

        if (uploadtype == 0) {
            toastr.error(error_reception_type);
            $("#error-uploadtype").show();
            $("#fileReceptionExcel").val("");
            isValid5 = false;
        } else {
            $("#error-uploadtype").hide();
            isValid5 = true;
        }

        if (isValid1 && isValid2 && isValid3 && isValid4 && isValid5) {
            toastr.clear();

            reception_data_array = [];
            total_pieces_uploaded = 0;
            total_volume_uploaded = 0;
            var fd = new FormData();
            var files = $('#fileReceptionExcel')[0].files[0];
            if (files != null && files != "") {
                fd.append('fileReceptionExcel', files);
                fd.append('inventoryOrder', inventoryorder);
                fd.append('originId', $("#origin_reception").val());
                fd.append('productTypeId', $("#product_type").val());
                fd.append('measurementSystemId', $("#measurement_system").val());
                fd.append('uploadType', $("#upload_type").val());

                $('#loading').show();
                $.ajax({
                    url: base_url + "/load_reception_template",
                    type: 'post',
                    data: fd,
                    contentType: false,
                    processData: false,
                    success: function(JSON) {
                        $('#loading').hide();
                        $("#fileReceptionExcel").val("");
                        deletefilesfromfolder();
                        if (JSON.redirect == true) {
                            window.location.replace(login_url);
                        } else if (JSON.result != '') {
                            toastr.clear();
                            if (JSON.result["purchaseUnit"] == "1") {
                                $("#lblVolume").text(total_pie + ": " + JSON.result["totalVolume"]);
                            } else {
                                $("#lblVolume").text(total_volume + ": " + JSON.result["totalVolume"]);
                            }

                            $("#lblTotalPieces").text(total_pieces + ": " + JSON.result["totalPieces"]);
                            reception_data_array = JSON.result["receptionData"];
                            total_pieces_uploaded = JSON.result["totalPieces"];
                            total_volume_uploaded = JSON.result["totalVolume"];
                            $("#divUploadedDetails").show();
                        } else if (JSON.error != '') {
                            toastr.clear();
                            toastr.error(JSON.error);
                            $("#divUploadedDetails").hide();
                        } else if (JSON.warning != '') {
                            toastr.clear();
                            toastr.warning(JSON.warning);
                            $("#divUploadedDetails").hide();
                        }
                    }
                });
            } else {
                toastr.clear();
                toastr.error(common_error);
                $("#fileReceptionExcel").val("");
            }
        }
    };

    function isNumberKey(txt, evt) {
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode == 46) {
            if (txt.value.indexOf('.') === -1) {
                return true;
            } else {
                return false;
            }
        } else {
            if (charCode > 31 &&
                (charCode < 48 || charCode > 57))
                return false;
        }
        return true;
    }
</script>
<script src="<?php echo base_url() . 'assets/js/i18n/datepicker-' . $wz_lang . '.js'; ?>"></script>
<script type="text/javascript">
    $(function() {
        
        $("#reception_date").datepicker({
            dateFormat: "dd/mm/yy",
            changeMonth: true,
            changeYear: true,
            minDate: '-1y',
            maxDate: '10d',
            onSelect: function(date) {
                $("#error-receptiondate").hide();
            }
        });
        $('.ui-datepicker').addClass('notranslate');
    });
</script>