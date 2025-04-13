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
                <h3> <?php echo $this->lang->line('purchase_manager_ledger_header'); ?> </h3>
            </div>
        </div>
    </div>
    <div class="card-body pt-5">
        <div class="mb-3 row">
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

            <label class="col-sm-2 col-form-label lbl-font" for="purchasemanager_name_ledger"><?php echo $this->lang->line('purchasemanagername_title'); ?></label>
            <div class="col-sm-10">
                <select class="form-control" name="purchasemanager_name_ledger" id="purchasemanager_name_ledger" data-plugin="select_erp">
                    <option value="0"><?php echo $this->lang->line('select'); ?></option>
                </select>
                <div class="mb-4 row"></div>
            </div>

            <div class="row flex-between-end">
                <div class="col-md-10 ms-auto">
                    <button class="btn btn-primary btn-block" title="<?php echo $this->lang->line("download_reports"); ?>" type="button" id="btn_download_reports">
                        <span class="ms-1"><?php echo $this->lang->line("download_reports"); ?></span></button>

                    <button class="btn btn-danger btn-block ml-10" title="<?php echo $this->lang->line("reset"); ?>" type="button" id="btn_reset">
                        <span class="ms-1"><?php echo $this->lang->line("reset"); ?></span></button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-3" id="divInventoryLedgers">
    <div class="card-body">
        <div class="row">
            <div class="col-lg-4 border-lg-end border-bottom border-lg-0 pb-3 pb-lg-0">
                <div class="d-flex flex-between-center mb-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-item icon-item-md bg-soft-primary shadow-none me-2 bg-soft-primary"><span class="fs-0 fas fa-download text-primary"></span></div>
                        <h6 class="mb-0"><?php echo $this->lang->line("total_credit"); ?></h6>
                    </div>
                </div>
                <div class="d-flex">
                    <div class="d-flex">
                        <p class="font-sans-serif lh-1 mb-1 fs-2 fw-medium pe-2" id="txtTotalCredits"></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 border-lg-end border-bottom border-lg-0 py-3 py-lg-0">
                <div class="d-flex flex-between-center mb-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-item icon-item-md bg-soft-primary shadow-none me-2 bg-soft-info"><span class="fs-0 fas fa-upload text-info"></span></div>
                        <h6 class="mb-0"><?php echo $this->lang->line("total_debit"); ?></h6>
                    </div>
                </div>
                <div class="d-flex">
                    <div class="d-flex">
                        <p class="font-sans-serif lh-1 mb-1 fs-2 fw-medium pe-2" id="txtTotalDebits"></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 pt-3 pt-lg-0">
                <div class="d-flex flex-between-center mb-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-item icon-item-md bg-soft-primary shadow-none me-2 bg-soft-success"><span class="fs-0 fas fa-dollar text-success"></span></div>
                        <h6 class="mb-0"><?php echo $this->lang->line("total_outstanding"); ?></h6>
                    </div>
                </div>
                <div class="d-flex">
                    <div class="d-flex">
                        <p class="font-sans-serif lh-1 mb-1 fs-2 fw-medium pe-2" id="txtTotalOutstanding"></p>

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
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading1">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="true" aria-controls="collapse1">
                                    <?php echo $this->lang->line('credit_transaction'); ?>
                                </button>
                            </h2>
                            <div class="accordion-collapse collapse" id="collapse1" aria-labelledby="heading1" data-bs-parent="#accordionExample">
                                <div class="accordion-body table-responsive">
                                    <table class="datatables-demo table table-responsive table-striped table-bordered" id="xin_table_credits" style="width: 100% !important;">

                                        <thead>
                                            <tr>
                                                <th width="100px"><?php echo $this->lang->line("action"); ?></th>
                                                <th><?php echo $this->lang->line("transaction_date"); ?></th>
                                                <th><?php echo $this->lang->line("amount"); ?></th>
                                                <th><?php echo $this->lang->line("credited_by"); ?></th>
                                            </tr>
                                        </thead>

                                    </table>
                                </div>
                            </div>
                        </div>
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
                                                <th><?php echo $this->lang->line("transaction_date"); ?></th>
                                                <th><?php echo $this->lang->line("inventory_order"); ?></th>
                                                <th><?php echo $this->lang->line("amount"); ?></th>
                                                <th><?php echo $this->lang->line("ledger_type"); ?></th>
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
    $("#divInventoryLedgers").hide();
    $("#divTransactions").hide();

    $(document).ready(function() {

        $("#origin_ledger").change(function() {

            $("#divInventoryLedgers").hide();
            $("#divTransactions").hide();

            fetchPurchaseManagers($("#origin_ledger").val());
        });

        $("#purchasemanager_name_ledger").change(function() {

            $("#divExpenseLedgers").hide();
            $("#divTransactions").hide();

            $("#loading").show();
            $.ajax({
                url: base_url + "/get_ledger_details_by_purchasemanager?originid=" + $("#origin_ledger").val() + "&purchasemanagerid=" + $("#purchasemanager_name_ledger").val(),
                cache: false,
                method: "GET",
                dataType: "json",
                success: function(JSON) {
                    $("#loading").hide();
                    if (JSON.redirect == true) {
                        window.location.replace(login_url);
                    } else if (JSON.result != '') {

                        $("#txtTotalCredits").text(JSON.result["totalCredits"]);
                        $("#txtTotalDebits").text(JSON.result["totalDebits"]);
                        $("#txtTotalOutstanding").text(JSON.result["totalOutstanding"]);
                        parseCreditTransactions(JSON.result["creditTransactions"]);
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
            $("#origin_ledger").select2("val", "0");
        });
    });

    function fetchPurchaseManagers(originid) {
        $("#loading").show();
        $.ajax({
            url: base_url + "/get_purchasemanager_by_origin?originid=" + originid,
            cache: false,
            method: "GET",
            dataType: "json",
            success: function(JSON) {
                $("#loading").hide();
                if (JSON.redirect == true) {
                    window.location.replace(login_url);
                } else if (JSON.result != '') {
                    $("#purchasemanager_name_ledger").empty();
                    $("#purchasemanager_name_ledger").append(JSON.result);
                }
            }
        });
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
                    data: 'transactionDate'
                }, {
                    data: 'inventoryOrder'
                }, {
                    data: 'amount'
                }, {
                    data: 'transactionType'
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
        }, 2000);
    }
</script>