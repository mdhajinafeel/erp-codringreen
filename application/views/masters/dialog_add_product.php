<?php
$session = $this->session->userdata('fullname');
$applicable_origins = $session["applicable_origins"];
?>
<?php $site_lang = $this->load->helper('language'); ?>
<?php $wz_lang = $site_lang->session->userdata('site_lang'); ?>
<div class="modal-header">
	<h4 class="modal-title" id="add-modal-data"><?php echo $pageheading; ?></h4>
	<?php echo form_button(array('aria-label' => 'Close', 'data-bs-dismiss' => 'modal', 'type' => 'button', 'class' => 'close', 'content' => '<span aria-hidden="true">×</span>')); ?>

</div>
<?php $attributes = array('name' => 'add_product', 'id' => 'add_product', 'autocomplete' => 'off', 'class' => '"m-b-1'); ?>
<?php $hidden = array('_method' => $pagetype); ?>
<?php echo form_open_multipart('products/add', $attributes, $hidden); ?>
<div class="modal-body product-modal">
	<input type="hidden" id="pagetype" name="pagetype" value="<?php echo $pagetype; ?>">
	<input type="hidden" id="hdnproductid" name="hdnproductid" value="<?php echo $productid;  ?>">
	<div class="row">
		<div class="col-md-6">
			<label for="name"><?php echo $this->lang->line('product_name'); ?></label>
			<input class="form-control" placeholder="<?php echo $this->lang->line('product_name'); ?>" name="name" id="name" type="text" value="<?php echo isset($get_product_details[0]->product_name) ? $get_product_details[0]->product_name : ''; ?>">
			<label id="error-name" class="error-text"><?php echo $this->lang->line('error_name'); ?></label>
		</div>
		<div class="col-md-6">
			<label for="description"><?php echo $this->lang->line('product_desc'); ?></label>
			<textarea name="description" id="description" maxlength="400" rows="3" class="form-control" placeholder="<?php echo $this->lang->line('product_desc'); ?>"><?php echo isset($get_product_details[0]->product_desc) ? htmlspecialchars($get_product_details[0]->product_desc) : ''; ?></textarea>
			<label id="error-description" class="error-text"><?php echo $this->lang->line('error_desc'); ?></label>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<label for="producticon"><?php echo $this->lang->line('product_icon'); ?></label>
			<ul>
				<?php foreach ($get_all_producticons as $icon) { ?>
					<li>

						<?php if ($pagetype == 'add') { ?>
							<input type="radio" name="producticon" id="producticons<?php echo $icon->icon_id; ?>" value="<?php echo $icon->icon_id; ?>" <?php if ($icon->icon_id == 1) : ?> checked="checked" <?php endif; ?> />
						<?php } else { ?>
							<input type="radio" name="producticon" id="producticons<?php echo $icon->icon_id; ?>" value="<?php echo $icon->icon_id; ?>" <?php if ($icon->icon_id == $get_product_details[0]->product_icon) : ?> checked="checked" <?php endif; ?> />
						<?php } ?>

						<label for="producticons<?php echo $icon->icon_id; ?>"><img style="width: 80px; height: 80px; background: burlywood;
					padding: 10px;" src="<?php echo base_url() . $icon->icon_name; ?>" /></label>
					</li>

				<?php } ?>
			</ul>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6">
			<label for="origin"><?php echo $this->lang->line('origin'); ?></label>
			<select class="form-control" name="origin" id="origin" data-plugin="select_erp">
				<option value="0"><?php echo $this->lang->line("select"); ?></option>
				<?php foreach ($applicable_origins as $origin) { ?>
					<?php if ($get_product_details[0]->origin_id == $origin->id) { ?>
						<option value="<?php echo $origin->id; ?>" selected="selected"><?php echo $origin->origin_name; ?></option>
					<?php } else { ?>
						<option value="<?php echo $origin->id; ?>"><?php echo $origin->origin_name; ?></option>
					<?php } ?>
				<?php } ?>
			</select>
			<label id="error-origin" class="error-text"><?php echo $this->lang->line('error_origin_screen'); ?></label>
		</div>
		<div class="col-md-6">
			<label for="status"><?php echo $this->lang->line('status'); ?></label>
			<select class="form-control" name="status" id="status" data-plugin="select_erp">
				<?php if ($pagetype == 'add') { ?>
					<option value="1"><?php echo $this->lang->line('active'); ?></option>
					<option value="0"><?php echo $this->lang->line('inactive'); ?></option>
				<?php } else { ?>
					<option value="1" <?php if ($get_product_details[0]->isactive == 1) : ?> selected="selected" <?php endif; ?>><?php echo $this->lang->line('active'); ?></option>
					<option value="0" <?php if ($get_product_details[0]->isactive == 0) : ?> selected="selected" <?php endif; ?>><?php echo $this->lang->line('inactive'); ?></option>
				<?php } ?>
			</select>
		</div>
	</div>
</div>
<div class="modal-footer">
	<?php echo form_button(array('data-bs-dismiss' => 'modal', 'type' => 'button', 'class' => 'btn btn-secondary', 'content' => $this->lang->line('close'))); ?>
	<?php echo form_button(array('name' => 'cgrerp_form_origin', 'type' => 'submit', 'class' => 'btn btn-success addproduct', 'content' => $pagetype == 'edit' ? $this->lang->line('update') : $this->lang->line('add'))); ?>
</div>
<?php echo form_close(); ?>
<link rel="stylesheet" href="<?php echo base_url() . 'assets/css/bootstrap-multiselect.css'; ?>">
<script src="<?php echo base_url() . 'assets/js/bootstrap-multiselect.js'; ?>"></script>

<script type="text/javascript">
	var processing_request = "<?php echo $this->lang->line("processing_request") ?>";

	$(document).ready(function() {

		$("#error-name").hide();
		$("#error-description").hide();
		$("#error-origin").hide();

		$("#add_product").submit(function(e) {
			e.preventDefault();
			var pagetype = $("#pagetype").val().trim();
			var productid = $("#hdnproductid").val().trim();
			var name = $("#name").val().trim();
			var description = $("#description").val().trim();
			var product_origin = $("#origin").val();
			var selectedicon = 1;
			$('input[name="producticon"]:checked').each(function() {
				selectedicon = this.value;
			});
			var isValid1 = true,
				isValid2 = true,
				isValid3 = true;

			if (name.length == 0) {
				$("#error-name").show();
				isValid1 = false;
			} else {
				$("#error-name").hide();
				isValid1 = true;
			}

			if (description.length == 0) {
				$("#error-description").show();
				isValid2 = false;
			} else {
				$("#error-description").hide();
				isValid2 = true;
			}

			if (product_origin == 0) {
				$("#error-origin").show();
				isValid3 = false;
			} else {
				$("#error-origin").hide();
				isValid3 = true;
			}

			if (isValid1 && isValid2 && isValid3) {

				var fd = new FormData(this);
				fd.append("name", name);
				fd.append("description", description);
				fd.append("is_ajax", 2);
				fd.append("form", action);
				fd.append("add_type", "products");
				fd.append("action_type", pagetype);
				fd.append("product_id", productid);
				fd.append("selectedicon", selectedicon);
				fd.append("product_origin", product_origin);

				$(".addproduct").prop('disabled', true);
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
							$('.addproduct').prop('disabled', false);
							$('input[name="csrf_cgrerp"]').val(JSON.csrf_hash);
						} else {
							toastr.clear();
							toastr.success(JSON.result);
							$('.addproduct').prop('disabled', false);
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
