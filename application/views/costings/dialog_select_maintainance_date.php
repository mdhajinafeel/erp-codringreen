<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<?php $site_lang = $this->load->helper('language'); ?>
<?php $wz_lang = $site_lang->session->userdata('site_lang'); ?>
<div class="modal-header">
    <h4 class="modal-title" id="add-modal-data"><?php echo $pageheading; ?></h4>
    <?php echo form_button(array('aria-label' => 'Close', 'data-bs-dismiss' => 'modal', 'type' => 'button', 'class' => 'close', 'content' => '<span aria-hidden="true">×</span>')); ?>

</div>
<?php $attributes = array('name' => 'update', 'id' => 'update', 'autocomplete' => 'off', 'class' => '"m-b-1'); ?>
<?php $hidden = array('_method' => $pagetype); ?>
<?php echo form_open("", $attributes, $hidden); ?>
<div class="modal-body">
    <input type="hidden" id="pagetype" name="pagetype" value="<?php echo $pagetype; ?>">
    <input type="hidden" id="hdnCsrf" name="hdnCsrf" value="<?php echo $csrf_hash; ?>">
    <input type="hidden" id="hdnOriginId" name="hdnOriginId" value="<?php echo $originId; ?>">

    <div class="row mb-3">
        <div class="col-md-12 mb-2">
            <label class="form-label d-block">
                <?php echo $this->lang->line('date_type'); ?>
            </label>
            <div class="form-check-inline">
                <input class="form-check-input" style="margin-top:0px !important; margin-right:1px !important;" id="year_type" type="radio" name="date_type" value="1" checked />
                <label for="year_type"><?php echo $this->lang->line('year'); ?></label>
            </div>
            <div class="form-check-inline">
                <input class="form-check-input" style="margin-top:0px !important; margin-right:1px !important;" id="date_range_type" type="radio" name="date_type" value="2" />
                <label for="date_range_type"><?php echo $this->lang->line('date_range'); ?></label>
            </div>
        </div>
    </div>

    <div class="row mb-3" id="div_year">
        <div class="col-md-12 mb-2">
            <label for="year_report"><?php echo $this->lang->line('year'); ?></label>
            <select class="form-control select2 form-select" id="year_report">
                <?php
                $sYear = date("Y");
                $eYear = 2023;

                for ($i = $sYear; $i >= $eYear; $i--) {
                    echo '<option value="' . $i . '">' . $i . '</option>';
                }
                ?>
            </select>
        </div>
    </div>

    <div class="row mb-3" id="div_from_date" style="display:none;">
        <div class="col-md-12 mb-2">
            <label for="from_date_report"><?php echo $this->lang->line('from_date'); ?></label>
            <input type="text" id="from_date_report" name="from_date_report" class="form-control from_date_report" readonly />
            <label id="error-fromdate" class="error-text"><?php echo $this->lang->line('error_date'); ?></label>
        </div>
    </div>

    <div class="row mb-3" id="div_to_date" style="display:none;">
        <div class="col-md-12 mb-2">
            <label for="to_date_report"><?php echo $this->lang->line('to_date'); ?></label>
            <input type="text" id="to_date_report" name="to_date_report" class="form-control to_date_report" readonly />
            <label id="error-todate" class="error-text"><?php echo $this->lang->line('error_date'); ?></label>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-12 mb-2">
            <label for="supplier_name_report"><?php echo $this->lang->line('supplier_name'); ?></label>
            <select class="form-control" name="supplier_name" id="supplier_name" data-plugin="select_erp">
                <option value="0"><?php echo $this->lang->line('select'); ?></option>
                <?php foreach ($suppliers as $supplier) { ?>
                    <option value="<?php echo $supplier->id; ?>"><?php echo $supplier->supplier_name; ?></option>
                <?php } ?>
            </select>
        </div>
    </div>
</div>
<div class="modal-footer">
    <?php echo form_button(array('data-bs-dismiss' => 'modal', 'type' => 'button', 'class' => 'btn btn-secondary', 'content' => $this->lang->line("close"))); ?>

    <button type="button" class="btn btn-sm btn-success mb-1" name="btn_generate_report" id="btn_generate_report"><?php echo $this->lang->line("generate"); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    var common_error = "<?php echo $this->lang->line("common_error"); ?>";

    $("#error-fromdate").hide();
    $("#error-todate").hide();

    $(document).ready(function() {

        document.querySelectorAll("input[name='date_type']").forEach((radio) => {
            radio.addEventListener("change", () => {
                let selectedValue = document.querySelector("input[name='date_type']:checked").value;
                if (selectedValue == 1) {
                    $("#div_from_date").hide();
                    $("#div_to_date").hide();
                    $("#div_year").show();
                } else {
                    $("#div_from_date").show();
                    $("#div_to_date").show();
                    $("#div_year").hide();
                }
            });
        });

        $("#btn_generate_report").click(function() {

            var fd = new FormData();
            fd.append("fromDate", $("#from_date_report").val());
            fd.append("toDate", $("#to_date_report").val());
            fd.append("dateType", document.querySelector("input[name='date_type']:checked").value);
            fd.append("year", $("#year_report").val());
            fd.append("originId", $("#hdnOriginId").val());
            fd.append("csrf_cgrerp", $("#hdnCsrf").val());
            fd.append("supplierId", $("#supplier_name").val());

            toastr.clear();
            toastr.info(processing_request);
            $("#loading").show();
            $.ajax({
                type: "POST",
                url: base_url + "/generate_maintainance_report",
                data: fd,
                contentType: false,
                cache: false,
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
                        //wait(3000);
                        //deletefilesfromfolder();
                    }
                }
            });
        });
    });
</script>
<script src="<?php echo base_url() . 'assets/js/i18n/datepicker-' . $wz_lang . '.js'; ?>"></script>
<script type="text/javascript">
    $(function() {
        $(".from_date_report").datepicker({
            dateFormat: "dd/mm/yy",
            changeMonth: true,
            changeYear: true,
            minDate: '-5y',
            maxDate: '5y',
            onSelect: function(date) {
                $('#ui-datepicker-div table td a').attr('href', 'javascript:;');
                var selectedDate = $(".from_date_report").val().split("/");
                var dateval = new Date(selectedDate[1] + "/" + selectedDate[0] + "/" + selectedDate[2]);
                var endDate = new Date(dateval);
                $(".to_date_report").datepicker("option", "minDate", endDate);
            }
        });

        $(".to_date_report").datepicker({
            dateFormat: "dd/mm/yy",
            changeMonth: true,
            changeYear: true,
            minDate: '-5y',
            maxDate: '5y',
            onSelect: function(date) {}
        });

        $('.ui-datepicker').addClass('notranslate');
    });
</script>