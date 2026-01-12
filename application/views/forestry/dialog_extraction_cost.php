<?php
defined('BASEPATH') or exit('No direct script access allowed');

$session = $this->session->userdata('fullname');
$applicable_origins = $session["applicable_origins"];
$site_lang = $this->load->helper('language');
$wz_lang = $site_lang->session->userdata('site_lang');
?>

<div class="modal-header">
    <h4 class="modal-title" id="add-modal-data"><?php echo $pageheading; ?></h4>
    <?php echo form_button(array('aria-label' => 'Close', 'data-bs-dismiss' => 'modal', 'type' => 'button', 'class' => 'close', 'content' => '<span aria-hidden="true">×</span>')); ?>
</div>

<div class="modal-body farm-modal">
    <input type="hidden" id="pagetype" name="pagetype" value="<?php echo $pagetype; ?>">
    <input type="hidden" id="hdnExtractionCost" name="hdnExtractionCost" value="0">
    <input type="hidden" id="hdnExtractionId" name="hdnExtractionId" value="<?php echo $extraction_id; ?>">
    <input type="hidden" id="hdnCsrf" name="hdnCsrf" value="<?php echo $csrfcgr; ?>">
    <div class="row mb-3">
        <div class="col-md-6 mb-2">
            <label for="origin_extraction"><?php echo $this->lang->line('origin'); ?></label>
            <select class="form-control" name="origin_extraction" id="origin_extraction" data-plugin="select_erp">
                <option value="0"><?php echo $this->lang->line("select"); ?></option>
                <?php foreach ($applicable_origins as $origin) { ?>
                    <?php if ($extractionDetails[0]->origin_id == $origin->id) { ?>
                        <option value="<?php echo $origin->id; ?>" selected="selected"><?php echo $origin->origin_name; ?></option>
                    <?php } else { ?>
                        <option value="<?php echo $origin->id; ?>"><?php echo $origin->origin_name; ?></option>
                    <?php } ?>
                <?php } ?>
            </select>
            <label id="error-origin" class="error-text"><?php echo $this->lang->line('error_origin_screen'); ?></label>
        </div>

        <div class="col-md-6">
            <label for="supplier_extraction"><?php echo $this->lang->line('supplier_name'); ?></label>
            <select class="form-control" name="supplier_extraction" id="supplier_extraction" data-plugin="select_erp" disabled>
                <option value="0"><?php echo $this->lang->line("select"); ?></option>

                <?php if ($pagetype == "edit") { ?>
                    <?php foreach ($suppliers as $supplier) { ?>
                        <option value="<?php echo $supplier->id; ?>" <?php if ($extractionDetails[0]->supplier_id == $supplier->id) : ?> selected="selected" <?php endif; ?>><?php echo $supplier->supplier_name; ?></option>
                    <?php } ?>
                <?php } ?>
            </select>
            <label id="error-name" class="error-text"><?php echo $this->lang->line('error_select_name'); ?></label>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6 mb-2">
            <label for="purchase_contract_extraction"><?php echo $this->lang->line('purchase_contract'); ?></label>
            <select class="form-control" name="purchase_contract_extraction" id="purchase_contract_extraction" data-plugin="select_erp" disabled>
                <option value="0"><?php echo $this->lang->line("select"); ?></option>
                <?php if ($pagetype == "edit") { ?>
                    <?php foreach ($purchaseContracts as $contract) {

                        if ($contract->description == null || $contract->description == "") {
                            $contractData = $contract->contract_code;
                        } else {
                            $contractData = $contract->contract_code . " -- " . $contract->description;
                        }

                    ?>
                        <option value="<?php echo $contract->contract_id; ?>" <?php if ($extractionDetails[0]->contract_id == $contract->contract_id) : ?> selected="selected" <?php endif; ?>><?php echo $contractData; ?></option>
                    <?php } ?>
                <?php } ?>
            </select>
            <label id="error-purchasecontract" class="error-text"><?php echo $this->lang->line('error_purchase_contract'); ?></label>
        </div>

        <div class="row col-md-6" id="divShowDetails">
            <?php if ($pagetype == "edit") { ?>
                <label class="label-showdetails" for="lblContractDesc" id="lblContractDesc"><?php echo $this->lang->line('description') . ': ' . $contractDescription[0]->description; ?></label>
                <label class="label-showdetails" for="lblExtractionCost" id="lblExtractionCost"><?php echo $this->lang->line('extraction_cost') . ': ' . $contractDescription[0]->extraction_cost + 0; ?></label>
            <?php } else { ?>
                <label class="label-showdetails" for="lblContractDesc" id="lblContractDesc"><?php echo $this->lang->line('description') . ': ---'; ?></label>
                <label class="label-showdetails" for="lblExtractionCost" id="lblExtractionCost"><?php echo $this->lang->line('extraction_cost') . ': ---'; ?></label>
            <?php } ?>
        </div>

    </div>

    <div class="row mb-3">
        <div class="col-md-2">
            <label for="extraction_date"><?php echo $this->lang->line('extraction_date'); ?></label>
            <input type="text" id="extraction_date" name="extraction_date" class="form-control" readonly placeholder="<?php echo $this->lang->line('extraction_date'); ?>" value="<?php echo $extractionDetails[0]->extraction_date; ?>" />
            <label id="error-extractiondate" class="error-text"><?php echo $this->lang->line('error_date'); ?></label>
        </div>

        <div class="col-md-2 d-flex align-items-end">
            <button class="btn btn-primary btn-md" title="<?php echo $this->lang->line('save'); ?>"
                type="button" id="btn_save_extraction">
                <span class="ms-1"><?php echo $this->lang->line('save'); ?></span>
            </button>
        </div>

        <div class="col-md-2">
            <label class="head-label"><?php echo $this->lang->line('total_trees'); ?></label>
            <div class="input-group">
                <label class="control-label" id="lblExtractionTrees"><?php echo $totalTrees; ?></label>
            </div>
        </div>

        <div class="col-md-2">
            <label class="head-label"><?php echo $this->lang->line('total_no_of_pieces'); ?></label>
            <div class="input-group">
                <label class="control-label" id="lblExtractionPieces"><?php echo $totalPieces; ?></label>
            </div>
        </div>

        <div class="col-md-2">
            <label class="head-label"><?php echo $this->lang->line('total_volume'); ?></label>
            <div class="input-group">
                <label class="control-label" id="lblExtractionVolume"><?php echo $totalVolume; ?></label>
            </div>
        </div>

        <div class="col-md-2">
            <label class="head-label"><?php echo $this->lang->line('total_cost'); ?></label>
            <div class="input-group">
                <label class="control-label" id="lblExtractionTotalCost"><?php echo $totalCost; ?></label>
            </div>
        </div>
    </div>

    <div id="divExtractionDetails">
        <div class="row mb-3">
            <h6 class="mb-3"><u>Extraction Details</u></h6>

            <div class="col-md-2 mb-2">
                <label for="tree_no"><?php echo $this->lang->line('tree_no'); ?></label>
                <select class="form-control" name="tree_no" id="tree_no" data-plugin="select_erp">
                    <option value="0"><?php echo $this->lang->line("select"); ?></option>
                    <?php
                    $minNo = 1;
                    $maxNo = 200;

                    for ($i = $minNo; $i <= $maxNo; $i++) {
                        echo '<option value="' . $i . '">' . $i . '</option>';
                    }
                    ?>
                </select>
                <label id="error-treeno" class="error-text"><?php echo $this->lang->line('select_tree_no'); ?></label>
            </div>

            <div class="col-md-3">
                <label class="head-label"><?php echo $this->lang->line('pieces'); ?></label>
                <div class="input-group">
                    <label class="control-label" id="lblTotalPieces"><?php echo "0"; ?></label>
                </div>
            </div>

            <div class="col-md-3">
                <label class="head-label"><?php echo $this->lang->line('volume'); ?></label>
                <div class="input-group">
                    <label class="control-label" id="lblTotalVolume"><?php echo "0.000"; ?></label>
                </div>
            </div>
        </div>

        <div class="row mt-3 mb-2">
            <div class="col-md-2">
                <label class="col-form-label font-template-heading text-decoration-underline"><?php echo $this->lang->line('log_number'); ?></label>
            </div>
            <div class="col-md-2">
                <label class="col-form-label font-template-heading text-decoration-underline"><?php echo $this->lang->line('circumference'); ?></label>
            </div>
            <div class="col-md-2">
                <label class="col-form-label font-template-heading text-decoration-underline"><?php echo $this->lang->line('length'); ?></label>
            </div>
            <div class="col-md-2">
                <label class="col-form-label font-template-heading text-decoration-underline"><?php echo $this->lang->line('text_volume'); ?></label>
            </div>
        </div>
        <div id="priceRows">
            <div class="row DataRow">
                <div class="col-md-2 mb-2">
                    <input type="number" id="log_number" name="log_number[]" class="form-control log-number" readonly>
                </div>

                <div class="col-md-2">
                    <input type="number" id="circumference" name="circumference[]" class="form-control">
                </div>

                <div class="col-md-2">
                    <input type="number" id="length" name="length[]" class="form-control">
                </div>

                <div class="col-md-2">
                    <input type="number" id="text_volume" name="text_volume[]" class="form-control" readonly>
                </div>

                <div class="col-md-2 action-buttons">
                    <button type="button" class="btn btn-success add_price">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12 d-flex justify-content-end gap-2">
                <button class="btn btn-danger btn-md"
                    type="button" id="btn_reset_extraction_trees">
                    <?php echo $this->lang->line('reset'); ?>
                </button>

                <button class="btn btn-success btn-md"
                    type="button" id="btn_save_extraction_trees">
                    <?php echo $this->lang->line('save'); ?>
                </button>
            </div>
        </div>
    </div>

    <div class="row mb-3" id="divExtractionTrees">
        <div class="row flex-between-end">
            <div class="col-auto align-self-center">
                <h6><u><?php echo $this->lang->line('extraction_lists'); ?></u></h6>
            </div>
            <div class="col-auto ms-auto">
                <button class="btn btn-success btn-md" title="<?php echo $this->lang->line('add'); ?>" type="button" id="btn_add_extraction_tree">
                    <span class="fas fa-plus" data-fa-transform="shrink-3 down-2"></span><span class="ms-1"><?php echo $this->lang->line('tree'); ?></span></button>
            </div>

        </div>
        <table class="datatables-demo table table-striped table-bordered" id="xin_table_extraction_trees" style="width: 100% !important;">
            <thead>
                <tr>
                    <th><?php echo $this->lang->line("action"); ?></th>
                    <th><?php echo $this->lang->line("tree_no"); ?></th>
                    <th><?php echo $this->lang->line("total_no_of_pieces"); ?></th>
                    <th><?php echo $this->lang->line("total_volume"); ?></th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<script type="text/javascript">
    var contract_desc = "<?php echo $this->lang->line('description'); ?>";
    var extraction_cost = "<?php echo $this->lang->line('extraction_cost'); ?>";
    var error_extraction_cost = "<?php echo $this->lang->line('error_extraction_cost'); ?>";
    var error_extraction_details = "<?php echo $this->lang->line('error_extraction_details'); ?>";
    var error_fill_all_rows = "<?php echo $this->lang->line('error_fill_all_rows'); ?>";
    var row_required = "<?php echo $this->lang->line('row_required'); ?>";

    // Remove old handlers first
    $(document).off('click', '.add_price');
    $(document).off('click', '.remove_price');

    $(document).ready(function() {

        $("#error-origin").hide();
        $("#error-name").hide();
        $("#error-purchasecontract").hide();
        $("#error-extractiondate").hide();
        $("#error-treeno").hide();
        $("#divShowDetails").hide();
        $("#divExtractionDetails").hide();
        $("#divExtractionTrees").show();
        $("#hdnExtractionCost").val(0);

        <?php if ($pagetype == "edit") { ?>
            $('#origin_extraction').prop('disabled', true).trigger('change');
            $("#extraction_date").prop('disabled', true);
            $("#divShowDetails").show();
            $("#btn_save_extraction").hide();
        <?php } else { ?>
            $("#divShowDetails").hide();
            $("#btn_save_extraction").show();
        <?php } ?>

        updateLogNumbers();

        $("#origin_extraction").change(function() {
            if ($("#origin_extraction").val() == 0) {
                $("#supplier_extraction").attr("disabled", true);
                fetchSuppliers(0);
                fetchContracts(0, 0);
            } else {
                fetchSuppliers($("#origin_extraction").val());
                $("#supplier_extraction").attr("disabled", false);
                $("#purchase_contract_extraction").attr("disabled", true);

                fetchContracts(0, 0);
                $("#error-origin").hide();
            }

            $("#divShowDetails").hide();
            $("#hdnExtractionCost").val(0);
        });

        $("#supplier_extraction").change(function() {

            if ($("#supplier_extraction").val() == 0) {
                $("#purchase_contract_extraction").attr("disabled", true);
                fetchContracts(0, 0);
            } else {
                fetchContracts($("#origin_extraction").val(), $("#supplier_extraction").val());
                $("#error-name").hide();
            }

            $("#divShowDetails").hide();
            $("#hdnExtractionCost").val(0);
        });

        $("#purchase_contract_extraction").change(function() {

            if ($("#purchase_contract_extraction").val() == 0) {
                fetchContractDetails(0, 0, 0, 0, 0);
            } else {
                fetchContractDetails($("#origin_extraction").val(), $("#purchase_contract_extraction").val());
                $("#purchase_contract_extraction").attr("disabled", false);
                $("#error-purchasecontract").hide();
            }
        });

        $("#extraction_date").datepicker({
            dateFormat: "dd/mm/yy",
            changeMonth: true,
            changeYear: true,
            minDate: '-1y',
            maxDate: '0d',
            onSelect: function(date) {
                $("#error-extractiondate").hide();
            }
        });

        $("#btn_save_extraction").click(function() {
            var origin_extraction = $("#origin_extraction").val();
            var supplier_extraction = $("#supplier_extraction").val();
            var purchase_contract_extraction = $("#purchase_contract_extraction").val();
            var extraction_date = $("#extraction_date").val().trim();
            var editId = $("#hdnExtractionId").val();
            var extractionCost = $("#hdnExtractionCost").val();
            var pageType = $("#pagetype").val();

            var isValid1 = true,
                isValid2 = true,
                isValid3 = true,
                isValid4 = true;

            if (origin_extraction == 0) {
                $("#error-origin").show();
                isValid1 = false;
            } else {
                $("#error-origin").hide();
                isValid1 = true;
            }

            if (supplier_extraction == 0) {
                $("#error-name").show();
                isValid2 = false;
            } else {
                $("#error-name").hide();
                isValid2 = true;
            }

            if (purchase_contract_extraction == 0) {
                $("#error-purchasecontract").show();
                isValid3 = false;
            } else {
                $("#error-purchasecontract").hide();
                isValid3 = true;
            }

            if (extraction_date == "") {
                $("#error-extractiondate").show();
                isValid4 = false;
            } else {
                $("#error-extractiondate").hide();
                isValid4 = true;
            }

            if (isValid1 && isValid2 && isValid3 && isValid4) {
                if (extractionCost <= 0) {
                    toastr.clear();
                    toastr.warning(error_extraction_cost);
                    $("#divExtractionDetails").hide();
                } else {

                    var fd = new FormData();
                    fd.append("originId", origin_extraction);
                    fd.append("supplierId", supplier_extraction);
                    fd.append("contractId", purchase_contract_extraction);
                    fd.append("extractionDate", extraction_date);
                    fd.append("extractionId", editId);
                    fd.append("extractionCost", extractionCost);
                    fd.append("pageType", pageType);
                    fd.append("actionType", "extraction");
                    fd.append("csrf_cgrerp", $("#hdnCsrf").val());

                    $("#btn_save_extraction").prop('disabled', true);
                    toastr.info(processing_request);

                    $("#loading").show();

                    $.ajax({
                        type: "POST",
                        url: BASE_URL_SUBFOLDER + "forestry/extractioncost/save_extraction",
                        data: fd,
                        contentType: false,
                        cache: false,
                        processData: false,
                        success: function(JSON) {
                            $("#loading").hide();
                            if (JSON.redirect == true) {
                                window.location.replace(login_url);
                            } else if (JSON.warning != '') {
                                toastr.clear();
                                toastr.warning(JSON.warning);
                                $("#btn_save_extraction").prop('disabled', false);
                                $('input[name="csrf_cgrerp"]').val(JSON.csrf_hash);
                                $("#divExtractionDetails").hide();
                            } else if (JSON.error != '') {
                                toastr.clear();
                                toastr.error(JSON.error);
                                $("#btn_save_extraction").prop('disabled', false);
                                $('input[name="csrf_cgrerp"]').val(JSON.csrf_hash);
                                $("#divExtractionDetails").hide();
                            } else {
                                toastr.clear();
                                toastr.success(JSON.result);
                                $("#btn_save_extraction").prop('disabled', false);
                                $('input[name="csrf_cgrerp"]').val(JSON.csrf_hash);
                                $("#divExtractionDetails").show();
                                $("#divExtractionTrees").hide();
                                $("#hdnExtractionId").val(JSON.extraction_id);
                                $("#btn_save_extraction").hide();
                            }

                            // $('#xin_table_extractions').DataTable().ajax.reload(null, false);
                            if (window.extractionTable) {
                                window.extractionTable.ajax.reload(null, false);
                            }
                            refreshExtractionTotals(editId);
                        }
                    });
                }
            }
        });

        $("#btn_save_extraction_trees").click(function() {
            var treeNo = $("#tree_no").val();
            var extractionId = $("#hdnExtractionId").val();
            let extractionDetailsJson = getExtractionDetailsJson();

            if (treeNo == 0) {
                $("#error-treeno").show();
            } else {
                $("#error-treeno").hide();
                if (extractionDetailsJson == false) {
                    return;
                } else if (extractionDetailsJson.length === 0) {
                    toastr.warning(error_extraction_details);
                    return;
                } else {
                    var fd = new FormData();
                    fd.append("extractionId", extractionId);
                    fd.append("treeNo", treeNo);
                    fd.append("totalPieces", $("#lblTotalPieces").text());
                    fd.append("totalVolume", $("#lblTotalVolume").text());
                    fd.append("extractionData", JSON.stringify(extractionDetailsJson));
                    fd.append("csrf_cgrerp", $("#hdnCsrf").val());
                    fd.append("actionType", "extractionTrees");

                    let mode = $("#btn_save_extraction_trees").data('mode') || 'add';
                    let treeId = $("#btn_save_extraction_trees").data('treeid') || 0;

                    fd.append("pageType", mode);
                    fd.append("treeId", treeId);

                    $("#btn_save_extraction_trees").prop('disabled', true);
                    toastr.info(processing_request);

                    $("#loading").show();

                    $.ajax({
                        type: "POST",
                        url: BASE_URL_SUBFOLDER + "forestry/extractioncost/save_extraction_details",
                        data: fd,
                        contentType: false,
                        cache: false,
                        processData: false,
                        success: function(JSON) {
                            $("#loading").hide();
                            if (JSON.redirect == true) {
                                window.location.replace(login_url);
                            } else if (JSON.warning != '') {
                                toastr.clear();
                                toastr.warning(JSON.warning);
                                $("#btn_save_extraction_trees").prop('disabled', false);
                                $('input[name="csrf_cgrerp"]').val(JSON.csrf_hash);
                                $("#divExtractionTrees").hide();
                            } else if (JSON.error != '') {
                                toastr.clear();
                                toastr.error(JSON.error);
                                $("#btn_save_extraction_trees").prop('disabled', false);
                                $('input[name="csrf_cgrerp"]').val(JSON.csrf_hash);
                                $("#divExtractionTrees").hide();
                            } else {
                                toastr.clear();
                                toastr.success(JSON.result);
                                $("#btn_save_extraction_trees").prop('disabled', false);
                                $('input[name="csrf_cgrerp"]').val(JSON.csrf_hash);

                                $("#divExtractionTrees").show();
                                $("#divExtractionDetails").hide();

                                $("#btn_save_extraction_trees").removeData('mode');
                                $("#btn_save_extraction_trees").removeData('treeid');

                                // ✅ ENABLE Tree No again
                                $("#tree_no").prop('disabled', false);

                                // 🔥 RESET FORM ROWS AFTER SAVE
                                resetExtractionFormRows();
                                $("#btn_add_extraction_tree").show();
                            }

                            loadExtractionTreesTable(extractionId);
                            refreshExtractionTotals(extractionId);
                        }
                    });
                }
            }
        });

        $("#btn_reset_extraction_trees").click(function() {
            $("#divExtractionTrees").show();
            $("#divExtractionDetails").hide();

            // 🔥 RESET FORM ROWS AFTER SAVE
            resetExtractionFormRows();

            $("#btn_save_extraction_trees").removeData('mode');
            $("#btn_save_extraction_trees").removeData('treeid');

            // ✅ ENABLE Tree No again
            $("#tree_no").prop('disabled', false);

            $('#xin_table_extraction_trees').DataTable().ajax.reload(null, false);

            refreshExtractionTotals(extractionId);
        });

        // Add row
        $(document).on('click', '.add_price', function() {

            let row = $(this).closest('.DataRow');
            let clone = row.clone();

            clone.find('input').not('.log-number').val('');

            clone.find('.action-buttons').html(`
                <button type="button" class="btn btn-danger remove_price">
                    <i class="fas fa-trash"></i>
                </button>
            `);

            clone.hide().appendTo('#priceRows').fadeIn(200);
            updateLogNumbers();

            // 🎯 Focus circumference in new row
            clone.find('input[name="circumference[]"]').focus();
        });

        // Remove row
        $(document).on('click', '.remove_price', function() {

            // 🚫 Prevent deleting last row
            if ($('.DataRow').length === 1) {
                toastr.warning(row_required);
                return;
            }

            $(this).closest('.DataRow').fadeOut(200, function() {
                $(this).remove();
                updateLogNumbers();
                updateTotals(); // ✅ RE-CALCULATE AFTER DELETE
            });
        });

        // Calculate volume on typing
        $(document).on(
            'input',
            'input[name="circumference[]"], input[name="length[]"]',
            function() {

                let row = $(this).closest('.DataRow');

                let circumference = parseFloat(row.find('input[name="circumference[]"]').val()) || 0;
                let length = parseFloat(row.find('input[name="length[]"]').val()) || 0;

                if (circumference > 0 && length > 0) {

                    let volume = (circumference * circumference * length) / 16000000;

                    // ✅ TRUNCATE to 3 decimals
                    volume = truncate(volume, 3);

                    row.find('input[name="text_volume[]"]').val(volume.toFixed(3));

                } else {
                    row.find('input[name="text_volume[]"]').val('');
                }

                updateTotals();
            }
        );

        $("#btn_add_extraction_tree").click(function() {
            $("#divExtractionDetails").show();
            $("#divExtractionTrees").hide();

            let extractionId = $("#hdnExtractionId").val();

            loadAvailableTreeNumbers(extractionId); // 🔥 FILTER TREE NOS

            resetExtractionFormRows();

            $("#btn_save_extraction_trees").removeData('mode');
            $("#btn_save_extraction_trees").removeData('treeid');

            // ✅ ENABLE Tree No again
            $("#tree_no").prop('disabled', false);

            refreshExtractionTotals(extractionId);
        });

        $(document).on('click', 'button[data-role=editextractiontree]', function() {

            let extractionId = $("#hdnExtractionId").val();
            let treeId = $(this).data('extraction_tree_id');
            let treeNo = $(this).closest('tr').find('td:eq(1)').text().trim();

            $("#loading").show();

            $.ajax({
                url: BASE_URL_SUBFOLDER +
                    "forestry/extractioncost/fetch_extraction_tree_details",
                type: "GET",
                data: {
                    treeId: treeId,
                    extractionId: extractionId
                },
                dataType: "json",
                success: function(JSON) {

                    $("#loading").hide();

                    $("#divExtractionTrees").hide();
                    $("#divExtractionDetails").show();

                    loadTreeIntoForm(JSON.result);

                    // 🔥 Lock Tree No in edit
                    $("#tree_no").empty()
                        .append(`<option value="${treeNo}" selected>${treeNo}</option>`)
                        .prop('disabled', true);

                    $("#btn_save_extraction_trees").data('mode', 'edit');
                    $("#btn_save_extraction_trees").data('treeid', treeId);
                }
            });
        });

        $(document).on('click', 'button[data-role=deleteextractiontree]', function() {
            let extractionId = $("#hdnExtractionId").val();
            let treeId = $(this).data('extraction_tree_id');

            $.ajax({
                url: BASE_URL_SUBFOLDER + "forestry/extractioncost/dialog_extraction_action",
                type: "GET",
                data: 'jd=1&is_ajax=3&mode=modal&type=deleteextractionconfirmation&eid=' + extractionId + '&tid=' + treeId,
                success: function(response) {

                    $("#ajax_modal").html(response);

                    let zIndex = 1051; // higher than edit modal (Bootstrap default 1050)

                    $("#add-modal-data")
                        .css('z-index', zIndex)
                        .modal('show');

                    // // Fix backdrop
                    // $('.modal-backdrop')
                    //     .not('.modal-stack')
                    //     .css('z-index', zIndex - 1)
                    //     .addClass('modal-stack');
                }
            });
        });
    });

    function fetchSuppliers(originid) {
        $("#loading").show();
        $.ajax({
            url: BASE_URL_SUBFOLDER + "forestry/extractioncost/fetch_suppliers?originid=" + originid,
            cache: false,
            method: "GET",
            dataType: 'json',
            success: function(JSON) {
                $("#loading").hide();
                if (JSON.redirect == true) {
                    window.location.replace(login_url);
                } else if (JSON.result != '') {
                    $("#supplier_extraction").empty();
                    $("#supplier_extraction").append(JSON.result);
                }
            }
        });
    }

    function fetchContracts(originid, supplierid) {
        $("#divReceptionDetail").hide();
        $("#loading").show();
        $.ajax({
            url: BASE_URL_SUBFOLDER + "forestry/extractioncost/get_contracts_by_supplier?originid=" + originid + "&supplierid=" + supplierid,
            cache: false,
            method: "GET",
            dataType: 'json',
            success: function(JSON) {
                $("#loading").hide();
                if (JSON.redirect == true) {
                    window.location.replace(login_url);
                } else if (JSON.result != '') {
                    $("#purchase_contract_extraction").attr("disabled", false);
                    $("#purchase_contract_extraction").empty();
                    $("#purchase_contract_extraction").append(JSON.result);
                } else {
                    $("#purchase_contract_extraction").attr("disabled", true);
                }
            }
        });
    }

    function fetchContractDetails(originid, contractid) {
        $("#loading").show();
        $.ajax({
            url: BASE_URL_SUBFOLDER + "forestry/extractioncost/fetch_contract_details?originid=" + originid + "&contractid=" + contractid,
            cache: false,
            method: "GET",
            dataType: 'json',
            success: function(JSON) {
                $("#loading").hide();
                $("#divShowDetails").hide();
                $("#hdnExtractionCost").val(0);
                if (JSON.redirect == true) {
                    window.location.replace(login_url);
                } else if (JSON.error != '') {
                    toastr.clear();
                    toastr.error(JSON.error);
                } else if (JSON.result != '') {
                    toastr.clear();
                    $("#divShowDetails").show();
                    $("#lblContractDesc").text(contract_desc + ": " + JSON.result["contract_desc"]);
                    $("#lblExtractionCost").text(extraction_cost + ": " + JSON.result["extraction_cost"]);
                    $("#hdnExtractionCost").val(JSON.result["extraction_cost"]);
                }
            }
        });
    }

    function updateLogNumbers() {
        $('.DataRow').each(function(index) {
            $(this).find('.log-number').val(index + 1);
        });
    }

    function getExtractionDetailsJson() {

        let extractionDetails = [];
        let isValid = true;

        $('.DataRow').each(function(index) {

            let logNumber = $(this).find('input[name="log_number[]"]').val().trim();
            let circumference = $(this).find('input[name="circumference[]"]').val().trim();
            let length = $(this).find('input[name="length[]"]').val().trim();
            let textVolume = $(this).find('input[name="text_volume[]"]').val().trim();

            // ❌ If ANY field empty → invalid
            if (!logNumber || !circumference || !length || !textVolume) {
                toastr.error(error_fill_all_rows + (index + 1));
                isValid = false;
                return false; // break .each()
            }

            extractionDetails.push({
                log_number: logNumber,
                circumference: circumference,
                length: length,
                volume: textVolume
            });
        });

        return isValid ? extractionDetails : false;
    }

    function updateTotals() {

        let totalPieces = 0;
        let totalVolume = 0;

        $('.DataRow').each(function() {

            let volume = parseFloat(
                $(this).find('input[name="text_volume[]"]').val()
            ) || 0;

            if (volume > 0) {
                totalPieces++;
                totalVolume += volume;
            }
        });

        $('#lblTotalPieces').text(totalPieces);
        $('#lblTotalVolume').text(truncate(totalVolume, 3).toFixed(3));
    }

    function truncate(value, decimals) {
        let factor = Math.pow(10, decimals);
        return Math.floor(value * factor) / factor;
    }

    function loadExtractionTreesTable(extractionId) {

        $('#xin_table_extraction_trees').DataTable({
            destroy: true,
            paging: false,
            searching: false,
            ordering: false,
            scrollX: true,
            ajax: {
                url: BASE_URL_SUBFOLDER +
                    "forestry/extractioncost/extraction_trees_list",
                type: "GET",
                data: {
                    extractionId: extractionId
                }
            },
            language: {
                url: datatable_language
            }
        });
    }

    function resetExtractionFormRows() {

        // Remove all rows
        $('#priceRows').empty();

        // Add ONE fresh row
        let newRow = `
            <div class="row DataRow">
                <div class="col-md-2 mb-2">
                    <input type="number" name="log_number[]" class="form-control log-number" readonly>
                </div>

                <div class="col-md-2">
                    <input type="number" name="circumference[]" class="form-control">
                </div>

                <div class="col-md-2">
                    <input type="number" name="length[]" class="form-control">
                </div>

                <div class="col-md-2">
                    <input type="number" name="text_volume[]" class="form-control" readonly>
                </div>

                <div class="col-md-2 action-buttons">
                    <button type="button" class="btn btn-success add_price">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
        `;

        $('#priceRows').append(newRow);

        // Reset totals
        $('#lblTotalPieces').text(0);
        $('#lblTotalVolume').text('0.000');

        // Reset log numbers
        updateLogNumbers();

        // 🎯 Focus first circumference
        $('input[name="circumference[]"]').first().focus();
    }

    function loadTreeIntoForm(treeDetails) {

        $('#priceRows').empty();

        treeDetails.forEach((row, index) => {

            let isFirstRow = index === 0;

            $('#priceRows').append(`
                <div class="row DataRow">
                    <div class="col-md-2 mb-2">
                        <input type="number" name="log_number[]"
                            class="form-control log-number"
                            value="${row.log_no}" readonly>
                    </div>

                    <div class="col-md-2">
                        <input type="number" name="circumference[]"
                            class="form-control"
                            value="${parseFloat(row.circumference).toFixed(0)}">
                    </div>

                    <div class="col-md-2">
                        <input type="number" name="length[]"
                            class="form-control"
                            value="${parseFloat(row.length).toFixed(0)}">
                    </div>

                    <div class="col-md-2">
                        <input type="number" name="text_volume[]"
                            class="form-control"
                            value="${parseFloat(row.volume).toFixed(3)}" readonly>
                    </div>

                    <div class="col-md-2 action-buttons">
                        ${
                            isFirstRow
                            ? `<button type="button" class="btn btn-success add_price">
                                <i class="fas fa-plus"></i>
                            </button>`
                            : `<button type="button" class="btn btn-danger remove_price">
                                <i class="fas fa-trash"></i>
                            </button>`
                        }
                    </div>
                </div>
            `);
        });

        updateLogNumbers();
        updateTotals();

        // 🎯 Focus first circumference like initial load
        $('input[name="circumference[]"]').first().focus();
    }

    function loadAvailableTreeNumbers(extractionId) {
        $.ajax({
            url: BASE_URL_SUBFOLDER +
                "forestry/extractioncost/get_used_tree_numbers",
            type: "GET",
            data: {
                extractionId: extractionId
            },
            dataType: "json",
            success: function(usedTreeNos) {

                let $treeSelect = $("#tree_no");
                $treeSelect.empty();
                $treeSelect.append('<option value="0">Select</option>');

                for (let i = 1; i <= 200; i++) {
                    if (!usedTreeNos.includes(i)) {
                        $treeSelect.append(
                            `<option value="${i}">${i}</option>`
                        );
                    }
                }
            }
        });
    }

    function refreshExtractionTotals(extractionId) {

        $.ajax({
            url: BASE_URL_SUBFOLDER + "forestry/extractioncost/get_extraction_totals",
            type: "GET",
            data: { extractionId: extractionId },
            dataType: "json",
            success: function (res) {

                if (res.redirect) {
                    window.location.replace(login_url);
                    return;
                }

                $("#lblExtractionTrees").text(res.total_trees);
                $("#lblExtractionPieces").text(res.total_pieces);
                $("#lblExtractionVolume").text(res.total_volume);
                $("#lblExtractionTotalCost").text(res.total_cost);
            }
        });
    }
</script>