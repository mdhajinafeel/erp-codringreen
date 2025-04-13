<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<?php
$session = $this->session->userdata('fullname');
$applicable_origins = $session["applicable_origins"];
?>
<div class="modal-header">
	<h4 class="modal-title" id="add-modal-data"><?php echo $this->lang->line('add_origin'); ?></h4>
	<?php echo form_button(array('aria-label' => 'Close', 'data-bs-dismiss' => 'modal', 'type' => 'button', 'class' => 'close', 'content' => '<span aria-hidden="true">×</span>')); ?>

</div>
<?php $attributes = array('name' => 'add_origin', 'id' => 'add_origin', 'autocomplete' => 'off', 'class' => '"m-b-1'); ?>
<?php $hidden = array('_method' => 'ADD'); ?>
<?php echo form_open('origins/add', $attributes, $hidden); ?>
<div class="modal-body">
	<div class="row">
		<div class="col-md-6">
			<label for="origin_id"><?php echo $this->lang->line('origin_name'); ?></label>
			<select class="form-control" name="origin_id" id="origin_id" data-plugin="select_erp">
				<option value="0"><?php echo $this->lang->line("select"); ?></option>
				<?php foreach ($get_all_countries as $country) { ?>
					<option value="<?php echo $country->id; ?>"> <?php echo $country->name; ?></option>
				<?php } ?>
			</select>
			<label id="error-origin" class="error-text"><?php echo $this->lang->line('error_name'); ?></label>
		</div>
		<div class="col-md-6">
			<label for="status"><?php echo $this->lang->line('status'); ?></label>
			<select class="form-control" name="status" id="status" data-plugin="select_erp">
				<option value="1"><?php echo $this->lang->line('active'); ?></option>
				<option value="0"><?php echo $this->lang->line('inactive'); ?></option>
			</select>
		</div>
	</div>
</div>
<div class="modal-footer">
	<?php echo form_button(array('data-bs-dismiss' => 'modal', 'type' => 'button', 'class' => 'btn btn-secondary', 'content' => $this->lang->line('close'))); ?>
	<?php echo form_button(array('name' => 'cgrerp_form_origin', 'type' => 'submit', 'class' => 'btn btn-success addorigin', 'content' => $this->lang->line('add'))); ?>
</div>
<?php echo form_close(); ?>
<script type="text/javascript">

	$("#error-origin").hide();

	$(document).ready(function() {

		$('#origin_id').on('change', function() {
			if (this.value == 0) {
				$("#error-origin").show();
			} else {
				$("#error-origin").hide();
			}
		});

		$("#add_origin").submit(function(e) {
			e.preventDefault();
			var origin_id = $("#origin_id").val();
			var status = $("#status").val().trim();
			var isValid = true;

			if (origin_id == 0) {
				$("#error-origin").show();
				isValid = false;
			} else {
				$("#error-origin").hide();
				isValid = true;
			}
			if (isValid) {
				//$(".addorigin").prop('disabled', true);
				toastr.info(processing_request);
				var obj = $(this),
					action = obj.attr('name'),
					form_table = obj.data('form-table');

				$("#loading").show();
				
				$.ajax({
					type: "POST",
					url: e.target.action,
					data: obj.serialize() + "&is_ajax=1&add_type=origin&form=" + action,
					cache: false,
					success: function(JSON) {
						$("#loading").hide();
						if (JSON.error != '') {
							toastr.clear();
							toastr.error(JSON.error);
							$('.addorigin').prop('disabled', false);
							$('input[name="csrf_cgrerp"]').val(JSON.csrf_hash);
						} else {
							toastr.clear();
							toastr.success(JSON.result);
							$('.addorigin').prop('disabled', false);
							$('input[name="csrf_cgrerp"]').val(JSON.csrf_hash);
							$("#add-modal-data-lg-bd").modal('hide');

							$('#xin_table').DataTable().ajax.reload(null, false);
						}
					}
				});
			}
		});
	});
</script>
