<?php
defined('BASEPATH') or exit('No direct script access allowed');

$session = $this->session->userdata('fullname');
$applicable_origins = $session["applicable_origins"];
?>
<?php $site_lang = $this->load->helper('language'); ?>
<?php $wz_lang = $site_lang->session->userdata('site_lang'); ?>
<div class="modal-header">
	<h4 class="modal-title" id="add-modal-data"><?php echo $pageheading; ?></h4>
	<?php echo form_button(array('aria-label' => 'Close', 'data-bs-dismiss' => 'modal', 'type' => 'button', 'class' => 'close', 'content' => '<span aria-hidden="true">×</span>')); ?>

</div>
<?php $attributes = array('name' => 'add_user', 'id' => 'add_user', 'autocomplete' => 'off', 'class' => '"m-b-1'); ?>
<?php $hidden = array('_method' => $pagetype); ?>
<?php echo form_open_multipart('users/add', $attributes, $hidden); ?>
<div class="modal-body">
	<input type="hidden" id="pagetype" name="pagetype" value="<?php echo $pagetype; ?>">
	<input type="hidden" id="hdnuserid" name="hdnuserid" value="<?php echo $userid;  ?>">
	<div class="row mb-3">
		<div class="col-md-6 mb-3">
			<label for="name"><?php echo $this->lang->line('name'); ?></label>
			<input class="form-control" placeholder="<?php echo $this->lang->line('name'); ?>" name="name" id="name" type="text" value="<?php echo isset($get_user_details[0]->fullname) ? $get_user_details[0]->fullname : ''; ?>">
			<label id="error-name" class="error-text"><?php echo $this->lang->line('error_name'); ?></label>
		</div>
		<div class="col-md-6">
			<label for="emailid"><?php echo $this->lang->line('emailid'); ?></label>
			<input class="form-control" placeholder="<?php echo $this->lang->line('emailid'); ?>" name="emailid" id="emailid" type="text" value="<?php echo isset($get_user_details[0]->emailid) ? $get_user_details[0]->emailid : ''; ?>">
			<label id="error-validemail" class="error-text"><?php echo $this->lang->line('error_validemail'); ?></label>
		</div>
	</div>
	<div class="row mb-3">
		<div class="col-md-6 mb-3">
			<label for="contactno"><?php echo $this->lang->line('contactno'); ?></label>
			<input class="form-control" placeholder="<?php echo $this->lang->line('contactno'); ?>" name="contactno" id="contactno" type="text" value="<?php echo isset($get_user_details[0]->contactno) ? $get_user_details[0]->contactno : ''; ?>">
		</div>
		<div class="col-md-4">
			<label for="photo"><?php echo $this->lang->line('photo'); ?></label>
			<input class="form-control" name="photo" id="photo" type="file" accept="image/*">
		</div>
		<div class="col-md-2">
			<img id="output" name="output" src="<?php echo isset($get_user_details[0]->profilephoto) ? (($get_user_details[0]->profilephoto != null && $get_user_details[0]->profilephoto != '') ? (base_url() . $get_user_details[0]->profilephoto) : (base_url() . 'assets/img/user_icon.png')) : (base_url() . 'assets/img/user_icon.png') ?>" width="60px" height="60px">
		</div>
	</div>
	<div class="row mb-3">
		<div class="col-md-12 mb-3">
			<label for="address"><?php echo $this->lang->line('address'); ?></label>
			<textarea name="address" id="address" maxlength="400" rows="3" class="form-control" placeholder="<?php echo $this->lang->line('address'); ?>"><?php echo isset($get_user_details[0]->address) ? htmlspecialchars($get_user_details[0]->address) : ''; ?></textarea>
			<label id="error-address" class="error-text"><?php echo $this->lang->line('error_address'); ?></label>
		</div>
	</div>
	<div class="row mb-3">
		<div class="col-md-5 mb-3">
			<label for="username"><?php echo $this->lang->line('username'); ?></label>
			<input class="form-control" placeholder="<?php echo $this->lang->line('username'); ?>" name="username" id="username" type="text" value="<?php echo isset($get_user_details[0]->username) ? $get_user_details[0]->username : ''; ?>">
			<label id="error-username" class="error-text"><?php echo $this->lang->line('error_username'); ?></label>
		</div>
		<div class="col-md-4">
			<label for="password"><?php echo $this->lang->line('password'); ?></label>
			<input class="form-control" placeholder="<?php echo $this->lang->line('password'); ?>" id="password" name="password" type="password" value="<?php echo isset($get_user_details[0]->password) ? $get_user_details[0]->password : ''; ?>" autocomplete="new-password">
			<label id="error-password" class="error-text"><?php echo $this->lang->line('error_password'); ?></label>
		</div>
		<div class="col-md-3 form-check" style="display: flex; align-items: center;">
			<input class="form-check-input" id="showpassword" name="showpassword" type="checkbox" value="" onclick="showPassword();">
			<label for="showpassword"><?php echo $this->lang->line('show_password'); ?></label>
		</div>
	</div>
	<div class="row mb-3">
		<div class="col-md-6 mb-3">
			<label for="roles"><?php echo $this->lang->line('roles'); ?></label>
			<select class="form-control" name="roles[]" id="roles" data-plugin="select_erp" multiple>
				<?php foreach ($get_all_roles as $role) { ?>

					<?php if (isset($get_user_details[0]->role)) { ?>
						<option value="<?php echo $role->roleid; ?>" <?php if (in_array($role->roleid, $get_user_details[0]->role)) : ?> selected="selected" <?php endif; ?>> <?php echo $this->lang->line($role->rolename); ?></option>
					<?php } else { ?>
						<option value="<?php echo $role->roleid; ?>"> <?php echo $this->lang->line($role->rolename); ?></option>
					<?php } ?>
				<?php } ?>
			</select>
			<label id="error-roles" class="error-text"><?php echo $this->lang->line('error_role'); ?></label>
		</div>
		<div class="col-md-6 mb-3">
			<label for="applicableorigins"><?php echo $this->lang->line('applicable_origins'); ?></label>
			<select class="form-control" name="applicableorigins[]" id="applicableorigins" data-plugin="select_erp" multiple>
				<?php foreach ($applicable_origins as $origin) { ?>
					<?php if (isset($get_user_details[0]->role)) { ?>
						<option value="<?php echo $origin->id; ?>" <?php if (in_array($origin->id, $get_user_details[0]->applicable_origins)) : ?> selected="selected" <?php endif; ?>> <?php echo $origin->origin_name; ?></option>
					<?php } else { ?>
						<option value="<?php echo $origin->id; ?>"> <?php echo $origin->origin_name; ?></option>
					<?php } ?>
				<?php } ?>
			</select>
			<label id="error-origins" class="error-text"><?php echo $this->lang->line('error_origin'); ?></label>
		</div>
	</div>
	<div class="row mb-3">
		<div class="col-md-6 mb-3">
			<label for="default_language"><?php echo $this->lang->line('default_language'); ?></label>
			<select class="form-control" name="default_language" id="default_language" data-plugin="select_erp">
				<?php foreach ($get_all_languages as $language) { ?>
					<option value="<?php echo $language->id; ?>" <?php if (isset($get_user_details[0]->default_language) && $get_user_details[0]->default_language == $language->id) { ?> selected="selected" <?php } ?>> <?php echo $this->lang->line($language->language_name); ?></option>
				<?php } ?>
			</select>
		</div>

		<div class="col-md-6">
			<label for="default_timezone"><?php echo $this->lang->line('default_timezone'); ?></label>
			<select class="form-control" name="default_timezone" id="default_timezone" data-plugin="select_erp">
				<?php foreach ($get_all_timezones as $timezone) { ?>
					<option value="<?php echo $timezone->id; ?>" <?php if (isset($get_user_details[0]->default_timezone) && $get_user_details[0]->default_timezone == $timezone->id) { ?> selected="selected" <?php } ?>> <?php echo $timezone->name; ?></option>
				<?php } ?>
			</select>
		</div>
	</div>
	<div class="row mb-3">
		<div class="col-md-6 mb-3">
			<label for="status"><?php echo $this->lang->line('status'); ?></label>
			<select class="form-control" name="status" id="status" data-plugin="select_erp">
				<?php if ($pagetype == 'add') { ?>
					<option value="1"><?php echo $this->lang->line('active'); ?></option>
					<option value="0"><?php echo $this->lang->line('inactive'); ?></option>
				<?php } else { ?>
					<option value="1" <?php if ($get_user_details[0]->isactive == 1) : ?> selected="selected" <?php endif; ?>><?php echo $this->lang->line('active'); ?></option>
					<option value="0" <?php if ($get_user_details[0]->isactive == 0) : ?> selected="selected" <?php endif; ?>><?php echo $this->lang->line('inactive'); ?></option>
				<?php } ?>
			</select>
		</div>
	</div>
</div>
<div class="modal-footer">
	<?php echo form_button(array('data-bs-dismiss' => 'modal', 'type' => 'button', 'class' => 'btn btn-secondary', 'content' => $this->lang->line('close'))); ?>
	<?php echo form_button(array('name' => 'cgrerp_form_origin', 'type' => 'submit', 'class' => 'btn btn-success adduser', 'content' => $pagetype == 'edit' ? $this->lang->line('update') : $this->lang->line('add'))); ?>
</div>
<?php echo form_close(); ?>
<link rel="stylesheet" href="<?php echo base_url() . 'assets/css/bootstrap-multiselect.css'; ?>">
<script src="<?php echo base_url() . 'assets/js/bootstrap-multiselect.js'; ?>"></script>

<script type="text/javascript">
	var inputElement = document.getElementById("photo");
	var selecttext = "<?php echo $this->lang->line("select"); ?>";

	inputElement.onclick = function(event) {
		var target = event.target || event.srcElement;
		if (target.value.length == 0) {
			output.src = "<?php echo base_url() . 'assets/img/user_icon.png'; ?>";
		} else {
			output.src = URL.createObjectURL(event.target.files[0]);
		}
	}

	inputElement.onchange = function(event) {
		var target = event.target || event.srcElement;
		if (target.value.length == 0) {
			output.src = "<?php echo base_url() . 'assets/img/user_icon.png'; ?>";
		} else {
			output.src = URL.createObjectURL(event.target.files[0]);
		}
	}

	function showPassword() {
		var x = document.getElementById("password");
		if (x.type === "password") {
			x.type = "text";
		} else {
			x.type = "password";
		}
	}

	$(document).ready(function() {

		$("#error-name").hide();
		$("#error-address").hide();
		$("#error-username").hide();
		$("#error-password").hide();
		$("#error-roles").hide();
		$("#error-origins").hide();
		$("#error-validemail").hide();

		$('select[multiple]').multiselect({
			placeholder: selecttext,
			search: true,
			selectAll: false,
		});

		$("#add_user").submit(function(e) {
			e.preventDefault();
			var pagetype = $("#pagetype").val().trim();
			var userid = $("#hdnuserid").val().trim();
			var name = $("#name").val().trim();
			var emailid = $("#emailid").val().trim();
			var contactno = $("#contactno").val().trim();
			var address = $("#address").val().trim();
			var username = $("#username").val().trim();
			var password = $("#password").val().trim();
			var roles = $("#roles").val();
			var applicableorigins = $("#applicableorigins").val();
			var isValid1 = true,
				isValid2 = true,
				isValid3 = true,
				isValid4 = true,
				isValid5 = true,
				isValid6 = true,
				isValid7 = true;

			if (name.length == 0) {
				$("#error-name").show();
				isValid1 = false;
			} else {
				$("#error-name").hide();
				isValid1 = true;
			}

			if (address.length == 0) {
				$("#error-address").show();
				isValid2 = false;
			} else {
				$("#error-address").hide();
				isValid2 = true;
			}

			if (username.length == 0) {
				$("#error-username").show();
				isValid3 = false;
			} else {
				$("#error-username").hide();
				isValid3 = true;
			}

			if (password.length == 0) {
				$("#error-password").show();
				isValid4 = false;
			} else {
				$("#error-password").hide();
				isValid4 = true;
			}

			if (roles == "" || roles == null) {
				$("#error-roles").show();
				isValid5 = false;
			} else {
				$("#error-roles").hide();
				isValid5 = true;
			}

			if (applicableorigins == "" || applicableorigins == null) {
				$("#error-origins").show();
				isValid6 = false;
			} else {
				$("#error-origins").hide();
				isValid6 = true;
			}

			if (emailid.length > 0) {
				var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;

				if (!regex.test(emailid)) {
					$("#error-validemail").show();
					isValid7 = false;
				} else {
					$("#error-validemail").hide();
					isValid7 = true;
				}
			}

			if (isValid1 && isValid2 && isValid3 && isValid4 && isValid5 && isValid6 && isValid7) {

				var fd = new FormData(this);
				var files = $('#photo')[0].files[0];
				if (files != null && files != "") {
					fd.append('file', files);
				} else {
					fd.append('file', "");
				}

				fd.append("roles", roles);
				fd.append("applicableorigins", applicableorigins);
				fd.append("is_ajax", 2);
				fd.append("form", action);
				fd.append("add_type", "users");
				fd.append("action_type", pagetype);
				fd.append("user_id", userid);

				$(".adduser").prop('disabled', true);
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
							$('.adduser').prop('disabled', false);
							$('input[name="csrf_cgrerp"]').val(JSON.csrf_hash);
						} else {
							toastr.clear();
							toastr.success(JSON.result);
							$('.adduser').prop('disabled', false);
							$('input[name="csrf_cgrerp"]').val(JSON.csrf_hash);
							$("#add-modal-data-bd").modal('hide');

							$('#xin_table').DataTable().ajax.reload(null, false);
						}
					}
				});
			}
		});
	});
</script>