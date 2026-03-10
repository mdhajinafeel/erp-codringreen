<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<?php $site_lang = $this->load->helper('language'); ?>
<?php $wz_lang = $site_lang->session->userdata('site_lang'); ?>
<div class="modal fadeInRight animated" data-bs-keyboard="false" data-bs-backdrop="static" tabindex="-1" id="alert-dialog-info" role="dialog" aria-labelledby="alert-dialog-info" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="titlehead"></h4>
                <?php echo form_button(array('aria-label' => 'Close', 'data-bs-dismiss' => 'modal', 'type' => 'button', 'class' => 'close', 'content' => '<span aria-hidden="true">×</span>')); ?>

            </div>
            <?php echo form_open(); ?>
            <div class="modal-body farm-modal">
                <h5 class="text-center modal-message" id="infomessage"></h5>
            </div>
            <div class="modal-footer">
                <?php echo form_button(array('data-bs-dismiss' => 'modal', 'type' => 'button', 'class' => 'btn btn-secondary', 'content' => $this->lang->line('close'))); ?>
                <?php echo form_button(array('id' => 'deletebutton', 'type' => 'button', 'class' => 'btn btn-danger', 'content' => $this->lang->line('ok'))); ?>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
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
                            <!--<button class="nav-link text-white" id="nav-remobilization-tab" data-bs-toggle="tab" data-bs-target="#nav-remobilization" type="button" role="tab" aria-controls="nav-remobilization" aria-selected="false">-->
                            <!--    <?php echo $this->lang->line("doc_remobilization"); ?>-->
                            <!--</button>-->
                            <button class="nav-link text-white" id="nav-containercost-tab" data-bs-toggle="tab" data-bs-target="#nav-containercost" type="button" role="tab" aria-controls="nav-containercost" aria-selected="false">
                                <?php echo $this->lang->line("doc_containercost"); ?>
                            </button>
                            <button class="nav-link text-white" id="nav-containerloadingcost-tab" data-bs-toggle="tab" data-bs-target="#nav-containerloadingcost" type="button" role="tab" aria-controls="nav-containerloadingcost" aria-selected="false">
                                <?php echo $this->lang->line("doc_containerloading"); ?>
                            </button>
                            <button class="nav-link text-white" id="nav-dhlcost-tab" data-bs-toggle="tab" data-bs-target="#nav-dhlcost" type="button" role="tab" aria-controls="nav-dhlcost" aria-selected="false">
                                <?php echo $this->lang->line("doc_dhl"); ?>
                            </button>
                            <button class="nav-link text-white" id="nav-othercost-tab" data-bs-toggle="tab" data-bs-target="#nav-othercost" type="button" role="tab" aria-controls="nav-othercost" aria-selected="false">
                                <?php echo $this->lang->line("doc_othercost"); ?>
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
                                    <div class="col-md-2">
                                        <label for="export_subtotal"><?php echo $this->lang->line("export_subtotal"); ?></label>
                                        <!-- <div class="input-group">
                                            <label class="control-label" id="lblTotalAmountCustoms"></label>
                                        </div> -->
                                        <input type="number" step="any" id="lblTotalAmountCustoms" name="lblTotalAmountCustoms" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_subtotal"); ?>" />
                                    </div>

                                    <div class="col-md-2">
                                        <label for="export_iva"><?php echo $this->lang->line("export_iva"); ?></label>
                                        <!-- <div class="input-group">
                                            <label class="control-label" id="lblTotalTaxAmountCustoms"></label>
                                        </div> -->
                                        <input type="number" step="any" id="lblTotalTaxAmountCustoms" name="lblTotalTaxAmountCustoms" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_iva"); ?>" />
                                    </div>

                                    <div class="col-md-2">
                                        <label for="export_retefuente"><?php echo $this->lang->line("export_retefuente"); ?></label>
                                        <!-- <div class="input-group">
                                            <label class="control-label" id="lblAllowanceAmountCustoms"></label>
                                        </div> -->
                                        <input type="number" step="any" id="lblAllowanceAmountCustoms" name="lblAllowanceAmountCustoms" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_retefuente"); ?>" />
                                    </div>

                                    <div class="col-md-3">
                                        <label for="export_total_payable"><?php echo $this->lang->line("export_total_payable"); ?></label>
                                        <!-- <div class="input-group">
                                            <label class="control-label" id="lblPayableAmountCustoms"></label>
                                        </div> -->
                                        <input type="number" step="any" id="lblPayableAmountCustoms" name="lblPayableAmountCustoms" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_total_payable"); ?>" />
                                    </div>

                                    <div class="col-md-3">
                                        <label for="total_container_value"><?php echo $this->lang->line("total_container_value"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="lblContainerValueCustoms"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="divPdf">
                                <div class="row mb-3">
                                    <div class="col-md-3">
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

                                    <div class="col-md-3">
                                        <label for="issued_date_custom"><?php echo $this->lang->line("issued_date"); ?></label>
                                        <input type="datetime-local" id="issued_date_custom" name="issued_date_custom" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("issued_date"); ?>" />
                                        <input type="text" id="formatted_date_custom" class="form-control text-uppercase" placeholder="DD/MM/YYYY HH:MM AM/PM" readonly style="display: none;" />
                                        <label id="error-issued_date_custom" class="error-text"><?php echo $this->lang->line("error_issued_date"); ?></label>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-2">
                                        <label for="export_subtotal"><?php echo $this->lang->line("export_subtotal"); ?></label>
                                        <input type="number" step="any" id="subtotal_custom" name="subtotal_custom" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_subtotal"); ?>" />
                                        <label id="error-subtotal_custom" class="error-text"><?php echo $this->lang->line("error_value"); ?></label>
                                    </div>

                                    <div class="col-md-2">
                                        <label for="export_iva"><?php echo $this->lang->line("export_iva"); ?></label>
                                        <input type="number" step="any" id="iva_custom" name="iva_custom" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_iva"); ?>" />
                                        <label id="error-iva_custom" class="error-text"><?php echo $this->lang->line("error_value"); ?></label>
                                    </div>

                                    <div class="col-md-2">
                                        <label for="export_retefuente"><?php echo $this->lang->line("export_retefuente"); ?></label>
                                        <input type="number" step="any" id="retefuente_custom" name="retefuente_custom" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_retefuente"); ?>" />
                                        <label id="error-retefuente_custom" class="error-text"><?php echo $this->lang->line("error_value"); ?></label>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="export_total_payable"><?php echo $this->lang->line("export_total_payable"); ?></label>
                                        <input type="number" step="any" id="payable_custom" name="payable_custom" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_total_payable"); ?>" />
                                        <label id="error-payable_custom" class="error-text"><?php echo $this->lang->line("error_value"); ?></label>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="container_value_custom"><?php echo $this->lang->line("total_container_value"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="container_value_custom">0</label>
                                        </div>
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
                                            <input type="number" step="any" value="<?php echo 0; ?>" class="form-control text-uppercase" id="custom_container_value" name="custom_container_value[<?php echo $containerdetail->dispatch_id; ?>]" />
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>

                            <div class="row mb-3 mt-4">
                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-danger col-md-2" style="margin-right: 5px;" id="btnResetCustoms" name="btnResetCustoms"><?php echo $this->lang->line('reset'); ?></button>
                                    <button type="button" class="btn btn-primary col-md-2" id="btnSaveCustoms" name="btnSaveCustoms"><?php echo $this->lang->line('save'); ?></button>
                                </div>
                            </div>

                            <div id="divInvoiceListCustoms">
                                <div class="card-header">
                                    <h5 class="mb-0"><?php echo $this->lang->line("invoice_list"); ?></h5>
                                </div>
                                <div style="overflow-x: auto;">
                                    <table class="datatables-demo table table-striped table-bordered" id="xin_table_invoice_customs" style="width: 100% !important;">
                                        <thead>
                                            <tr>
                                                <th><?php echo $this->lang->line("action"); ?></th>
                                                <th><?php echo $this->lang->line("invoice_no"); ?></th>
                                                <th><?php echo $this->lang->line("supplier_name"); ?></th>
                                                <th><?php echo $this->lang->line("invoice_date"); ?></th>
                                                <th><?php echo $this->lang->line("export_subtotal"); ?></th>
                                                <th><?php echo $this->lang->line("export_iva"); ?></th>
                                                <th><?php echo $this->lang->line("export_retefuente"); ?></th>
                                                <th><?php echo $this->lang->line("export_total_payable"); ?></th>
                                                <th><?php echo $this->lang->line("container_value"); ?></th>
                                            </tr>
                                        </thead>
                                    </table>
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
                                    <div class="col-md-2">
                                        <label for="export_subtotal"><?php echo $this->lang->line("export_subtotal"); ?></label>
                                        <!-- <div class="input-group">
                                            <label class="control-label" id="lblTotalAmountITR"></label>
                                        </div> -->
                                        <input type="number" step="any" id="lblTotalAmountITR" name="lblTotalAmountITR" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_subtotal"); ?>" />
                                    </div>

                                    <div class="col-md-2">
                                        <label for="export_iva"><?php echo $this->lang->line("export_iva"); ?></label>
                                        <!-- <div class="input-group">
                                            <label class="control-label" id="lblTotalTaxAmountITR"></label>
                                        </div> -->
                                        <input type="number" step="any" id="lblTotalTaxAmountITR" name="lblTotalTaxAmountITR" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_iva"); ?>" />
                                    </div>

                                    <div class="col-md-2">
                                        <label for="export_retefuente"><?php echo $this->lang->line("export_retefuente"); ?></label>
                                        <!-- <div class="input-group">
                                            <label class="control-label" id="lblAllowanceAmountITR"></label>
                                        </div> -->
                                        <input type="number" step="any" id="lblAllowanceAmountITR" name="lblAllowanceAmountITR" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_retefuente"); ?>" />
                                    </div>

                                    <div class="col-md-3">
                                        <label for="export_total_payable"><?php echo $this->lang->line("export_total_payable"); ?></label>
                                        <!-- <div class="input-group">
                                            <label class="control-label" id="lblPayableAmountITR"></label>
                                        </div> -->
                                        <input type="number" step="any" id="lblPayableAmountITR" name="lblPayableAmountITR" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_total_payable"); ?>" />
                                    </div>

                                    <div class="col-md-3">
                                        <label for="total_container_value"><?php echo $this->lang->line("total_container_value"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="lblContainerValueITR"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="divPdfITR">
                                <div class="row mb-3">
                                    <div class="col-md-3">
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

                                    <div class="col-md-3">
                                        <label for="issued_date_itr"><?php echo $this->lang->line("issued_date"); ?></label>
                                        <input type="datetime-local" id="issued_date_itr" name="issued_date_itr" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("issued_date"); ?>" />
                                        <input type="text" id="formatted_date_itr" class="form-control text-uppercase" placeholder="DD/MM/YYYY HH:MM AM/PM" readonly style="display: none;" />
                                        <label id="error-issued_date_itr" class="error-text"><?php echo $this->lang->line("error_issued_date"); ?></label>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-2">
                                        <label for="export_subtotal"><?php echo $this->lang->line("export_subtotal"); ?></label>
                                        <input type="number" step="any" id="subtotal_itr" name="subtotal_itr" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_subtotal"); ?>" />
                                        <label id="error-subtotal_itr" class="error-text"><?php echo $this->lang->line("error_value"); ?></label>
                                    </div>

                                    <div class="col-md-2">
                                        <label for="export_iva"><?php echo $this->lang->line("export_iva"); ?></label>
                                        <input type="number" step="any" id="iva_itr" name="iva_itr" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_iva"); ?>" />
                                        <label id="error-iva_itr" class="error-text"><?php echo $this->lang->line("error_value"); ?></label>
                                    </div>

                                    <div class="col-md-2">
                                        <label for="export_retefuente"><?php echo $this->lang->line("export_retefuente"); ?></label>
                                        <input type="number" step="any" id="retefuente_itr" name="retefuente_itr" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_retefuente"); ?>" />
                                        <label id="error-retefuente_itr" class="error-text"><?php echo $this->lang->line("error_value"); ?></label>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="export_total_payable"><?php echo $this->lang->line("export_total_payable"); ?></label>
                                        <input type="number" step="any" id="payable_itr" name="payable_itr" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_total_payable"); ?>" />
                                        <label id="error-payable_itr" class="error-text"><?php echo $this->lang->line("error_value"); ?></label>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="container_value_itr"><?php echo $this->lang->line("total_container_value"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="container_value_itr">0</label>
                                        </div>
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
                                            <input type="number" step="any" value="<?php echo 0; ?>" class="form-control text-uppercase" id="itr_container_value" name="itr_container_value[<?php echo $containerdetail->dispatch_id; ?>]" />
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>

                            <div class="row mb-3 mt-4">
                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-danger col-md-2" style="margin-right: 5px;" id="btnResetITR" name="btnResetITR"><?php echo $this->lang->line('reset'); ?></button>
                                    <button type="button" class="btn btn-primary col-md-2" id="btnSaveITR" name="btnSaveITR"><?php echo $this->lang->line('save'); ?></button>
                                </div>
                            </div>

                            <div id="divInvoiceListITR">
                                <div class="card-header">
                                    <h5 class="mb-0"><?php echo $this->lang->line("invoice_list"); ?></h5>
                                </div>
                                <div style="overflow-x: auto;">
                                    <table class="datatables-demo table table-striped table-bordered" id="xin_table_invoice_itr" style="width: 100% !important;">
                                        <thead>
                                            <tr>
                                                <th><?php echo $this->lang->line("action"); ?></th>
                                                <th><?php echo $this->lang->line("invoice_no"); ?></th>
                                                <th><?php echo $this->lang->line("supplier_name"); ?></th>
                                                <th><?php echo $this->lang->line("invoice_date"); ?></th>
                                                <th><?php echo $this->lang->line("export_subtotal"); ?></th>
                                                <th><?php echo $this->lang->line("export_iva"); ?></th>
                                                <th><?php echo $this->lang->line("export_retefuente"); ?></th>
                                                <th><?php echo $this->lang->line("export_total_payable"); ?></th>
                                                <th><?php echo $this->lang->line("container_value"); ?></th>
                                            </tr>
                                        </thead>
                                    </table>
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
                                    <div class="col-md-2">
                                        <label for="export_subtotal"><?php echo $this->lang->line("export_subtotal"); ?></label>
                                        <!-- <div class="input-group">
                                            <label class="control-label" id="lblTotalAmountPort"></label>
                                        </div> -->
                                        <input type="number" step="any" id="lblTotalAmountPort" name="lblTotalAmountPort" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_subtotal"); ?>" />
                                    </div>

                                    <div class="col-md-2">
                                        <label for="export_iva"><?php echo $this->lang->line("export_iva"); ?></label>
                                        <!-- <div class="input-group">
                                            <label class="control-label" id="lblTotalTaxAmountPort"></label>
                                        </div> -->
                                        <input type="number" step="any" id="lblTotalTaxAmountPort" name="lblTotalTaxAmountPort" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_iva"); ?>" />
                                    </div>

                                    <div class="col-md-2">
                                        <label for="export_retefuente"><?php echo $this->lang->line("export_retefuente"); ?></label>
                                        <!-- <div class="input-group">
                                            <label class="control-label" id="lblAllowanceAmountPort"></label>
                                        </div> -->
                                        <input type="number" step="any" id="lblAllowanceAmountPort" name="lblAllowanceAmountPort" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_retefuente"); ?>" />
                                    </div>

                                    <div class="col-md-3">
                                        <label for="export_total_payable"><?php echo $this->lang->line("export_total_payable"); ?></label>
                                        <!-- <div class="input-group">
                                            <label class="control-label" id="lblPayableAmountPort"></label>
                                        </div> -->
                                        <input type="number" step="any" id="lblPayableAmountPort" name="lblPayableAmountPort" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_total_payable"); ?>" />
                                    </div>

                                    <div class="col-md-3">
                                        <label for="total_container_value"><?php echo $this->lang->line("total_container_value"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="lblContainerValuePort"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="divPdfPort">
                                <div class="row mb-3">
                                    <div class="col-md-3">
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

                                    <div class="col-md-3">
                                        <label for="issued_date_port"><?php echo $this->lang->line("issued_date"); ?></label>
                                        <input type="datetime-local" id="issued_date_port" name="issued_date_port" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("issued_date"); ?>" />
                                        <input type="text" id="formatted_date_port" class="form-control text-uppercase" placeholder="DD/MM/YYYY HH:MM AM/PM" readonly style="display: none;" />
                                        <label id="error-issued_date_port" class="error-text"><?php echo $this->lang->line("error_issued_date"); ?></label>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-2">
                                        <label for="export_subtotal"><?php echo $this->lang->line("export_subtotal"); ?></label>
                                        <input type="number" step="any" id="subtotal_port" name="subtotal_port" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_subtotal"); ?>" />
                                        <label id="error-subtotal_port" class="error-text"><?php echo $this->lang->line("error_value"); ?></label>
                                    </div>

                                    <div class="col-md-2">
                                        <label for="export_iva"><?php echo $this->lang->line("export_iva"); ?></label>
                                        <input type="number" step="any" id="iva_port" name="iva_port" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_iva"); ?>" />
                                        <label id="error-iva_port" class="error-text"><?php echo $this->lang->line("error_value"); ?></label>
                                    </div>

                                    <div class="col-md-2">
                                        <label for="export_retefuente"><?php echo $this->lang->line("export_retefuente"); ?></label>
                                        <input type="number" step="any" id="retefuente_port" name="retefuente_port" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_retefuente"); ?>" />
                                        <label id="error-retefuente_port" class="error-text"><?php echo $this->lang->line("error_value"); ?></label>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="export_total_payable"><?php echo $this->lang->line("export_total_payable"); ?></label>
                                        <input type="number" step="any" id="payable_port" name="payable_port" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_total_payable"); ?>" />
                                        <label id="error-payable_port" class="error-text"><?php echo $this->lang->line("error_value"); ?></label>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="container_value_port"><?php echo $this->lang->line("total_container_value"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="container_value_port"><?php echo 0; ?></label>
                                        </div>
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
                                            <input type="number" step="any" value="<?php echo 0; ?>" class="form-control text-uppercase" id="port_container_value" name="port_container_value[<?php echo $containerdetail->dispatch_id; ?>]" />
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>

                            <div class="row mb-3 mt-4">
                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-danger col-md-2" style="margin-right: 5px;" id="btnResetPort" name="btnResetPort"><?php echo $this->lang->line('reset'); ?></button>
                                    <button type="button" class="btn btn-primary col-md-2" id="btnSavePort" name="btnSavePort"><?php echo $this->lang->line('save'); ?></button>
                                </div>
                            </div>

                            <div id="divInvoiceListPort">
                                <div class="card-header">
                                    <h5 class="mb-0"><?php echo $this->lang->line("invoice_list"); ?></h5>
                                </div>
                                <div style="overflow-x: auto;">
                                    <table class="datatables-demo table table-striped table-bordered" id="xin_table_invoice_port" style="width: 100% !important;">
                                        <thead>
                                            <tr>
                                                <th><?php echo $this->lang->line("action"); ?></th>
                                                <th><?php echo $this->lang->line("invoice_no"); ?></th>
                                                <th><?php echo $this->lang->line("supplier_name"); ?></th>
                                                <th><?php echo $this->lang->line("invoice_date"); ?></th>
                                                <th><?php echo $this->lang->line("export_subtotal"); ?></th>
                                                <th><?php echo $this->lang->line("export_iva"); ?></th>
                                                <th><?php echo $this->lang->line("export_retefuente"); ?></th>
                                                <th><?php echo $this->lang->line("export_total_payable"); ?></th>
                                                <th><?php echo $this->lang->line("container_value"); ?></th>
                                            </tr>
                                        </thead>
                                    </table>
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
                                    <div class="col-md-2">
                                        <label for="export_subtotal"><?php echo $this->lang->line("export_subtotal"); ?></label>
                                        <!-- <div class="input-group">
                                            <label class="control-label" id="lblTotalAmountShipping"></label>
                                        </div> -->
                                        <input type="number" step="any" id="lblTotalAmountShipping" name="lblTotalAmountShipping" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_subtotal"); ?>" />
                                    </div>

                                    <div class="col-md-2">
                                        <label for="export_iva"><?php echo $this->lang->line("export_iva"); ?></label>
                                        <!-- <div class="input-group">
                                            <label class="control-label" id="lblTotalTaxAmountShipping"></label>
                                        </div> -->
                                        <input type="number" step="any" id="lblTotalTaxAmountShipping" name="lblTotalTaxAmountShipping" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_iva"); ?>" />
                                    </div>

                                    <div class="col-md-2">
                                        <label for="export_retefuente"><?php echo $this->lang->line("export_retefuente"); ?></label>
                                        <!-- <div class="input-group">
                                            <label class="control-label" id="lblAllowanceAmountShipping"></label>
                                        </div> -->
                                        <input type="number" step="any" id="lblAllowanceAmountShipping" name="lblAllowanceAmountShipping" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_retefuente"); ?>" />
                                    </div>

                                    <div class="col-md-3">
                                        <label for="export_total_payable"><?php echo $this->lang->line("export_total_payable"); ?></label>
                                        <!-- <div class="input-group">
                                            <label class="control-label" id="lblPayableAmountShipping"></label>
                                        </div> -->
                                        <input type="number" step="any" id="lblPayableAmountShipping" name="lblPayableAmountShipping" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_total_payable"); ?>" />
                                    </div>

                                    <div class="col-md-3">
                                        <label for="total_container_value"><?php echo $this->lang->line("total_container_value"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="lblContainerValueShipping"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="divPdfShipping">
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label for="invoice_number_shipping"><?php echo $this->lang->line("invoice_number"); ?></label>
                                        <input type="text" id="invoice_number_shipping" name="invoice_number_shipping" class="form-control text-uppercase" value="<?php echo isset($exportDocumentsShipping[0]->invoice_no) ? $exportDocumentsShipping[0]->invoice_no : ''; ?>" placeholder="<?php echo $this->lang->line("invoice_number"); ?>" />
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

                                    <div class="col-md-3">
                                        <label for="issued_date_shipping"><?php echo $this->lang->line("issued_date"); ?></label>
                                        <input type="datetime-local" id="issued_date_shipping" name="issued_date_shipping" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("issued_date"); ?>" />
                                        <input type="text" id="formatted_date_shipping" class="form-control text-uppercase" placeholder="DD/MM/YYYY HH:MM AM/PM" readonly style="display: none;" />
                                        <label id="error-issued_date_shipping" class="error-text"><?php echo $this->lang->line("error_issued_date"); ?></label>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-2">
                                        <label for="export_subtotal"><?php echo $this->lang->line("export_subtotal"); ?></label>
                                        <input type="number" step="any" id="subtotal_shipping" name="subtotal_shipping" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("exshipping_subtotal"); ?>" />
                                        <label id="error-subtotal_shipping" class="error-text"><?php echo $this->lang->line("error_value"); ?></label>
                                    </div>

                                    <div class="col-md-2">
                                        <label for="export_iva"><?php echo $this->lang->line("export_iva"); ?></label>
                                        <input type="number" step="any" id="iva_shipping" name="iva_shipping" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("exshipping_iva"); ?>" />
                                        <label id="error-iva_shipping" class="error-text"><?php echo $this->lang->line("error_value"); ?></label>
                                    </div>

                                    <div class="col-md-2">
                                        <label for="export_retefuente"><?php echo $this->lang->line("export_retefuente"); ?></label>
                                        <input type="number" step="any" id="retefuente_shipping" name="retefuente_shipping" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("exshipping_retefuente"); ?>" />
                                        <label id="error-retefuente_shipping" class="error-text"><?php echo $this->lang->line("error_value"); ?></label>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="export_total_payable"><?php echo $this->lang->line("export_total_payable"); ?></label>
                                        <input type="number" step="any" id="payable_shipping" name="payable_shipping" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("exshipping_total_payable"); ?>" />
                                        <label id="error-payable_shipping" class="error-text"><?php echo $this->lang->line("error_value"); ?></label>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="container_value_shipping"><?php echo $this->lang->line("total_container_value"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="container_value_shipping">0</label>
                                        </div>
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
                                            <input type="number" step="any" value="<?php echo 0; ?>" class="form-control text-uppercase" id="shipping_container_value" name="shipping_container_value[<?php echo $containerdetail->dispatch_id; ?>]" />
                                        </div>
                                    </div>
                                <?php } ?>


                            </div>

                            <div class="row mb-3 mt-4">
                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-danger col-md-2" style="margin-right: 5px;" id="btnResetShipping" name="btnResetShipping"><?php echo $this->lang->line('reset'); ?></button>
                                    <button type="button" class="btn btn-primary col-md-2" id="btnSaveShipping" name="btnSaveShipping"><?php echo $this->lang->line('save'); ?></button>
                                </div>
                            </div>

                            <div id="divInvoiceListShipping">
                                <div class="card-header">
                                    <h5 class="mb-0"><?php echo $this->lang->line("invoice_list"); ?></h5>
                                </div>
                                <div style="overflow-x: auto;">
                                    <table class="datatables-demo table table-striped table-bordered" id="xin_table_invoice_shipping" style="width: 100% !important;">
                                        <thead>
                                            <tr>
                                                <th><?php echo $this->lang->line("action"); ?></th>
                                                <th><?php echo $this->lang->line("invoice_no"); ?></th>
                                                <th><?php echo $this->lang->line("supplier_name"); ?></th>
                                                <th><?php echo $this->lang->line("invoice_date"); ?></th>
                                                <th><?php echo $this->lang->line("export_subtotal"); ?></th>
                                                <th><?php echo $this->lang->line("export_iva"); ?></th>
                                                <th><?php echo $this->lang->line("export_retefuente"); ?></th>
                                                <th><?php echo $this->lang->line("export_total_payable"); ?></th>
                                                <th><?php echo $this->lang->line("container_value"); ?></th>
                                            </tr>
                                        </thead>
                                    </table>
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
                                    <div class="col-md-2">
                                        <label for="export_subtotal"><?php echo $this->lang->line("export_subtotal"); ?></label>
                                        <!-- <div class="input-group">
                                            <label class="control-label" id="lblTotalAmountFumigation"></label>
                                        </div> -->
                                        <input type="number" step="any" id="lblTotalAmountFumigation" name="lblTotalAmountFumigation" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_subtotal"); ?>" />
                                    </div>

                                    <div class="col-md-2">
                                        <label for="export_iva"><?php echo $this->lang->line("export_iva"); ?></label>
                                        <!-- <div class="input-group">
                                            <label class="control-label" id="lblTotalTaxAmountFumigation"></label>
                                        </div> -->
                                        <input type="number" step="any" id="lblTotalTaxAmountFumigation" name="lblTotalTaxAmountFumigation" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_iva"); ?>" />
                                    </div>

                                    <div class="col-md-2">
                                        <label for="export_retefuente"><?php echo $this->lang->line("export_retefuente"); ?></label>
                                        <!-- <div class="input-group">
                                            <label class="control-label" id="lblAllowanceAmountFumigation"></label>
                                        </div> -->
                                        <input type="number" step="any" id="lblAllowanceAmountFumigation" name="lblAllowanceAmountFumigation" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_retefuente"); ?>" />
                                    </div>

                                    <div class="col-md-3">
                                        <label for="export_total_payable"><?php echo $this->lang->line("export_total_payable"); ?></label>
                                        <!-- <div class="input-group">
                                            <label class="control-label" id="lblPayableAmountFumigation"></label>
                                        </div> -->
                                        <input type="number" step="any" id="lblPayableAmountFumigation" name="lblPayableAmountFumigation" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_total_payable"); ?>" />
                                    </div>

                                    <div class="col-md-3">
                                        <label for="total_container_value"><?php echo $this->lang->line("total_container_value"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="lblContainerValueFumigation"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="divPdfFumigation">
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label for="invoice_number_fumigation"><?php echo $this->lang->line("invoice_number"); ?></label>
                                        <input type="text" id="invoice_number_fumigation" name="invoice_number_fumigation" class="form-control text-uppercase" value="<?php echo isset($exportDocumentsFumigation[0]->invoice_no) ? $exportDocumentsFumigation[0]->invoice_no : ''; ?>" placeholder="<?php echo $this->lang->line("invoice_number"); ?>" />
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

                                    <div class="col-md-3">
                                        <label for="issued_date_fumigation"><?php echo $this->lang->line("issued_date"); ?></label>
                                        <input type="datetime-local" id="issued_date_fumigation" name="issued_date_fumigation" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("issued_date"); ?>" />
                                        <input type="text" id="formatted_date_fumigation" class="form-control text-uppercase" placeholder="DD/MM/YYYY HH:MM AM/PM" readonly style="display: none;" />
                                        <label id="error-issued_date_fumigation" class="error-text"><?php echo $this->lang->line("error_issued_date"); ?></label>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-2">
                                        <label for="export_subtotal"><?php echo $this->lang->line("export_subtotal"); ?></label>
                                        <input type="number" step="any" id="subtotal_fumigation" name="subtotal_fumigation" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("exfumigation_subtotal"); ?>" />
                                        <label id="error-subtotal_fumigation" class="error-text"><?php echo $this->lang->line("error_value"); ?></label>
                                    </div>

                                    <div class="col-md-2">
                                        <label for="export_iva"><?php echo $this->lang->line("export_iva"); ?></label>
                                        <input type="number" step="any" id="iva_fumigation" name="iva_fumigation" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("exfumigation_iva"); ?>" />
                                        <label id="error-iva_fumigation" class="error-text"><?php echo $this->lang->line("error_value"); ?></label>
                                    </div>

                                    <div class="col-md-2">
                                        <label for="export_retefuente"><?php echo $this->lang->line("export_retefuente"); ?></label>
                                        <input type="number" step="any" id="retefuente_fumigation" name="retefuente_fumigation" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("exfumigation_retefuente"); ?>" />
                                        <label id="error-retefuente_fumigation" class="error-text"><?php echo $this->lang->line("error_value"); ?></label>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="export_total_payable"><?php echo $this->lang->line("export_total_payable"); ?></label>
                                        <input type="number" step="any" id="payable_fumigation" name="payable_fumigation" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("exfumigation_total_payable"); ?>" />
                                        <label id="error-payable_fumigation" class="error-text"><?php echo $this->lang->line("error_value"); ?></label>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="container_value_fumigation"><?php echo $this->lang->line("total_container_value"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="container_value_fumigation">0</label>
                                        </div>
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
                                            <input type="number" step="any" value="<?php echo 0; ?>" class="form-control text-uppercase" id="fumigation_container_value" name="fumigation_container_value[<?php echo $containerdetail->dispatch_id; ?>]" />
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>

                            <div class="row mb-3 mt-4">
                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-danger col-md-2" style="margin-right: 5px;" id="btnResetFumigation" name="btnResetFumigation"><?php echo $this->lang->line('reset'); ?></button>
                                    <button type="button" class="btn btn-primary col-md-2" id="btnSaveFumigation" name="btnSaveFumigation"><?php echo $this->lang->line('save'); ?></button>
                                </div>
                            </div>

                            <div id="divInvoiceListFumigation">
                                <div class="card-header">
                                    <h5 class="mb-0"><?php echo $this->lang->line("invoice_list"); ?></h5>
                                </div>
                                <div style="overflow-x: auto;">
                                    <table class="datatables-demo table table-striped table-bordered" id="xin_table_invoice_fumigation" style="width: 100% !important;">
                                        <thead>
                                            <tr>
                                                <th><?php echo $this->lang->line("action"); ?></th>
                                                <th><?php echo $this->lang->line("invoice_no"); ?></th>
                                                <th><?php echo $this->lang->line("supplier_name"); ?></th>
                                                <th><?php echo $this->lang->line("invoice_date"); ?></th>
                                                <th><?php echo $this->lang->line("export_subtotal"); ?></th>
                                                <th><?php echo $this->lang->line("export_iva"); ?></th>
                                                <th><?php echo $this->lang->line("export_retefuente"); ?></th>
                                                <th><?php echo $this->lang->line("export_total_payable"); ?></th>
                                                <th><?php echo $this->lang->line("container_value"); ?></th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="nav-coteros" role="tabpanel" aria-labelledby="nav-coteros-tab">
                            <div class="row mb-3">
                                <div class="col-md-6 mb-2">
                                    <label for="fileUploadDoc_Coteros"><?php echo $this->lang->line('upload_document'); ?></label>
                                    <input name="fileUploadDoc_Coteros" type="file" accept=".xml,.pdf" id="fileUploadDoc_Coteros" onchange="loadFileCoteros(event)" class="form-control">
                                    <label id="error-selectdoccoteros" class="error-text"><?php echo $this->lang->line('error_select_document'); ?></label>
                                </div>
                            </div>

                            <div id="divXmlCoteros">

                                <div class="row mb-3">

                                    <div class="col-md-2">
                                        <label for="invoice_number"><?php echo $this->lang->line("invoice_number"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="lblInvoiceNoCoteros"></label>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="supplier_name"><?php echo $this->lang->line("supplier_name") . " - " . $this->lang->line("supplier_id"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="lblSupplierNameCoteros"></label>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label for="issued_date"><?php echo $this->lang->line("issued_date"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="lblIssuedDateCoteros"></label>
                                        </div>
                                    </div>

                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-2">
                                        <label for="export_subtotal"><?php echo $this->lang->line("export_subtotal"); ?></label>
                                        <!-- <div class="input-group">
                                            <label class="control-label" id="lblTotalAmountCoteros"></label>
                                        </div> -->
                                        <input type="number" step="any" id="lblTotalAmountCoteros" name="lblTotalAmountCoteros" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_subtotal"); ?>" />
                                    </div>

                                    <div class="col-md-2">
                                        <label for="export_iva"><?php echo $this->lang->line("export_iva"); ?></label>
                                        <!-- <div class="input-group">
                                            <label class="control-label" id="lblTotalTaxAmountCoteros"></label>
                                        </div> -->
                                        <input type="number" step="any" id="lblTotalTaxAmountCoteros" name="lblTotalTaxAmountCoteros" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_iva"); ?>" />
                                    </div>

                                    <div class="col-md-2">
                                        <label for="export_retefuente"><?php echo $this->lang->line("export_retefuente"); ?></label>
                                        <!-- <div class="input-group">
                                            <label class="control-label" id="lblAllowanceAmountCoteros"></label>
                                        </div> -->
                                        <input type="number" step="any" id="lblAllowanceAmountCoteros" name="lblAllowanceAmountCoteros" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_retefuente"); ?>" />
                                    </div>

                                    <div class="col-md-3">
                                        <label for="export_total_payable"><?php echo $this->lang->line("export_total_payable"); ?></label>
                                        <!-- <div class="input-group">
                                            <label class="control-label" id="lblPayableAmountCoteros"></label>
                                        </div> -->
                                        <input type="number" step="any" id="lblPayableAmountCoteros" name="lblPayableAmountCoteros" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_total_payable"); ?>" />
                                    </div>

                                    <div class="col-md-3">
                                        <label for="total_container_value"><?php echo $this->lang->line("total_container_value"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="lblContainerValueCoteros"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="divPdfCoteros">
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label for="invoice_number_coteros"><?php echo $this->lang->line("invoice_number"); ?></label>
                                        <input type="text" id="invoice_number_coteros" name="invoice_number_coteros" class="form-control text-uppercase" value="<?php echo isset($exportDocumentsCoteros[0]->invoice_no) ? $exportDocumentsCoteros[0]->invoice_no : ''; ?>" placeholder="<?php echo $this->lang->line("invoice_number"); ?>" />
                                        <label id="error-invoice_no_coteros" class="error-text"><?php echo $this->lang->line("error_invoice_no"); ?></label>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="supplier_name_coteros"><?php echo $this->lang->line("supplier_name"); ?></label>
                                        <select class="form-control" name="supplier_name_coteros" id="supplier_name_coteros" data-plugin="select_erp">
                                            <option value="0"><?php echo $this->lang->line("select"); ?></option>
                                            <?php foreach ($exportSuppliersCoteros as $supplier) { ?>
                                                <option value="<?php echo $supplier->id; ?>"><?php echo $supplier->supplier_name . " - " . $supplier->supplier_id; ?></option>
                                            <?php } ?>
                                        </select>
                                        <label id="error-supplier_name_coteros" class="error-text"><?php echo $this->lang->line("error_supplier"); ?></label>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="issued_date_coteros"><?php echo $this->lang->line("issued_date"); ?></label>
                                        <input type="datetime-local" id="issued_date_coteros" name="issued_date_coteros" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("issued_date"); ?>" />
                                        <input type="text" id="formatted_date_coteros" class="form-control text-uppercase" placeholder="DD/MM/YYYY HH:MM AM/PM" readonly style="display: none;" />
                                        <label id="error-issued_date_coteros" class="error-text"><?php echo $this->lang->line("error_issued_date"); ?></label>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-2">
                                        <label for="export_subtotal"><?php echo $this->lang->line("export_subtotal"); ?></label>
                                        <input type="number" step="any" id="subtotal_coteros" name="subtotal_coteros" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("excoteros_subtotal"); ?>" />
                                        <label id="error-subtotal_coteros" class="error-text"><?php echo $this->lang->line("error_value"); ?></label>
                                    </div>

                                    <div class="col-md-2">
                                        <label for="export_iva"><?php echo $this->lang->line("export_iva"); ?></label>
                                        <input type="number" step="any" id="iva_coteros" name="iva_coteros" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("excoteros_iva"); ?>" />
                                        <label id="error-iva_coteros" class="error-text"><?php echo $this->lang->line("error_value"); ?></label>
                                    </div>

                                    <div class="col-md-2">
                                        <label for="export_retefuente"><?php echo $this->lang->line("export_retefuente"); ?></label>
                                        <input type="number" step="any" id="retefuente_coteros" name="retefuente_coteros" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("excoteros_retefuente"); ?>" />
                                        <label id="error-retefuente_coteros" class="error-text"><?php echo $this->lang->line("error_value"); ?></label>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="export_total_payable"><?php echo $this->lang->line("export_total_payable"); ?></label>
                                        <input type="number" step="any" id="payable_coteros" name="payable_coteros" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("excoteros_total_payable"); ?>" />
                                        <label id="error-payable_coteros" class="error-text"><?php echo $this->lang->line("error_value"); ?></label>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="container_value_coteros"><?php echo $this->lang->line("total_container_value"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="container_value_coteros">0</label>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div id="divContainersCoteros">

                                <div class="row mb-3">
                                    <label class="col-md-3 col-form-label header-profile-menu1 fontsize" for="continerNo_Coteros"><?php echo $this->lang->line('container_number'); ?></label>
                                    <label class="col-md-3 col-form-label header-profile-menu1 fontsize" for="totalPieces_Coteros"><?php echo $this->lang->line('total_no_of_pieces'); ?></label>
                                    <label class="col-md-3 col-form-label header-profile-menu1 fontsize" for="totalVolume_Coteros"><?php echo $this->lang->line('total_volume'); ?></label>
                                    <label class="col-md-3 col-form-label header-profile-menu1 fontsize" for="containerValue_Coteros"><?php echo $this->lang->line('container_value'); ?></label>
                                </div>

                                <?php foreach ($containerDetails as $containerdetail) { ?>

                                    <div class="row mb-3">

                                        <input type="hidden" id="hdnDispatchIdCoteros" name="dispatchid_coteros[<?php echo $containerdetail->dispatch_id; ?>]" value="<?php echo $containerdetail->dispatch_id; ?>">

                                        <label class="col-md-3 lbl-font header-profile-menu1 fontsize" for="lblContainerCoteros" name="containerNumberCoteros[]" value="<?php echo $containerdetail->container_number; ?>">
                                            <?php echo strtoupper($containerdetail->container_number); ?>
                                        </label>

                                        <label class="col-md-3 col-form-label lbl-font header-profile-menu1 fontsize" for="lblContainer1Coteros" name="containerNumber1Coteros[]" value="<?php echo $containerdetail->total_pieces; ?>">
                                            <?php echo $containerdetail->total_pieces + 0; ?>
                                        </label>

                                        <label class="col-md-3 col-form-label lbl-font header-profile-menu1 fontsize" for="lblContainer2Coteros" name="containerNumber2Coteros[]" value="<?php echo $containerdetail->total_volume; ?>">
                                            <?php echo sprintf("%0.3f", $containerdetail->total_volume + 0); ?>
                                        </label>

                                        <div class="col-md-3">
                                            <input type="number" step="any" value="<?php echo 0; ?>" class="form-control text-uppercase" id="coteros_container_value" name="coteros_container_value[<?php echo $containerdetail->dispatch_id; ?>]" />
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>

                            <div class="row mb-3 mt-4">
                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-danger col-md-2" style="margin-right: 5px;" id="btnResetCoteros" name="btnResetCoteros"><?php echo $this->lang->line('reset'); ?></button>
                                    <button type="button" class="btn btn-primary col-md-2" id="btnSaveCoteros" name="btnSaveCoteros"><?php echo $this->lang->line('save'); ?></button>
                                </div>
                            </div>

                            <div id="divInvoiceListCoteros">
                                <div class="card-header">
                                    <h5 class="mb-0"><?php echo $this->lang->line("invoice_list"); ?></h5>
                                </div>
                                <div style="overflow-x: auto;">
                                    <table class="datatables-demo table table-striped table-bordered" id="xin_table_invoice_coteros" style="width: 100% !important;">
                                        <thead>
                                            <tr>
                                                <th><?php echo $this->lang->line("action"); ?></th>
                                                <th><?php echo $this->lang->line("invoice_no"); ?></th>
                                                <th><?php echo $this->lang->line("supplier_name"); ?></th>
                                                <th><?php echo $this->lang->line("invoice_date"); ?></th>
                                                <th><?php echo $this->lang->line("export_subtotal"); ?></th>
                                                <th><?php echo $this->lang->line("export_iva"); ?></th>
                                                <th><?php echo $this->lang->line("export_retefuente"); ?></th>
                                                <th><?php echo $this->lang->line("export_total_payable"); ?></th>
                                                <th><?php echo $this->lang->line("container_value"); ?></th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="nav-phyto" role="tabpanel" aria-labelledby="nav-phyto-tab">
                            <div class="row mb-3">
                                <div class="col-md-6 mb-2">
                                    <label for="fileUploadDoc_Phyto"><?php echo $this->lang->line('upload_document'); ?></label>
                                    <input name="fileUploadDoc_Phyto" type="file" accept=".xml,.pdf" id="fileUploadDoc_Phyto" onchange="loadFilePhyto(event)" class="form-control">
                                    <label id="error-selectdocphyto" class="error-text"><?php echo $this->lang->line('error_select_document'); ?></label>
                                </div>
                            </div>

                            <div id="divXmlPhyto">

                                <div class="row mb-3">

                                    <div class="col-md-2">
                                        <label for="invoice_number"><?php echo $this->lang->line("invoice_number"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="lblInvoiceNoPhyto"></label>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="supplier_name"><?php echo $this->lang->line("supplier_name") . " - " . $this->lang->line("supplier_id"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="lblSupplierNamePhyto"></label>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label for="issued_date"><?php echo $this->lang->line("issued_date"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="lblIssuedDatePhyto"></label>
                                        </div>
                                    </div>

                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-2">
                                        <label for="export_subtotal"><?php echo $this->lang->line("export_subtotal"); ?><i class="fa fa-info-circle text-primary ms-2"
                                                style="cursor:pointer;" id="infoValueBeforeTaxPhyto" title="Select value before tax"></i></label>
                                        <!-- <div class="input-group">
                                            <label class="control-label" id="lblTotalAmountPhyto"></label>
                                        </div> -->
                                        <input type="number" step="any" id="lblTotalAmountPhyto" name="lblTotalAmountPhyto" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_subtotal"); ?>" />
                                    </div>

                                    <div class="col-md-2">
                                        <label for="export_iva"><?php echo $this->lang->line("export_iva"); ?></label>
                                        <!-- <div class="input-group">
                                            <label class="control-label" id="lblTotalTaxAmountPhyto"></label>
                                        </div> -->
                                        <input type="number" step="any" id="lblTotalTaxAmountPhyto" name="lblTotalTaxAmountPhyto" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_iva"); ?>" />
                                    </div>

                                    <div class="col-md-2">
                                        <label for="export_retefuente"><?php echo $this->lang->line("export_retefuente"); ?></label>
                                        <!-- <div class="input-group">
                                            <label class="control-label" id="lblAllowanceAmountPhyto"></label>
                                        </div> -->
                                        <input type="number" step="any" id="lblAllowanceAmountPhyto" name="lblAllowanceAmountPhyto" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_retefuente"); ?>" />
                                    </div>

                                    <div class="col-md-3">
                                        <label for="export_total_payable"><?php echo $this->lang->line("export_total_payable"); ?></label>
                                        <!-- <div class="input-group">
                                            <label class="control-label" id="lblPayableAmountPhyto"></label>
                                        </div> -->
                                        <input type="number" step="any" id="lblPayableAmountPhyto" name="lblPayableAmountPhyto" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_total_payable"); ?>" />
                                    </div>

                                    <div class="col-md-3">
                                        <label for="total_container_value"><?php echo $this->lang->line("total_container_value"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="lblContainerValuePhyto"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="divPdfPhyto">
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label for="invoice_number_phyto"><?php echo $this->lang->line("invoice_number"); ?></label>
                                        <input type="text" id="invoice_number_phyto" name="invoice_number_phyto" class="form-control text-uppercase" value="<?php echo isset($exportDocumentsPhyto[0]->invoice_no) ? $exportDocumentsPhyto[0]->invoice_no : ''; ?>" placeholder="<?php echo $this->lang->line("invoice_number"); ?>" />
                                        <label id="error-invoice_no_phyto" class="error-text"><?php echo $this->lang->line("error_invoice_no"); ?></label>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="supplier_name_phyto"><?php echo $this->lang->line("supplier_name"); ?></label>
                                        <select class="form-control" name="supplier_name_phyto" id="supplier_name_phyto" data-plugin="select_erp">
                                            <option value="0"><?php echo $this->lang->line("select"); ?></option>
                                            <?php foreach ($exportSuppliersPhyto as $supplier) { ?>
                                                <option value="<?php echo $supplier->id; ?>"><?php echo $supplier->supplier_name . " - " . $supplier->supplier_id; ?></option>
                                            <?php } ?>
                                        </select>
                                        <label id="error-supplier_name_phyto" class="error-text"><?php echo $this->lang->line("error_supplier"); ?></label>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="issued_date_phyto"><?php echo $this->lang->line("issued_date"); ?></label>
                                        <input type="datetime-local" id="issued_date_phyto" name="issued_date_phyto" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("issued_date"); ?>" />
                                        <input type="text" id="formatted_date_phyto" class="form-control text-uppercase" placeholder="DD/MM/YYYY HH:MM AM/PM" readonly style="display: none;" />
                                        <label id="error-issued_date_phyto" class="error-text"><?php echo $this->lang->line("error_issued_date"); ?></label>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-2">
                                        <label for="export_subtotal"><?php echo $this->lang->line("export_subtotal"); ?></label>
                                        <input type="number" step="any" id="subtotal_phyto" name="subtotal_phyto" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("exphyto_subtotal"); ?>" />
                                        <label id="error-subtotal_phyto" class="error-text"><?php echo $this->lang->line("error_value"); ?></label>
                                    </div>

                                    <div class="col-md-2">
                                        <label for="export_iva"><?php echo $this->lang->line("export_iva"); ?></label>
                                        <input type="number" step="any" id="iva_phyto" name="iva_phyto" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("exphyto_iva"); ?>" />
                                        <label id="error-iva_phyto" class="error-text"><?php echo $this->lang->line("error_value"); ?></label>
                                    </div>

                                    <div class="col-md-2">
                                        <label for="export_retefuente"><?php echo $this->lang->line("export_retefuente"); ?></label>
                                        <input type="number" step="any" id="retefuente_phyto" name="retefuente_phyto" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("exphyto_retefuente"); ?>" />
                                        <label id="error-retefuente_phyto" class="error-text"><?php echo $this->lang->line("error_value"); ?></label>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="export_total_payable"><?php echo $this->lang->line("export_total_payable"); ?></label>
                                        <input type="number" step="any" id="payable_phyto" name="payable_phyto" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("exphyto_total_payable"); ?>" />
                                        <label id="error-payable_phyto" class="error-text"><?php echo $this->lang->line("error_value"); ?></label>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="container_value_phyto"><?php echo $this->lang->line("total_container_value"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="container_value_phyto">0</label>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div id="divContainersPhyto">

                                <div class="row mb-3">
                                    <label class="col-md-3 col-form-label header-profile-menu1 fontsize" for="continerNo_Phyto"><?php echo $this->lang->line('container_number'); ?></label>
                                    <label class="col-md-3 col-form-label header-profile-menu1 fontsize" for="totalPieces_Phyto"><?php echo $this->lang->line('total_no_of_pieces'); ?></label>
                                    <label class="col-md-3 col-form-label header-profile-menu1 fontsize" for="totalVolume_Phyto"><?php echo $this->lang->line('total_volume'); ?></label>
                                    <label class="col-md-3 col-form-label header-profile-menu1 fontsize" for="containerValue_Phyto"><?php echo $this->lang->line('container_value'); ?></label>
                                </div>

                                <?php foreach ($containerDetails as $containerdetail) { ?>

                                    <div class="row mb-3">

                                        <input type="hidden" id="hdnDispatchIdPhyto" name="dispatchid_phyto[<?php echo $containerdetail->dispatch_id; ?>]" value="<?php echo $containerdetail->dispatch_id; ?>">

                                        <label class="col-md-3 lbl-font header-profile-menu1 fontsize" for="lblContainerPhyto" name="containerNumberPhyto[]" value="<?php echo $containerdetail->container_number; ?>">
                                            <?php echo strtoupper($containerdetail->container_number); ?>
                                        </label>

                                        <label class="col-md-3 col-form-label lbl-font header-profile-menu1 fontsize" for="lblContainer1Phyto" name="containerNumber1Phyto[]" value="<?php echo $containerdetail->total_pieces; ?>">
                                            <?php echo $containerdetail->total_pieces + 0; ?>
                                        </label>

                                        <label class="col-md-3 col-form-label lbl-font header-profile-menu1 fontsize" for="lblContainer2Phyto" name="containerNumber2Phyto[]" value="<?php echo $containerdetail->total_volume; ?>">
                                            <?php echo sprintf("%0.3f", $containerdetail->total_volume + 0); ?>
                                        </label>

                                        <div class="col-md-3">
                                            <input type="number" step="any" value="<?php echo 0; ?>" class="form-control text-uppercase" id="phyto_container_value" name="phyto_container_value[<?php echo $containerdetail->dispatch_id; ?>]" />
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>

                            <div class="row mb-3 mt-4">
                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-danger col-md-2" style="margin-right: 5px;" id="btnResetPhyto" name="btnResetPhyto"><?php echo $this->lang->line('reset'); ?></button>
                                    <button type="button" class="btn btn-primary col-md-2" id="btnSavePhyto" name="btnSavePhyto"><?php echo $this->lang->line('save'); ?></button>
                                </div>
                            </div>

                            <div id="divInvoiceListPhyto">
                                <div class="card-header">
                                    <h5 class="mb-0"><?php echo $this->lang->line("invoice_list"); ?></h5>
                                </div>
                                <div style="overflow-x: auto;">
                                    <table class="datatables-demo table table-striped table-bordered" id="xin_table_invoice_phyto" style="width: 100% !important;">
                                        <thead>
                                            <tr>
                                                <th><?php echo $this->lang->line("action"); ?></th>
                                                <th><?php echo $this->lang->line("invoice_no"); ?></th>
                                                <th><?php echo $this->lang->line("supplier_name"); ?></th>
                                                <th><?php echo $this->lang->line("invoice_date"); ?></th>
                                                <th><?php echo $this->lang->line("export_subtotal"); ?></th>
                                                <th><?php echo $this->lang->line("export_iva"); ?></th>
                                                <th><?php echo $this->lang->line("export_retefuente"); ?></th>
                                                <th><?php echo $this->lang->line("export_total_payable"); ?></th>
                                                <th><?php echo $this->lang->line("container_value"); ?></th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="nav-incentives" role="tabpanel" aria-labelledby="nav-incentives-tab">
                            <div class="row mb-3">
                                <div class="col-md-6 mb-2">
                                    <label for="fileUploadDoc_Incentives"><?php echo $this->lang->line('upload_document'); ?></label>
                                    <input name="fileUploadDoc_Incentives" type="file" accept=".xml,.pdf" id="fileUploadDoc_Incentives" onchange="loadFileIncentives(event)" class="form-control">
                                    <label id="error-selectdocincentives" class="error-text"><?php echo $this->lang->line('error_select_document'); ?></label>
                                </div>
                            </div>

                            <div id="divXmlIncentives">

                                <div class="row mb-3">

                                    <div class="col-md-2">
                                        <label for="invoice_number"><?php echo $this->lang->line("invoice_number"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="lblInvoiceNoIncentives"></label>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="supplier_name"><?php echo $this->lang->line("supplier_name") . " - " . $this->lang->line("supplier_id"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="lblSupplierNameIncentives"></label>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label for="issued_date"><?php echo $this->lang->line("issued_date"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="lblIssuedDateIncentives"></label>
                                        </div>
                                    </div>

                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-2">
                                        <label for="export_subtotal"><?php echo $this->lang->line("export_subtotal"); ?></label>
                                        <!-- <div class="input-group">
                                            <label class="control-label" id="lblTotalAmountIncentives"></label>
                                        </div> -->
                                        <input type="number" step="any" id="lblTotalAmountIncentives" name="lblTotalAmountIncentives" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_subtotal"); ?>" />
                                    </div>

                                    <div class="col-md-2">
                                        <label for="export_iva"><?php echo $this->lang->line("export_iva"); ?></label>
                                        <!-- <div class="input-group">
                                            <label class="control-label" id="lblTotalTaxAmountIncentives"></label>
                                        </div> -->
                                        <input type="number" step="any" id="lblTotalTaxAmountIncentives" name="lblTotalTaxAmountIncentives" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_iva"); ?>" />
                                    </div>

                                    <div class="col-md-2">
                                        <label for="export_retefuente"><?php echo $this->lang->line("export_retefuente"); ?></label>
                                        <!-- <div class="input-group">
                                            <label class="control-label" id="lblAllowanceAmountIncentives"></label>
                                        </div> -->
                                        <input type="number" step="any" id="lblAllowanceAmountIncentives" name="lblAllowanceAmountIncentives" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_retefuente"); ?>" />
                                    </div>

                                    <div class="col-md-3">
                                        <label for="export_total_payable"><?php echo $this->lang->line("export_total_payable"); ?></label>
                                        <!-- <div class="input-group">
                                            <label class="control-label" id="lblPayableAmountIncentives"></label>
                                        </div> -->
                                        <input type="number" step="any" id="lblPayableAmountIncentives" name="lblPayableAmountIncentives" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_total_payable"); ?>" />
                                    </div>

                                    <div class="col-md-3">
                                        <label for="total_container_value"><?php echo $this->lang->line("total_container_value"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="lblContainerValueIncentives"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="divPdfIncentives">
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label for="invoice_number_incentives"><?php echo $this->lang->line("invoice_number"); ?></label>
                                        <input type="text" id="invoice_number_incentives" name="invoice_number_incentives" class="form-control text-uppercase" value="<?php echo isset($exportDocumentsIncentives[0]->invoice_no) ? $exportDocumentsIncentives[0]->invoice_no : ''; ?>" placeholder="<?php echo $this->lang->line("invoice_number"); ?>" />
                                        <label id="error-invoice_no_incentives" class="error-text"><?php echo $this->lang->line("error_invoice_no"); ?></label>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="supplier_name_incentives"><?php echo $this->lang->line("supplier_name"); ?></label>
                                        <select class="form-control" name="supplier_name_incentives" id="supplier_name_incentives" data-plugin="select_erp">
                                            <option value="0"><?php echo $this->lang->line("select"); ?></option>
                                            <?php foreach ($exportSuppliersIncentives as $supplier) { ?>
                                                <option value="<?php echo $supplier->id; ?>"><?php echo $supplier->supplier_name . " - " . $supplier->supplier_id; ?></option>
                                            <?php } ?>
                                        </select>
                                        <label id="error-supplier_name_incentives" class="error-text"><?php echo $this->lang->line("error_supplier"); ?></label>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="issued_date_incentives"><?php echo $this->lang->line("issued_date"); ?></label>
                                        <input type="datetime-local" id="issued_date_incentives" name="issued_date_incentives" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("issued_date"); ?>" />
                                        <input type="text" id="formatted_date_incentives" class="form-control text-uppercase" placeholder="DD/MM/YYYY HH:MM AM/PM" readonly style="display: none;" />
                                        <label id="error-issued_date_incentives" class="error-text"><?php echo $this->lang->line("error_issued_date"); ?></label>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-2">
                                        <label for="export_subtotal"><?php echo $this->lang->line("export_subtotal"); ?></label>
                                        <input type="number" step="any" id="subtotal_incentives" name="subtotal_incentives" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("exincentives_subtotal"); ?>" />
                                        <label id="error-subtotal_incentives" class="error-text"><?php echo $this->lang->line("error_value"); ?></label>
                                    </div>

                                    <div class="col-md-2">
                                        <label for="export_iva"><?php echo $this->lang->line("export_iva"); ?></label>
                                        <input type="number" step="any" id="iva_incentives" name="iva_incentives" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("exincentives_iva"); ?>" />
                                        <label id="error-iva_incentives" class="error-text"><?php echo $this->lang->line("error_value"); ?></label>
                                    </div>

                                    <div class="col-md-2">
                                        <label for="export_retefuente"><?php echo $this->lang->line("export_retefuente"); ?></label>
                                        <input type="number" step="any" id="retefuente_incentives" name="retefuente_incentives" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("exincentives_retefuente"); ?>" />
                                        <label id="error-retefuente_incentives" class="error-text"><?php echo $this->lang->line("error_value"); ?></label>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="export_total_payable"><?php echo $this->lang->line("export_total_payable"); ?></label>
                                        <input type="number" step="any" id="payable_incentives" name="payable_incentives" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("exincentives_total_payable"); ?>" />
                                        <label id="error-payable_incentives" class="error-text"><?php echo $this->lang->line("error_value"); ?></label>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="container_value_incentives"><?php echo $this->lang->line("total_container_value"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="container_value_incentives">0</label>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div id="divContainersIncentives">

                                <div class="row mb-3">
                                    <label class="col-md-3 col-form-label header-profile-menu1 fontsize" for="continerNo_Incentives"><?php echo $this->lang->line('container_number'); ?></label>
                                    <label class="col-md-3 col-form-label header-profile-menu1 fontsize" for="totalPieces_Incentives"><?php echo $this->lang->line('total_no_of_pieces'); ?></label>
                                    <label class="col-md-3 col-form-label header-profile-menu1 fontsize" for="totalVolume_Incentives"><?php echo $this->lang->line('total_volume'); ?></label>
                                    <label class="col-md-3 col-form-label header-profile-menu1 fontsize" for="containerValue_Incentives"><?php echo $this->lang->line('container_value'); ?></label>
                                </div>

                                <?php foreach ($containerDetails as $containerdetail) { ?>

                                    <div class="row mb-3">

                                        <input type="hidden" id="hdnDispatchIdIncentives" name="dispatchid_incentives[<?php echo $containerdetail->dispatch_id; ?>]" value="<?php echo $containerdetail->dispatch_id; ?>">

                                        <label class="col-md-3 lbl-font header-profile-menu1 fontsize" for="lblContainerIncentives" name="containerNumberIncentives[]" value="<?php echo $containerdetail->container_number; ?>">
                                            <?php echo strtoupper($containerdetail->container_number); ?>
                                        </label>

                                        <label class="col-md-3 col-form-label lbl-font header-profile-menu1 fontsize" for="lblContainer1Incentives" name="containerNumber1Incentives[]" value="<?php echo $containerdetail->total_pieces; ?>">
                                            <?php echo $containerdetail->total_pieces + 0; ?>
                                        </label>

                                        <label class="col-md-3 col-form-label lbl-font header-profile-menu1 fontsize" for="lblContainer2Incentives" name="containerNumber2Incentives[]" value="<?php echo $containerdetail->total_volume; ?>">
                                            <?php echo sprintf("%0.3f", $containerdetail->total_volume + 0); ?>
                                        </label>

                                        <div class="col-md-3">
                                            <input type="number" step="any" value="<?php echo 0; ?>" class="form-control text-uppercase" id="incentives_container_value" name="incentives_container_value[<?php echo $containerdetail->dispatch_id; ?>]" />
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>

                            <div class="row mb-3 mt-4">
                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-danger col-md-2" style="margin-right: 5px;" id="btnResetIncentives" name="btnResetIncentives"><?php echo $this->lang->line('reset'); ?></button>
                                    <button type="button" class="btn btn-primary col-md-2" id="btnSaveIncentives" name="btnSaveIncentives"><?php echo $this->lang->line('save'); ?></button>
                                </div>
                            </div>

                            <div id="divInvoiceListIncentives">
                                <div class="card-header">
                                    <h5 class="mb-0"><?php echo $this->lang->line("invoice_list"); ?></h5>
                                </div>
                                <div style="overflow-x: auto;">
                                    <table class="datatables-demo table table-striped table-bordered" id="xin_table_invoice_incentives" style="width: 100% !important;">
                                        <thead>
                                            <tr>
                                                <th><?php echo $this->lang->line("action"); ?></th>
                                                <th><?php echo $this->lang->line("invoice_no"); ?></th>
                                                <th><?php echo $this->lang->line("supplier_name"); ?></th>
                                                <th><?php echo $this->lang->line("invoice_date"); ?></th>
                                                <th><?php echo $this->lang->line("export_subtotal"); ?></th>
                                                <th><?php echo $this->lang->line("export_iva"); ?></th>
                                                <th><?php echo $this->lang->line("export_retefuente"); ?></th>
                                                <th><?php echo $this->lang->line("export_total_payable"); ?></th>
                                                <th><?php echo $this->lang->line("container_value"); ?></th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="nav-remobilization" role="tabpanel" aria-labelledby="nav-remobilization-tab">
                            <div class="row mb-3">
                                <div class="col-md-6 mb-2">
                                    <label for="fileUploadDoc_Remobilization"><?php echo $this->lang->line('upload_document'); ?></label>
                                    <input name="fileUploadDoc_Remobilization" type="file" accept=".xml,.pdf" id="fileUploadDoc_Remobilization" onchange="loadFileRemobilization(event)" class="form-control">
                                    <label id="error-selectdocremobilization" class="error-text"><?php echo $this->lang->line('error_select_document'); ?></label>
                                </div>
                            </div>

                            <div id="divXmlRemobilization">

                                <div class="row mb-3">

                                    <div class="col-md-2">
                                        <label for="invoice_number"><?php echo $this->lang->line("invoice_number"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="lblInvoiceNoRemobilization"></label>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="supplier_name"><?php echo $this->lang->line("supplier_name") . " - " . $this->lang->line("supplier_id"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="lblSupplierNameRemobilization"></label>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label for="issued_date"><?php echo $this->lang->line("issued_date"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="lblIssuedDateRemobilization"></label>
                                        </div>
                                    </div>

                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-2">
                                        <label for="export_subtotal"><?php echo $this->lang->line("export_subtotal"); ?></label>
                                        <!-- <div class="input-group">
                                            <label class="control-label" id="lblTotalAmountRemobilization"></label>
                                        </div> -->
                                        <input type="number" step="any" id="lblTotalAmountRemobilization" name="lblTotalAmountRemobilization" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_subtotal"); ?>" />
                                    </div>

                                    <div class="col-md-2">
                                        <label for="export_iva"><?php echo $this->lang->line("export_iva"); ?></label>
                                        <!-- <div class="input-group">
                                            <label class="control-label" id="lblTotalTaxAmountRemobilization"></label>
                                        </div> -->
                                        <input type="number" step="any" id="lblTotalTaxAmountRemobilization" name="lblTotalTaxAmountRemobilization" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_iva"); ?>" />
                                    </div>

                                    <div class="col-md-2">
                                        <label for="export_retefuente"><?php echo $this->lang->line("export_retefuente"); ?></label>
                                        <!-- <div class="input-group">
                                            <label class="control-label" id="lblAllowanceAmountRemobilization"></label>
                                        </div> -->
                                        <input type="number" step="any" id="lblAllowanceAmountRemobilization" name="lblAllowanceAmountRemobilization" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_retefuente"); ?>" />
                                    </div>

                                    <div class="col-md-3">
                                        <label for="export_total_payable"><?php echo $this->lang->line("export_total_payable"); ?></label>
                                        <!-- <div class="input-group">
                                            <label class="control-label" id="lblPayableAmountRemobilization"></label>
                                        </div> -->
                                        <input type="number" step="any" id="lblPayableAmountRemobilization" name="lblPayableAmountRemobilization" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_total_payable"); ?>" />
                                    </div>

                                    <div class="col-md-3">
                                        <label for="total_container_value"><?php echo $this->lang->line("total_container_value"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="lblContainerValueRemobilization"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="divPdfRemobilization">
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label for="invoice_number_remobilization"><?php echo $this->lang->line("invoice_number"); ?></label>
                                        <input type="text" id="invoice_number_remobilization" name="invoice_number_remobilization" class="form-control text-uppercase" value="<?php echo isset($exportDocumentsRemobilization[0]->invoice_no) ? $exportDocumentsRemobilization[0]->invoice_no : ''; ?>" placeholder="<?php echo $this->lang->line("invoice_number"); ?>" />
                                        <label id="error-invoice_no_remobilization" class="error-text"><?php echo $this->lang->line("error_invoice_no"); ?></label>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="supplier_name_remobilization"><?php echo $this->lang->line("supplier_name"); ?></label>
                                        <select class="form-control" name="supplier_name_remobilization" id="supplier_name_remobilization" data-plugin="select_erp">
                                            <option value="0"><?php echo $this->lang->line("select"); ?></option>
                                            <?php foreach ($exportSuppliersRemobilization as $supplier) { ?>
                                                <option value="<?php echo $supplier->id; ?>"><?php echo $supplier->supplier_name . " - " . $supplier->supplier_id; ?></option>
                                            <?php } ?>
                                        </select>
                                        <label id="error-supplier_name_remobilization" class="error-text"><?php echo $this->lang->line("error_supplier"); ?></label>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="issued_date_remobilization"><?php echo $this->lang->line("issued_date"); ?></label>
                                        <input type="datetime-local" id="issued_date_remobilization" name="issued_date_remobilization" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("issued_date"); ?>" />
                                        <input type="text" id="formatted_date_remobilization" class="form-control text-uppercase" placeholder="DD/MM/YYYY HH:MM AM/PM" readonly style="display: none;" />
                                        <label id="error-issued_date_remobilization" class="error-text"><?php echo $this->lang->line("error_issued_date"); ?></label>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-2">
                                        <label for="export_subtotal"><?php echo $this->lang->line("export_subtotal"); ?></label>
                                        <input type="number" step="any" id="subtotal_remobilization" name="subtotal_remobilization" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("exremobilization_subtotal"); ?>" />
                                        <label id="error-subtotal_remobilization" class="error-text"><?php echo $this->lang->line("error_value"); ?></label>
                                    </div>

                                    <div class="col-md-2">
                                        <label for="export_iva"><?php echo $this->lang->line("export_iva"); ?></label>
                                        <input type="number" step="any" id="iva_remobilization" name="iva_remobilization" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("exremobilization_iva"); ?>" />
                                        <label id="error-iva_remobilization" class="error-text"><?php echo $this->lang->line("error_value"); ?></label>
                                    </div>

                                    <div class="col-md-2">
                                        <label for="export_retefuente"><?php echo $this->lang->line("export_retefuente"); ?></label>
                                        <input type="number" step="any" id="retefuente_remobilization" name="retefuente_remobilization" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("exremobilization_retefuente"); ?>" />
                                        <label id="error-retefuente_remobilization" class="error-text"><?php echo $this->lang->line("error_value"); ?></label>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="export_total_payable"><?php echo $this->lang->line("export_total_payable"); ?></label>
                                        <input type="number" step="any" id="payable_remobilization" name="payable_remobilization" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("exremobilization_total_payable"); ?>" />
                                        <label id="error-payable_remobilization" class="error-text"><?php echo $this->lang->line("error_value"); ?></label>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="container_value_remobilization"><?php echo $this->lang->line("total_container_value"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="container_value_remobilization">0</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="divContainersRemobilization">

                                <div class="row mb-3">
                                    <label class="col-md-3 col-form-label header-profile-menu1 fontsize" for="continerNo_Remobilization"><?php echo $this->lang->line('container_number'); ?></label>
                                    <label class="col-md-3 col-form-label header-profile-menu1 fontsize" for="totalPieces_Remobilization"><?php echo $this->lang->line('total_no_of_pieces'); ?></label>
                                    <label class="col-md-3 col-form-label header-profile-menu1 fontsize" for="totalVolume_Remobilization"><?php echo $this->lang->line('total_volume'); ?></label>
                                    <label class="col-md-3 col-form-label header-profile-menu1 fontsize" for="containerValue_Remobilization"><?php echo $this->lang->line('container_value'); ?></label>
                                </div>

                                <?php foreach ($containerDetails as $containerdetail) { ?>

                                    <div class="row mb-3">

                                        <input type="hidden" id="hdnDispatchIdRemobilization" name="dispatchid_remobilization[<?php echo $containerdetail->dispatch_id; ?>]" value="<?php echo $containerdetail->dispatch_id; ?>">

                                        <label class="col-md-3 lbl-font header-profile-menu1 fontsize" for="lblContainerRemobilization" name="containerNumberRemobilization[]" value="<?php echo $containerdetail->container_number; ?>">
                                            <?php echo strtoupper($containerdetail->container_number); ?>
                                        </label>

                                        <label class="col-md-3 col-form-label lbl-font header-profile-menu1 fontsize" for="lblContainer1Remobilization" name="containerNumber1Remobilization[]" value="<?php echo $containerdetail->total_pieces; ?>">
                                            <?php echo $containerdetail->total_pieces + 0; ?>
                                        </label>

                                        <label class="col-md-3 col-form-label lbl-font header-profile-menu1 fontsize" for="lblContainer2Remobilization" name="containerNumber2Remobilization[]" value="<?php echo $containerdetail->total_volume; ?>">
                                            <?php echo sprintf("%0.3f", $containerdetail->total_volume + 0); ?>
                                        </label>

                                        <div class="col-md-3">
                                            <input type="number" step="any" value="<?php echo 0; ?>" class="form-control text-uppercase" id="remobilization_container_value" name="remobilization_container_value[<?php echo $containerdetail->dispatch_id; ?>]" />
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>

                            <div class="row mb-3 mt-4">
                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-danger col-md-2" style="margin-right: 5px;" id="btnResetRemobilization" name="btnResetRemobilization"><?php echo $this->lang->line('reset'); ?></button>
                                    <button type="button" class="btn btn-primary col-md-2" id="btnSaveRemobilization" name="btnSaveRemobilization"><?php echo $this->lang->line('save'); ?></button>
                                </div>
                            </div>

                            <div id="divInvoiceListRemobilization">
                                <div class="card-header">
                                    <h5 class="mb-0"><?php echo $this->lang->line("invoice_list"); ?></h5>
                                </div>
                                <div style="overflow-x: auto;">
                                    <table class="datatables-demo table table-striped table-bordered" id="xin_table_invoice_remobilization" style="width: 100% !important;">
                                        <thead>
                                            <tr>
                                                <th><?php echo $this->lang->line("action"); ?></th>
                                                <th><?php echo $this->lang->line("invoice_no"); ?></th>
                                                <th><?php echo $this->lang->line("supplier_name"); ?></th>
                                                <th><?php echo $this->lang->line("invoice_date"); ?></th>
                                                <th><?php echo $this->lang->line("export_subtotal"); ?></th>
                                                <th><?php echo $this->lang->line("export_iva"); ?></th>
                                                <th><?php echo $this->lang->line("export_retefuente"); ?></th>
                                                <th><?php echo $this->lang->line("export_total_payable"); ?></th>
                                                <th><?php echo $this->lang->line("container_value"); ?></th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="nav-containercost" role="tabpanel" aria-labelledby="nav-containercost-tab">
                            <div id="divContainersContainer">

                                <div class="row mb-3">
                                    <label class="col-md-3 col-form-label header-profile-menu1 fontsize" for="continerNo_ContainerCost"><?php echo $this->lang->line('container_number'); ?></label>
                                    <label class="col-md-2 col-form-label header-profile-menu1 fontsize" for="totalPieces_ContainerCost"><?php echo $this->lang->line('total_no_of_pieces'); ?></label>
                                    <label class="col-md-2 col-form-label header-profile-menu1 fontsize" for="totalVolume_ContainerCost"><?php echo $this->lang->line('total_volume'); ?></label>
                                    <label class="col-md-2 col-form-label header-profile-menu1 fontsize" for="containerValue_ContainerCost"><?php echo $this->lang->line('container_unit_price'); ?></label>
                                    <label class="col-md-2 col-form-label header-profile-menu1 fontsize" for="containerValue_ContainerCost"><?php echo $this->lang->line('container_trm'); ?></label>
                                </div>

                                <?php foreach ($containerDetails as $containerdetail) { ?>

                                    <div class="row mb-3">

                                        <input type="hidden" id="hdnDispatchIdContainerCost" name="dispatchid_containercost[<?php echo $containerdetail->dispatch_id; ?>]" value="<?php echo $containerdetail->dispatch_id; ?>">

                                        <label class="col-md-3 lbl-font header-profile-menu1 fontsize" for="lblContainerContainerCost" name="containerNumberContainerCost[]" value="<?php echo $containerdetail->container_number; ?>">
                                            <?php echo strtoupper($containerdetail->container_number); ?>
                                        </label>

                                        <label class="col-md-2 col-form-label lbl-font header-profile-menu1 fontsize" for="lblContainer1ContainerCost" name="containerNumber1ContainerCost[]" value="<?php echo $containerdetail->total_pieces; ?>">
                                            <?php echo $containerdetail->total_pieces + 0; ?>
                                        </label>

                                        <label class="col-md-2 col-form-label lbl-font header-profile-menu1 fontsize" for="lblContainer2ContainerCost" name="containerNumber2ContainerCost[]" value="<?php echo $containerdetail->total_volume; ?>">
                                            <?php echo sprintf("%0.3f", $containerdetail->total_volume + 0); ?>
                                        </label>

                                        <?php $containerCostValue = 0;
                                        $containerTrmValue = 0;
                                        foreach ($exportContainerCosts as $containercost) { ?>
                                            <?php if ($containercost->dispatch_id == $containerdetail->dispatch_id) {
                                                $containerCostValue = $containercost->unit_price + 0;
                                                $containerTrmValue = $containercost->exchange_rate + 0;
                                                break;
                                            } ?>
                                        <?php } ?>

                                        <div class="col-md-2">
                                            <input type="number" step="any" value="<?php echo $containerCostValue + 0; ?>" class="form-control text-uppercase" id="containercost_container_value" name="containercost_container_value[<?php echo $containerdetail->dispatch_id; ?>]" />
                                        </div>

                                        <div class="col-md-2">
                                            <input type="number" step="any" value="<?php echo $containerTrmValue + 0; ?>" class="form-control text-uppercase" id="containercosttrm_container_value" name="containercosttrm_container_value[<?php echo $containerdetail->dispatch_id; ?>]" />
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>

                            <div class="row mb-3 mt-4">
                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-primary col-md-2" id="btnSaveContainerCost" name="btnSaveContainerCost"><?php echo $this->lang->line('save'); ?></button>
                                </div>
                            </div>
                        </div>

                        <!-- <div class="tab-pane fade" id="nav-containerloadingcost" role="tabpanel" aria-labelledby="nav-containerloadingcost-tab">
                            <div id="divContainerLoadingCost">

                                <div class="row mb-3">
                                    <label class="col-md-3 col-form-label header-profile-menu1 fontsize" for="continerNo_ContainerCost"><?php echo $this->lang->line('container_number'); ?></label>
                                    <label class="col-md-2 col-form-label header-profile-menu1 fontsize" for="totalPieces_ContainerCost"><?php echo $this->lang->line('total_no_of_pieces'); ?></label>
                                    <label class="col-md-2 col-form-label header-profile-menu1 fontsize" for="totalVolume_ContainerCost"><?php echo $this->lang->line('total_volume'); ?></label>
                                    <label class="col-md-2 col-form-label header-profile-menu1 fontsize" for="containerValue_ContainerCost"><?php echo $this->lang->line('total_value'); ?></label>
                                </div>

                                <?php foreach ($containerDetails as $containerdetail) { ?>

                                    <div class="row mb-3">

                                        <input type="hidden" id="hdnDispatchIdContainerCost" name="dispatchid_containercost[<?php echo $containerdetail->dispatch_id; ?>]" value="<?php echo $containerdetail->dispatch_id; ?>">

                                        <label class="col-md-3 lbl-font header-profile-menu1 fontsize" for="lblContainerContainerCost" name="containerNumberContainerCost[]" value="<?php echo $containerdetail->container_number; ?>">
                                            <?php echo strtoupper($containerdetail->container_number); ?>
                                        </label>

                                        <label class="col-md-2 col-form-label lbl-font header-profile-menu1 fontsize" for="lblContainer1ContainerCost" name="containerNumber1ContainerCost[]" value="<?php echo $containerdetail->total_pieces; ?>">
                                            <?php echo $containerdetail->total_pieces + 0; ?>
                                        </label>

                                        <label class="col-md-2 col-form-label lbl-font header-profile-menu1 fontsize" for="lblContainer2ContainerCost" name="containerNumber2ContainerCost[]" value="<?php echo $containerdetail->total_volume; ?>">
                                            <?php echo sprintf("%0.3f", $containerdetail->total_volume + 0); ?>
                                        </label>

                                        <?php $containerLoadingValue = 0;
                                        foreach ($exportLoadingCosts as $containerloadingcost) { ?>
                                            <?php if ($containerloadingcost->dispatch_id == $containerdetail->dispatch_id) {
                                                $containerLoadingValue = $containerloadingcost->loading_cost + 0;
                                                break;
                                            } ?>
                                        <?php } ?>

    
                                        <div class="col-md-2">
                                            <input type="number" step="any" value="<?php echo $containerLoadingValue + 0; ?>" class="form-control text-uppercase" id="containercostloading_container_value" name="containercostloading_container_value[<?php echo $containerdetail->dispatch_id; ?>]" />
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>

                            <div class="row mb-3 mt-4">
                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-primary col-md-2" id="btnSaveContainerLoadingCost" name="btnSaveContainerLoadingCost"><?php echo $this->lang->line('save'); ?></button>
                                </div>
                            </div>
                        </div> -->

                        <div class="tab-pane fade" id="nav-containerloadingcost" role="tabpanel" aria-labelledby="nav-containerloadingcost-tab">
                            <div class="row mb-3">
                                <div class="col-md-6 mb-2">
                                    <label for="fileUploadDoc_ContainerLoadingCost"><?php echo $this->lang->line('upload_document'); ?></label>
                                    <input name="fileUploadDoc_ContainerLoadingCost" type="file" accept=".xml,.pdf" id="fileUploadDoc_ContainerLoadingCost" onchange="loadFileContainerLoadingCost(event)" class="form-control">
                                    <label id="error-selectdoccontainerloadingcost" class="error-text"><?php echo $this->lang->line('error_select_document'); ?></label>
                                </div>
                            </div>

                            <div id="divXmlContainerLoadingCost">

                                <div class="row mb-3">

                                    <div class="col-md-2">
                                        <label for="invoice_number"><?php echo $this->lang->line("invoice_number"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="lblInvoiceNoContainerLoadingCost"></label>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="supplier_name"><?php echo $this->lang->line("supplier_name") . " - " . $this->lang->line("supplier_id"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="lblSupplierNameContainerLoadingCost"></label>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label for="issued_date"><?php echo $this->lang->line("issued_date"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="lblIssuedDateContainerLoadingCost"></label>
                                        </div>
                                    </div>

                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-2">
                                        <label for="export_subtotal"><?php echo $this->lang->line("export_subtotal"); ?></label>
                                        <!-- <div class="input-group">
                                            <label class="control-label" id="lblTotalAmountContainerLoadingCost"></label>
                                        </div> -->
                                        <input type="number" step="any" id="lblTotalAmountContainerLoadingCost" name="lblTotalAmountContainerLoadingCost" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_subtotal"); ?>" />
                                    </div>

                                    <div class="col-md-2">
                                        <label for="export_iva"><?php echo $this->lang->line("export_iva"); ?></label>
                                        <!-- <div class="input-group">
                                            <label class="control-label" id="lblTotalTaxAmountContainerLoadingCost"></label>
                                        </div> -->
                                        <input type="number" step="any" id="lblTotalTaxAmountContainerLoadingCost" name="lblTotalTaxAmountContainerLoadingCost" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_iva"); ?>" />
                                    </div>

                                    <div class="col-md-2">
                                        <label for="export_retefuente"><?php echo $this->lang->line("export_retefuente"); ?></label>
                                        <!-- <div class="input-group">
                                            <label class="control-label" id="lblAllowanceAmountContainerLoadingCost"></label>
                                        </div> -->
                                        <input type="number" step="any" id="lblAllowanceAmountContainerLoadingCost" name="lblAllowanceAmountContainerLoadingCost" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_retefuente"); ?>" />
                                    </div>

                                    <div class="col-md-3">
                                        <label for="export_total_payable"><?php echo $this->lang->line("export_total_payable"); ?></label>
                                        <!-- <div class="input-group">
                                            <label class="control-label" id="lblPayableAmountContainerLoadingCost"></label>
                                        </div> -->
                                        <input type="number" step="any" id="lblPayableAmountContainerLoadingCost" name="lblPayableAmountContainerLoadingCost" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_total_payable"); ?>" />
                                    </div>

                                    <div class="col-md-3">
                                        <label for="total_container_value"><?php echo $this->lang->line("total_container_value"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="lblContainerValueContainerLoadingCost"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="divPdfContainerLoadingCost">
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label for="invoice_number_containerloadingcost"><?php echo $this->lang->line("invoice_number"); ?></label>
                                        <input type="text" id="invoice_number_containerloadingcost" name="invoice_number_containerloadingcost" class="form-control text-uppercase" value="<?php echo isset($exportDocumentsContainerLoadingCost[0]->invoice_no) ? $exportDocumentsContainerLoadingCost[0]->invoice_no : ''; ?>" placeholder="<?php echo $this->lang->line("invoice_number"); ?>" />
                                        <label id="error-invoice_no_containerloadingcost" class="error-text"><?php echo $this->lang->line("error_invoice_no"); ?></label>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="supplier_name_containerloadingcost"><?php echo $this->lang->line("supplier_name"); ?></label>
                                        <select class="form-control" name="supplier_name_containerloadingcost" id="supplier_name_containerloadingcost" data-plugin="select_erp">
                                            <option value="0"><?php echo $this->lang->line("select"); ?></option>
                                            <?php foreach ($exportSuppliersContainerLoadingCost as $supplier) { ?>
                                                <option value="<?php echo $supplier->id; ?>"><?php echo $supplier->supplier_name . " - " . $supplier->supplier_id; ?></option>
                                            <?php } ?>
                                        </select>
                                        <label id="error-supplier_name_containerloadingcost" class="error-text"><?php echo $this->lang->line("error_supplier"); ?></label>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="issued_date_containerloadingcost"><?php echo $this->lang->line("issued_date"); ?></label>
                                        <input type="datetime-local" id="issued_date_containerloadingcost" name="issued_date_containerloadingcost" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("issued_date"); ?>" />
                                        <input type="text" id="formatted_date_containerloadingcost" class="form-control text-uppercase" placeholder="DD/MM/YYYY HH:MM AM/PM" readonly style="display: none;" />
                                        <label id="error-issued_date_containerloadingcost" class="error-text"><?php echo $this->lang->line("error_issued_date"); ?></label>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-2">
                                        <label for="export_subtotal"><?php echo $this->lang->line("export_subtotal"); ?></label>
                                        <input type="number" step="any" id="subtotal_containerloadingcost" name="subtotal_containerloadingcost" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("excontainerloadingcost_subtotal"); ?>" />
                                        <label id="error-subtotal_containerloadingcost" class="error-text"><?php echo $this->lang->line("error_value"); ?></label>
                                    </div>

                                    <div class="col-md-2">
                                        <label for="export_iva"><?php echo $this->lang->line("export_iva"); ?></label>
                                        <input type="number" step="any" id="iva_containerloadingcost" name="iva_containerloadingcost" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("excontainerloadingcost_iva"); ?>" />
                                        <label id="error-iva_containerloadingcost" class="error-text"><?php echo $this->lang->line("error_value"); ?></label>
                                    </div>

                                    <div class="col-md-2">
                                        <label for="export_retefuente"><?php echo $this->lang->line("export_retefuente"); ?></label>
                                        <input type="number" step="any" id="retefuente_containerloadingcost" name="retefuente_containerloadingcost" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("excontainerloadingcost_retefuente"); ?>" />
                                        <label id="error-retefuente_containerloadingcost" class="error-text"><?php echo $this->lang->line("error_value"); ?></label>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="export_total_payable"><?php echo $this->lang->line("export_total_payable"); ?></label>
                                        <input type="number" step="any" id="payable_containerloadingcost" name="payable_containerloadingcost" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("excontainerloadingcost_total_payable"); ?>" />
                                        <label id="error-payable_containerloadingcost" class="error-text"><?php echo $this->lang->line("error_value"); ?></label>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="container_value_containerloadingcost"><?php echo $this->lang->line("total_container_value"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="container_value_containerloadingcost">0</label>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div id="divContainersContainerLoadingCost">

                                <div class="row mb-3">
                                    <label class="col-md-3 col-form-label header-profile-menu1 fontsize" for="continerNo_ContainerLoadingCost"><?php echo $this->lang->line('container_number'); ?></label>
                                    <label class="col-md-3 col-form-label header-profile-menu1 fontsize" for="totalPieces_ContainerLoadingCost"><?php echo $this->lang->line('total_no_of_pieces'); ?></label>
                                    <label class="col-md-3 col-form-label header-profile-menu1 fontsize" for="totalVolume_ContainerLoadingCost"><?php echo $this->lang->line('total_volume'); ?></label>
                                    <label class="col-md-3 col-form-label header-profile-menu1 fontsize" for="containerValue_ContainerLoadingCost"><?php echo $this->lang->line('container_value'); ?></label>
                                </div>

                                <?php foreach ($containerDetails as $containerdetail) { ?>

                                    <div class="row mb-3">

                                        <input type="hidden" id="hdnDispatchIdContainerLoadingCost" name="dispatchid_containerloadingcost[<?php echo $containerdetail->dispatch_id; ?>]" value="<?php echo $containerdetail->dispatch_id; ?>">

                                        <label class="col-md-3 lbl-font header-profile-menu1 fontsize" for="lblContainerContainerLoadingCost" name="containerNumberContainerLoadingCost[]" value="<?php echo $containerdetail->container_number; ?>">
                                            <?php echo strtoupper($containerdetail->container_number); ?>
                                        </label>

                                        <label class="col-md-3 col-form-label lbl-font header-profile-menu1 fontsize" for="lblContainer1ContainerLoadingCost" name="containerNumber1ContainerLoadingCost[]" value="<?php echo $containerdetail->total_pieces; ?>">
                                            <?php echo $containerdetail->total_pieces + 0; ?>
                                        </label>

                                        <label class="col-md-3 col-form-label lbl-font header-profile-menu1 fontsize" for="lblContainer2ContainerLoadingCost" name="containerNumber2ContainerLoadingCost[]" value="<?php echo $containerdetail->total_volume; ?>">
                                            <?php echo sprintf("%0.3f", $containerdetail->total_volume + 0); ?>
                                        </label>

                                        <div class="col-md-3">
                                            <input type="number" step="any" value="<?php echo 0; ?>" class="form-control text-uppercase" id="containerloadingcost_container_value" name="containerloadingcost_container_value[<?php echo $containerdetail->dispatch_id; ?>]" />
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>

                            <div class="row mb-3 mt-4">
                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-danger col-md-2" style="margin-right: 5px;" id="btnResetContainerLoadingCost" name="btnResetContainerLoadingCost"><?php echo $this->lang->line('reset'); ?></button>
                                    <button type="button" class="btn btn-primary col-md-2" id="btnSaveContainerLoadingCost" name="btnSaveContainerLoadingCost"><?php echo $this->lang->line('save'); ?></button>
                                </div>
                            </div>

                            <div id="divInvoiceListContainerLoadingCost">
                                <div class="card-header">
                                    <h5 class="mb-0"><?php echo $this->lang->line("invoice_list"); ?></h5>
                                </div>
                                <div style="overflow-x: auto;">
                                    <table class="datatables-demo table table-striped table-bordered" id="xin_table_invoice_containerloadingcost" style="width: 100% !important;">
                                        <thead>
                                            <tr>
                                                <th><?php echo $this->lang->line("action"); ?></th>
                                                <th><?php echo $this->lang->line("invoice_no"); ?></th>
                                                <th><?php echo $this->lang->line("supplier_name"); ?></th>
                                                <th><?php echo $this->lang->line("invoice_date"); ?></th>
                                                <th><?php echo $this->lang->line("export_subtotal"); ?></th>
                                                <th><?php echo $this->lang->line("export_iva"); ?></th>
                                                <th><?php echo $this->lang->line("export_retefuente"); ?></th>
                                                <th><?php echo $this->lang->line("export_total_payable"); ?></th>
                                                <th><?php echo $this->lang->line("container_value"); ?></th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="nav-dhlcost" role="tabpanel" aria-labelledby="nav-dhlcost-tab">
                            <div class="row mb-3">
                                <div class="col-md-6 mb-2">
                                    <label for="fileUploadDoc_Dhlcost"><?php echo $this->lang->line('upload_document'); ?></label>
                                    <input name="fileUploadDoc_Dhlcost" type="file" accept=".xml,.pdf" id="fileUploadDoc_Dhlcost" onchange="loadFileDhlCost(event)" class="form-control">
                                    <label id="error-selectdocdhlcost" class="error-text"><?php echo $this->lang->line('error_select_document'); ?></label>
                                </div>
                            </div>

                            <div id="divXmlDhlCost">

                                <div class="row mb-3">

                                    <div class="col-md-2">
                                        <label for="invoice_number"><?php echo $this->lang->line("invoice_number"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="lblInvoiceNoDhlCost"></label>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="supplier_name"><?php echo $this->lang->line("supplier_name") . " - " . $this->lang->line("supplier_id"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="lblSupplierNameDhlCost"></label>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label for="issued_date"><?php echo $this->lang->line("issued_date"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="lblIssuedDateDhlCost"></label>
                                        </div>
                                    </div>

                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-2">
                                        <label for="export_subtotal"><?php echo $this->lang->line("export_subtotal"); ?></label>
                                        <!-- <div class="input-group">
                                            <label class="control-label" id="lblTotalAmountDhlCost"></label>
                                        </div> -->
                                        <input type="number" step="any" id="lblTotalAmountDhlCost" name="lblTotalAmountDhlCost" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_subtotal"); ?>" />
                                    </div>

                                    <div class="col-md-2">
                                        <label for="export_iva"><?php echo $this->lang->line("export_iva"); ?></label>
                                        <!-- <div class="input-group">
                                            <label class="control-label" id="lblTotalTaxAmountDhlCost"></label>
                                        </div> -->
                                        <input type="number" step="any" id="lblTotalTaxAmountDhlCost" name="lblTotalTaxAmountDhlCost" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_iva"); ?>" />
                                    </div>

                                    <div class="col-md-2">
                                        <label for="export_retefuente"><?php echo $this->lang->line("export_retefuente"); ?></label>
                                        <!-- <div class="input-group">
                                            <label class="control-label" id="lblAllowanceAmountDhlCost"></label>
                                        </div> -->
                                        <input type="number" step="any" id="lblAllowanceAmountDhlCost" name="lblAllowanceAmountDhlCost" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_retefuente"); ?>" />
                                    </div>

                                    <div class="col-md-3">
                                        <label for="export_total_payable"><?php echo $this->lang->line("export_total_payable"); ?></label>
                                        <!-- <div class="input-group">
                                            <label class="control-label" id="lblPayableAmountDhlCost"></label>
                                        </div> -->
                                        <input type="number" step="any" id="lblPayableAmountDhlCost" name="lblPayableAmountDhlCost" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_total_payable"); ?>" />
                                    </div>

                                    <div class="col-md-3">
                                        <label for="total_container_value"><?php echo $this->lang->line("total_container_value"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="lblContainerValueDhlCost"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="divPdfDhlCost">
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label for="invoice_number_dhlcost"><?php echo $this->lang->line("invoice_number"); ?></label>
                                        <input type="text" id="invoice_number_dhlcost" name="invoice_number_dhlcost" class="form-control text-uppercase" value="<?php echo isset($exportDocumentsDhlCost[0]->invoice_no) ? $exportDocumentsDhlCost[0]->invoice_no : ''; ?>" placeholder="<?php echo $this->lang->line("invoice_number"); ?>" />
                                        <label id="error-invoice_no_dhlcost" class="error-text"><?php echo $this->lang->line("error_invoice_no"); ?></label>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="supplier_name_dhlcost"><?php echo $this->lang->line("supplier_name"); ?></label>
                                        <select class="form-control" name="supplier_name_dhlcost" id="supplier_name_dhlcost" data-plugin="select_erp">
                                            <option value="0"><?php echo $this->lang->line("select"); ?></option>
                                            <?php foreach ($exportSuppliersDhlCost as $supplier) { ?>
                                                <option value="<?php echo $supplier->id; ?>"><?php echo $supplier->supplier_name . " - " . $supplier->supplier_id; ?></option>
                                            <?php } ?>
                                        </select>
                                        <label id="error-supplier_name_dhlcost" class="error-text"><?php echo $this->lang->line("error_supplier"); ?></label>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="issued_date_dhlcost"><?php echo $this->lang->line("issued_date"); ?></label>
                                        <input type="datetime-local" id="issued_date_dhlcost" name="issued_date_dhlcost" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("issued_date"); ?>" />
                                        <input type="text" id="formatted_date_dhlcost" class="form-control text-uppercase" placeholder="DD/MM/YYYY HH:MM AM/PM" readonly style="display: none;" />
                                        <label id="error-issued_date_dhlcost" class="error-text"><?php echo $this->lang->line("error_issued_date"); ?></label>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-2">
                                        <label for="export_subtotal"><?php echo $this->lang->line("export_subtotal"); ?></label>
                                        <input type="number" step="any" id="subtotal_dhlcost" name="subtotal_dhlcost" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("exdhlcost_subtotal"); ?>" />
                                        <label id="error-subtotal_dhlcost" class="error-text"><?php echo $this->lang->line("error_value"); ?></label>
                                    </div>

                                    <div class="col-md-2">
                                        <label for="export_iva"><?php echo $this->lang->line("export_iva"); ?></label>
                                        <input type="number" step="any" id="iva_dhlcost" name="iva_dhlcost" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("exdhlcost_iva"); ?>" />
                                        <label id="error-iva_dhlcost" class="error-text"><?php echo $this->lang->line("error_value"); ?></label>
                                    </div>

                                    <div class="col-md-2">
                                        <label for="export_retefuente"><?php echo $this->lang->line("export_retefuente"); ?></label>
                                        <input type="number" step="any" id="retefuente_dhlcost" name="retefuente_dhlcost" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("exdhlcost_retefuente"); ?>" />
                                        <label id="error-retefuente_dhlcost" class="error-text"><?php echo $this->lang->line("error_value"); ?></label>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="export_total_payable"><?php echo $this->lang->line("export_total_payable"); ?></label>
                                        <input type="number" step="any" id="payable_dhlcost" name="payable_dhlcost" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("exdhlcost_total_payable"); ?>" />
                                        <label id="error-payable_dhlcost" class="error-text"><?php echo $this->lang->line("error_value"); ?></label>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="container_value_dhlcost"><?php echo $this->lang->line("total_container_value"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="container_value_dhlcost">0</label>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div id="divContainersDhlcost">

                                <div class="row mb-3">
                                    <label class="col-md-3 col-form-label header-profile-menu1 fontsize" for="continerNo_Dhlcost"><?php echo $this->lang->line('container_number'); ?></label>
                                    <label class="col-md-3 col-form-label header-profile-menu1 fontsize" for="totalPieces_Dhlcost"><?php echo $this->lang->line('total_no_of_pieces'); ?></label>
                                    <label class="col-md-3 col-form-label header-profile-menu1 fontsize" for="totalVolume_Dhlcost"><?php echo $this->lang->line('total_volume'); ?></label>
                                    <label class="col-md-3 col-form-label header-profile-menu1 fontsize" for="containerValue_Dhlcost"><?php echo $this->lang->line('container_value'); ?></label>
                                </div>

                                <?php foreach ($containerDetails as $containerdetail) { ?>

                                    <div class="row mb-3">

                                        <input type="hidden" id="hdnDispatchIdDhlcost" name="dispatchid_dhlcost[<?php echo $containerdetail->dispatch_id; ?>]" value="<?php echo $containerdetail->dispatch_id; ?>">

                                        <label class="col-md-3 lbl-font header-profile-menu1 fontsize" for="lblContainerDhlcost" name="containerNumberDhlcost[]" value="<?php echo $containerdetail->container_number; ?>">
                                            <?php echo strtoupper($containerdetail->container_number); ?>
                                        </label>

                                        <label class="col-md-3 col-form-label lbl-font header-profile-menu1 fontsize" for="lblContainer1Dhlcost" name="containerNumber1Dhlcost[]" value="<?php echo $containerdetail->total_pieces; ?>">
                                            <?php echo $containerdetail->total_pieces + 0; ?>
                                        </label>

                                        <label class="col-md-3 col-form-label lbl-font header-profile-menu1 fontsize" for="lblContainer2Dhlcost" name="containerNumber2Dhlcost[]" value="<?php echo $containerdetail->total_volume; ?>">
                                            <?php echo sprintf("%0.3f", $containerdetail->total_volume + 0); ?>
                                        </label>

                                        <div class="col-md-3">
                                            <input type="number" step="any" value="<?php echo 0; ?>" class="form-control text-uppercase" id="dhlcost_container_value" name="dhlcost_container_value[<?php echo $containerdetail->dispatch_id; ?>]" />
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>

                            <div class="row mb-3 mt-4">
                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-danger col-md-2" style="margin-right: 5px;" id="btnResetDhlCost" name="btnResetDhlCost"><?php echo $this->lang->line('reset'); ?></button>
                                    <button type="button" class="btn btn-primary col-md-2" id="btnSaveDhlCost" name="btnSaveDhlCost"><?php echo $this->lang->line('save'); ?></button>
                                </div>
                            </div>

                            <div id="divInvoiceListDhlCost">
                                <div class="card-header">
                                    <h5 class="mb-0"><?php echo $this->lang->line("invoice_list"); ?></h5>
                                </div>
                                <div style="overflow-x: auto;">
                                    <table class="datatables-demo table table-striped table-bordered" id="xin_table_invoice_dhlcost" style="width: 100% !important;">
                                        <thead>
                                            <tr>
                                                <th><?php echo $this->lang->line("action"); ?></th>
                                                <th><?php echo $this->lang->line("invoice_no"); ?></th>
                                                <th><?php echo $this->lang->line("supplier_name"); ?></th>
                                                <th><?php echo $this->lang->line("invoice_date"); ?></th>
                                                <th><?php echo $this->lang->line("export_subtotal"); ?></th>
                                                <th><?php echo $this->lang->line("export_iva"); ?></th>
                                                <th><?php echo $this->lang->line("export_retefuente"); ?></th>
                                                <th><?php echo $this->lang->line("export_total_payable"); ?></th>
                                                <th><?php echo $this->lang->line("container_value"); ?></th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="nav-othercost" role="tabpanel" aria-labelledby="nav-othercost-tab">
                            <div class="row mb-3">
                                <div class="col-md-6 mb-2">
                                    <label for="fileUploadDoc_Othercost"><?php echo $this->lang->line('upload_document'); ?></label>
                                    <input name="fileUploadDoc_Othercost" type="file" accept=".xml,.pdf" id="fileUploadDoc_Othercost" onchange="loadFileOthercost(event)" class="form-control">
                                    <label id="error-selectdocothercost" class="error-text"><?php echo $this->lang->line('error_select_document'); ?></label>
                                </div>
                            </div>

                            <div id="divXmlOthercost">

                                <div class="row mb-3">

                                    <div class="col-md-2">
                                        <label for="invoice_number"><?php echo $this->lang->line("invoice_number"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="lblInvoiceNoOthercost"></label>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="supplier_name"><?php echo $this->lang->line("supplier_name") . " - " . $this->lang->line("supplier_id"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="lblSupplierNameOthercost"></label>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label for="issued_date"><?php echo $this->lang->line("issued_date"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="lblIssuedDateOthercost"></label>
                                        </div>
                                    </div>

                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-2">
                                        <label for="export_subtotal"><?php echo $this->lang->line("export_subtotal"); ?></label>
                                        <!-- <div class="input-group">
                                            <label class="control-label" id="lblTotalAmountOthercost"></label>
                                        </div> -->
                                        <input type="number" step="any" id="lblTotalAmountOthercost" name="lblTotalAmountOthercost" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_subtotal"); ?>" />
                                    </div>

                                    <div class="col-md-2">
                                        <label for="export_iva"><?php echo $this->lang->line("export_iva"); ?></label>
                                        <!-- <div class="input-group">
                                            <label class="control-label" id="lblTotalTaxAmountOthercost"></label>
                                        </div> -->
                                        <input type="number" step="any" id="lblTotalTaxAmountOthercost" name="lblTotalTaxAmountOthercost" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_iva"); ?>" />
                                    </div>

                                    <div class="col-md-2">
                                        <label for="export_retefuente"><?php echo $this->lang->line("export_retefuente"); ?></label>
                                        <!-- <div class="input-group">
                                            <label class="control-label" id="lblAllowanceAmountOthercost"></label>
                                        </div> -->
                                        <input type="number" step="any" id="lblAllowanceAmountOthercost" name="lblAllowanceAmountOthercost" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_retefuente"); ?>" />
                                    </div>

                                    <div class="col-md-3">
                                        <label for="export_total_payable"><?php echo $this->lang->line("export_total_payable"); ?></label>
                                        <!-- <div class="input-group">
                                            <label class="control-label" id="lblPayableAmountOthercost"></label>
                                        </div> -->
                                        <input type="number" step="any" id="lblPayableAmountOthercost" name="lblPayableAmountOthercost" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("export_total_payable"); ?>" />
                                    </div>

                                    <div class="col-md-3">
                                        <label for="total_container_value"><?php echo $this->lang->line("total_container_value"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="lblContainerValueOthercost"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="divPdfOthercost">
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label for="invoice_number_othercost"><?php echo $this->lang->line("invoice_number"); ?></label>
                                        <input type="text" id="invoice_number_othercost" name="invoice_number_othercost" class="form-control text-uppercase" value="<?php echo isset($exportDocumentsOthercost[0]->invoice_no) ? $exportDocumentsOthercost[0]->invoice_no : ''; ?>" placeholder="<?php echo $this->lang->line("invoice_number"); ?>" />
                                        <label id="error-invoice_no_othercost" class="error-text"><?php echo $this->lang->line("error_invoice_no"); ?></label>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="supplier_name_othercost"><?php echo $this->lang->line("supplier_name"); ?></label>
                                        <select class="form-control" name="supplier_name_othercost" id="supplier_name_othercost" data-plugin="select_erp">
                                            <option value="0"><?php echo $this->lang->line("select"); ?></option>
                                            <?php foreach ($exportSuppliersOthercost as $supplier) { ?>
                                                <option value="<?php echo $supplier->id; ?>"><?php echo $supplier->supplier_name . " - " . $supplier->supplier_id; ?></option>
                                            <?php } ?>
                                        </select>
                                        <label id="error-supplier_name_othercost" class="error-text"><?php echo $this->lang->line("error_supplier"); ?></label>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="issued_date_othercost"><?php echo $this->lang->line("issued_date"); ?></label>
                                        <input type="datetime-local" id="issued_date_othercost" name="issued_date_othercost" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("issued_date"); ?>" />
                                        <input type="text" id="formatted_date_othercost" class="form-control text-uppercase" placeholder="DD/MM/YYYY HH:MM AM/PM" readonly style="display: none;" />
                                        <label id="error-issued_date_othercost" class="error-text"><?php echo $this->lang->line("error_issued_date"); ?></label>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-2">
                                        <label for="export_subtotal"><?php echo $this->lang->line("export_subtotal"); ?></label>
                                        <input type="number" step="any" id="subtotal_othercost" name="subtotal_othercost" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("exothercost_subtotal"); ?>" />
                                        <label id="error-subtotal_othercost" class="error-text"><?php echo $this->lang->line("error_value"); ?></label>
                                    </div>

                                    <div class="col-md-2">
                                        <label for="export_iva"><?php echo $this->lang->line("export_iva"); ?></label>
                                        <input type="number" step="any" id="iva_othercost" name="iva_othercost" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("exothercost_iva"); ?>" />
                                        <label id="error-iva_othercost" class="error-text"><?php echo $this->lang->line("error_value"); ?></label>
                                    </div>

                                    <div class="col-md-2">
                                        <label for="export_retefuente"><?php echo $this->lang->line("export_retefuente"); ?></label>
                                        <input type="number" step="any" id="retefuente_othercost" name="retefuente_othercost" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("exothercost_retefuente"); ?>" />
                                        <label id="error-retefuente_othercost" class="error-text"><?php echo $this->lang->line("error_value"); ?></label>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="export_total_payable"><?php echo $this->lang->line("export_total_payable"); ?></label>
                                        <input type="number" step="any" id="payable_othercost" name="payable_othercost" class="form-control text-uppercase" value="" placeholder="<?php echo $this->lang->line("exothercost_total_payable"); ?>" />
                                        <label id="error-payable_othercost" class="error-text"><?php echo $this->lang->line("error_value"); ?></label>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="container_value_othercost"><?php echo $this->lang->line("total_container_value"); ?></label>
                                        <div class="input-group">
                                            <label class="control-label" id="container_value_othercost">0</label>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div id="divContainersOthercost">

                                <div class="row mb-3">
                                    <label class="col-md-3 col-form-label header-profile-menu1 fontsize" for="continerNo_Othercost"><?php echo $this->lang->line('container_number'); ?></label>
                                    <label class="col-md-3 col-form-label header-profile-menu1 fontsize" for="totalPieces_Othercost"><?php echo $this->lang->line('total_no_of_pieces'); ?></label>
                                    <label class="col-md-3 col-form-label header-profile-menu1 fontsize" for="totalVolume_Othercost"><?php echo $this->lang->line('total_volume'); ?></label>
                                    <label class="col-md-3 col-form-label header-profile-menu1 fontsize" for="containerValue_Othercost"><?php echo $this->lang->line('container_value'); ?></label>
                                </div>

                                <?php foreach ($containerDetails as $containerdetail) { ?>

                                    <div class="row mb-3">

                                        <input type="hidden" id="hdnDispatchIdOthercost" name="dispatchid_othercost[<?php echo $containerdetail->dispatch_id; ?>]" value="<?php echo $containerdetail->dispatch_id; ?>">

                                        <label class="col-md-3 lbl-font header-profile-menu1 fontsize" for="lblContainerOthercost" name="containerNumberOthercost[]" value="<?php echo $containerdetail->container_number; ?>">
                                            <?php echo strtoupper($containerdetail->container_number); ?>
                                        </label>

                                        <label class="col-md-3 col-form-label lbl-font header-profile-menu1 fontsize" for="lblContainer1Othercost" name="containerNumber1Othercost[]" value="<?php echo $containerdetail->total_pieces; ?>">
                                            <?php echo $containerdetail->total_pieces + 0; ?>
                                        </label>

                                        <label class="col-md-3 col-form-label lbl-font header-profile-menu1 fontsize" for="lblContainer2Othercost" name="containerNumber2Othercost[]" value="<?php echo $containerdetail->total_volume; ?>">
                                            <?php echo sprintf("%0.3f", $containerdetail->total_volume + 0); ?>
                                        </label>

                                        <div class="col-md-3">
                                            <input type="number" step="any" value="<?php echo 0; ?>" class="form-control text-uppercase" id="othercost_container_value" name="othercost_container_value[<?php echo $containerdetail->dispatch_id; ?>]" />
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>

                            <div class="row mb-3 mt-4">
                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-danger col-md-2" style="margin-right: 5px;" id="btnResetOthercost" name="btnResetOthercost"><?php echo $this->lang->line('reset'); ?></button>
                                    <button type="button" class="btn btn-primary col-md-2" id="btnSaveOthercost" name="btnSaveOthercost"><?php echo $this->lang->line('save'); ?></button>
                                </div>
                            </div>

                            <div id="divInvoiceListOthercost">
                                <div class="card-header">
                                    <h5 class="mb-0"><?php echo $this->lang->line("invoice_list"); ?></h5>
                                </div>
                                <div style="overflow-x: auto;">
                                    <table class="datatables-demo table table-striped table-bordered" id="xin_table_invoice_othercost" style="width: 100% !important;">
                                        <thead>
                                            <tr>
                                                <th><?php echo $this->lang->line("action"); ?></th>
                                                <th><?php echo $this->lang->line("invoice_no"); ?></th>
                                                <th><?php echo $this->lang->line("supplier_name"); ?></th>
                                                <th><?php echo $this->lang->line("invoice_date"); ?></th>
                                                <th><?php echo $this->lang->line("export_subtotal"); ?></th>
                                                <th><?php echo $this->lang->line("export_iva"); ?></th>
                                                <th><?php echo $this->lang->line("export_retefuente"); ?></th>
                                                <th><?php echo $this->lang->line("export_total_payable"); ?></th>
                                                <th><?php echo $this->lang->line("container_value"); ?></th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
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
    var total_error = "<?php echo $this->lang->line("total_error"); ?>";
    var data_updated = "<?php echo $this->lang->line("data_updated"); ?>";
    var text_save = "<?php echo $this->lang->line("save"); ?>";
    var no_data_available = "<?php echo $this->lang->line("no_data_available"); ?>";
    var export_subtotal = "<?php echo $this->lang->line("export_subtotal"); ?>";
    var text_update = "<?php echo $this->lang->line("update"); ?>";
    var totalPayableAmount = 0;
    var uploadPdfFileCustomAgency = "";
    var fileExtension = "";
    var supplierId = 0;
    var subTotalCustoms = 0;
    var ivaCustoms = 0;
    var retefuenteCustoms = 0;
    var payableCustoms = 0;
    let containerValuePhytoArr = [];
    let containerValueArr = [];
    let totalContainers = 0;

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
        $("#btnResetCustoms").hide();

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
        $("#btnResetITR").hide();

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
        $("#btnResetPort").hide();

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
        $("#btnResetShipping").hide();

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
        $("#btnResetFumigation").hide();

        $("#error-selectdoccoteros").hide();
        $("#error-invoice_no_coteros").hide();
        $("#error-supplier_name_coteros").hide();
        $("#error-supplier_id_coteros").hide();
        $("#error-issued_date_coteros").hide();
        $("#error-subtotal_coteros").hide();
        $("#error-iva_coteros").hide();
        $("#error-retefuente_coteros").hide();
        $("#error-payable_coteros").hide();
        $("#divXmlCoteros").hide();
        $("#btnResetCoteros").hide();

        $("#error-selectdocphyto").hide();
        $("#error-invoice_no_phyto").hide();
        $("#error-supplier_name_phyto").hide();
        $("#error-supplier_id_phyto").hide();
        $("#error-issued_date_phyto").hide();
        $("#error-subtotal_phyto").hide();
        $("#error-iva_phyto").hide();
        $("#error-retefuente_phyto").hide();
        $("#error-payable_phyto").hide();
        $("#divXmlPhyto").hide();
        $("#btnResetPhyto").hide();

        $("#error-selectdocincentives").hide();
        $("#error-invoice_no_incentives").hide();
        $("#error-supplier_name_incentives").hide();
        $("#error-supplier_id_incentives").hide();
        $("#error-issued_date_incentives").hide();
        $("#error-subtotal_incentives").hide();
        $("#error-iva_incentives").hide();
        $("#error-retefuente_incentives").hide();
        $("#error-payable_incentives").hide();
        $("#divXmlIncentives").hide();
        $("#btnResetIncentives").hide();

        $("#error-selectdoccontainerloadingcost").hide();
        $("#error-invoice_no_containerloadingcost").hide();
        $("#error-supplier_name_containerloadingcost").hide();
        $("#error-supplier_id_containerloadingcost").hide();
        $("#error-issued_date_containerloadingcost").hide();
        $("#error-subtotal_containerloadingcost").hide();
        $("#error-iva_containerloadingcost").hide();
        $("#error-retefuente_containerloadingcost").hide();
        $("#error-payable_containerloadingcost").hide();
        $("#divXmlContainerLoadingCost").hide();
        $("#btnResetContainerLoadingCost").hide();

        $("#error-selectdocdhlcost").hide();
        $("#error-invoice_no_dhlcost").hide();
        $("#error-supplier_name_dhlcost").hide();
        $("#error-supplier_id_dhlcost").hide();
        $("#error-issued_date_dhlcost").hide();
        $("#error-subtotal_dhlcost").hide();
        $("#error-iva_dhlcost").hide();
        $("#error-retefuente_dhlcost").hide();
        $("#error-payable_dhlcost").hide();
        $("#divXmlDhlCost").hide();
        $("#btnResetDhlCost").hide();

        $("#error-selectdocremobilization").hide();
        $("#error-invoice_no_remobilization").hide();
        $("#error-supplier_name_remobilization").hide();
        $("#error-supplier_id_remobilization").hide();
        $("#error-issued_date_remobilization").hide();
        $("#error-subtotal_remobilization").hide();
        $("#error-iva_remobilization").hide();
        $("#error-retefuente_remobilization").hide();
        $("#error-payable_remobilization").hide();
        $("#divXmlRemobilization").hide();
        $("#btnResetRemobilization").hide();

        $("#error-selectdocothercost").hide();
        $("#error-invoice_no_othercost").hide();
        $("#error-supplier_name_othercost").hide();
        $("#error-supplier_id_othercost").hide();
        $("#error-issued_date_othercost").hide();
        $("#error-subtotal_othercost").hide();
        $("#error-iva_othercost").hide();
        $("#error-retefuente_othercost").hide();
        $("#error-payable_othercost").hide();
        $("#divXmlOthercost").hide();
        $("#btnResetOthercost").hide();

        var selectedInvoiceId = 0;
        var selectedExportId = 0;
        var selectedExportType = 0;

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

                if (fileExtension == "pdf" || fileExtension == "PDF" || fileExtension == "") {
                    totalPayableAmount = parseFloat($("#payable_custom").val()) || 0;
                }

                // if (totalContainerValue != totalPayableAmount) {
                //     toastr.clear();
                //     toastr.error(total_error);
                //     return false;
                // }

                var isValid1 = true,
                    isValid2 = true,
                    isValid3 = true,
                    isValid4 = true,
                    isValid5 = true,
                    isValid6 = true,
                    isValid7 = true;
                if ((fileExtension != "xml" || fileExtension != "XML" || fileExtension == "") && $('#divPdf').is(':visible')) {

                    if ($("#invoice_number_custom").val().length == 0) {
                        $("#error-invoice_no_custom").show();
                        isValid1 = false;
                    } else {
                        $("#error-invoice_no_custom").hide();
                        isValid1 = true;
                    }

                    if ($("#supplier_name_custom").val() == 0) {
                        $("#error-supplier_name_custom").show();
                        isValid2 = false;
                    } else {
                        $("#error-supplier_name_custom").hide();
                        isValid2 = true;
                    }

                    if ($("#issued_date_custom").val().length == 0) {
                        $("#error-issued_date_custom").show();
                        isValid3 = false;
                    } else {
                        $("#error-issued_date_custom").hide();
                        isValid3 = true;
                    }

                    if ($("#subtotal_custom").val().length == 0) {
                        $("#error-subtotal_custom").show();
                        isValid4 = false;
                    } else {
                        $("#error-subtotal_custom").hide();
                        isValid4 = true;
                    }

                    if ($("#iva_custom").val().length == 0) {
                        $("#error-iva_custom").show();
                        isValid5 = false;
                    } else {
                        $("#error-iva_custom").hide();
                        isValid5 = true;
                    }

                    if ($("#retefuente_custom").val().length == 0) {
                        $("#error-retefuente_custom").show();
                        isValid6 = false;
                    } else {
                        $("#error-retefuente_custom").hide();
                        isValid6 = true;
                    }

                    if ($("#payable_custom").val().length == 0) {
                        $("#error-payable_custom").show();
                        isValid7 = false;
                    } else {
                        $("#error-payable_custom").hide();
                        isValid7 = true;
                    }
                }

                if (isValid1 && isValid2 && isValid3 && isValid4 && isValid5 && isValid6 && isValid7) {
                    var fd = new FormData();
                    fd.append("exportId", $("#hdnExportId").val());
                    fd.append("originId", $("#hdnOriginId").val());
                    fd.append('saNumber', $("#hdnSaNumber").val());
                    fd.append('selectedInvoiceId', selectedInvoiceId);
                    fd.append('selectedExportId', selectedExportId);
                    fd.append("fileExtension", fileExtension);
                    fd.append("updateContainerValueData_Custom", JSON.stringify(arrUpdateContainerValue));
                    fd.append("uploadPdfFileCustomAgency", uploadPdfFileCustomAgency);
                    if (fileExtension == "xml" || fileExtension == "XML") {

                        fd.append('invoiceNo_Custom', $("#lblInvoiceNoCustoms").text());
                        fd.append('supplierName_Custom', supplierId);
                        fd.append('formattedDate_Custom', $("#lblIssuedDateCustoms").text());
                        // fd.append('subTotal_Custom', subTotalCustoms);
                        // fd.append('iva_Custom', ivaCustoms);
                        // fd.append('retefuente_Custom', retefuenteCustoms);
                        // fd.append('payable_Custom', payableCustoms);
                        fd.append('subTotal_Custom', $("#lblTotalAmountCustoms").val());
                        fd.append('iva_Custom', $("#lblTotalTaxAmountCustoms").val());
                        fd.append('retefuente_Custom', $("#lblAllowanceAmountCustoms").val());
                        fd.append('payable_Custom', $("#lblPayableAmountCustoms").val());
                    } else {

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

                    $('#loading').show();
                    $.ajax({
                        url: base_url + "/save_export_documents",
                        type: 'post',
                        data: fd,
                        contentType: false,
                        processData: false,
                        success: function(jsonResult) {
                            $('#loading').hide();

                            selectedInvoiceId = 0;
                            selectedExportId = 0;
                            selectedExportType = 0;

                            if (jsonResult.redirect == true) {
                                window.location.replace(login_url);
                            } else if (jsonResult.result != '') {
                                toastr.clear();
                                toastr.success(data_updated);

                                resetForm();
                                $('#btnResetCustoms').hide();
                                $('#btnSaveCustoms').text(text_save);
                                $("#divInvoiceListCustoms").show();

                                $("#xin_table_invoice_customs").DataTable({
                                    data: JSON.parse(jsonResult.updatedlist),
                                    columns: [{
                                            data: "id",
                                            render: function(data, type, row) {
                                                return `
                                                        <button type="button" class="btn icon-btn btn-xs btn-success waves-effect waves-light" data-role="editcustoms_invoice" data-export_id="${row.export_id}" data-id="${data}" title="<?php echo $this->lang->line("edit"); ?>">
                                                            <span class="fas fa-pencil"></span>
                                                        </button>
                                                        
                                                        <button type="button" style="margin-left:5px;" class="btn icon-btn btn-xs btn-delete waves-effect waves-light" data-export_id="${row.export_id}" data-role="deletecustoms_invoice" data-id="${data}" title="<?php echo $this->lang->line("delete"); ?>">
                                                            <span class="fas fa-trash"></span>
                                                        </button>
                                                    `;
                                            }
                                        },
                                        {
                                            data: "invoice_no"
                                        },
                                        {
                                            data: "supplier_name"
                                        },
                                        {
                                            data: "invoice_date"
                                        },
                                        {
                                            data: "sub_total",
                                            render: function(data) {
                                                return parseFloat(data).toFixed(2);
                                            }
                                        },
                                        {
                                            data: "tax_total",
                                            render: function(data) {
                                                return parseFloat(data).toFixed(2);
                                            }
                                        },
                                        {
                                            data: "allowance_total",
                                            render: function(data) {
                                                return parseFloat(data).toFixed(2);
                                            }
                                        },
                                        {
                                            data: "payable_total",
                                            render: function(data) {
                                                return parseFloat(data).toFixed(2);
                                            }
                                        },
                                        {
                                            data: "container_value_total",
                                            render: function(data) {
                                                return parseFloat(data).toFixed(2);
                                            }
                                        }
                                    ],
                                    scrollX: true, // ✅ Enables horizontal scrolling
                                    autoWidth: true, // ✅ Enables automatic column sizing
                                    scrollCollapse: true,
                                    responsive: false, // ✅ Keep this false with scrollX to avoid conflict
                                    bDestroy: true,
                                    paging: false,
                                    searching: false,
                                    sorting: false,
                                    language: {
                                        url: datatable_language
                                    }
                                });
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

            console.log(arrUpdateContainerValue);

            if (arrUpdateContainerValue.length > 0) {

                if (fileExtension == "pdf" || fileExtension == "PDF" || fileExtension == "") {
                    totalPayableAmount = parseFloat($("#payable_itr").val()) || 0;
                }

                // if (totalContainerValue != totalPayableAmount) {
                //     toastr.clear();
                //     toastr.error(total_error);
                //     return false;
                // }

                var isValid1 = true,
                    isValid2 = true,
                    isValid3 = true,
                    isValid4 = true,
                    isValid5 = true,
                    isValid6 = true,
                    isValid7 = true;
                if ((fileExtension != "xml" || fileExtension != "XML" || fileExtension == "") && $('#divPdfITR').is(':visible')) {

                    if ($("#invoice_number_itr").val().length == 0) {
                        $("#error-invoice_no_itr").show();
                        isValid1 = false;
                    } else {
                        $("#error-invoice_no_itr").hide();
                        isValid1 = true;
                    }

                    if ($("#supplier_name_itr").val() == 0) {
                        $("#error-supplier_name_itr").show();
                        isValid2 = false;
                    } else {
                        $("#error-supplier_name_itr").hide();
                        isValid2 = true;
                    }

                    if ($("#issued_date_itr").val().length == 0) {
                        $("#error-issued_date_itr").show();
                        isValid3 = false;
                    } else {
                        $("#error-issued_date_itr").hide();
                        isValid3 = true;
                    }

                    if ($("#subtotal_itr").val().length == 0) {
                        $("#error-subtotal_itr").show();
                        isValid4 = false;
                    } else {
                        $("#error-subtotal_itr").hide();
                        isValid4 = true;
                    }

                    if ($("#iva_itr").val().length == 0) {
                        $("#error-iva_itr").show();
                        isValid5 = false;
                    } else {
                        $("#error-iva_itr").hide();
                        isValid5 = true;
                    }

                    if ($("#retefuente_itr").val().length == 0) {
                        $("#error-retefuente_itr").show();
                        isValid6 = false;
                    } else {
                        $("#error-retefuente_itr").hide();
                        isValid6 = true;
                    }

                    if ($("#payable_itr").val().length == 0) {
                        $("#error-payable_itr").show();
                        isValid7 = false;
                    } else {
                        $("#error-payable_itr").hide();
                        isValid7 = true;
                    }
                }

                if (isValid1 && isValid2 && isValid3 && isValid4 && isValid5 && isValid6 && isValid7) {
                    var fd = new FormData();
                    fd.append("exportId", $("#hdnExportId").val());
                    fd.append("originId", $("#hdnOriginId").val());
                    fd.append('saNumber', $("#hdnSaNumber").val());
                    fd.append('selectedInvoiceId', selectedInvoiceId);
                    fd.append('selectedExportId', selectedExportId);
                    fd.append("fileExtension", fileExtension);
                    fd.append("updateContainerValueData_ITR", JSON.stringify(arrUpdateContainerValue));
                    fd.append("uploadPdfFileITR", uploadPdfFileCustomAgency);

                    if (fileExtension == "xml" || fileExtension == "XML") {

                        fd.append('invoiceNo_ITR', $("#lblInvoiceNoITR").text());
                        fd.append('supplierName_ITR', supplierId);
                        fd.append('formattedDate_ITR', $("#lblIssuedDateITR").text());
                        // fd.append('subTotal_ITR', subTotalCustoms);
                        // fd.append('iva_ITR', ivaCustoms);
                        // fd.append('retefuente_ITR', retefuenteCustoms);
                        // fd.append('payable_ITR', payableCustoms);
                        fd.append('subTotal_ITR', $("#lblTotalAmountITR").val());
                        fd.append('iva_ITR', $("#lblTotalTaxAmountITR").val());
                        fd.append('retefuente_ITR', $("#lblAllowanceAmountITR").val());
                        fd.append('payable_ITR', $("#lblPayableAmountITR").val());
                    } else {

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

                    $('#loading').show();
                    $.ajax({
                        url: base_url + "/save_export_documents",
                        type: 'post',
                        data: fd,
                        contentType: false,
                        processData: false,
                        success: function(jsonResult) {
                            $('#loading').hide();

                            selectedInvoiceId = 0;
                            selectedExportId = 0;
                            selectedExportType = 0;

                            if (jsonResult.redirect == true) {
                                window.location.replace(login_url);
                            } else if (jsonResult.result != '') {
                                toastr.clear();
                                toastr.success(data_updated);

                                resetForm();
                                $('#btnResetITR').hide();
                                $('#btnSaveITR').text(text_save);
                                $("#divInvoiceListITR").show();

                                $("#xin_table_invoice_itr").DataTable({
                                    data: JSON.parse(jsonResult.updatedlist),
                                    columns: [{
                                            data: "id",
                                            render: function(data, type, row) {
                                                return `
                                                        <button type="button" class="btn icon-btn btn-xs btn-success waves-effect waves-light" data-role="edititr_invoice" data-export_id="${row.export_id}" data-id="${data}" title="<?php echo $this->lang->line("edit"); ?>">
                                                            <span class="fas fa-pencil"></span>
                                                        </button>
                                                        
                                                        <button type="button" style="margin-left:5px;" class="btn icon-btn btn-xs btn-delete waves-effect waves-light" data-export_id="${row.export_id}" data-role="deleteitr_invoice" data-id="${data}" title="<?php echo $this->lang->line("delete"); ?>">
                                                            <span class="fas fa-trash"></span>
                                                        </button>
                                                    `;
                                            }
                                        },
                                        {
                                            data: "invoice_no"
                                        },
                                        {
                                            data: "supplier_name"
                                        },
                                        {
                                            data: "invoice_date"
                                        },
                                        {
                                            data: "sub_total",
                                            render: function(data) {
                                                return parseFloat(data).toFixed(2);
                                            }
                                        },
                                        {
                                            data: "tax_total",
                                            render: function(data) {
                                                return parseFloat(data).toFixed(2);
                                            }
                                        },
                                        {
                                            data: "allowance_total",
                                            render: function(data) {
                                                return parseFloat(data).toFixed(2);
                                            }
                                        },
                                        {
                                            data: "payable_total",
                                            render: function(data) {
                                                return parseFloat(data).toFixed(2);
                                            }
                                        },
                                        {
                                            data: "container_value_total",
                                            render: function(data) {
                                                return parseFloat(data).toFixed(2);
                                            }
                                        }
                                    ],
                                    scrollX: true, // ✅ Enables horizontal scrolling
                                    autoWidth: true, // ✅ Enables automatic column sizing
                                    scrollCollapse: true,
                                    responsive: false, // ✅ Keep this false with scrollX to avoid conflict
                                    bDestroy: true,
                                    paging: false,
                                    searching: false,
                                    sorting: false,
                                    language: {
                                        url: datatable_language
                                    }
                                });

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

                if (fileExtension == "pdf" || fileExtension == "PDF" || fileExtension == "") {
                    totalPayableAmount = parseFloat($("#payable_port").val()) || 0;
                }

                // if (totalContainerValue != totalPayableAmount) {
                //     toastr.clear();
                //     toastr.error(total_error);
                //     return false;
                // }

                var isValid1 = true,
                    isValid2 = true,
                    isValid3 = true,
                    isValid4 = true,
                    isValid5 = true,
                    isValid6 = true,
                    isValid7 = true;
                if ((fileExtension != "xml" || fileExtension != "XML" || fileExtension == "") && $('#divPdfPort').is(':visible')) {

                    if ($("#invoice_number_port").val().length == 0) {
                        $("#error-invoice_no_port").show();
                        isValid1 = false;
                    } else {
                        $("#error-invoice_no_port").hide();
                        isValid1 = true;
                    }

                    if ($("#supplier_name_port").val() == 0) {
                        $("#error-supplier_name_port").show();
                        isValid2 = false;
                    } else {
                        $("#error-supplier_name_port").hide();
                        isValid2 = true;
                    }

                    if ($("#issued_date_port").val().length == 0) {
                        $("#error-issued_date_port").show();
                        isValid3 = false;
                    } else {
                        $("#error-issued_date_port").hide();
                        isValid3 = true;
                    }

                    if ($("#subtotal_port").val().length == 0) {
                        $("#error-subtotal_port").show();
                        isValid4 = false;
                    } else {
                        $("#error-subtotal_port").hide();
                        isValid4 = true;
                    }

                    if ($("#iva_port").val().length == 0) {
                        $("#error-iva_port").show();
                        isValid5 = false;
                    } else {
                        $("#error-iva_port").hide();
                        isValid5 = true;
                    }

                    if ($("#retefuente_port").val().length == 0) {
                        $("#error-retefuente_port").show();
                        isValid6 = false;
                    } else {
                        $("#error-retefuente_port").hide();
                        isValid6 = true;
                    }

                    if ($("#payable_port").val().length == 0) {
                        $("#error-payable_port").show();
                        isValid7 = false;
                    } else {
                        $("#error-payable_port").hide();
                        isValid7 = true;
                    }
                }

                if (isValid1 && isValid2 && isValid3 && isValid4 && isValid5 && isValid6 && isValid7) {

                    var fd = new FormData();
                    fd.append("exportId", $("#hdnExportId").val());
                    fd.append("originId", $("#hdnOriginId").val());
                    fd.append('saNumber', $("#hdnSaNumber").val());
                    fd.append('selectedInvoiceId', selectedInvoiceId);
                    fd.append('selectedExportId', selectedExportId);
                    fd.append("fileExtension", fileExtension);
                    fd.append("updateContainerValueData_Port", JSON.stringify(arrUpdateContainerValue));
                    fd.append("uploadPdfFilePort", uploadPdfFileCustomAgency);

                    if (fileExtension == "xml" || fileExtension == "XML") {

                        fd.append('invoiceNo_Port', $("#lblInvoiceNoPort").text());
                        fd.append('supplierName_Port', supplierId);
                        fd.append('formattedDate_Port', $("#lblIssuedDatePort").text());
                        // fd.append('subTotal_Port', subTotalCustoms);
                        // fd.append('iva_Port', ivaCustoms);
                        // fd.append('retefuente_Port', retefuenteCustoms);
                        // fd.append('payable_Port', payableCustoms);
                        fd.append('subTotal_Port', $("#lblTotalAmountPort").val());
                        fd.append('iva_Port', $("#lblTotalTaxAmountPort").val());
                        fd.append('retefuente_Port', $("#lblAllowanceAmountPort").val());
                        fd.append('payable_Port', $("#lblPayableAmountPort").val());
                    } else {

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

                    $('#loading').show();
                    $.ajax({
                        url: base_url + "/save_export_documents",
                        type: 'post',
                        data: fd,
                        contentType: false,
                        processData: false,
                        success: function(jsonResult) {
                            $('#loading').hide();

                            selectedInvoiceId = 0;
                            selectedExportId = 0;
                            selectedExportType = 0;

                            if (jsonResult.redirect == true) {
                                window.location.replace(login_url);
                            } else if (jsonResult.result != '') {
                                toastr.clear();
                                toastr.success(data_updated);

                                resetForm();
                                $('#btnResetPort').hide();
                                $('#btnSavePort').text(text_save);
                                $("#divInvoiceListPort").show();

                                $("#xin_table_invoice_port").DataTable({
                                    data: JSON.parse(jsonResult.updatedlist),
                                    columns: [{
                                            data: "id",
                                            render: function(data, type, row) {
                                                return `
                                                        <button type="button" class="btn icon-btn btn-xs btn-success waves-effect waves-light" data-role="editport_invoice" data-export_id="${row.export_id}" data-id="${data}" title="<?php echo $this->lang->line("edit"); ?>">
                                                            <span class="fas fa-pencil"></span>
                                                        </button>
                                                        
                                                        <button type="button" style="margin-left:5px;" class="btn icon-btn btn-xs btn-delete waves-effect waves-light" data-export_id="${row.export_id}" data-role="deleteport_invoice" data-id="${data}" title="<?php echo $this->lang->line("delete"); ?>">
                                                            <span class="fas fa-trash"></span>
                                                        </button>
                                                    `;
                                            }
                                        },
                                        {
                                            data: "invoice_no"
                                        },
                                        {
                                            data: "supplier_name"
                                        },
                                        {
                                            data: "invoice_date"
                                        },
                                        {
                                            data: "sub_total",
                                            render: function(data) {
                                                return parseFloat(data).toFixed(2);
                                            }
                                        },
                                        {
                                            data: "tax_total",
                                            render: function(data) {
                                                return parseFloat(data).toFixed(2);
                                            }
                                        },
                                        {
                                            data: "allowance_total",
                                            render: function(data) {
                                                return parseFloat(data).toFixed(2);
                                            }
                                        },
                                        {
                                            data: "payable_total",
                                            render: function(data) {
                                                return parseFloat(data).toFixed(2);
                                            }
                                        },
                                        {
                                            data: "container_value_total",
                                            render: function(data) {
                                                return parseFloat(data).toFixed(2);
                                            }
                                        }
                                    ],
                                    scrollX: true, // ✅ Enables horizontal scrolling
                                    autoWidth: true, // ✅ Enables automatic column sizing
                                    scrollCollapse: true,
                                    responsive: false, // ✅ Keep this false with scrollX to avoid conflict
                                    bDestroy: true,
                                    paging: false,
                                    searching: false,
                                    sorting: false,
                                    language: {
                                        url: datatable_language
                                    }
                                });
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

                if (fileExtension == "pdf" || fileExtension == "PDF" || fileExtension == "") {
                    totalPayableAmount = parseFloat($("#payable_shipping").val()) || 0;
                }

                // if (totalContainerValue != totalPayableAmount) {
                //     toastr.clear();
                //     toastr.error(total_error);
                //     return false;
                // }

                var isValid1 = true,
                    isValid2 = true,
                    isValid3 = true,
                    isValid4 = true,
                    isValid5 = true,
                    isValid6 = true,
                    isValid7 = true;
                if ((fileExtension != "xml" || fileExtension != "XML" || fileExtension == "") && $('#divPdfShipping').is(':visible')) {

                    if ($("#invoice_number_shipping").val().length == 0) {
                        $("#error-invoice_no_shipping").show();
                        isValid1 = false;
                    } else {
                        $("#error-invoice_no_shipping").hide();
                        isValid1 = true;
                    }

                    if ($("#supplier_name_shipping").val() == 0) {
                        $("#error-supplier_name_shipping").show();
                        isValid2 = false;
                    } else {
                        $("#error-supplier_name_shipping").hide();
                        isValid2 = true;
                    }

                    if ($("#issued_date_shipping").val().length == 0) {
                        $("#error-issued_date_shipping").show();
                        isValid3 = false;
                    } else {
                        $("#error-issued_date_shipping").hide();
                        isValid3 = true;
                    }

                    if ($("#subtotal_shipping").val().length == 0) {
                        $("#error-subtotal_shipping").show();
                        isValid4 = false;
                    } else {
                        $("#error-subtotal_shipping").hide();
                        isValid4 = true;
                    }

                    if ($("#iva_shipping").val().length == 0) {
                        $("#error-iva_shipping").show();
                        isValid5 = false;
                    } else {
                        $("#error-iva_shipping").hide();
                        isValid5 = true;
                    }

                    if ($("#retefuente_shipping").val().length == 0) {
                        $("#error-retefuente_shipping").show();
                        isValid6 = false;
                    } else {
                        $("#error-retefuente_shipping").hide();
                        isValid6 = true;
                    }

                    if ($("#payable_shipping").val().length == 0) {
                        $("#error-payable_shipping").show();
                        isValid7 = false;
                    } else {
                        $("#error-payable_shipping").hide();
                        isValid7 = true;
                    }
                }

                if (isValid1 && isValid2 && isValid3 && isValid4 && isValid5 && isValid6 && isValid7) {

                    var fd = new FormData();
                    fd.append("exportId", $("#hdnExportId").val());
                    fd.append("originId", $("#hdnOriginId").val());
                    fd.append('saNumber', $("#hdnSaNumber").val());
                    fd.append('selectedInvoiceId', selectedInvoiceId);
                    fd.append('selectedExportId', selectedExportId);
                    fd.append("fileExtension", fileExtension);
                    fd.append("updateContainerValueData_Shipping", JSON.stringify(arrUpdateContainerValue));
                    fd.append("uploadPdfFileShipping", uploadPdfFileCustomAgency);

                    if (fileExtension == "xml" || fileExtension == "XML") {

                        fd.append('invoiceNo_Shipping', $("#lblInvoiceNoShipping").text());
                        fd.append('supplierName_Shipping', supplierId);
                        fd.append('formattedDate_Shipping', $("#lblIssuedDateShipping").text());
                        // fd.append('subTotal_Shipping', subTotalCustoms);
                        // fd.append('iva_Shipping', ivaCustoms);
                        // fd.append('retefuente_Shipping', retefuenteCustoms);
                        // fd.append('payable_Shipping', payableCustoms);
                        fd.append('subTotal_Shipping', $("#lblTotalAmountShipping").val());
                        fd.append('iva_Shipping', $("#lblTotalTaxAmountShipping").val());
                        fd.append('retefuente_Shipping', $("#lblAllowanceAmountShipping").val());
                        fd.append('payable_Shipping', $("#lblPayableAmountShipping").val());
                    } else {

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

                    $('#loading').show();
                    $.ajax({
                        url: base_url + "/save_export_documents",
                        type: 'post',
                        data: fd,
                        contentType: false,
                        processData: false,
                        success: function(jsonResult) {
                            $('#loading').hide();

                            selectedInvoiceId = 0;
                            selectedExportId = 0;
                            selectedExportType = 0;

                            if (jsonResult.redirect == true) {
                                window.location.replace(login_url);
                            } else if (jsonResult.result != '') {
                                toastr.clear();
                                toastr.success(data_updated);

                                resetForm();
                                $('#btnResetShipping').hide();
                                $('#btnSaveShipping').text(text_save);
                                $("#divInvoiceListShipping").show();

                                $("#xin_table_invoice_shipping").DataTable({
                                    data: JSON.parse(jsonResult.updatedlist),
                                    columns: [{
                                            data: "id",
                                            render: function(data, type, row) {
                                                return `
                                                        <button type="button" class="btn icon-btn btn-xs btn-success waves-effect waves-light" data-role="editshipping_invoice" data-export_id="${row.export_id}" data-id="${data}" title="<?php echo $this->lang->line("edit"); ?>">
                                                            <span class="fas fa-pencil"></span>
                                                        </button>
                                                        
                                                        <button type="button" style="margin-left:5px;" class="btn icon-btn btn-xs btn-delete waves-effect waves-light" data-export_id="${row.export_id}" data-role="deleteshipping_invoice" data-id="${data}" title="<?php echo $this->lang->line("delete"); ?>">
                                                            <span class="fas fa-trash"></span>
                                                        </button>
                                                    `;
                                            }
                                        },
                                        {
                                            data: "invoice_no"
                                        },
                                        {
                                            data: "supplier_name"
                                        },
                                        {
                                            data: "invoice_date"
                                        },
                                        {
                                            data: "sub_total",
                                            render: function(data) {
                                                return parseFloat(data).toFixed(2);
                                            }
                                        },
                                        {
                                            data: "tax_total",
                                            render: function(data) {
                                                return parseFloat(data).toFixed(2);
                                            }
                                        },
                                        {
                                            data: "allowance_total",
                                            render: function(data) {
                                                return parseFloat(data).toFixed(2);
                                            }
                                        },
                                        {
                                            data: "payable_total",
                                            render: function(data) {
                                                return parseFloat(data).toFixed(2);
                                            }
                                        },
                                        {
                                            data: "container_value_total",
                                            render: function(data) {
                                                return parseFloat(data).toFixed(2);
                                            }
                                        }
                                    ],
                                    scrollX: true, // ✅ Enables horizontal scrolling
                                    autoWidth: true, // ✅ Enables automatic column sizing
                                    scrollCollapse: true,
                                    responsive: false, // ✅ Keep this false with scrollX to avoid conflict
                                    bDestroy: true,
                                    paging: false,
                                    searching: false,
                                    sorting: false,
                                    language: {
                                        url: datatable_language
                                    }
                                });
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

                if (fileExtension == "pdf" || fileExtension == "PDF" || fileExtension == "") {
                    totalPayableAmount = parseFloat($("#payable_fumigation").val()) || 0;
                }

                // if (totalContainerValue != totalPayableAmount) {
                //     toastr.clear();
                //     toastr.error(total_error);
                //     return false;
                // }

                var isValid1 = true,
                    isValid2 = true,
                    isValid3 = true,
                    isValid4 = true,
                    isValid5 = true,
                    isValid6 = true,
                    isValid7 = true;
                if ((fileExtension != "xml" || fileExtension != "XML" || fileExtension == "") && $('#divPdfFumigation').is(':visible')) {

                    if ($("#invoice_number_fumigation").val().length == 0) {
                        $("#error-invoice_no_fumigation").show();
                        isValid1 = false;
                    } else {
                        $("#error-invoice_no_fumigation").hide();
                        isValid1 = true;
                    }

                    if ($("#supplier_name_fumigation").val() == 0) {
                        $("#error-supplier_name_fumigation").show();
                        isValid2 = false;
                    } else {
                        $("#error-supplier_name_fumigation").hide();
                        isValid2 = true;
                    }

                    if ($("#issued_date_fumigation").val().length == 0) {
                        $("#error-issued_date_fumigation").show();
                        isValid3 = false;
                    } else {
                        $("#error-issued_date_fumigation").hide();
                        isValid3 = true;
                    }

                    if ($("#subtotal_fumigation").val().length == 0) {
                        $("#error-subtotal_fumigation").show();
                        isValid4 = false;
                    } else {
                        $("#error-subtotal_fumigation").hide();
                        isValid4 = true;
                    }

                    if ($("#iva_fumigation").val().length == 0) {
                        $("#error-iva_fumigation").show();
                        isValid5 = false;
                    } else {
                        $("#error-iva_fumigation").hide();
                        isValid5 = true;
                    }

                    if ($("#retefuente_fumigation").val().length == 0) {
                        $("#error-retefuente_fumigation").show();
                        isValid6 = false;
                    } else {
                        $("#error-retefuente_fumigation").hide();
                        isValid6 = true;
                    }

                    if ($("#payable_fumigation").val().length == 0) {
                        $("#error-payable_fumigation").show();
                        isValid7 = false;
                    } else {
                        $("#error-payable_fumigation").hide();
                        isValid7 = true;
                    }
                }

                if (isValid1 && isValid2 && isValid3 && isValid4 && isValid5 && isValid6 && isValid7) {

                    var fd = new FormData();
                    fd.append("exportId", $("#hdnExportId").val());
                    fd.append("originId", $("#hdnOriginId").val());
                    fd.append('saNumber', $("#hdnSaNumber").val());
                    fd.append('selectedInvoiceId', selectedInvoiceId);
                    fd.append('selectedExportId', selectedExportId);
                    fd.append("fileExtension", fileExtension);
                    fd.append("updateContainerValueData_Fumigation", JSON.stringify(arrUpdateContainerValue));
                    fd.append("uploadPdfFileFumigation", uploadPdfFileCustomAgency);

                    if (fileExtension == "xml" || fileExtension == "XML") {

                        fd.append('invoiceNo_Fumigation', $("#lblInvoiceNoFumigation").text());
                        fd.append('supplierName_Fumigation', supplierId);
                        fd.append('formattedDate_Fumigation', $("#lblIssuedDateFumigation").text());
                        // fd.append('subTotal_Fumigation', subTotalCustoms);
                        // fd.append('iva_Fumigation', ivaCustoms);
                        // fd.append('retefuente_Fumigation', retefuenteCustoms);
                        // fd.append('payable_Fumigation', payableCustoms);
                        fd.append('subTotal_Fumigation', $("#lblTotalAmountFumigation").val());
                        fd.append('iva_Fumigation', $("#lblTotalTaxAmountFumigation").val());
                        fd.append('retefuente_Fumigation', $("#lblAllowanceAmountFumigation").val());
                        fd.append('payable_Fumigation', $("#lblPayableAmountFumigation").val());
                    } else {

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

                    $('#loading').show();
                    $.ajax({
                        url: base_url + "/save_export_documents",
                        type: 'post',
                        data: fd,
                        contentType: false,
                        processData: false,
                        success: function(jsonResult) {
                            $('#loading').hide();

                            selectedInvoiceId = 0;
                            selectedExportId = 0;
                            selectedExportType = 0;

                            if (jsonResult.redirect == true) {
                                window.location.replace(login_url);
                            } else if (jsonResult.result != '') {
                                toastr.clear();
                                toastr.success(data_updated);

                                resetForm();
                                $('#btnResetFumigation').hide();
                                $('#btnSaveFumigation').text(text_save);
                                $("#divInvoiceListFumigation").show();

                                $("#xin_table_invoice_fumigation").DataTable({
                                    data: JSON.parse(jsonResult.updatedlist),
                                    columns: [{
                                            data: "id",
                                            render: function(data, type, row) {
                                                return `
                                                        <button type="button" class="btn icon-btn btn-xs btn-success waves-effect waves-light" data-role="editfumigation_invoice" data-export_id="${row.export_id}" data-id="${data}" title="<?php echo $this->lang->line("edit"); ?>">
                                                            <span class="fas fa-pencil"></span>
                                                        </button>
                                                        
                                                        <button type="button" style="margin-left:5px;" class="btn icon-btn btn-xs btn-delete waves-effect waves-light" data-export_id="${row.export_id}" data-role="deletefumigation_invoice" data-id="${data}" title="<?php echo $this->lang->line("delete"); ?>">
                                                            <span class="fas fa-trash"></span>
                                                        </button>
                                                    `;
                                            }
                                        },
                                        {
                                            data: "invoice_no"
                                        },
                                        {
                                            data: "supplier_name"
                                        },
                                        {
                                            data: "invoice_date"
                                        },
                                        {
                                            data: "sub_total",
                                            render: function(data) {
                                                return parseFloat(data).toFixed(2);
                                            }
                                        },
                                        {
                                            data: "tax_total",
                                            render: function(data) {
                                                return parseFloat(data).toFixed(2);
                                            }
                                        },
                                        {
                                            data: "allowance_total",
                                            render: function(data) {
                                                return parseFloat(data).toFixed(2);
                                            }
                                        },
                                        {
                                            data: "payable_total",
                                            render: function(data) {
                                                return parseFloat(data).toFixed(2);
                                            }
                                        },
                                        {
                                            data: "container_value_total",
                                            render: function(data) {
                                                return parseFloat(data).toFixed(2);
                                            }
                                        }
                                    ],
                                    scrollX: true, // ✅ Enables horizontal scrolling
                                    autoWidth: true, // ✅ Enables automatic column sizing
                                    scrollCollapse: true,
                                    responsive: false, // ✅ Keep this false with scrollX to avoid conflict
                                    bDestroy: true,
                                    paging: false,
                                    searching: false,
                                    sorting: false,
                                    language: {
                                        url: datatable_language
                                    }
                                });

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
            }
        });

        $("#btnSaveCoteros").click(function() {

            var arrUpdateContainerValue = [];
            var containerData = <?php echo json_encode($containerDetails); ?>;
            var totalContainerValue = 0;

            $.each(containerData, function(i, item) {

                var mappingId = item.dispatch_id;
                var containerNumber = item.container_number;
                var isValid = true;

                if (mappingId != null && mappingId != '' && mappingId != undefined && mappingId > 0) {

                    var updatedContainerValue = parseFloat($('input[name="coteros_container_value[' + mappingId + ']"]').val()) || 0;
                    arrUpdateContainerValue.push({
                        mappingid: mappingId,
                        containerNumber: containerNumber,
                        updatedContainerValue: updatedContainerValue
                    });

                    totalContainerValue += updatedContainerValue;
                }
            });

            if (arrUpdateContainerValue.length > 0) {

                if (fileExtension == "pdf" || fileExtension == "PDF" || fileExtension == "") {
                    totalPayableAmount = parseFloat($("#payable_coteros").val()) || 0;
                }

                // if (totalContainerValue != totalPayableAmount) {
                //     toastr.clear();
                //     toastr.error(total_error);
                //     return false;
                // }

                var isValid1 = true,
                    isValid2 = true,
                    isValid3 = true,
                    isValid4 = true,
                    isValid5 = true,
                    isValid6 = true,
                    isValid7 = true;
                if ((fileExtension != "xml" || fileExtension != "XML" || fileExtension == "") && $('#divPdfCoteros').is(':visible')) {

                    if ($("#invoice_number_coteros").val().length == 0) {
                        $("#error-invoice_no_coteros").show();
                        isValid1 = false;
                    } else {
                        $("#error-invoice_no_coteros").hide();
                        isValid1 = true;
                    }

                    if ($("#supplier_name_coteros").val() == 0) {
                        $("#error-supplier_name_coteros").show();
                        isValid2 = false;
                    } else {
                        $("#error-supplier_name_coteros").hide();
                        isValid2 = true;
                    }

                    if ($("#issued_date_coteros").val().length == 0) {
                        $("#error-issued_date_coteros").show();
                        isValid3 = false;
                    } else {
                        $("#error-issued_date_coteros").hide();
                        isValid3 = true;
                    }

                    if ($("#subtotal_coteros").val().length == 0) {
                        $("#error-subtotal_coteros").show();
                        isValid4 = false;
                    } else {
                        $("#error-subtotal_coteros").hide();
                        isValid4 = true;
                    }

                    if ($("#iva_coteros").val().length == 0) {
                        $("#error-iva_coteros").show();
                        isValid5 = false;
                    } else {
                        $("#error-iva_coteros").hide();
                        isValid5 = true;
                    }

                    if ($("#retefuente_coteros").val().length == 0) {
                        $("#error-retefuente_coteros").show();
                        isValid6 = false;
                    } else {
                        $("#error-retefuente_coteros").hide();
                        isValid6 = true;
                    }

                    if ($("#payable_coteros").val().length == 0) {
                        $("#error-payable_coteros").show();
                        isValid7 = false;
                    } else {
                        $("#error-payable_coteros").hide();
                        isValid7 = true;
                    }
                }

                if (isValid1 && isValid2 && isValid3 && isValid4 && isValid5 && isValid6 && isValid7) {

                    var fd = new FormData();
                    fd.append("exportId", $("#hdnExportId").val());
                    fd.append("originId", $("#hdnOriginId").val());
                    fd.append('saNumber', $("#hdnSaNumber").val());
                    fd.append("fileExtension", fileExtension);
                    fd.append("updateContainerValueData_Coteros", JSON.stringify(arrUpdateContainerValue));
                    fd.append("uploadPdfFileCoteros", uploadPdfFileCustomAgency);

                    if (fileExtension == "xml" || fileExtension == "XML") {

                        fd.append('invoiceNo_Coteros', $("#lblInvoiceNoCoteros").text());
                        fd.append('supplierName_Coteros', supplierId);
                        fd.append('formattedDate_Coteros', $("#lblIssuedDateCoteros").text());
                        // fd.append('subTotal_Coteros', subTotalCustoms);
                        // fd.append('iva_Coteros', ivaCustoms);
                        // fd.append('retefuente_Coteros', retefuenteCustoms);
                        // fd.append('payable_Coteros', payableCustoms);
                        fd.append('subTotal_Coteros', $("#lblTotalAmountCoteros").val());
                        fd.append('iva_Coteros', $("#lblTotalTaxAmountCoteros").val());
                        fd.append('retefuente_Coteros', $("#lblAllowanceAmountCoteros").val());
                        fd.append('payable_Coteros', $("#lblPayableAmountCoteros").val());
                    } else {
                        fd.append('invoiceNo_Coteros', $("#invoice_number_coteros").val());
                        fd.append('supplierName_Coteros', $("#supplier_name_coteros").val());
                        fd.append('formattedDate_Coteros', $("#formatted_date_coteros").val());
                        fd.append('subTotal_Coteros', $("#subtotal_coteros").val());
                        fd.append('iva_Coteros', $("#iva_coteros").val());
                        fd.append('retefuente_Coteros', $("#retefuente_coteros").val());
                        fd.append('payable_Coteros', $("#payable_coteros").val());
                    }

                    fd.append("csrf_cgrerp", $("#hdnCsrf").val());
                    fd.append("add_type", 6);

                    $('#loading').show();
                    $.ajax({
                        url: base_url + "/save_export_documents",
                        type: 'post',
                        data: fd,
                        contentType: false,
                        processData: false,
                        success: function(jsonResult) {
                            $('#loading').hide();

                            selectedInvoiceId = 0;
                            selectedExportId = 0;
                            selectedExportType = 0;

                            if (jsonResult.redirect == true) {
                                window.location.replace(login_url);
                            } else if (jsonResult.result != '') {
                                toastr.clear();
                                toastr.success(data_updated);

                                resetForm();
                                $('#btnResetCoteros').hide();
                                $('#btnSaveCoteros').text(text_save);
                                $("#divInvoiceListCoteros").show();

                                $("#xin_table_invoice_coteros").DataTable({
                                    data: JSON.parse(jsonResult.updatedlist),
                                    columns: [{
                                            data: "id",
                                            render: function(data, type, row) {
                                                return `
                                                        <button type="button" class="btn icon-btn btn-xs btn-success waves-effect waves-light" data-role="editcoteros_invoice" data-export_id="${row.export_id}" data-id="${data}" title="<?php echo $this->lang->line("edit"); ?>">
                                                            <span class="fas fa-pencil"></span>
                                                        </button>
                                                        
                                                        <button type="button" style="margin-left:5px;" class="btn icon-btn btn-xs btn-delete waves-effect waves-light" data-export_id="${row.export_id}" data-role="deletecoteros_invoice" data-id="${data}" title="<?php echo $this->lang->line("delete"); ?>">
                                                            <span class="fas fa-trash"></span>
                                                        </button>
                                                    `;
                                            }
                                        },
                                        {
                                            data: "invoice_no"
                                        },
                                        {
                                            data: "supplier_name"
                                        },
                                        {
                                            data: "invoice_date"
                                        },
                                        {
                                            data: "sub_total",
                                            render: function(data) {
                                                return parseFloat(data).toFixed(2);
                                            }
                                        },
                                        {
                                            data: "tax_total",
                                            render: function(data) {
                                                return parseFloat(data).toFixed(2);
                                            }
                                        },
                                        {
                                            data: "allowance_total",
                                            render: function(data) {
                                                return parseFloat(data).toFixed(2);
                                            }
                                        },
                                        {
                                            data: "payable_total",
                                            render: function(data) {
                                                return parseFloat(data).toFixed(2);
                                            }
                                        },
                                        {
                                            data: "container_value_total",
                                            render: function(data) {
                                                return parseFloat(data).toFixed(2);
                                            }
                                        }
                                    ],
                                    scrollX: true, // ✅ Enables horizontal scrolling
                                    autoWidth: true, // ✅ Enables automatic column sizing
                                    scrollCollapse: true,
                                    responsive: false, // ✅ Keep this false with scrollX to avoid conflict
                                    bDestroy: true,
                                    paging: false,
                                    searching: false,
                                    sorting: false,
                                    language: {
                                        url: datatable_language
                                    }
                                });
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
            }
        });

        $("#btnSavePhyto").click(function() {

            var arrUpdateContainerValue = [];
            var containerData = <?php echo json_encode($containerDetails); ?>;
            var totalContainerValue = 0;

            $.each(containerData, function(i, item) {

                var mappingId = item.dispatch_id;
                var containerNumber = item.container_number;
                var isValid = true;

                if (mappingId != null && mappingId != '' && mappingId != undefined && mappingId > 0) {

                    var updatedContainerValue = parseFloat($('input[name="phyto_container_value[' + mappingId + ']"]').val()) || 0;
                    arrUpdateContainerValue.push({
                        mappingid: mappingId,
                        containerNumber: containerNumber,
                        updatedContainerValue: updatedContainerValue
                    });

                    totalContainerValue += updatedContainerValue;
                }
            });

            if (arrUpdateContainerValue.length > 0) {

                if (fileExtension == "pdf" || fileExtension == "PDF" || fileExtension == "") {
                    totalPayableAmount = parseFloat($("#payable_phyto").val()) || 0;
                }

                // if (totalContainerValue != totalPayableAmount) {
                //     toastr.clear();
                //     toastr.error(total_error);
                //     return false;
                // }

                var isValid1 = true,
                    isValid2 = true,
                    isValid3 = true,
                    isValid4 = true,
                    isValid5 = true,
                    isValid6 = true,
                    isValid7 = true;
                if ((fileExtension != "xml" || fileExtension != "XML" || fileExtension == "") && $('#divPdfPhyto').is(':visible')) {

                    if ($("#invoice_number_phyto").val().length == 0) {
                        $("#error-invoice_no_phyto").show();
                        isValid1 = false;
                    } else {
                        $("#error-invoice_no_phyto").hide();
                        isValid1 = true;
                    }

                    if ($("#supplier_name_phyto").val() == 0) {
                        $("#error-supplier_name_phyto").show();
                        isValid2 = false;
                    } else {
                        $("#error-supplier_name_phyto").hide();
                        isValid2 = true;
                    }

                    if ($("#issued_date_phyto").val().length == 0) {
                        $("#error-issued_date_phyto").show();
                        isValid3 = false;
                    } else {
                        $("#error-issued_date_phyto").hide();
                        isValid3 = true;
                    }

                    if ($("#subtotal_phyto").val().length == 0) {
                        $("#error-subtotal_phyto").show();
                        isValid4 = false;
                    } else {
                        $("#error-subtotal_phyto").hide();
                        isValid4 = true;
                    }

                    if ($("#iva_phyto").val().length == 0) {
                        $("#error-iva_phyto").show();
                        isValid5 = false;
                    } else {
                        $("#error-iva_phyto").hide();
                        isValid5 = true;
                    }

                    if ($("#retefuente_phyto").val().length == 0) {
                        $("#error-retefuente_phyto").show();
                        isValid6 = false;
                    } else {
                        $("#error-retefuente_phyto").hide();
                        isValid6 = true;
                    }

                    if ($("#payable_phyto").val().length == 0) {
                        $("#error-payable_phyto").show();
                        isValid7 = false;
                    } else {
                        $("#error-payable_phyto").hide();
                        isValid7 = true;
                    }
                }

                if (isValid1 && isValid2 && isValid3 && isValid4 && isValid5 && isValid6 && isValid7) {
                    var fd = new FormData();
                    fd.append("exportId", $("#hdnExportId").val());
                    fd.append("originId", $("#hdnOriginId").val());
                    fd.append('saNumber', $("#hdnSaNumber").val());
                    fd.append("fileExtension", fileExtension);
                    fd.append("updateContainerValueData_Phyto", JSON.stringify(arrUpdateContainerValue));
                    fd.append("uploadPdfFilePhyto", uploadPdfFileCustomAgency);

                    if (fileExtension == "xml" || fileExtension == "XML") {

                        fd.append('invoiceNo_Phyto', $("#lblInvoiceNoPhyto").text());
                        fd.append('supplierName_Phyto', supplierId);
                        fd.append('formattedDate_Phyto', $("#lblIssuedDatePhyto").text());
                        // fd.append('subTotal_Phyto', subTotalCustoms);
                        // fd.append('iva_Phyto', ivaCustoms);
                        // fd.append('retefuente_Phyto', retefuenteCustoms);
                        // fd.append('payable_Phyto', payableCustoms);
                        fd.append('subTotal_Phyto', $("#lblTotalAmountPhyto").val());
                        fd.append('iva_Phyto', $("#lblTotalTaxAmountPhyto").val());
                        fd.append('retefuente_Phyto', $("#lblAllowanceAmountPhyto").val());
                        fd.append('payable_Phyto', $("#lblPayableAmountPhyto").val());
                    } else {

                        fd.append('invoiceNo_Phyto', $("#invoice_number_phyto").val());
                        fd.append('supplierName_Phyto', $("#supplier_name_phyto").val());
                        fd.append('formattedDate_Phyto', $("#formatted_date_phyto").val());
                        fd.append('subTotal_Phyto', $("#subtotal_phyto").val());
                        fd.append('iva_Phyto', $("#iva_phyto").val());
                        fd.append('retefuente_Phyto', $("#retefuente_phyto").val());
                        fd.append('payable_Phyto', $("#payable_phyto").val());
                    }

                    fd.append("csrf_cgrerp", $("#hdnCsrf").val());
                    fd.append("add_type", 5);

                    $('#loading').show();
                    $.ajax({
                        url: base_url + "/save_export_documents",
                        type: 'post',
                        data: fd,
                        contentType: false,
                        processData: false,
                        success: function(jsonResult) {
                            $('#loading').hide();

                            selectedInvoiceId = 0;
                            selectedExportId = 0;
                            selectedExportType = 0;

                            if (jsonResult.redirect == true) {
                                window.location.replace(login_url);
                            } else if (jsonResult.result != '') {
                                toastr.clear();
                                toastr.success(data_updated);

                                resetForm();
                                $('#btnResetPhyto').hide();
                                $('#btnSavePhyto').text(text_save);
                                $("#divInvoiceListPhyto").show();

                                $("#xin_table_invoice_phyto").DataTable({
                                    data: JSON.parse(jsonResult.updatedlist),
                                    columns: [{
                                            data: "id",
                                            render: function(data, type, row) {
                                                return `
                                                        <button type="button" class="btn icon-btn btn-xs btn-success waves-effect waves-light" data-role="editphyto_invoice" data-export_id="${row.export_id}" data-id="${data}" title="<?php echo $this->lang->line("edit"); ?>">
                                                            <span class="fas fa-pencil"></span>
                                                        </button>
                                                        
                                                        <button type="button" style="margin-left:5px;" class="btn icon-btn btn-xs btn-delete waves-effect waves-light" data-export_id="${row.export_id}" data-role="deletephyto_invoice" data-id="${data}" title="<?php echo $this->lang->line("delete"); ?>">
                                                            <span class="fas fa-trash"></span>
                                                        </button>
                                                    `;
                                            }
                                        },
                                        {
                                            data: "invoice_no"
                                        },
                                        {
                                            data: "supplier_name"
                                        },
                                        {
                                            data: "invoice_date"
                                        },
                                        {
                                            data: "sub_total",
                                            render: function(data) {
                                                return parseFloat(data).toFixed(2);
                                            }
                                        },
                                        {
                                            data: "tax_total",
                                            render: function(data) {
                                                return parseFloat(data).toFixed(2);
                                            }
                                        },
                                        {
                                            data: "allowance_total",
                                            render: function(data) {
                                                return parseFloat(data).toFixed(2);
                                            }
                                        },
                                        {
                                            data: "payable_total",
                                            render: function(data) {
                                                return parseFloat(data).toFixed(2);
                                            }
                                        },
                                        {
                                            data: "container_value_total",
                                            render: function(data) {
                                                return parseFloat(data).toFixed(2);
                                            }
                                        }
                                    ],
                                    scrollX: true, // ✅ Enables horizontal scrolling
                                    autoWidth: true, // ✅ Enables automatic column sizing
                                    scrollCollapse: true,
                                    responsive: false, // ✅ Keep this false with scrollX to avoid conflict
                                    bDestroy: true,
                                    paging: false,
                                    searching: false,
                                    sorting: false,
                                    language: {
                                        url: datatable_language
                                    }
                                });
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
            }
        });

        $("#btnSaveIncentives").click(function() {

            var arrUpdateContainerValue = [];
            var containerData = <?php echo json_encode($containerDetails); ?>;
            var totalContainerValue = 0;

            $.each(containerData, function(i, item) {

                var mappingId = item.dispatch_id;
                var containerNumber = item.container_number;
                var isValid = true;

                if (mappingId != null && mappingId != '' && mappingId != undefined && mappingId > 0) {

                    var updatedContainerValue = parseFloat($('input[name="incentives_container_value[' + mappingId + ']"]').val()) || 0;
                    arrUpdateContainerValue.push({
                        mappingid: mappingId,
                        containerNumber: containerNumber,
                        updatedContainerValue: updatedContainerValue
                    });

                    totalContainerValue += updatedContainerValue;
                }
            });

            if (arrUpdateContainerValue.length > 0) {

                if (fileExtension == "pdf" || fileExtension == "PDF" || fileExtension == "") {
                    totalPayableAmount = parseFloat($("#payable_incentives").val()) || 0;
                }

                // if (totalContainerValue != totalPayableAmount) {
                //     toastr.clear();
                //     toastr.error(total_error);
                //     return false;
                // }

                var isValid1 = true,
                    isValid2 = true,
                    isValid3 = true,
                    isValid4 = true,
                    isValid5 = true,
                    isValid6 = true,
                    isValid7 = true;
                if ((fileExtension != "xml" || fileExtension != "XML" || fileExtension == "") && $('#divPdfIncentives').is(':visible')) {

                    if ($("#invoice_number_incentives").val().length == 0) {
                        $("#error-invoice_no_incentives").show();
                        isValid1 = false;
                    } else {
                        $("#error-invoice_no_incentives").hide();
                        isValid1 = true;
                    }

                    if ($("#supplier_name_incentives").val() == 0) {
                        $("#error-supplier_name_incentives").show();
                        isValid2 = false;
                    } else {
                        $("#error-supplier_name_incentives").hide();
                        isValid2 = true;
                    }

                    if ($("#issued_date_incentives").val().length == 0) {
                        $("#error-issued_date_incentives").show();
                        isValid3 = false;
                    } else {
                        $("#error-issued_date_incentives").hide();
                        isValid3 = true;
                    }

                    if ($("#subtotal_incentives").val().length == 0) {
                        $("#error-subtotal_incentives").show();
                        isValid4 = false;
                    } else {
                        $("#error-subtotal_incentives").hide();
                        isValid4 = true;
                    }

                    if ($("#iva_incentives").val().length == 0) {
                        $("#error-iva_incentives").show();
                        isValid5 = false;
                    } else {
                        $("#error-iva_incentives").hide();
                        isValid5 = true;
                    }

                    if ($("#retefuente_incentives").val().length == 0) {
                        $("#error-retefuente_incentives").show();
                        isValid6 = false;
                    } else {
                        $("#error-retefuente_incentives").hide();
                        isValid6 = true;
                    }

                    if ($("#payable_incentives").val().length == 0) {
                        $("#error-payable_incentives").show();
                        isValid7 = false;
                    } else {
                        $("#error-payable_incentives").hide();
                        isValid7 = true;
                    }
                }

                if (isValid1 && isValid2 && isValid3 && isValid4 && isValid5 && isValid6 && isValid7) {
                    var fd = new FormData();
                    fd.append("exportId", $("#hdnExportId").val());
                    fd.append("originId", $("#hdnOriginId").val());
                    fd.append('saNumber', $("#hdnSaNumber").val());
                    fd.append("fileExtension", fileExtension);
                    fd.append("updateContainerValueData_Incentives", JSON.stringify(arrUpdateContainerValue));
                    fd.append("uploadPdfFileIncentives", uploadPdfFileCustomAgency);

                    if (fileExtension == "xml" || fileExtension == "XML") {

                        fd.append('invoiceNo_Incentives', $("#lblInvoiceNoIncentives").text());
                        fd.append('supplierName_Incentives', supplierId);
                        fd.append('formattedDate_Incentives', $("#lblIssuedDateIncentives").text());
                        // fd.append('subTotal_Incentives', subTotalCustoms);
                        // fd.append('iva_Incentives', ivaCustoms);
                        // fd.append('retefuente_Incentives', retefuenteCustoms);
                        // fd.append('payable_Incentives', payableCustoms);
                        fd.append('subTotal_Incentives', $("#lblTotalAmountIncentives").val());
                        fd.append('iva_Incentives', $("#lblTotalTaxAmountIncentives").val());
                        fd.append('retefuente_Incentives', $("#lblAllowanceAmountIncentives").val());
                        fd.append('payable_Incentives', $("#lblPayableAmountIncentives").val());
                    } else {

                        fd.append('invoiceNo_Incentives', $("#invoice_number_incentives").val());
                        fd.append('supplierName_Incentives', $("#supplier_name_incentives").val());
                        fd.append('formattedDate_Incentives', $("#formatted_date_incentives").val());
                        fd.append('subTotal_Incentives', $("#subtotal_incentives").val());
                        fd.append('iva_Incentives', $("#iva_incentives").val());
                        fd.append('retefuente_Incentives', $("#retefuente_incentives").val());
                        fd.append('payable_Incentives', $("#payable_incentives").val());
                    }

                    fd.append("csrf_cgrerp", $("#hdnCsrf").val());
                    fd.append("add_type", 7);

                    $('#loading').show();
                    $.ajax({
                        url: base_url + "/save_export_documents",
                        type: 'post',
                        data: fd,
                        contentType: false,
                        processData: false,
                        success: function(jsonResult) {
                            $('#loading').hide();

                            selectedInvoiceId = 0;
                            selectedExportId = 0;
                            selectedExportType = 0;

                            if (jsonResult.redirect == true) {
                                window.location.replace(login_url);
                            } else if (jsonResult.result != '') {
                                toastr.clear();
                                toastr.success(data_updated);

                                resetForm();
                                $('#btnResetIncentives').hide();
                                $('#btnSaveIncentives').text(text_save);
                                $("#divInvoiceListIncentives").show();

                                $("#xin_table_invoice_incentives").DataTable({
                                    data: JSON.parse(jsonResult.updatedlist),
                                    columns: [{
                                            data: "id",
                                            render: function(data, type, row) {
                                                return `
                                                        <button type="button" class="btn icon-btn btn-xs btn-success waves-effect waves-light" data-role="editincentives_invoice" data-export_id="${row.export_id}" data-id="${data}" title="<?php echo $this->lang->line("edit"); ?>">
                                                            <span class="fas fa-pencil"></span>
                                                        </button>
                                                        
                                                        <button type="button" style="margin-left:5px;" class="btn icon-btn btn-xs btn-delete waves-effect waves-light" data-export_id="${row.export_id}" data-role="deleteincentives_invoice" data-id="${data}" title="<?php echo $this->lang->line("delete"); ?>">
                                                            <span class="fas fa-trash"></span>
                                                        </button>
                                                    `;
                                            }
                                        },
                                        {
                                            data: "invoice_no"
                                        },
                                        {
                                            data: "supplier_name"
                                        },
                                        {
                                            data: "invoice_date"
                                        },
                                        {
                                            data: "sub_total",
                                            render: function(data) {
                                                return parseFloat(data).toFixed(2);
                                            }
                                        },
                                        {
                                            data: "tax_total",
                                            render: function(data) {
                                                return parseFloat(data).toFixed(2);
                                            }
                                        },
                                        {
                                            data: "allowance_total",
                                            render: function(data) {
                                                return parseFloat(data).toFixed(2);
                                            }
                                        },
                                        {
                                            data: "payable_total",
                                            render: function(data) {
                                                return parseFloat(data).toFixed(2);
                                            }
                                        },
                                        {
                                            data: "container_value_total",
                                            render: function(data) {
                                                return parseFloat(data).toFixed(2);
                                            }
                                        }
                                    ],
                                    scrollX: true, // ✅ Enables horizontal scrolling
                                    autoWidth: true, // ✅ Enables automatic column sizing
                                    scrollCollapse: true,
                                    responsive: false, // ✅ Keep this false with scrollX to avoid conflict
                                    bDestroy: true,
                                    paging: false,
                                    searching: false,
                                    sorting: false,
                                    language: {
                                        url: datatable_language
                                    }
                                });
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
            }
        });

        $("#btnSaveRemobilization").click(function() {

            var arrUpdateContainerValue = [];
            var containerData = <?php echo json_encode($containerDetails); ?>;
            var totalContainerValue = 0;

            $.each(containerData, function(i, item) {

                var mappingId = item.dispatch_id;
                var containerNumber = item.container_number;
                var isValid = true;

                if (mappingId != null && mappingId != '' && mappingId != undefined && mappingId > 0) {

                    var updatedContainerValue = parseFloat($('input[name="remobilization_container_value[' + mappingId + ']"]').val()) || 0;
                    arrUpdateContainerValue.push({
                        mappingid: mappingId,
                        containerNumber: containerNumber,
                        updatedContainerValue: updatedContainerValue
                    });

                    totalContainerValue += updatedContainerValue;
                }
            });

            if (arrUpdateContainerValue.length > 0) {

                if (fileExtension == "pdf" || fileExtension == "PDF" || fileExtension == "") {
                    totalPayableAmount = parseFloat($("#payable_remobilization").val()) || 0;
                }

                // if (totalContainerValue != totalPayableAmount) {
                //     toastr.clear();
                //     toastr.error(total_error);
                //     return false;
                // }

                var isValid1 = true,
                    isValid2 = true,
                    isValid3 = true,
                    isValid4 = true,
                    isValid5 = true,
                    isValid6 = true,
                    isValid7 = true;
                if ((fileExtension != "xml" || fileExtension != "XML" || fileExtension == "") && $('#divPdfRemobilization').is(':visible')) {

                    if ($("#invoice_number_remobilization").val().length == 0) {
                        $("#error-invoice_no_remobilization").show();
                        isValid1 = false;
                    } else {
                        $("#error-invoice_no_remobilization").hide();
                        isValid1 = true;
                    }

                    if ($("#supplier_name_remobilization").val() == 0) {
                        $("#error-supplier_name_remobilization").show();
                        isValid2 = false;
                    } else {
                        $("#error-supplier_name_remobilization").hide();
                        isValid2 = true;
                    }

                    if ($("#issued_date_remobilization").val().length == 0) {
                        $("#error-issued_date_remobilization").show();
                        isValid3 = false;
                    } else {
                        $("#error-issued_date_remobilization").hide();
                        isValid3 = true;
                    }

                    if ($("#subtotal_remobilization").val().length == 0) {
                        $("#error-subtotal_remobilization").show();
                        isValid4 = false;
                    } else {
                        $("#error-subtotal_remobilization").hide();
                        isValid4 = true;
                    }

                    if ($("#iva_remobilization").val().length == 0) {
                        $("#error-iva_remobilization").show();
                        isValid5 = false;
                    } else {
                        $("#error-iva_remobilization").hide();
                        isValid5 = true;
                    }

                    if ($("#retefuente_remobilization").val().length == 0) {
                        $("#error-retefuente_remobilization").show();
                        isValid6 = false;
                    } else {
                        $("#error-retefuente_remobilization").hide();
                        isValid6 = true;
                    }

                    if ($("#payable_remobilization").val().length == 0) {
                        $("#error-payable_remobilization").show();
                        isValid7 = false;
                    } else {
                        $("#error-payable_remobilization").hide();
                        isValid7 = true;
                    }
                }

                if (isValid1 && isValid2 && isValid3 && isValid4 && isValid5 && isValid6 && isValid7) {
                    var fd = new FormData();
                    fd.append("exportId", $("#hdnExportId").val());
                    fd.append("originId", $("#hdnOriginId").val());
                    fd.append('saNumber', $("#hdnSaNumber").val());
                    fd.append("fileExtension", fileExtension);
                    fd.append("updateContainerValueData_Remobilization", JSON.stringify(arrUpdateContainerValue));
                    fd.append("uploadPdfFileRemobilization", uploadPdfFileCustomAgency);

                    if (fileExtension == "xml" || fileExtension == "XML") {

                        fd.append('invoiceNo_Remobilization', $("#lblInvoiceNoRemobilization").text());
                        fd.append('supplierName_Remobilization', supplierId);
                        fd.append('formattedDate_Remobilization', $("#lblIssuedDateRemobilization").text());
                        // fd.append('subTotal_Remobilization', subTotalCustoms);
                        // fd.append('iva_Remobilization', ivaCustoms);
                        // fd.append('retefuente_Remobilization', retefuenteCustoms);
                        // fd.append('payable_Remobilization', payableCustoms);
                        fd.append('subTotal_Remobilization', $("#lblTotalAmountRemobilization").val());
                        fd.append('iva_Remobilization', $("#lblTotalTaxAmountRemobilization").val());
                        fd.append('retefuente_Remobilization', $("#lblAllowanceAmountRemobilization").val());
                        fd.append('payable_Remobilization', $("#lblPayableAmountRemobilization").val());
                    } else {

                        fd.append('invoiceNo_Remobilization', $("#invoice_number_remobilization").val());
                        fd.append('supplierName_Remobilization', $("#supplier_name_remobilization").val());
                        fd.append('formattedDate_Remobilization', $("#formatted_date_remobilization").val());
                        fd.append('subTotal_Remobilization', $("#subtotal_remobilization").val());
                        fd.append('iva_Remobilization', $("#iva_remobilization").val());
                        fd.append('retefuente_Remobilization', $("#retefuente_remobilization").val());
                        fd.append('payable_Remobilization', $("#payable_remobilization").val());
                    }

                    fd.append("csrf_cgrerp", $("#hdnCsrf").val());
                    fd.append("add_type", 8);

                    $('#loading').show();
                    $.ajax({
                        url: base_url + "/save_export_documents",
                        type: 'post',
                        data: fd,
                        contentType: false,
                        processData: false,
                        success: function(jsonResult) {
                            $('#loading').hide();

                            selectedInvoiceId = 0;
                            selectedExportId = 0;
                            selectedExportType = 0;

                            if (jsonResult.redirect == true) {
                                window.location.replace(login_url);
                            } else if (jsonResult.result != '') {
                                toastr.clear();
                                toastr.success(data_updated);

                                resetForm();
                                $('#btnResetRemobilization').hide();
                                $('#btnSaveRemobilization').text(text_save);
                                $("#divInvoiceListRemobilization").show();

                                $("#xin_table_invoice_remobilization").DataTable({
                                    data: JSON.parse(jsonResult.updatedlist),
                                    columns: [{
                                            data: "id",
                                            render: function(data, type, row) {
                                                return `
                                                        <button type="button" class="btn icon-btn btn-xs btn-success waves-effect waves-light" data-role="editremobilization_invoice" data-export_id="${row.export_id}" data-id="${data}" title="<?php echo $this->lang->line("edit"); ?>">
                                                            <span class="fas fa-pencil"></span>
                                                        </button>
                                                        
                                                        <button type="button" style="margin-left:5px;" class="btn icon-btn btn-xs btn-delete waves-effect waves-light" data-export_id="${row.export_id}" data-role="deleteremobilization_invoice" data-id="${data}" title="<?php echo $this->lang->line("delete"); ?>">
                                                            <span class="fas fa-trash"></span>
                                                        </button>
                                                    `;
                                            }
                                        },
                                        {
                                            data: "invoice_no"
                                        },
                                        {
                                            data: "supplier_name"
                                        },
                                        {
                                            data: "invoice_date"
                                        },
                                        {
                                            data: "sub_total",
                                            render: function(data) {
                                                return parseFloat(data).toFixed(2);
                                            }
                                        },
                                        {
                                            data: "tax_total",
                                            render: function(data) {
                                                return parseFloat(data).toFixed(2);
                                            }
                                        },
                                        {
                                            data: "allowance_total",
                                            render: function(data) {
                                                return parseFloat(data).toFixed(2);
                                            }
                                        },
                                        {
                                            data: "payable_total",
                                            render: function(data) {
                                                return parseFloat(data).toFixed(2);
                                            }
                                        },
                                        {
                                            data: "container_value_total",
                                            render: function(data) {
                                                return parseFloat(data).toFixed(2);
                                            }
                                        }
                                    ],
                                    scrollX: true, // ✅ Enables horizontal scrolling
                                    autoWidth: true, // ✅ Enables automatic column sizing
                                    scrollCollapse: true,
                                    responsive: false, // ✅ Keep this false with scrollX to avoid conflict
                                    bDestroy: true,
                                    paging: false,
                                    searching: false,
                                    sorting: false,
                                    language: {
                                        url: datatable_language
                                    }
                                });
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
            }
        });

        $("#btnSaveContainerCost").click(function() {

            var arrUpdateContainerValue = [];
            var containerData = <?php echo json_encode($containerDetails); ?>;

            $.each(containerData, function(i, item) {

                var mappingId = item.dispatch_id;
                var containerNumber = item.container_number;
                var isValid = true;

                if (mappingId != null && mappingId != '' && mappingId != undefined && mappingId > 0) {

                    var updatedContainerCostValue = parseFloat($('input[name="containercost_container_value[' + mappingId + ']"]').val()) || 0;
                    var updatedContainerCostTrmValue = parseFloat($('input[name="containercosttrm_container_value[' + mappingId + ']"]').val()) || 0;
                    arrUpdateContainerValue.push({
                        mappingid: mappingId,
                        containerNumber: containerNumber,
                        updatedContainerCostValue: updatedContainerCostValue,
                        updatedContainerCostTrmValue: updatedContainerCostTrmValue
                    });

                }
            });

            if (arrUpdateContainerValue.length > 0) {

                // if (fileExtension == "pdf" || fileExtension == "PDF" || fileExtension == "") {
                //     totalPayableAmount = parseFloat($("#payable_fumigation").val()) || 0;
                // }

                // if (totalContainerValue != totalPayableAmount) {
                //     toastr.clear();
                //     toastr.error(total_error);
                //     return false;
                // }

                var isValid1 = true,
                    isValid2 = true,
                    isValid3 = true,
                    isValid4 = true,
                    isValid5 = true,
                    isValid6 = true,
                    isValid7 = true;
                // if ((fileExtension != "xml" || fileExtension != "XML" || fileExtension == "") && $('#divPdfFumigation').is(':visible')) {

                //     if ($("#invoice_number_fumigation").val().length == 0) {
                //         $("#error-invoice_no_fumigation").show();
                //         isValid1 = false;
                //     } else {
                //         $("#error-invoice_no_fumigation").hide();
                //         isValid1 = true;
                //     }

                //     if ($("#supplier_name_fumigation").val() == 0) {
                //         $("#error-supplier_name_fumigation").show();
                //         isValid2 = false;
                //     } else {
                //         $("#error-supplier_name_fumigation").hide();
                //         isValid2 = true;
                //     }

                //     if ($("#issued_date_fumigation").val().length == 0) {
                //         $("#error-issued_date_fumigation").show();
                //         isValid3 = false;
                //     } else {
                //         $("#error-issued_date_fumigation").hide();
                //         isValid3 = true;
                //     }

                //     if ($("#subtotal_fumigation").val().length == 0) {
                //         $("#error-subtotal_fumigation").show();
                //         isValid4 = false;
                //     } else {
                //         $("#error-subtotal_fumigation").hide();
                //         isValid4 = true;
                //     }

                //     if ($("#iva_fumigation").val().length == 0) {
                //         $("#error-iva_fumigation").show();
                //         isValid5 = false;
                //     } else {
                //         $("#error-iva_fumigation").hide();
                //         isValid5 = true;
                //     }

                //     if ($("#retefuente_fumigation").val().length == 0) {
                //         $("#error-retefuente_fumigation").show();
                //         isValid6 = false;
                //     } else {
                //         $("#error-retefuente_fumigation").hide();
                //         isValid6 = true;
                //     }

                //     if ($("#payable_fumigation").val().length == 0) {
                //         $("#error-payable_fumigation").show();
                //         isValid7 = false;
                //     } else {
                //         $("#error-payable_fumigation").hide();
                //         isValid7 = true;
                //     }
                // }

                if (isValid1 && isValid2 && isValid3 && isValid4 && isValid5 && isValid6 && isValid7) {

                    var fd = new FormData();
                    fd.append("exportId", $("#hdnExportId").val());
                    fd.append("originId", $("#hdnOriginId").val());
                    fd.append('saNumber', $("#hdnSaNumber").val());
                    //fd.append("fileExtension", fileExtension);
                    fd.append("updateContainerValueData_ContainerCost", JSON.stringify(arrUpdateContainerValue));
                    //fd.append("uploadPdfFileFumigation", uploadPdfFileCustomAgency);

                    // if (fileExtension == "xml" || fileExtension == "XML") {

                    //     fd.append('invoiceNo_Fumigation', $("#lblInvoiceNoFumigation").text());
                    //     fd.append('supplierName_Fumigation', supplierId);
                    //     fd.append('formattedDate_Fumigation', $("#lblIssuedDateFumigation").text());
                    //     fd.append('subTotal_Fumigation', subTotalCustoms);
                    //     fd.append('iva_Fumigation', ivaCustoms);
                    //     fd.append('retefuente_Fumigation', retefuenteCustoms);
                    //     fd.append('payable_Fumigation', payableCustoms);
                    // } else {

                    //     fd.append('invoiceNo_Fumigation', $("#invoice_number_fumigation").val());
                    //     fd.append('supplierName_Fumigation', $("#supplier_name_fumigation").val());
                    //     fd.append('formattedDate_Fumigation', $("#formatted_date_fumigation").val());
                    //     fd.append('subTotal_Fumigation', $("#subtotal_fumigation").val());
                    //     fd.append('iva_Fumigation', $("#iva_fumigation").val());
                    //     fd.append('retefuente_Fumigation', $("#retefuente_fumigation").val());
                    //     fd.append('payable_Fumigation', $("#payable_fumigation").val());
                    // }

                    fd.append("csrf_cgrerp", $("#hdnCsrf").val());
                    fd.append("add_type", 10);

                    $('#loading').show();
                    $.ajax({
                        url: base_url + "/save_export_documents",
                        type: 'post',
                        data: fd,
                        contentType: false,
                        processData: false,
                        success: function(jsonResult) {
                            $('#loading').hide();

                            selectedInvoiceId = 0;
                            selectedExportId = 0;
                            selectedExportType = 0;

                            if (jsonResult.redirect == true) {
                                window.location.replace(login_url);
                            } else if (jsonResult.result != '') {
                                toastr.clear();
                                toastr.success(data_updated);
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
            }
        });

        // $("#btnSaveContainerLoadingCost").click(function() {

        //     var arrUpdateContainerLoadingValue = [];
        //     var containerData = <?php echo json_encode($containerDetails); ?>;

        //     $.each(containerData, function(i, item) {

        //         var mappingId = item.dispatch_id;
        //         var containerNumber = item.container_number;
        //         var isValid = true;

        //         if (mappingId != null && mappingId != '' && mappingId != undefined && mappingId > 0) {

        //             var updatedContainerLoadingCostCostValue = parseFloat($('input[name="containercostloading_container_value[' + mappingId + ']"]').val()) || 0;
        //             arrUpdateContainerLoadingValue.push({
        //                 mappingid: mappingId,
        //                 containerNumber: containerNumber,
        //                 updatedContainerLoadingCostValue: updatedContainerLoadingCostCostValue,
        //             });

        //         }
        //     });

        //     if (arrUpdateContainerLoadingValue.length > 0) {

        //         var isValid1 = true;

        //         if (isValid1) {

        //             var fd = new FormData();
        //             fd.append("exportId", $("#hdnExportId").val());
        //             fd.append("originId", $("#hdnOriginId").val());
        //             fd.append('saNumber', $("#hdnSaNumber").val());
        //             fd.append("updateContainerValueData_ContainerLoadingCost", JSON.stringify(arrUpdateContainerLoadingValue));

        //             fd.append("csrf_cgrerp", $("#hdnCsrf").val());
        //             fd.append("add_type", 11);

        //             $('#loading').show();
        //             $.ajax({
        //                 url: base_url + "/save_export_documents",
        //                 type: 'post',
        //                 data: fd,
        //                 contentType: false,
        //                 processData: false,
        //                 success: function(jsonResult) {
        //                     $('#loading').hide();

        //                     selectedInvoiceId = 0;
        //                     selectedExportId = 0;
        //                     selectedExportType = 0;

        //                     if (jsonResult.redirect == true) {
        //                         window.location.replace(login_url);
        //                     } else if (jsonResult.result != '') {
        //                         toastr.clear();
        //                         toastr.success(data_updated);
        //                     } else if (jsonResult.error != '') {
        //                         toastr.clear();
        //                         toastr.error(jsonResult.error);
        //                         $('input[name="csrf_cgrerp"]').val(jsonResult.csrf_hash);
        //                     } else if (jsonResult.warning != '') {
        //                         toastr.clear();
        //                         toastr.warning(jsonResult.warning);
        //                         $('input[name="csrf_cgrerp"]').val(jsonResult.csrf_hash);
        //                     } else {
        //                         toastr.clear();
        //                     }
        //                 }
        //             });
        //         }
        //     }
        // });

        $("#btnSaveContainerLoadingCost").click(function() {

            var arrUpdateContainerValue = [];
            var containerData = <?php echo json_encode($containerDetails); ?>;
            var totalContainerValue = 0;

            $.each(containerData, function(i, item) {

                var mappingId = item.dispatch_id;
                var containerNumber = item.container_number;
                var isValid = true;

                if (mappingId != null && mappingId != '' && mappingId != undefined && mappingId > 0) {

                    var updatedContainerValue = parseFloat($('input[name="containerloadingcost_container_value[' + mappingId + ']"]').val()) || 0;
                    arrUpdateContainerValue.push({
                        mappingid: mappingId,
                        containerNumber: containerNumber,
                        updatedContainerValue: updatedContainerValue
                    });

                    totalContainerValue += updatedContainerValue;
                }
            });

            if (arrUpdateContainerValue.length > 0) {

                if (fileExtension == "pdf" || fileExtension == "PDF" || fileExtension == "") {
                    totalPayableAmount = parseFloat($("#payable_containerloadingcost").val()) || 0;
                }

                // if (totalContainerValue != totalPayableAmount) {
                //     toastr.clear();
                //     toastr.error(total_error);
                //     return false;
                // }

                var isValid1 = true,
                    isValid2 = true,
                    isValid3 = true,
                    isValid4 = true,
                    isValid5 = true,
                    isValid6 = true,
                    isValid7 = true;
                if ((fileExtension != "xml" || fileExtension != "XML" || fileExtension == "") && $('#divPdfContainerLoadingCost').is(':visible')) {

                    if ($("#invoice_number_containerloadingcost").val().length == 0) {
                        $("#error-invoice_no_containerloadingcost").show();
                        isValid1 = false;
                    } else {
                        $("#error-invoice_no_containerloadingcost").hide();
                        isValid1 = true;
                    }

                    if ($("#supplier_name_containerloadingcost").val() == 0) {
                        $("#error-supplier_name_containerloadingcost").show();
                        isValid2 = false;
                    } else {
                        $("#error-supplier_name_containerloadingcost").hide();
                        isValid2 = true;
                    }

                    if ($("#issued_date_containerloadingcost").val().length == 0) {
                        $("#error-issued_date_containerloadingcost").show();
                        isValid3 = false;
                    } else {
                        $("#error-issued_date_containerloadingcost").hide();
                        isValid3 = true;
                    }

                    if ($("#subtotal_containerloadingcost").val().length == 0) {
                        $("#error-subtotal_containerloadingcost").show();
                        isValid4 = false;
                    } else {
                        $("#error-subtotal_containerloadingcost").hide();
                        isValid4 = true;
                    }

                    if ($("#iva_containerloadingcost").val().length == 0) {
                        $("#error-iva_containerloadingcost").show();
                        isValid5 = false;
                    } else {
                        $("#error-iva_containerloadingcost").hide();
                        isValid5 = true;
                    }

                    if ($("#retefuente_containerloadingcost").val().length == 0) {
                        $("#error-retefuente_containerloadingcost").show();
                        isValid6 = false;
                    } else {
                        $("#error-retefuente_containerloadingcost").hide();
                        isValid6 = true;
                    }

                    if ($("#payable_containerloadingcost").val().length == 0) {
                        $("#error-payable_containerloadingcost").show();
                        isValid7 = false;
                    } else {
                        $("#error-payable_containerloadingcost").hide();
                        isValid7 = true;
                    }
                }

                if (isValid1 && isValid2 && isValid3 && isValid4 && isValid5 && isValid6 && isValid7) {
                    var fd = new FormData();
                    fd.append("exportId", $("#hdnExportId").val());
                    fd.append("originId", $("#hdnOriginId").val());
                    fd.append('saNumber', $("#hdnSaNumber").val());
                    fd.append("fileExtension", fileExtension);
                    fd.append("updateContainerValueData_ContainerLoadingCost", JSON.stringify(arrUpdateContainerValue));
                    fd.append("uploadPdfFileContainerLoadingCost", uploadPdfFileCustomAgency);

                    if (fileExtension == "xml" || fileExtension == "XML") {

                        fd.append('invoiceNo_ContainerLoadingCost', $("#lblInvoiceNoContainerLoadingCost").text());
                        fd.append('supplierName_ContainerLoadingCost', supplierId);
                        fd.append('formattedDate_ContainerLoadingCost', $("#lblIssuedDateContainerLoadingCost").text());
                        // fd.append('subTotal_ContainerLoadingCost', subTotalCustoms);
                        // fd.append('iva_ContainerLoadingCost', ivaCustoms);
                        // fd.append('retefuente_ContainerLoadingCost', retefuenteCustoms);
                        // fd.append('payable_ContainerLoadingCost', payableCustoms);
                        fd.append('subTotal_ContainerLoadingCost', $("#lblTotalAmountContainerLoadingCost").val());
                        fd.append('iva_ContainerLoadingCost', $("#lblTotalTaxAmountContainerLoadingCost").val());
                        fd.append('retefuente_ContainerLoadingCost', $("#lblAllowanceAmountContainerLoadingCost").val());
                        fd.append('payable_ContainerLoadingCost', $("#lblPayableAmountContainerLoadingCost").val());
                    } else {

                        fd.append('invoiceNo_ContainerLoadingCost', $("#invoice_number_containerloadingcost").val());
                        fd.append('supplierName_ContainerLoadingCost', $("#supplier_name_containerloadingcost").val());
                        fd.append('formattedDate_ContainerLoadingCost', $("#formatted_date_containerloadingcost").val());
                        fd.append('subTotal_ContainerLoadingCost', $("#subtotal_containerloadingcost").val());
                        fd.append('iva_ContainerLoadingCost', $("#iva_containerloadingcost").val());
                        fd.append('retefuente_ContainerLoadingCost', $("#retefuente_containerloadingcost").val());
                        fd.append('payable_ContainerLoadingCost', $("#payable_containerloadingcost").val());
                    }

                    fd.append("csrf_cgrerp", $("#hdnCsrf").val());
                    fd.append("add_type", 11);

                    $('#loading').show();
                    $.ajax({
                        url: base_url + "/save_export_documents",
                        type: 'post',
                        data: fd,
                        contentType: false,
                        processData: false,
                        success: function(jsonResult) {
                            $('#loading').hide();

                            selectedInvoiceId = 0;
                            selectedExportId = 0;
                            selectedExportType = 0;

                            if (jsonResult.redirect == true) {
                                window.location.replace(login_url);
                            } else if (jsonResult.result != '') {
                                toastr.clear();
                                toastr.success(data_updated);

                                resetForm();
                                $('#btnResetContainerLoadingCost').hide();
                                $('#btnSaveContainerLoadingCost').text(text_save);
                                $("#divInvoiceListContainerLoadingCost").show();

                                $("#xin_table_invoice_containerloadingcost").DataTable({
                                    data: JSON.parse(jsonResult.updatedlist),
                                    columns: [{
                                            data: "id",
                                            render: function(data, type, row) {
                                                return `
                                                        <button type="button" class="btn icon-btn btn-xs btn-success waves-effect waves-light" data-role="editcontainerloadingcost_invoice" data-export_id="${row.export_id}" data-id="${data}" title="<?php echo $this->lang->line("edit"); ?>">
                                                            <span class="fas fa-pencil"></span>
                                                        </button>
                                                        
                                                        <button type="button" style="margin-left:5px;" class="btn icon-btn btn-xs btn-delete waves-effect waves-light" data-export_id="${row.export_id}" data-role="deletecontainerloadingcost_invoice" data-id="${data}" title="<?php echo $this->lang->line("delete"); ?>">
                                                            <span class="fas fa-trash"></span>
                                                        </button>
                                                    `;
                                            }
                                        },
                                        {
                                            data: "invoice_no"
                                        },
                                        {
                                            data: "supplier_name"
                                        },
                                        {
                                            data: "invoice_date"
                                        },
                                        {
                                            data: "sub_total",
                                            render: function(data) {
                                                return parseFloat(data).toFixed(2);
                                            }
                                        },
                                        {
                                            data: "tax_total",
                                            render: function(data) {
                                                return parseFloat(data).toFixed(2);
                                            }
                                        },
                                        {
                                            data: "allowance_total",
                                            render: function(data) {
                                                return parseFloat(data).toFixed(2);
                                            }
                                        },
                                        {
                                            data: "payable_total",
                                            render: function(data) {
                                                return parseFloat(data).toFixed(2);
                                            }
                                        },
                                        {
                                            data: "container_value_total",
                                            render: function(data) {
                                                return parseFloat(data).toFixed(2);
                                            }
                                        }
                                    ],
                                    scrollX: true, // ✅ Enables horizontal scrolling
                                    autoWidth: true, // ✅ Enables automatic column sizing
                                    scrollCollapse: true,
                                    responsive: false, // ✅ Keep this false with scrollX to avoid conflict
                                    bDestroy: true,
                                    paging: false,
                                    searching: false,
                                    sorting: false,
                                    language: {
                                        url: datatable_language
                                    }
                                });
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
            }
        });

        $("#btnSaveDhlCost").click(function() {

            var arrUpdateContainerValue = [];
            var containerData = <?php echo json_encode($containerDetails); ?>;
            var totalContainerValue = 0;

            $.each(containerData, function(i, item) {

                var mappingId = item.dispatch_id;
                var containerNumber = item.container_number;
                var isValid = true;

                if (mappingId != null && mappingId != '' && mappingId != undefined && mappingId > 0) {

                    var updatedContainerValue = parseFloat($('input[name="dhlcost_container_value[' + mappingId + ']"]').val()) || 0;
                    arrUpdateContainerValue.push({
                        mappingid: mappingId,
                        containerNumber: containerNumber,
                        updatedContainerValue: updatedContainerValue
                    });

                    totalContainerValue += updatedContainerValue;
                }
            });

            if (arrUpdateContainerValue.length > 0) {

                if (fileExtension == "pdf" || fileExtension == "PDF" || fileExtension == "") {
                    totalPayableAmount = parseFloat($("#payable_dhlcost").val()) || 0;
                }

                // if (totalContainerValue != totalPayableAmount) {
                //     toastr.clear();
                //     toastr.error(total_error);
                //     return false;
                // }

                var isValid1 = true,
                    isValid2 = true,
                    isValid3 = true,
                    isValid4 = true,
                    isValid5 = true,
                    isValid6 = true,
                    isValid7 = true;
                if ((fileExtension != "xml" || fileExtension != "XML" || fileExtension == "") && $('#divPdfDhlCost').is(':visible')) {

                    if ($("#invoice_number_dhlcost").val().length == 0) {
                        $("#error-invoice_no_dhlcost").show();
                        isValid1 = false;
                    } else {
                        $("#error-invoice_no_dhlcost").hide();
                        isValid1 = true;
                    }

                    if ($("#supplier_name_dhlcost").val() == 0) {
                        $("#error-supplier_name_dhlcost").show();
                        isValid2 = false;
                    } else {
                        $("#error-supplier_name_dhlcost").hide();
                        isValid2 = true;
                    }

                    if ($("#issued_date_dhlcost").val().length == 0) {
                        $("#error-issued_date_dhlcost").show();
                        isValid3 = false;
                    } else {
                        $("#error-issued_date_dhlcost").hide();
                        isValid3 = true;
                    }

                    if ($("#subtotal_dhlcost").val().length == 0) {
                        $("#error-subtotal_dhlcost").show();
                        isValid4 = false;
                    } else {
                        $("#error-subtotal_dhlcost").hide();
                        isValid4 = true;
                    }

                    if ($("#iva_dhlcost").val().length == 0) {
                        $("#error-iva_dhlcost").show();
                        isValid5 = false;
                    } else {
                        $("#error-iva_dhlcost").hide();
                        isValid5 = true;
                    }

                    if ($("#retefuente_dhlcost").val().length == 0) {
                        $("#error-retefuente_dhlcost").show();
                        isValid6 = false;
                    } else {
                        $("#error-retefuente_dhlcost").hide();
                        isValid6 = true;
                    }

                    if ($("#payable_dhlcost").val().length == 0) {
                        $("#error-payable_dhlcost").show();
                        isValid7 = false;
                    } else {
                        $("#error-payable_dhlcost").hide();
                        isValid7 = true;
                    }
                }

                if (isValid1 && isValid2 && isValid3 && isValid4 && isValid5 && isValid6 && isValid7) {
                    var fd = new FormData();
                    fd.append("exportId", $("#hdnExportId").val());
                    fd.append("originId", $("#hdnOriginId").val());
                    fd.append('saNumber', $("#hdnSaNumber").val());
                    fd.append("fileExtension", fileExtension);
                    fd.append("updateContainerValueData_DhlCost", JSON.stringify(arrUpdateContainerValue));
                    fd.append("uploadPdfFileDhlCost", uploadPdfFileCustomAgency);

                    if (fileExtension == "xml" || fileExtension == "XML") {

                        fd.append('invoiceNo_DhlCost', $("#lblInvoiceNoDhlCost").text());
                        fd.append('supplierName_DhlCost', supplierId);
                        fd.append('formattedDate_DhlCost', $("#lblIssuedDateDhlCost").text());
                        // fd.append('subTotal_DhlCost', subTotalCustoms);
                        // fd.append('iva_DhlCost', ivaCustoms);
                        // fd.append('retefuente_DhlCost', retefuenteCustoms);
                        // fd.append('payable_DhlCost', payableCustoms);
                        fd.append('subTotal_DhlCost', $("#lblTotalAmountDhlCost").val());
                        fd.append('iva_DhlCost', $("#lblTotalTaxAmountDhlCost").val());
                        fd.append('retefuente_DhlCost', $("#lblAllowanceAmountDhlCost").val());
                        fd.append('payable_DhlCost', $("#lblPayableAmountDhlCost").val());
                    } else {

                        fd.append('invoiceNo_DhlCost', $("#invoice_number_dhlcost").val());
                        fd.append('supplierName_DhlCost', $("#supplier_name_dhlcost").val());
                        fd.append('formattedDate_DhlCost', $("#formatted_date_dhlcost").val());
                        fd.append('subTotal_DhlCost', $("#subtotal_dhlcost").val());
                        fd.append('iva_DhlCost', $("#iva_dhlcost").val());
                        fd.append('retefuente_DhlCost', $("#retefuente_dhlcost").val());
                        fd.append('payable_DhlCost', $("#payable_dhlcost").val());
                    }

                    fd.append("csrf_cgrerp", $("#hdnCsrf").val());
                    fd.append("add_type", 13);

                    $('#loading').show();
                    $.ajax({
                        url: base_url + "/save_export_documents",
                        type: 'post',
                        data: fd,
                        contentType: false,
                        processData: false,
                        success: function(jsonResult) {
                            $('#loading').hide();

                            selectedInvoiceId = 0;
                            selectedExportId = 0;
                            selectedExportType = 0;

                            if (jsonResult.redirect == true) {
                                window.location.replace(login_url);
                            } else if (jsonResult.result != '') {
                                toastr.clear();
                                toastr.success(data_updated);

                                resetForm();
                                $('#btnResetDhlCost').hide();
                                $('#btnSaveDhlCost').text(text_save);
                                $("#divInvoiceListDhlCost").show();

                                $("#xin_table_invoice_dhlcost").DataTable({
                                    data: JSON.parse(jsonResult.updatedlist),
                                    columns: [{
                                            data: "id",
                                            render: function(data, type, row) {
                                                return `
                                                        <button type="button" class="btn icon-btn btn-xs btn-success waves-effect waves-light" data-role="editdhlcost_invoice" data-export_id="${row.export_id}" data-id="${data}" title="<?php echo $this->lang->line("edit"); ?>">
                                                            <span class="fas fa-pencil"></span>
                                                        </button>
                                                        
                                                        <button type="button" style="margin-left:5px;" class="btn icon-btn btn-xs btn-delete waves-effect waves-light" data-export_id="${row.export_id}" data-role="deletedhlcost_invoice" data-id="${data}" title="<?php echo $this->lang->line("delete"); ?>">
                                                            <span class="fas fa-trash"></span>
                                                        </button>
                                                    `;
                                            }
                                        },
                                        {
                                            data: "invoice_no"
                                        },
                                        {
                                            data: "supplier_name"
                                        },
                                        {
                                            data: "invoice_date"
                                        },
                                        {
                                            data: "sub_total",
                                            render: function(data) {
                                                return parseFloat(data).toFixed(2);
                                            }
                                        },
                                        {
                                            data: "tax_total",
                                            render: function(data) {
                                                return parseFloat(data).toFixed(2);
                                            }
                                        },
                                        {
                                            data: "allowance_total",
                                            render: function(data) {
                                                return parseFloat(data).toFixed(2);
                                            }
                                        },
                                        {
                                            data: "payable_total",
                                            render: function(data) {
                                                return parseFloat(data).toFixed(2);
                                            }
                                        },
                                        {
                                            data: "container_value_total",
                                            render: function(data) {
                                                return parseFloat(data).toFixed(2);
                                            }
                                        }
                                    ],
                                    scrollX: true, // ✅ Enables horizontal scrolling
                                    autoWidth: true, // ✅ Enables automatic column sizing
                                    scrollCollapse: true,
                                    responsive: false, // ✅ Keep this false with scrollX to avoid conflict
                                    bDestroy: true,
                                    paging: false,
                                    searching: false,
                                    sorting: false,
                                    language: {
                                        url: datatable_language
                                    }
                                });
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
            }
        });

        $("#btnSaveOthercost").click(function() {

            var arrUpdateContainerValue = [];
            var containerData = <?php echo json_encode($containerDetails); ?>;
            var totalContainerValue = 0;

            $.each(containerData, function(i, item) {

                var mappingId = item.dispatch_id;
                var containerNumber = item.container_number;
                var isValid = true;

                if (mappingId != null && mappingId != '' && mappingId != undefined && mappingId > 0) {

                    var updatedContainerValue = parseFloat($('input[name="othercost_container_value[' + mappingId + ']"]').val()) || 0;
                    arrUpdateContainerValue.push({
                        mappingid: mappingId,
                        containerNumber: containerNumber,
                        updatedContainerValue: updatedContainerValue
                    });

                    totalContainerValue += updatedContainerValue;
                }
            });

            if (arrUpdateContainerValue.length > 0) {

                if (fileExtension == "pdf" || fileExtension == "PDF" || fileExtension == "") {
                    totalPayableAmount = parseFloat($("#payable_othercost").val()) || 0;
                }

                // if (totalContainerValue != totalPayableAmount) {
                //     toastr.clear();
                //     toastr.error(total_error);
                //     return false;
                // }

                var isValid1 = true,
                    isValid2 = true,
                    isValid3 = true,
                    isValid4 = true,
                    isValid5 = true,
                    isValid6 = true,
                    isValid7 = true;
                if ((fileExtension != "xml" || fileExtension != "XML" || fileExtension == "") && $('#divPdfOthercost').is(':visible')) {

                    if ($("#invoice_number_othercost").val().length == 0) {
                        $("#error-invoice_no_othercost").show();
                        isValid1 = false;
                    } else {
                        $("#error-invoice_no_othercost").hide();
                        isValid1 = true;
                    }

                    if ($("#supplier_name_othercost").val() == 0) {
                        $("#error-supplier_name_othercost").show();
                        isValid2 = false;
                    } else {
                        $("#error-supplier_name_othercost").hide();
                        isValid2 = true;
                    }

                    if ($("#issued_date_othercost").val().length == 0) {
                        $("#error-issued_date_othercost").show();
                        isValid3 = false;
                    } else {
                        $("#error-issued_date_othercost").hide();
                        isValid3 = true;
                    }

                    if ($("#subtotal_othercost").val().length == 0) {
                        $("#error-subtotal_othercost").show();
                        isValid4 = false;
                    } else {
                        $("#error-subtotal_othercost").hide();
                        isValid4 = true;
                    }

                    if ($("#iva_othercost").val().length == 0) {
                        $("#error-iva_othercost").show();
                        isValid5 = false;
                    } else {
                        $("#error-iva_othercost").hide();
                        isValid5 = true;
                    }

                    if ($("#retefuente_othercost").val().length == 0) {
                        $("#error-retefuente_othercost").show();
                        isValid6 = false;
                    } else {
                        $("#error-retefuente_othercost").hide();
                        isValid6 = true;
                    }

                    if ($("#payable_othercost").val().length == 0) {
                        $("#error-payable_othercost").show();
                        isValid7 = false;
                    } else {
                        $("#error-payable_othercost").hide();
                        isValid7 = true;
                    }
                }

                if (isValid1 && isValid2 && isValid3 && isValid4 && isValid5 && isValid6 && isValid7) {
                    var fd = new FormData();
                    fd.append("exportId", $("#hdnExportId").val());
                    fd.append("originId", $("#hdnOriginId").val());
                    fd.append('saNumber', $("#hdnSaNumber").val());
                    fd.append("fileExtension", fileExtension);
                    fd.append("updateContainerValueData_Othercost", JSON.stringify(arrUpdateContainerValue));
                    fd.append("uploadPdfFileOthercost", uploadPdfFileCustomAgency);

                    if (fileExtension == "xml" || fileExtension == "XML") {

                        fd.append('invoiceNo_Othercost', $("#lblInvoiceNoOthercost").text());
                        fd.append('supplierName_Othercost', supplierId);
                        fd.append('formattedDate_Othercost', $("#lblIssuedDateOthercost").text());
                        // fd.append('subTotal_Othercost', subTotalCustoms);
                        // fd.append('iva_Othercost', ivaCustoms);
                        // fd.append('retefuente_Othercost', retefuenteCustoms);
                        // fd.append('payable_Othercost', payableCustoms);
                        fd.append('subTotal_Othercost', $("#lblTotalAmountOthercost").val());
                        fd.append('iva_Othercost', $("#lblTotalTaxAmountOthercost").val());
                        fd.append('retefuente_Othercost', $("#lblAllowanceAmountOthercost").val());
                        fd.append('payable_Othercost', $("#lblPayableAmountOthercost").val());
                    } else {

                        fd.append('invoiceNo_Othercost', $("#invoice_number_othercost").val());
                        fd.append('supplierName_Othercost', $("#supplier_name_othercost").val());
                        fd.append('formattedDate_Othercost', $("#formatted_date_othercost").val());
                        fd.append('subTotal_Othercost', $("#subtotal_othercost").val());
                        fd.append('iva_Othercost', $("#iva_othercost").val());
                        fd.append('retefuente_Othercost', $("#retefuente_othercost").val());
                        fd.append('payable_Othercost', $("#payable_othercost").val());
                    }

                    fd.append("csrf_cgrerp", $("#hdnCsrf").val());
                    fd.append("add_type", 12);

                    $('#loading').show();
                    $.ajax({
                        url: base_url + "/save_export_documents",
                        type: 'post',
                        data: fd,
                        contentType: false,
                        processData: false,
                        success: function(jsonResult) {
                            $('#loading').hide();

                            selectedInvoiceId = 0;
                            selectedExportId = 0;
                            selectedExportType = 0;

                            if (jsonResult.redirect == true) {
                                window.location.replace(login_url);
                            } else if (jsonResult.result != '') {
                                toastr.clear();
                                toastr.success(data_updated);

                                resetForm();
                                $('#btnResetOthercost').hide();
                                $('#btnSaveOthercost').text(text_save);
                                $("#divInvoiceListOthercost").show();

                                $("#xin_table_invoice_othercost").DataTable({
                                    data: JSON.parse(jsonResult.updatedlist),
                                    columns: [{
                                            data: "id",
                                            render: function(data, type, row) {
                                                return `
                                                        <button type="button" class="btn icon-btn btn-xs btn-success waves-effect waves-light" data-role="editothercost_invoice" data-export_id="${row.export_id}" data-id="${data}" title="<?php echo $this->lang->line("edit"); ?>">
                                                            <span class="fas fa-pencil"></span>
                                                        </button>

                                                        <button type="button" style="margin-left:5px;" class="btn icon-btn btn-xs btn-delete waves-effect waves-light" data-export_id="${row.export_id}" data-role="deleteothercost_invoice" data-id="${data}" title="<?php echo $this->lang->line("delete"); ?>">
                                                            <span class="fas fa-trash"></span>
                                                        </button>
                                                    `;
                                            }
                                        },
                                        {
                                            data: "invoice_no"
                                        },
                                        {
                                            data: "supplier_name"
                                        },
                                        {
                                            data: "invoice_date"
                                        },
                                        {
                                            data: "sub_total",
                                            render: function(data) {
                                                return parseFloat(data).toFixed(2);
                                            }
                                        },
                                        {
                                            data: "tax_total",
                                            render: function(data) {
                                                return parseFloat(data).toFixed(2);
                                            }
                                        },
                                        {
                                            data: "allowance_total",
                                            render: function(data) {
                                                return parseFloat(data).toFixed(2);
                                            }
                                        },
                                        {
                                            data: "payable_total",
                                            render: function(data) {
                                                return parseFloat(data).toFixed(2);
                                            }
                                        },
                                        {
                                            data: "container_value_total",
                                            render: function(data) {
                                                return parseFloat(data).toFixed(2);
                                            }
                                        }
                                    ],
                                    scrollX: true, // ✅ Enables horizontal scrolling
                                    autoWidth: true, // ✅ Enables automatic column sizing
                                    scrollCollapse: true,
                                    responsive: false, // ✅ Keep this false with scrollX to avoid conflict
                                    bDestroy: true,
                                    paging: false,
                                    searching: false,
                                    sorting: false,
                                    language: {
                                        url: datatable_language
                                    }
                                });
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
            }
        });

        //INVOICE LISTS

        //CUSTOMS

        var exportDocumentsCustomsInvoiceLists = [];
        var invoiceCustomsCount = <?php echo $exportDocumentsCustomsInvoiceListsCount; ?>;
        if (invoiceCustomsCount > 0) {
            exportDocumentsCustomsInvoiceLists = <?php echo $exportDocumentsCustomsInvoiceLists; ?>;
        }
        $("#xin_table_invoice_customs").DataTable({
            data: exportDocumentsCustomsInvoiceLists,
            columns: [{
                    data: "id",
                    render: function(data, type, row) {
                        return `
                            <button type="button" class="btn icon-btn btn-xs btn-success waves-effect waves-light" data-role="editcustoms_invoice" data-export_id="${row.export_id}" data-id="${data}" title="<?php echo $this->lang->line("edit"); ?>">
                                <span class="fas fa-pencil"></span>
                            </button>
                            
                            <button type="button" style="margin-left:5px;" class="btn icon-btn btn-xs btn-delete waves-effect waves-light" data-export_id="${row.export_id}" data-role="deletecustoms_invoice" data-id="${data}" title="<?php echo $this->lang->line("delete"); ?>">
                                <span class="fas fa-trash"></span>
                            </button>
                        `;
                    }
                },
                {
                    data: "invoice_no"
                },
                {
                    data: "supplier_name"
                },
                {
                    data: "invoice_date"
                },
                {
                    data: "sub_total",
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                },
                {
                    data: "tax_total",
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                },
                {
                    data: "allowance_total",
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                },
                {
                    data: "payable_total",
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                },
                {
                    data: "container_value_total",
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                }
            ],
            scrollX: true, // ✅ Enables horizontal scrolling
            autoWidth: true, // ✅ Enables automatic column sizing
            scrollCollapse: true,
            responsive: false, // ✅ Keep this false with scrollX to avoid conflict
            bDestroy: true,
            paging: false,
            searching: false,
            sorting: false,
            language: {
                url: datatable_language
            }
        });

        $(document).on('click', 'button[data-role=editcustoms_invoice]', function() {
            selectedInvoiceId = 0;
            selectedExportId = 0;
            selectedExportType = 0;
            var invoice_id = $(this).data("id");
            var export_id = $(this).data("export_id");
            $.ajax({
                url: base_url + "/dialog_export_action",
                type: "GET",
                data: 'jd=1&is_ajax=3&mode=modal&type=editcustoms_invoice&id=' + invoice_id + "&export_id=" + export_id,
                success: function(response) {
                    if (response.redirect == true) {
                        window.location.replace(login_url);
                    } else if (response.error != '') {
                        toastr.clear();
                        toastr.error(response.error);
                        $('input[name="csrf_cgrerp"]').val(response.csrf_hash);
                        $("#divInvoiceListbtnSaveCustoms").show();
                        $("#btnResetCustoms").hide();
                    } else {
                        $("#invoice_number_custom").val(response.result.invoice_number);
                        $("#supplier_name_custom").val(response.result.supplier_id).trigger("change");
                        $("#issued_date_custom").val(response.result.invoice_date);
                        $("#formatted_date_custom").val(response.result.original_invoice_date);
                        $("#subtotal_custom").val(response.result.sub_total);
                        $("#iva_custom").val(response.result.tax_total);
                        $("#retefuente_custom").val(response.result.allowance_total);
                        $("#payable_custom").val(response.result.payable_total);
                        $("#container_value_custom").text(response.result.container_value_total);
                        selectedExportId = response.result.export_id;
                        selectedInvoiceId = response.result.invoice_id;

                        var containerData = JSON.parse(response.result.container_data);
                        containerData.forEach(function(container) {
                            var dispatchId = container.dispatch_id;
                            var containerValue = container.container_value;

                            // Find the input with name matching the dispatch ID
                            var input = $("input[name='custom_container_value[" + dispatchId + "]']");
                            if (input.length > 0) {
                                input.val(parseFloat(containerValue));
                            }
                        });

                        $('input[name="csrf_cgrerp"]').val(response.csrf_hash);
                        $("#divInvoiceListCustoms").hide();
                        $("#btnResetCustoms").show();
                        $("#btnSaveCustoms").text(text_update);
                    }
                }
            });
        });

        $(document).on('click', 'button[data-role=deletecustoms_invoice]', function() {
            selectedInvoiceId = 0;
            selectedExportId = 0;
            selectedExportType = 0;
            var invoice_id = $(this).data("id");
            var export_id = $(this).data("export_id");
            var export_type = 1;
            $.ajax({
                url: base_url + "/dialog_export_action",
                type: "GET",
                data: 'jd=1&is_ajax=3&mode=modal&type=deleteinvoiceconfirmation&id=' + invoice_id + "&export_id=" + export_id + "&export_type=" + export_type,
                success: function(response) {
                    if (response.redirect == true) {
                        window.location.replace(login_url);
                    } else {
                        $("#titlehead").text(response.pageheading);
                        $("#infomessage").text(response.pagemessage);
                        selectedInvoiceId = response.selectedInvoiceId;
                        selectedExportId = response.selectedExportId;
                        selectedExportType = response.selectedExportType;
                        $("#alert-dialog-info").modal('show');
                    }
                }
            });
        });

        //END CUSTOMS

        //ITR

        var exportDocumentsITRInvoiceLists = [];
        var invoiceITRCount = <?php echo $exportDocumentsITRInvoiceListsCount; ?>;
        if (invoiceITRCount > 0) {
            exportDocumentsITRInvoiceLists = <?php echo $exportDocumentsITRInvoiceLists; ?>;
        }
        $("#xin_table_invoice_itr").DataTable({
            data: exportDocumentsITRInvoiceLists,
            columns: [{
                    data: "id",
                    render: function(data, type, row) {
                        return `
                            <button type="button" class="btn icon-btn btn-xs btn-success waves-effect waves-light" data-role="edititr_invoice" data-export_id="${row.export_id}" data-id="${data}" title="<?php echo $this->lang->line("edit"); ?>">
                                <span class="fas fa-pencil"></span>
                            </button>
                            
                            <button type="button" style="margin-left:5px;" class="btn icon-btn btn-xs btn-delete waves-effect waves-light" data-export_id="${row.export_id}" data-role="deleteitr_invoice" data-id="${data}" title="<?php echo $this->lang->line("delete"); ?>">
                                <span class="fas fa-trash"></span>
                            </button>
                        `;
                    }
                },
                {
                    data: "invoice_no"
                },
                {
                    data: "supplier_name"
                },
                {
                    data: "invoice_date"
                },
                {
                    data: "sub_total",
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                },
                {
                    data: "tax_total",
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                },
                {
                    data: "allowance_total",
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                },
                {
                    data: "payable_total",
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                },
                {
                    data: "container_value_total",
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                }
            ],
            scrollX: true, // ✅ Enables horizontal scrolling
            autoWidth: true, // ✅ Enables automatic column sizing
            scrollCollapse: true,
            responsive: false, // ✅ Keep this false with scrollX to avoid conflict
            bDestroy: true,
            paging: false,
            searching: false,
            sorting: false,
            language: {
                url: datatable_language
            }
        });

        $(document).on('click', 'button[data-role=edititr_invoice]', function() {
            selectedInvoiceId = 0;
            selectedExportId = 0;
            selectedExportType = 0;
            var invoice_id = $(this).data("id");
            var export_id = $(this).data("export_id");
            $.ajax({
                url: base_url + "/dialog_export_action",
                type: "GET",
                data: 'jd=1&is_ajax=3&mode=modal&type=edititr_invoice&id=' + invoice_id + "&export_id=" + export_id,
                success: function(response) {
                    if (response.redirect == true) {
                        window.location.replace(login_url);
                    } else if (response.error != '') {
                        toastr.clear();
                        toastr.error(response.error);
                        $('input[name="csrf_cgrerp"]').val(response.csrf_hash);
                        $("#divInvoiceListITR").show();
                        $("#btnResetITR").hide();
                    } else {
                        $("#invoice_number_itr").val(response.result.invoice_number);
                        $("#supplier_name_itr").val(response.result.supplier_id).trigger("change");
                        $("#issued_date_itr").val(response.result.invoice_date);
                        $("#formatted_date_itr").val(response.result.original_invoice_date);
                        $("#subtotal_itr").val(response.result.sub_total);
                        $("#iva_itr").val(response.result.tax_total);
                        $("#retefuente_itr").val(response.result.allowance_total);
                        $("#payable_itr").val(response.result.payable_total);
                        $("#container_value_itr").text(response.result.container_value_total);
                        selectedExportId = response.result.export_id;
                        selectedInvoiceId = response.result.invoice_id;

                        var containerData = JSON.parse(response.result.container_data);
                        containerData.forEach(function(container) {
                            var dispatchId = container.dispatch_id;
                            var containerValue = container.container_value;

                            // Find the input with name matching the dispatch ID
                            var input = $("input[name='itr_container_value[" + dispatchId + "]']");
                            if (input.length > 0) {
                                input.val(parseFloat(containerValue));
                            }
                        });

                        $('input[name="csrf_cgrerp"]').val(response.csrf_hash);
                        $("#divInvoiceListITR").hide();
                        $("#btnResetITR").show();
                        $("#btnSaveITR").text(text_update);
                    }
                }
            });
        });

        $(document).on('click', 'button[data-role=deleteitr_invoice]', function() {
            selectedInvoiceId = 0;
            selectedExportId = 0;
            selectedExportType = 0;
            var invoice_id = $(this).data("id");
            var export_id = $(this).data("export_id");
            var export_type = 2;
            $.ajax({
                url: base_url + "/dialog_export_action",
                type: "GET",
                data: 'jd=1&is_ajax=3&mode=modal&type=deleteinvoiceconfirmation&id=' + invoice_id + "&export_id=" + export_id + "&export_type=" + export_type,
                success: function(response) {
                    if (response.redirect == true) {
                        window.location.replace(login_url);
                    } else {
                        $("#titlehead").text(response.pageheading);
                        $("#infomessage").text(response.pagemessage);
                        selectedInvoiceId = response.selectedInvoiceId;
                        selectedExportId = response.selectedExportId;
                        selectedExportType = response.selectedExportType;
                        $("#alert-dialog-info").modal('show');
                    }
                }
            });
        });

        //END ITR

        //PORT

        var exportDocumentsPortInvoiceLists = [];
        var invoicePortCount = <?php echo $exportDocumentsPortInvoiceListsCount; ?>;
        if (invoicePortCount > 0) {
            exportDocumentsPortInvoiceLists = <?php echo $exportDocumentsPortInvoiceLists; ?>;
        }
        $("#xin_table_invoice_port").DataTable({
            data: exportDocumentsPortInvoiceLists,
            columns: [{
                    data: "id",
                    render: function(data, type, row) {
                        return `
                            <button type="button" class="btn icon-btn btn-xs btn-success waves-effect waves-light" data-role="editport_invoice" data-export_id="${row.export_id}" data-id="${data}" title="<?php echo $this->lang->line("edit"); ?>">
                                <span class="fas fa-pencil"></span>
                            </button>
                            
                            <button type="button" style="margin-left:5px;" class="btn icon-btn btn-xs btn-delete waves-effect waves-light" data-export_id="${row.export_id}" data-role="deleteport_invoice" data-id="${data}" title="<?php echo $this->lang->line("delete"); ?>">
                                <span class="fas fa-trash"></span>
                            </button>
                        `;
                    }
                },
                {
                    data: "invoice_no"
                },
                {
                    data: "supplier_name"
                },
                {
                    data: "invoice_date"
                },
                {
                    data: "sub_total",
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                },
                {
                    data: "tax_total",
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                },
                {
                    data: "allowance_total",
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                },
                {
                    data: "payable_total",
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                },
                {
                    data: "container_value_total",
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                }
            ],
            scrollX: true, // ✅ Enables horizontal scrolling
            autoWidth: true, // ✅ Enables automatic column sizing
            scrollCollapse: true,
            responsive: false, // ✅ Keep this false with scrollX to avoid conflict
            bDestroy: true,
            paging: false,
            searching: false,
            sorting: false,
            language: {
                url: datatable_language
            }
        });

        $(document).on('click', 'button[data-role=editport_invoice]', function() {
            selectedInvoiceId = 0;
            selectedExportId = 0;
            selectedExportType = 0;
            var invoice_id = $(this).data("id");
            var export_id = $(this).data("export_id");
            $.ajax({
                url: base_url + "/dialog_export_action",
                type: "GET",
                data: 'jd=1&is_ajax=3&mode=modal&type=editport_invoice&id=' + invoice_id + "&export_id=" + export_id,
                success: function(response) {
                    if (response.redirect == true) {
                        window.location.replace(login_url);
                    } else if (response.error != '') {
                        toastr.clear();
                        toastr.error(response.error);
                        $('input[name="csrf_cgrerp"]').val(response.csrf_hash);
                        $("#divInvoiceListPort").show();
                        $("#btnResetPort").hide();
                    } else {
                        $("#invoice_number_port").val(response.result.invoice_number);
                        $("#supplier_name_port").val(response.result.supplier_id).trigger("change");
                        $("#issued_date_port").val(response.result.invoice_date);
                        $("#formatted_date_port").val(response.result.original_invoice_date);
                        $("#subtotal_port").val(response.result.sub_total);
                        $("#iva_port").val(response.result.tax_total);
                        $("#retefuente_port").val(response.result.allowance_total);
                        $("#payable_port").val(response.result.payable_total);
                        $("#container_value_port").text(response.result.container_value_total);
                        selectedExportId = response.result.export_id;
                        selectedInvoiceId = response.result.invoice_id;

                        var containerData = JSON.parse(response.result.container_data);
                        containerData.forEach(function(container) {
                            var dispatchId = container.dispatch_id;
                            var containerValue = container.container_value;

                            // Find the input with name matching the dispatch ID
                            var input = $("input[name='port_container_value[" + dispatchId + "]']");
                            if (input.length > 0) {
                                input.val(parseFloat(containerValue));
                            }
                        });

                        $('input[name="csrf_cgrerp"]').val(response.csrf_hash);
                        $("#divInvoiceListPort").hide();
                        $("#btnResetPort").show();
                        $("#btnSavePort").text(text_update);
                    }
                }
            });
        });

        $(document).on('click', 'button[data-role=deleteport_invoice]', function() {
            selectedInvoiceId = 0;
            selectedExportId = 0;
            selectedExportType = 0;
            var invoice_id = $(this).data("id");
            var export_id = $(this).data("export_id");
            var export_type = 3;
            $.ajax({
                url: base_url + "/dialog_export_action",
                type: "GET",
                data: 'jd=1&is_ajax=3&mode=modal&type=deleteinvoiceconfirmation&id=' + invoice_id + "&export_id=" + export_id + "&export_type=" + export_type,
                success: function(response) {
                    if (response.redirect == true) {
                        window.location.replace(login_url);
                    } else {
                        $("#titlehead").text(response.pageheading);
                        $("#infomessage").text(response.pagemessage);
                        selectedInvoiceId = response.selectedInvoiceId;
                        selectedExportId = response.selectedExportId;
                        selectedExportType = response.selectedExportType;
                        $("#alert-dialog-info").modal('show');
                    }
                }
            });
        });

        //END PORT

        //SHIPPING

        var exportDocumentsShippingInvoiceLists = [];
        var invoiceShippingCount = <?php echo $exportDocumentsShippingInvoiceListsCount; ?>;
        if (invoiceShippingCount > 0) {
            exportDocumentsShippingInvoiceLists = <?php echo $exportDocumentsShippingInvoiceLists; ?>;
        }
        $("#xin_table_invoice_shipping").DataTable({
            data: exportDocumentsShippingInvoiceLists,
            columns: [{
                    data: "id",
                    render: function(data, type, row) {
                        return `
                                                        <button type="button" class="btn icon-btn btn-xs btn-success waves-effect waves-light" data-role="editshipping_invoice" data-export_id="${row.export_id}" data-id="${data}" title="<?php echo $this->lang->line("edit"); ?>">
                                                            <span class="fas fa-pencil"></span>
                                                        </button>
                                                        
                                                        <button type="button" style="margin-left:5px;" class="btn icon-btn btn-xs btn-delete waves-effect waves-light" data-export_id="${row.export_id}" data-role="deleteshipping_invoice" data-id="${data}" title="<?php echo $this->lang->line("delete"); ?>">
                                                            <span class="fas fa-trash"></span>
                                                        </button>
                                                    `;
                    }
                },
                {
                    data: "invoice_no"
                },
                {
                    data: "supplier_name"
                },
                {
                    data: "invoice_date"
                },
                {
                    data: "sub_total",
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                },
                {
                    data: "tax_total",
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                },
                {
                    data: "allowance_total",
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                },
                {
                    data: "payable_total",
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                },
                {
                    data: "container_value_total",
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                }
            ],
            scrollX: true, // ✅ Enables horizontal scrolling
            autoWidth: true, // ✅ Enables automatic column sizing
            scrollCollapse: true,
            responsive: false, // ✅ Keep this false with scrollX to avoid conflict
            bDestroy: true,
            paging: false,
            searching: false,
            sorting: false,
            language: {
                url: datatable_language
            }
        });

        $(document).on('click', 'button[data-role=editshipping_invoice]', function() {
            selectedInvoiceId = 0;
            selectedExportId = 0;
            selectedExportType = 0;
            var invoice_id = $(this).data("id");
            var export_id = $(this).data("export_id");
            $.ajax({
                url: base_url + "/dialog_export_action",
                type: "GET",
                data: 'jd=1&is_ajax=3&mode=modal&type=editshipping_invoice&id=' + invoice_id + "&export_id=" + export_id,
                success: function(response) {
                    if (response.redirect == true) {
                        window.location.replace(login_url);
                    } else if (response.error != '') {
                        toastr.clear();
                        toastr.error(response.error);
                        $('input[name="csrf_cgrerp"]').val(response.csrf_hash);
                        $("#divInvoiceListbtnSaveShipping").show();
                        $("#btnResetShipping").hide();
                    } else {
                        $("#invoice_number_shipping").val(response.result.invoice_number);
                        $("#supplier_name_shipping").val(response.result.supplier_id).trigger("change");
                        $("#issued_date_shipping").val(response.result.invoice_date);
                        $("#formatted_date_shipping").val(response.result.original_invoice_date);
                        $("#subtotal_shipping").val(response.result.sub_total);
                        $("#iva_shipping").val(response.result.tax_total);
                        $("#retefuente_shipping").val(response.result.allowance_total);
                        $("#payable_shipping").val(response.result.payable_total);
                        $("#container_value_shipping").text(response.result.container_value_total);
                        selectedExportId = response.result.export_id;
                        selectedInvoiceId = response.result.invoice_id;

                        var containerData = JSON.parse(response.result.container_data);
                        containerData.forEach(function(container) {
                            var dispatchId = container.dispatch_id;
                            var containerValue = container.container_value;

                            // Find the input with name matching the dispatch ID
                            var input = $("input[name='shipping_container_value[" + dispatchId + "]']");
                            if (input.length > 0) {
                                input.val(parseFloat(containerValue));
                            }
                        });

                        $('input[name="csrf_cgrerp"]').val(response.csrf_hash);
                        $("#divInvoiceListShipping").hide();
                        $("#btnResetShipping").show();
                        $("#btnSaveShipping").text(text_update);
                    }
                }
            });
        });

        $(document).on('click', 'button[data-role=deleteshipping_invoice]', function() {
            selectedInvoiceId = 0;
            selectedExportId = 0;
            selectedExportType = 0;
            var invoice_id = $(this).data("id");
            var export_id = $(this).data("export_id");
            var export_type = 9;
            $.ajax({
                url: base_url + "/dialog_export_action",
                type: "GET",
                data: 'jd=1&is_ajax=3&mode=modal&type=deleteinvoiceconfirmation&id=' + invoice_id + "&export_id=" + export_id + "&export_type=" + export_type,
                success: function(response) {
                    if (response.redirect == true) {
                        window.location.replace(login_url);
                    } else {
                        $("#titlehead").text(response.pageheading);
                        $("#infomessage").text(response.pagemessage);
                        selectedInvoiceId = response.selectedInvoiceId;
                        selectedExportId = response.selectedExportId;
                        selectedExportType = response.selectedExportType;
                        $("#alert-dialog-info").modal('show');
                    }
                }
            });
        });

        //END SHIPPING

        //FUMIGATION

        var exportDocumentsFumigationInvoiceLists = [];
        var invoiceFumigationCount = <?php echo $exportDocumentsFumigationInvoiceListsCount; ?>;
        if (invoiceFumigationCount > 0) {
            exportDocumentsFumigationInvoiceLists = <?php echo $exportDocumentsFumigationInvoiceLists; ?>;
        }
        $("#xin_table_invoice_fumigation").DataTable({
            data: exportDocumentsFumigationInvoiceLists,
            columns: [{
                    data: "id",
                    render: function(data, type, row) {
                        return `
                            <button type="button" class="btn icon-btn btn-xs btn-success waves-effect waves-light" data-role="editfumigation_invoice" data-export_id="${row.export_id}" data-id="${data}" title="<?php echo $this->lang->line("edit"); ?>">
                                <span class="fas fa-pencil"></span>
                            </button>
                            
                            <button type="button" style="margin-left:5px;" class="btn icon-btn btn-xs btn-delete waves-effect waves-light" data-export_id="${row.export_id}" data-role="deletefumigation_invoice" data-id="${data}" title="<?php echo $this->lang->line("delete"); ?>">
                                <span class="fas fa-trash"></span>
                            </button>
                        `;
                    }
                },
                {
                    data: "invoice_no"
                },
                {
                    data: "supplier_name"
                },
                {
                    data: "invoice_date"
                },
                {
                    data: "sub_total",
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                },
                {
                    data: "tax_total",
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                },
                {
                    data: "allowance_total",
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                },
                {
                    data: "payable_total",
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                },
                {
                    data: "container_value_total",
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                }
            ],
            scrollX: true, // ✅ Enables horizontal scrolling
            autoWidth: true, // ✅ Enables automatic column sizing
            scrollCollapse: true,
            responsive: false, // ✅ Keep this false with scrollX to avoid conflict
            bDestroy: true,
            paging: false,
            searching: false,
            sorting: false,
            language: {
                url: datatable_language
            }
        });

        $(document).on('click', 'button[data-role=editfumigation_invoice]', function() {
            selectedInvoiceId = 0;
            selectedExportId = 0;
            selectedExportType = 0;
            var invoice_id = $(this).data("id");
            var export_id = $(this).data("export_id");
            $.ajax({
                url: base_url + "/dialog_export_action",
                type: "GET",
                data: 'jd=1&is_ajax=3&mode=modal&type=editfumigation_invoice&id=' + invoice_id + "&export_id=" + export_id,
                success: function(response) {
                    if (response.redirect == true) {
                        window.location.replace(login_url);
                    } else if (response.error != '') {
                        toastr.clear();
                        toastr.error(response.error);
                        $('input[name="csrf_cgrerp"]').val(response.csrf_hash);
                        $("#divInvoiceListFumigation").show();
                        $("#btnResetFumigation").hide();
                    } else {
                        $("#invoice_number_fumigation").val(response.result.invoice_number);
                        $("#supplier_name_fumigation").val(response.result.supplier_id).trigger("change");
                        $("#issued_date_fumigation").val(response.result.invoice_date);
                        $("#formatted_date_fumigation").val(response.result.original_invoice_date);
                        $("#subtotal_fumigation").val(response.result.sub_total);
                        $("#iva_fumigation").val(response.result.tax_total);
                        $("#retefuente_fumigation").val(response.result.allowance_total);
                        $("#payable_fumigation").val(response.result.payable_total);
                        $("#container_value_fumigation").text(response.result.container_value_total);
                        selectedExportId = response.result.export_id;
                        selectedInvoiceId = response.result.invoice_id;

                        var containerData = JSON.parse(response.result.container_data);
                        containerData.forEach(function(container) {
                            var dispatchId = container.dispatch_id;
                            var containerValue = container.container_value;

                            // Find the input with name matching the dispatch ID
                            var input = $("input[name='fumigation_container_value[" + dispatchId + "]']");
                            if (input.length > 0) {
                                input.val(parseFloat(containerValue));
                            }
                        });

                        $('input[name="csrf_cgrerp"]').val(response.csrf_hash);
                        $("#divInvoiceListFumigation").hide();
                        $("#btnResetFumigation").show();
                        $("#btnSaveFumigation").text(text_update);
                    }
                }
            });
        });

        $(document).on('click', 'button[data-role=deletefumigation_invoice]', function() {
            selectedInvoiceId = 0;
            selectedExportId = 0;
            selectedExportType = 0;
            var invoice_id = $(this).data("id");
            var export_id = $(this).data("export_id");
            var export_type = 4;
            $.ajax({
                url: base_url + "/dialog_export_action",
                type: "GET",
                data: 'jd=1&is_ajax=3&mode=modal&type=deleteinvoiceconfirmation&id=' + invoice_id + "&export_id=" + export_id + "&export_type=" + export_type,
                success: function(response) {
                    if (response.redirect == true) {
                        window.location.replace(login_url);
                    } else {
                        $("#titlehead").text(response.pageheading);
                        $("#infomessage").text(response.pagemessage);
                        selectedInvoiceId = response.selectedInvoiceId;
                        selectedExportId = response.selectedExportId;
                        selectedExportType = response.selectedExportType;
                        $("#alert-dialog-info").modal('show');
                    }
                }
            });
        });

        //END FUMIGATION 

        //PHYTO

        var exportDocumentsPhytoInvoiceLists = [];
        var invoicePhytoCount = <?php echo $exportDocumentsPhytoInvoiceListsCount; ?>;
        if (invoicePhytoCount > 0) {
            exportDocumentsPhytoInvoiceLists = <?php echo $exportDocumentsPhytoInvoiceLists; ?>;
        }
        $("#xin_table_invoice_phyto").DataTable({
            data: exportDocumentsPhytoInvoiceLists,
            columns: [{
                    data: "id",
                    render: function(data, type, row) {
                        return `
                            <button type="button" class="btn icon-btn btn-xs btn-success waves-effect waves-light" data-role="editphyto_invoice" data-export_id="${row.export_id}" data-id="${data}" title="<?php echo $this->lang->line("edit"); ?>">
                                <span class="fas fa-pencil"></span>
                            </button>
                            
                            <button type="button" style="margin-left:5px;" class="btn icon-btn btn-xs btn-delete waves-effect waves-light" data-export_id="${row.export_id}" data-role="deletephyto_invoice" data-id="${data}" title="<?php echo $this->lang->line("delete"); ?>">
                                <span class="fas fa-trash"></span>
                            </button>
                        `;
                    }
                },
                {
                    data: "invoice_no"
                },
                {
                    data: "supplier_name"
                },
                {
                    data: "invoice_date"
                },
                {
                    data: "sub_total",
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                },
                {
                    data: "tax_total",
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                },
                {
                    data: "allowance_total",
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                },
                {
                    data: "payable_total",
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                },
                {
                    data: "container_value_total",
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                }
            ],
            scrollX: true, // ✅ Enables horizontal scrolling
            autoWidth: true, // ✅ Enables automatic column sizing
            scrollCollapse: true,
            responsive: false, // ✅ Keep this false with scrollX to avoid conflict
            bDestroy: true,
            paging: false,
            searching: false,
            sorting: false,
            language: {
                url: datatable_language
            }
        });

        $(document).on('click', 'button[data-role=editphyto_invoice]', function() {
            selectedInvoiceId = 0;
            selectedExportId = 0;
            selectedExportType = 0;
            var invoice_id = $(this).data("id");
            var export_id = $(this).data("export_id");
            $.ajax({
                url: base_url + "/dialog_export_action",
                type: "GET",
                data: 'jd=1&is_ajax=3&mode=modal&type=editphyto_invoice&id=' + invoice_id + "&export_id=" + export_id,
                success: function(response) {
                    if (response.redirect == true) {
                        window.location.replace(login_url);
                    } else if (response.error != '') {
                        toastr.clear();
                        toastr.error(response.error);
                        $('input[name="csrf_cgrerp"]').val(response.csrf_hash);
                        $("#divInvoiceListPhyto").show();
                        $("#btnResetPhyto").hide();
                    } else {
                        $("#invoice_number_phyto").val(response.result.invoice_number);
                        $("#supplier_name_phyto").val(response.result.supplier_id).trigger("change");
                        $("#issued_date_phyto").val(response.result.invoice_date);
                        $("#formatted_date_phyto").val(response.result.original_invoice_date);
                        $("#subtotal_phyto").val(response.result.sub_total);
                        $("#iva_phyto").val(response.result.tax_total);
                        $("#retefuente_phyto").val(response.result.allowance_total);
                        $("#payable_phyto").val(response.result.payable_total);
                        $("#container_value_phyto").text(response.result.container_value_total);
                        selectedExportId = response.result.export_id;
                        selectedInvoiceId = response.result.invoice_id;

                        var containerData = JSON.parse(response.result.container_data);
                        containerData.forEach(function(container) {
                            var dispatchId = container.dispatch_id;
                            var containerValue = container.container_value;

                            // Find the input with name matching the dispatch ID
                            var input = $("input[name='phyto_container_value[" + dispatchId + "]']");
                            if (input.length > 0) {
                                input.val(parseFloat(containerValue));
                            }
                        });

                        $('input[name="csrf_cgrerp"]').val(response.csrf_hash);
                        $("#divInvoiceListPhyto").hide();
                        $("#btnResetPhyto").show();
                        $("#btnSavePhyto").text(text_update);
                    }
                }
            });
        });

        $(document).on('click', 'button[data-role=deletephyto_invoice]', function() {
            selectedInvoiceId = 0;
            selectedExportId = 0;
            selectedExportType = 0;
            var invoice_id = $(this).data("id");
            var export_id = $(this).data("export_id");
            var export_type = 5;
            $.ajax({
                url: base_url + "/dialog_export_action",
                type: "GET",
                data: 'jd=1&is_ajax=3&mode=modal&type=deleteinvoiceconfirmation&id=' + invoice_id + "&export_id=" + export_id + "&export_type=" + export_type,
                success: function(response) {
                    if (response.redirect == true) {
                        window.location.replace(login_url);
                    } else {
                        $("#titlehead").text(response.pageheading);
                        $("#infomessage").text(response.pagemessage);
                        selectedInvoiceId = response.selectedInvoiceId;
                        selectedExportId = response.selectedExportId;
                        selectedExportType = response.selectedExportType;
                        $("#alert-dialog-info").modal('show');
                    }
                }
            });
        });

        //END PHYTO

        //COTEROS

        var exportDocumentsCoterosInvoiceLists = [];
        var invoiceCoterosCount = <?php echo $exportDocumentsCoterosInvoiceListsCount; ?>;
        if (invoiceCoterosCount > 0) {
            exportDocumentsCoterosInvoiceLists = <?php echo $exportDocumentsCoterosInvoiceLists; ?>;
        }
        $("#xin_table_invoice_coteros").DataTable({
            data: exportDocumentsCoterosInvoiceLists,
            columns: [{
                    data: "id",
                    render: function(data, type, row) {
                        return `
                            <button type="button" class="btn icon-btn btn-xs btn-success waves-effect waves-light" data-role="editcoteros_invoice" data-export_id="${row.export_id}" data-id="${data}" title="<?php echo $this->lang->line("edit"); ?>">
                                <span class="fas fa-pencil"></span>
                            </button>
                            
                            <button type="button" style="margin-left:5px;" class="btn icon-btn btn-xs btn-delete waves-effect waves-light" data-export_id="${row.export_id}" data-role="deletecoteros_invoice" data-id="${data}" title="<?php echo $this->lang->line("delete"); ?>">
                                <span class="fas fa-trash"></span>
                            </button>
                        `;
                    }
                },
                {
                    data: "invoice_no"
                },
                {
                    data: "supplier_name"
                },
                {
                    data: "invoice_date"
                },
                {
                    data: "sub_total",
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                },
                {
                    data: "tax_total",
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                },
                {
                    data: "allowance_total",
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                },
                {
                    data: "payable_total",
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                },
                {
                    data: "container_value_total",
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                }
            ],
            scrollX: true, // ✅ Enables horizontal scrolling
            autoWidth: true, // ✅ Enables automatic column sizing
            scrollCollapse: true,
            responsive: false, // ✅ Keep this false with scrollX to avoid conflict
            bDestroy: true,
            paging: false,
            searching: false,
            sorting: false,
            language: {
                url: datatable_language
            }
        });

        $(document).on('click', 'button[data-role=editcoteros_invoice]', function() {
            selectedInvoiceId = 0;
            selectedExportId = 0;
            selectedExportType = 0;
            var invoice_id = $(this).data("id");
            var export_id = $(this).data("export_id");
            $.ajax({
                url: base_url + "/dialog_export_action",
                type: "GET",
                data: 'jd=1&is_ajax=3&mode=modal&type=editcoteros_invoice&id=' + invoice_id + "&export_id=" + export_id,
                success: function(response) {
                    if (response.redirect == true) {
                        window.location.replace(login_url);
                    } else if (response.error != '') {
                        toastr.clear();
                        toastr.error(response.error);
                        $('input[name="csrf_cgrerp"]').val(response.csrf_hash);
                        $("#divInvoiceListCoteros").show();
                        $("#btnResetCoteros").hide();
                    } else {
                        $("#invoice_number_coteros").val(response.result.invoice_number);
                        $("#supplier_name_coteros").val(response.result.supplier_id).trigger("change");
                        $("#issued_date_coteros").val(response.result.invoice_date);
                        $("#formatted_date_coteros").val(response.result.original_invoice_date);
                        $("#subtotal_coteros").val(response.result.sub_total);
                        $("#iva_coteros").val(response.result.tax_total);
                        $("#retefuente_coteros").val(response.result.allowance_total);
                        $("#payable_coteros").val(response.result.payable_total);
                        $("#container_value_coteros").text(response.result.container_value_total);
                        selectedExportId = response.result.export_id;
                        selectedInvoiceId = response.result.invoice_id;

                        var containerData = JSON.parse(response.result.container_data);
                        containerData.forEach(function(container) {
                            var dispatchId = container.dispatch_id;
                            var containerValue = container.container_value;

                            // Find the input with name matching the dispatch ID
                            var input = $("input[name='coteros_container_value[" + dispatchId + "]']");
                            if (input.length > 0) {
                                input.val(parseFloat(containerValue));
                            }
                        });

                        $('input[name="csrf_cgrerp"]').val(response.csrf_hash);
                        $("#divInvoiceListCoteros").hide();
                        $("#btnResetCoteros").show();
                        $("#btnSaveCoteros").text(text_update);
                    }
                }
            });
        });

        $(document).on('click', 'button[data-role=deletecoteros_invoice]', function() {
            selectedInvoiceId = 0;
            selectedExportId = 0;
            selectedExportType = 0;
            var invoice_id = $(this).data("id");
            var export_id = $(this).data("export_id");
            var export_type = 6;
            $.ajax({
                url: base_url + "/dialog_export_action",
                type: "GET",
                data: 'jd=1&is_ajax=3&mode=modal&type=deleteinvoiceconfirmation&id=' + invoice_id + "&export_id=" + export_id + "&export_type=" + export_type,
                success: function(response) {
                    if (response.redirect == true) {
                        window.location.replace(login_url);
                    } else {
                        $("#titlehead").text(response.pageheading);
                        $("#infomessage").text(response.pagemessage);
                        selectedInvoiceId = response.selectedInvoiceId;
                        selectedExportId = response.selectedExportId;
                        selectedExportType = response.selectedExportType;
                        $("#alert-dialog-info").modal('show');
                    }
                }
            });
        });

        //END COTEROS

        //INCENTIVES

        var exportDocumentsIncentivesInvoiceLists = [];
        var invoiceIncentivesCount = <?php echo $exportDocumentsIncentivesInvoiceListsCount; ?>;
        if (invoiceIncentivesCount > 0) {
            exportDocumentsIncentivesInvoiceLists = <?php echo $exportDocumentsIncentivesInvoiceLists; ?>;
        }
        $("#xin_table_invoice_incentives").DataTable({
            data: exportDocumentsIncentivesInvoiceLists,
            columns: [{
                    data: "id",
                    render: function(data, type, row) {
                        return `
                            <button type="button" class="btn icon-btn btn-xs btn-success waves-effect waves-light" data-role="editincentives_invoice" data-export_id="${row.export_id}" data-id="${data}" title="<?php echo $this->lang->line("edit"); ?>">
                                <span class="fas fa-pencil"></span>
                            </button>
                            
                            <button type="button" style="margin-left:5px;" class="btn icon-btn btn-xs btn-delete waves-effect waves-light" data-export_id="${row.export_id}" data-role="deleteincentives_invoice" data-id="${data}" title="<?php echo $this->lang->line("delete"); ?>">
                                <span class="fas fa-trash"></span>
                            </button>
                        `;
                    }
                },
                {
                    data: "invoice_no"
                },
                {
                    data: "supplier_name"
                },
                {
                    data: "invoice_date"
                },
                {
                    data: "sub_total",
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                },
                {
                    data: "tax_total",
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                },
                {
                    data: "allowance_total",
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                },
                {
                    data: "payable_total",
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                },
                {
                    data: "container_value_total",
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                }
            ],
            scrollX: true, // ✅ Enables horizontal scrolling
            autoWidth: true, // ✅ Enables automatic column sizing
            scrollCollapse: true,
            responsive: false, // ✅ Keep this false with scrollX to avoid conflict
            bDestroy: true,
            paging: false,
            searching: false,
            sorting: false,
            language: {
                url: datatable_language
            }
        });

        $(document).on('click', 'button[data-role=editincentives_invoice]', function() {
            selectedInvoiceId = 0;
            selectedExportId = 0;
            selectedExportType = 0;
            var invoice_id = $(this).data("id");
            var export_id = $(this).data("export_id");
            $.ajax({
                url: base_url + "/dialog_export_action",
                type: "GET",
                data: 'jd=1&is_ajax=3&mode=modal&type=editincentives_invoice&id=' + invoice_id + "&export_id=" + export_id,
                success: function(response) {
                    if (response.redirect == true) {
                        window.location.replace(login_url);
                    } else if (response.error != '') {
                        toastr.clear();
                        toastr.error(response.error);
                        $('input[name="csrf_cgrerp"]').val(response.csrf_hash);
                        $("#divInvoiceListIncentives").show();
                        $("#btnResetIncentives").hide();
                    } else {
                        $("#invoice_number_incentives").val(response.result.invoice_number);
                        $("#supplier_name_incentives").val(response.result.supplier_id).trigger("change");
                        $("#issued_date_incentives").val(response.result.invoice_date);
                        $("#formatted_date_incentives").val(response.result.original_invoice_date);
                        $("#subtotal_incentives").val(response.result.sub_total);
                        $("#iva_incentives").val(response.result.tax_total);
                        $("#retefuente_incentives").val(response.result.allowance_total);
                        $("#payable_incentives").val(response.result.payable_total);
                        $("#container_value_incentives").text(response.result.container_value_total);
                        selectedExportId = response.result.export_id;
                        selectedInvoiceId = response.result.invoice_id;

                        var containerData = JSON.parse(response.result.container_data);
                        containerData.forEach(function(container) {
                            var dispatchId = container.dispatch_id;
                            var containerValue = container.container_value;

                            // Find the input with name matching the dispatch ID
                            var input = $("input[name='incentives_container_value[" + dispatchId + "]']");
                            if (input.length > 0) {
                                input.val(parseFloat(containerValue));
                            }
                        });

                        $('input[name="csrf_cgrerp"]').val(response.csrf_hash);
                        $("#divInvoiceListIncentives").hide();
                        $("#btnResetIncentives").show();
                        $("#btnSaveIncentives").text(text_update);
                    }
                }
            });
        });

        $(document).on('click', 'button[data-role=deleteincentives_invoice]', function() {
            selectedInvoiceId = 0;
            selectedExportId = 0;
            selectedExportType = 0;
            var invoice_id = $(this).data("id");
            var export_id = $(this).data("export_id");
            var export_type = 7;
            $.ajax({
                url: base_url + "/dialog_export_action",
                type: "GET",
                data: 'jd=1&is_ajax=3&mode=modal&type=deleteinvoiceconfirmation&id=' + invoice_id + "&export_id=" + export_id + "&export_type=" + export_type,
                success: function(response) {
                    if (response.redirect == true) {
                        window.location.replace(login_url);
                    } else {
                        $("#titlehead").text(response.pageheading);
                        $("#infomessage").text(response.pagemessage);
                        selectedInvoiceId = response.selectedInvoiceId;
                        selectedExportId = response.selectedExportId;
                        selectedExportType = response.selectedExportType;
                        $("#alert-dialog-info").modal('show');
                    }
                }
            });
        });

        //END INCENTIVES

        //REMOBILIZATION

        var exportDocumentsRemobilizationInvoiceLists = [];
        var invoiceRemobilizationCount = <?php echo $exportDocumentsRemobilizationInvoiceListsCount; ?>;
        if (invoiceRemobilizationCount > 0) {
            exportDocumentsRemobilizationInvoiceLists = <?php echo $exportDocumentsRemobilizationInvoiceLists; ?>;
        }
        $("#xin_table_invoice_remobilization").DataTable({
            data: exportDocumentsRemobilizationInvoiceLists,
            columns: [{
                    data: "id",
                    render: function(data, type, row) {
                        return `
                            <button type="button" class="btn icon-btn btn-xs btn-success waves-effect waves-light" data-role="editremobilization_invoice" data-export_id="${row.export_id}" data-id="${data}" title="<?php echo $this->lang->line("edit"); ?>">
                                <span class="fas fa-pencil"></span>
                            </button>
                            
                            <button type="button" style="margin-left:5px;" class="btn icon-btn btn-xs btn-delete waves-effect waves-light" data-export_id="${row.export_id}" data-role="deleteremobilization_invoice" data-id="${data}" title="<?php echo $this->lang->line("delete"); ?>">
                                <span class="fas fa-trash"></span>
                            </button>
                        `;
                    }
                },
                {
                    data: "invoice_no"
                },
                {
                    data: "supplier_name"
                },
                {
                    data: "invoice_date"
                },
                {
                    data: "sub_total",
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                },
                {
                    data: "tax_total",
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                },
                {
                    data: "allowance_total",
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                },
                {
                    data: "payable_total",
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                },
                {
                    data: "container_value_total",
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                }
            ],
            scrollX: true, // ✅ Enables horizontal scrolling
            autoWidth: true, // ✅ Enables automatic column sizing
            scrollCollapse: true,
            responsive: false, // ✅ Keep this false with scrollX to avoid conflict
            bDestroy: true,
            paging: false,
            searching: false,
            sorting: false,
            language: {
                url: datatable_language
            }
        });

        $(document).on('click', 'button[data-role=editremobilization_invoice]', function() {
            selectedInvoiceId = 0;
            selectedExportId = 0;
            selectedExportType = 0;
            var invoice_id = $(this).data("id");
            var export_id = $(this).data("export_id");
            $.ajax({
                url: base_url + "/dialog_export_action",
                type: "GET",
                data: 'jd=1&is_ajax=3&mode=modal&type=editremobilization_invoice&id=' + invoice_id + "&export_id=" + export_id,
                success: function(response) {
                    if (response.redirect == true) {
                        window.location.replace(login_url);
                    } else if (response.error != '') {
                        toastr.clear();
                        toastr.error(response.error);
                        $('input[name="csrf_cgrerp"]').val(response.csrf_hash);
                        $("#divInvoiceListRemobilization").show();
                        $("#btnResetRemobilization").hide();
                    } else {
                        $("#invoice_number_remobilization").val(response.result.invoice_number);
                        $("#supplier_name_remobilization").val(response.result.supplier_id).trigger("change");
                        $("#issued_date_remobilization").val(response.result.invoice_date);
                        $("#formatted_date_remobilization").val(response.result.original_invoice_date);
                        $("#subtotal_remobilization").val(response.result.sub_total);
                        $("#iva_remobilization").val(response.result.tax_total);
                        $("#retefuente_remobilization").val(response.result.allowance_total);
                        $("#payable_remobilization").val(response.result.payable_total);
                        $("#container_value_remobilization").text(response.result.container_value_total);
                        selectedExportId = response.result.export_id;
                        selectedInvoiceId = response.result.invoice_id;

                        var containerData = JSON.parse(response.result.container_data);
                        containerData.forEach(function(container) {
                            var dispatchId = container.dispatch_id;
                            var containerValue = container.container_value;

                            // Find the input with name matching the dispatch ID
                            var input = $("input[name='remobilization_container_value[" + dispatchId + "]']");
                            if (input.length > 0) {
                                input.val(parseFloat(containerValue));
                            }
                        });

                        $('input[name="csrf_cgrerp"]').val(response.csrf_hash);
                        $("#divInvoiceListRemobilization").hide();
                        $("#btnResetRemobilization").show();
                        $("#btnSaveRemobilization").text(text_update);
                    }
                }
            });
        });

        $(document).on('click', 'button[data-role=deleteremobilization_invoice]', function() {
            selectedInvoiceId = 0;
            selectedExportId = 0;
            selectedExportType = 0;
            var invoice_id = $(this).data("id");
            var export_id = $(this).data("export_id");
            var export_type = 8;
            $.ajax({
                url: base_url + "/dialog_export_action",
                type: "GET",
                data: 'jd=1&is_ajax=3&mode=modal&type=deleteinvoiceconfirmation&id=' + invoice_id + "&export_id=" + export_id + "&export_type=" + export_type,
                success: function(response) {
                    if (response.redirect == true) {
                        window.location.replace(login_url);
                    } else {
                        $("#titlehead").text(response.pageheading);
                        $("#infomessage").text(response.pagemessage);
                        selectedInvoiceId = response.selectedInvoiceId;
                        selectedExportId = response.selectedExportId;
                        selectedExportType = response.selectedExportType;
                        $("#alert-dialog-info").modal('show');
                    }
                }
            });
        });

        //END REMOBILIZATION 

        //OTHER COSTS

        var exportDocumentsOthercostInvoiceLists = [];
        var invoiceOthercostCount = <?php echo $exportDocumentsOthercostInvoiceListsCount; ?>;
        if (invoiceOthercostCount > 0) {
            exportDocumentsOthercostInvoiceLists = <?php echo $exportDocumentsOthercostInvoiceLists; ?>;
        }
        $("#xin_table_invoice_othercost").DataTable({
            data: exportDocumentsOthercostInvoiceLists,
            columns: [{
                    data: "id",
                    render: function(data, type, row) {
                        return `
                            <button type="button" class="btn icon-btn btn-xs btn-success waves-effect waves-light" data-role="editothercost_invoice" data-export_id="${row.export_id}" data-id="${data}" title="<?php echo $this->lang->line("edit"); ?>">
                                <span class="fas fa-pencil"></span>
                            </button>

                            <button type="button" style="margin-left:5px;" class="btn icon-btn btn-xs btn-delete waves-effect waves-light" data-export_id="${row.export_id}" data-role="deleteothercost_invoice" data-id="${data}" title="<?php echo $this->lang->line("delete"); ?>">
                                <span class="fas fa-trash"></span>
                            </button>
                        `;
                    }
                },
                {
                    data: "invoice_no"
                },
                {
                    data: "supplier_name"
                },
                {
                    data: "invoice_date"
                },
                {
                    data: "sub_total",
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                },
                {
                    data: "tax_total",
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                },
                {
                    data: "allowance_total",
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                },
                {
                    data: "payable_total",
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                },
                {
                    data: "container_value_total",
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                }
            ],
            scrollX: true, // ✅ Enables horizontal scrolling
            autoWidth: true, // ✅ Enables automatic column sizing
            scrollCollapse: true,
            responsive: false, // ✅ Keep this false with scrollX to avoid conflict
            bDestroy: true,
            paging: false,
            searching: false,
            sorting: false,
            language: {
                url: datatable_language
            }
        });

        $(document).on('click', 'button[data-role=editothercost_invoice]', function() {
            selectedInvoiceId = 0;
            selectedExportId = 0;
            selectedExportType = 0;
            var invoice_id = $(this).data("id");
            var export_id = $(this).data("export_id");
            $.ajax({
                url: base_url + "/dialog_export_action",
                type: "GET",
                data: 'jd=1&is_ajax=3&mode=modal&type=editothercost_invoice&id=' + invoice_id + "&export_id=" + export_id,
                success: function(response) {
                    if (response.redirect == true) {
                        window.location.replace(login_url);
                    } else if (response.error != '') {
                        toastr.clear();
                        toastr.error(response.error);
                        $('input[name="csrf_cgrerp"]').val(response.csrf_hash);
                        $("#divInvoiceListOthercost").show();
                        $("#btnResetOthercost").hide();
                    } else {
                        $("#invoice_number_othercost").val(response.result.invoice_number);
                        $("#supplier_name_othercost").val(response.result.supplier_id).trigger("change");
                        $("#issued_date_othercost").val(response.result.invoice_date);
                        $("#formatted_date_othercost").val(response.result.original_invoice_date);
                        $("#subtotal_othercost").val(response.result.sub_total);
                        $("#iva_othercost").val(response.result.tax_total);
                        $("#retefuente_othercost").val(response.result.allowance_total);
                        $("#payable_othercost").val(response.result.payable_total);
                        $("#container_value_othercost").text(response.result.container_value_total);
                        selectedExportId = response.result.export_id;
                        selectedInvoiceId = response.result.invoice_id;

                        var containerData = JSON.parse(response.result.container_data);
                        containerData.forEach(function(container) {
                            var dispatchId = container.dispatch_id;
                            var containerValue = container.container_value;

                            // Find the input with name matching the dispatch ID
                            var input = $("input[name='othercost_container_value[" + dispatchId + "]']");
                            if (input.length > 0) {
                                input.val(parseFloat(containerValue));
                            }
                        });

                        $('input[name="csrf_cgrerp"]').val(response.csrf_hash);
                        $("#divInvoiceListOthercost").hide();
                        $("#btnResetOthercost").show();
                        $("#btnSaveOthercost").text(text_update);
                    }
                }
            });
        });

        $(document).on('click', 'button[data-role=deleteothercost_invoice]', function() {
            selectedInvoiceId = 0;
            selectedExportId = 0;
            selectedExportType = 0;
            var invoice_id = $(this).data("id");
            var export_id = $(this).data("export_id");
            var export_type = 5;
            $.ajax({
                url: base_url + "/dialog_export_action",
                type: "GET",
                data: 'jd=1&is_ajax=3&mode=modal&type=deleteinvoiceconfirmation&id=' + invoice_id + "&export_id=" + export_id + "&export_type=" + export_type,
                success: function(response) {
                    if (response.redirect == true) {
                        window.location.replace(login_url);
                    } else {
                        $("#titlehead").text(response.pageheading);
                        $("#infomessage").text(response.pagemessage);
                        selectedInvoiceId = response.selectedInvoiceId;
                        selectedExportId = response.selectedExportId;
                        selectedExportType = response.selectedExportType;
                        $("#alert-dialog-info").modal('show');
                    }
                }
            });
        });

        //END OTHER COSTS

        //DHL

        var exportDocumentsDhlcostInvoiceLists = [];
        var invoiceDhlcostCount = <?php echo $exportDocumentsDhlcostInvoiceListsCount; ?>;
        if (invoiceDhlcostCount > 0) {
            exportDocumentsDhlcostInvoiceLists = <?php echo $exportDocumentsDhlcostInvoiceLists; ?>;
        }
        $("#xin_table_invoice_dhlcost").DataTable({
            data: exportDocumentsDhlcostInvoiceLists,
            columns: [{
                    data: "id",
                    render: function(data, type, row) {
                        return `
                            <button type="button" class="btn icon-btn btn-xs btn-success waves-effect waves-light" data-role="editdhlcost_invoice" data-export_id="${row.export_id}" data-id="${data}" title="<?php echo $this->lang->line("edit"); ?>">
                                <span class="fas fa-pencil"></span>
                            </button>
                            
                            <button type="button" style="margin-left:5px;" class="btn icon-btn btn-xs btn-delete waves-effect waves-light" data-export_id="${row.export_id}" data-role="deletedhlcost_invoice" data-id="${data}" title="<?php echo $this->lang->line("delete"); ?>">
                                <span class="fas fa-trash"></span>
                            </button>
                        `;
                    }
                },
                {
                    data: "invoice_no"
                },
                {
                    data: "supplier_name"
                },
                {
                    data: "invoice_date"
                },
                {
                    data: "sub_total",
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                },
                {
                    data: "tax_total",
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                },
                {
                    data: "allowance_total",
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                },
                {
                    data: "payable_total",
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                },
                {
                    data: "container_value_total",
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                }
            ],
            scrollX: true, // ✅ Enables horizontal scrolling
            autoWidth: true, // ✅ Enables automatic column sizing
            scrollCollapse: true,
            responsive: false, // ✅ Keep this false with scrollX to avoid conflict
            bDestroy: true,
            paging: false,
            searching: false,
            sorting: false,
            language: {
                url: datatable_language
            }
        });

        $(document).on('click', 'button[data-role=editdhlcost_invoice]', function() {
            selectedInvoiceId = 0;
            selectedExportId = 0;
            selectedExportType = 0;
            var invoice_id = $(this).data("id");
            var export_id = $(this).data("export_id");
            $.ajax({
                url: base_url + "/dialog_export_action",
                type: "GET",
                data: 'jd=1&is_ajax=3&mode=modal&type=editdhlcost_invoice&id=' + invoice_id + "&export_id=" + export_id,
                success: function(response) {
                    if (response.redirect == true) {
                        window.location.replace(login_url);
                    } else if (response.error != '') {
                        toastr.clear();
                        toastr.error(response.error);
                        $('input[name="csrf_cgrerp"]').val(response.csrf_hash);
                        $("#divInvoiceListDhlCost").show();
                        $("#btnResetDhlCost").hide();
                    } else {
                        $("#invoice_number_dhlcost").val(response.result.invoice_number);
                        $("#supplier_name_dhlcost").val(response.result.supplier_id).trigger("change");
                        $("#issued_date_dhlcost").val(response.result.invoice_date);
                        $("#formatted_date_dhlcost").val(response.result.original_invoice_date);
                        $("#subtotal_dhlcost").val(response.result.sub_total);
                        $("#iva_dhlcost").val(response.result.tax_total);
                        $("#retefuente_dhlcost").val(response.result.allowance_total);
                        $("#payable_dhlcost").val(response.result.payable_total);
                        $("#container_value_dhlcost").text(response.result.container_value_total);
                        selectedExportId = response.result.export_id;
                        selectedInvoiceId = response.result.invoice_id;

                        var containerData = JSON.parse(response.result.container_data);
                        containerData.forEach(function(container) {
                            var dispatchId = container.dispatch_id;
                            var containerValue = container.container_value;

                            // Find the input with name matching the dispatch ID
                            var input = $("input[name='dhlcost_container_value[" + dispatchId + "]']");
                            if (input.length > 0) {
                                input.val(parseFloat(containerValue));
                            }
                        });

                        $('input[name="csrf_cgrerp"]').val(response.csrf_hash);
                        $("#divInvoiceListDhlCost").hide();
                        $("#btnResetDhlCost").show();
                        $("#btnSaveDhlCost").text(text_update);
                    }
                }
            });
        });

        $(document).on('click', 'button[data-role=deletedhlcost_invoice]', function() {
            selectedInvoiceId = 0;
            selectedExportId = 0;
            selectedExportType = 0;
            var invoice_id = $(this).data("id");
            var export_id = $(this).data("export_id");
            var export_type = 13;
            $.ajax({
                url: base_url + "/dialog_export_action",
                type: "GET",
                data: 'jd=1&is_ajax=3&mode=modal&type=deleteinvoiceconfirmation&id=' + invoice_id + "&export_id=" + export_id + "&export_type=" + export_type,
                success: function(response) {
                    if (response.redirect == true) {
                        window.location.replace(login_url);
                    } else {
                        $("#titlehead").text(response.pageheading);
                        $("#infomessage").text(response.pagemessage);
                        selectedInvoiceId = response.selectedInvoiceId;
                        selectedExportId = response.selectedExportId;
                        selectedExportType = response.selectedExportType;
                        $("#alert-dialog-info").modal('show');
                    }
                }
            });
        });

        //END DHL

        //CONTAINER LOADING COST

        var exportDocumentsContainerLoadingCostInvoiceLists = [];
        var invoiceContainerLoadingCostCount = <?php echo $exportDocumentsContainerLoadingCostInvoiceListsCount; ?>;
        if (invoiceContainerLoadingCostCount > 0) {
            exportDocumentsContainerLoadingCostInvoiceLists = <?php echo $exportDocumentsContainerLoadingCostInvoiceLists; ?>;
        }
        $("#xin_table_invoice_containerloadingcost").DataTable({
            data: exportDocumentsContainerLoadingCostInvoiceLists,
            columns: [{
                    data: "id",
                    render: function(data, type, row) {
                        return `
                            <button type="button" class="btn icon-btn btn-xs btn-success waves-effect waves-light" data-role="editcontainerloadingcost_invoice" data-export_id="${row.export_id}" data-id="${data}" title="<?php echo $this->lang->line("edit"); ?>">
                                <span class="fas fa-pencil"></span>
                            </button>
                            
                            <button type="button" style="margin-left:5px;" class="btn icon-btn btn-xs btn-delete waves-effect waves-light" data-export_id="${row.export_id}" data-role="deletecontainerloadingcost_invoice" data-id="${data}" title="<?php echo $this->lang->line("delete"); ?>">
                                <span class="fas fa-trash"></span>
                            </button>
                        `;
                    }
                },
                {
                    data: "invoice_no"
                },
                {
                    data: "supplier_name"
                },
                {
                    data: "invoice_date"
                },
                {
                    data: "sub_total",
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                },
                {
                    data: "tax_total",
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                },
                {
                    data: "allowance_total",
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                },
                {
                    data: "payable_total",
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                },
                {
                    data: "container_value_total",
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                }
            ],
            scrollX: true, // ✅ Enables horizontal scrolling
            autoWidth: true, // ✅ Enables automatic column sizing
            scrollCollapse: true,
            responsive: false, // ✅ Keep this false with scrollX to avoid conflict
            bDestroy: true,
            paging: false,
            searching: false,
            sorting: false,
            language: {
                url: datatable_language
            }
        });

        $(document).on('click', 'button[data-role=editcontainerloadingcost_invoice]', function() {
            selectedInvoiceId = 0;
            selectedExportId = 0;
            selectedExportType = 0;
            var invoice_id = $(this).data("id");
            var export_id = $(this).data("export_id");
            $.ajax({
                url: base_url + "/dialog_export_action",
                type: "GET",
                data: 'jd=1&is_ajax=3&mode=modal&type=editcontainerloadingcost_invoice&id=' + invoice_id + "&export_id=" + export_id,
                success: function(response) {
                    if (response.redirect == true) {
                        window.location.replace(login_url);
                    } else if (response.error != '') {
                        toastr.clear();
                        toastr.error(response.error);
                        $('input[name="csrf_cgrerp"]').val(response.csrf_hash);
                        $("#divInvoiceListContainerLoadingCost").show();
                        $("#btnResetContainerLoadingCost").hide();
                    } else {
                        $("#invoice_number_containerloadingcost").val(response.result.invoice_number);
                        $("#supplier_name_containerloadingcost").val(response.result.supplier_id).trigger("change");
                        $("#issued_date_containerloadingcost").val(response.result.invoice_date);
                        $("#formatted_date_containerloadingcost").val(response.result.original_invoice_date);
                        $("#subtotal_containerloadingcost").val(response.result.sub_total);
                        $("#iva_containerloadingcost").val(response.result.tax_total);
                        $("#retefuente_containerloadingcost").val(response.result.allowance_total);
                        $("#payable_containerloadingcost").val(response.result.payable_total);
                        $("#container_value_containerloadingcost").text(response.result.container_value_total);
                        selectedExportId = response.result.export_id;
                        selectedInvoiceId = response.result.invoice_id;

                        var containerData = JSON.parse(response.result.container_data);
                        containerData.forEach(function(container) {
                            var dispatchId = container.dispatch_id;
                            var containerValue = container.container_value;

                            // Find the input with name matching the dispatch ID
                            var input = $("input[name='containerloadingcost_container_value[" + dispatchId + "]']");
                            if (input.length > 0) {
                                input.val(parseFloat(containerValue));
                            }
                        });

                        $('input[name="csrf_cgrerp"]').val(response.csrf_hash);
                        $("#divInvoiceListContainerLoadingCost").hide();
                        $("#btnResetContainerLoadingCost").show();
                        $("#btnSaveContainerLoadingCost").text(text_update);
                    }
                }
            });
        });

        $(document).on('click', 'button[data-role=deletecontainerloadingcost_invoice]', function() {
            selectedInvoiceId = 0;
            selectedExportId = 0;
            selectedExportType = 0;
            var invoice_id = $(this).data("id");
            var export_id = $(this).data("export_id");
            var export_type = 11;
            $.ajax({
                url: base_url + "/dialog_export_action",
                type: "GET",
                data: 'jd=1&is_ajax=3&mode=modal&type=deleteinvoiceconfirmation&id=' + invoice_id + "&export_id=" + export_id + "&export_type=" + export_type,
                success: function(response) {
                    if (response.redirect == true) {
                        window.location.replace(login_url);
                    } else {
                        $("#titlehead").text(response.pageheading);
                        $("#infomessage").text(response.pagemessage);
                        selectedInvoiceId = response.selectedInvoiceId;
                        selectedExportId = response.selectedExportId;
                        selectedExportType = response.selectedExportType;
                        $("#alert-dialog-info").modal('show');
                    }
                }
            });
        });

        //END CONTAINER LOADING COST

        $("#btnResetCustoms").click(function() {
            resetForm();
        });

        $("#btnResetITR").click(function() {
            resetForm();
        });

        $("#btnResetPort").click(function() {
            resetForm();
        });

        $("#btnResetShipping").click(function() {
            resetForm();
        });

        $("#btnResetFumigation").click(function() {
            resetForm();
        });

        $("#btnResetCoteros").click(function() {
            resetForm();
        });

        $("#btnResetPhyto").click(function() {
            resetForm();
        });

        $("#btnResetIncentives").click(function() {
            resetForm();
        });

        $("#btnResetRemobilization").click(function() {
            resetForm();
        });

        $("#deletebutton").click(function(e) {
            if (selectedInvoiceId > 0 && selectedExportId > 0) {
                $.ajax({
                    type: "GET",
                    url: base_url + "/dialog_export_action",
                    data: 'jd=1&is_ajax=3&mode=modal&type=deleteexportinvoice&inputid=' + selectedInvoiceId + "&inputid1=" + selectedExportId + "&inputid2=" + selectedExportType,
                    success: function(response) {
                        $("#loading").hide();
                        selectedInvoiceId = 0;
                        selectedExportId = 0;
                        selectedExportType = 0;
                        if (response.redirect == true) {
                            window.location.replace(login_url);
                        } else if (response.error != '') {
                            toastr.clear();
                            toastr.error(response.error);
                            $('input[name="csrf_cgrerp"]').val(response.csrf_hash);
                        } else {
                            toastr.clear();
                            toastr.success(response.result);
                            $('input[name="csrf_cgrerp"]').val(response.csrf_hash);
                            $("#alert-dialog-info").modal('hide');
                            reloadTables(response.type, response.updatedlist);
                        }
                    }
                });
            } else {
                toastr.clear();
                toastr.error(common_error);
            }
        });

        $("#btnResetOthercost").click(function() {
            resetForm();
        });

        $("#btnResetDhlCost").click(function() {
            resetForm();
        });

        $("#btnResetContainerLoadingCost").click(function() {
            resetForm();
        });
    });

    function resetForm() {

        selectedInvoiceId = 0;
        selectedExportId = 0;
        selectedExportType = 0;

        //CUSTOM
        $("#invoice_number_custom").val("");
        $("#supplier_name_custom").val(0).trigger("change");
        $("#issued_date_custom").val("");
        $("#formatted_date_custom").val("");
        $("#subtotal_custom").val("");
        $("#iva_custom").val("");
        $("#retefuente_custom").val("");
        $("#payable_custom").val("");
        $("#container_value_custom").text("0");
        $("#fileUploadDoc").val("");

        $("input[name^='custom_container_value']").each(function() {
            $(this).val("0");
        });

        $("#divInvoiceListCustoms").show();

        $("#error-selectdoccustom").hide();
        $("#error-invoice_no_custom").hide();
        $("#error-supplier_name_custom").hide();
        $("#error-supplier_id_custom").hide();
        $("#error-issued_date_custom").hide();
        $("#error-subtotal_custom").hide();
        $("#error-iva_custom").hide();
        $("#error-retefuente_custom").hide();
        $("#error-payable_custom").hide();
        $("#divXml").hide();
        $("#divPdf").show();
        $("#btnResetCustoms").hide();
        $("#btnSaveCustoms").text(text_save);

        //ITR
        $("#invoice_number_itr").val("");
        $("#supplier_name_itr").val(0).trigger("change");
        $("#issued_date_itr").val("");
        $("#formatted_date_itr").val("");
        $("#subtotal_itr").val("");
        $("#iva_itr").val("");
        $("#retefuente_itr").val("");
        $("#payable_itr").val("");
        $("#container_value_itr").text("0");
        $("#fileUploadDoc_ITR").val("");

        $("input[name^='itr_container_value']").each(function() {
            $(this).val("0");
        });

        $("#divInvoiceListITR").show();

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
        $("#divPdfITR").show();
        $("#btnResetITR").hide();
        $("#btnSaveITR").text(text_save);

        //PORT
        $("#invoice_number_port").val("");
        $("#supplier_name_port").val(0).trigger("change");
        $("#issued_date_port").val("");
        $("#formatted_date_port").val("");
        $("#subtotal_port").val("");
        $("#iva_port").val("");
        $("#retefuente_port").val("");
        $("#payable_port").val("");
        $("#container_value_port").text("0");
        $("#fileUploadDoc_Port").val("");

        $("input[name^='port_container_value']").each(function() {
            $(this).val("0");
        });

        $("#divInvoiceListPort").show();

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
        $("#divPdfPort").show();
        $("#btnResetPort").hide();
        $("#btnSavePort").text(text_save);

        //SHIPPING
        $("#invoice_number_shipping").val("");
        $("#supplier_name_shipping").val(0).trigger("change");
        $("#issued_date_shipping").val("");
        $("#formatted_date_shipping").val("");
        $("#subtotal_shipping").val("");
        $("#iva_shipping").val("");
        $("#retefuente_shipping").val("");
        $("#payable_shipping").val("");
        $("#container_value_shipping").text("0");
        $("#fileUploadDoc_Shipping").val("");

        $("input[name^='shipping_container_value']").each(function() {
            $(this).val("0");
        });

        $("#divInvoiceListShipping").show();

        $("#error-selectdocshipping").hide();
        $("#error-invoice_no_shipping").hide();
        $("#error-supplier_name_selectdocshipping").hide();
        $("#error-supplier_id_selectdocshipping").hide();
        $("#error-issued_date_selectdocshipping").hide();
        $("#error-subtotal_selectdocshipping").hide();
        $("#error-iva_selectdocshipping").hide();
        $("#error-retefuente_selectdocshipping").hide();
        $("#error-payable_selectdocshipping").hide();
        $("#divXmlShipping").hide();
        $("#divPdfShipping").show();
        $("#btnResetShipping").hide();
        $("#btnSaveShipping").text(text_save);

        //FUMIGATION
        $("#invoice_number_fumigation").val("");
        $("#supplier_name_fumigation").val(0).trigger("change");
        $("#issued_date_fumigation").val("");
        $("#formatted_date_fumigation").val("");
        $("#subtotal_fumigation").val("");
        $("#iva_fumigation").val("");
        $("#retefuente_fumigation").val("");
        $("#payable_fumigation").val("");
        $("#container_value_fumigation").text("0");
        $("#fileUploadDoc_Fumigation").val("");

        $("input[name^='fumigation_container_value']").each(function() {
            $(this).val("0");
        });

        $("#divInvoiceListFumigation").show();

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
        $("#divPdfFumigation").show();
        $("#btnResetFumigation").hide();
        $("#btnSaveFumigation").text(text_save);

        //COTEROS
        $("#invoice_number_coteros").val("");
        $("#supplier_name_coteros").val(0).trigger("change");
        $("#issued_date_coteros").val("");
        $("#formatted_date_coteros").val("");
        $("#subtotal_coteros").val("");
        $("#iva_coteros").val("");
        $("#retefuente_coteros").val("");
        $("#payable_coteros").val("");
        $("#container_value_coteros").text("0");
        $("#fileUploadDoc_Coteros").val("");

        $("input[name^='coteros_container_value']").each(function() {
            $(this).val("0");
        });

        $("#divInvoiceListCoteros").show();

        $("#error-selectdoccoteros").hide();
        $("#error-invoice_no_coteros").hide();
        $("#error-supplier_name_coteros").hide();
        $("#error-supplier_id_coteros").hide();
        $("#error-issued_date_coteros").hide();
        $("#error-subtotal_coteros").hide();
        $("#error-iva_coteros").hide();
        $("#error-retefuente_coteros").hide();
        $("#error-payable_coteros").hide();
        $("#divXmlCoteros").hide();
        $("#divPdfCoteros").show();
        $("#btnResetCoteros").hide();
        $("#btnSaveCoteros").text(text_save);

        //PHYTO
        $("#invoice_number_phyto").val("");
        $("#supplier_name_phyto").val(0).trigger("change");
        $("#issued_date_phyto").val("");
        $("#formatted_date_phyto").val("");
        $("#subtotal_phyto").val("");
        $("#iva_phyto").val("");
        $("#retefuente_phyto").val("");
        $("#payable_phyto").val("");
        $("#container_value_phyto").text("0");
        $("#fileUploadDoc_Phyto").val("");

        $("input[name^='phyto_container_value']").each(function() {
            $(this).val("0");
        });

        $("#divInvoiceListPhyto").show();

        $("#error-selectdocphyto").hide();
        $("#error-invoice_no_phyto").hide();
        $("#error-supplier_name_phyto").hide();
        $("#error-supplier_id_phyto").hide();
        $("#error-issued_date_phyto").hide();
        $("#error-subtotal_phyto").hide();
        $("#error-iva_phyto").hide();
        $("#error-retefuente_phyto").hide();
        $("#error-payable_phyto").hide();
        $("#divXmlPhyto").hide();
        $("#divPdfPhyto").show();
        $("#btnResetPhyto").hide();
        $("#btnSavePhyto").text(text_save);

        //INCENTIVES
        $("#invoice_number_incentives").val("");
        $("#supplier_name_incentives").val(0).trigger("change");
        $("#issued_date_incentives").val("");
        $("#formatted_date_incentives").val("");
        $("#subtotal_incentives").val("");
        $("#iva_incentives").val("");
        $("#retefuente_incentives").val("");
        $("#payable_incentives").val("");
        $("#container_value_incentives").text("0");
        $("#fileUploadDoc_Incentives").val("");

        $("input[name^='incentives_container_value']").each(function() {
            $(this).val("0");
        });

        $("#divInvoiceListIncentives").show();

        $("#error-selectdocincentives").hide();
        $("#error-invoice_no_incentives").hide();
        $("#error-supplier_name_incentives").hide();
        $("#error-supplier_id_incentives").hide();
        $("#error-issued_date_incentives").hide();
        $("#error-subtotal_incentives").hide();
        $("#error-iva_incentives").hide();
        $("#error-retefuente_incentives").hide();
        $("#error-payable_incentives").hide();
        $("#divXmlIncentives").hide();
        $("#divPdfIncentives").show();
        $("#btnResetIncentives").hide();
        $("#btnSaveIncentives").text(text_save);

        //REMOBILIZATION
        $("#invoice_number_remobilization").val("");
        $("#supplier_name_remobilization").val(0).trigger("change");
        $("#issued_date_remobilization").val("");
        $("#formatted_date_remobilization").val("");
        $("#subtotal_remobilization").val("");
        $("#iva_remobilization").val("");
        $("#retefuente_remobilization").val("");
        $("#payable_remobilization").val("");
        $("#container_value_remobilization").text("0");
        $("#fileUploadDoc_Remobilization").val("");

        $("input[name^='remobilization_container_value']").each(function() {
            $(this).val("0");
        });

        $("#divInvoiceListRemobilization").show();

        $("#error-selectdocremobilization").hide();
        $("#error-invoice_no_remobilization").hide();
        $("#error-supplier_name_remobilization").hide();
        $("#error-supplier_id_remobilization").hide();
        $("#error-issued_date_remobilization").hide();
        $("#error-subtotal_remobilization").hide();
        $("#error-iva_remobilization").hide();
        $("#error-retefuente_remobilization").hide();
        $("#error-payable_remobilization").hide();
        $("#divXmlRemobilization").hide();
        $("#divPdfRemobilization").show();
        $("#btnResetRemobilization").hide();
        $("#btnSaveRemobilization").text(text_save);

        //OTHER COST
        $("#invoice_number_othercost").val("");
        $("#supplier_name_othercost").val(0).trigger("change");
        $("#issued_date_othercost").val("");
        $("#formatted_date_othercost").val("");
        $("#subtotal_othercost").val("");
        $("#iva_othercost").val("");
        $("#retefuente_othercost").val("");
        $("#payable_othercost").val("");
        $("#container_value_othercost").text("0");
        $("#fileUploadDoc_Othercost").val("");

        $("input[name^='othercost_container_value']").each(function() {
            $(this).val("0");
        });

        $("#divInvoiceListOthercost").show();

        $("#error-selectdocothercost").hide();
        $("#error-invoice_no_othercost").hide();
        $("#error-supplier_name_othercost").hide();
        $("#error-supplier_id_othercost").hide();
        $("#error-issued_date_othercost").hide();
        $("#error-subtotal_othercost").hide();
        $("#error-iva_othercost").hide();
        $("#error-retefuente_othercost").hide();
        $("#error-payable_othercost").hide();
        $("#divXmlOthercost").hide();
        $("#divPdfOthercost").show();
        $("#btnResetOthercost").hide();
        $("#btnSaveOthercost").text(text_save);

        //DHL COST
        $("#invoice_number_dhlcost").val("");
        $("#supplier_name_dhlcost").val(0).trigger("change");
        $("#issued_date_dhlcost").val("");
        $("#formatted_date_dhlcost").val("");
        $("#subtotal_dhlcost").val("");
        $("#iva_dhlcost").val("");
        $("#retefuente_dhlcost").val("");
        $("#payable_dhlcost").val("");
        $("#container_value_dhlcost").text("0");
        $("#fileUploadDoc_Dhlcost").val("");

        $("input[name^='dhlcost_container_value']").each(function() {
            $(this).val("0");
        });

        $("#divInvoiceListDhlCost").show();

        $("#error-selectdocdhlcost").hide();
        $("#error-invoice_no_dhlcost").hide();
        $("#error-supplier_name_dhlcost").hide();
        $("#error-supplier_id_dhlcost").hide();
        $("#error-issued_date_dhlcost").hide();
        $("#error-subtotal_dhlcost").hide();
        $("#error-iva_dhlcost").hide();
        $("#error-retefuente_dhlcost").hide();
        $("#error-payable_dhlCost").hide();
        $("#divXmlDhlCost").hide();
        $("#divPdfDhlCost").show();
        $("#btnResetDhlCost").hide();
        $("#btnSaveDhlCost").text(text_save);

        //CONTAINER LOADING COST
        $("#invoice_number_containerloadingcost").val("");
        $("#supplier_name_containerloadingcost").val(0).trigger("change");
        $("#issued_date_containerloadingcost").val("");
        $("#formatted_date_containerloadingcost").val("");
        $("#subtotal_containerloadingcost").val("");
        $("#iva_containerloadingcost").val("");
        $("#retefuente_containerloadingcost").val("");
        $("#payable_containerloadingcost").val("");
        $("#container_value_containerloadingcost").text("0");
        $("#fileUploadDoc_ContainerLoadingCost").val("");

        $("input[name^='containerloadingcost_container_value']").each(function() {
            $(this).val("0");
        });

        $("#divInvoiceListContainerLoadingCost").show();

        $("#error-selectdoccontainer_loading_cost").hide();
        $("#error-invoice_no_container_loading_cost").hide();
        $("#error-supplier_name_container_loading_cost").hide();
        $("#error-supplier_id_container_loading_cost").hide();
        $("#error-issued_date_container_loading_cost").hide();
        $("#error-subtotal_container_loading_cost").hide();
        $("#error-iva_container_loading_cost").hide();
        $("#error-retefuente_container_loading_cost").hide();
        $("#error-payable_container_loading_cost").hide();
        $("#divXmlContainerLoadingCost").hide();
        $("#divPdfContainerLoadingCost").show();
        $("#btnResetContainerLoadingCost").hide();
        $("#btnSaveContainerLoadingCost").text(text_save);
    }

    function reloadTables(type, response) {

        if (type == 1) {
            $("#xin_table_invoice_customs").DataTable({
                data: JSON.parse(response),
                columns: [{
                        data: "id",
                        render: function(data, type, row) {
                            return `
                                                    <button type="button" class="btn icon-btn btn-xs btn-success waves-effect waves-light" data-role="editcustoms_invoice" data-export_id="${row.export_id}" data-id="${data}" title="<?php echo $this->lang->line("edit"); ?>">
                                                        <span class="fas fa-pencil"></span>
                                                    </button>
                                                    
                                                    <button type="button" style="margin-left:5px;" class="btn icon-btn btn-xs btn-delete waves-effect waves-light" data-export_id="${row.export_id}" data-role="deletecustoms_invoice" data-id="${data}" title="<?php echo $this->lang->line("delete"); ?>">
                                                        <span class="fas fa-trash"></span>
                                                    </button>
                                                `;
                        }
                    },
                    {
                        data: "invoice_no"
                    },
                    {
                        data: "supplier_name"
                    },
                    {
                        data: "invoice_date"
                    },
                    {
                        data: "sub_total",
                        render: function(data) {
                            return parseFloat(data).toFixed(2);
                        }
                    },
                    {
                        data: "tax_total",
                        render: function(data) {
                            return parseFloat(data).toFixed(2);
                        }
                    },
                    {
                        data: "allowance_total",
                        render: function(data) {
                            return parseFloat(data).toFixed(2);
                        }
                    },
                    {
                        data: "payable_total",
                        render: function(data) {
                            return parseFloat(data).toFixed(2);
                        }
                    },
                    {
                        data: "container_value_total",
                        render: function(data) {
                            return parseFloat(data).toFixed(2);
                        }
                    }
                ],
                scrollX: true, // ✅ Enables horizontal scrolling
                autoWidth: true, // ✅ Enables automatic column sizing
                scrollCollapse: true,
                responsive: false, // ✅ Keep this false with scrollX to avoid conflict
                bDestroy: true,
                paging: false,
                searching: false,
                sorting: false,
                language: {
                    url: datatable_language
                }
            });
        } else if (type == 2) {

            $("#xin_table_invoice_itr").DataTable({
                data: JSON.parse(response),
                columns: [{
                        data: "id",
                        render: function(data, type, row) {
                            return `
                                                    <button type="button" class="btn icon-btn btn-xs btn-success waves-effect waves-light" data-role="edititr_invoice" data-export_id="${row.export_id}" data-id="${data}" title="<?php echo $this->lang->line("edit"); ?>">
                                                        <span class="fas fa-pencil"></span>
                                                    </button>
                                                    
                                                    <button type="button" style="margin-left:5px;" class="btn icon-btn btn-xs btn-delete waves-effect waves-light" data-export_id="${row.export_id}" data-role="deleteitr_invoice" data-id="${data}" title="<?php echo $this->lang->line("delete"); ?>">
                                                        <span class="fas fa-trash"></span>
                                                    </button>
                                                `;
                        }
                    },
                    {
                        data: "invoice_no"
                    },
                    {
                        data: "supplier_name"
                    },
                    {
                        data: "invoice_date"
                    },
                    {
                        data: "sub_total",
                        render: function(data) {
                            return parseFloat(data).toFixed(2);
                        }
                    },
                    {
                        data: "tax_total",
                        render: function(data) {
                            return parseFloat(data).toFixed(2);
                        }
                    },
                    {
                        data: "allowance_total",
                        render: function(data) {
                            return parseFloat(data).toFixed(2);
                        }
                    },
                    {
                        data: "payable_total",
                        render: function(data) {
                            return parseFloat(data).toFixed(2);
                        }
                    },
                    {
                        data: "container_value_total",
                        render: function(data) {
                            return parseFloat(data).toFixed(2);
                        }
                    }
                ],
                scrollX: true, // ✅ Enables horizontal scrolling
                autoWidth: true, // ✅ Enables automatic column sizing
                scrollCollapse: true,
                responsive: false, // ✅ Keep this false with scrollX to avoid conflict
                bDestroy: true,
                paging: false,
                searching: false,
                sorting: false,
                language: {
                    url: datatable_language
                }
            });

        } else if (type == 3) {

            $("#xin_table_invoice_port").DataTable({
                data: JSON.parse(response),
                columns: [{
                        data: "id",
                        render: function(data, type, row) {
                            return `
                                                    <button type="button" class="btn icon-btn btn-xs btn-success waves-effect waves-light" data-role="editport_invoice" data-export_id="${row.export_id}" data-id="${data}" title="<?php echo $this->lang->line("edit"); ?>">
                                                        <span class="fas fa-pencil"></span>
                                                    </button>
                                                    
                                                    <button type="button" style="margin-left:5px;" class="btn icon-btn btn-xs btn-delete waves-effect waves-light" data-export_id="${row.export_id}" data-role="deleteport_invoice" data-id="${data}" title="<?php echo $this->lang->line("delete"); ?>">
                                                        <span class="fas fa-trash"></span>
                                                    </button>
                                                `;
                        }
                    },
                    {
                        data: "invoice_no"
                    },
                    {
                        data: "supplier_name"
                    },
                    {
                        data: "invoice_date"
                    },
                    {
                        data: "sub_total",
                        render: function(data) {
                            return parseFloat(data).toFixed(2);
                        }
                    },
                    {
                        data: "tax_total",
                        render: function(data) {
                            return parseFloat(data).toFixed(2);
                        }
                    },
                    {
                        data: "allowance_total",
                        render: function(data) {
                            return parseFloat(data).toFixed(2);
                        }
                    },
                    {
                        data: "payable_total",
                        render: function(data) {
                            return parseFloat(data).toFixed(2);
                        }
                    },
                    {
                        data: "container_value_total",
                        render: function(data) {
                            return parseFloat(data).toFixed(2);
                        }
                    }
                ],
                scrollX: true, // ✅ Enables horizontal scrolling
                autoWidth: true, // ✅ Enables automatic column sizing
                scrollCollapse: true,
                responsive: false, // ✅ Keep this false with scrollX to avoid conflict
                bDestroy: true,
                paging: false,
                searching: false,
                sorting: false,
                language: {
                    url: datatable_language
                }
            });

        } else if (type == 4) {

            $("#xin_table_invoice_fumigation").DataTable({
                data: JSON.parse(response),
                columns: [{
                        data: "id",
                        render: function(data, type, row) {
                            return `
                                                        <button type="button" class="btn icon-btn btn-xs btn-success waves-effect waves-light" data-role="editfumigation_invoice" data-export_id="${row.export_id}" data-id="${data}" title="<?php echo $this->lang->line("edit"); ?>">
                                                            <span class="fas fa-pencil"></span>
                                                        </button>
                                                        
                                                        <button type="button" style="margin-left:5px;" class="btn icon-btn btn-xs btn-delete waves-effect waves-light" data-export_id="${row.export_id}" data-role="deletefumigation_invoice" data-id="${data}" title="<?php echo $this->lang->line("delete"); ?>">
                                                            <span class="fas fa-trash"></span>
                                                        </button>
                                                    `;
                        }
                    },
                    {
                        data: "invoice_no"
                    },
                    {
                        data: "supplier_name"
                    },
                    {
                        data: "invoice_date"
                    },
                    {
                        data: "sub_total",
                        render: function(data) {
                            return parseFloat(data).toFixed(2);
                        }
                    },
                    {
                        data: "tax_total",
                        render: function(data) {
                            return parseFloat(data).toFixed(2);
                        }
                    },
                    {
                        data: "allowance_total",
                        render: function(data) {
                            return parseFloat(data).toFixed(2);
                        }
                    },
                    {
                        data: "payable_total",
                        render: function(data) {
                            return parseFloat(data).toFixed(2);
                        }
                    },
                    {
                        data: "container_value_total",
                        render: function(data) {
                            return parseFloat(data).toFixed(2);
                        }
                    }
                ],
                scrollX: true, // ✅ Enables horizontal scrolling
                autoWidth: true, // ✅ Enables automatic column sizing
                scrollCollapse: true,
                responsive: false, // ✅ Keep this false with scrollX to avoid conflict
                bDestroy: true,
                paging: false,
                searching: false,
                sorting: false,
                language: {
                    url: datatable_language
                }
            });

        } else if (type == 5) {

            $("#xin_table_invoice_phyto").DataTable({
                data: JSON.parse(response),
                columns: [{
                        data: "id",
                        render: function(data, type, row) {
                            return `
                                                        <button type="button" class="btn icon-btn btn-xs btn-success waves-effect waves-light" data-role="editphyto_invoice" data-export_id="${row.export_id}" data-id="${data}" title="<?php echo $this->lang->line("edit"); ?>">
                                                            <span class="fas fa-pencil"></span>
                                                        </button>
                                                        
                                                        <button type="button" style="margin-left:5px;" class="btn icon-btn btn-xs btn-delete waves-effect waves-light" data-export_id="${row.export_id}" data-role="deletephyto_invoice" data-id="${data}" title="<?php echo $this->lang->line("delete"); ?>">
                                                            <span class="fas fa-trash"></span>
                                                        </button>
                                                    `;
                        }
                    },
                    {
                        data: "invoice_no"
                    },
                    {
                        data: "supplier_name"
                    },
                    {
                        data: "invoice_date"
                    },
                    {
                        data: "sub_total",
                        render: function(data) {
                            return parseFloat(data).toFixed(2);
                        }
                    },
                    {
                        data: "tax_total",
                        render: function(data) {
                            return parseFloat(data).toFixed(2);
                        }
                    },
                    {
                        data: "allowance_total",
                        render: function(data) {
                            return parseFloat(data).toFixed(2);
                        }
                    },
                    {
                        data: "payable_total",
                        render: function(data) {
                            return parseFloat(data).toFixed(2);
                        }
                    },
                    {
                        data: "container_value_total",
                        render: function(data) {
                            return parseFloat(data).toFixed(2);
                        }
                    }
                ],
                scrollX: true, // ✅ Enables horizontal scrolling
                autoWidth: true, // ✅ Enables automatic column sizing
                scrollCollapse: true,
                responsive: false, // ✅ Keep this false with scrollX to avoid conflict
                bDestroy: true,
                paging: false,
                searching: false,
                sorting: false,
                language: {
                    url: datatable_language
                }
            });

        } else if (type == 6) {

            $("#xin_table_invoice_coteros").DataTable({
                data: JSON.parse(response),
                columns: [{
                        data: "id",
                        render: function(data, type, row) {
                            return `
                                                        <button type="button" class="btn icon-btn btn-xs btn-success waves-effect waves-light" data-role="editcoteros_invoice" data-export_id="${row.export_id}" data-id="${data}" title="<?php echo $this->lang->line("edit"); ?>">
                                                            <span class="fas fa-pencil"></span>
                                                        </button>
                                                        
                                                        <button type="button" style="margin-left:5px;" class="btn icon-btn btn-xs btn-delete waves-effect waves-light" data-export_id="${row.export_id}" data-role="deletecoteros_invoice" data-id="${data}" title="<?php echo $this->lang->line("delete"); ?>">
                                                            <span class="fas fa-trash"></span>
                                                        </button>
                                                    `;
                        }
                    },
                    {
                        data: "invoice_no"
                    },
                    {
                        data: "supplier_name"
                    },
                    {
                        data: "invoice_date"
                    },
                    {
                        data: "sub_total",
                        render: function(data) {
                            return parseFloat(data).toFixed(2);
                        }
                    },
                    {
                        data: "tax_total",
                        render: function(data) {
                            return parseFloat(data).toFixed(2);
                        }
                    },
                    {
                        data: "allowance_total",
                        render: function(data) {
                            return parseFloat(data).toFixed(2);
                        }
                    },
                    {
                        data: "payable_total",
                        render: function(data) {
                            return parseFloat(data).toFixed(2);
                        }
                    },
                    {
                        data: "container_value_total",
                        render: function(data) {
                            return parseFloat(data).toFixed(2);
                        }
                    }
                ],
                scrollX: true, // ✅ Enables horizontal scrolling
                autoWidth: true, // ✅ Enables automatic column sizing
                scrollCollapse: true,
                responsive: false, // ✅ Keep this false with scrollX to avoid conflict
                bDestroy: true,
                paging: false,
                searching: false,
                sorting: false,
                language: {
                    url: datatable_language
                }
            });

        } else if (type == 7) {

            $("#xin_table_invoice_incentives").DataTable({
                data: JSON.parse(response),
                columns: [{
                        data: "id",
                        render: function(data, type, row) {
                            return `
                                                        <button type="button" class="btn icon-btn btn-xs btn-success waves-effect waves-light" data-role="editincentives_invoice" data-export_id="${row.export_id}" data-id="${data}" title="<?php echo $this->lang->line("edit"); ?>">
                                                            <span class="fas fa-pencil"></span>
                                                        </button>
                                                        
                                                        <button type="button" style="margin-left:5px;" class="btn icon-btn btn-xs btn-delete waves-effect waves-light" data-export_id="${row.export_id}" data-role="deleteincentives_invoice" data-id="${data}" title="<?php echo $this->lang->line("delete"); ?>">
                                                            <span class="fas fa-trash"></span>
                                                        </button>
                                                    `;
                        }
                    },
                    {
                        data: "invoice_no"
                    },
                    {
                        data: "supplier_name"
                    },
                    {
                        data: "invoice_date"
                    },
                    {
                        data: "sub_total",
                        render: function(data) {
                            return parseFloat(data).toFixed(2);
                        }
                    },
                    {
                        data: "tax_total",
                        render: function(data) {
                            return parseFloat(data).toFixed(2);
                        }
                    },
                    {
                        data: "allowance_total",
                        render: function(data) {
                            return parseFloat(data).toFixed(2);
                        }
                    },
                    {
                        data: "payable_total",
                        render: function(data) {
                            return parseFloat(data).toFixed(2);
                        }
                    },
                    {
                        data: "container_value_total",
                        render: function(data) {
                            return parseFloat(data).toFixed(2);
                        }
                    }
                ],
                scrollX: true, // ✅ Enables horizontal scrolling
                autoWidth: true, // ✅ Enables automatic column sizing
                scrollCollapse: true,
                responsive: false, // ✅ Keep this false with scrollX to avoid conflict
                bDestroy: true,
                paging: false,
                searching: false,
                sorting: false,
                language: {
                    url: datatable_language
                }
            });

        } else if (type == 8) {

            $("#xin_table_invoice_remobilization").DataTable({
                data: JSON.parse(response),
                columns: [{
                        data: "id",
                        render: function(data, type, row) {
                            return `
                                                        <button type="button" class="btn icon-btn btn-xs btn-success waves-effect waves-light" data-role="editremobilization_invoice" data-export_id="${row.export_id}" data-id="${data}" title="<?php echo $this->lang->line("edit"); ?>">
                                                            <span class="fas fa-pencil"></span>
                                                        </button>
                                                        
                                                        <button type="button" style="margin-left:5px;" class="btn icon-btn btn-xs btn-delete waves-effect waves-light" data-export_id="${row.export_id}" data-role="deleteremobilization_invoice" data-id="${data}" title="<?php echo $this->lang->line("delete"); ?>">
                                                            <span class="fas fa-trash"></span>
                                                        </button>
                                                    `;
                        }
                    },
                    {
                        data: "invoice_no"
                    },
                    {
                        data: "supplier_name"
                    },
                    {
                        data: "invoice_date"
                    },
                    {
                        data: "sub_total",
                        render: function(data) {
                            return parseFloat(data).toFixed(2);
                        }
                    },
                    {
                        data: "tax_total",
                        render: function(data) {
                            return parseFloat(data).toFixed(2);
                        }
                    },
                    {
                        data: "allowance_total",
                        render: function(data) {
                            return parseFloat(data).toFixed(2);
                        }
                    },
                    {
                        data: "payable_total",
                        render: function(data) {
                            return parseFloat(data).toFixed(2);
                        }
                    },
                    {
                        data: "container_value_total",
                        render: function(data) {
                            return parseFloat(data).toFixed(2);
                        }
                    }
                ],
                scrollX: true, // ✅ Enables horizontal scrolling
                autoWidth: true, // ✅ Enables automatic column sizing
                scrollCollapse: true,
                responsive: false, // ✅ Keep this false with scrollX to avoid conflict
                bDestroy: true,
                paging: false,
                searching: false,
                sorting: false,
                language: {
                    url: datatable_language
                }
            });

        } else if (type == 9) {

            $("#xin_table_invoice_shipping").DataTable({
                data: JSON.parse(response),
                columns: [{
                        data: "id",
                        render: function(data, type, row) {
                            return `
                                                        <button type="button" class="btn icon-btn btn-xs btn-success waves-effect waves-light" data-role="editshipping_invoice" data-export_id="${row.export_id}" data-id="${data}" title="<?php echo $this->lang->line("edit"); ?>">
                                                            <span class="fas fa-pencil"></span>
                                                        </button>
                                                        
                                                        <button type="button" style="margin-left:5px;" class="btn icon-btn btn-xs btn-delete waves-effect waves-light" data-export_id="${row.export_id}" data-role="deleteshipping_invoice" data-id="${data}" title="<?php echo $this->lang->line("delete"); ?>">
                                                            <span class="fas fa-trash"></span>
                                                        </button>
                                                    `;
                        }
                    },
                    {
                        data: "invoice_no"
                    },
                    {
                        data: "supplier_name"
                    },
                    {
                        data: "invoice_date"
                    },
                    {
                        data: "sub_total",
                        render: function(data) {
                            return parseFloat(data).toFixed(2);
                        }
                    },
                    {
                        data: "tax_total",
                        render: function(data) {
                            return parseFloat(data).toFixed(2);
                        }
                    },
                    {
                        data: "allowance_total",
                        render: function(data) {
                            return parseFloat(data).toFixed(2);
                        }
                    },
                    {
                        data: "payable_total",
                        render: function(data) {
                            return parseFloat(data).toFixed(2);
                        }
                    },
                    {
                        data: "container_value_total",
                        render: function(data) {
                            return parseFloat(data).toFixed(2);
                        }
                    }
                ],
                scrollX: true, // ✅ Enables horizontal scrolling
                autoWidth: true, // ✅ Enables automatic column sizing
                scrollCollapse: true,
                responsive: false, // ✅ Keep this false with scrollX to avoid conflict
                bDestroy: true,
                paging: false,
                searching: false,
                sorting: false,
                language: {
                    url: datatable_language
                }
            });
        } else if (type == 12) {

            $("#xin_table_invoice_othercost").DataTable({
                data: JSON.parse(response),
                columns: [{
                        data: "id",
                        render: function(data, type, row) {
                            return `
                                                        <button type="button" class="btn icon-btn btn-xs btn-success waves-effect waves-light" data-role="editothercost_invoice" data-export_id="${row.export_id}" data-id="${data}" title="<?php echo $this->lang->line("edit"); ?>">
                                                            <span class="fas fa-pencil"></span>
                                                        </button>

                                                        <button type="button" style="margin-left:5px;" class="btn icon-btn btn-xs btn-delete waves-effect waves-light" data-export_id="${row.export_id}" data-role="deleteothercost_invoice" data-id="${data}" title="<?php echo $this->lang->line("delete"); ?>">
                                                            <span class="fas fa-trash"></span>
                                                        </button>
                                                    `;
                        }
                    },
                    {
                        data: "invoice_no"
                    },
                    {
                        data: "supplier_name"
                    },
                    {
                        data: "invoice_date"
                    },
                    {
                        data: "sub_total",
                        render: function(data) {
                            return parseFloat(data).toFixed(2);
                        }
                    },
                    {
                        data: "tax_total",
                        render: function(data) {
                            return parseFloat(data).toFixed(2);
                        }
                    },
                    {
                        data: "allowance_total",
                        render: function(data) {
                            return parseFloat(data).toFixed(2);
                        }
                    },
                    {
                        data: "payable_total",
                        render: function(data) {
                            return parseFloat(data).toFixed(2);
                        }
                    },
                    {
                        data: "container_value_total",
                        render: function(data) {
                            return parseFloat(data).toFixed(2);
                        }
                    }
                ],
                scrollX: true, // ✅ Enables horizontal scrolling
                autoWidth: true, // ✅ Enables automatic column sizing
                scrollCollapse: true,
                responsive: false, // ✅ Keep this false with scrollX to avoid conflict
                bDestroy: true,
                paging: false,
                searching: false,
                sorting: false,
                language: {
                    url: datatable_language
                }
            });

        } else if (type == 13) {

            $("#xin_table_invoice_dhlcost").DataTable({
                data: JSON.parse(response),
                columns: [{
                        data: "id",
                        render: function(data, type, row) {
                            return `
                                                        <button type="button" class="btn icon-btn btn-xs btn-success waves-effect waves-light" data-role="editdhlcost_invoice" data-export_id="${row.export_id}" data-id="${data}" title="<?php echo $this->lang->line("edit"); ?>">
                                                            <span class="fas fa-pencil"></span>
                                                        </button>
                                                        
                                                        <button type="button" style="margin-left:5px;" class="btn icon-btn btn-xs btn-delete waves-effect waves-light" data-export_id="${row.export_id}" data-role="deletedhlcost_invoice" data-id="${data}" title="<?php echo $this->lang->line("delete"); ?>">
                                                            <span class="fas fa-trash"></span>
                                                        </button>
                                                    `;
                        }
                    },
                    {
                        data: "invoice_no"
                    },
                    {
                        data: "supplier_name"
                    },
                    {
                        data: "invoice_date"
                    },
                    {
                        data: "sub_total",
                        render: function(data) {
                            return parseFloat(data).toFixed(2);
                        }
                    },
                    {
                        data: "tax_total",
                        render: function(data) {
                            return parseFloat(data).toFixed(2);
                        }
                    },
                    {
                        data: "allowance_total",
                        render: function(data) {
                            return parseFloat(data).toFixed(2);
                        }
                    },
                    {
                        data: "payable_total",
                        render: function(data) {
                            return parseFloat(data).toFixed(2);
                        }
                    },
                    {
                        data: "container_value_total",
                        render: function(data) {
                            return parseFloat(data).toFixed(2);
                        }
                    }
                ],
                scrollX: true, // ✅ Enables horizontal scrolling
                autoWidth: true, // ✅ Enables automatic column sizing
                scrollCollapse: true,
                responsive: false, // ✅ Keep this false with scrollX to avoid conflict
                bDestroy: true,
                paging: false,
                searching: false,
                sorting: false,
                language: {
                    url: datatable_language
                }
            });

        } else if (type == 11) {

            $("#xin_table_invoice_containerloadingcost").DataTable({
                data: JSON.parse(response),
                columns: [{
                        data: "id",
                        render: function(data, type, row) {
                            return `
                                                        <button type="button" class="btn icon-btn btn-xs btn-success waves-effect waves-light" data-role="editcontainerloadingcost_invoice" data-export_id="${row.export_id}" data-id="${data}" title="<?php echo $this->lang->line("edit"); ?>">
                                                            <span class="fas fa-pencil"></span>
                                                        </button>
                                                        
                                                        <button type="button" style="margin-left:5px;" class="btn icon-btn btn-xs btn-delete waves-effect waves-light" data-export_id="${row.export_id}" data-role="deletecontainerloadingcost_invoice" data-id="${data}" title="<?php echo $this->lang->line("delete"); ?>">
                                                            <span class="fas fa-trash"></span>
                                                        </button>
                                                    `;
                        }
                    },
                    {
                        data: "invoice_no"
                    },
                    {
                        data: "supplier_name"
                    },
                    {
                        data: "invoice_date"
                    },
                    {
                        data: "sub_total",
                        render: function(data) {
                            return parseFloat(data).toFixed(2);
                        }
                    },
                    {
                        data: "tax_total",
                        render: function(data) {
                            return parseFloat(data).toFixed(2);
                        }
                    },
                    {
                        data: "allowance_total",
                        render: function(data) {
                            return parseFloat(data).toFixed(2);
                        }
                    },
                    {
                        data: "payable_total",
                        render: function(data) {
                            return parseFloat(data).toFixed(2);
                        }
                    },
                    {
                        data: "container_value_total",
                        render: function(data) {
                            return parseFloat(data).toFixed(2);
                        }
                    }
                ],
                scrollX: true, // ✅ Enables horizontal scrolling
                autoWidth: true, // ✅ Enables automatic column sizing
                scrollCollapse: true,
                responsive: false, // ✅ Keep this false with scrollX to avoid conflict
                bDestroy: true,
                paging: false,
                searching: false,
                sorting: false,
                language: {
                    url: datatable_language
                }
            });

        }
    }

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
                        // $("#lblTotalAmountCustoms").text(jsonResult.result["taxExclusiveAmount"]);
                        // $("#lblTotalTaxAmountCustoms").text(jsonResult.result["taxAmount"]);
                        // $("#lblAllowanceAmountCustoms").text(jsonResult.result["allowanceTotalAmount"]);
                        // $("#lblPayableAmountCustoms").text(jsonResult.result["payableAmount"]);

                        $("#lblTotalAmountCustoms").val(jsonResult.result["taxExclusiveAmountValue"]);
                        $("#lblTotalTaxAmountCustoms").val(jsonResult.result["taxAmountValue"]);
                        $("#lblAllowanceAmountCustoms").val(jsonResult.result["allowanceTotalAmountValue"]);
                        $("#lblPayableAmountCustoms").val(jsonResult.result["payableAmountValue"]);

                        if (jsonResult.result["fileExtension"] == "xml" || jsonResult.result["fileExtension"] == "XML") {
                            $("#divXml").show();
                            $("#divPdf").hide();
                            $("#divContainersCustoms").show();

                            totalPayableAmount = jsonResult.result["payableAmountValue"];
                            uploadPdfFileCustomAgency = jsonResult.result["fileUrl"];
                            fileExtension = jsonResult.result["fileExtension"];
                            supplierId = jsonResult.result["supplierId"];
                            subTotalCustoms = jsonResult.result["taxExclusiveAmountValue"];
                            ivaCustoms = jsonResult.result["taxAmountValue"];
                            retefuenteCustoms = jsonResult.result["allowanceTotalAmountValue"];
                            payableCustoms = jsonResult.result["payableAmountValue"];

                            var containerData = JSON.parse(jsonResult.result["containerValue"]);
                            containerData.forEach(function(container) {
                                var dispatchId = container.dispatchId;
                                var containerValue = container.containerValue;

                                // Find the input with name matching the dispatch ID
                                var input = $("input[name='custom_container_value[" + dispatchId + "]']");
                                if (input.length > 0) {
                                    input.val(parseFloat(containerValue));
                                }
                            });
                        } else if (jsonResult.result["fileExtension"] == "pdf" || jsonResult.result["fileExtension"] == "PDF") {
                            $("#divXml").hide();
                            $("#divPdf").show();
                            $("#divContainersCustoms").show();

                            uploadPdfFileCustomAgency = jsonResult.result["fileUrl"];
                            fileExtension = jsonResult.result["fileExtension"];
                            supplierId = jsonResult.result["supplierId"];

                            var containerData = JSON.parse(jsonResult.result["containerValue"]);
                            containerData.forEach(function(container) {
                                var dispatchId = container.dispatchId;
                                var containerValue = container.containerValue;

                                // Find the input with name matching the dispatch ID
                                var input = $("input[name='custom_container_value[" + dispatchId + "]']");
                                if (input.length > 0) {
                                    input.val(parseFloat(containerValue));
                                }
                            });
                        }

                    } else if (jsonResult.error != '') {
                        toastr.clear();
                        toastr.error(jsonResult.error);
                        $('input[name="csrf_cgrerp"]').val(jsonResult.csrf_hash);
                        $("#fileUploadDoc").val("");

                        $("#lblInvoiceNoCustoms").text("");
                        $("#lblSupplierNameCustoms").text("");
                        $("#lblIssuedDateCustoms").text("");
                        //$("#lblTotalAmountCustoms").text("");
                        // $("#lblTotalTaxAmountCustoms").text("");
                        // $("#lblAllowanceAmountCustoms").text("");
                        // $("#lblPayableAmountCustoms").text("");

                        $("#lblTotalAmountCustoms").val("0");
                        $("#lblTotalTaxAmountCustoms").val("0");
                        $("#lblAllowanceAmountCustoms").val("0");
                        $("#lblPayableAmountCustoms").val("0");

                        $("#divXml").hide();
                        $("#divPdf").show();
                        $("#divContainersCustoms").hide();

                        totalPayableAmount = 0;
                        uploadPdfFileCustomAgency = "";
                        fileExtension = "";
                        supplierId = 0;
                        subTotalCustoms = 0;
                        ivaCustoms = 0;
                        retefuenteCustoms = 0;
                        payableCustoms = 0;

                    } else if (jsonResult.warning != '') {
                        toastr.clear();
                        toastr.warning(jsonResult.warning);
                        $('input[name="csrf_cgrerp"]').val(jsonResult.csrf_hash);

                        $("#divXml").hide();
                        $("#divPdf").show();
                        $("#divContainersCustoms").hide();

                        $("#fileUploadDoc").val("");

                        $("#lblInvoiceNoCustoms").text("");
                        $("#lblSupplierNameCustoms").text("");
                        $("#lblIssuedDateCustoms").text("");
                        //$("#lblTotalAmountCustoms").text("");
                        // $("#lblTotalTaxAmountCustoms").text("");
                        // $("#lblAllowanceAmountCustoms").text("");
                        // $("#lblPayableAmountCustoms").text("");

                        $("#lblTotalAmountCustoms").val("0");
                        $("#lblTotalTaxAmountCustoms").val("0");
                        $("#lblAllowanceAmountCustoms").val("0");
                        $("#lblPayableAmountCustoms").val("0");

                        totalPayableAmount = 0;
                        uploadPdfFileCustomAgency = "";
                        fileExtension = "";
                        supplierId = 0;
                        subTotalCustoms = 0;
                        ivaCustoms = 0;
                        retefuenteCustoms = 0;
                        payableCustoms = 0;
                    } else {
                        toastr.clear();
                    }
                }
            });
        } else {
            toastr.clear();
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
                        // $("#lblTotalAmountITR").text(jsonResult.result["taxExclusiveAmount"]);
                        // $("#lblTotalTaxAmountITR").text(jsonResult.result["taxAmount"]);
                        // $("#lblAllowanceAmountITR").text(jsonResult.result["allowanceTotalAmount"]);
                        // $("#lblPayableAmountITR").text(jsonResult.result["payableAmount"]);

                        $("#lblTotalAmountITR").val(jsonResult.result["taxExclusiveAmountValue"]);
                        $("#lblTotalTaxAmountITR").val(jsonResult.result["taxAmountValue"]);
                        $("#lblAllowanceAmountITR").val(jsonResult.result["allowanceTotalAmountValue"]);
                        $("#lblPayableAmountITR").val(jsonResult.result["payableAmountValue"]);

                        if (jsonResult.result["fileExtension"] == "xml" || jsonResult.result["fileExtension"] == "XML") {
                            $("#divXmlITR").show();
                            $("#divPdfITR").hide();
                            $("#divContainersITR").show();

                            $("#fileUploadDoc_ITR").val("");

                            totalPayableAmount = jsonResult.result["payableAmountValue"];
                            uploadPdfFileCustomAgency = jsonResult.result["fileUrl"];
                            fileExtension = jsonResult.result["fileExtension"];
                            supplierId = jsonResult.result["supplierId"];
                            subTotalCustoms = jsonResult.result["taxExclusiveAmountValue"];
                            ivaCustoms = jsonResult.result["taxAmountValue"];
                            retefuenteCustoms = jsonResult.result["allowanceTotalAmountValue"];
                            payableCustoms = jsonResult.result["payableAmountValue"];

                            var containerData = JSON.parse(jsonResult.result["containerValue"]);
                            containerData.forEach(function(container) {
                                var dispatchId = container.dispatchId;
                                var containerValue = container.containerValue;

                                // Find the input with name matching the dispatch ID
                                var input = $("input[name='itr_container_value[" + dispatchId + "]']");
                                if (input.length > 0) {
                                    input.val(parseFloat(containerValue));
                                }
                            });
                        } else if (jsonResult.result["fileExtension"] == "pdf" || jsonResult.result["fileExtension"] == "PDF") {
                            $("#divXmlITR").hide();
                            $("#divPdfITR").show();
                            $("#divContainersITR").show();

                            uploadPdfFileCustomAgency = jsonResult.result["fileUrl"];
                            fileExtension = jsonResult.result["fileExtension"];
                            supplierId = jsonResult.result["supplierId"];

                            var containerData = JSON.parse(jsonResult.result["containerValue"]);
                            containerData.forEach(function(container) {
                                var dispatchId = container.dispatchId;
                                var containerValue = container.containerValue;

                                // Find the input with name matching the dispatch ID
                                var input = $("input[name='itr_container_value[" + dispatchId + "]']");
                                if (input.length > 0) {
                                    input.val(parseFloat(containerValue));
                                }
                            });
                        }

                    } else if (jsonResult.error != '') {
                        toastr.clear();
                        toastr.error(jsonResult.error);
                        $('input[name="csrf_cgrerp"]').val(jsonResult.csrf_hash);

                        $("#lblInvoiceNoITR").text("");
                        $("#lblSupplierNameITR").text("");
                        $("#lblIssuedDateITR").text("");
                        $("#lblTotalAmountITR").val("0");
                        $("#lblTotalTaxAmountITR").val("0");
                        $("#lblAllowanceAmountITR").val("0");
                        $("#lblPayableAmountITR").val("0");

                        $("#divXmlITR").hide();
                        $("#divPdfITR").show();
                        $("#divContainersITR").hide();

                        totalPayableAmount = 0;
                        uploadPdfFileCustomAgency = "";
                        fileExtension = "";
                        supplierId = 0;
                        subTotalCustoms = 0;
                        ivaCustoms = 0;
                        retefuenteCustoms = 0;
                        payableCustoms = 0;
                    } else if (jsonResult.warning != '') {
                        toastr.clear();
                        toastr.warning(jsonResult.warning);
                        $('input[name="csrf_cgrerp"]').val(jsonResult.csrf_hash);

                        $("#divXmlITR").hide();
                        $("#divPdfITR").show();
                        $("#divContainersCustomsITR").hide();

                        $("#fileUploadDoc_ITR").val("");

                        $("#lblInvoiceNoITR").text("");
                        $("#lblSupplierNameITR").text("");
                        $("#lblIssuedDateITR").text("");
                        $("#lblTotalAmountITR").val("0");
                        $("#lblTotalTaxAmountITR").val("0");
                        $("#lblAllowanceAmountITR").val("0");
                        $("#lblPayableAmountITR").val("0");

                        totalPayableAmount = 0;
                        uploadPdfFileCustomAgency = "";
                        fileExtension = "";
                        supplierId = 0;
                        subTotalCustoms = 0;
                        ivaCustoms = 0;
                        retefuenteCustoms = 0;
                        payableCustoms = 0;
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
                        // $("#lblTotalAmountPort").text(jsonResult.result["taxExclusiveAmount"]);
                        // $("#lblTotalTaxAmountPort").text(jsonResult.result["taxAmount"]);
                        // $("#lblAllowanceAmountPort").text(jsonResult.result["allowanceTotalAmount"]);
                        // $("#lblPayableAmountPort").text(jsonResult.result["payableAmount"]);
                        $("#lblTotalAmountPort").val(jsonResult.result["taxExclusiveAmountValue"]);
                        $("#lblTotalTaxAmountPort").val(jsonResult.result["taxAmountValue"]);
                        $("#lblAllowanceAmountPort").val(jsonResult.result["allowanceTotalAmountValue"]);
                        $("#lblPayableAmountPort").val(jsonResult.result["payableAmountValue"]);

                        if (jsonResult.result["fileExtension"] == "xml" || jsonResult.result["fileExtension"] == "XML") {
                            $("#divXmlPort").show();
                            $("#divPdfPort").hide();
                            $("#divContainersPort").show();

                            $("#fileUploadDoc_Port").val("");

                            totalPayableAmount = jsonResult.result["payableAmountValue"];
                            uploadPdfFileCustomAgency = jsonResult.result["fileUrl"];
                            fileExtension = jsonResult.result["fileExtension"];
                            supplierId = jsonResult.result["supplierId"];
                            subTotalCustoms = jsonResult.result["taxExclusiveAmountValue"];
                            ivaCustoms = jsonResult.result["taxAmountValue"];
                            retefuenteCustoms = jsonResult.result["allowanceTotalAmountValue"];
                            payableCustoms = jsonResult.result["payableAmountValue"];

                            var containerData = JSON.parse(jsonResult.result["containerValue"]);
                            containerData.forEach(function(container) {
                                var dispatchId = container.dispatchId;
                                var containerValue = container.containerValue;

                                // Find the input with name matching the dispatch ID
                                var input = $("input[name='port_container_value[" + dispatchId + "]']");
                                if (input.length > 0) {
                                    input.val(parseFloat(containerValue));
                                }
                            });
                        } else if (jsonResult.result["fileExtension"] == "pdf" || jsonResult.result["fileExtension"] == "PDF") {
                            $("#divXmlPort").hide();
                            $("#divPdfPort").show();
                            $("#divContainersPort").show();

                            uploadPdfFileCustomAgency = jsonResult.result["fileUrl"];
                            fileExtension = jsonResult.result["fileExtension"];
                            supplierId = jsonResult.result["supplierId"];

                            var containerData = JSON.parse(jsonResult.result["containerValue"]);
                            containerData.forEach(function(container) {
                                var dispatchId = container.dispatchId;
                                var containerValue = container.containerValue;

                                // Find the input with name matching the dispatch ID
                                var input = $("input[name='port_container_value[" + dispatchId + "]']");
                                if (input.length > 0) {
                                    input.val(parseFloat(containerValue));
                                }
                            });
                        }

                    } else if (jsonResult.error != '') {
                        toastr.clear();
                        toastr.error(jsonResult.error);
                        $('input[name="csrf_cgrerp"]').val(jsonResult.csrf_hash);

                        $("#lblInvoiceNoPort").text("");
                        $("#lblSupplierNamePort").text("");
                        $("#lblIssuedDatePort").text("");
                        $("#lblTotalAmountPort").val("0");
                        $("#lblTotalTaxAmountPort").val("0");
                        $("#lblAllowanceAmountPort").val("0");
                        $("#lblPayableAmountPort").val("0");

                        $("#divXmlPort").hide();
                        $("#divPdfPort").show();
                        $("#divContainersPort").hide();

                        totalPayableAmount = 0;
                        uploadPdfFileCustomAgency = "";
                        fileExtension = "";
                        supplierId = 0;
                        subTotalCustoms = 0;
                        ivaCustoms = 0;
                        retefuenteCustoms = 0;
                        payableCustoms = 0;
                    } else if (jsonResult.warning != '') {
                        toastr.clear();
                        toastr.warning(jsonResult.warning);
                        $('input[name="csrf_cgrerp"]').val(jsonResult.csrf_hash);

                        $("#divXmlPort").hide();
                        $("#divPdfPort").show();
                        $("#divContainersCustomsPort").hide();

                        $("#fileUploadDoc_Port").val("");

                        $("#lblInvoiceNoPort").text("");
                        $("#lblSupplierNamePort").text("");
                        $("#lblIssuedDatePort").text("");
                        $("#lblTotalAmountPort").val("0");
                        $("#lblTotalTaxAmountPort").val("0");
                        $("#lblAllowanceAmountPort").val("0");
                        $("#lblPayableAmountPort").val("0");

                        totalPayableAmount = 0;
                        uploadPdfFileCustomAgency = "";
                        fileExtension = "";
                        supplierId = 0;
                        subTotalCustoms = 0;
                        ivaCustoms = 0;
                        retefuenteCustoms = 0;
                        payableCustoms = 0;
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
                        // $("#lblTotalAmountShipping").text(jsonResult.result["taxExclusiveAmount"]);
                        // $("#lblTotalTaxAmountShipping").text(jsonResult.result["taxAmount"]);
                        // $("#lblAllowanceAmountShipping").text(jsonResult.result["allowanceTotalAmount"]);
                        // $("#lblPayableAmountShipping").text(jsonResult.result["payableAmount"]);
                        $("#lblTotalAmountShipping").val(jsonResult.result["taxExclusiveAmountValue"]);
                        $("#lblTotalTaxAmountShipping").val(jsonResult.result["taxAmountValue"]);
                        $("#lblAllowanceAmountShipping").val(jsonResult.result["allowanceTotalAmountValue"]);
                        $("#lblPayableAmountShipping").val(jsonResult.result["payableAmountValue"]);

                        if (jsonResult.result["fileExtension"] == "xml" || jsonResult.result["fileExtension"] == "XML") {
                            $("#divXmlShipping").show();
                            $("#divPdfShipping").hide();
                            $("#divContainersShipping").show();

                            $("#fileUploadDoc_Shipping").val("");

                            totalPayableAmount = jsonResult.result["payableAmountValue"];
                            uploadPdfFileCustomAgency = jsonResult.result["fileUrl"];
                            fileExtension = jsonResult.result["fileExtension"];
                            supplierId = jsonResult.result["supplierId"];
                            subTotalCustoms = jsonResult.result["taxExclusiveAmountValue"];
                            ivaCustoms = jsonResult.result["taxAmountValue"];
                            retefuenteCustoms = jsonResult.result["allowanceTotalAmountValue"];
                            payableCustoms = jsonResult.result["payableAmountValue"];

                            var containerData = JSON.parse(jsonResult.result["containerValue"]);
                            containerData.forEach(function(container) {
                                var dispatchId = container.dispatchId;
                                var containerValue = container.containerValue;

                                // Find the input with name matching the dispatch ID
                                var input = $("input[name='shipping_container_value[" + dispatchId + "]']");
                                if (input.length > 0) {
                                    input.val(parseFloat(containerValue));
                                }
                            });
                        } else if (jsonResult.result["fileExtension"] == "pdf" || jsonResult.result["fileExtension"] == "PDF") {
                            $("#divXmlShipping").hide();
                            $("#divPdfShipping").show();
                            $("#divContainersShipping").show();

                            uploadPdfFileCustomAgency = jsonResult.result["fileUrl"];
                            fileExtension = jsonResult.result["fileExtension"];
                            supplierId = jsonResult.result["supplierId"];

                            var containerData = JSON.parse(jsonResult.result["containerValue"]);
                            containerData.forEach(function(container) {
                                var dispatchId = container.dispatchId;
                                var containerValue = container.containerValue;

                                // Find the input with name matching the dispatch ID
                                var input = $("input[name='shipping_container_value[" + dispatchId + "]']");
                                if (input.length > 0) {
                                    input.val(parseFloat(containerValue));
                                }
                            });
                        }

                    } else if (jsonResult.error != '') {
                        toastr.clear();
                        toastr.error(jsonResult.error);
                        $('input[name="csrf_cgrerp"]').val(jsonResult.csrf_hash);

                        $("#divXmlShipping").hide();
                        $("#divPdfShipping").show();
                        $("#divContainersCustomsShipping").hide();

                        $("#fileUploadDoc_Shipping").val("");

                        $("#lblInvoiceNoShipping").text("");
                        $("#lblSupplierNameShipping").text("");
                        $("#lblIssuedDateShipping").text("");
                        $("#lblTotalAmountShipping").val("0");
                        $("#lblTotalTaxAmountShipping").val("0");
                        $("#lblAllowanceAmountShipping").val("0");
                        $("#lblPayableAmountShipping").val("0");

                        totalPayableAmount = 0;
                        uploadPdfFileCustomAgency = "";
                        fileExtension = "";
                        supplierId = 0;
                        subTotalCustoms = 0;
                        ivaCustoms = 0;
                        retefuenteCustoms = 0;
                        payableCustoms = 0;
                    } else if (jsonResult.warning != '') {
                        toastr.clear();
                        toastr.warning(jsonResult.warning);
                        $('input[name="csrf_cgrerp"]').val(jsonResult.csrf_hash);

                        $("#lblInvoiceNoShipping").text("");
                        $("#lblSupplierNameShipping").text("");
                        $("#lblIssuedDateShipping").text("");
                        $("#lblTotalAmountShipping").val("0");
                        $("#lblTotalTaxAmountShipping").val("0");
                        $("#lblAllowanceAmountShipping").val("0");
                        $("#lblPayableAmountShipping").val("0");

                        $("#divXmlShipping").hide();
                        $("#divPdfShipping").show();
                        $("#divContainersShipping").hide();

                        totalPayableAmount = 0;
                        uploadPdfFileCustomAgency = "";
                        fileExtension = "";
                        supplierId = 0;
                        subTotalCustoms = 0;
                        ivaCustoms = 0;
                        retefuenteCustoms = 0;
                        payableCustoms = 0;
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
                        // $("#lblTotalAmountFumigation").text(jsonResult.result["taxExclusiveAmount"]);
                        // $("#lblTotalTaxAmountFumigation").text(jsonResult.result["taxAmount"]);
                        // $("#lblAllowanceAmountFumigation").text(jsonResult.result["allowanceTotalAmount"]);
                        // $("#lblPayableAmountFumigation").text(jsonResult.result["payableAmount"]);
                        $("#lblTotalAmountFumigation").val(jsonResult.result["taxExclusiveAmountValue"]);
                        $("#lblTotalTaxAmountFumigation").val(jsonResult.result["taxAmountValue"]);
                        $("#lblAllowanceAmountFumigation").val(jsonResult.result["allowanceTotalAmountValue"]);
                        $("#lblPayableAmountFumigation").val(jsonResult.result["payableAmountValue"]);

                        if (jsonResult.result["fileExtension"] == "xml" || jsonResult.result["fileExtension"] == "XML") {
                            $("#divXmlFumigation").show();
                            $("#divPdfFumigation").hide();
                            $("#divContainersFumigation").show();

                            $("#fileUploadDoc_Fumigation").val("");

                            totalPayableAmount = jsonResult.result["payableAmountValue"];
                            uploadPdfFileCustomAgency = jsonResult.result["fileUrl"];
                            fileExtension = jsonResult.result["fileExtension"];
                            supplierId = jsonResult.result["supplierId"];
                            subTotalCustoms = jsonResult.result["taxExclusiveAmountValue"];
                            ivaCustoms = jsonResult.result["taxAmountValue"];
                            retefuenteCustoms = jsonResult.result["allowanceTotalAmountValue"];
                            payableCustoms = jsonResult.result["payableAmountValue"];

                            var containerData = JSON.parse(jsonResult.result["containerValue"]);
                            containerData.forEach(function(container) {
                                var dispatchId = container.dispatchId;
                                var containerValue = container.containerValue;

                                // Find the input with name matching the dispatch ID
                                var input = $("input[name='fumigation_container_value[" + dispatchId + "]']");
                                if (input.length > 0) {
                                    input.val(parseFloat(containerValue));
                                }
                            });
                        } else if (jsonResult.result["fileExtension"] == "pdf" || jsonResult.result["fileExtension"] == "PDF") {
                            $("#divXmlFumigation").hide();
                            $("#divPdfFumigation").show();
                            $("#divContainersFumigation").show();

                            uploadPdfFileCustomAgency = jsonResult.result["fileUrl"];
                            fileExtension = jsonResult.result["fileExtension"];
                            supplierId = jsonResult.result["supplierId"];

                            var containerData = JSON.parse(jsonResult.result["containerValue"]);
                            containerData.forEach(function(container) {
                                var dispatchId = container.dispatchId;
                                var containerValue = container.containerValue;

                                // Find the input with name matching the dispatch ID
                                var input = $("input[name='fumigation_container_value[" + dispatchId + "]']");
                                if (input.length > 0) {
                                    input.val(parseFloat(containerValue));
                                }
                            });
                        }

                    } else if (jsonResult.error != '') {
                        toastr.clear();
                        toastr.error(jsonResult.error);
                        $('input[name="csrf_cgrerp"]').val(jsonResult.csrf_hash);

                        $("#divXmlFumigation").hide();
                        $("#divPdfFumigation").show();
                        $("#divContainersFumigation").hide();

                        $("#fileUploadDoc_Fumigation").val("");

                        $("#lblInvoiceNoFumigation").text("");
                        $("#lblSupplierNameFumigation").text("");
                        $("#lblIssuedDateFumigation").text("");
                        $("#lblTotalAmountFumigation").val("0");
                        $("#lblTotalTaxAmountFumigation").val("0");
                        $("#lblAllowanceAmountFumigation").val("0");
                        $("#lblPayableAmountFumigation").val("0");

                        totalPayableAmount = 0;
                        uploadPdfFileCustomAgency = "";
                        fileExtension = "";
                        supplierId = 0;
                        subTotalCustoms = 0;
                        ivaCustoms = 0;
                        retefuenteCustoms = 0;
                        payableCustoms = 0;
                    } else if (jsonResult.warning != '') {
                        toastr.clear();
                        toastr.warning(jsonResult.warning);
                        $('input[name="csrf_cgrerp"]').val(jsonResult.csrf_hash);

                        $("#lblInvoiceNoFumigation").text("");
                        $("#lblSupplierNameFumigation").text("");
                        $("#lblIssuedDateFumigation").text("");
                        $("#lblTotalAmountFumigation").val("0");
                        $("#lblTotalTaxAmountFumigation").val("0");
                        $("#lblAllowanceAmountFumigation").val("0");
                        $("#lblPayableAmountFumigation").val("0");

                        $("#divXmlFumigation").hide();
                        $("#divPdfFumigation").show();
                        $("#divContainersFumigation").hide();

                        totalPayableAmount = 0;
                        uploadPdfFileCustomAgency = "";
                        fileExtension = "";
                        supplierId = 0;
                        subTotalCustoms = 0;
                        ivaCustoms = 0;
                        retefuenteCustoms = 0;
                        payableCustoms = 0;
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

    var loadFileCoteros = function(event) {
        $("#error-selectdoccoteros").hide();
        event.preventDefault();

        var files = $('#fileUploadDoc_Coteros')[0].files[0];
        if (files != null && files != "") {

            $('#loading').show();
            farm_data_array = [];
            var fd = new FormData();

            fd.append('fileUploadDoc', files);
            fd.append('exportId', $("#hdnExportId").val());
            fd.append('originId', $("#hdnOriginId").val());
            fd.append('saNumber', $("#hdnSaNumber").val());
            fd.append('exportType', 6);
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

                        $("#lblInvoiceNoCoteros").text(jsonResult.result["documentId"]);
                        $("#lblSupplierNameCoteros").text(jsonResult.result["registrationName"] + " --- " + jsonResult.result["companyId"]);
                        $("#lblIssuedDateCoteros").text(jsonResult.result["issueDate"]);
                        // $("#lblTotalAmountCoteros").text(jsonResult.result["taxExclusiveAmount"]);
                        // $("#lblTotalTaxAmountCoteros").text(jsonResult.result["taxAmount"]);
                        // $("#lblAllowanceAmountCoteros").text(jsonResult.result["allowanceTotalAmount"]);
                        // $("#lblPayableAmountCoteros").text(jsonResult.result["payableAmount"]);
                        $("#lblTotalAmountCoteros").val(jsonResult.result["taxExclusiveAmountValue"]);
                        $("#lblTotalTaxAmountCoteros").val(jsonResult.result["taxAmountValue"]);
                        $("#lblAllowanceAmountCoteros").val(jsonResult.result["allowanceTotalAmountValue"]);
                        $("#lblPayableAmountCoteros").val(jsonResult.result["payableAmountValue"]);

                        if (jsonResult.result["fileExtension"] == "xml" || jsonResult.result["fileExtension"] == "XML") {
                            $("#divXmlCoteros").show();
                            $("#divPdfCoteros").hide();
                            $("#divContainersCoteros").show();

                            $("#fileUploadDoc_Coteros").val("");

                            totalPayableAmount = jsonResult.result["payableAmountValue"];
                            uploadPdfFileCustomAgency = jsonResult.result["fileUrl"];
                            fileExtension = jsonResult.result["fileExtension"];
                            supplierId = jsonResult.result["supplierId"];
                            subTotalCustoms = jsonResult.result["taxExclusiveAmountValue"];
                            ivaCustoms = jsonResult.result["taxAmountValue"];
                            retefuenteCustoms = jsonResult.result["allowanceTotalAmountValue"];
                            payableCustoms = jsonResult.result["payableAmountValue"];

                            var containerData = JSON.parse(jsonResult.result["containerValue"]);
                            containerData.forEach(function(container) {
                                var dispatchId = container.dispatchId;
                                var containerValue = container.containerValue;

                                // Find the input with name matching the dispatch ID
                                var input = $("input[name='coteros_container_value[" + dispatchId + "]']");
                                if (input.length > 0) {
                                    input.val(parseFloat(containerValue));
                                }
                            });
                        } else if (jsonResult.result["fileExtension"] == "pdf" || jsonResult.result["fileExtension"] == "PDF") {
                            $("#divXmlCoteros").hide();
                            $("#divPdfCoteros").show();
                            $("#divContainersCoteros").show();

                            uploadPdfFileCustomAgency = jsonResult.result["fileUrl"];
                            fileExtension = jsonResult.result["fileExtension"];
                            supplierId = jsonResult.result["supplierId"];

                            var containerData = JSON.parse(jsonResult.result["containerValue"]);
                            containerData.forEach(function(container) {
                                var dispatchId = container.dispatchId;
                                var containerValue = container.containerValue;

                                // Find the input with name matching the dispatch ID
                                var input = $("input[name='coteros_container_value[" + dispatchId + "]']");
                                if (input.length > 0) {
                                    input.val(parseFloat(containerValue));
                                }
                            });
                        }

                    } else if (jsonResult.error != '') {
                        toastr.clear();
                        toastr.error(jsonResult.error);
                        $('input[name="csrf_cgrerp"]').val(jsonResult.csrf_hash);

                        $("#divXmlCoteros").hide();
                        $("#divPdfCoteros").show();
                        $("#divContainersCoteros").hide();

                        $("#fileUploadDoc_Coteros").val("");

                        $("#lblInvoiceNoCoteros").text("");
                        $("#lblSupplierNameCoteros").text("");
                        $("#lblIssuedDateCoteros").text("");
                        $("#lblTotalAmountCoteros").val("0");
                        $("#lblTotalTaxAmountCoteros").val("0");
                        $("#lblAllowanceAmountCoteros").val("0");
                        $("#lblPayableAmountCoteros").val("0");

                        totalPayableAmount = 0;
                        uploadPdfFileCustomAgency = "";
                        fileExtension = "";
                        supplierId = 0;
                        subTotalCustoms = 0;
                        ivaCustoms = 0;
                        retefuenteCustoms = 0;
                        payableCustoms = 0;
                    } else if (jsonResult.warning != '') {
                        toastr.clear();
                        toastr.warning(jsonResult.warning);
                        $('input[name="csrf_cgrerp"]').val(jsonResult.csrf_hash);

                        $("#lblInvoiceNoCoteros").text("");
                        $("#lblSupplierNameCoteros").text("");
                        $("#lblIssuedDateCoteros").text("");
                        $("#lblTotalAmountCoteros").val("0");
                        $("#lblTotalTaxAmountCoteros").val("0");
                        $("#lblAllowanceAmountCoteros").val("0");
                        $("#lblPayableAmountCoteros").val("0");

                        $("#divXmlCoteros").hide();
                        $("#divPdfCoteros").show();
                        $("#divContainersCoteros").hide();

                        totalPayableAmount = 0;
                        uploadPdfFileCustomAgency = "";
                        fileExtension = "";
                        supplierId = 0;
                        subTotalCustoms = 0;
                        ivaCustoms = 0;
                        retefuenteCustoms = 0;
                        payableCustoms = 0;
                    } else {
                        toastr.clear();
                    }
                }
            });
        } else {
            toastr.clear();
            //toastr.error(common_error);
            $("#fileUploadDoc_Coteros").val("");
        }
    };

    var loadFilePhyto = function(event) {
        $("#error-selectdocphyto").hide();
        event.preventDefault();

        var files = $('#fileUploadDoc_Phyto')[0].files[0];
        if (files != null && files != "") {

            $('#loading').show();
            farm_data_array = [];
            var fd = new FormData();

            fd.append('fileUploadDoc', files);
            fd.append('exportId', $("#hdnExportId").val());
            fd.append('originId', $("#hdnOriginId").val());
            fd.append('saNumber', $("#hdnSaNumber").val());
            fd.append('exportType', 5);
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

                        $("#lblInvoiceNoPhyto").text(jsonResult.result["documentId"]);
                        $("#lblSupplierNamePhyto").text(jsonResult.result["registrationName"] + " --- " + jsonResult.result["companyId"]);
                        $("#lblIssuedDatePhyto").text(jsonResult.result["issueDate"]);
                        // $("#lblTotalAmountPhyto").text(jsonResult.result["taxExclusiveAmount"]);
                        // $("#lblTotalTaxAmountPhyto").text(jsonResult.result["taxAmount"]);
                        // $("#lblAllowanceAmountPhyto").text(jsonResult.result["allowanceTotalAmount"]);
                        // $("#lblPayableAmountPhyto").text(jsonResult.result["payableAmount"]);
                        $("#lblTotalAmountPhyto").val(jsonResult.result["taxExclusiveAmountValue"]);
                        $("#lblTotalTaxAmountPhyto").val(jsonResult.result["taxAmountValue"]);
                        $("#lblAllowanceAmountPhyto").val(jsonResult.result["allowanceTotalAmountValue"]);
                        $("#lblPayableAmountPhyto").val(jsonResult.result["payableAmountValue"]);

                        if (jsonResult.result["fileExtension"] == "xml" || jsonResult.result["fileExtension"] == "XML") {
                            $("#divXmlPhyto").show();
                            $("#divPdfPhyto").hide();
                            $("#divContainersPhyto").show();

                            $("#fileUploadDoc_Phyto").val("");

                            totalPayableAmount = jsonResult.result["payableAmountValue"];
                            uploadPdfFileCustomAgency = jsonResult.result["fileUrl"];
                            fileExtension = jsonResult.result["fileExtension"];
                            supplierId = jsonResult.result["supplierId"];
                            subTotalCustoms = jsonResult.result["taxExclusiveAmountValue"];
                            ivaCustoms = jsonResult.result["taxAmountValue"];
                            retefuenteCustoms = jsonResult.result["allowanceTotalAmountValue"];
                            payableCustoms = jsonResult.result["payableAmountValue"];

                            var containerData = JSON.parse(jsonResult.result["containerValue"]);
                            containerData.forEach(function(container) {
                                var dispatchId = container.dispatchId;
                                var containerValue = container.containerValue;

                                // Find the input with name matching the dispatch ID
                                var input = $("input[name='phyto_container_value[" + dispatchId + "]']");
                                if (input.length > 0) {
                                    input.val(parseFloat(containerValue));
                                }
                            });

                            containerValuePhytoArr.push(jsonResult.result["taxExclusiveAmountArray"]);
                            containerValueArr = JSON.parse(jsonResult.result["containerValue"]);
                            totalContainers = jsonResult.result["totalContainers"];
                            containerValuePhytoArr = containerValuePhytoArr.flat();
                        } else if (jsonResult.result["fileExtension"] == "pdf" || jsonResult.result["fileExtension"] == "PDF") {
                            $("#divXmlPhyto").hide();
                            $("#divPdfPhyto").show();
                            $("#divContainersPhyto").show();

                            uploadPdfFileCustomAgency = jsonResult.result["fileUrl"];
                            fileExtension = jsonResult.result["fileExtension"];
                            supplierId = jsonResult.result["supplierId"];

                            var containerData = JSON.parse(jsonResult.result["containerValue"]);
                            containerData.forEach(function(container) {
                                var dispatchId = container.dispatchId;
                                var containerValue = container.containerValue;

                                // Find the input with name matching the dispatch ID
                                var input = $("input[name='phyto_container_value[" + dispatchId + "]']");
                                if (input.length > 0) {
                                    input.val(parseFloat(containerValue));
                                }
                            });
                        }

                    } else if (jsonResult.error != '') {
                        toastr.clear();
                        toastr.error(jsonResult.error);
                        $('input[name="csrf_cgrerp"]').val(jsonResult.csrf_hash);

                        $("#divXmlPhyto").hide();
                        $("#divPdfPhyto").show();
                        $("#divContainersPhyto").hide();

                        $("#fileUploadDoc_Phyto").val("");

                        $("#lblInvoiceNoPhyto").text("");
                        $("#lblSupplierNamePhyto").text("");
                        $("#lblIssuedDatePhyto").text("");
                        $("#lblTotalAmountPhyto").val("0");
                        $("#lblTotalTaxAmountPhyto").val("0");
                        $("#lblAllowanceAmountPhyto").val("0");
                        $("#lblPayableAmountPhyto").val("0");

                        totalPayableAmount = 0;
                        uploadPdfFileCustomAgency = "";
                        fileExtension = "";
                        supplierId = 0;
                        subTotalCustoms = 0;
                        ivaCustoms = 0;
                        retefuenteCustoms = 0;
                        payableCustoms = 0;
                    } else if (jsonResult.warning != '') {
                        toastr.clear();
                        toastr.warning(jsonResult.warning);
                        $('input[name="csrf_cgrerp"]').val(jsonResult.csrf_hash);

                        $("#lblInvoiceNoPhyto").text("");
                        $("#lblSupplierNamePhyto").text("");
                        $("#lblIssuedDatePhyto").text("");
                        $("#lblTotalAmountPhyto").val("0");
                        $("#lblTotalTaxAmountPhyto").val("0");
                        $("#lblAllowanceAmountPhyto").val("0");
                        $("#lblPayableAmountPhyto").val("0");

                        $("#divXmlPhyto").hide();
                        $("#divPdfPhyto").show();
                        $("#divContainersPhyto").hide();

                        totalPayableAmount = 0;
                        uploadPdfFileCustomAgency = "";
                        fileExtension = "";
                        supplierId = 0;
                        subTotalCustoms = 0;
                        ivaCustoms = 0;
                        retefuenteCustoms = 0;
                        payableCustoms = 0;
                    } else {
                        toastr.clear();
                    }
                }
            });
        } else {
            toastr.clear();
            //toastr.error(common_error);
            $("#fileUploadDoc_Phyto").val("");
        }
    };

    var loadFileIncentives = function(event) {
        $("#error-selectdocincentives").hide();
        event.preventDefault();

        var files = $('#fileUploadDoc_Incentives')[0].files[0];
        if (files != null && files != "") {

            $('#loading').show();
            farm_data_array = [];
            var fd = new FormData();

            fd.append('fileUploadDoc', files);
            fd.append('exportId', $("#hdnExportId").val());
            fd.append('originId', $("#hdnOriginId").val());
            fd.append('saNumber', $("#hdnSaNumber").val());
            fd.append('exportType', 7);
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

                        $("#lblInvoiceNoIncentives").text(jsonResult.result["documentId"]);
                        $("#lblSupplierNameIncentives").text(jsonResult.result["registrationName"] + " --- " + jsonResult.result["companyId"]);
                        $("#lblIssuedDateIncentives").text(jsonResult.result["issueDate"]);
                        // $("#lblTotalAmountIncentives").text(jsonResult.result["taxExclusiveAmount"]);
                        // $("#lblTotalTaxAmountIncentives").text(jsonResult.result["taxAmount"]);
                        // $("#lblAllowanceAmountIncentives").text(jsonResult.result["allowanceTotalAmount"]);
                        // $("#lblPayableAmountIncentives").text(jsonResult.result["payableAmount"]);
                        $("#lblTotalAmountIncentives").val(jsonResult.result["taxExclusiveAmountValue"]);
                        $("#lblTotalTaxAmountIncentives").val(jsonResult.result["taxAmountValue"]);
                        $("#lblAllowanceAmountIncentives").val(jsonResult.result["allowanceTotalAmountValue"]);
                        $("#lblPayableAmountIncentives").val(jsonResult.result["payableAmountValue"]);

                        if (jsonResult.result["fileExtension"] == "xml" || jsonResult.result["fileExtension"] == "XML") {
                            $("#divXmlIncentives").show();
                            $("#divPdfIncentives").hide();
                            $("#divContainersIncentives").show();

                            $("#fileUploadDoc_Incentives").val("");

                            totalPayableAmount = jsonResult.result["payableAmountValue"];
                            uploadPdfFileCustomAgency = jsonResult.result["fileUrl"];
                            fileExtension = jsonResult.result["fileExtension"];
                            supplierId = jsonResult.result["supplierId"];
                            subTotalCustoms = jsonResult.result["taxExclusiveAmountValue"];
                            ivaCustoms = jsonResult.result["taxAmountValue"];
                            retefuenteCustoms = jsonResult.result["allowanceTotalAmountValue"];
                            payableCustoms = jsonResult.result["payableAmountValue"];

                            var containerData = JSON.parse(jsonResult.result["containerValue"]);
                            containerData.forEach(function(container) {
                                var dispatchId = container.dispatchId;
                                var containerValue = container.containerValue;

                                // Find the input with name matching the dispatch ID
                                var input = $("input[name='incentives_container_value[" + dispatchId + "]']");
                                if (input.length > 0) {
                                    input.val(parseFloat(containerValue));
                                }
                            });
                        } else if (jsonResult.result["fileExtension"] == "pdf" || jsonResult.result["fileExtension"] == "PDF") {
                            $("#divXmlIncentives").hide();
                            $("#divPdfIncentives").show();
                            $("#divContainersIncentives").show();

                            uploadPdfFileCustomAgency = jsonResult.result["fileUrl"];
                            fileExtension = jsonResult.result["fileExtension"];
                            supplierId = jsonResult.result["supplierId"];

                            var containerData = JSON.parse(jsonResult.result["containerValue"]);
                            containerData.forEach(function(container) {
                                var dispatchId = container.dispatchId;
                                var containerValue = container.containerValue;

                                // Find the input with name matching the dispatch ID
                                var input = $("input[name='incentives_container_value[" + dispatchId + "]']");
                                if (input.length > 0) {
                                    input.val(parseFloat(containerValue));
                                }
                            });
                        }

                    } else if (jsonResult.error != '') {
                        toastr.clear();
                        toastr.error(jsonResult.error);
                        $('input[name="csrf_cgrerp"]').val(jsonResult.csrf_hash);

                        $("#divXmlIncentives").hide();
                        $("#divPdfIncentives").show();
                        $("#divContainersIncentives").hide();

                        $("#fileUploadDoc_Incentives").val("");

                        $("#lblInvoiceNoIncentives").text("");
                        $("#lblSupplierNameIncentives").text("");
                        $("#lblIssuedDateIncentives").text("");
                        $("#lblTotalAmountIncentives").val("0");
                        $("#lblTotalTaxAmountIncentives").val("0");
                        $("#lblAllowanceAmountIncentives").val("0");
                        $("#lblPayableAmountIncentives").val("0");

                        totalPayableAmount = 0;
                        uploadPdfFileCustomAgency = "";
                        fileExtension = "";
                        supplierId = 0;
                        subTotalCustoms = 0;
                        ivaCustoms = 0;
                        retefuenteCustoms = 0;
                        payableCustoms = 0;
                    } else if (jsonResult.warning != '') {
                        toastr.clear();
                        toastr.warning(jsonResult.warning);
                        $('input[name="csrf_cgrerp"]').val(jsonResult.csrf_hash);

                        $("#lblInvoiceNoIncentives").text("");
                        $("#lblSupplierNameIncentives").text("");
                        $("#lblIssuedDateIncentives").text("");
                        $("#lblTotalAmountIncentives").val("0");
                        $("#lblTotalTaxAmountIncentives").val("0");
                        $("#lblAllowanceAmountIncentives").val("0");
                        $("#lblPayableAmountIncentives").val("0");

                        $("#divXmlIncentives").hide();
                        $("#divPdfIncentives").show();
                        $("#divContainersIncentives").hide();

                        totalPayableAmount = 0;
                        uploadPdfFileCustomAgency = "";
                        fileExtension = "";
                        supplierId = 0;
                        subTotalCustoms = 0;
                        ivaCustoms = 0;
                        retefuenteCustoms = 0;
                        payableCustoms = 0;
                    } else {
                        toastr.clear();
                    }
                }
            });
        } else {
            toastr.clear();
            //toastr.error(common_error);
            $("#fileUploadDoc_Incentives").val("");
        }
    };

    var loadFileRemobilization = function(event) {
        $("#error-selectdocremobilization").hide();
        event.preventDefault();

        var files = $('#fileUploadDoc_Remobilization')[0].files[0];
        if (files != null && files != "") {

            $('#loading').show();
            farm_data_array = [];
            var fd = new FormData();

            fd.append('fileUploadDoc', files);
            fd.append('exportId', $("#hdnExportId").val());
            fd.append('originId', $("#hdnOriginId").val());
            fd.append('saNumber', $("#hdnSaNumber").val());
            fd.append('exportType', 8);
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

                        $("#lblInvoiceNoRemobilization").text(jsonResult.result["documentId"]);
                        $("#lblSupplierNameRemobilization").text(jsonResult.result["registrationName"] + " --- " + jsonResult.result["companyId"]);
                        $("#lblIssuedDateRemobilization").text(jsonResult.result["issueDate"]);
                        // $("#lblTotalAmountRemobilization").text(jsonResult.result["taxExclusiveAmount"]);
                        // $("#lblTotalTaxAmountRemobilization").text(jsonResult.result["taxAmount"]);
                        // $("#lblAllowanceAmountRemobilization").text(jsonResult.result["allowanceTotalAmount"]);
                        // $("#lblPayableAmountRemobilization").text(jsonResult.result["payableAmount"]);
                        $("#lblTotalAmountRemobilization").val(jsonResult.result["taxExclusiveAmountValue"]);
                        $("#lblTotalTaxAmountRemobilization").val(jsonResult.result["taxAmountValue"]);
                        $("#lblAllowanceAmountRemobilization").val(jsonResult.result["allowanceTotalAmountValue"]);
                        $("#lblPayableAmountRemobilization").val(jsonResult.result["payableAmountValue"]);

                        if (jsonResult.result["fileExtension"] == "xml" || jsonResult.result["fileExtension"] == "XML") {
                            $("#divXmlRemobilization").show();
                            $("#divPdfRemobilization").hide();
                            $("#divContainersRemobilization").show();

                            $("#fileUploadDoc_Remobilization").val("");

                            totalPayableAmount = jsonResult.result["payableAmountValue"];
                            uploadPdfFileCustomAgency = jsonResult.result["fileUrl"];
                            fileExtension = jsonResult.result["fileExtension"];
                            supplierId = jsonResult.result["supplierId"];
                            subTotalCustoms = jsonResult.result["taxExclusiveAmountValue"];
                            ivaCustoms = jsonResult.result["taxAmountValue"];
                            retefuenteCustoms = jsonResult.result["allowanceTotalAmountValue"];
                            payableCustoms = jsonResult.result["payableAmountValue"];

                            var containerData = JSON.parse(jsonResult.result["containerValue"]);
                            containerData.forEach(function(container) {
                                var dispatchId = container.dispatchId;
                                var containerValue = container.containerValue;

                                // Find the input with name matching the dispatch ID
                                var input = $("input[name='remobilization_container_value[" + dispatchId + "]']");
                                if (input.length > 0) {
                                    input.val(parseFloat(containerValue));
                                }
                            });
                        } else if (jsonResult.result["fileExtension"] == "pdf" || jsonResult.result["fileExtension"] == "PDF") {
                            $("#divXmlRemobilization").hide();
                            $("#divPdfRemobilization").show();
                            $("#divContainersRemobilization").show();

                            uploadPdfFileCustomAgency = jsonResult.result["fileUrl"];
                            fileExtension = jsonResult.result["fileExtension"];
                            supplierId = jsonResult.result["supplierId"];

                            var containerData = JSON.parse(jsonResult.result["containerValue"]);
                            containerData.forEach(function(container) {
                                var dispatchId = container.dispatchId;
                                var containerValue = container.containerValue;

                                // Find the input with name matching the dispatch ID
                                var input = $("input[name='remobilization_container_value[" + dispatchId + "]']");
                                if (input.length > 0) {
                                    input.val(parseFloat(containerValue));
                                }
                            });
                        }

                    } else if (jsonResult.error != '') {
                        toastr.clear();
                        toastr.error(jsonResult.error);
                        $('input[name="csrf_cgrerp"]').val(jsonResult.csrf_hash);

                        $("#divXmlRemobilization").hide();
                        $("#divPdfRemobilization").show();
                        $("#divContainersRemobilization").hide();

                        $("#fileUploadDoc_Remobilization").val("");

                        $("#lblInvoiceNoRemobilization").text("");
                        $("#lblSupplierNameRemobilization").text("");
                        $("#lblIssuedDateRemobilization").text("");
                        $("#lblTotalAmountRemobilization").val("0");
                        $("#lblTotalTaxAmountRemobilization").val("0");
                        $("#lblAllowanceAmountRemobilization").val("0");
                        $("#lblPayableAmountRemobilization").val("0");

                        totalPayableAmount = 0;
                        uploadPdfFileCustomAgency = "";
                        fileExtension = "";
                        supplierId = 0;
                        subTotalCustoms = 0;
                        ivaCustoms = 0;
                        retefuenteCustoms = 0;
                        payableCustoms = 0;
                    } else if (jsonResult.warning != '') {
                        toastr.clear();
                        toastr.warning(jsonResult.warning);
                        $('input[name="csrf_cgrerp"]').val(jsonResult.csrf_hash);

                        $("#lblInvoiceNoRemobilization").text("");
                        $("#lblSupplierNameRemobilization").text("");
                        $("#lblIssuedDateRemobilization").text("");
                        $("#lblTotalAmountRemobilization").val("0");
                        $("#lblTotalTaxAmountRemobilization").val("0");
                        $("#lblAllowanceAmountRemobilization").val("0");
                        $("#lblPayableAmountRemobilization").val("0");

                        $("#divXmlRemobilization").hide();
                        $("#divPdfRemobilization").show();
                        $("#divContainersRemobilization").hide();

                        totalPayableAmount = 0;
                        uploadPdfFileCustomAgency = "";
                        fileExtension = "";
                        supplierId = 0;
                        subTotalCustoms = 0;
                        ivaCustoms = 0;
                        retefuenteCustoms = 0;
                        payableCustoms = 0;
                    } else {
                        toastr.clear();
                    }
                }
            });
        } else {
            toastr.clear();
            //toastr.error(common_error);
            $("#fileUploadDoc_Remobilization").val("");
        }
    };

    var loadFileOthercost = function(event) {
        $("#error-selectdocothercost").hide();
        event.preventDefault();

        var files = $('#fileUploadDoc_Othercost')[0].files[0];
        if (files != null && files != "") {

            $('#loading').show();
            farm_data_array = [];
            var fd = new FormData();

            fd.append('fileUploadDoc', files);
            fd.append('exportId', $("#hdnExportId").val());
            fd.append('originId', $("#hdnOriginId").val());
            fd.append('saNumber', $("#hdnSaNumber").val());
            fd.append('exportType', 12);
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

                        $("#lblInvoiceNoOthercost").text(jsonResult.result["documentId"]);
                        $("#lblSupplierNameOthercost").text(jsonResult.result["registrationName"] + " --- " + jsonResult.result["companyId"]);
                        $("#lblIssuedDateOthercost").text(jsonResult.result["issueDate"]);
                        // $("#lblTotalAmountOthercost").text(jsonResult.result["taxExclusiveAmount"]);
                        // $("#lblTotalTaxAmountOthercost").text(jsonResult.result["taxAmount"]);
                        // $("#lblAllowanceAmountOthercost").text(jsonResult.result["allowanceTotalAmount"]);
                        // $("#lblPayableAmountOthercost").text(jsonResult.result["payableAmount"]);
                        $("#lblTotalAmountOthercost").val(jsonResult.result["taxExclusiveAmountValue"]);
                        $("#lblTotalTaxAmountOthercost").val(jsonResult.result["taxAmountValue"]);
                        $("#lblAllowanceAmountOthercost").val(jsonResult.result["allowanceTotalAmountValue"]);
                        $("#lblPayableAmountOthercost").val(jsonResult.result["payableAmountValue"]);

                        if (jsonResult.result["fileExtension"] == "xml" || jsonResult.result["fileExtension"] == "XML") {
                            $("#divXmlOthercost").show();
                            $("#divPdfOthercost").hide();
                            $("#divContainersOthercost").show();

                            $("#fileUploadDoc_Phyto").val("");

                            totalPayableAmount = jsonResult.result["payableAmountValue"];
                            uploadPdfFileCustomAgency = jsonResult.result["fileUrl"];
                            fileExtension = jsonResult.result["fileExtension"];
                            supplierId = jsonResult.result["supplierId"];
                            subTotalCustoms = jsonResult.result["taxExclusiveAmountValue"];
                            ivaCustoms = jsonResult.result["taxAmountValue"];
                            retefuenteCustoms = jsonResult.result["allowanceTotalAmountValue"];
                            payableCustoms = jsonResult.result["payableAmountValue"];

                            var containerData = JSON.parse(jsonResult.result["containerValue"]);
                            containerData.forEach(function(container) {
                                var dispatchId = container.dispatchId;
                                var containerValue = container.containerValue;

                                // Find the input with name matching the dispatch ID
                                var input = $("input[name='othercost_container_value[" + dispatchId + "]']");
                                if (input.length > 0) {
                                    input.val(parseFloat(containerValue));
                                }
                            });
                        } else if (jsonResult.result["fileExtension"] == "pdf" || jsonResult.result["fileExtension"] == "PDF") {
                            $("#divXmlOthercost").hide();
                            $("#divPdfOthercost").show();
                            $("#divContainersOthercost").show();

                            uploadPdfFileCustomAgency = jsonResult.result["fileUrl"];
                            fileExtension = jsonResult.result["fileExtension"];
                            supplierId = jsonResult.result["supplierId"];

                            var containerData = JSON.parse(jsonResult.result["containerValue"]);
                            containerData.forEach(function(container) {
                                var dispatchId = container.dispatchId;
                                var containerValue = container.containerValue;

                                // Find the input with name matching the dispatch ID
                                var input = $("input[name='othercost_container_value[" + dispatchId + "]']");
                                if (input.length > 0) {
                                    input.val(parseFloat(containerValue));
                                }
                            });
                        }

                    } else if (jsonResult.error != '') {
                        toastr.clear();
                        toastr.error(jsonResult.error);
                        $('input[name="csrf_cgrerp"]').val(jsonResult.csrf_hash);

                        $("#divXmlOthercost").hide();
                        $("#divPdfOthercost").show();
                        $("#divContainersOthercost").hide();

                        $("#fileUploadDoc_Othercost").val("");

                        $("#lblInvoiceNoOthercost").text("");
                        $("#lblSupplierNameOthercost").text("");
                        $("#lblIssuedDateOthercost").text("");
                        $("#lblTotalAmountOthercost").val("0");
                        $("#lblTotalTaxAmountOthercost").val("0");
                        $("#lblAllowanceAmountOthercost").val("0");
                        $("#lblPayableAmountOthercost").val("0");

                        totalPayableAmount = 0;
                        uploadPdfFileCustomAgency = "";
                        fileExtension = "";
                        supplierId = 0;
                        subTotalCustoms = 0;
                        ivaCustoms = 0;
                        retefuenteCustoms = 0;
                        payableCustoms = 0;
                    } else if (jsonResult.warning != '') {
                        toastr.clear();
                        toastr.warning(jsonResult.warning);
                        $('input[name="csrf_cgrerp"]').val(jsonResult.csrf_hash);

                        $("#lblInvoiceNoOthercost").text("");
                        $("#lblSupplierNameOthercost").text("");
                        $("#lblIssuedDateOthercost").text("");
                        $("#lblTotalAmountOthercost").val("0");
                        $("#lblTotalTaxAmountOthercost").val("0");
                        $("#lblAllowanceAmountOthercost").val("0");
                        $("#lblPayableAmountOthercost").val("0");

                        $("#divXmlOthercost").hide();
                        $("#divPdfOthercost").show();
                        $("#divContainersOthercost").hide();

                        totalPayableAmount = 0;
                        uploadPdfFileCustomAgency = "";
                        fileExtension = "";
                        supplierId = 0;
                        subTotalCustoms = 0;
                        ivaCustoms = 0;
                        retefuenteCustoms = 0;
                        payableCustoms = 0;
                    } else {
                        toastr.clear();
                    }
                }
            });
        } else {
            toastr.clear();
            //toastr.error(common_error);
            $("#fileUploadDoc_Othercost").val("");
        }
    };

    var loadFileDhlCost = function(event) {
        $("#error-selectdocdhlcost").hide();
        event.preventDefault();

        var files = $('#fileUploadDoc_Dhlcost')[0].files[0];
        if (files != null && files != "") {

            $('#loading').show();
            farm_data_array = [];
            var fd = new FormData();

            fd.append('fileUploadDoc', files);
            fd.append('exportId', $("#hdnExportId").val());
            fd.append('originId', $("#hdnOriginId").val());
            fd.append('saNumber', $("#hdnSaNumber").val());
            fd.append('exportType', 13);
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

                        $("#lblInvoiceNoDhlCost").text(jsonResult.result["documentId"]);
                        $("#lblSupplierNameDhlCost").text(jsonResult.result["registrationName"] + " --- " + jsonResult.result["companyId"]);
                        $("#lblIssuedDateDhlCost").text(jsonResult.result["issueDate"]);
                        // $("#lblTotalAmountIncentives").text(jsonResult.result["taxExclusiveAmount"]);
                        // $("#lblTotalTaxAmountIncentives").text(jsonResult.result["taxAmount"]);
                        // $("#lblAllowanceAmountIncentives").text(jsonResult.result["allowanceTotalAmount"]);
                        // $("#lblPayableAmountIncentives").text(jsonResult.result["payableAmount"]);
                        $("#lblTotalAmountDhlCost").val(jsonResult.result["taxExclusiveAmountValue"]);
                        $("#lblTotalTaxAmountDhlCost").val(jsonResult.result["taxAmountValue"]);
                        $("#lblAllowanceAmountDhlCost").val(jsonResult.result["allowanceTotalAmountValue"]);
                        $("#lblPayableAmountDhlCost").val(jsonResult.result["payableAmountValue"]);

                        if (jsonResult.result["fileExtension"] == "xml" || jsonResult.result["fileExtension"] == "XML") {
                            $("#divXmlDhlCost").show();
                            $("#divPdfDhlCost").hide();
                            $("#divContainersDhlCost").show();

                            $("#fileUploadDoc_Dhlcost").val("");

                            totalPayableAmount = jsonResult.result["payableAmountValue"];
                            uploadPdfFileCustomAgency = jsonResult.result["fileUrl"];
                            fileExtension = jsonResult.result["fileExtension"];
                            supplierId = jsonResult.result["supplierId"];
                            subTotalCustoms = jsonResult.result["taxExclusiveAmountValue"];
                            ivaCustoms = jsonResult.result["taxAmountValue"];
                            retefuenteCustoms = jsonResult.result["allowanceTotalAmountValue"];
                            payableCustoms = jsonResult.result["payableAmountValue"];

                            var containerData = JSON.parse(jsonResult.result["containerValue"]);
                            containerData.forEach(function(container) {
                                var dispatchId = container.dispatchId;
                                var containerValue = container.containerValue;

                                // Find the input with name matching the dispatch ID
                                var input = $("input[name='dhlcost_container_value[" + dispatchId + "]']");
                                if (input.length > 0) {
                                    input.val(parseFloat(containerValue));
                                }
                            });
                        } else if (jsonResult.result["fileExtension"] == "pdf" || jsonResult.result["fileExtension"] == "PDF") {
                            $("#divXmlDhlCost").hide();
                            $("#divPdfDhlCost").show();
                            $("#divContainersDhlCost").show();

                            uploadPdfFileCustomAgency = jsonResult.result["fileUrl"];
                            fileExtension = jsonResult.result["fileExtension"];
                            supplierId = jsonResult.result["supplierId"];

                            var containerData = JSON.parse(jsonResult.result["containerValue"]);
                            containerData.forEach(function(container) {
                                var dispatchId = container.dispatchId;
                                var containerValue = container.containerValue;

                                // Find the input with name matching the dispatch ID
                                var input = $("input[name='dhlcost_container_value[" + dispatchId + "]']");
                                if (input.length > 0) {
                                    input.val(parseFloat(containerValue));
                                }
                            });
                        }

                    } else if (jsonResult.error != '') {
                        toastr.clear();
                        toastr.error(jsonResult.error);
                        $('input[name="csrf_cgrerp"]').val(jsonResult.csrf_hash);

                        $("#divXmlDhlCost").hide();
                        $("#divPdfDhlCost").show();
                        $("#divContainersDhlCost").hide();

                        $("#fileUploadDoc_Dhlcost").val("");

                        $("#lblInvoiceNoDhlCost").text("");
                        $("#lblSupplierNameDhlCost").text("");
                        $("#lblIssuedDateDhlCost").text("");
                        $("#lblTotalAmountDhlCost").val("0");
                        $("#lblTotalTaxAmountDhlCost").val("0");
                        $("#lblAllowanceAmountDhlCost").val("0");
                        $("#lblPayableAmountDhlCost").val("0");

                        totalPayableAmount = 0;
                        uploadPdfFileCustomAgency = "";
                        fileExtension = "";
                        supplierId = 0;
                        subTotalCustoms = 0;
                        ivaCustoms = 0;
                        retefuenteCustoms = 0;
                        payableCustoms = 0;
                    } else if (jsonResult.warning != '') {
                        toastr.clear();
                        toastr.warning(jsonResult.warning);
                        $('input[name="csrf_cgrerp"]').val(jsonResult.csrf_hash);

                        $("#lblInvoiceNoDhlCost").text("");
                        $("#lblSupplierNameDhlCost").text("");
                        $("#lblIssuedDateDhlCost").text("");
                        $("#lblTotalAmountDhlCost").val("0");
                        $("#lblTotalTaxAmountDhlCost").val("0");
                        $("#lblAllowanceAmountDhlCost").val("0");
                        $("#lblPayableAmountDhlCost").val("0");

                        $("#divXmlDhlCost").hide();
                        $("#divPdfDhlCost").show();
                        $("#divContainersDhlCost").hide();

                        totalPayableAmount = 0;
                        uploadPdfFileCustomAgency = "";
                        fileExtension = "";
                        supplierId = 0;
                        subTotalCustoms = 0;
                        ivaCustoms = 0;
                        retefuenteCustoms = 0;
                        payableCustoms = 0;
                    } else {
                        toastr.clear();
                    }
                }
            });
        } else {
            toastr.clear();
            //toastr.error(common_error);
            $("#fileUploadDoc_Dhlcost").val("");
        }
    };

    var loadFileContainerLoadingCost = function(event) {
        $("#error-selectdoccontainerloadingcost").hide();
        event.preventDefault();

        var files = $('#fileUploadDoc_ContainerLoadingCost')[0].files[0];
        if (files != null && files != "") {

            $('#loading').show();
            farm_data_array = [];
            var fd = new FormData();

            fd.append('fileUploadDoc', files);
            fd.append('exportId', $("#hdnExportId").val());
            fd.append('originId', $("#hdnOriginId").val());
            fd.append('saNumber', $("#hdnSaNumber").val());
            fd.append('exportType', 11);
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

                        $("#lblInvoiceNoContainerLoadingCost").text(jsonResult.result["documentId"]);
                        $("#lblSupplierNameContainerLoadingCost").text(jsonResult.result["registrationName"] + " --- " + jsonResult.result["companyId"]);
                        $("#lblIssuedDateContainerLoadingCost").text(jsonResult.result["issueDate"]);
                        // $("#lblTotalAmountIncentives").text(jsonResult.result["taxExclusiveAmount"]);
                        // $("#lblTotalTaxAmountIncentives").text(jsonResult.result["taxAmount"]);
                        // $("#lblAllowanceAmountIncentives").text(jsonResult.result["allowanceTotalAmount"]);
                        // $("#lblPayableAmountIncentives").text(jsonResult.result["payableAmount"]);
                        $("#lblTotalAmountContainerLoadingCost").val(jsonResult.result["taxExclusiveAmountValue"]);
                        $("#lblTotalTaxAmountContainerLoadingCost").val(jsonResult.result["taxAmountValue"]);
                        $("#lblAllowanceAmountContainerLoadingCost").val(jsonResult.result["allowanceTotalAmountValue"]);
                        $("#lblPayableAmountContainerLoadingCost").val(jsonResult.result["payableAmountValue"]);

                        if (jsonResult.result["fileExtension"] == "xml" || jsonResult.result["fileExtension"] == "XML") {
                            $("#divXmlContainerLoadingCost").show();
                            $("#divPdfContainerLoadingCost").hide();
                            $("#divContainersContainerLoadingCost").show();

                            $("#fileUploadDoc_ContainerLoadingCost").val("");

                            totalPayableAmount = jsonResult.result["payableAmountValue"];
                            uploadPdfFileCustomAgency = jsonResult.result["fileUrl"];
                            fileExtension = jsonResult.result["fileExtension"];
                            supplierId = jsonResult.result["supplierId"];
                            subTotalCustoms = jsonResult.result["taxExclusiveAmountValue"];
                            ivaCustoms = jsonResult.result["taxAmountValue"];
                            retefuenteCustoms = jsonResult.result["allowanceTotalAmountValue"];
                            payableCustoms = jsonResult.result["payableAmountValue"];

                            var containerData = JSON.parse(jsonResult.result["containerValue"]);
                            containerData.forEach(function(container) {
                                var dispatchId = container.dispatchId;
                                var containerValue = container.containerValue;

                                // Find the input with name matching the dispatch ID
                                var input = $("input[name='containerloadingcost_container_value[" + dispatchId + "]']");
                                if (input.length > 0) {
                                    input.val(parseFloat(containerValue));
                                }
                            });
                        } else if (jsonResult.result["fileExtension"] == "pdf" || jsonResult.result["fileExtension"] == "PDF") {
                            $("#divXmlContainerLoadingCost").hide();
                            $("#divPdfContainerLoadingCost").show();
                            $("#divContainersContainerLoadingCost").show();

                            uploadPdfFileCustomAgency = jsonResult.result["fileUrl"];
                            fileExtension = jsonResult.result["fileExtension"];
                            supplierId = jsonResult.result["supplierId"];

                            var containerData = JSON.parse(jsonResult.result["containerValue"]);
                            containerData.forEach(function(container) {
                                var dispatchId = container.dispatchId;
                                var containerValue = container.containerValue;

                                // Find the input with name matching the dispatch ID
                                var input = $("input[name='containerloadingcost_container_value[" + dispatchId + "]']");
                                if (input.length > 0) {
                                    input.val(parseFloat(containerValue));
                                }
                            });
                        }

                    } else if (jsonResult.error != '') {
                        toastr.clear();
                        toastr.error(jsonResult.error);
                        $('input[name="csrf_cgrerp"]').val(jsonResult.csrf_hash);

                        $("#divXmlContainerLoadingCost").hide();
                        $("#divPdfContainerLoadingCost").show();
                        $("#divContainersContainerLoadingCost").hide();

                        $("#fileUploadDoc_ContainerLoadingCost").val("");

                        $("#lblInvoiceNoContainerLoadingCost").text("");
                        $("#lblSupplierNameContainerLoadingCost").text("");
                        $("#lblIssuedDateContainerLoadingCost").text("");
                        $("#lblTotalAmountContainerLoadingCost").val("0");
                        $("#lblTotalTaxAmountContainerLoadingCost").val("0");
                        $("#lblAllowanceAmountContainerLoadingCost").val("0");
                        $("#lblPayableAmountContainerLoadingCost").val("0");

                        totalPayableAmount = 0;
                        uploadPdfFileCustomAgency = "";
                        fileExtension = "";
                        supplierId = 0;
                        subTotalCustoms = 0;
                        ivaCustoms = 0;
                        retefuenteCustoms = 0;
                        payableCustoms = 0;
                    } else if (jsonResult.warning != '') {
                        toastr.clear();
                        toastr.warning(jsonResult.warning);
                        $('input[name="csrf_cgrerp"]').val(jsonResult.csrf_hash);

                        $("#lblInvoiceNoContainerLoadingCost").text("");
                        $("#lblSupplierNameContainerLoadingCost").text("");
                        $("#lblIssuedDateContainerLoadingCost").text("");
                        $("#lblTotalAmountContainerLoadingCost").val("0");
                        $("#lblTotalTaxAmountContainerLoadingCost").val("0");
                        $("#lblAllowanceAmountContainerLoadingCost").val("0");
                        $("#lblPayableAmountContainerLoadingCost").val("0");

                        $("#divXmlContainerLoadingCost").hide();
                        $("#divPdfContainerLoadingCost").show();
                        $("#divContainersContainerLoadingCost").hide();

                        totalPayableAmount = 0;
                        uploadPdfFileCustomAgency = "";
                        fileExtension = "";
                        supplierId = 0;
                        subTotalCustoms = 0;
                        ivaCustoms = 0;
                        retefuenteCustoms = 0;
                        payableCustoms = 0;
                    } else {
                        toastr.clear();
                    }
                }
            });
        } else {
            toastr.clear();
            //toastr.error(common_error);
            $("#fileUploadDoc_ContainerLoadingCost").val("");
        }
    };
</script>

<script>
    //CUSTOMS
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

    document.getElementById("formatted_date_custom").addEventListener("click", function() {
        document.getElementById("issued_date_custom").click();
    });

    //ITR
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

        document.getElementById("formatted_date_itr").value = formattedDate;
    });

    document.getElementById("formatted_date_itr").addEventListener("click", function() {
        document.getElementById("issued_date_itr").click();
    });

    //PORT
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

    document.getElementById("formatted_date_port").addEventListener("click", function() {
        document.getElementById("issued_date_port").click();
    });

    //SHIPPING
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

    document.getElementById("formatted_date_shipping").addEventListener("click", function() {
        document.getElementById("issued_date_shipping").click();
    });

    //FUMIGATION
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

    document.getElementById("formatted_date_fumigation").addEventListener("click", function() {
        document.getElementById("issued_date_fumigation").click();
    });

    //PHYTO
    document.getElementById("issued_date_phyto").addEventListener("change", function() {
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

        document.getElementById("formatted_date_phyto").value = formattedDate;
    });

    document.getElementById("formatted_date_phyto").addEventListener("click", function() {
        document.getElementById("issued_date_phyto").click();
    });

    //COTEROS
    document.getElementById("issued_date_coteros").addEventListener("change", function() {
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

        document.getElementById("formatted_date_coteros").value = formattedDate;
    });

    document.getElementById("formatted_date_coteros").addEventListener("click", function() {
        document.getElementById("issued_date_coteros").click();
    });

    //INCENTIVES
    document.getElementById("issued_date_incentives").addEventListener("change", function() {
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

        document.getElementById("formatted_date_incentives").value = formattedDate;
    });

    document.getElementById("formatted_date_incentives").addEventListener("click", function() {
        document.getElementById("issued_date_incentives").click();
    });

    //REMOBOLIZATION
    document.getElementById("issued_date_remobilization").addEventListener("change", function() {
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

        document.getElementById("formatted_date_remobilization").value = formattedDate;
    });

    document.getElementById("formatted_date_remobilization").addEventListener("click", function() {
        document.getElementById("issued_date_remobilization").click();
    });

    //OTHER COSTS
    document.getElementById("issued_date_othercost").addEventListener("change", function() {
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

        document.getElementById("formatted_date_othercost").value = formattedDate;
    });

    document.getElementById("formatted_date_othercost").addEventListener("click", function() {
        document.getElementById("issued_date_othercost").click();
    });

    //DHL COSTS
    document.getElementById("issued_date_dhlcost").addEventListener("change", function() {
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

        document.getElementById("formatted_date_dhlcost").value = formattedDate;
    });

    document.getElementById("formatted_date_dhlcost").addEventListener("click", function() {
        document.getElementById("issued_date_dhlcost").click();
    });

    //CONTAINER LOADINGS COSTS
    document.getElementById("issued_date_containerloadingcost").addEventListener("change", function() {
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

        document.getElementById("formatted_date_containerloadingcost").value = formattedDate;
    });

    document.getElementById("formatted_date_containerloadingcost").addEventListener("click", function() {
        document.getElementById("issued_date_containerloadingcost").click();
    });

    $(document).on('click', '#infoValueBeforeTaxPhyto', function() {

        let html = '';

        if (!containerValuePhytoArr.length) {
            html = '<p class="text-danger">' + no_data_available + '</p>';
        } else {

            containerValuePhytoArr.forEach(function(val, index) {
                html += `
                    <div class="form-check mb-2">
                        <input class="form-check-input phyto-tax-value"
                            type="checkbox"
                            value="${val}"
                            id="phytoTaxVal_${index}">
                        <label class="form-check-label" for="phytoTaxVal_${index}">
                            ${parseFloat(val).toFixed(2)}
                        </label>
                    </div>
                `;
            });
        }

        $('#valueBeforeTaxListPhyto').html(html);
        $('#modalValueBeforeTaxPhyto').modal('show');
    });

    $(document).on('change', '.phyto-tax-value', function() {

        // allow only one selection
        $('.phyto-tax-value').not(this).prop('checked', false);

        if (!$(this).is(':checked')) return;

        let totalValue = parseFloat($(this).val());
        $('#lblTotalAmountPhyto').val(totalValue.toFixed(2));
        $('#modalValueBeforeTaxPhyto').modal('hide');

        let remainingValue = totalValue;
        let validContainers = [];

        // collect containers that can receive value
        containerValueArr.forEach(function(container) {
            if (parseFloat(container.containerValue) !== 0) {
                validContainers.push(container);
            }
        });

        let containerCount = validContainers.length;
        if (containerCount === 0) return;

        let eachContainerValue = parseFloat((totalValue / containerCount).toFixed(2));

        validContainers.forEach(function(container, index) {

            let dispatchId = container.dispatchId;
            let valueToAssign = eachContainerValue;

            // 🔥 FIRST container absorbs rounding difference
            if (index === 0) {
                valueToAssign = parseFloat(
                    (remainingValue - (eachContainerValue * (containerCount - 1))).toFixed(2)
                );
            }

            let input = $("input[name='phyto_container_value[" + dispatchId + "]']");
            if (input.length) {
                input.val(valueToAssign);
            }
        });

        // zero containers stay zero
        containerValueArr.forEach(function(container) {
            if (parseFloat(container.containerValue) === 0) {
                let input = $("input[name='phyto_container_value[" + container.dispatchId + "]']");
                if (input.length) {
                    input.val(0);
                }
            }
        });
    });
</script>

<div class="modal fade" id="modalValueBeforeTaxPhyto" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">

            <div class="modal-header">
                <h5 style="color:white;" class="modal-title"><?php echo $this->lang->line("export_subtotal"); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body" id="valueBeforeTaxListPhyto">
                <!-- values injected by JS -->
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Close
                </button>
            </div>

        </div>
    </div>
</div>