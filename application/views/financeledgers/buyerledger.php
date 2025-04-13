<?php
$session = $this->session->userdata('fullname');
$applicable_origins = $session["applicable_origins"];
?>
<?php $site_lang = $this->load->helper('language'); ?>
<?php $wz_lang = $site_lang->session->userdata('site_lang'); ?>
<div class="card mb-3">
    <div class="card-header table-responsive">
        <div class="row flex-between-end">
            <div class="col-auto align-self-center">
                <input type="hidden" id="hdnCsrf" name="hdnCsrf" value="<?php echo $csrf_cgrerp; ?>" />
                <h3> <?php echo $this->lang->line('buyer_ledger_header'); ?> </h3>
            </div>
        </div>
        <div class="card-body pt-5">
            <div class="mb-3 row">
                <label class="col-sm-2 col-form-label lbl-font" for="buyer_name"><?php echo $this->lang->line('buyer_name_invoice'); ?></label>
                <div class="col-sm-10">
                    <select class="form-control" name="buyer_name" id="buyer_name" data-plugin="select_erp">
                        <option value="0"><?php echo $this->lang->line('select'); ?></option>
                        <?php foreach ($buyers as $buyer) { ?>
                            <option value="<?php echo $buyer->id; ?>"><?php echo $buyer->buyer_name; ?></option>
                        <?php } ?>
                    </select>
                    <div class="mb-4 row"></div>
                </div>
                
                <label class="col-sm-2 col-form-label lbl-font" for="origin_ledger"><?php echo $this->lang->line('origin'); ?></label>
                <div class="col-sm-10">
                    <select class="form-control" name="origin_ledger" id="origin_ledger" data-plugin="select_erp">
                        <option value="0"><?php echo $this->lang->line('select'); ?></option>
                        <?php foreach ($applicable_origins as $origin) { ?>
                            <option value="<?php echo $origin->id; ?>"><?php echo $origin->origin_name; ?></option>
                        <?php } ?>
                    </select>
                    <div class="mb-4 row"></div>
                </div>

                <div class="row flex-between-end">
                    <div class="col-md-10 ms-auto">
                        <!--<button class="btn btn-primary btn-block" title="<?php echo $this->lang->line("download_reports"); ?>" type="button" id="btn_download_reports">-->
                        <!--    <span class="ms-1"><?php echo $this->lang->line("download_reports"); ?></span></button>-->

                        <button class="btn btn-danger btn-block ml-10" title="<?php echo $this->lang->line("reset"); ?>" type="button" id="btn_reset">
                            <span class="ms-1"><?php echo $this->lang->line("reset"); ?></span></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-3" id="divInventoryLedgers">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-lg-2 border-lg-end border-bottom border-lg-0 pb-3 pb-lg-0">
                <div class="d-flex flex-between-center mb-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-item icon-item-md shadow-none me-2 bg-soft-shipment"><span class="fs-0 fas fa-ship text-soft-shipment"></span></div>
                        <h6 class="mb-0"><?php echo $this->lang->line("total_shipments"); ?></h6>
                    </div>
                </div>
                <div class="d-flex">
                    <div class="d-flex">
                        <p class="font-sans-serif lh-1 mb-1 fs-2 fw-medium pe-2" style="margin-left: 45px !important;" id="txtTotalShipments"></p>
                    </div>
                </div>

            </div>

            <div class="col-lg-2 border-lg-end border-bottom border-lg-0 py-3 py-lg-0">
                <div class="d-flex flex-between-center mb-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-item icon-item-md shadow-none me-2 bg-soft-containers"><span class="fs-0 fas fa-truck text-soft-containers"></span></div>
                        <h6 class="mb-0"><?php echo $this->lang->line("total_container_buyer"); ?></h6>
                    </div>
                </div>
                <div class="d-flex">
                    <div class="d-flex">
                        <p class="font-sans-serif lh-1 mb-1 fs-2 fw-medium pe-2" style="margin-left: 45px !important;" id="txtTotalContainers"></p>
                    </div>
                </div>
            </div>

            <div class="col-lg-2 border-lg-end border-bottom border-lg-0 py-3 py-lg-0">
                <div class="d-flex flex-between-center mb-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-item icon-item-md shadow-none me-2 bg-soft-totalpieces" style="font-size: 1.5rem !important;"><span class="fs-0 fas fa-th text-soft-totalpieces"></span></div>
                        <h6 class="mb-0"><?php echo $this->lang->line("total_no_of_pieces"); ?></h6>
                    </div>
                </div>
                <div class="d-flex">
                    <div class="d-flex">
                        <p class="font-sans-serif lh-1 mb-1 fs-2 fw-medium pe-2" style="margin-left: 45px !important;" id="txtTotalPieces"></p>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 border-lg-end border-bottom border-lg-0 py-3 py-lg-0">
                <div class="d-flex flex-between-center mb-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-item icon-item-md shadow-none me-2 bg-soft-volume" style="font-size: 1.5rem !important;"><span class="mdi mdi-numeric text-soft-volume"></span></div>
                        <h6 class="mb-0"><?php echo $this->lang->line("total_volume_buyer"); ?></h6>
                    </div>
                </div>
                <div class="d-flex">
                    <div class="d-flex">
                        <p class="font-sans-serif lh-1 mb-1 fs-2 fw-medium pe-2" style="margin-left: 45px !important;" id="txtTotalVolume"></p>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 border-bottom border-lg-0 py-3 py-lg-0">
                <div class="d-flex flex-between-center mb-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-item icon-item-md shadow-none me-2 bg-soft-totalweight" style="font-size: 1.5rem !important;"><span class="mdi mdi-weight text-soft-totalweight"></span></div>
                        <h6 class="mb-0"><?php echo $this->lang->line("total_weight_buyer"); ?></h6>
                    </div>
                </div>
                <div class="d-flex">
                    <div class="d-flex">
                        <p class="font-sans-serif lh-1 mb-1 fs-2 fw-medium pe-2" style="margin-left: 45px !important;" id="txtTotalWeight"></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-5">
            
            <div class="col-lg-2 border-lg-end border-bottom border-lg-0 py-3 py-lg-0">
                <div class="d-flex flex-between-center mb-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-item icon-item-md shadow-none me-2 bg-soft-sales" style="font-size: 1.5rem !important;"><span class="mdi mdi-cash text-soft-sales"></span></div>
                        <h6 class="mb-0"><?php echo $this->lang->line("total_sales_value"); ?></h6>
                    </div>
                </div>
                <div class="d-flex">
                    <div class="d-flex">
                        <p class="font-sans-serif lh-1 mb-1 fs-2 fw-medium pe-2" style="margin-left: 45px !important;" id="txtSalesValue"></p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-2 border-lg-end border-bottom border-lg-0 pb-3 pb-lg-0">
                <div class="d-flex flex-between-center mb-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-item icon-item-md shadow-none me-2 bg-soft-advance" style="font-size: 1.5rem !important;"><span class="mdi mdi-cash-fast text-soft-advance"></span></div>
                        <h6 class="mb-0"><?php echo $this->lang->line("total_advance_cost"); ?></h6>
                    </div>
                </div>
                <div class="d-flex">
                    <div class="d-flex">
                        <p class="font-sans-serif lh-1 mb-1 fs-2 fw-medium pe-2" style="margin-left: 45px !important;" id="txtTotalAdvanceCost"></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 border-lg-end border-bottom border-lg-0 py-3 py-lg-0">
                <div class="d-flex flex-between-center mb-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-item icon-item-md shadow-none me-2 bg-soft-servicecost" style="font-size: 1.5rem !important;"><span class="mdi mdi-cash-multiple text-soft-servicecost"></span></div>
                        <h6 class="mb-0"><?php echo $this->lang->line("total_service_cost"); ?></h6>
                    </div>
                </div>
                <div class="d-flex">
                    <div class="d-flex">
                        <p class="font-sans-serif lh-1 mb-1 fs-2 fw-medium pe-2" style="margin-left: 45px !important;" id="txtTotalServiceCost"></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 border-lg-end border-bottom border-lg-0 py-3 py-lg-0">
                <div class="d-flex flex-between-center mb-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-item icon-item-md shadow-none me-2 bg-soft-claimcost" style="font-size: 1.5rem !important;"><span class="mdi mdi-cash-refund text-soft-claimcost"></span></div>
                        <h6 class="mb-0"><?php echo $this->lang->line("total_claim_cost"); ?></h6>
                    </div>
                </div>
                <div class="d-flex">
                    <div class="d-flex">
                        <p class="font-sans-serif lh-1 mb-1 fs-2 fw-medium pe-2" style="margin-left: 45px !important;" id="txtClaimCost"></p>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 border-bottom border-lg-0 py-3 py-lg-0">
                <div class="d-flex flex-between-center mb-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-item icon-item-md shadow-none me-2 bg-soft-invoicevalue"><span class="fs-0 fas fa-usd text-soft-invoicevalue"></span></div>
                        <h6 class="mb-0"><?php echo $this->lang->line("total_invoice_value"); ?></h6>
                    </div>
                </div>
                <div class="d-flex">
                    <div class="d-flex">
                        <p class="font-sans-serif lh-1 mb-1 fs-2 fw-medium pe-2" style="margin-left: 45px !important;" id="txtInvoiceValue"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-3" id="divTransactions">
    <div class="card-body">

        <div class="row">
            <div class="tab-content">
                <div class="tab-pane preview-tab-pane active" role="tabpanel" aria-labelledby="tab-dom-ec0fa1e3-6325-4caf-a468-7691ef065d01" id="dom-ec0fa1e3-6325-4caf-a468-7691ef065d01">
                    <div class="accordion" id="accordionExample">
                        <!--<div class="accordion-item">-->
                        <!--    <h2 class="accordion-header" id="heading1">-->
                        <!--        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="true" aria-controls="collapse1">-->
                        <!--            <?php echo $this->lang->line('credit_transaction'); ?>-->
                        <!--        </button>-->
                        <!--    </h2>-->
                        <!--    <div class="accordion-collapse collapse" id="collapse1" aria-labelledby="heading1" data-bs-parent="#accordionExample">-->
                        <!--        <div class="accordion-body table-responsive">-->
                        <!--            <table class="datatables-demo table table-responsive table-striped table-bordered" id="xin_table_credits" style="width: 100% !important;">-->

                        <!--                <thead>-->
                        <!--                    <tr>-->
                        <!--                        <th width="100px"><?php echo $this->lang->line("action"); ?></th>-->
                        <!--                        <th><?php echo $this->lang->line("transaction_date"); ?></th>-->
                        <!--                        <th><?php echo $this->lang->line("amount"); ?></th>-->
                        <!--                        <th><?php echo $this->lang->line("credited_by"); ?></th>-->
                        <!--                    </tr>-->
                        <!--                </thead>-->

                        <!--            </table>-->
                        <!--        </div>-->
                        <!--    </div>-->
                        <!--</div>-->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading2">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2" aria-expanded="true" aria-controls="collapse2">
                                    <?php echo $this->lang->line('debit_transaction'); ?>
                                </button>
                            </h2>
                            <div class="accordion-collapse collapse" id="collapse2" aria-labelledby="heading2" data-bs-parent="#accordionExample">
                                <div class="accordion-body table-responsive">
                                    <table class="datatables-demo table table-responsive table-striped table-bordered" id="xin_table_debits" style="width: 100% !important;">

                                        <thead>
                                            <tr>
                                                <th><?php echo $this->lang->line("sa_number"); ?></th>
                                                <th><?php echo $this->lang->line("total_containers"); ?></th>
                                                <th><?php echo $this->lang->line("total_no_of_pieces"); ?></th>
                                                <th><?php echo $this->lang->line("text_volume"); ?></th>
                                                <th><?php echo $this->lang->line("text_weight"); ?></th>
                                                <th><?php echo $this->lang->line("text_sales_value"); ?></th>
                                                <th><?php echo $this->lang->line("text_service_cost"); ?></th>
                                                <th><?php echo $this->lang->line("text_advance_cost"); ?></th>
                                                <th><?php echo $this->lang->line("text_claim_cost"); ?></th>
                                                <th><?php echo $this->lang->line("text_invoice_cost"); ?></th>
                                                <th><?php echo $this->lang->line("origin"); ?></th>
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

<script src="<?php echo base_url() . 'assets/js/jquery341.min.js'; ?>"></script>
<script src="<?php echo base_url() . "assets/js/jquery.dataTables.min.js"; ?>"></script>
<script src="<?php echo base_url() . "assets/js/dataTables.bootstrap.min.js"; ?>"></script>
<link rel="stylesheet" href="<?php echo base_url() . "assets/css/jquery-ui.css"; ?>">
<script src="<?php echo base_url() . "assets/js/jquery-ui.js"; ?>"></script>
<script src="<?php echo base_url() . 'assets/js/i18n/datepicker-' . $wz_lang . '.js'; ?>"></script>

<script type="text/javascript">
    var error_origin_screen = "<?php echo $this->lang->line('error_origin_screen'); ?>";
    var error_select_name = "<?php echo $this->lang->line('error_select_name'); ?>";

    $("#divInventoryLedgers").hide();
    $("#divTransactions").hide();

    $(document).ready(function() {

        $("#buyer_name").change(function() {

            $("#divInventoryLedgers").hide();
            $("#divTransactions").hide();

            if ($("#buyer_name").val() == 0) {

                $("#divInventoryLedgers").hide();
                $("#divTransactions").hide();

            } else {
                $("#loading").show();
                $.ajax({
                    url: base_url + "/get_ledger_details_by_buyer?buyerId=" + $("#buyer_name").val() + "&originId=" + $("#origin_ledger").val(),
                    cache: false,
                    method: "GET",
                    dataType: "json",
                    success: function(JSON) {
                        $("#loading").hide();
                        if (JSON.redirect == true) {
                            window.location.replace(login_url);
                        } else if (JSON.result != '') {

                            $("#txtTotalShipments").text(JSON.result["totalShipments"]);
                            $("#txtTotalContainers").text(JSON.result["totalContainers"]);
                            $("#txtTotalVolume").text(JSON.result["totalVolume"]);
                            $("#txtTotalPieces").text(JSON.result["totalPieces"]);
                            $("#txtTotalWeight").text(JSON.result["totalWeight"]);

                            $("#txtSalesValue").text(JSON.result["totalSalesCost"]);
                            $("#txtTotalAdvanceCost").text(JSON.result["totalAdvanceCost"]);
                            $("#txtTotalServiceCost").text(JSON.result["totalServiceCost"]);
                            $("#txtClaimCost").text(JSON.result["totalClaimCost"]);
                            $("#txtInvoiceValue").text(JSON.result["totalInvoiceValue"]);
                            
                            parseDebitTransactions(JSON.result["debitTransactions"]);

                            $("#divInventoryLedgers").show();
                            $("#divTransactions").show();
                        } else {
                            toastr.clear();
                            toastr.error(JSON.error);
                        }
                    }
                });
            }


        });
        
        $("#origin_ledger").change(function() {

            $("#divInventoryLedgers").hide();
            $("#divTransactions").hide();

            $("#loading").show();
            $.ajax({
                url: base_url + "/get_ledger_details_by_buyer?buyerId=" + $("#buyer_name").val() + "&originId=" + $("#origin_ledger").val(),
                cache: false,
                method: "GET",
                dataType: "json",
                success: function(JSON) {
                    $("#loading").hide();
                    if (JSON.redirect == true) {
                        window.location.replace(login_url);
                    } else if (JSON.result != '') {

                        $("#txtTotalShipments").text(JSON.result["totalShipments"]);
                        $("#txtTotalContainers").text(JSON.result["totalContainers"]);
                        $("#txtTotalVolume").text(JSON.result["totalVolume"]);
                        $("#txtTotalPieces").text(JSON.result["totalPieces"]);
                        $("#txtTotalWeight").text(JSON.result["totalWeight"]);

                        $("#txtSalesValue").text(JSON.result["totalSalesCost"]);
                        $("#txtTotalAdvanceCost").text(JSON.result["totalAdvanceCost"]);
                        $("#txtTotalServiceCost").text(JSON.result["totalServiceCost"]);
                        $("#txtClaimCost").text(JSON.result["totalClaimCost"]);
                        $("#txtInvoiceValue").text(JSON.result["totalInvoiceValue"]);

                        parseDebitTransactions(JSON.result["debitTransactions"]);

                        $("#divInventoryLedgers").show();
                        $("#divTransactions").show();
                    } else {
                        toastr.clear();
                        toastr.error(JSON.error);
                    }
                }
            });

        });

        $("#btn_reset").click(function() {
            $("#buyer_name").select2("val", "0");
        });

        $("#btn_download_reports").click(function() {
            if ($("#buyer_name").val() == 0) {

                toastr.clear();
                toastr.warning(error_select_name);

            } else {

                toastr.clear();
                toastr.info(processing_request);

                $("#loading").show();
                $.ajax({
                    url: base_url + "/generate_buyer_ledger?&buyerId=" + $("#buyer_name").val(),
                    cache: false,
                    method: "GET",
                    dataType: "json",
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
            }
        });
    });

    function deletefilesfromfolder() {
        $.ajax({
            type: "GET",
            url: base_url + "/deletefilesfromfolder",
            contentType: false,
            cache: false,
            processData: false,
            success: function(JSON) {
                //$("#loading").hide();
            }
        });
    }

    function wait(ms) {
        var start = new Date().getTime();
        var end = start;
        while (end < start + ms) {
            end = new Date().getTime();
        }
    }

    function parseCreditTransactions(credittransactions) {
        $('#xin_table_credits').DataTable({
            "bDestroy": true,
            "lengthMenu": [
                [50, 100, 200, -1],
                [50, 100, 200, "All"]
            ],
            data: credittransactions,
            columns: [{
                data: 'action'
            }, {
                data: 'transactionDate'
            }, {
                data: 'amount'
            }, {
                data: 'fullName'
            }],
            //dom: 'lBfrtip',
            "sScrollX": "100%",
            "scrollCollapse": true,
            "bPaginate": true,
            "sPaginationType": "full_numbers",
            paging: true,
            searching: true,
            fixedColumns: true,
            responsive: true,
            "columnDefs": [{
                "searchable": true,
                "orderable": true,
                "targets": 1,
                "type": 'date'
            }],
            "order": [
                [0, "asc"]
            ],
            "language": {
                "url": datatable_language
            }
        });
    }

    function parseDebitTransactions(debittransactions) {
        setTimeout(function() {
            $('#xin_table_debits').DataTable({
                "bDestroy": true,
                "lengthMenu": [
                    [50, 100, 200, -1],
                    [50, 100, 200, "All"]
                ],
                data: debittransactions,
                columns: [{
                    data: 'saNumber'
                }, {
                    data: 'containers'
                }, {
                    data: 'pieces'
                }, {
                    data: 'volume'
                }, {
                    data: 'weight'
                }, {
                    data: 'salescost'
                }, {
                    data: 'servicecost'
                }, {
                    data: 'advancecost'
                }, {
                    data: 'claimcost'
                }, {
                    data: 'invoicevalue'
                }, {
                    data: 'origin'
                }],
                //dom: 'lBfrtip',
                "sScrollX": "100%",
                "scrollCollapse": true,
                "bPaginate": true,
                "sPaginationType": "full_numbers",
                paging: true,
                searching: true,
                "ordering": true,
                fixedColumns: true,
                responsive: true,
                "columnDefs": [{
                    "searchable": true,
                    "orderable": true,
                    "targets": 0,
                    "type": 'date'
                }],
                "order": [
                    [0, "asc"]
                ],
                "language": {
                    "url": datatable_language
                }
            });

            jQuery('.dataTable').wrap('<div class="dataTables_scroll" />');
        }, 1800);
    }
</script>