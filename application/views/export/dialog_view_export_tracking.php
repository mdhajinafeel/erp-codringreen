<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<?php $site_lang = $this->load->helper('language'); ?>
<?php $wz_lang = $site_lang->session->userdata('site_lang'); ?>
<div class="modal-header">
    <h4 class="modal-title" id="add-modal-data"><?php echo $pageheading; ?></h4>
    <?php echo form_button(array('aria-label' => 'Close', 'data-bs-dismiss' => 'modal', 'type' => 'button', 'class' => 'close', 'content' => '<span aria-hidden="true">×</span>')); ?>

</div>
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

    <div class="row mb-3">
        <div class="col-md-3">
            <label for="sa_number"><?php echo $this->lang->line("sa_number"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo $export_details[0]->sa_number; ?></label>
            </div>
        </div>

        <div class="col-md-3">
            <label for="port_of_loading"><?php echo $this->lang->line("port_of_loading"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo $export_details[0]->pol_name; ?></label>
            </div>
        </div>

        <div class="col-md-3">
            <label for="product_type"><?php echo $this->lang->line("product_type"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo $this->lang->line($export_details[0]->product_type_name); ?></label>
            </div>
        </div>

        <div class="col-md-3">
            <label for="shipping_line"><?php echo $this->lang->line("shipping_line"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo $export_details[0]->shipping_line; ?></label>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-3">
            <label for="shipping_line"><?php echo $this->lang->line("port_of_discharge"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo $export_details[0]->pod_name; ?></label>
            </div>
        </div>

        <div class="col-md-3">
            <label for="vessel_name"><?php echo $this->lang->line("vessel_name"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo $export_details[0]->vessel_name; ?></label>
            </div>
        </div>

        <div class="col-md-3">
            <label for="bl_number"><?php echo $this->lang->line("bl_number"); ?></label>
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
    </div>

    <div class="row mb-3">
        <div class="col-md-3">
            <label for="shipped_date"><?php echo $this->lang->line("shipped_date"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo $export_details[0]->shipped_date; ?></label>
            </div>
        </div>

        <div class="col-md-3">
            <label for="eta_date"><?php echo $this->lang->line("eta_destination"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo $export_details[0]->eta_date; ?></label>
            </div>
        </div>

        <div class="col-md-3">
            <label for="client_pno"><?php echo $this->lang->line("client_pno"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo $export_details[0]->client_pno; ?></label>
            </div>
        </div>

        <div class="col-md-3">
            <label for="measuremet_system"><?php echo $this->lang->line("measuremet_system"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo $export_details[0]->measurement_name; ?></label>
            </div>
        </div>

    </div>

    <div class="row mb-3">
        <div class="col-md-3">
            <label for="total_containers"><?php echo $this->lang->line("total_containers"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo $export_details[0]->total_containers; ?></label>
            </div>
        </div>

        <div class="col-md-3">
            <label for="total_no_of_pieces"><?php echo $this->lang->line("total_no_of_pieces"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo $export_details[0]->total_pieces; ?></label>
            </div>
        </div>

        <div class="col-md-3">
            <label for="total_gross_volume"><?php echo $this->lang->line("total_gross_volume"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo $export_details[0]->total_gross_volume + 0; ?></label>
            </div>
        </div>

        <div class="col-md-3">
            <label for="total_net_volume"><?php echo $this->lang->line("total_net_volume"); ?></label>
            <div class="input-group">
                <label class="control-label"><?php echo $export_details[0]->total_net_volume + 0; ?></label>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-3">
            <label for="entry_date"><?php echo $this->lang->line("entry_date"); ?></label>
            <input type="text" id="entry_date" name="entry_date" class="form-control" value="<?php echo $exportTrackingDetails[0]->entry_date; ?>" placeholder="<?php echo $this->lang->line("entry_date"); ?>" readonly />
        </div>

        <div class="col-md-3">
            <label for="client"><?php echo $this->lang->line("client"); ?></label>
            <select class="form-control" name="client" id="client" data-plugin="select_erp">
                <option value="0"><?php echo $this->lang->line("select"); ?></option>
                <?php foreach ($buyers as $buyer) { ?>
                    <option value="<?php echo $buyer->id; ?>" <?php if ($exportTrackingDetails[0]->client == $buyer->id) { ?> selected="selected" <?php } ?>><?php echo $buyer->buyer_name; ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="col-md-3">
            <label for="freight_type"><?php echo $this->lang->line("freight_type"); ?></label>
            <select class="form-control" name="freight_type" id="freight_type" data-plugin="select_erp">
                <option value="0"><?php echo $this->lang->line("select"); ?></option>
                <option value="1" <?php if ($exportTrackingDetails[0]->freight_type == 1) { ?> selected="selected" <?php } ?>><?php echo $this->lang->line("prepaid"); ?></option>
                <option value="2" <?php if ($exportTrackingDetails[0]->freight_type == 2) { ?> selected="selected" <?php } ?>><?php echo $this->lang->line("collect"); ?></option>
            </select>
        </div>

        <div class="col-md-3">
            <label for="cut_off_document"><?php echo $this->lang->line("cut_off_document"); ?></label>
            <input type="text" id="cut_off_document" name="cut_off_document" class="form-control" value="<?php echo $exportTrackingDetails[0]->cut_off_document; ?>" placeholder="<?php echo $this->lang->line("cut_off_document"); ?>" readonly />
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-3">
            <label for="port"><?php echo $this->lang->line("shipping_port"); ?></label>
            <select class="form-control" name="port" id="port" data-plugin="select_erp">
                <option value="0"><?php echo $this->lang->line("select"); ?></option>
                <?php foreach ($shippingports as $port) { ?>
                    <option value="<?php echo $port->id; ?>" <?php if ($exportTrackingDetails[0]->port == $port->id) { ?> selected="selected" <?php } ?>><?php echo $port->port_name; ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="col-md-3">
            <label for="actual_eta"><?php echo $this->lang->line("actual_eta"); ?></label>
            <input type="text" id="actual_eta" name="actual_eta" class="form-control" value="<?php echo $exportTrackingDetails[0]->actual_eta; ?>" placeholder="<?php echo $this->lang->line("actual_eta"); ?>" readonly />
        </div>

        <div class="col-md-3">
            <label for="tracking_customs"><?php echo $this->lang->line("tracking_customs"); ?></label>
            <select class="form-control" name="tracking_customs" id="tracking_customs" data-plugin="select_erp">
                <option value="0"><?php echo $this->lang->line("select"); ?></option>
                <?php foreach ($shippingcustoms as $custom) { ?>
                    <option value="<?php echo $custom->id; ?>" <?php if ($exportTrackingDetails[0]->customs == $custom->id) { ?> selected="selected" <?php } ?>><?php echo $custom->customs_name; ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="col-md-3">
            <label for="shipping_notice"><?php echo $this->lang->line("shipping_notice"); ?></label>
            <select class="form-control" name="shipping_notice" id="shipping_notice" data-plugin="select_erp">
                <option value="0"><?php echo $this->lang->line("select"); ?></option>
                <option value="1" <?php if ($exportTrackingDetails[0]->shipping_notice == 1) { ?> selected="selected" <?php } ?>><?php echo $this->lang->line("yes"); ?></option>
                <option value="2" <?php if ($exportTrackingDetails[0]->shipping_notice == 2) { ?> selected="selected" <?php } ?>><?php echo $this->lang->line("no"); ?></option>
            </select>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-3">
            <label for="shipping_notice"><?php echo $this->lang->line("closing_shipping_document"); ?></label>
            <select class="form-control" name="closing_shipping_document" id="closing_shipping_document" data-plugin="select_erp">
                <option value="0"><?php echo $this->lang->line("select"); ?></option>
                <option value="1" <?php if ($exportTrackingDetails[0]->closing_shipping_document == 1) { ?> selected="selected" <?php } ?>><?php echo $this->lang->line("yes"); ?></option>
                <option value="2" <?php if ($exportTrackingDetails[0]->closing_shipping_document == 2) { ?> selected="selected" <?php } ?>><?php echo $this->lang->line("no"); ?></option>
            </select>
        </div>
    </div>

    <div class="row mb-3">

        <h4 class="mb-3" style="font-size:16px;"><u><?php echo $this->lang->line("port_monitoring"); ?></u></h4>

        <div class="col-md-3">
            <label for="document_management"><?php echo $this->lang->line("document_management"); ?></label>
            <select class="form-control" name="document_management" id="document_management" data-plugin="select_erp">
                <option value="0"><?php echo $this->lang->line("select"); ?></option>
                <option value="1" <?php if ($exportTrackingDetails[0]->document_management == 1) { ?> selected="selected" <?php } ?>><?php echo $this->lang->line("yes"); ?></option>
                <option value="2" <?php if ($exportTrackingDetails[0]->document_management == 2) { ?> selected="selected" <?php } ?>><?php echo $this->lang->line("no"); ?></option>
            </select>
        </div>

        <div class="col-md-3">
            <label for="enter_port"><?php echo $this->lang->line("enter_port"); ?></label>
            <select class="form-control" name="enter_port" id="enter_port" data-plugin="select_erp">
                <option value="0"><?php echo $this->lang->line("select"); ?></option>
                <option value="1" <?php if ($exportTrackingDetails[0]->enter_port == 1) { ?> selected="selected" <?php } ?>><?php echo $this->lang->line("yes"); ?></option>
                <option value="2" <?php if ($exportTrackingDetails[0]->enter_port == 2) { ?> selected="selected" <?php } ?>><?php echo $this->lang->line("no"); ?></option>
            </select>
        </div>

        <div class="col-md-3">
            <label for="vgm_processed"><?php echo $this->lang->line("vgm_processed"); ?></label>
            <select class="form-control" name="vgm_processed" id="vgm_processed" data-plugin="select_erp">
                <option value="0"><?php echo $this->lang->line("select"); ?></option>
                <option value="1" <?php if ($exportTrackingDetails[0]->vgm_processed == 1) { ?> selected="selected" <?php } ?>><?php echo $this->lang->line("yes"); ?></option>
                <option value="2" <?php if ($exportTrackingDetails[0]->vgm_processed == 2) { ?> selected="selected" <?php } ?>><?php echo $this->lang->line("no"); ?></option>
            </select>
        </div>

        <div class="col-md-3">
            <label for="port_inspection"><?php echo $this->lang->line("port_inspection"); ?></label>
            <select class="form-control" name="port_inspection" id="port_inspection" data-plugin="select_erp">
                <option value="0"><?php echo $this->lang->line("select"); ?></option>
                <option value="1" <?php if ($exportTrackingDetails[0]->port_inspection == 1) { ?> selected="selected" <?php } ?>><?php echo $this->lang->line("yes"); ?></option>
                <option value="2" <?php if ($exportTrackingDetails[0]->port_inspection == 2) { ?> selected="selected" <?php } ?>><?php echo $this->lang->line("no"); ?></option>
            </select>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-3">
            <label for="boarding_console"><?php echo $this->lang->line("boarding_console"); ?></label>
            <select class="form-control" name="boarding_console" id="boarding_console" data-plugin="select_erp">
                <option value="0"><?php echo $this->lang->line("select"); ?></option>
                <option value="1" <?php if ($exportTrackingDetails[0]->boarding_console == 1) { ?> selected="selected" <?php } ?>><?php echo $this->lang->line("yes"); ?></option>
                <option value="2" <?php if ($exportTrackingDetails[0]->boarding_console == 2) { ?> selected="selected" <?php } ?>><?php echo $this->lang->line("no"); ?></option>
            </select>
        </div>

        <div class="col-md-3">
            <label for="vessel_departure"><?php echo $this->lang->line("vessel_departure"); ?></label>
            <select class="form-control" name="vessel_departure" id="vessel_departure" data-plugin="select_erp">
                <option value="0"><?php echo $this->lang->line("select"); ?></option>
                <option value="1" <?php if ($exportTrackingDetails[0]->vessel_departure == 1) { ?> selected="selected" <?php } ?>><?php echo $this->lang->line("yes"); ?></option>
                <option value="2" <?php if ($exportTrackingDetails[0]->vessel_departure == 2) { ?> selected="selected" <?php } ?>><?php echo $this->lang->line("no"); ?></option>
            </select>
        </div>

        <div class="col-md-3">
            <label for="departure_date"><?php echo $this->lang->line("departure_date"); ?></label>
            <input type="text" id="departure_date" name="departure_date" class="form-control" value="<?php echo $exportTrackingDetails[0]->departure_date; ?>" placeholder="<?php echo $this->lang->line("departure_date"); ?>" readonly />
        </div>
    </div>

    <div class="row mb-3">

        <h4 class="mb-3" style="font-size:16px;"><u><?php echo $this->lang->line("document_status"); ?></u></h4>

        <div class="col-md-3">
            <label for="phyto"><?php echo $this->lang->line("phyto"); ?></label>
            <select class="form-control" name="phyto" id="phyto" data-plugin="select_erp">
                <option value="0"><?php echo $this->lang->line("select"); ?></option>
                <option value="1" <?php if ($exportTrackingDetails[0]->phyto == 1) { ?> selected="selected" <?php } ?>><?php echo $this->lang->line("yes"); ?></option>
                <option value="2" <?php if ($exportTrackingDetails[0]->phyto == 2) { ?> selected="selected" <?php } ?>><?php echo $this->lang->line("no"); ?></option>
            </select>
        </div>

        <div class="col-md-3">
            <label for="coo"><?php echo $this->lang->line("coo"); ?></label>
            <select class="form-control" name="coo" id="coo" data-plugin="select_erp">
                <option value="0"><?php echo $this->lang->line("select"); ?></option>
                <option value="1" <?php if ($exportTrackingDetails[0]->coo == 1) { ?> selected="selected" <?php } ?>><?php echo $this->lang->line("yes"); ?></option>
                <option value="2" <?php if ($exportTrackingDetails[0]->coo == 2) { ?> selected="selected" <?php } ?>><?php echo $this->lang->line("no"); ?></option>
            </select>
        </div>

        <div class="col-md-3">
            <label for="dex"><?php echo $this->lang->line("dex"); ?></label>
            <select class="form-control" name="dex" id="dex" data-plugin="select_erp">
                <option value="0"><?php echo $this->lang->line("select"); ?></option>
                <option value="1" <?php if ($exportTrackingDetails[0]->dex == 1) { ?> selected="selected" <?php } ?>><?php echo $this->lang->line("yes"); ?></option>
                <option value="2" <?php if ($exportTrackingDetails[0]->dex == 2) { ?> selected="selected" <?php } ?>><?php echo $this->lang->line("no"); ?></option>
            </select>
        </div>

        <div class="col-md-3">
            <label for="fumigation"><?php echo $this->lang->line("fumigation"); ?></label>
            <select class="form-control" name="fumigation" id="fumigation" data-plugin="select_erp">
                <option value="0"><?php echo $this->lang->line("select"); ?></option>
                <option value="1" <?php if ($exportTrackingDetails[0]->fumigation == 1) { ?> selected="selected" <?php } ?>><?php echo $this->lang->line("yes"); ?></option>
                <option value="2" <?php if ($exportTrackingDetails[0]->fumigation == 2) { ?> selected="selected" <?php } ?>><?php echo $this->lang->line("no"); ?></option>
            </select>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-3">
            <label for="secure"><?php echo $this->lang->line("secure"); ?></label>
            <select class="form-control" name="secure" id="secure" data-plugin="select_erp">
                <option value="0"><?php echo $this->lang->line("select"); ?></option>
                <option value="1" <?php if ($exportTrackingDetails[0]->secure == 1) { ?> selected="selected" <?php } ?>><?php echo $this->lang->line("yes"); ?></option>
                <option value="2" <?php if ($exportTrackingDetails[0]->secure == 2) { ?> selected="selected" <?php } ?>><?php echo $this->lang->line("no"); ?></option>
            </select>
        </div>

    </div>

    <div class="row mb-3">

        <h4 class="mb-3" style="font-size:16px;"><u><?php echo $this->lang->line("client_document"); ?></u></h4>

        <div class="col-md-3">
            <label for="sent_by_mail"><?php echo $this->lang->line("sent_by_mail"); ?></label>
            <select class="form-control" name="sent_by_mail" id="sent_by_mail" data-plugin="select_erp">
                <option value="0"><?php echo $this->lang->line("select"); ?></option>
                <option value="1" <?php if ($exportTrackingDetails[0]->sent_by_mail == 1) { ?> selected="selected" <?php } ?>><?php echo $this->lang->line("yes"); ?></option>
                <option value="2" <?php if ($exportTrackingDetails[0]->sent_by_mail == 2) { ?> selected="selected" <?php } ?>><?php echo $this->lang->line("no"); ?></option>
            </select>
        </div>

        <div class="col-md-3">
            <label for="approved"><?php echo $this->lang->line("approved"); ?></label>
            <select class="form-control" name="approved" id="approved" data-plugin="select_erp">
                <option value="0"><?php echo $this->lang->line("select"); ?></option>
                <option value="1" <?php if ($exportTrackingDetails[0]->approved == 1) { ?> selected="selected" <?php } ?>><?php echo $this->lang->line("yes"); ?></option>
                <option value="2" <?php if ($exportTrackingDetails[0]->approved == 2) { ?> selected="selected" <?php } ?>><?php echo $this->lang->line("no"); ?></option>
            </select>
        </div>

        <div class="col-md-3">
            <label for="release"><?php echo $this->lang->line("release"); ?></label>
            <select class="form-control" name="release" id="release" data-plugin="select_erp">
                <option value="0"><?php echo $this->lang->line("select"); ?></option>
                <option value="1" <?php if ($exportTrackingDetails[0]->document_release == 1) { ?> selected="selected" <?php } ?>><?php echo $this->lang->line("yes"); ?></option>
                <option value="2" <?php if ($exportTrackingDetails[0]->document_release == 2) { ?> selected="selected" <?php } ?>><?php echo $this->lang->line("no"); ?></option>
            </select>
        </div>
    </div>

    <div class="row mb-3">

        <h4 class="mb-3" style="font-size:16px;"><u><?php echo $this->lang->line("billing"); ?></u></h4>

        <div class="col-md-3">
            <label for="patio"><?php echo $this->lang->line("patio"); ?></label>
            <select class="form-control" name="patio" id="patio" data-plugin="select_erp">
                <option value="0"><?php echo $this->lang->line("select"); ?></option>
                <option value="1" <?php if ($exportTrackingDetails[0]->patio == 1) { ?> selected="selected" <?php } ?>><?php echo $this->lang->line("yes"); ?></option>
                <option value="2" <?php if ($exportTrackingDetails[0]->patio == 2) { ?> selected="selected" <?php } ?>><?php echo $this->lang->line("no"); ?></option>
            </select>
        </div>

        <div class="col-md-3">
            <label for="billing_port"><?php echo $this->lang->line("shipping_port"); ?></label>
            <select class="form-control" name="billing_port" id="billing_port" data-plugin="select_erp">
                <option value="0"><?php echo $this->lang->line("select"); ?></option>
                <option value="1" <?php if ($exportTrackingDetails[0]->billing_port == 1) { ?> selected="selected" <?php } ?>><?php echo $this->lang->line("yes"); ?></option>
                <option value="2" <?php if ($exportTrackingDetails[0]->billing_port == 2) { ?> selected="selected" <?php } ?>><?php echo $this->lang->line("no"); ?></option>
            </select>
        </div>

        <div class="col-md-3">
            <label for="billing_fumigation"><?php echo $this->lang->line("fumigation"); ?></label>
            <select class="form-control" name="billing_fumigation" id="billing_fumigation" data-plugin="select_erp">
                <option value="0"><?php echo $this->lang->line("select"); ?></option>
                <option value="1" <?php if ($exportTrackingDetails[0]->billing_fumigation == 1) { ?> selected="selected" <?php } ?>><?php echo $this->lang->line("yes"); ?></option>
                <option value="2" <?php if ($exportTrackingDetails[0]->billing_fumigation == 2) { ?> selected="selected" <?php } ?>><?php echo $this->lang->line("no"); ?></option>
            </select>
        </div>

        <div class="col-md-3">
            <label for="billing_customs"><?php echo $this->lang->line("tracking_customs"); ?></label>
            <select class="form-control" name="billing_customs" id="billing_customs" data-plugin="select_erp">
                <option value="0"><?php echo $this->lang->line("select"); ?></option>
                <option value="1" <?php if ($exportTrackingDetails[0]->billing_customs == 1) { ?> selected="selected" <?php } ?>><?php echo $this->lang->line("yes"); ?></option>
                <option value="2" <?php if ($exportTrackingDetails[0]->billing_customs == 2) { ?> selected="selected" <?php } ?>><?php echo $this->lang->line("no"); ?></option>
            </select>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-3">
            <label for="billing_shipping"><?php echo $this->lang->line("billing_shipping"); ?></label>
            <select class="form-control" name="billing_shipping" id="billing_shipping" data-plugin="select_erp">
                <option value="0"><?php echo $this->lang->line("select"); ?></option>
                <option value="1" <?php if ($exportTrackingDetails[0]->billing_shipping == 1) { ?> selected="selected" <?php } ?>><?php echo $this->lang->line("yes"); ?></option>
                <option value="2" <?php if ($exportTrackingDetails[0]->billing_shipping == 2) { ?> selected="selected" <?php } ?>><?php echo $this->lang->line("no"); ?></option>
            </select>
        </div>

        <div class="col-md-3">
            <label for="tracking_observation"><?php echo $this->lang->line("tracking_observation"); ?></label>
            <textarea id="tracking_observation" name="tracking_observation" class="form-control" placeholder="<?php echo $this->lang->line("tracking_observation"); ?>"><?php echo $exportTrackingDetails[0]->observation; ?></textarea>
        </div>
    </div>
</div>
<div class="modal-footer">
    <?php echo form_button(array('data-bs-dismiss' => 'modal', 'type' => 'button', 'class' => 'btn btn-secondary', 'content' => $this->lang->line("close"))); ?>
    <?php echo form_button(array("name" => "cgrerp_form_origin", "type" => "button", "id" => "update_export", "class" => "btn btn-success update_export", "content" => $pagetype == "view" ? $this->lang->line("update") : $this->lang->line("add"))); ?>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function() {

        $("#update_export").click(function() {

            var entry_date = $("#entry_date").val().trim();
            var client = $("#client").val();
            var freight_type = $("#freight_type").val();
            var cut_off_document = $("#cut_off_document").val().trim();
            var port = $("#port").val();
            var actual_eta = $("#actual_eta").val().trim();
            var customs = $("#customs").val();
            var shipping_notice = $("#shipping_notice").val();
            var closing_shipping_document = $("#closing_shipping_document").val();
            var document_management = $("#document_management").val();
            var enter_port = $("#enter_port").val();
            var vgm_processed = $("#vgm_processed").val();
            var port_inspection = $("#port_inspection").val();
            var boarding_console = $("#boarding_console").val();
            var vessel_departure = $("#vessel_departure").val();
            var departure_date = $("#departure_date").val().trim();
            var phyto = $("#phyto").val();
            var coo = $("#coo").val();
            var dex = $("#dex").val();
            var fumigation = $("#fumigation").val();
            var secure = $("#secure").val();
            var sent_by_mail = $("#sent_by_mail").val();
            var approved = $("#approved").val();
            var release = $("#release").val();
            var patio = $("#patio").val();
            var billing_port = $("#billing_port").val();
            var billing_fumigation = $("#billing_fumigation").val();
            var billing_customs = $("#billing_customs").val();
            var billing_shipping = $("#billing_shipping").val();
            var tracking_observation = $("#tracking_observation").val().trim();

            var fd = new FormData();
            fd.append("add_type", "exporttracking");

            fd.append("exportid", $("#hdnExportId").val());
            fd.append("sanumber", $("#hdnSaNumber").val());
            fd.append("originid", $('#hdnOriginId').val());
            fd.append("csrf_cgrerp", $('#hdnCsrf').val());
            fd.append("entry_date", entry_date);
            fd.append("client", client);
            fd.append("freight_type", freight_type);
            fd.append("cut_off_document", cut_off_document);
            fd.append("port", port);
            fd.append("actual_eta", actual_eta);
            fd.append("customs", customs);
            fd.append("shipping_notice", shipping_notice);
            fd.append("closing_shipping_document", closing_shipping_document);
            fd.append("document_management", document_management);
            fd.append("enter_port", enter_port);
            fd.append("vgm_processed", vgm_processed);
            fd.append("port_inspection", port_inspection);
            fd.append("boarding_console", boarding_console);
            fd.append("vessel_departure", vessel_departure);
            fd.append("departure_date", departure_date);
            fd.append("phyto", phyto);
            fd.append("coo", coo);
            fd.append("dex", dex);
            fd.append("fumigation", fumigation);
            fd.append("secure", secure);
            fd.append("sent_by_mail", sent_by_mail);
            fd.append("approved", approved);
            fd.append("release", release);
            fd.append("patio", patio);
            fd.append("billing_port", billing_port);
            fd.append("billing_fumigation", billing_fumigation);
            fd.append("billing_customs", billing_customs);
            fd.append("billing_shipping", billing_shipping);
            fd.append("tracking_observation", tracking_observation);

            $(".update_export").prop('disabled', false);
            toastr.info(processing_request);

            $("#loading").show();
            $.ajax({
                type: "POST",
                url: base_url + "/update",
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
                        $('.update_export').prop('disabled', false);
                        $('input[name="csrf_cgrerp"]').val(JSON.csrf_hash);
                    } else {
                        toastr.clear();
                        toastr.success(JSON.result);
                        $('.update_export').prop('disabled', false);
                        $('input[name="csrf_cgrerp"]').val(JSON.csrf_hash);
                        $("#add-modal-data-bd").modal('hide');
                        $('#xin_table_exports').DataTable().ajax.reload(null, false);
                    }
                },
                error: function(jqXHR, exception) {
                    toastr.clear();
                    $('.add_export').prop('disabled', false);
                }
            });
        });
    });
</script>

<script src="<?php echo base_url() . 'assets/js/i18n/datepicker-' . $wz_lang . '.js'; ?>"></script>
<script type="text/javascript">
    $(function() {
        $("#entry_date").datepicker({
            dateFormat: "dd/mm/yy",
            changeMonth: true,
            changeYear: true,
            minDate: "-6m",
            maxDate: "1y",
            onSelect: function(date) {}
        });

        $("#cut_off_document").datepicker({
            dateFormat: "dd/mm/yy",
            changeMonth: true,
            changeYear: true,
            minDate: "-6m",
            maxDate: "1y",
            onSelect: function(date) {}
        });

        $("#actual_eta").datepicker({
            dateFormat: "dd/mm/yy",
            changeMonth: true,
            changeYear: true,
            minDate: "-6m",
            maxDate: "1y",
            onSelect: function(date) {}
        });

        $("#departure_date").datepicker({
            dateFormat: "dd/mm/yy",
            changeMonth: true,
            changeYear: true,
            minDate: "-6m",
            maxDate: "1y",
            onSelect: function(date) {}
        });

        $('.ui-datepicker').addClass('notranslate');
    });
</script>