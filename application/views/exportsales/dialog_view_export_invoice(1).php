<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<?php $site_lang = $this->load->helper('language'); ?>
<?php $wz_lang = $site_lang->session->userdata('site_lang'); ?>
<div class="modal-header">
    <h4 class="modal-title" id="add-modal-data"><?php echo $pageheading; ?></h4>
    <?php echo form_button(array('aria-label' => 'Close', 'data-bs-dismiss' => 'modal', 'type' => 'button', 'class' => 'close', 'content' => '<span aria-hidden="true">×</span>')); ?>

</div>
<?php $attributes = array('name' => 'update_export', 'id' => 'update_export', 'autocomplete' => 'off', 'class' => '"m-b-1'); ?>
<?php $hidden = array('_method' => $pagetype); ?>
<?php echo form_open($formsubmit, $attributes, $hidden); ?>
<div class="modal-body">
    <input type="hidden" id="pagetype" name="pagetype" value="<?php echo $pagetype; ?>">
    <input type="hidden" id="hdnExportId" name="hdnExportId" value="<?php echo $exportid; ?>">
    <input type="hidden" id="hdnSaNumber" name="hdnSaNumber" value="<?php echo $sanumber; ?>">
    <input type="hidden" id="hdnCsrf" name="hdnCsrf" value="<?php echo $csrfhash; ?>">
    <input type="hidden" id="hdnOriginId" name="hdnOriginId" value="<?php echo $originid; ?>">
    <input type="hidden" id="hdnProductTypeId" name="hdnProductTypeId" value="<?php echo $product_type_id ?>">

    <div class="row mb-2">
        <div class="col-md-3 mb-2">
            <label for="sa_number"><?php echo $this->lang->line("sa_number"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo $export_details[0]->sa_number; ?></label>
            </div>
        </div>
        <div class="col-md-3 mb-2">
            <label for="port_of_loading"><?php echo $this->lang->line("port_of_loading"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo $export_details[0]->pol_name; ?></label>
            </div>
        </div>
        <div class="col-md-3 mb-2">
            <label for="port_of_discharge"><?php echo $this->lang->line("port_of_discharge"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo $export_details[0]->pod_name; ?></label>
            </div>
        </div>
        <div class="col-md-3 mb-2">
            <label for="invoice_date"><?php echo $this->lang->line("invoice_date"); ?></label>
            <input type="text" id="invoice_date" name="invoice_date" class="form-control" readonly />
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-md-3 mb-2">
            <label for="no_of_fcls"><?php echo $this->lang->line("no_of_fcls"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo $export_details[0]->d_total_containers; ?></label>
            </div>
        </div>

        <div class="col-md-3 mb-2">
            <label for="total_no_of_pieces"><?php echo $this->lang->line("total_no_of_pieces"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo $export_details[0]->total_pieces; ?></label>
            </div>
        </div>

        <?php if ($originid == 3) { ?>

            <div class="col-md-3 mb-2">
                <label for="total_net_weight"><?php echo $this->lang->line("total_net_weight"); ?></label>
                <div class="input-group">
                    <label class="control-label"><?php echo $export_details[0]->total_net_weight + 0; ?></label>
                </div>
            </div>

        <?php } else { ?>

            <div class="col-md-3 mb-2">
                <label for="total_gross_volume"><?php echo $this->lang->line("total_gross_volume"); ?></label>
                <div class="input-group">
                    <label class="control-label"><?php echo $export_details[0]->total_gross_volume + 0; ?></label>
                </div>
            </div>

            <div class="col-md-3 mb-2">
                <label for="total_net_volume"><?php echo $this->lang->line("total_net_volume"); ?></label>
                <div class="input-group">
                    <label class="control-label"><?php echo $export_details[0]->total_net_volume + 0; ?></label>
                </div>
            </div>

        <?php } ?>
    </div>

    <?php if ($originid == 1 || $originid == 2) { ?>

        <div class="row mb-2">
            <div class="col-md-3 mb-2">
                <label for="circumference_allowance"><?php echo $this->lang->line("circumference_allowance"); ?></label>
                <input type="number" id="circumference_allowance" name="circumference_allowance" class="form-control text-uppercase" step="any" value="<?php echo $company_setting[0]->circumference_allowance_export + 0; ?>" />
            </div>
            <div class="col-md-3 mb-2">
                <label for="length_allowance"><?php echo $this->lang->line("length_allowance"); ?></label>
                <input type="number" id="length_allowance" name="length_allowance" class="form-control text-uppercase" step="any" value="<?php echo $company_setting[0]->length_allowance_export + 0; ?>" />
            </div>

            <div class="col-md-3 mb-2">
                <label for="circumference_adjustment"><?php echo $this->lang->line("circ_adjustment"); ?></label>
                <input type="number" id="circumference_adjustment" name="circumference_adjustment" class="form-control text-uppercase" />
            </div>
        <?php } ?>
        <div class="col-md-3 mb-2">
            <label for="measurement_system"><?php echo $this->lang->line("measuremet_system"); ?></label>
            <select class="form-control" name="measurement_system" id="measurement_system" data-plugin="select_erp">
                <option value="0"><?php echo $this->lang->line("select"); ?></option>
                <?php foreach ($measurementsystems as $measurementsystem) { ?>
                    <option value="<?php echo $measurementsystem->measurement_id; ?>" <?php if ($export_details[0]->measurement_system == $measurementsystem->measurement_id) { ?> selected="selected" <?php } ?>><?php echo $measurementsystem->measurement_name; ?></option>
                <?php } ?>
            </select>
        </div>
        </div>

        <?php if ($originid == 1 || $originid == 2) { ?>
            <div class="row mb-2">
                <div class="col-md-12 mb-2">
                    <label for="price_details"><u><?php echo $this->lang->line("price_details"); ?></u></label>
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-md-3 mb-2">
                    <input class="form-check-input" id="servicesales" name="servicesales" type="checkbox" value="1">
                    <label for="servicesales"><?php echo $this->lang->line('service_sales'); ?></label>

                    <input type="number" id="service_sales_percentage" name="service_sales_percentage" class="form-control text-uppercase" step="1" min="0" oninput="validity.valid||(value='');" disabled />
                </div>

                <div class="col-md-3 mb-2">
                    <input class="form-check-input" id="salesadvance" name="salesadvance" type="checkbox" value="1">
                    <label for="salesadvance"><?php echo $this->lang->line('sales_advance'); ?></label>

                    <input type="number" id="sales_advance_cost" name="sales_advance_cost" class="form-control text-uppercase" step="1" min="0" oninput="validity.valid||(value='');" disabled />
                </div>

                <div class="col-md-2 mb-2 form-check" style="display: flex; align-items: center;">
                    <input class="form-check-input" id="accountinginvoice" name="accountinginvoice" type="checkbox" value="1">
                    <label for="accountinginvoice"><?php echo $this->lang->line('accounting_invoice'); ?></label>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="credit_note"><?php echo $this->lang->line('credit_note'); ?></label>
                    <select class="form-control" name="credit_note[]" id="credit_note" data-plugin="select_erp" multiple>
                        <?php foreach ($claimtrackers as $claim) { ?>
                                <option value="<?php echo $claim->id; ?>"> <?php echo $claim->claim_amount; ?><?php ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <div class="row mb-2">
                <label for="base_price"><u><?php echo $this->lang->line("base_price"); ?></u></label>
            </div>

            <div class="row mb-2">
                <div class="col-md-3 mb-2">
                    <label for="price_shorts"><?php echo $this->lang->line("text_shorts"); ?></label>
                    <input type="number" id="price_shorts" name="price_shorts" class="form-control text-uppercase" step="any" />

                    <input class="form-check-input" id="enablejumpprice_shorts" name="enablejumpprice_shorts" type="checkbox" value="1" checked>
                    <label for="enablejumpprice_shorts"><?php echo $this->lang->line('enable_jump_price'); ?></label>
                </div>
                <div class="col-md-3 mb-2">
                    <label for="price_semi"><?php echo $this->lang->line("text_semi"); ?></label>
                    <input type="number" id="price_semi" name="price_semi" class="form-control text-uppercase" step="any" />

                    <input class="form-check-input" id="enablejumpprice_semi" name="enablejumpprice_semi" type="checkbox" value="1" checked>
                    <label for="enablejumpprice_semi"><?php echo $this->lang->line('enable_jump_price'); ?></label>
                </div>
                <div class="col-md-3 mb-2">
                    <label for="price_longs"><?php echo $this->lang->line("text_longs"); ?></label>
                    <input type="number" id="price_longs" name="price_longs" class="form-control text-uppercase" step="any" />

                    <input class="form-check-input" id="enablejumpprice_longs" name="enablejumpprice_longs" type="checkbox" value="1" checked>
                    <label for="enablejumpprice_longs"><?php echo $this->lang->line('enable_jump_price'); ?></label>
                </div>
            </div>

            <div class="row mb-4">
                <div class="tab-content">
                    <div class="tab-pane preview-tab-pane active" role="tabpanel" aria-labelledby="tab-dom-ec0fa1e3-6325-4caf-a468-7691ef065d01" id="dom-ec0fa1e3-6325-4caf-a468-7691ef065d01">
                        <div class="accordion" id="accordionExample">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading1">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="true" aria-controls="collapse1">
                                        <?php echo $this->lang->line('container_pricing'); ?>
                                    </button>
                                </h2>
                                <div class="accordion-collapse collapse" id="collapse1" aria-labelledby="heading1" data-bs-parent="#accordionExample" style="padding: 20px;">
                                    <?php $i = 0;
                                    foreach ($containernumbers as $containernumber) {
                                        $i = $i + 1;
                                        $containerNumber = $containernumber->container_number; ?>

                                        <div class="row mb-3">

                                            <label class="col-sm-2 col-form-label lbl-font header-profile-menu1 fontsize" for="lblContainerNumber" name="containerNumber[]" value="<?php echo $i; ?>">
                                                <?php echo strtoupper($containerNumber); ?>
                                            </label>

                                            <div class="col-sm-3">
                                                <input type="text" value="" class="form-control text-uppercase" id="container_number" name="container_number[<?php echo $i; ?>]" />
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>

        <div class="row mb-2">
            <div class="col-md-6 mb-2">
                <label for="buyer"><?php echo $this->lang->line("buyer_name"); ?></label>
                <select class="form-control" name="buyer_name" id="buyer_name" data-plugin="select_erp">
                    <option value="0"><?php echo $this->lang->line("select"); ?></option>
                    <?php foreach ($buyers as $buyer) { ?>
                        <option value="<?php echo $buyer->id; ?>" <?php if ($export_details[0]->invoice_buyer_id == $buyer->buyer_name) { ?> selected="selected" <?php } ?>><?php echo $buyer->buyer_name; ?></option>
                    <?php } ?>
                </select>
                <label id="error-buyername" class="error-text"><?php echo $this->lang->line("error_select_name"); ?></label>
            </div>
            <div class="col-md-6 mb-2">
                <div class="input-group">
                    <textarea name="buyer_name_details" id="buyer_name_details" maxlength="400" rows="8" class="form-control" disabled readonly></textarea>
                </div>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-md-6 mb-2">
                <label for="buyer"><?php echo $this->lang->line("bank_name"); ?></label>
                <select class="form-control" name="bank_name" id="bank_name" data-plugin="select_erp">
                    <option value="0"><?php echo $this->lang->line("select"); ?></option>
                    <?php foreach ($banks as $bank) { ?>
                        <option value="<?php echo $bank->id; ?>" <?php if ($export_details[0]->invoice_bank_id == $bank->bank_name) { ?> selected="selected" <?php } ?>><?php echo $bank->bank_name; ?></option>
                    <?php } ?>
                </select>
                <label id="error-bankname" class="error-text"><?php echo $this->lang->line("error_select_name"); ?></label>
            </div>
            <div class="col-md-6 mb-2">
                <div class="input-group">
                    <textarea name="bank_name_details" id="bank_name_details" maxlength="400" rows="8" class="form-control" disabled readonly></textarea>
                </div>
            </div>
        </div>
</div>

<div class="modal-footer">
    <?php echo form_button(array('data-bs-dismiss' => 'modal', 'type' => 'button', 'class' => 'btn btn-secondary', 'content' => $this->lang->line("close"))); ?>
    <button type="button" id="btn_generate_proforma_invoice" name="btn_generate_proforma_invoice" class="btn btn-success"><?php echo $this->lang->line("generate"); ?></button>
</div>
<?php echo form_close(); ?>
<link rel="stylesheet" href="<?php echo base_url() . 'assets/css/bootstrap-multiselect.css'; ?>">
<script src="<?php echo base_url() . 'assets/js/bootstrap-multiselect.js'; ?>"></script>

<script type="text/javascript">
    var selecttext = "<?php echo $this->lang->line("select"); ?>";
    $(document).ready(function() {

        $('select[multiple]').multiselect({
			placeholder: selecttext,
			search: true,
			selectAll: false,
		});

        $("#error-buyername").hide();
        $("#error-bankname").hide();

        $("#btn_generate_proforma_invoice").click(function() {

            var pagetype = $("#pagetype").val().trim();
            var origin = $("#hdnOriginId").val();
            var sanumber = $("#hdnSaNumber").val();
            var producttypeid = $("#hdnProductTypeId").val();
            var exportid = $("#hdnExportId").val();
            var creditnotes = $("#credit_note").val();

            var arrUpdateContainerPrice = [];

            var isValid1 = true,
                isValid2 = true,
                isValid3 = true;

            if (isValid1) {
                $("#loading").show();
                var fd = new FormData();
                fd.append("type", "generateinvoice");
                fd.append("csrf_cgrerp", $("#hdnCsrf").val());
                fd.append("originid", origin);
                fd.append("sanumber", sanumber);
                fd.append("exportid", exportid);
                fd.append("invoicedate", $("#invoice_date").val().trim());
                fd.append("creditnotes", creditnotes);

                if (origin == 3) {
                    //DO NOTHING
                } else {
                    var containerNumber = <?php echo json_encode($containernumbers); ?>;

                    var iContainer = 0;
                    $.each(containerNumber, function(i, item) {
                        iContainer = iContainer + 1;
                        var cNumber = item.container_number;
                        if (cNumber != null && cNumber != '' && cNumber != undefined) {
                            var updatedContainerPrice = $('input[name="container_number[' + iContainer + ']"]').val();

                            if (updatedContainerPrice > 0) {
                                arrUpdateContainerPrice.push({
                                    containerId: iContainer,
                                    containerNumber: cNumber,
                                    containerPrice: updatedContainerPrice,
                                });
                            }
                        }
                    });
                }
                fd.append("updatecontainerprice", JSON.stringify(arrUpdateContainerPrice));

                if ($("#buyer_name").val() == 0) {
                    isValid2 = false;
                    $("#error-buyername").show();
                } else {
                    isValid2 = true;
                    $("#error-buyername").hide();
                    fd.append("buyerid", $("#buyer_name").val());
                }

                if ($("#bank_name").val() == 0) {
                    isValid3 = false;
                    $("#error-bankname").show();
                } else {
                    isValid3 = true;
                    $("#error-bankname").hide();
                    fd.append("bankid", $("#bank_name").val());
                }

                if (isValid2 && isValid3) {

                    if (origin == 3) {

                        fd.append("circumferenceallowance", 0);
                        fd.append("lengthallowance", 0);
                        fd.append("priceshorts", 0);
                        fd.append("pricesemi", 0);
                        fd.append("pricelongs", 0);
                        fd.append("enablejump_shorts", 0);
                        fd.append("enablejump_semi", 0);
                        fd.append("enablejump_longs", 0);
                        fd.append("servicesales", 0);
                        fd.append("servicesales_percentage", 0);
                        fd.append("salesadvance", 0);
                        fd.append("salesadvance_cost", 0);
                        fd.append("accountinginvoice", 0);

                    } else {
                        if ($("#circumference_allowance").val() == null || $("#circumference_allowance").val() == "") {
                            fd.append("circumferenceallowance", 0);
                        } else {
                            fd.append("circumferenceallowance", $("#circumference_allowance").val());
                        }

                        if ($("#length_allowance").val() == null || $("#length_allowance").val() == "") {
                            fd.append("lengthallowance", 0);
                        } else {
                            fd.append("lengthallowance", $("#length_allowance").val());
                        }

                        if (origin == 1) {
                            if (exportid >= 444 && exportid <= 531) {
                                fd.append("circumferenceadjustment", 1);
                            } else {
                                if ($("#circumference_adjustment").val() == null || $("#circumference_adjustment").val() == "") {
                                    fd.append("circumferenceadjustment", 0);
                                } else {
                                    fd.append("circumferenceadjustment", $("#circumference_adjustment").val());
                                }
                            }
                        } else {
                            if ($("#circumference_adjustment").val() == null || $("#circumference_adjustment").val() == "") {
                                fd.append("circumferenceadjustment", 0);
                            } else {
                                fd.append("circumferenceadjustment", $("#circumference_adjustment").val());
                            }
                        }

                        if ($("#measurement_system").val() == null || $("#measurement_system").val() == "") {
                            fd.append("measurementsystem", 0);
                        } else {
                            fd.append("measurementsystem", $("#measurement_system").val());
                        }

                        if ($("#price_shorts").val() == null || $("#price_shorts").val() == "") {
                            fd.append("priceshorts", 0);
                        } else {
                            fd.append("priceshorts", $("#price_shorts").val());
                        }

                        if ($("#price_semi").val() == null || $("#price_semi").val() == "") {
                            fd.append("pricesemi", 0);
                        } else {
                            fd.append("pricesemi", $("#price_semi").val());
                        }

                        if ($("#price_longs").val() == null || $("#price_longs").val() == "") {
                            fd.append("pricelongs", 0);
                        } else {
                            fd.append("pricelongs", $("#price_longs").val());
                        }

                        if ($("#enablejumpprice_shorts").is(":checked") == true) {
                            fd.append("enablejump_shorts", 1);
                        } else {
                            fd.append("enablejump_shorts", 0);
                        }

                        if ($("#enablejumpprice_semi").is(":checked") == true) {
                            fd.append("enablejump_semi", 1);
                        } else {
                            fd.append("enablejump_semi", 0);
                        }

                        if ($("#enablejumpprice_longs").is(":checked") == true) {
                            fd.append("enablejump_longs", 1);
                        } else {
                            fd.append("enablejump_longs", 0);
                        }

                        if ($("#servicesales").is(":checked") == true) {
                            fd.append("servicesales", 1);
                            if ($("#service_sales_percentage").val().length == 0) {
                                fd.append("servicesales_percentage", 0);
                            } else {
                                fd.append("servicesales_percentage", $("#service_sales_percentage").val());
                            }

                        } else {
                            fd.append("servicesales", 0);
                            fd.append("servicesales_percentage", 0);
                        }

                        if ($("#salesadvance").is(":checked") == true) {
                            fd.append("salesadvance", 1);
                            if ($("#sales_advance_cost").val().length == 0) {
                                fd.append("salesadvance_cost", 0);
                            } else {
                                fd.append("salesadvance_cost", $("#sales_advance_cost").val());
                            }
                        } else {
                            fd.append("salesadvance", 1);
                            fd.append("salesadvance_cost", 0);
                        }

                        if ($("#accountinginvoice").is(":checked") == true) {
                            fd.append("accountinginvoice", 1);
                        } else {
                            fd.append("accountinginvoice", 0);
                        }
                    }

                    toastr.info(processing_request);
                    $.ajax({
                        url: base_url + "/generate_proforma_invoice",
                        type: "POST",
                        data: fd,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            $("#loading").hide();
                            if (response.redirect == true) {
                                window.location.replace(login_url);
                            } else if (response.error != '') {
                                toastr.clear();
                                toastr.error(response.error);
                                $('input[name="csrf_cgrerp"]').val(response.csrf_hash);
                            } else {
                                toastr.clear();
                                //$("#add-modal-data-xl").modal('hide');
                                toastr.success(response.successmessage);
                                $('input[name="csrf_cgrerp"]').val(response.csrf_hash);
                                window.location = response.result;
                                wait(3000);
                                deletefilesfromfolder();
                            }
                        }
                    });
                }
            }
        });

        $("#buyer_name").change(function() {
            $("#error-buyername").hide();
            if ($("#buyer_name").val() == 0) {
                $("#buyer_name_details").text("");
            } else {
                fetchBuyerDetails($("#buyer_name").val());
            }
        });

        $("#bank_name").change(function() {
            $("#error-bankname").hide();
            if ($("#bank_name").val() == 0) {
                $("#bank_name_details").text("");
            } else {
                fetchBankDetails($("#bank_name").val());
            }
        });

        $("#servicesales").change(function() {
            if ($("#servicesales").is(":checked") == true) {
                $("#service_sales_percentage").removeAttr("disabled");
            } else {
                $("#service_sales_percentage").attr("disabled", true);
                $("#service_sales_percentage").val("");
            }
        });

        $("#salesadvance").change(function() {
            if ($("#salesadvance").is(":checked") == true) {
                $("#sales_advance_cost").removeAttr("disabled");
            } else {
                $("#sales_advance_cost").attr("disabled", true);
                $("#sales_advance_cost").val("");
            }
        });
    });

    function fetchBuyerDetails(buyerid) {

        var fd = new FormData();
        fd.append("originId", $("#hdnOriginId").val());
        fd.append("csrf_cgrerp", $("#hdnCsrf").val());
        fd.append("buyerId", buyerid);

        $("#loading").show();
        $.ajax({
            url: base_url + "/fetch_buyer_details",
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
                    $("#buyer_name_details").text(JSON.result);
                } else {
                    toastr.clear();
                    toastr.error(JSON.error);
                }
            }
        });
    }

    function fetchBankDetails(bankid) {

        var fd = new FormData();
        fd.append("originId", $("#hdnOriginId").val());
        fd.append("csrf_cgrerp", $("#hdnCsrf").val());
        fd.append("bankId", bankid);

        $("#loading").show();
        $.ajax({
            url: base_url + "/fetch_bank_details",
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
                    $("#bank_name_details").text(JSON.result);
                } else {
                    toastr.clear();
                    toastr.error(JSON.error);
                }
            }
        });
    }
</script>

<script src="<?php echo base_url() . 'assets/js/i18n/datepicker-' . $wz_lang . '.js'; ?>"></script>
<script type="text/javascript">
    $(function() {
        $("#invoice_date").datepicker({
            dateFormat: "dd/mm/yy",
            changeMonth: true,
            changeYear: true,
            minDate: "-1y",
            maxDate: "1y",
            beforeShow: function(input, inst) {
                var rect = input.getBoundingClientRect();
                setTimeout(function () {
        	        inst.dpDiv.css({ top: rect.top + 40, left: rect.left + 0 });
                }, 0);
            },
            onSelect: function(date) {}
        });

        $('#invoice_date').datepicker('setDate', 'today');

        $('.ui-datepicker').addClass('notranslate');
    });
</script>