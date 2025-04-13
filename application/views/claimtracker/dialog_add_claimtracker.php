<?php
$session = $this->session->userdata('fullname');
$applicable_origins = $session["applicable_origins"];
?>
<?php $site_lang = $this->load->helper('language'); ?>
<?php $wz_lang = $site_lang->session->userdata('site_lang'); ?>
<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<div class="modal-header">
	<h4 class="modal-title" id="add-modal-data"><?php echo $pageheading; ?></h4>
	<?php echo form_button(array('aria-label' => 'Close', 'data-bs-dismiss' => 'modal', 'type' => 'button', 'class' => 'close', 'content' => '<span aria-hidden="true">×</span>')); ?>

</div>
<?php $attributes = array('name' => 'add_claim', 'id' => 'add_claim', 'autocomplete' => 'off', 'class' => '"m-b-1'); ?>
<?php $hidden = array('_method' => $pagetype); ?>
<?php echo form_open_multipart('claimtracker/add', $attributes, $hidden); ?>
<div class="modal-body">
	<input type="hidden" id="pagetype" name="pagetype" value="<?php echo $pagetype; ?>">
	<input type="hidden" id="hdnclaimid" name="hdnclaimid" value="<?php echo $claimid;  ?>">

	<div class="row mb-3 div-row-center">

		<label class="col-sm-3 col-form-label lbl-font header-profile-menu1 fontsize" for="origin">
			<?php echo $this->lang->line('origin'); ?>
		</label>

		<div class="col-sm-7">
			<select class="form-control" name="origin" id="origin" data-plugin="select_erp" <?php if ($pagetype == "edit") { ?> disabled <?php } ?>>
				<option value="0"><?php echo $this->lang->line("select"); ?></option>
				<?php foreach ($applicable_origins as $origin) { ?>
					<?php if ($get_shipping_details[0]->origin_id == $origin->id) { ?>
						<option value="<?php echo $origin->id; ?>" selected="selected"><?php echo $origin->origin_name; ?></option>
					<?php } else { ?>
						<option value="<?php echo $origin->id; ?>"><?php echo $origin->origin_name; ?></option>
					<?php } ?>
				<?php } ?>
			</select>
			<label id="error-origin" class="error-text"><?php echo $this->lang->line('error_origin_screen'); ?></label>
		</div>
	</div>

	<div class="row mb-3 div-row-center">

		<label class="col-sm-3 col-form-label lbl-font header-profile-menu1 fontsize" for="sanumber">
			<?php echo $this->lang->line('sa_number'); ?>
		</label>

		<div class="col-sm-7">
			<select class="form-control" name="sa_number" id="sa_number" data-plugin="select_erp" <?php if ($pagetype == "edit") { ?> disabled <?php } ?>>
				<option value="0"><?php echo $this->lang->line("select"); ?></option>
			</select>
			<label id="error-sanumber" class="error-text"><?php echo $this->lang->line('error_select_sanumber'); ?></label>
		</div>
	</div>

	<div class="row mb-3 div-row-center">

		<label class="col-sm-3 col-form-label lbl-font header-profile-menu1 fontsize" for="claim_amount">
			<?php echo $this->lang->line('claim_amount'); ?>
		</label>

		<div class="col-sm-7">
			<input type="number" id="claim_amount" step="any" maxlength="10" name="claim_amount" class="form-control">
			<label id="error-claimamount" class="error-text"><?php echo $this->lang->line('error_value'); ?></label>
		</div>
	</div>

	<div class="row mb-3 div-row-center">
		<label class="col-sm-3 col-form-label lbl-font header-profile-menu1 fontsize" for="claim_remarks">
			<?php echo $this->lang->line('claim_remarks'); ?>
		</label>

		<div class="col-sm-7">
			<textarea name="claim_remarks" id="claim_remarks" maxlength="150" rows="3" class="form-control text-capitalize"></textarea>
			<label id="error-claimremarks" class="error-text"><?php echo $this->lang->line('error_enter_remarks'); ?></label>
		</div>
	</div>
</div>
<div class="modal-footer">
	<?php echo form_button(array('data-bs-dismiss' => 'modal', 'type' => 'button', 'class' => 'btn btn-secondary', 'content' => $this->lang->line('close'))); ?>
	<?php echo form_button(array('name' => 'cgrerp_form_origin', 'type' => 'submit', 'class' => 'btn btn-success addclaim', 'content' => $pagetype == 'edit' ? $this->lang->line('update') : $this->lang->line('add'))); ?>
</div>
<?php echo form_close(); ?>
<link rel="stylesheet" href="<?php echo base_url() . 'assets/css/bootstrap-multiselect.css'; ?>">
<script src="<?php echo base_url() . 'assets/js/bootstrap-multiselect.js'; ?>"></script>

<script type="text/javascript">
	$(document).ready(function() {

		var error_value = "<?php echo $this->lang->line("error_value"); ?>";
    	var error_zero_value = "<?php echo $this->lang->line("error_zero_value"); ?>";

		$("#error-origin").hide();
		$("#error-sanumber").hide();
		$("#error-claimremarks").hide();
		$("#error-claimamount").hide();

		$("#add_claim").submit(function(e) {
			e.preventDefault();
			var pagetype = $("#pagetype").val().trim();
			var claimid = $("#hdnclaimid").val().trim();
			var origin = $("#origin").val().trim();
			var sa_number = $("#sa_number").val();

			var sa_number_text = "";
			var data = $("#sa_number").select2('data');
			if(data) {
				sa_number_text = data[0].text;
			}

			var claim_amount = $("#claim_amount").val();
			var claim_remarks = $("#claim_remarks").val();

			var isValid1 = true,
				isValid2 = true, isValid3 = true, isValid4 = true, isValid5 = true;

			if (origin == 0) {
				$("#error-origin").show();
				isValid1 = false;
			} else {
				$("#error-origin").hide();
				isValid1 = true;
			}

			if (sa_number == 0) {
				$("#error-sanumber").show();
				isValid2 = false;
			} else {
				$("#error-sanumber").hide();
				isValid2 = true;
			}

			if (claim_amount.length == 0) {
                $("#error-claimamount").show();
                $("#error-claimamount").text(error_value);
                isValid3 = false;
            } else {
                $("#error-claimamount").hide();
                $("#error-claimamount").text("");
                isValid3 = true;
            }

            if (isValid3 == true) {
                if (claim_amount == 0) {
                    $("#error-claimamount").show();
                    $("#error-claimamount").text(error_zero_value);
                    isValid5 = false;
                } else {
                    $("#error-claimamount").hide();
                    $("#error-claimamount").text("");
                    isValid5 = true;
                }
            }

			if (claim_remarks.length == 0) {
				$("#error-claimremarks").show();
				isValid4 = false;
			} else {
				$("#error-claimremarks").hide();
				isValid4 = true;
			}

			if (isValid1 && isValid2 && isValid3 && isValid4 && isValid5) {

				var fd = new FormData(this);
				fd.append("origin", origin);
				fd.append("is_ajax", 2);
				fd.append("form", action);
				fd.append("add_type", "invoiceclaim");
				fd.append("action_type", pagetype);
				fd.append("claim_id", claimid);
				fd.append("sa_number", sa_number);
				fd.append("sa_number_text", sa_number_text);
				fd.append("claim_amount", claim_amount);
				fd.append("claim_remarks", claim_remarks);

				$(".addclaim").prop('disabled', true);
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
							$('.addclaim').prop('disabled', false);
							$('input[name="csrf_cgrerp"]').val(JSON.csrf_hash);
						} else {
							toastr.clear();
							toastr.success(JSON.result);
							$('.addclaim').prop('disabled', false);
							$('input[name="csrf_cgrerp"]').val(JSON.csrf_hash);
							$("#add-modal-data-lg-bd").modal('hide');

							$('#xin_table_claimtracker').DataTable().ajax.reload(null, false);
						}
					}
				});
			}
		});

		$("#origin").change(function() {
			if ($("#origin").val() == 0) {
				fetchSANumber(0);
				$("#error-origin").hide();
			} else {
				fetchSANumber($("#origin").val());
				$("#error-sanumber").hide();
				$("#error-origin").hide();
			}
		});

		function fetchSANumber(originid) {
			$("#loading").show();
			$.ajax({
				url: base_url + "/fetch_sanumbers?originid=" + originid,
				cache: false,
				method: "GET",
				dataType: 'json',
				success: function(JSON) {
					$("#loading").hide();
					if (JSON.redirect == true) {
						window.location.replace(login_url);
					} else if (JSON.result != '') {
						$("#sa_number").select2({
							dropdownCssClass: "myFont",
							dropdownParent: $('#ajax_modal_lg_bd')
						});
						$("#sa_number").empty();
						$("#sa_number").append(JSON.result);
					}
				}
			});
		}
	});
</script>