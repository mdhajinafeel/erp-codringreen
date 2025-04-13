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
    <input type="hidden" id="hdnDispatchIds" name="hdnDispatchIds" value="<?php echo $dispatchids; ?>">
    <input type="hidden" id="hdnProductTypeId" name="hdnProductTypeId" value="<?php echo $product_type_id ?>">

    <div class="row mb-2">
        <div class="col-md-3">
            <label for="sa_number"><?php echo $this->lang->line("sa_number"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo $export_details[0]->sa_number; ?></label>
            </div>
        </div>

        <div class="col-md-3">
            <label for="pod_name"><?php echo $this->lang->line("port_of_loading"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo $export_details[0]->pod_name; ?></label>
            </div>
        </div>

        <div class="col-md-3">
            <label for="pod_name"><?php echo $this->lang->line("port_of_discharge"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo $export_details[0]->pod_name; ?></label>
            </div>
        </div>

        <div class="col-md-3">
            <label for="shipping_line"><?php echo $this->lang->line("shipping_line"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo $export_details[0]->shipping_line; ?></label>
            </div>
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-md-3">
            <label for="bl_no"><?php echo $this->lang->line("bl_number"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo $export_details[0]->bl_no; ?></label>
            </div>
        </div>
        <div class="col-md-3">
            <label for="bl_date"><?php echo $this->lang->line("bl_date"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo $export_details[0]->bl_date; ?></label>
            </div>
        </div>
        <div class="col-md-2">
            <label for="shipped_date"><?php echo $this->lang->line("shipped_date"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo $export_details[0]->shipped_date; ?></label>
            </div>
        </div>
        <div class="col-md-4">
            <label for="vessel_name"><?php echo $this->lang->line("vessel_name"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo $export_details[0]->vessel_name; ?></label>
            </div>
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-md-2">
            <label for="product_type"><?php echo $this->lang->line("product_type"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo $this->lang->line($export_details[0]->product_type_name); ?></label>
            </div>
        </div>

        <div class="col-md-2">
            <label for="measuremet_system"><?php echo $this->lang->line("measuremet_system"); ?></label>
            <div class="input-group">
                <?php foreach ($measurementsystems as $measurementsystem) { ?>
                    <?php if ($export_details[0]->measurement_system == $measurementsystem->measurement_id) { ?>
                        <label class="control-label"><?php echo $measurementsystem->measurement_name; ?></label>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>

        <div class="col-md-2">
            <label for="total_containers"><?php echo $this->lang->line("total_containers"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo $export_details[0]->total_containers; ?></label>
            </div>
        </div>

        <div class="col-md-2">
            <label for="total_no_of_pieces"><?php echo $this->lang->line("total_no_of_pieces"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo $export_details[0]->total_pieces + 0; ?></label>
            </div>
        </div>

        <div class="col-md-2">
            <label for="total_gross_volume"><?php echo $this->lang->line("total_gross_volume"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo $export_details[0]->total_gross_volume + 0; ?></label>
            </div>
        </div>

        <div class="col-md-2">
            <label for="total_net_volume"><?php echo $this->lang->line("total_net_volume"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo $export_details[0]->total_net_volume + 0; ?></label>
            </div>
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-md-12">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0"><?php echo $this->lang->line("documents"); ?></h5>
                </div>
                <div class="card-body">
                    <nav>
                        <div class="nav nav-tabs mb-3 flex-nowrap overflow-auto d-flex" id="nav-tab" role="tablist" style="white-space: nowrap; overflow-x: auto;">
                            <!-- <button class="nav-link active" id="nav-matcost-tab" data-bs-toggle="tab" data-bs-target="#nav-matcost" type="button" role="tab" aria-controls="nav-matcost" aria-selected="true">
                                <?php echo $this->lang->line("doc_material_cost"); ?>
                            </button> -->
                            <button class="nav-link text-white active" id="nav-custom-tab" data-bs-toggle="tab" data-bs-target="#nav-custom" type="button" role="tab" aria-controls="nav-custom" aria-selected="true">
                                <?php echo $this->lang->line("doc_custom_agency"); ?>
                            </button>
                            <button class="nav-link text-white" id="nav-itr-tab" data-bs-toggle="tab" data-bs-target="#nav-itr" type="button" role="tab" aria-controls="nav-itr" aria-selected="false">
                                <?php echo $this->lang->line("doc_itr_transport"); ?>
                            </button>
                            <button class="nav-link text-white" id="nav-port-tab" data-bs-toggle="tab" data-bs-target="#nav-port" type="button" role="tab" aria-controls="nav-port" aria-selected="false">
                                <?php echo $this->lang->line("doc_port"); ?>
                            </button>
                            <button class="nav-link text-white" id="nav-shipping-tab" data-bs-toggle="tab" data-bs-target="#nav-shipping" type="button" role="tab" aria-controls="nav-shipping" aria-selected="false">
                                <?php echo $this->lang->line("doc_shipping"); ?>
                            </button>
                            <button class="nav-link text-white" id="nav-fumigation-tab" data-bs-toggle="tab" data-bs-target="#nav-fumigation" type="button" role="tab" aria-controls="nav-fumigation" aria-selected="false">
                                <?php echo $this->lang->line("doc_fumigation"); ?>
                            </button>
                            <button class="nav-link text-white" id="nav-coteros-tab" data-bs-toggle="tab" data-bs-target="#nav-coteros" type="button" role="tab" aria-controls="nav-coteros" aria-selected="false">
                                <?php echo $this->lang->line("doc_coteros"); ?>
                            </button>
                            <button class="nav-link text-white" id="nav-phyto-tab" data-bs-toggle="tab" data-bs-target="#nav-phyto" type="button" role="tab" aria-controls="nav-phyto" aria-selected="false">
                                <?php echo $this->lang->line("doc_phyto"); ?>
                            </button>
                            <button class="nav-link text-white" id="nav-incentives-tab" data-bs-toggle="tab" data-bs-target="#nav-incentives" type="button" role="tab" aria-controls="nav-incentives" aria-selected="false">
                                <?php echo $this->lang->line("doc_incentives"); ?>
                            </button>
                            <button class="nav-link text-white" id="nav-remobilization-tab" data-bs-toggle="tab" data-bs-target="#nav-remobilization" type="button" role="tab" aria-controls="nav-remobilization" aria-selected="false">
                                <?php echo $this->lang->line("doc_remobilization"); ?>
                            </button>
                        </div>
                    </nav>

                    <div class="tab-content" id="nav-tabContent">
                        <!-- <div class="tab-pane fade active show" id="nav-matcost" role="tabpanel" aria-labelledby="nav-matcost-tab">

                        </div> -->

                        <div class="tab-pane fade active show" id="nav-custom" role="tabpanel" aria-labelledby="nav-custom-tab">

                            <div class="row mb-3">
                                <div class="col-md-6 mb-2">
                                    <label for="fileUploadDoc"><?php echo $this->lang->line('upload_document'); ?></label>
                                    <input name="fileUploadDoc" type="file" accept=".xml,.pdf" id="fileUploadDoc" onchange="loadFileCustom(event)" class="form-control">
                                    <label id="error-selectdoc" class="error-text"><?php echo $this->lang->line('error_select_document'); ?></label>
                                </div>
                            </div>

                            <div id="divXml">

                                <div class="row mb-3">

                                    <div class="col-md-2">
                                        <label for="invoice_number"><?php echo $this->lang->line("invoice_number"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="lblInvoiceNoCustoms"></label>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="supplier_name"><?php echo $this->lang->line("supplier_name") . " - " . $this->lang->line("supplier_id"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="lblSupplierNameCustoms"></label>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label for="issued_date"><?php echo $this->lang->line("issued_date"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="lblIssuedDateCustoms"></label>
                                        </div>
                                    </div>

                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label for="export_subtotal"><?php echo $this->lang->line("export_subtotal"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="lblTotalAmountCustoms"></label>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="export_iva"><?php echo $this->lang->line("export_iva"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="lblTotalTaxAmountCustoms"></label>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="export_retefuente"><?php echo $this->lang->line("export_retefuente"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="lblAllowanceAmountCustoms"></label>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="export_total_payable"><?php echo $this->lang->line("export_total_payable"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="lblPayableAmountCustoms"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="divPdf">
                                <div class="row mb-3">
                                    <div class="col-md-2">
                                        <label for="invoice_number_custom"><?php echo $this->lang->line("invoice_number"); ?></label>
                                        <input type="text" id="invoice_number_custom" name="invoice_number_custom" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("invoice_number"); ?>" />
                                        <label id="error-invoice_no_custom" class="error-text"><?php echo $this->lang->line("error_invoice_no"); ?></label>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="supplier_name_custom"><?php echo $this->lang->line("supplier_name"); ?></label>
                                        <select class="form-control" name="supplier_name_custom" id="supplier_name_custom" data-plugin="select_erp">
                                            <option value="0"><?php echo $this->lang->line("select"); ?></option>
                                            <?php foreach ($exportSuppliersCustoms as $supplier) { ?>
                                                <option value="<?php echo $supplier->id; ?>"><?php echo $supplier->supplier_name . " - " . $supplier->supplier_id; ?></option>
                                            <?php } ?>
                                        </select>
                                        <label id="error-supplier_name_custom" class="error-text"><?php echo $this->lang->line("error_supplier"); ?></label>
                                    </div>

                                    <div class="col-md-4">
                                        <label for="issued_date_custom"><?php echo $this->lang->line("issued_date"); ?></label>
                                        <input type="datetime-local" id="issued_date_custom" name="issued_date_custom" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("issued_date"); ?>" />
                                        <input type="text" id="formatted_date_custom" class="form-control text-uppercase" placeholder="DD/MM/YYYY HH:MM AM/PM" readonly style="display: none;" />
                                        <label id="error-issued_date_custom" class="error-text"><?php echo $this->lang->line("error_issued_date"); ?></label>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label for="export_subtotal"><?php echo $this->lang->line("export_subtotal"); ?></label>
                                        <input type="number" step="any" id="subtotal_custom" name="subtotal_custom" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_subtotal"); ?>" />
                                        <label id="error-subtotal_custom" class="error-text"><?php echo $this->lang->line("error_invoice_no"); ?></label>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="export_iva"><?php echo $this->lang->line("export_iva"); ?></label>
                                        <input type="number" step="any" id="iva_custom" name="iva_custom" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_iva"); ?>" />
                                        <label id="error-iva_custom" class="error-text"><?php echo $this->lang->line("error_invoice_no"); ?></label>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="export_retefuente"><?php echo $this->lang->line("export_retefuente"); ?></label>
                                        <input type="number" step="any" id="retefuente_custom" name="retefuente_custom" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_retefuente"); ?>" />
                                        <label id="error-retefuente_custom" class="error-text"><?php echo $this->lang->line("error_invoice_no"); ?></label>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="export_total_payable"><?php echo $this->lang->line("export_total_payable"); ?></label>
                                        <input type="number" step="any" id="payable_custom" name="payable_custom" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_total_payable"); ?>" />
                                        <label id="error-payable_custom" class="error-text"><?php echo $this->lang->line("error_invoice_no"); ?></label>
                                    </div>
                                </div>

                            </div>

                            <div id="divContainersCustoms">

                                <div class="row mb-3">
                                    <label class="col-md-3 col-form-label header-profile-menu1 fontsize" for="continerNo_Custom"><?php echo $this->lang->line('container_number'); ?></label>
                                    <label class="col-md-3 col-form-label header-profile-menu1 fontsize" for="totalPieces_Custom"><?php echo $this->lang->line('total_no_of_pieces'); ?></label>
                                    <label class="col-md-3 col-form-label header-profile-menu1 fontsize" for="totalVolume_Custom"><?php echo $this->lang->line('total_volume'); ?></label>
                                    <label class="col-md-3 col-form-label header-profile-menu1 fontsize" for="containerValue_Custom"><?php echo $this->lang->line('container_value'); ?></label>
                                </div>

                                <?php foreach ($containerDetails as $containerdetail) { ?>

                                    <div class="row mb-3">

                                        <input type="hidden" id="hdnDispatchIdCustom" name="dispatchid_custom[<?php echo $containerdetail->dispatch_id; ?>]" value="<?php echo $containerdetail->dispatch_id; ?>">

                                        <label class="col-md-3 lbl-font header-profile-menu1 fontsize" for="lblContainerCustom" name="containerNumberCustom[]" value="<?php echo $containerdetail->container_number; ?>">
                                            <?php echo strtoupper($containerdetail->container_number); ?>
                                        </label>

                                        <label class="col-md-3 col-form-label lbl-font header-profile-menu1 fontsize" for="lblContainer1Custom" name="containerNumber1Custom[]" value="<?php echo $containerdetail->total_pieces; ?>">
                                            <?php echo $containerdetail->total_pieces + 0; ?>
                                        </label>

                                        <label class="col-md-3 col-form-label lbl-font header-profile-menu1 fontsize" for="lblContainer2Custom" name="containerNumber2Custom[]" value="<?php echo $containerdetail->total_volume; ?>">
                                            <?php echo sprintf("%0.3f", $containerdetail->total_volume + 0); ?>
                                        </label>

                                        <div class="col-md-3">
                                            <input type="number" step="any" value="" class="form-control text-uppercase" id="custom_container_value" name="custom_container_value[<?php echo $containerdetail->dispatch_id; ?>]" />
                                        </div>
                                    </div>
                                <?php } ?>

                                <div class="row mb-3 mt-4">
                                    <div class="d-flex justify-content-end">
                                        <button type="button" class="btn btn-primary col-md-4" id="btnSaveCustoms" name="btnSaveCustoms"><?php echo $this->lang->line('save'); ?></button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="nav-itr" role="tabpanel" aria-labelledby="nav-itr-tab">

                            <div class="row mb-3">
                                <div class="col-md-6 mb-2">
                                    <label for="fileUploadDoc_ITR"><?php echo $this->lang->line('upload_document'); ?></label>
                                    <input name="fileUploadDoc_ITR" type="file" accept=".xml,.pdf" id="fileUploadDoc_ITR" onchange="loadFileITR(event)" class="form-control">
                                    <label id="error-selectdocitr" class="error-text"><?php echo $this->lang->line('error_select_document'); ?></label>
                                </div>
                            </div>

                            <div id="divXmlITR">

                                <div class="row mb-3">

                                    <div class="col-md-2">
                                        <label for="invoice_number"><?php echo $this->lang->line("invoice_number"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="lblInvoiceNoITR"></label>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="supplier_name"><?php echo $this->lang->line("supplier_name") . " - " . $this->lang->line("supplier_id"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="lblSupplierNameITR"></label>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label for="issued_date"><?php echo $this->lang->line("issued_date"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="lblIssuedDateITR"></label>
                                        </div>
                                    </div>

                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label for="export_subtotal"><?php echo $this->lang->line("export_subtotal"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="lblTotalAmountITR"></label>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="export_iva"><?php echo $this->lang->line("export_iva"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="lblTotalTaxAmountITR"></label>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="export_retefuente"><?php echo $this->lang->line("export_retefuente"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="lblAllowanceAmountITR"></label>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="export_total_payable"><?php echo $this->lang->line("export_total_payable"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="lblPayableAmountITR"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="divPdfITR">
                                <div class="row mb-3">
                                    <div class="col-md-2">
                                        <label for="invoice_number_itr"><?php echo $this->lang->line("invoice_number"); ?></label>
                                        <input type="text" id="invoice_number_itr" name="invoice_number_itr" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("invoice_number"); ?>" />
                                        <label id="error-invoice_no_itr" class="error-text"><?php echo $this->lang->line("error_invoice_no"); ?></label>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="supplier_name_itr"><?php echo $this->lang->line("supplier_name"); ?></label>
                                        <select class="form-control" name="supplier_name_itr" id="supplier_name_itr" data-plugin="select_erp">
                                            <option value="0"><?php echo $this->lang->line("select"); ?></option>
                                            <?php foreach ($exportSuppliersItr as $supplier) { ?>
                                                <option value="<?php echo $supplier->id; ?>"><?php echo $supplier->supplier_name . " - " . $supplier->supplier_id; ?></option>
                                            <?php } ?>
                                        </select>
                                        <label id="error-supplier_name_itr" class="error-text"><?php echo $this->lang->line("error_supplier"); ?></label>
                                    </div>

                                    <div class="col-md-4">
                                        <label for="issued_date_itr"><?php echo $this->lang->line("issued_date"); ?></label>
                                        <input type="datetime-local" id="issued_date_itr" name="issued_date_itr" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("issued_date"); ?>" />
                                        <input type="text" id="formatted_date_itr" class="form-control text-uppercase" placeholder="DD/MM/YYYY HH:MM AM/PM" readonly style="display: none;" />
                                        <label id="error-issued_date_itr" class="error-text"><?php echo $this->lang->line("error_issued_date"); ?></label>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label for="export_subtotal"><?php echo $this->lang->line("export_subtotal"); ?></label>
                                        <input type="number" step="any" id="subtotal_itr" name="subtotal_itr" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_subtotal"); ?>" />
                                        <label id="error-subtotal_itr" class="error-text"><?php echo $this->lang->line("error_invoice_no"); ?></label>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="export_iva"><?php echo $this->lang->line("export_iva"); ?></label>
                                        <input type="number" step="any" id="iva_itr" name="iva_itr" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_iva"); ?>" />
                                        <label id="error-iva_itr" class="error-text"><?php echo $this->lang->line("error_invoice_no"); ?></label>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="export_retefuente"><?php echo $this->lang->line("export_retefuente"); ?></label>
                                        <input type="number" step="any" id="retefuente_itr" name="retefuente_itr" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_retefuente"); ?>" />
                                        <label id="error-retefuente_itr" class="error-text"><?php echo $this->lang->line("error_invoice_no"); ?></label>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="export_total_payable"><?php echo $this->lang->line("export_total_payable"); ?></label>
                                        <input type="number" step="any" id="payable_itr" name="payable_itr" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_total_payable"); ?>" />
                                        <label id="error-payable_itr" class="error-text"><?php echo $this->lang->line("error_invoice_no"); ?></label>
                                    </div>
                                </div>

                            </div>

                            <div id="divContainersITR">

                                <div class="row mb-3">
                                    <label class="col-md-3 col-form-label header-profile-menu1 fontsize" for="continerNo_ITR"><?php echo $this->lang->line('container_number'); ?></label>
                                    <label class="col-md-3 col-form-label header-profile-menu1 fontsize" for="totalPieces_ITR"><?php echo $this->lang->line('total_no_of_pieces'); ?></label>
                                    <label class="col-md-3 col-form-label header-profile-menu1 fontsize" for="totalVolume_ITR"><?php echo $this->lang->line('total_volume'); ?></label>
                                    <label class="col-md-3 col-form-label header-profile-menu1 fontsize" for="containerValue_ITR"><?php echo $this->lang->line('container_value'); ?></label>
                                </div>

                                <?php foreach ($containerDetails as $containerdetail) { ?>

                                    <div class="row mb-3">

                                        <input type="hidden" id="hdnDispatchIdITR" name="dispatchid_itr[<?php echo $containerdetail->dispatch_id; ?>]" value="<?php echo $containerdetail->dispatch_id; ?>">

                                        <label class="col-md-3 lbl-font header-profile-menu1 fontsize" for="lblContainerITR" name="containerNumberITR[]" value="<?php echo $containerdetail->container_number; ?>">
                                            <?php echo strtoupper($containerdetail->container_number); ?>
                                        </label>

                                        <label class="col-md-3 col-form-label lbl-font header-profile-menu1 fontsize" for="lblContainer1ITR" name="containerNumber1ITR[]" value="<?php echo $containerdetail->total_pieces; ?>">
                                            <?php echo $containerdetail->total_pieces + 0; ?>
                                        </label>

                                        <label class="col-md-3 col-form-label lbl-font header-profile-menu1 fontsize" for="lblContainer2ITR" name="containerNumber2ITR[]" value="<?php echo $containerdetail->total_volume; ?>">
                                            <?php echo sprintf("%0.3f", $containerdetail->total_volume + 0); ?>
                                        </label>

                                        <div class="col-md-3">
                                            <input type="number" step="any" value="" class="form-control text-uppercase" id="itr_container_value" name="itr_container_value[<?php echo $containerdetail->dispatch_id; ?>]" />
                                        </div>
                                    </div>
                                <?php } ?>

                                <div class="row mb-3 mt-4">
                                    <div class="d-flex justify-content-end">
                                        <button type="button" class="btn btn-primary col-md-4" id="btnSaveITR" name="btnSaveITR"><?php echo $this->lang->line('save'); ?></button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="nav-port" role="tabpanel" aria-labelledby="nav-port-tab">
                            <div class="row mb-3">
                                <div class="col-md-6 mb-2">
                                    <label for="fileUploadDoc_Port"><?php echo $this->lang->line('upload_document'); ?></label>
                                    <input name="fileUploadDoc_Port" type="file" accept=".xml,.pdf" id="fileUploadDoc_Port" onchange="loadFilePort(event)" class="form-control">
                                    <label id="error-selectdocport" class="error-text"><?php echo $this->lang->line('error_select_document'); ?></label>
                                </div>
                            </div>

                            <div id="divXmlPort">

                                <div class="row mb-3">

                                    <div class="col-md-2">
                                        <label for="invoice_number"><?php echo $this->lang->line("invoice_number"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="lblInvoiceNoPort"></label>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="supplier_name"><?php echo $this->lang->line("supplier_name") . " - " . $this->lang->line("supplier_id"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="lblSupplierNamePort"></label>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label for="issued_date"><?php echo $this->lang->line("issued_date"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="lblIssuedDatePort"></label>
                                        </div>
                                    </div>

                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label for="export_subtotal"><?php echo $this->lang->line("export_subtotal"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="lblTotalAmountPort"></label>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="export_iva"><?php echo $this->lang->line("export_iva"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="lblTotalTaxAmountPort"></label>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="export_retefuente"><?php echo $this->lang->line("export_retefuente"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="lblAllowanceAmountPort"></label>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="export_total_payable"><?php echo $this->lang->line("export_total_payable"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="lblPayableAmountPort"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="divPdfPort">
                                <div class="row mb-3">
                                    <div class="col-md-2">
                                        <label for="invoice_number_port"><?php echo $this->lang->line("invoice_number"); ?></label>
                                        <input type="text" id="invoice_number_port" name="invoice_number_port" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("invoice_number"); ?>" />
                                        <label id="error-invoice_no_port" class="error-text"><?php echo $this->lang->line("error_invoice_no"); ?></label>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="supplier_name_port"><?php echo $this->lang->line("supplier_name"); ?></label>
                                        <select class="form-control" name="supplier_name_port" id="supplier_name_port" data-plugin="select_erp">
                                            <option value="0"><?php echo $this->lang->line("select"); ?></option>
                                            <?php foreach ($exportSuppliersPort as $supplier) { ?>
                                                <option value="<?php echo $supplier->id; ?>"><?php echo $supplier->supplier_name . " - " . $supplier->supplier_id; ?></option>
                                            <?php } ?>
                                        </select>
                                        <label id="error-supplier_name_port" class="error-text"><?php echo $this->lang->line("error_supplier"); ?></label>
                                    </div>

                                    <div class="col-md-4">
                                        <label for="issued_date_port"><?php echo $this->lang->line("issued_date"); ?></label>
                                        <input type="datetime-local" id="issued_date_port" name="issued_date_port" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("issued_date"); ?>" />
                                        <input type="text" id="formatted_date_port" class="form-control text-uppercase" placeholder="DD/MM/YYYY HH:MM AM/PM" readonly style="display: none;" />
                                        <label id="error-issued_date_port" class="error-text"><?php echo $this->lang->line("error_issued_date"); ?></label>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label for="export_subtotal"><?php echo $this->lang->line("export_subtotal"); ?></label>
                                        <input type="number" step="any" id="subtotal_port" name="subtotal_port" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_subtotal"); ?>" />
                                        <label id="error-subtotal_port" class="error-text"><?php echo $this->lang->line("error_invoice_no"); ?></label>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="export_iva"><?php echo $this->lang->line("export_iva"); ?></label>
                                        <input type="number" step="any" id="iva_port" name="iva_port" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_iva"); ?>" />
                                        <label id="error-iva_port" class="error-text"><?php echo $this->lang->line("error_invoice_no"); ?></label>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="export_retefuente"><?php echo $this->lang->line("export_retefuente"); ?></label>
                                        <input type="number" step="any" id="retefuente_port" name="retefuente_port" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_retefuente"); ?>" />
                                        <label id="error-retefuente_port" class="error-text"><?php echo $this->lang->line("error_invoice_no"); ?></label>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="export_total_payable"><?php echo $this->lang->line("export_total_payable"); ?></label>
                                        <input type="number" step="any" id="payable_port" name="payable_port" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_total_payable"); ?>" />
                                        <label id="error-payable_port" class="error-text"><?php echo $this->lang->line("error_invoice_no"); ?></label>
                                    </div>
                                </div>

                            </div>

                            <div id="divContainersPort">

                                <div class="row mb-3">
                                    <label class="col-md-3 col-form-label header-profile-menu1 fontsize" for="continerNo_Port"><?php echo $this->lang->line('container_number'); ?></label>
                                    <label class="col-md-3 col-form-label header-profile-menu1 fontsize" for="totalPieces_Port"><?php echo $this->lang->line('total_no_of_pieces'); ?></label>
                                    <label class="col-md-3 col-form-label header-profile-menu1 fontsize" for="totalVolume_Port"><?php echo $this->lang->line('total_volume'); ?></label>
                                    <label class="col-md-3 col-form-label header-profile-menu1 fontsize" for="containerValue_Port"><?php echo $this->lang->line('container_value'); ?></label>
                                </div>

                                <?php foreach ($containerDetails as $containerdetail) { ?>

                                    <div class="row mb-3">

                                        <input type="hidden" id="hdnDispatchIdPort" name="dispatchid_port[<?php echo $containerdetail->dispatch_id; ?>]" value="<?php echo $containerdetail->dispatch_id; ?>">

                                        <label class="col-md-3 lbl-font header-profile-menu1 fontsize" for="lblContainerPort" name="containerNumberPort[]" value="<?php echo $containerdetail->container_number; ?>">
                                            <?php echo strtoupper($containerdetail->container_number); ?>
                                        </label>

                                        <label class="col-md-3 col-form-label lbl-font header-profile-menu1 fontsize" for="lblContainer1Port" name="containerNumber1Port[]" value="<?php echo $containerdetail->total_pieces; ?>">
                                            <?php echo $containerdetail->total_pieces + 0; ?>
                                        </label>

                                        <label class="col-md-3 col-form-label lbl-font header-profile-menu1 fontsize" for="lblContainer2Port" name="containerNumber2Port[]" value="<?php echo $containerdetail->total_volume; ?>">
                                            <?php echo sprintf("%0.3f", $containerdetail->total_volume + 0); ?>
                                        </label>

                                        <div class="col-md-3">
                                            <input type="number" step="any" value="" class="form-control text-uppercase" id="port_container_value" name="port_container_value[<?php echo $containerdetail->dispatch_id; ?>]" />
                                        </div>
                                    </div>
                                <?php } ?>

                                <div class="row mb-3 mt-4">
                                    <div class="d-flex justify-content-end">
                                        <button type="button" class="btn btn-primary col-md-4" id="btnSavePort" name="btnSavePort"><?php echo $this->lang->line('save'); ?></button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="nav-shipping" role="tabpanel" aria-labelledby="nav-shipping-tab">

                            <div class="row mb-3">
                                <div class="col-md-6 mb-2">
                                    <label for="fileUploadDoc_Shipping"><?php echo $this->lang->line('upload_document'); ?></label>
                                    <input name="fileUploadDoc_Shipping" type="file" accept=".xml,.pdf" id="fileUploadDoc_Shipping" onchange="loadFileShipping(event)" class="form-control">
                                    <label id="error-selectdocshipping" class="error-text"><?php echo $this->lang->line('error_select_document'); ?></label>
                                </div>
                            </div>

                            <div id="divXmlShipping">

                                <div class="row mb-3">

                                    <div class="col-md-2">
                                        <label for="invoice_number"><?php echo $this->lang->line("invoice_number"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="lblInvoiceNoShipping"></label>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="supplier_name"><?php echo $this->lang->line("supplier_name") . " - " . $this->lang->line("supplier_id"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="lblSupplierNameShipping"></label>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label for="issued_date"><?php echo $this->lang->line("issued_date"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="lblIssuedDateShipping"></label>
                                        </div>
                                    </div>

                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label for="export_subtotal"><?php echo $this->lang->line("export_subtotal"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="lblTotalAmountShipping"></label>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="export_iva"><?php echo $this->lang->line("export_iva"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="lblTotalTaxAmountShipping"></label>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="export_retefuente"><?php echo $this->lang->line("export_retefuente"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="lblAllowanceAmountShipping"></label>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="export_total_payable"><?php echo $this->lang->line("export_total_payable"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="lblPayableAmountShipping"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="divPdfShipping">
                                <div class="row mb-3">
                                    <div class="col-md-2">
                                        <label for="invoice_number_shipping"><?php echo $this->lang->line("invoice_number"); ?></label>
                                        <input type="text" id="invoice_number_shipping" name="invoice_number_shipping" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("invoice_number"); ?>" />
                                        <label id="error-invoice_no_shipping" class="error-text"><?php echo $this->lang->line("error_invoice_no"); ?></label>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="supplier_name_shipping"><?php echo $this->lang->line("supplier_name"); ?></label>
                                        <select class="form-control" name="supplier_name_shipping" id="supplier_name_shipping" data-plugin="select_erp">
                                            <option value="0"><?php echo $this->lang->line("select"); ?></option>
                                            <?php foreach ($exportSuppliersShipping as $supplier) { ?>
                                                <option value="<?php echo $supplier->id; ?>"><?php echo $supplier->supplier_name . " - " . $supplier->supplier_id; ?></option>
                                            <?php } ?>
                                        </select>
                                        <label id="error-supplier_name_shipping" class="error-text"><?php echo $this->lang->line("error_supplier"); ?></label>
                                    </div>

                                    <div class="col-md-4">
                                        <label for="issued_date_shipping"><?php echo $this->lang->line("issued_date"); ?></label>
                                        <input type="datetime-local" id="issued_date_shipping" name="issued_date_shipping" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("issued_date"); ?>" />
                                        <input type="text" id="formatted_date_shipping" class="form-control text-uppercase" placeholder="DD/MM/YYYY HH:MM AM/PM" readonly style="display: none;" />
                                        <label id="error-issued_date_shipping" class="error-text"><?php echo $this->lang->line("error_issued_date"); ?></label>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label for="export_subtotal"><?php echo $this->lang->line("export_subtotal"); ?></label>
                                        <input type="number" step="any" id="subtotal_shipping" name="subtotal_shipping" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("exshipping_subtotal"); ?>" />
                                        <label id="error-subtotal_shipping" class="error-text"><?php echo $this->lang->line("error_invoice_no"); ?></label>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="export_iva"><?php echo $this->lang->line("export_iva"); ?></label>
                                        <input type="number" step="any" id="iva_shipping" name="iva_shipping" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("exshipping_iva"); ?>" />
                                        <label id="error-iva_shipping" class="error-text"><?php echo $this->lang->line("error_invoice_no"); ?></label>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="export_retefuente"><?php echo $this->lang->line("export_retefuente"); ?></label>
                                        <input type="number" step="any" id="retefuente_shipping" name="retefuente_shipping" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("exshipping_retefuente"); ?>" />
                                        <label id="error-retefuente_shipping" class="error-text"><?php echo $this->lang->line("error_invoice_no"); ?></label>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="export_total_payable"><?php echo $this->lang->line("export_total_payable"); ?></label>
                                        <input type="number" step="any" id="payable_shipping" name="payable_shipping" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("exshipping_total_payable"); ?>" />
                                        <label id="error-payable_shipping" class="error-text"><?php echo $this->lang->line("error_invoice_no"); ?></label>
                                    </div>
                                </div>

                            </div>

                            <div id="divContainersShipping">

                                <div class="row mb-3">
                                    <label class="col-md-3 col-form-label header-profile-menu1 fontsize" for="continerNo_Shipping"><?php echo $this->lang->line('container_number'); ?></label>
                                    <label class="col-md-3 col-form-label header-profile-menu1 fontsize" for="totalPieces_Shipping"><?php echo $this->lang->line('total_no_of_pieces'); ?></label>
                                    <label class="col-md-3 col-form-label header-profile-menu1 fontsize" for="totalVolume_Shipping"><?php echo $this->lang->line('total_volume'); ?></label>
                                    <label class="col-md-3 col-form-label header-profile-menu1 fontsize" for="containerValue_Shipping"><?php echo $this->lang->line('container_value'); ?></label>
                                </div>

                                <?php foreach ($containerDetails as $containerdetail) { ?>

                                    <div class="row mb-3">

                                        <input type="hidden" id="hdnDispatchIdShipping" name="dispatchid_shipping[<?php echo $containerdetail->dispatch_id; ?>]" value="<?php echo $containerdetail->dispatch_id; ?>">

                                        <label class="col-md-3 lbl-font header-profile-menu1 fontsize" for="lblContainerShipping" name="containerNumberShipping[]" value="<?php echo $containerdetail->container_number; ?>">
                                            <?php echo strtoupper($containerdetail->container_number); ?>
                                        </label>

                                        <label class="col-md-3 col-form-label lbl-font header-profile-menu1 fontsize" for="lblContainer1Shipping" name="containerNumber1Shipping[]" value="<?php echo $containerdetail->total_pieces; ?>">
                                            <?php echo $containerdetail->total_pieces + 0; ?>
                                        </label>

                                        <label class="col-md-3 col-form-label lbl-font header-profile-menu1 fontsize" for="lblContainer2Shipping" name="containerNumber2Shipping[]" value="<?php echo $containerdetail->total_volume; ?>">
                                            <?php echo sprintf("%0.3f", $containerdetail->total_volume + 0); ?>
                                        </label>

                                        <div class="col-md-3">
                                            <input type="number" step="any" value="" class="form-control text-uppercase" id="shipping_container_value" name="shipping_container_value[<?php echo $containerdetail->dispatch_id; ?>]" />
                                        </div>
                                    </div>
                                <?php } ?>

                                <div class="row mb-3 mt-4">
                                    <div class="d-flex justify-content-end">
                                        <button type="button" class="btn btn-primary col-md-4" id="btnSaveShipping" name="btnSaveShipping"><?php echo $this->lang->line('save'); ?></button>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="tab-pane fade" id="nav-fumigation" role="tabpanel" aria-labelledby="nav-fumigation-tab">
                            <div class="row mb-3">
                                <div class="col-md-6 mb-2">
                                    <label for="fileUploadDoc_Fumigation"><?php echo $this->lang->line('upload_document'); ?></label>
                                    <input name="fileUploadDoc_Fumigation" type="file" accept=".xml,.pdf" id="fileUploadDoc_Fumigation" onchange="loadFileFumigation(event)" class="form-control">
                                    <label id="error-selectdocfumigation" class="error-text"><?php echo $this->lang->line('error_select_document'); ?></label>
                                </div>
                            </div>

                            <div id="divXmlFumigation">

                                <div class="row mb-3">

                                    <div class="col-md-2">
                                        <label for="invoice_number"><?php echo $this->lang->line("invoice_number"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="lblInvoiceNoFumigation"></label>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="supplier_name"><?php echo $this->lang->line("supplier_name") . " - " . $this->lang->line("supplier_id"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="lblSupplierNameFumigation"></label>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label for="issued_date"><?php echo $this->lang->line("issued_date"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="lblIssuedDateFumigation"></label>
                                        </div>
                                    </div>

                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label for="export_subtotal"><?php echo $this->lang->line("export_subtotal"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="lblTotalAmountFumigation"></label>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="export_iva"><?php echo $this->lang->line("export_iva"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="lblTotalTaxAmountFumigation"></label>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="export_retefuente"><?php echo $this->lang->line("export_retefuente"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="lblAllowanceAmountFumigation"></label>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="export_total_payable"><?php echo $this->lang->line("export_total_payable"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="lblPayableAmountFumigation"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="divPdfFumigation">
                                <div class="row mb-3">
                                    <div class="col-md-2">
                                        <label for="invoice_number_fumigation"><?php echo $this->lang->line("invoice_number"); ?></label>
                                        <input type="text" id="invoice_number_fumigation" name="invoice_number_fumigation" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("invoice_number"); ?>" />
                                        <label id="error-invoice_no_fumigation" class="error-text"><?php echo $this->lang->line("error_invoice_no"); ?></label>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="supplier_name_fumigation"><?php echo $this->lang->line("supplier_name"); ?></label>
                                        <select class="form-control" name="supplier_name_fumigation" id="supplier_name_fumigation" data-plugin="select_erp">
                                            <option value="0"><?php echo $this->lang->line("select"); ?></option>
                                            <?php foreach ($exportSuppliersFumigation as $supplier) { ?>
                                                <option value="<?php echo $supplier->id; ?>"><?php echo $supplier->supplier_name . " - " . $supplier->supplier_id; ?></option>
                                            <?php } ?>
                                        </select>
                                        <label id="error-supplier_name_fumigation" class="error-text"><?php echo $this->lang->line("error_supplier"); ?></label>
                                    </div>

                                    <div class="col-md-4">
                                        <label for="issued_date_fumigation"><?php echo $this->lang->line("issued_date"); ?></label>
                                        <input type="datetime-local" id="issued_date_fumigation" name="issued_date_fumigation" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("issued_date"); ?>" />
                                        <input type="text" id="formatted_date_fumigation" class="form-control text-uppercase" placeholder="DD/MM/YYYY HH:MM AM/PM" readonly style="display: none;" />
                                        <label id="error-issued_date_fumigation" class="error-text"><?php echo $this->lang->line("error_issued_date"); ?></label>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label for="export_subtotal"><?php echo $this->lang->line("export_subtotal"); ?></label>
                                        <input type="number" step="any" id="subtotal_fumigation" name="subtotal_fumigation" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("exfumigation_subtotal"); ?>" />
                                        <label id="error-subtotal_fumigation" class="error-text"><?php echo $this->lang->line("error_invoice_no"); ?></label>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="export_iva"><?php echo $this->lang->line("export_iva"); ?></label>
                                        <input type="number" step="any" id="iva_fumigation" name="iva_fumigation" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("exfumigation_iva"); ?>" />
                                        <label id="error-iva_fumigation" class="error-text"><?php echo $this->lang->line("error_invoice_no"); ?></label>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="export_retefuente"><?php echo $this->lang->line("export_retefuente"); ?></label>
                                        <input type="number" step="any" id="retefuente_fumigation" name="retefuente_fumigation" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("exfumigation_retefuente"); ?>" />
                                        <label id="error-retefuente_fumigation" class="error-text"><?php echo $this->lang->line("error_invoice_no"); ?></label>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="export_total_payable"><?php echo $this->lang->line("export_total_payable"); ?></label>
                                        <input type="number" step="any" id="payable_fumigation" name="payable_fumigation" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("exfumigation_total_payable"); ?>" />
                                        <label id="error-payable_fumigation" class="error-text"><?php echo $this->lang->line("error_invoice_no"); ?></label>
                                    </div>
                                </div>

                            </div>

                            <div id="divContainersFumigation">

                                <div class="row mb-3">
                                    <label class="col-md-3 col-form-label header-profile-menu1 fontsize" for="continerNo_Fumigation"><?php echo $this->lang->line('container_number'); ?></label>
                                    <label class="col-md-3 col-form-label header-profile-menu1 fontsize" for="totalPieces_Fumigation"><?php echo $this->lang->line('total_no_of_pieces'); ?></label>
                                    <label class="col-md-3 col-form-label header-profile-menu1 fontsize" for="totalVolume_Fumigation"><?php echo $this->lang->line('total_volume'); ?></label>
                                    <label class="col-md-3 col-form-label header-profile-menu1 fontsize" for="containerValue_Fumigation"><?php echo $this->lang->line('container_value'); ?></label>
                                </div>

                                <?php foreach ($containerDetails as $containerdetail) { ?>

                                    <div class="row mb-3">

                                        <input type="hidden" id="hdnDispatchIdFumigation" name="dispatchid_fumigation[<?php echo $containerdetail->dispatch_id; ?>]" value="<?php echo $containerdetail->dispatch_id; ?>">

                                        <label class="col-md-3 lbl-font header-profile-menu1 fontsize" for="lblContainerFumigation" name="containerNumberFumigation[]" value="<?php echo $containerdetail->container_number; ?>">
                                            <?php echo strtoupper($containerdetail->container_number); ?>
                                        </label>

                                        <label class="col-md-3 col-form-label lbl-font header-profile-menu1 fontsize" for="lblContainer1Fumigation" name="containerNumber1Fumigation[]" value="<?php echo $containerdetail->total_pieces; ?>">
                                            <?php echo $containerdetail->total_pieces + 0; ?>
                                        </label>

                                        <label class="col-md-3 col-form-label lbl-font header-profile-menu1 fontsize" for="lblContainer2Fumigation" name="containerNumber2Fumigation[]" value="<?php echo $containerdetail->total_volume; ?>">
                                            <?php echo sprintf("%0.3f", $containerdetail->total_volume + 0); ?>
                                        </label>

                                        <div class="col-md-3">
                                            <input type="number" step="any" value="" class="form-control text-uppercase" id="fumigation_container_value" name="fumigation_container_value[<?php echo $containerdetail->dispatch_id; ?>]" />
                                        </div>
                                    </div>
                                <?php } ?>

                                <div class="row mb-3 mt-4">
                                    <div class="d-flex justify-content-end">
                                        <button type="button" class="btn btn-primary col-md-4" id="btnSaveFumigation" name="btnSaveFumigation"><?php echo $this->lang->line('save'); ?></button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="nav-coteros" role="tabpanel" aria-labelledby="nav-coteros-tab">
                            <p><strong>This is some placeholder content the Contact tab's associated content.</strong>
                                Clicking another tab will toggle the visibility of this one for the next.
                                The tab JavaScript swaps classes to control the content visibility and styling. You can use it with
                                tabs, pills, and any other <code>.nav</code>-powered navigation.</p>
                        </div>

                        <div class="tab-pane fade" id="nav-phyto" role="tabpanel" aria-labelledby="nav-phyto-tab">
                            <p><strong>This is some placeholder content the Home tab's associated content.</strong>
                                Clicking another tab will toggle the visibility of this one for the next. The tab JavaScript swaps
                                classes to control the content visibility and styling. You can use it with tabs, pills, and any
                                other <code>.nav</code>-powered navigation.</p>
                        </div>

                        <div class="tab-pane fade" id="nav-incentives" role="tabpanel" aria-labelledby="nav-incentives-tab">
                            <p><strong>This is some placeholder content the Profile tab's associated content.</strong>
                                Clicking another tab will toggle the visibility of this one for the next.
                                The tab JavaScript swaps classes to control the content visibility and styling. You can use it with
                                tabs, pills, and any other <code>.nav</code>-powered navigation.</p>
                        </div>

                        <div class="tab-pane fade" id="nav-remobilization" role="tabpanel" aria-labelledby="nav-remobilization-tab">
                            <p><strong>This is some placeholder content the Contact tab's associated content.</strong>
                                Clicking another tab will toggle the visibility of this one for the next.
                                The tab JavaScript swaps classes to control the content visibility and styling. You can use it with
                                tabs, pills, and any other <code>.nav</code>-powered navigation.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <?php echo form_button(array('data-bs-dismiss' => 'modal', 'type' => 'button', 'class' => 'btn btn-secondary', 'content' => $this->lang->line("close"))); ?>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    var common_error = "<?php echo $this->lang->line("common_error"); ?>";
    var totalPayableAmount = 0;
    var uploadPdfFileCustomAgency = "";
    var fileExtension = "";
    var supplierId = 0;
    var subTotalCustoms = 0;
    var ivaCustoms = 0;
    var retefuenteCustoms = 0;
    var payableCustoms = 0;

    $(document).ready(function() {

        $("#error-selectdoc").hide();
        $("#error-invoice_no_custom").hide();
        $("#error-supplier_name_custom").hide();
        $("#error-supplier_id_custom").hide();
        $("#error-issued_date_custom").hide();
        $("#error-subtotal_custom").hide();
        $("#error-iva_custom").hide();
        $("#error-retefuente_custom").hide();
        $("#error-payable_custom").hide();
        $("#divXml").hide();
        $("#divPdf").hide();
        $("#divContainersCustoms").hide();

        $("#error-selectdocitr").hide();
        $("#error-invoice_no_itr").hide();
        $("#error-supplier_name_itr").hide();
        $("#error-supplier_id_itr").hide();
        $("#error-issued_date_itr").hide();
        $("#error-subtotal_itr").hide();
        $("#error-iva_itr").hide();
        $("#error-retefuente_itr").hide();
        $("#error-payable_itr").hide();
        $("#divXmlITR").hide();
        $("#divPdfITR").hide();
        $("#divContainersITR").hide();

        $("#error-selectdocport").hide();
        $("#error-invoice_no_port").hide();
        $("#error-supplier_name_port").hide();
        $("#error-supplier_id_port").hide();
        $("#error-issued_date_port").hide();
        $("#error-subtotal_port").hide();
        $("#error-iva_port").hide();
        $("#error-retefuente_port").hide();
        $("#error-payable_port").hide();
        $("#divXmlPort").hide();
        $("#divPdfPort").hide();
        $("#divContainersPort").hide();

        $("#error-selectdocshipping").hide();
        $("#error-invoice_no_shipping").hide();
        $("#error-supplier_name_shipping").hide();
        $("#error-supplier_id_shipping").hide();
        $("#error-issued_date_shipping").hide();
        $("#error-subtotal_shipping").hide();
        $("#error-iva_shipping").hide();
        $("#error-retefuente_shipping").hide();
        $("#error-payable_shipping").hide();
        $("#divXmlShipping").hide();
        $("#divPdfShipping").hide();
        $("#divContainersShipping").hide();

        $("#error-selectdocfumigation").hide();
        $("#error-invoice_no_fumigation").hide();
        $("#error-supplier_name_fumigation").hide();
        $("#error-supplier_id_fumigation").hide();
        $("#error-issued_date_fumigation").hide();
        $("#error-subtotal_fumigation").hide();
        $("#error-iva_fumigation").hide();
        $("#error-retefuente_fumigation").hide();
        $("#error-payable_fumigation").hide();
        $("#divXmlFumigation").hide();
        $("#divPdfFumigation").hide();
        $("#divContainersFumigation").hide();

        $("#btnSaveCustoms").click(function() {

            var arrUpdateContainerValue = [];
            var containerData = <?php echo json_encode($containerDetails); ?>;
            var totalContainerValue = 0;

            $.each(containerData, function(i, item) {

                var mappingId = item.dispatch_id;
                var containerNumber = item.container_number;
                var isValid = true;

                if (mappingId != null && mappingId != '' && mappingId != undefined && mappingId > 0) {

                    var updatedContainerValue = parseFloat($('input[name="custom_container_value[' + mappingId + ']"]').val()) || 0;
                    arrUpdateContainerValue.push({
                        mappingid: mappingId,
                        containerNumber: containerNumber,
                        updatedContainerValue: updatedContainerValue
                    });

                    totalContainerValue += updatedContainerValue;
                }
            });

            if (arrUpdateContainerValue.length > 0) {

                console.log(totalContainerValue);

                if (fileExtension == "pdf" || fileExtension == "PDF") {
                    totalPayableAmount = parseFloat($("#payable_custom").val()) || 0;
                }

                if (totalContainerValue != totalPayableAmount) {
                    toastr.clear();
                    toastr.error("Total Container Value should be equal to Total Payable Amount");
                    return false;
                }

                var fd = new FormData();
                fd.append("exportId", $("#hdnExportId").val());
                fd.append("originId", $("#hdnOriginId").val());
                fd.append('saNumber', $("#hdnSaNumber").val());
                fd.append("fileExtension", fileExtension);
                fd.append("updateContainerValueData_Custom", JSON.stringify(arrUpdateContainerValue));
                if (fileExtension == "xml" || fileExtension == "XML") {

                    fd.append('invoiceNo_Custom', $("#lblInvoiceNoCustoms").text());
                    fd.append('supplierName_Custom', supplierId);
                    fd.append('formattedDate_Custom', $("#lblIssuedDateCustoms").text());
                    fd.append('subTotal_Custom', subTotalCustoms);
                    fd.append('iva_Custom', ivaCustoms);
                    fd.append('retefuente_Custom', retefuenteCustoms);
                    fd.append('payable_Custom', payableCustoms);
                } else {
                    fd.append("uploadPdfFileCustomAgency", uploadPdfFileCustomAgency);
                    fd.append('invoiceNo_Custom', $("#invoice_number_custom").val());
                    fd.append('supplierName_Custom', $("#supplier_name_custom").val());
                    fd.append('formattedDate_Custom', $("#formatted_date_custom").val());
                    fd.append('subTotal_Custom', $("#subtotal_custom").val());
                    fd.append('iva_Custom', $("#iva_custom").val());
                    fd.append('retefuente_Custom', $("#retefuente_custom").val());
                    fd.append('payable_Custom', $("#payable_custom").val());
                }

                fd.append("csrf_cgrerp", $("#hdnCsrf").val());
                fd.append("add_type", 1);

                $.ajax({
                    url: base_url + "/save_export_documents",
                    type: 'post',
                    data: fd,
                    contentType: false,
                    processData: false,
                    success: function(jsonResult) {
                        $('#loading').hide();

                        if (jsonResult.redirect == true) {
                            window.location.replace(login_url);
                        } else if (jsonResult.result != '') {
                            toastr.clear();


                        } else if (jsonResult.error != '') {
                            toastr.clear();
                            toastr.error(jsonResult.error);
                            $('input[name="csrf_cgrerp"]').val(jsonResult.csrf_hash);
                        } else if (jsonResult.warning != '') {
                            toastr.clear();
                            toastr.warning(jsonResult.warning);
                            $('input[name="csrf_cgrerp"]').val(jsonResult.csrf_hash);
                        } else {
                            toastr.clear();
                        }
                    }
                });
            }
        });

        $("#btnSaveITR").click(function() {

            var arrUpdateContainerValue = [];
            var containerData = <?php echo json_encode($containerDetails); ?>;
            var totalContainerValue = 0;

            $.each(containerData, function(i, item) {

                var mappingId = item.dispatch_id;
                var containerNumber = item.container_number;
                var isValid = true;

                if (mappingId != null && mappingId != '' && mappingId != undefined && mappingId > 0) {

                    var updatedContainerValue = parseFloat($('input[name="itr_container_value[' + mappingId + ']"]').val()) || 0;
                    arrUpdateContainerValue.push({
                        mappingid: mappingId,
                        containerNumber: containerNumber,
                        updatedContainerValue: updatedContainerValue
                    });

                    totalContainerValue += updatedContainerValue;
                }
            });

            if (arrUpdateContainerValue.length > 0) {

                console.log(totalContainerValue);

                if (fileExtension == "pdf" || fileExtension == "PDF") {
                    totalPayableAmount = parseFloat($("#payable_itr").val()) || 0;
                }

                if (totalContainerValue != totalPayableAmount) {
                    toastr.clear();
                    toastr.error("Total Container Value should be equal to Total Payable Amount");
                    return false;
                }

                var fd = new FormData();
                fd.append("exportId", $("#hdnExportId").val());
                fd.append("originId", $("#hdnOriginId").val());
                fd.append('saNumber', $("#hdnSaNumber").val());
                fd.append("fileExtension", fileExtension);
                fd.append("updateContainerValueData_ITR", JSON.stringify(arrUpdateContainerValue));
                if (fileExtension == "xml" || fileExtension == "XML") {

                    fd.append('invoiceNo_ITR', $("#lblInvoiceNoITR").text());
                    fd.append('supplierName_ITR', supplierId);
                    fd.append('formattedDate_ITR', $("#lblIssuedDateITR").text());
                    fd.append('subTotal_ITR', subTotalCustoms);
                    fd.append('iva_ITR', ivaCustoms);
                    fd.append('retefuente_ITR', retefuenteCustoms);
                    fd.append('payable_ITR', payableCustoms);
                } else {
                    fd.append("uploadPdfFileITR", uploadPdfFileCustomAgency);
                    fd.append('invoiceNo_ITR', $("#invoice_number_itr").val());
                    fd.append('supplierName_ITR', $("#supplier_name_itr").val());
                    fd.append('formattedDate_ITR', $("#formatted_date_itr").val());
                    fd.append('subTotal_ITR', $("#subtotal_itr").val());
                    fd.append('iva_ITR', $("#iva_itr").val());
                    fd.append('retefuente_ITR', $("#retefuente_itr").val());
                    fd.append('payable_ITR', $("#payable_itr").val());
                }

                fd.append("csrf_cgrerp", $("#hdnCsrf").val());
                fd.append("add_type", 2);

                $.ajax({
                    url: base_url + "/save_export_documents",
                    type: 'post',
                    data: fd,
                    contentType: false,
                    processData: false,
                    success: function(jsonResult) {
                        $('#loading').hide();

                        if (jsonResult.redirect == true) {
                            window.location.replace(login_url);
                        } else if (jsonResult.result != '') {
                            toastr.clear();


                        } else if (jsonResult.error != '') {
                            toastr.clear();
                            toastr.error(jsonResult.error);
                            $('input[name="csrf_cgrerp"]').val(jsonResult.csrf_hash);
                        } else if (jsonResult.warning != '') {
                            toastr.clear();
                            toastr.warning(jsonResult.warning);
                            $('input[name="csrf_cgrerp"]').val(jsonResult.csrf_hash);
                        } else {
                            toastr.clear();
                        }
                    }
                });
            }
        });

        $("#btnSavePort").click(function() {

            var arrUpdateContainerValue = [];
            var containerData = <?php echo json_encode($containerDetails); ?>;
            var totalContainerValue = 0;

            $.each(containerData, function(i, item) {

                var mappingId = item.dispatch_id;
                var containerNumber = item.container_number;
                var isValid = true;

                if (mappingId != null && mappingId != '' && mappingId != undefined && mappingId > 0) {

                    var updatedContainerValue = parseFloat($('input[name="port_container_value[' + mappingId + ']"]').val()) || 0;
                    arrUpdateContainerValue.push({
                        mappingid: mappingId,
                        containerNumber: containerNumber,
                        updatedContainerValue: updatedContainerValue
                    });

                    totalContainerValue += updatedContainerValue;
                }
            });

            if (arrUpdateContainerValue.length > 0) {

                console.log(totalContainerValue);

                if (fileExtension == "pdf" || fileExtension == "PDF") {
                    totalPayableAmount = parseFloat($("#payable_port").val()) || 0;
                }

                if (totalContainerValue != totalPayableAmount) {
                    toastr.clear();
                    toastr.error("Total Container Value should be equal to Total Payable Amount");
                    return false;
                }

                var fd = new FormData();
                fd.append("exportId", $("#hdnExportId").val());
                fd.append("originId", $("#hdnOriginId").val());
                fd.append('saNumber', $("#hdnSaNumber").val());
                fd.append("fileExtension", fileExtension);
                fd.append("updateContainerValueData_Port", JSON.stringify(arrUpdateContainerValue));
                if (fileExtension == "xml" || fileExtension == "XML") {

                    fd.append('invoiceNo_Port', $("#lblInvoiceNoPort").text());
                    fd.append('supplierName_Port', supplierId);
                    fd.append('formattedDate_Port', $("#lblIssuedDatePort").text());
                    fd.append('subTotal_Port', subTotalCustoms);
                    fd.append('iva_Port', ivaCustoms);
                    fd.append('retefuente_Port', retefuenteCustoms);
                    fd.append('payable_Port', payableCustoms);
                } else {
                    fd.append("uploadPdfFilePort", uploadPdfFileCustomAgency);
                    fd.append('invoiceNo_Port', $("#invoice_number_port").val());
                    fd.append('supplierName_Port', $("#supplier_name_port").val());
                    fd.append('formattedDate_Port', $("#formatted_date_port").val());
                    fd.append('subTotal_Port', $("#subtotal_port").val());
                    fd.append('iva_Port', $("#iva_port").val());
                    fd.append('retefuente_Port', $("#retefuente_port").val());
                    fd.append('payable_Port', $("#payable_port").val());
                }

                fd.append("csrf_cgrerp", $("#hdnCsrf").val());
                fd.append("add_type", 3);

                $.ajax({
                    url: base_url + "/save_export_documents",
                    type: 'post',
                    data: fd,
                    contentType: false,
                    processData: false,
                    success: function(jsonResult) {
                        $('#loading').hide();

                        if (jsonResult.redirect == true) {
                            window.location.replace(login_url);
                        } else if (jsonResult.result != '') {
                            toastr.clear();


                        } else if (jsonResult.error != '') {
                            toastr.clear();
                            toastr.error(jsonResult.error);
                            $('input[name="csrf_cgrerp"]').val(jsonResult.csrf_hash);
                        } else if (jsonResult.warning != '') {
                            toastr.clear();
                            toastr.warning(jsonResult.warning);
                            $('input[name="csrf_cgrerp"]').val(jsonResult.csrf_hash);
                        } else {
                            toastr.clear();
                        }
                    }
                });
            }
        });

        $("#btnSaveShipping").click(function() {

            var arrUpdateContainerValue = [];
            var containerData = <?php echo json_encode($containerDetails); ?>;
            var totalContainerValue = 0;

            $.each(containerData, function(i, item) {

                var mappingId = item.dispatch_id;
                var containerNumber = item.container_number;
                var isValid = true;

                if (mappingId != null && mappingId != '' && mappingId != undefined && mappingId > 0) {

                    var updatedContainerValue = parseFloat($('input[name="shipping_container_value[' + mappingId + ']"]').val()) || 0;
                    arrUpdateContainerValue.push({
                        mappingid: mappingId,
                        containerNumber: containerNumber,
                        updatedContainerValue: updatedContainerValue
                    });

                    totalContainerValue += updatedContainerValue;
                }
            });

            if (arrUpdateContainerValue.length > 0) {

                console.log(totalContainerValue);

                if (fileExtension == "pdf" || fileExtension == "PDF") {
                    totalPayableAmount = parseFloat($("#payable_shipping").val()) || 0;
                }

                if (totalContainerValue != totalPayableAmount) {
                    toastr.clear();
                    toastr.error("Total Container Value should be equal to Total Payable Amount");
                    return false;
                }

                var fd = new FormData();
                fd.append("exportId", $("#hdnExportId").val());
                fd.append("originId", $("#hdnOriginId").val());
                fd.append('saNumber', $("#hdnSaNumber").val());
                fd.append("fileExtension", fileExtension);
                fd.append("updateContainerValueData_Shipping", JSON.stringify(arrUpdateContainerValue));
                if (fileExtension == "xml" || fileExtension == "XML") {

                    fd.append('invoiceNo_Shipping', $("#lblInvoiceNoShipping").text());
                    fd.append('supplierName_Shipping', supplierId);
                    fd.append('formattedDate_Shipping', $("#lblIssuedDateShipping").text());
                    fd.append('subTotal_Shipping', subTotalCustoms);
                    fd.append('iva_Shipping', ivaCustoms);
                    fd.append('retefuente_Shipping', retefuenteCustoms);
                    fd.append('payable_Shipping', payableCustoms);
                } else {
                    fd.append("uploadPdfFileShipping", uploadPdfFileCustomAgency);
                    fd.append('invoiceNo_Shipping', $("#invoice_number_shipping").val());
                    fd.append('supplierName_Shipping', $("#supplier_name_shipping").val());
                    fd.append('formattedDate_Shipping', $("#formatted_date_shipping").val());
                    fd.append('subTotal_Shipping', $("#subtotal_shipping").val());
                    fd.append('iva_Shipping', $("#iva_shipping").val());
                    fd.append('retefuente_Shipping', $("#retefuente_shipping").val());
                    fd.append('payable_Shipping', $("#payable_shipping").val());
                }

                fd.append("csrf_cgrerp", $("#hdnCsrf").val());
                fd.append("add_type", 9);

                $.ajax({
                    url: base_url + "/save_export_documents",
                    type: 'post',
                    data: fd,
                    contentType: false,
                    processData: false,
                    success: function(jsonResult) {
                        $('#loading').hide();

                        if (jsonResult.redirect == true) {
                            window.location.replace(login_url);
                        } else if (jsonResult.result != '') {
                            toastr.clear();


                        } else if (jsonResult.error != '') {
                            toastr.clear();
                            toastr.error(jsonResult.error);
                            $('input[name="csrf_cgrerp"]').val(jsonResult.csrf_hash);
                        } else if (jsonResult.warning != '') {
                            toastr.clear();
                            toastr.warning(jsonResult.warning);
                            $('input[name="csrf_cgrerp"]').val(jsonResult.csrf_hash);
                        } else {
                            toastr.clear();
                        }
                    }
                });
            }
        });

        $("#btnSaveFumigation").click(function() {

            var arrUpdateContainerValue = [];
            var containerData = <?php echo json_encode($containerDetails); ?>;
            var totalContainerValue = 0;

            $.each(containerData, function(i, item) {

                var mappingId = item.dispatch_id;
                var containerNumber = item.container_number;
                var isValid = true;

                if (mappingId != null && mappingId != '' && mappingId != undefined && mappingId > 0) {

                    var updatedContainerValue = parseFloat($('input[name="fumigation_container_value[' + mappingId + ']"]').val()) || 0;
                    arrUpdateContainerValue.push({
                        mappingid: mappingId,
                        containerNumber: containerNumber,
                        updatedContainerValue: updatedContainerValue
                    });

                    totalContainerValue += updatedContainerValue;
                }
            });

            if (arrUpdateContainerValue.length > 0) {

                console.log(totalContainerValue);

                if (fileExtension == "pdf" || fileExtension == "PDF") {
                    totalPayableAmount = parseFloat($("#payable_fumigation").val()) || 0;
                }

                if (totalContainerValue != totalPayableAmount) {
                    toastr.clear();
                    toastr.error("Total Container Value should be equal to Total Payable Amount");
                    return false;
                }

                var fd = new FormData();
                fd.append("exportId", $("#hdnExportId").val());
                fd.append("originId", $("#hdnOriginId").val());
                fd.append('saNumber', $("#hdnSaNumber").val());
                fd.append("fileExtension", fileExtension);
                fd.append("updateContainerValueData_Fumigation", JSON.stringify(arrUpdateContainerValue));
                if (fileExtension == "xml" || fileExtension == "XML") {

                    fd.append('invoiceNo_Fumigation', $("#lblInvoiceNoFumigation").text());
                    fd.append('supplierName_Fumigation', supplierId);
                    fd.append('formattedDate_Fumigation', $("#lblIssuedDateFumigation").text());
                    fd.append('subTotal_Fumigation', subTotalCustoms);
                    fd.append('iva_Fumigation', ivaCustoms);
                    fd.append('retefuente_Fumigation', retefuenteCustoms);
                    fd.append('payable_Fumigation', payableCustoms);
                } else {
                    fd.append("uploadPdfFileFumigation", uploadPdfFileCustomAgency);
                    fd.append('invoiceNo_Fumigation', $("#invoice_number_fumigation").val());
                    fd.append('supplierName_Fumigation', $("#supplier_name_fumigation").val());
                    fd.append('formattedDate_Fumigation', $("#formatted_date_fumigation").val());
                    fd.append('subTotal_Fumigation', $("#subtotal_fumigation").val());
                    fd.append('iva_Fumigation', $("#iva_fumigation").val());
                    fd.append('retefuente_Fumigation', $("#retefuente_fumigation").val());
                    fd.append('payable_Fumigation', $("#payable_fumigation").val());
                }

                fd.append("csrf_cgrerp", $("#hdnCsrf").val());
                fd.append("add_type", 4);

                $.ajax({
                    url: base_url + "/save_export_documents",
                    type: 'post',
                    data: fd,
                    contentType: false,
                    processData: false,
                    success: function(jsonResult) {
                        $('#loading').hide();

                        if (jsonResult.redirect == true) {
                            window.location.replace(login_url);
                        } else if (jsonResult.result != '') {
                            toastr.clear();


                        } else if (jsonResult.error != '') {
                            toastr.clear();
                            toastr.error(jsonResult.error);
                            $('input[name="csrf_cgrerp"]').val(jsonResult.csrf_hash);
                        } else if (jsonResult.warning != '') {
                            toastr.clear();
                            toastr.warning(jsonResult.warning);
                            $('input[name="csrf_cgrerp"]').val(jsonResult.csrf_hash);
                        } else {
                            toastr.clear();
                        }
                    }
                });
            }
        });
    });

    var loadFileCustom = function(event) {
        $("#error-selectdoc").hide();
        event.preventDefault();

        var files = $('#fileUploadDoc')[0].files[0];
        if (files != null && files != "") {

            $('#loading').show();
            farm_data_array = [];
            var fd = new FormData();

            fd.append('fileUploadDoc', files);
            fd.append('exportId', $("#hdnExportId").val());
            fd.append('originId', $("#hdnOriginId").val());
            fd.append('saNumber', $("#hdnSaNumber").val());
            fd.append('exportType', 1);
            fd.append('csrf_cgrerp', $("#hdnCsrf").val());

            $.ajax({
                url: base_url + "/upload_documents",
                type: 'post',
                data: fd,
                contentType: false,
                processData: false,
                success: function(jsonResult) {
                    $('#loading').hide();

                    if (jsonResult.redirect == true) {
                        window.location.replace(login_url);
                    } else if (jsonResult.result != '') {
                        toastr.clear();

                        $("#lblInvoiceNoCustoms").text(jsonResult.result["documentId"]);
                        $("#lblSupplierNameCustoms").text(jsonResult.result["registrationName"] + " --- " + jsonResult.result["companyId"]);
                        $("#lblIssuedDateCustoms").text(jsonResult.result["issueDate"]);
                        $("#lblTotalAmountCustoms").text(jsonResult.result["taxExclusiveAmount"]);
                        $("#lblTotalTaxAmountCustoms").text(jsonResult.result["taxAmount"]);
                        $("#lblAllowanceAmountCustoms").text(jsonResult.result["allowanceTotalAmount"]);
                        $("#lblPayableAmountCustoms").text(jsonResult.result["payableAmount"]);

                        if (jsonResult.result["fileExtension"] == "xml" || jsonResult.result["fileExtension"] == "XML") {
                            $("#divXml").show();
                            $("#divPdf").hide();
                            $("#divContainersCustoms").show();

                            $("#fileUploadDoc").val("");

                            totalPayableAmount = jsonResult.result["payableAmountValue"];
                            uploadPdfFileCustomAgency = "";
                            fileExtension = jsonResult.result["fileExtension"];
                            supplierId = jsonResult.result["supplierId"];
                            subTotalCustoms = jsonResult.result["taxExclusiveAmountValue"];
                            ivaCustoms = jsonResult.result["taxAmountValue"];
                            retefuenteCustoms = jsonResult.result["allowanceTotalAmountValue"];
                            payableCustoms = jsonResult.result["payableAmountValue"];
                        } else if (jsonResult.result["fileExtension"] == "pdf" || jsonResult.result["fileExtension"] == "PDF") {
                            $("#divXml").hide();
                            $("#divPdf").show();
                            $("#divContainersCustoms").show();

                            uploadPdfFileCustomAgency = jsonResult.result["fileUrl"];
                            fileExtension = jsonResult.result["fileExtension"];
                            supplierId = jsonResult.result["supplierId"];
                        }

                    } else if (jsonResult.error != '') {
                        toastr.clear();
                        toastr.error(jsonResult.error);
                        $('input[name="csrf_cgrerp"]').val(jsonResult.csrf_hash);
                    } else if (jsonResult.warning != '') {
                        toastr.clear();
                        toastr.warning(jsonResult.warning);
                        $('input[name="csrf_cgrerp"]').val(jsonResult.csrf_hash);
                    } else {
                        toastr.clear();
                    }
                }
            });
        } else {
            toastr.clear();
            //toastr.error(common_error);
            $("#fileUploadDoc").val("");
        }
    };

    var loadFileITR = function(event) {
        $("#error-selectdocitr").hide();
        event.preventDefault();

        var files = $('#fileUploadDoc_ITR')[0].files[0];
        if (files != null && files != "") {

            $('#loading').show();
            farm_data_array = [];
            var fd = new FormData();

            fd.append('fileUploadDoc', files);
            fd.append('exportId', $("#hdnExportId").val());
            fd.append('originId', $("#hdnOriginId").val());
            fd.append('saNumber', $("#hdnSaNumber").val());
            fd.append('exportType', 2);
            fd.append('csrf_cgrerp', $("#hdnCsrf").val());

            $.ajax({
                url: base_url + "/upload_documents",
                type: 'post',
                data: fd,
                contentType: false,
                processData: false,
                success: function(jsonResult) {
                    $('#loading').hide();

                    if (jsonResult.redirect == true) {
                        window.location.replace(login_url);
                    } else if (jsonResult.result != '') {
                        toastr.clear();

                        $("#lblInvoiceNoITR").text(jsonResult.result["documentId"]);
                        $("#lblSupplierNameITR").text(jsonResult.result["registrationName"] + " --- " + jsonResult.result["companyId"]);
                        $("#lblIssuedDateITR").text(jsonResult.result["issueDate"]);
                        $("#lblTotalAmountITR").text(jsonResult.result["taxExclusiveAmount"]);
                        $("#lblTotalTaxAmountITR").text(jsonResult.result["taxAmount"]);
                        $("#lblAllowanceAmountITR").text(jsonResult.result["allowanceTotalAmount"]);
                        $("#lblPayableAmountITR").text(jsonResult.result["payableAmount"]);

                        if (jsonResult.result["fileExtension"] == "xml" || jsonResult.result["fileExtension"] == "XML") {
                            $("#divXmlITR").show();
                            $("#divPdfITR").hide();
                            $("#divContainersITR").show();

                            $("#fileUploadDoc_ITR").val("");

                            totalPayableAmount = jsonResult.result["payableAmountValue"];
                            uploadPdfFileCustomAgency = "";
                            fileExtension = jsonResult.result["fileExtension"];
                            supplierId = jsonResult.result["supplierId"];
                            subTotalCustoms = jsonResult.result["taxExclusiveAmountValue"];
                            ivaCustoms = jsonResult.result["taxAmountValue"];
                            retefuenteCustoms = jsonResult.result["allowanceTotalAmountValue"];
                            payableCustoms = jsonResult.result["payableAmountValue"];
                        } else if (jsonResult.result["fileExtension"] == "pdf" || jsonResult.result["fileExtension"] == "PDF") {
                            $("#divXmlITR").hide();
                            $("#divPdfITR").show();
                            $("#divContainersITR").show();

                            uploadPdfFileCustomAgency = jsonResult.result["fileUrl"];
                            fileExtension = jsonResult.result["fileExtension"];
                            supplierId = jsonResult.result["supplierId"];
                        }

                    } else if (jsonResult.error != '') {
                        toastr.clear();
                        toastr.error(jsonResult.error);
                        $('input[name="csrf_cgrerp"]').val(jsonResult.csrf_hash);
                    } else if (jsonResult.warning != '') {
                        toastr.clear();
                        toastr.warning(jsonResult.warning);
                        $('input[name="csrf_cgrerp"]').val(jsonResult.csrf_hash);
                    } else {
                        toastr.clear();
                    }
                }
            });
        } else {
            toastr.clear();
            //toastr.error(common_error);
            $("#fileUploadDoc_ITR").val("");
        }
    };

    var loadFilePort = function(event) {
        $("#error-selectdocport").hide();
        event.preventDefault();

        var files = $('#fileUploadDoc_Port')[0].files[0];
        if (files != null && files != "") {

            $('#loading').show();
            farm_data_array = [];
            var fd = new FormData();

            fd.append('fileUploadDoc', files);
            fd.append('exportId', $("#hdnExportId").val());
            fd.append('originId', $("#hdnOriginId").val());
            fd.append('saNumber', $("#hdnSaNumber").val());
            fd.append('exportType', 3);
            fd.append('csrf_cgrerp', $("#hdnCsrf").val());

            $.ajax({
                url: base_url + "/upload_documents",
                type: 'post',
                data: fd,
                contentType: false,
                processData: false,
                success: function(jsonResult) {
                    $('#loading').hide();

                    if (jsonResult.redirect == true) {
                        window.location.replace(login_url);
                    } else if (jsonResult.result != '') {
                        toastr.clear();

                        $("#lblInvoiceNoPort").text(jsonResult.result["documentId"]);
                        $("#lblSupplierNamePort").text(jsonResult.result["registrationName"] + " --- " + jsonResult.result["companyId"]);
                        $("#lblIssuedDatePort").text(jsonResult.result["issueDate"]);
                        $("#lblTotalAmountPort").text(jsonResult.result["taxExclusiveAmount"]);
                        $("#lblTotalTaxAmountPort").text(jsonResult.result["taxAmount"]);
                        $("#lblAllowanceAmountPort").text(jsonResult.result["allowanceTotalAmount"]);
                        $("#lblPayableAmountPort").text(jsonResult.result["payableAmount"]);

                        if (jsonResult.result["fileExtension"] == "xml" || jsonResult.result["fileExtension"] == "XML") {
                            $("#divXmlPort").show();
                            $("#divPdfPort").hide();
                            $("#divContainersPort").show();

                            $("#fileUploadDoc_Port").val("");

                            totalPayableAmount = jsonResult.result["payableAmountValue"];
                            uploadPdfFileCustomAgency = "";
                            fileExtension = jsonResult.result["fileExtension"];
                            supplierId = jsonResult.result["supplierId"];
                            subTotalCustoms = jsonResult.result["taxExclusiveAmountValue"];
                            ivaCustoms = jsonResult.result["taxAmountValue"];
                            retefuenteCustoms = jsonResult.result["allowanceTotalAmountValue"];
                            payableCustoms = jsonResult.result["payableAmountValue"];
                        } else if (jsonResult.result["fileExtension"] == "pdf" || jsonResult.result["fileExtension"] == "PDF") {
                            $("#divXmlPort").hide();
                            $("#divPdfPort").show();
                            $("#divContainersPort").show();

                            uploadPdfFileCustomAgency = jsonResult.result["fileUrl"];
                            fileExtension = jsonResult.result["fileExtension"];
                            supplierId = jsonResult.result["supplierId"];
                        }

                    } else if (jsonResult.error != '') {
                        toastr.clear();
                        toastr.error(jsonResult.error);
                        $('input[name="csrf_cgrerp"]').val(jsonResult.csrf_hash);
                    } else if (jsonResult.warning != '') {
                        toastr.clear();
                        toastr.warning(jsonResult.warning);
                        $('input[name="csrf_cgrerp"]').val(jsonResult.csrf_hash);
                    } else {
                        toastr.clear();
                    }
                }
            });
        } else {
            toastr.clear();
            //toastr.error(common_error);
            $("#fileUploadDoc_Port").val("");
        }
    };

    var loadFileShipping = function(event) {
        $("#error-selectdocshipping").hide();
        event.preventDefault();

        var files = $('#fileUploadDoc_Shipping')[0].files[0];
        if (files != null && files != "") {

            $('#loading').show();
            farm_data_array = [];
            var fd = new FormData();

            fd.append('fileUploadDoc', files);
            fd.append('exportId', $("#hdnExportId").val());
            fd.append('originId', $("#hdnOriginId").val());
            fd.append('saNumber', $("#hdnSaNumber").val());
            fd.append('exportType', 9);
            fd.append('csrf_cgrerp', $("#hdnCsrf").val());

            $.ajax({
                url: base_url + "/upload_documents",
                type: 'post',
                data: fd,
                contentType: false,
                processData: false,
                success: function(jsonResult) {
                    $('#loading').hide();

                    if (jsonResult.redirect == true) {
                        window.location.replace(login_url);
                    } else if (jsonResult.result != '') {
                        toastr.clear();

                        $("#lblInvoiceNoShipping").text(jsonResult.result["documentId"]);
                        $("#lblSupplierNameShipping").text(jsonResult.result["registrationName"] + " --- " + jsonResult.result["companyId"]);
                        $("#lblIssuedDateShipping").text(jsonResult.result["issueDate"]);
                        $("#lblTotalAmountShipping").text(jsonResult.result["taxExclusiveAmount"]);
                        $("#lblTotalTaxAmountShipping").text(jsonResult.result["taxAmount"]);
                        $("#lblAllowanceAmountShipping").text(jsonResult.result["allowanceTotalAmount"]);
                        $("#lblPayableAmountShipping").text(jsonResult.result["payableAmount"]);

                        if (jsonResult.result["fileExtension"] == "xml" || jsonResult.result["fileExtension"] == "XML") {
                            $("#divXmlShipping").show();
                            $("#divPdfShipping").hide();
                            $("#divContainersShipping").show();

                            $("#fileUploadDoc_Shipping").val("");

                            totalPayableAmount = jsonResult.result["payableAmountValue"];
                            uploadPdfFileCustomAgency = "";
                            fileExtension = jsonResult.result["fileExtension"];
                            supplierId = jsonResult.result["supplierId"];
                            subTotalCustoms = jsonResult.result["taxExclusiveAmountValue"];
                            ivaCustoms = jsonResult.result["taxAmountValue"];
                            retefuenteCustoms = jsonResult.result["allowanceTotalAmountValue"];
                            payableCustoms = jsonResult.result["payableAmountValue"];
                        } else if (jsonResult.result["fileExtension"] == "pdf" || jsonResult.result["fileExtension"] == "PDF") {
                            $("#divXmlShipping").hide();
                            $("#divPdfShipping").show();
                            $("#divContainersShipping").show();

                            uploadPdfFileCustomAgency = jsonResult.result["fileUrl"];
                            fileExtension = jsonResult.result["fileExtension"];
                            supplierId = jsonResult.result["supplierId"];
                        }

                    } else if (jsonResult.error != '') {
                        toastr.clear();
                        toastr.error(jsonResult.error);
                        $('input[name="csrf_cgrerp"]').val(jsonResult.csrf_hash);
                    } else if (jsonResult.warning != '') {
                        toastr.clear();
                        toastr.warning(jsonResult.warning);
                        $('input[name="csrf_cgrerp"]').val(jsonResult.csrf_hash);
                    } else {
                        toastr.clear();
                    }
                }
            });
        } else {
            toastr.clear();
            //toastr.error(common_error);
            $("#fileUploadDoc_Shipping").val("");
        }
    };

    var loadFileFumigation = function(event) {
        $("#error-selectdocfumigation").hide();
        event.preventDefault();

        var files = $('#fileUploadDoc_Fumigation')[0].files[0];
        if (files != null && files != "") {

            $('#loading').show();
            farm_data_array = [];
            var fd = new FormData();

            fd.append('fileUploadDoc', files);
            fd.append('exportId', $("#hdnExportId").val());
            fd.append('originId', $("#hdnOriginId").val());
            fd.append('saNumber', $("#hdnSaNumber").val());
            fd.append('exportType', 4);
            fd.append('csrf_cgrerp', $("#hdnCsrf").val());

            $.ajax({
                url: base_url + "/upload_documents",
                type: 'post',
                data: fd,
                contentType: false,
                processData: false,
                success: function(jsonResult) {
                    $('#loading').hide();

                    if (jsonResult.redirect == true) {
                        window.location.replace(login_url);
                    } else if (jsonResult.result != '') {
                        toastr.clear();

                        $("#lblInvoiceNoFumigation").text(jsonResult.result["documentId"]);
                        $("#lblSupplierNameFumigation").text(jsonResult.result["registrationName"] + " --- " + jsonResult.result["companyId"]);
                        $("#lblIssuedDateFumigation").text(jsonResult.result["issueDate"]);
                        $("#lblTotalAmountFumigation").text(jsonResult.result["taxExclusiveAmount"]);
                        $("#lblTotalTaxAmountFumigation").text(jsonResult.result["taxAmount"]);
                        $("#lblAllowanceAmountFumigation").text(jsonResult.result["allowanceTotalAmount"]);
                        $("#lblPayableAmountFumigation").text(jsonResult.result["payableAmount"]);

                        if (jsonResult.result["fileExtension"] == "xml" || jsonResult.result["fileExtension"] == "XML") {
                            $("#divXmlFumigation").show();
                            $("#divPdfFumigation").hide();
                            $("#divContainersFumigation").show();

                            $("#fileUploadDoc_Fumigation").val("");

                            totalPayableAmount = jsonResult.result["payableAmountValue"];
                            uploadPdfFileCustomAgency = "";
                            fileExtension = jsonResult.result["fileExtension"];
                            supplierId = jsonResult.result["supplierId"];
                            subTotalCustoms = jsonResult.result["taxExclusiveAmountValue"];
                            ivaCustoms = jsonResult.result["taxAmountValue"];
                            retefuenteCustoms = jsonResult.result["allowanceTotalAmountValue"];
                            payableCustoms = jsonResult.result["payableAmountValue"];
                        } else if (jsonResult.result["fileExtension"] == "pdf" || jsonResult.result["fileExtension"] == "PDF") {
                            $("#divXmlFumigation").hide();
                            $("#divPdfFumigation").show();
                            $("#divContainersFumigation").show();

                            uploadPdfFileCustomAgency = jsonResult.result["fileUrl"];
                            fileExtension = jsonResult.result["fileExtension"];
                            supplierId = jsonResult.result["supplierId"];
                        }

                    } else if (jsonResult.error != '') {
                        toastr.clear();
                        toastr.error(jsonResult.error);
                        $('input[name="csrf_cgrerp"]').val(jsonResult.csrf_hash);
                    } else if (jsonResult.warning != '') {
                        toastr.clear();
                        toastr.warning(jsonResult.warning);
                        $('input[name="csrf_cgrerp"]').val(jsonResult.csrf_hash);
                    } else {
                        toastr.clear();
                    }
                }
            });
        } else {
            toastr.clear();
            //toastr.error(common_error);
            $("#fileUploadDoc_Fumigation").val("");
        }
    };
</script>

<script>
    document.getElementById("issued_date_custom").addEventListener("change", function() {
        let inputDate = this.value;

        if (!inputDate) return;

        let dateObj = new Date(inputDate);
        let day = String(dateObj.getDate()).padStart(2, '0');
        let month = String(dateObj.getMonth() + 1).padStart(2, '0'); // Months are zero-based
        let year = dateObj.getFullYear();

        let hours = dateObj.getHours();
        let minutes = String(dateObj.getMinutes()).padStart(2, '0');
        let ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12 || 12; // Convert 24-hour format to 12-hour format

        let formattedDate = `${day}/${month}/${year} ${hours}:${minutes} ${ampm}`;

        document.getElementById("formatted_date_custom").value = formattedDate;
    });

    // Optional: Open datetime picker when clicking formatted input
    document.getElementById("formatted_date_itr").addEventListener("click", function() {
        document.getElementById("issued_date_itr").click();
    });

    document.getElementById("issued_date_itr").addEventListener("change", function() {
        let inputDate = this.value;

        if (!inputDate) return;

        let dateObj = new Date(inputDate);
        let day = String(dateObj.getDate()).padStart(2, '0');
        let month = String(dateObj.getMonth() + 1).padStart(2, '0'); // Months are zero-based
        let year = dateObj.getFullYear();

        let hours = dateObj.getHours();
        let minutes = String(dateObj.getMinutes()).padStart(2, '0');
        let ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12 || 12; // Convert 24-hour format to 12-hour format

        let formattedDate = `${day}/${month}/${year} ${hours}:${minutes} ${ampm}`;

        document.getElementById("formatted_date_custom").value = formattedDate;
    });

    // Optional: Open datetime picker when clicking formatted input
    document.getElementById("formatted_date_itr").addEventListener("click", function() {
        document.getElementById("issued_date_itr").click();
    });

    document.getElementById("issued_date_port").addEventListener("change", function() {
        let inputDate = this.value;

        if (!inputDate) return;

        let dateObj = new Date(inputDate);
        let day = String(dateObj.getDate()).padStart(2, '0');
        let month = String(dateObj.getMonth() + 1).padStart(2, '0'); // Months are zero-based
        let year = dateObj.getFullYear();

        let hours = dateObj.getHours();
        let minutes = String(dateObj.getMinutes()).padStart(2, '0');
        let ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12 || 12; // Convert 24-hour format to 12-hour format

        let formattedDate = `${day}/${month}/${year} ${hours}:${minutes} ${ampm}`;

        document.getElementById("formatted_date_port").value = formattedDate;
    });

    // Optional: Open datetime picker when clicking formatted input
    document.getElementById("formatted_date_port").addEventListener("click", function() {
        document.getElementById("issued_date_port").click();
    });

    document.getElementById("formatted_date_shipping").addEventListener("click", function() {
        document.getElementById("issued_date_shipping").click();
    });

    document.getElementById("issued_date_shipping").addEventListener("change", function() {
        let inputDate = this.value;

        if (!inputDate) return;

        let dateObj = new Date(inputDate);
        let day = String(dateObj.getDate()).padStart(2, '0');
        let month = String(dateObj.getMonth() + 1).padStart(2, '0'); // Months are zero-based
        let year = dateObj.getFullYear();

        let hours = dateObj.getHours();
        let minutes = String(dateObj.getMinutes()).padStart(2, '0');
        let ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12 || 12; // Convert 24-hour format to 12-hour format

        let formattedDate = `${day}/${month}/${year} ${hours}:${minutes} ${ampm}`;

        document.getElementById("formatted_date_shipping").value = formattedDate;
    });

    // Optional: Open datetime picker when clicking formatted input
    document.getElementById("formatted_date_shipping").addEventListener("click", function() {
        document.getElementById("issued_date_shipping").click();
    });

    document.getElementById("issued_date_fumigation").addEventListener("change", function() {
        let inputDate = this.value;

        if (!inputDate) return;

        let dateObj = new Date(inputDate);
        let day = String(dateObj.getDate()).padStart(2, '0');
        let month = String(dateObj.getMonth() + 1).padStart(2, '0'); // Months are zero-based
        let year = dateObj.getFullYear();

        let hours = dateObj.getHours();
        let minutes = String(dateObj.getMinutes()).padStart(2, '0');
        let ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12 || 12; // Convert 24-hour format to 12-hour format

        let formattedDate = `${day}/${month}/${year} ${hours}:${minutes} ${ampm}`;

        document.getElementById("formatted_date_fumigation").value = formattedDate;
    });

    // Optional: Open datetime picker when clicking formatted input
    document.getElementById("formatted_date_fumigation").addEventListener("click", function() {
        document.getElementById("issued_date_fumigation").click();
    });
</script>