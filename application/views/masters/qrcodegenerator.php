<?php
$session = $this->session->userdata('fullname');
$applicable_origins = $session["applicable_origins"];
?>
<?php $site_lang = $this->load->helper('language'); ?>
<?php $wz_lang = $site_lang->session->userdata('site_lang'); ?>
<?php $attributes = array('name' => 'qr_code_generator', 'id' => 'qr_code_generator', 'autocomplete' => 'off', 'class' => '"m-b-1'); ?>
<?php $hidden = array('_method' => 'post'); ?>
<?php echo form_open_multipart('qrcodegenerator/generateqrcode', $attributes, $hidden); ?>
<div class="card mb-3">
	<div class="card-header table-responsive">
		<div class="row flex-between-end">
			<div class="col-auto align-self-center">
				<h3> <?php echo $this->lang->line('qrcode_title'); ?> </h3>
			</div>
		</div>
	</div>
	<div class="card-body pt-0">

		<div class="row mb-4">
			<div class="col-md-4 align-self-center">
				<label for="origin_qrcode"><?php echo $this->lang->line("origin"); ?></label>
				<select class="form-control" name="origin_qrcode" id="origin_qrcode" data-plugin="select_erp">
					<?php if (count($applicable_origins) > 1) { ?>
						<option value="0"><?php echo $this->lang->line('all'); ?></option>
						<?php foreach ($applicable_origins as $origin) { ?>
							<option value="<?php echo $origin->id; ?>"><?php echo $origin->origin_name; ?></option>
						<?php } ?>
					<?php } else { ?>
						<?php foreach ($applicable_origins as $origin) { ?>
							<option value="<?php echo $origin->id; ?>" selected="selected"><?php echo $origin->origin_name; ?></option>
						<?php } ?>
					<?php } ?>
				</select>
				<label id="error-origin" style="display: none;" class="error-text"><?php echo $this->lang->line('error_origin_screen'); ?></label>
			</div>
		</div>

		<div class="row mb-4">
			<div class="col-md-4 align-self-center">
				<label for="generate_type"><?php echo $this->lang->line('generate_type'); ?></label>
				<select class="form-control" name="generate_type" id="generate_type" data-plugin="select_erp" <?php if (count($applicable_origins) > 1) : ?> disabled title="<?php echo $this->lang->line('enable_origin'); ?>" <?php endif; ?>>
					<option value="0"><?php echo $this->lang->line("select"); ?></option>
					<option value="1"><?php echo $this->lang->line('bulk'); ?></option>
					<option value="2"><?php echo $this->lang->line('single'); ?></option>
				</select>
				<label id="error-generatetype" style="display: none;" class="error-text"><?php echo $this->lang->line('error_generatetype'); ?></label>
			</div>
			<div class="col-md-4 align-self-center" id="divqrcode" style="display: none;">
				<label for="input_qrcode" id="lblqrcode"><?php echo $this->lang->line('number_of_qr_code'); ?></label>
				<input class="form-control" placeholder="<?php echo $this->lang->line('number_of_qr_code'); ?>" name="input_qrcode" id="input_qrcode" type="text">
				<label id="error-qrcode" style="display: none;" class="error-text"><?php echo $this->lang->line('error_value'); ?></label>
			</div>
			<div class="col-md-4 align-self-center" id="divshowcode" style="display: none;">
				<label for="last_qrcode"><?php echo $this->lang->line('last_generated_qrcode'); ?></label>
				<input class="form-control" name="last_qrcode" id="last_qrcode" type="text" disabled value="<?php echo $this->lang->line('last_generated_qrcode'); ?>">
			</div>
		</div>

		<div class="row mb-5">
			<div class="col-md-6 align-self-center">
				<?php echo form_button(array('name' => 'cgrerp_form_origin', 'id' => 'generate', 'type' => 'submit', 'class' => 'btn btn-success generate', 'content' => $this->lang->line('generate'))); ?>
			</div>
		</div>

		<div class="row mb-3">
			<div class="col-auto align-self-center list_title">
				<h5> <?php echo $this->lang->line('available_qrcode'); ?> </h5>
			</div>
		</div>
		<table class="datatables-demo table table-striped table-bordered xin_table_qrcodes" id="xin_table_qrcodes" style="width: 100% !important;">

			<thead>
				<tr>
					<th><?php echo $this->lang->line('action'); ?></th>
					<th><?php echo $this->lang->line('qrcode_ranges'); ?></th>
					<th><?php echo $this->lang->line('number_of_qr_code'); ?></th>
					<th><?php echo $this->lang->line('origin'); ?></th>
					<th><?php echo $this->lang->line('created_by'); ?></th>
				</tr>
			</thead>

		</table>
	</div>
</div>
<?php echo form_close(); ?>
<script src="<?php echo base_url() . 'assets/js/jquery341.min.js'; ?>"></script>

<script src="<?php echo base_url() . 'assets/js/jquery.dataTables.min.js'; ?>"></script>
<script src="<?php echo base_url() . 'assets/js/dataTables.bootstrap.min.js'; ?>"></script>

<script type="text/javascript">

	var number_of_qr_code = "<?php echo $this->lang->line('number_of_qr_code'); ?>";
	var qr_code_number = "<?php echo $this->lang->line('qr_code_number'); ?>";

	$(document).ready(function() {

		$("#error-origin").hide();
		$("#error-qrcode").hide();
		$("#error-generatetype").hide();

		$("#qr_code_generator").submit(function(e) {

			e.preventDefault();

			var isValid1 = true,
				isValid2 = true,
				isValid3 = true,
				isValid4 = true;

			var origin_id = $("#origin_qrcode").val();
			var generate_type = $("#generate_type").val();
			var input_qrcode = $("#input_qrcode").val().trim();

			var fd = new FormData(this);

			if (origin_id == 0) {
				isValid1 = false;
				$("#error-origin").show();
			} else {
				isValid1 = true;
				$("#error-origin").hide();
			}

			if (generate_type == 0) {
				isValid2 = false;
				$("#error-generatetype").show();
			} else if (generate_type == 1) {
				isValid2 = true;
				$("#error-generatetype").hide();

				if (input_qrcode.length == 0) {
					isValid3 = false;
					$("#error-qrcode").text("<?php echo $this->lang->line('error_value'); ?>");
					$("#error-qrcode").show();
				} else {
					isValid3 = true;
					$("#error-qrcode").hide();

					if (input_qrcode == 0) {
						isValid3 = false;
						$("#error-qrcode").text("<?php echo $this->lang->line('error_zero_value'); ?>");
						$("#error-qrcode").show();
					} else {
						isValid3 = true;
						$("#error-qrcode").hide();
					}

					if (input_qrcode > 500) {
						isValid4 = false;
						$("#error-qrcode").text("<?php echo $this->lang->line('qr_code_limit'); ?>");
						$("#error-qrcode").show();
					} else {
						isValid4 = true;
						$("#error-qrcode").hide();
					}

					if (isValid1 && isValid2 && isValid3 && isValid4) {

						$("#loading").show();
						fd.append("generatetype", generate_type);
						fd.append("originid", origin_id);
						fd.append("inputqrcode", input_qrcode);
						fd.append("add_type", "qrcode");

						toastr.info(processing_request);
						var obj = $(this),
							action = obj.attr('name'),
							form_table = obj.data('form-table');

						$('.generate').prop('disabled', true);

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
									$('.generate').prop('disabled', false);
									$('input[name="csrf_cgrerp"]').val(JSON.csrf_hash);
								} else {
									toastr.clear();
									toastr.success(JSON.result);
									$('.generate').prop('disabled', false);
									$('input[name="csrf_cgrerp"]').val(JSON.csrf_hash);
									$("#input_qrcode").val("");
									getlastgeneratedqrcode();
									downloadfile(JSON.downloadfile);
									$('#xin_table_qrcodes').DataTable().ajax.reload(null, false);
								}
							}
						});
					}
				}
			} else if (generate_type == 2) {
				isValid2 = true;
				$("#error-generatetype").hide();

				if (input_qrcode.length == 0) {
					isValid3 = false;
					$("#error-qrcode").text("<?php echo $this->lang->line('error_value'); ?>");
					$("#error-qrcode").show();
				} else {
					isValid3 = true;
					$("#error-qrcode").hide();

					if (input_qrcode == 0) {
						isValid3 = false;
						$("#error-qrcode").text("<?php echo $this->lang->line('error_zero_value'); ?>");
						$("#error-qrcode").show();
					} else {
						isValid3 = true;
						$("#error-qrcode").hide();
					}
				}

				if (isValid1 && isValid2 && isValid3) {

					$("#loading").show();
					fd.append("generatetype", generate_type);
					fd.append("originid", origin_id);
					fd.append("inputqrcode", input_qrcode);
					fd.append("add_type", "qrcode");

					toastr.info(processing_request);
					var obj = $(this),
						action = obj.attr('name'),
						form_table = obj.data('form-table');

					$('.generate').prop('disabled', true);

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
								$('.generate').prop('disabled', false);
								$('input[name="csrf_cgrerp"]').val(JSON.csrf_hash);
							} else {
								toastr.clear();
								toastr.success(JSON.result);
								$('.generate').prop('disabled', false);
								$('input[name="csrf_cgrerp"]').val(JSON.csrf_hash);
								$("#input_qrcode").val("");
								getlastgeneratedqrcode();
								downloadfile(JSON.downloadfile);
								$('#xin_table_qrcodes').DataTable().ajax.reload(null, false);
							}
						}
					});
				}
			}
		});

		$(document).on('click', 'button[data-role=download_pdf]', function() {
			$("#loading").show();
			var id = $(this).data('qrcode_id');
			toastr.info(processing_request);
			$.ajax({
				url: base_url + "/downloadfile",
				type: "GET",
				data: 'jd=1&is_ajax=3&mode=modal&type=download_pdf&qrcode_id=' + id,
				success: function(response) {
					$("#loading").hide();
					if (response.redirect == true) {
						window.location.replace(login_url);
					} else if (response.error != "") {
						toastr.clear();
						toastr.error(response.error);
					} else {
						toastr.clear();
						toastr.success(response.result);
						$('.generate').prop('disabled', false);
						$('input[name="csrf_cgrerp"]').val(response.csrf_hash);
						$("#input_qrcode").val("");
						getlastgeneratedqrcode();
						downloadfile(response.downloadfile);
						$('#xin_table_qrcodes').DataTable().ajax.reload(null, false);
					}
				}
			});
		});

		$(document).on('click', 'button[data-role=download_zip]', function() {
			$("#loading").show();
			var id = $(this).data('qrcode_id');
			toastr.info(processing_request);
			$.ajax({
				url: base_url + "/downloadfile",
				type: "GET",
				data: 'jd=1&is_ajax=3&mode=modal&type=download_zip&qrcode_id=' + id,
				success: function(response) {
					$("#loading").hide();
					if (response.redirect == true) {
						window.location.replace(login_url);
					} else if (response.error != "") {
						toastr.clear();
						toastr.error(response.error);
					} else {
						toastr.clear();
						toastr.success(response.result);
						$('.generate').prop('disabled', false);
						$('input[name="csrf_cgrerp"]').val(response.csrf_hash);
						$("#input_qrcode").val("");
						downloadfile(response.downloadfile);
						$('#xin_table_qrcodes').DataTable().ajax.reload(null, false);
					}
				}
			});
		});
	});

	function downloadfile(downloadurl) {
		$.ajax({
			url: downloadurl,
			method: 'GET',
			xhrFields: {
				responseType: 'blob'
			},
			success: function(data) {
				var filename = downloadurl.substring(downloadurl.lastIndexOf('/') + 1);
				var a = document.createElement('a');
				var url = window.URL.createObjectURL(data);
				a.href = url;
				a.download = filename;
				document.body.append(a);
				a.click();
				a.remove();
				window.URL.revokeObjectURL(url);
				deletefilesfromfolder();
			}
		});
	}

	function deletefilesfromfolder() {
		$.ajax({
			type: "GET",
			url: base_url + "/deletefilesfromfolder",
			contentType: false,
			cache: false,
			processData: false,
			success: function(JSON) {
				$("#loading").hide();
			}
		});
	}
</script>