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
    <input type="hidden" id="hdnCsrf" name="hdnCsrf" value="<?php echo $csrfhash; ?>">
    <div class="row mb-3">
        <div class="col-auto ms-auto mt-2 align-self-center">
            <button class="btn btn-primary btn-md btn-right-margin" title="<?php echo $this->lang->line('download'); ?>" type="button" id="generate_download">
                <span class="fas fa-file-excel"></span><span class="ms-1"><?php echo $this->lang->line('download'); ?></span>
            </button>
        </div>
    </div>
    <div style="overflow-x: auto;">
        <table class="datatables-demo table table-striped table-bordered" id="xin_table_mergeinvoice" style="width: 100% !important;">
            <thead>
                <tr>
                    <th><?php echo $this->lang->line("action"); ?></th>
                    <th><?php echo $this->lang->line("sa_no"); ?></th>
                    <th><?php echo $this->lang->line("seller_name_invoice"); ?></th>
                    <th><?php echo $this->lang->line("buyer_name_invoice"); ?></th>
                    <th><?php echo $this->lang->line("total_containers"); ?></th>
                    <th><?php echo $this->lang->line("total_volume"); ?></th>
                    <th><?php echo $this->lang->line("text_sales_value"); ?></th>
                    <th><?php echo $this->lang->line("text_service_cost"); ?></th>
                    <th><?php echo $this->lang->line("text_advance_cost"); ?></th>
                    <th><?php echo $this->lang->line("text_invoice_cost"); ?></th>
                </tr>
            </thead>

        </table>
    </div>
</div>
<?php echo form_close(); ?>
<script>
    $(document).ready(function() {

        var error_select_invoice = "<?php echo $this->lang->line("error_select_invoice"); ?>"
        var invoicesList = <?php echo $invoiceslist; ?>;

        $("#xin_table_mergeinvoice").DataTable({
            data: invoicesList,
            columns: [{
                    data: "invoice_id",
                    render: function(data) {
                        return '<input type="checkbox" name="merge_invoice_ids[]" value="' + data + '">';
                    }
                },
                {
                    data: "sa_number"
                },
                {
                    data: "seller_name"
                },
                {
                    data: "buyer_name"
                },
                {
                    data: "total_containers"
                },
                {
                    data: "total_volume",
                    render: function(data) {
                        return parseFloat(data).toFixed(3);
                    }
                },
                {
                    data: "total_sales_value",
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                },
                {
                    data: "total_service_cost",
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                },
                {
                    data: "total_advance_cost",
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                },
                {
                    data: "total_invoice_value",
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
            language: {
                url: datatable_language
            }
        });

        $("#generate_download").click(function() {

            toastr.clear();
            var selectedInvoiceIds = $("input[name='merge_invoice_ids[]']:checked").map(function() {
                return this.value;
            }).get().join(",");
            if (selectedInvoiceIds.length == 0) {
                toastr.error(error_select_invoice);
                return false;
            } else {
                $("#loading").show();
                var fd = new FormData();
                fd.append("csrf_cgrerp", $("#hdnCsrf").val());
                fd.append("invoiceIds", $("input[name='merge_invoice_ids[]']:checked").map(function() {
                    return this.value;
                }).get().join(","));
                $.ajax({
                    url: base_url + "/generate_merge_invoice_from_history",
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
                            toastr.success(response.successmessage);
                            $('input[name="csrf_cgrerp"]').val(response.csrf_hash);
                            window.location = response.result;
                        }
                    }
                });

            }
        });
    });
</script>