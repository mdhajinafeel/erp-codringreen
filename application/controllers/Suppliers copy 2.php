<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Suppliers extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Master_model');
		$this->load->model("Settings_model");
		$this->load->library('excel');
	}

	public function output($Return = array())
	{
		/*Set response header*/
		header("Access-Control-Allow-Origin: *");
		header("Content-Type: application/json; charset=UTF-8");
		/*Final JSON response*/
		exit(json_encode($Return));
	}

	public function index()
	{
		$data['title'] = $this->lang->line('supplier_title') . " - " . $this->lang->line('master_title') .  " | " . $this->Settings_model->site_title();
		$session = $this->session->userdata('fullname');
		if (empty($session)) {
			redirect("/logout");
		}
		$data['path_url'] = 'cgr_masters';
		if (!empty($session)) {
			$data['subview'] = $this->load->view("masters/suppliers", $data, TRUE);
			$this->load->view('layout/layout_main', $data); //page load
		} else {
			redirect("/logout");
		}
	}

	public function supplier_list()
	{
		$data['title'] = $this->lang->line('supplier_title') . " - " . $this->lang->line('master_title') .  " | " . $this->Settings_model->site_title();
		$session = $this->session->userdata('fullname');

		if (empty($session)) {
			redirect("/logout");
		}

		$draw = intval($this->input->get("draw"));
		$originid = intval($this->input->get("originid"));

		if ($originid == 0) {
			$supplier = $this->Master_model->all_suppliers();
		} else {
			$supplier = $this->Master_model->all_suppliers_origin($originid);
		}
		$data = array();

		foreach ($supplier as $r) {

			$editSupplier = '<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('edit') . '"><button type="button" class="btn icon-btn btn-xs btn-edit waves-effect waves-light" data-role="editsupplier" data-supplier_id="' . $r->id . '"><span class="fas fa-pencil"></span></button></span>
			<span data-toggle="tooltip" data-placement="top" title="' . $this->lang->line('view') . '"><button type="button" class="btn icon-btn btn-xs btn-view waves-effect waves-light" data-role="view" data-supplier_id="' . $r->id . '"><span class="fas fa-eye"></span></button></span>';

			if ($r->isactive == 1) {
				$status = $this->lang->line('active');
			} else {
				$status = $this->lang->line('inactive');
			}

			$r->products = str_replace('-----', '<br />', $r->products);

			$supplierName = $r->supplier_name . ' / ' . $r->supplier_id;

			$data[] = array(
				$editSupplier,
				$r->supplier_code,
				$supplierName,
				$r->products,
				$r->roles,
				$r->origin,
				$r->updatedby,
				$status
			);
		}

		$output = array(
			"draw" => $draw,
			"data" => $data
		);
		echo json_encode($output);
		exit();
	}

	public function dialog_supplier_add()
	{
		$session = $this->session->userdata('fullname');
		$Return = array('pages' => '', 'redirect' => false, 'result' => '', 'error' => '', 'csrf_hash' => '');

		$products = $this->Master_model->all_active_products();

		if ($this->input->get('type') == "addsupplier") {
			if (!empty($session)) {
				$data = array(
					'pageheading' => $this->lang->line('add_supplier'),
					'pagetype' => "add",
					'supplierid' => 0,
					'products' => $products,
					'supplier_role_enabled' => false,
					'provider_role_enabled' => false,
				);
				$this->load->view('masters/dialog_add_supplier', $data);
			} else {
				$Return['pages'] = "";
				$Return['redirect'] = true;
				$this->output($Return);
			}
		} else if ($this->input->get('type') == "editsupplier") {

			if (!empty($session)) {

				$getSupplierDetails = $this->Master_model->get_supplier_detail_by_id($this->input->get('sid'));

				$supplierRoleEnabled = false;
				$providerRoleEnabled = false;

				$roles_arr = explode(", ", $getSupplierDetails[0]->roles);

				foreach ($roles_arr as $role) {
					if ($role == "Supplier") {
						$supplierRoleEnabled = true;
					}

					if ($role == "Service Provider") {
						$providerRoleEnabled = true;
					}
				}

				$getSupplierTaxesEdit = $this->Master_model->get_supplier_taxes($this->input->get('sid'));
				$getProviderTaxesEdit = $this->Master_model->get_provider_taxes($this->input->get('sid'));

				$getSupplierTaxes = $this->Master_model->get_supplier_taxes_by_origin($getSupplierDetails[0]->origin_id);

				$supplierTaxes = "";
				foreach ($getSupplierTaxes as $suppliertax) {
					$taxEnabled = false;
					$taxValue = 0;
					foreach($getSupplierTaxesEdit as $suppliertaxedit) {
						if($suppliertax->id + 0 == $suppliertaxedit->tax_id + 0) {
							$taxEnabled = true;
							$taxValue = ($suppliertaxedit->tax_value + 0);
						}
					}

					$supplierTaxes = $supplierTaxes . '<div class="col-md-3">';
					$supplierTaxes = $supplierTaxes . '<div class="input-group">';
					$supplierTaxes = $supplierTaxes . '<div class="form-check">';
					$supplierTaxes = $supplierTaxes . '<input class="form-check-input" id="checksupplier' . $suppliertax->id . '" name="checksupplier' . $suppliertax->id . '" type="checkbox" '.($taxEnabled == true ? "checked" : "").' value="1">';

					if ($suppliertax->number_format == 2) {
						$supplierTaxes = $supplierTaxes . '<label for="checksupplier' . $suppliertax->id . '">' . $suppliertax->tax_name . '(%)' . '</label>';
					} else {
						$supplierTaxes = $supplierTaxes . '<label for="checksupplier' . $suppliertax->id . '">' . $suppliertax->tax_name . '</label>';
					}

					$supplierTaxes = $supplierTaxes . '</div>';
					$supplierTaxes = $supplierTaxes . '<div class="input-group">';

					if($taxEnabled == true) {
						$supplierTaxes = $supplierTaxes . '<input name="supplier' . $suppliertax->id . '" type="number" step="any" maxlength="8" id="supplier' . $suppliertax->id . '" autocomplete="off" class="form-control" value="'.$taxValue.'" />';
					} else {
						$supplierTaxes = $supplierTaxes . '<input name="supplier' . $suppliertax->id . '" type="number" step="any" maxlength="8" id="supplier' . $suppliertax->id . '" autocomplete="off" class="form-control" disabled />';
					}

					$supplierTaxes = $supplierTaxes . '</div>';
					$supplierTaxes = $supplierTaxes . '<label id="error-supplier' . $suppliertax->id . '" class="error-text">' . $this->lang->line("error_value") . '</label>';
					$supplierTaxes = $supplierTaxes . '</div>';
					$supplierTaxes = $supplierTaxes . '</div>';
					$supplierTaxes = $supplierTaxes . '<script type="text/javascript"> $("#error-supplier' . $suppliertax->id . '").hide();';
					$supplierTaxes = $supplierTaxes . '$("#checksupplier' . $suppliertax->id . '").change(function() {';
					$supplierTaxes = $supplierTaxes . 'if (this.checked) {';
					$supplierTaxes = $supplierTaxes . '$("#supplier' . $suppliertax->id . '").removeAttr("disabled", "disabled");';
					$supplierTaxes = $supplierTaxes . '$("#supplier' . $suppliertax->id . '").val("");';
					$supplierTaxes = $supplierTaxes . '} else {';
					$supplierTaxes = $supplierTaxes . '$("#supplier' . $suppliertax->id . '").attr("disabled", "disabled");';
					$supplierTaxes = $supplierTaxes . '$("#supplier' . $suppliertax->id . '").val("");';
					$supplierTaxes = $supplierTaxes . '}';
					$supplierTaxes = $supplierTaxes . '});';
					$supplierTaxes = $supplierTaxes . '</script>';
				}

				$getProviderTaxes = $this->Master_model->get_provider_taxes_by_origin($getSupplierDetails[0]->origin_id);

				$providerTaxes = "";
				foreach ($getProviderTaxes as $providertax) {
					$ptaxEnabled = false;
					$ptaxValue = 0;
					foreach($getProviderTaxesEdit as $providertaxedit) {
						if($providertax->id + 0 == $providertaxedit->tax_id + 0) {
							$ptaxEnabled = true;
							$ptaxValue = ($providertaxedit->tax_value + 0);
						}
					}

					$providerTaxes = $providerTaxes . '<div class="col-md-3">';
					$providerTaxes = $providerTaxes . '<div class="input-group">';
					$providerTaxes = $providerTaxes . '<div class="form-check">';
					$providerTaxes = $providerTaxes . '<input class="form-check-input" id="checkprovider' . $providertax->id . '" name="checkprovider' . $providertax->id . '" type="checkbox" '.($ptaxEnabled == true ? "checked" : "").' value="1">';

					if ($providertax->number_format == 2) {
						$providerTaxes = $providerTaxes . '<label for="checkprovider' . $providertax->id . '">' . $providertax->tax_name . '(%)' . '</label>';
					} else {
						$providerTaxes = $providerTaxes . '<label for="checkprovider' . $providertax->id . '">' . $providertax->tax_name . '</label>';
					}

					$providerTaxes = $providerTaxes . '</div>';
					$providerTaxes = $providerTaxes . '<div class="input-group">';
					if($ptaxEnabled == true) {
						$providerTaxes = $providerTaxes . '<input name="provider' . $providertax->id . '" type="number" step="any" maxlength="8" id="provider' . $providertax->id . '" autocomplete="off" class="form-control" value="'.$ptaxValue.'" />';
					} else {
						$providerTaxes = $providerTaxes . '<input name="provider' . $providertax->id . '" type="number" step="any" maxlength="8" id="provider' . $providertax->id . '" autocomplete="off" class="form-control" disabled />';
					
					}
					$providerTaxes = $providerTaxes . '</div>';
					$providerTaxes = $providerTaxes . '<label id="error-provider' . $providertax->id . '" class="error-text">' . $this->lang->line("error_value") . '</label>';
					$providerTaxes = $providerTaxes . '</div>';
					$providerTaxes = $providerTaxes . '</div>';
					$providerTaxes = $providerTaxes . '<script type="text/javascript"> $("#error-provider' . $providertax->id . '").hide();';
					$providerTaxes = $providerTaxes . '$("#checkprovider' . $providertax->id . '").change(function() {';
					$providerTaxes = $providerTaxes . 'if (this.checked) {';
					$providerTaxes = $providerTaxes . '$("#provider' . $providertax->id . '").removeAttr("disabled", "disabled");';
					$providerTaxes = $providerTaxes . '$("#provider' . $providertax->id . '").val("");';
					$providerTaxes = $providerTaxes . '} else {';
					$providerTaxes = $providerTaxes . '$("#provider' . $providertax->id . '").attr("disabled", "disabled");';
					$providerTaxes = $providerTaxes . '$("#provider' . $providertax->id . '").val("");';
					$providerTaxes = $providerTaxes . '}';
					$providerTaxes = $providerTaxes . '});';
					$providerTaxes = $providerTaxes . '</script>';
				}

				$data = array(
					'pageheading' => $this->lang->line('edit_supplier'),
					'pagetype' => "edit",
					'supplierid' => $getSupplierDetails[0]->id,
					'products' => $products,
					'get_supplier_details' => $getSupplierDetails,
					'supplier_role_enabled' => $supplierRoleEnabled,
					'provider_role_enabled' => $providerRoleEnabled,
					'supplier_bank_details' => $this->Master_model->get_supplierbank_byid($getSupplierDetails[0]->id),
					'supplier_product_details' => $this->Master_model->get_supplierproduct_byid($getSupplierDetails[0]->id),
					'supplier_taxes' => $supplierTaxes,
					'provider_taxes' => $providerTaxes,
					'master_supplier_taxes' => $getSupplierTaxes,
					'master_provider_taxes' => $getProviderTaxes,
				);
				$this->load->view('masters/dialog_add_supplier', $data);
			} else {
				$Return['pages'] = "";
				$Return['redirect'] = true;
				$this->output($Return);
			}
		} else {
			$Return['pages'] = "";
			$Return['redirect'] = true;
			$this->output($Return);
		}
	}

	public function add()
	{
		$Return = array('result' => '', 'error' => '', 'redirect' => false, 'csrf_hash' => '');
		$session = $this->session->userdata('fullname');

		if ($this->input->post('add_type') == 'suppliers') {

			if (!empty($session)) {

				if ($this->input->post('action_type') == 'add') {

					$Return['csrf_hash'] = $this->security->get_csrf_hash();

					$name = $this->input->post('name');
					$supplierid = $this->input->post('supplierid');
					$address = $this->input->post('address');
					$companyname = $this->input->post('companyname');
					$companyid = $this->input->post('companyid');
					$status = $this->input->post('status');
					$supplier_role_enabled = $this->input->post('supplier_role');
					$provider_role_enabled = $this->input->post('provider_role');
					$supplier_origin = $this->input->post('supplier_origin');

					$getCountryCode = $this->Master_model->get_origin_iso3_code($supplier_origin);

					$current_year = date("Y");
					$countrycode = $getCountryCode[0]->origin_iso3_code;
					$companycode = "CGR";
					$generatealpha = $this->supplierCodeSequence();
					$supplier_code = $countrycode . '/' . $companycode . '/' . $current_year . '/' . $generatealpha;

					// $is_supplier_iva_enabled = false;
					// $is_supplier_retention_enabled = false;
					// $is_supplier_retica_enabled = false;
					// $is_provider_iva_enabled = false;
					// $is_provider_retention_enabled = false;
					// $is_provider_retica_enabled = false;

					// $supplier_iva_value = 0;
					// $supplier_retention_value = 0;
					// $supplier_retica_value = 0;
					// $provider_iva_value = 0;
					// $provider_retention_value = 0;
					// $provider_retica_value = 0;

					// if ($this->input->post('supplierivaenabled') == 1) {
					// 	$is_supplier_iva_enabled = true;
					// 	$supplier_iva_value = $this->input->post('supplierivavalue');
					// }

					// if ($this->input->post('supplierretentionenabled') == 1) {
					// 	$is_supplier_retention_enabled = true;
					// 	$supplier_retention_value = $this->input->post('supplierretentionvalue');
					// }

					// if ($this->input->post('supplierreticaenabled') == 1) {
					// 	$is_supplier_retica_enabled = true;
					// 	$supplier_retica_value = $this->input->post('supplierreticavalue');
					// }

					// if ($this->input->post('providerivaenabled') == 1) {
					// 	$is_provider_iva_enabled = true;
					// 	$provider_iva_value = $this->input->post('providerivavalue');
					// }

					// if ($this->input->post('providerretentionenabled') == 1) {
					// 	$is_provider_retention_enabled = true;
					// 	$provider_retention_value = $this->input->post('providerretentionvalue');
					// }

					// if ($this->input->post('providerreticaenabled') == 1) {
					// 	$is_provider_retica_enabled = true;
					// 	$provider_retica_value = $this->input->post('providerreticavalue');
					// }



					if ($status == 0) {
						$status = false;
					} else {
						$status = true;
					}

					$dataSupplier = array(
						"supplier_code" => $supplier_code, "supplier_id" => $supplierid,
						"supplier_name" => $name, "company_name" => $companyname,
						"company_id" => $companyid, "supplier_address" => $address,
						"createdby" => $session['user_id'],
						"updatedby" => $session['user_id'], 'isactive' => $status, 'created_from' => 1,
						'origin_id' => $supplier_origin,
					);

					$insertSupplier = $this->Master_model->add_supplier($dataSupplier);

					if ($insertSupplier > 0) {

						//ROLES
						if ($supplier_role_enabled == 1) {
							$dataSupplierRoles = array(
								"supplier_id" => $insertSupplier, "role_id" => 1,
								"created_by" => $session['user_id'],
								"updated_by" => $session['user_id'], 'is_active' => 1,
							);

							$insertSupplierRoles = $this->Master_model->add_supplier_roles($dataSupplierRoles);

							if ($insertSupplierRoles > 0) {
								$dataSupplierTaxes = array();
								$supplierTaxes = json_decode($this->input->post('arrSupplierTaxes'), true);

								foreach ($supplierTaxes as $suppliertax) {

									if ($suppliertax["taxenabled"] == true) {
										$dataSupplierTaxes[] = array(
											"supplier_id" => $insertSupplier, "tax_id" => $suppliertax["taxid"],
											"tax_value" => $suppliertax["taxvalue"], "created_by" => $session['user_id'],
											"updated_by" => $session['user_id'], "is_active" => 1,
											"created_date" => date('Y-m-d H:i:s'), "updated_date" => date('Y-m-d H:i:s')
										);
									}
								}

								if (count($dataSupplierTaxes) > 0) {
									$insertSupplierTax = $this->Master_model->add_supplier_taxes($dataSupplierTaxes);
								}
							}
						}

						if ($provider_role_enabled == 1) {
							$dataSupplierRoles = array(
								"supplier_id" => $insertSupplier, "role_id" => 2,
								"created_by" => $session['user_id'],
								"updated_by" => $session['user_id'], 'is_active' => 1,
							);

							$insertSupplierRoles = $this->Master_model->add_supplier_roles($dataSupplierRoles);

							if ($insertSupplierRoles > 0) {
								$dataProviderTaxes = array();
								$providerTaxes = json_decode($this->input->post('arrProviderTaxes'), true);

								foreach ($providerTaxes as $providertax) {

									if ($providertax["taxenabled"] == true) {
										$dataProviderTaxes[] = array(
											"supplier_id" => $insertSupplier, "tax_id" => $providertax["taxid"],
											"tax_value" => $providertax["taxvalue"], "created_by" => $session['user_id'],
											"updated_by" => $session['user_id'], "is_active" => 1,
											"created_date" => date('Y-m-d H:i:s'), "updated_date" => date('Y-m-d H:i:s')
										);
									}
								}

								if (count($dataProviderTaxes) > 0) {
									$insertProviderTax = $this->Master_model->add_provider_taxes($dataProviderTaxes);
								}
							}
						}

						//BANK DETAILS
						$array_bank = json_decode($this->input->post('bank_details'), true);
						$bankId = 1;
						foreach ($array_bank as $bank) {
							$dataSupplierBanks = array(
								"supplier_id" => $insertSupplier, "supplier_code" => $supplier_code,
								"bank_sno" => $bankId, "bank_name" => $bank['bankName'],
								"bank_accountnumber" => $bank['bankAccountNumber'], "bank_holdername" => $bank['bankHolderName'],
								"bank_accounttype" => $bank['accountType'], "createdby" => $session['user_id'],
								"updatedby" => $session['user_id'], 'isactive' => 1,
							);

							$insertSupplierBank = $this->Master_model->add_supplier_banks($dataSupplierBanks);
							if ($insertSupplierBank > 0) {
								$bankId++;
							}
						}

						//PRODUCTS
						$array_product = json_decode($this->input->post('product_details'), true);
						$bankId = 1;
						foreach ($array_product as $product) {
							$dataSupplierProducts = array(
								"supplier_id" => $insertSupplier, "supplier_code" => $supplier_code,
								"product_name" => $product['productId'], "createdby" => $session['user_id'],
								"updatedby" => $session['user_id'], 'is_active' => 1,
							);

							$insertSupplierProduct = $this->Master_model->add_supplier_products($dataSupplierProducts);
							if ($insertSupplierProduct > 0) {
								$dataSupplierProductTypes = array(
									"supplier_id" => $insertSupplier, "supplier_code" => $supplier_code,
									"product_id" => $insertSupplierProduct, "product_type_id" => $product['woodType'],
									"createdby" => $session['user_id'],
									"updatedby" => $session['user_id'], 'is_active' => 1,
								);

								$insertSupplierProductType = $this->Master_model->add_supplier_product_types($dataSupplierProductTypes);
							}
						}

						$Return['result'] = $this->lang->line('data_added');
						$this->output($Return);
						exit;
					} else {
						$Return['error'] = $this->lang->line('error_adding');
						$this->output($Return);
						exit;
					}
				} else if ($this->input->post('action_type') == 'edit') {

					$Return['csrf_hash'] = $this->security->get_csrf_hash();

					$supplier_id = $this->input->post('supplier_id');
					$name = $this->input->post('name');
					$supplier_code = $this->input->post('suppliercode');
					$supplierid = $this->input->post('supplierid');
					$address = $this->input->post('address');
					$companyname = $this->input->post('companyname');
					$companyid = $this->input->post('companyid');
					$status = $this->input->post('status');
					$supplier_role_enabled = $this->input->post('supplier_role');
					$provider_role_enabled = $this->input->post('provider_role');
					$supplier_origin = $this->input->post('supplier_origin');

					// $is_supplier_iva_enabled = false;
					// $is_supplier_retention_enabled = false;
					// $is_supplier_retica_enabled = false;
					// $is_provider_iva_enabled = false;
					// $is_provider_retention_enabled = false;
					// $is_provider_retica_enabled = false;

					// $supplier_iva_value = 0;
					// $supplier_retention_value = 0;
					// $supplier_retica_value = 0;
					// $provider_iva_value = 0;
					// $provider_retention_value = 0;
					// $provider_retica_value = 0;

					// if ($this->input->post('supplierivaenabled') == 1) {
					// 	$is_supplier_iva_enabled = true;
					// 	$supplier_iva_value = $this->input->post('supplierivavalue');
					// }

					// if ($this->input->post('supplierretentionenabled') == 1) {
					// 	$is_supplier_retention_enabled = true;
					// 	$supplier_retention_value = $this->input->post('supplierretentionvalue');
					// }

					// if ($this->input->post('supplierreticaenabled') == 1) {
					// 	$is_supplier_retica_enabled = true;
					// 	$supplier_retica_value = $this->input->post('supplierreticavalue');
					// }

					// if ($this->input->post('providerivaenabled') == 1) {
					// 	$is_provider_iva_enabled = true;
					// 	$provider_iva_value = $this->input->post('providerivavalue');
					// }

					// if ($this->input->post('providerretentionenabled') == 1) {
					// 	$is_provider_retention_enabled = true;
					// 	$provider_retention_value = $this->input->post('providerretentionvalue');
					// }

					// if ($this->input->post('providerreticaenabled') == 1) {
					// 	$is_provider_retica_enabled = true;
					// 	$provider_retica_value = $this->input->post('providerreticavalue');
					// }

					if ($status == 0) {
						$status = false;
					} else {
						$status = true;
					}

					$dataSupplier = array(
						"supplier_id" => $supplierid,
						"supplier_name" => $name, "company_name" => $companyname,
						"company_id" => $companyid, "supplier_address" => $address,
						"updatedby" => $session['user_id'], 'isactive' => $status,
					);

					$updateSupplier = $this->Master_model->update_supplier($dataSupplier, $supplier_id, $supplier_origin);

					if ($updateSupplier == true) {

						//ROLES
						$this->Master_model->delete_supplier_role($supplier_id, 1);

						if ($supplier_role_enabled == 1) {

							$dataSupplierRoles = array(
								"supplier_id" => $supplier_id, "role_id" => 1,
								"created_by" => $session['user_id'],
								"updated_by" => $session['user_id'],
							);

							$insertSupplierRoles = $this->Master_model->add_supplier_roles($dataSupplierRoles);
						}

						$this->Master_model->delete_supplier_role($supplier_id, 2);
						if ($provider_role_enabled == 1) {

							$dataSupplierRoles = array(
								"supplier_id" => $supplier_id, "role_id" => 2,
								"created_by" => $session['user_id'],
								"updated_by" => $session['user_id'],
							);

							$insertSupplierRoles = $this->Master_model->add_supplier_roles($dataSupplierRoles);
						}

						//BANK DETAILS
						$array_bank = json_decode($this->input->post('bank_details'), true);

						if ($this->Master_model->delete_supplier_bank($supplier_id) == TRUE) {

							if ($array_bank > 0) {
								$bankId = 1;
								foreach ($array_bank as $bank) {
									$dataSupplierBanks = array(
										"supplier_id" => $supplier_id, "supplier_code" => $supplier_code,
										"bank_sno" => $bankId, "bank_name" => $bank['bankName'],
										"bank_accountnumber" => $bank['bankAccountNumber'], "bank_holdername" => $bank['bankHolderName'],
										"bank_accounttype" => $bank['accountType'], "createdby" => $session['user_id'],
										"updatedby" => $session['user_id'],
									);

									$insertSupplierBank = $this->Master_model->add_supplier_banks($dataSupplierBanks);
									if ($insertSupplierBank > 0) {
										$bankId++;
									}
								}
							}
						}

						//PRODUCTS
						$array_product = json_decode($this->input->post('product_details'), true);
						$bankId = 1;

						$deleteSupplierProducts = array(
							"supplier_id" => $supplier_id,
							"updatedby" => $session['user_id'], 'is_active' => 0,
						);

						$deleteSupplierProductTypes = array(
							"supplier_id" => $supplier_id,
							"updatedby" => $session['user_id'], 'is_active' => 0,
						);

						if ($this->Master_model->delete_supplier_product($deleteSupplierProducts, $deleteSupplierProductTypes, $supplier_id)) {

							if ($array_product > 0) {

								foreach ($array_product as $product) {
									$dataSupplierProducts = array(
										"supplier_id" => $supplier_id, "supplier_code" => $supplier_code,
										"product_name" => $product['productId'], "createdby" => $session['user_id'],
										"updatedby" => $session['user_id'], 'is_active' => 1,
									);

									$insertSupplierProduct = $this->Master_model->add_supplier_products($dataSupplierProducts);
									if ($insertSupplierProduct > 0) {
										$dataSupplierProductTypes = array(
											"supplier_id" => $supplier_id, "supplier_code" => $supplier_code,
											"product_id" => $insertSupplierProduct, "product_type_id" => $product['woodType'],
											"createdby" => $session['user_id'],
											"updatedby" => $session['user_id'], 'is_active' => 1,
										);

										$insertSupplierProductType = $this->Master_model->add_supplier_product_types($dataSupplierProductTypes);
									}
								}
							}
						}

						$Return['result'] = $this->lang->line('data_updated');
						$Return['csrf_hash'] = $this->security->get_csrf_hash();
						$this->output($Return);
						exit;
					} else {
						$Return['error'] = $this->lang->line('error_updating');
						$Return['csrf_hash'] = $this->security->get_csrf_hash();
						$this->output($Return);
						exit;
					}
				}
			} else {
				$Return['error'] = "";
				$Return['result'] = "";
				$Return['redirect'] = true;
				$Return['csrf_hash'] = $this->security->get_csrf_hash();
				$this->output($Return);
				exit;
			}
		} else {
			$Return['error'] = $this->lang->line('invalid_request');
			$Return['csrf_hash'] = $this->security->get_csrf_hash();
			$this->output($Return);
		}
	}

	public function dialog_supplier_view()
	{
		$session = $this->session->userdata('fullname');
		$Return = array('pages' => '', 'redirect' => false, 'result' => '', 'error' => '', 'csrf_hash' => '');

		if ($this->input->get('type') == "viewsupplier") {
			if (!empty($session)) {

				$getSupplierDetails = $this->Master_model->get_supplier_detail_by_id($this->input->get('sid'));

				$getSupplierDetails[0]->products = explode('-----', $getSupplierDetails[0]->products);
				$getSupplierDetails[0]->banks = explode('-----', $getSupplierDetails[0]->banks);


				if ($getSupplierDetails[0]->isactive == 1) {
					$status = $this->lang->line('active');
				} else {
					$status = $this->lang->line('inactive');
				}

				$supplierRoleEnabled = false;
				$providerRoleEnabled = false;

				$roles_arr = explode(", ", $getSupplierDetails[0]->roles);

				foreach ($roles_arr as $role) {
					if ($role == "Supplier") {
						$supplierRoleEnabled = true;
					}

					if ($role == "Service Provider") {
						$providerRoleEnabled = true;
					}
				}

				$getSupplierTaxes = $this->Master_model->get_supplier_taxes($this->input->get('sid'));
				$getProviderTaxes = $this->Master_model->get_provider_taxes($this->input->get('sid'));

				$data = array(
					'pageheading' => $this->lang->line('supplier_details'),
					'supplierid' => $this->input->get('sid'),
					'supplier_name' => $getSupplierDetails[0]->supplier_name,
					'supplier_code' => $getSupplierDetails[0]->supplier_code,
					'supplier_id' => $getSupplierDetails[0]->supplier_id,
					'company_name' => $getSupplierDetails[0]->company_name,
					'company_id' => $getSupplierDetails[0]->company_id,
					'supplier_address' => $getSupplierDetails[0]->supplier_address,
					'products' => $getSupplierDetails[0]->products,
					'roles' => $getSupplierDetails[0]->roles,
					'banks' => $getSupplierDetails[0]->banks,
					'status' => $status,
					'origin' => $getSupplierDetails[0]->origin,
					'supplier_role_enabled' => $supplierRoleEnabled,
					'provider_role_enabled' => $providerRoleEnabled,
					'supplier_taxes' => $getSupplierTaxes,
					'provider_taxes' => $getProviderTaxes,
				);
				$this->load->view('masters/dialog_view_supplier', $data);
			} else {
				$Return['pages'] = "";
				$Return['redirect'] = true;
				$this->output($Return);
			}
		} else {
			$Return['pages'] = "";
			$Return['redirect'] = true;
			$this->output($Return);
		}
	}

	public function products_byorigin()
	{
		$session = $this->session->userdata('fullname');
		$Return = array('pages' => '', 'redirect' => false, 'result' => '', 'error' => '', 'csrf_hash' => '');
		$Return['csrf_hash'] = $this->security->get_csrf_hash();
		if (!empty($session)) {

			$getProductByOrigin = $this->Master_model->get_product_byorigin($this->input->get('originid'));

			$result = "<option value='0'>" . $this->lang->line('select') . "</option>";
			foreach ($getProductByOrigin as $product) {
				$result = $result . "<option value='" . $product->product_id . "'>" . $product->product_name . "</option>";
			}

			$Return['result'] = $result;
			$Return['redirect'] = false;
			$this->output($Return);
		} else {
			$Return['pages'] = "";
			$Return['redirect'] = true;
			$this->output($Return);
		}
	}

	public function supplier_taxes_origin()
	{
		$session = $this->session->userdata("fullname");
		$Return = array("pages" => "", "redirect" => false, "result" => "", "error" => "", "csrf_hash" => "");
		$Return["csrf_hash"] = $this->security->get_csrf_hash();
		if (!empty($session)) {

			$getSupplierTaxes = $this->Master_model->get_supplier_taxes_by_origin($this->input->get("originid"));

			$supplierTaxes = "";
			foreach ($getSupplierTaxes as $suppliertax) {
				$supplierTaxes = $supplierTaxes . '<div class="col-md-3">';
				$supplierTaxes = $supplierTaxes . '<div class="input-group">';
				$supplierTaxes = $supplierTaxes . '<div class="form-check">';
				$supplierTaxes = $supplierTaxes . '<input class="form-check-input" id="checksupplier' . $suppliertax->id . '" name="checksupplier' . $suppliertax->id . '" type="checkbox" value="1">';

				if ($suppliertax->number_format == 2) {
					$supplierTaxes = $supplierTaxes . '<label for="checksupplier' . $suppliertax->id . '">' . $suppliertax->tax_name . '(%)' . '</label>';
				} else {
					$supplierTaxes = $supplierTaxes . '<label for="checksupplier' . $suppliertax->id . '">' . $suppliertax->tax_name . '</label>';
				}

				$supplierTaxes = $supplierTaxes . '</div>';
				$supplierTaxes = $supplierTaxes . '<div class="input-group">';
				$supplierTaxes = $supplierTaxes . '<input name="supplier' . $suppliertax->id . '" type="number" step="any" maxlength="8" id="supplier' . $suppliertax->id . '" autocomplete="off" class="form-control" disabled />';
				$supplierTaxes = $supplierTaxes . '</div>';
				$supplierTaxes = $supplierTaxes . '<label id="error-supplier' . $suppliertax->id . '" class="error-text">' . $this->lang->line("error_value") . '</label>';
				$supplierTaxes = $supplierTaxes . '</div>';
				$supplierTaxes = $supplierTaxes . '</div>';
				$supplierTaxes = $supplierTaxes . '<script type="text/javascript"> $("#error-supplier' . $suppliertax->id . '").hide(); supplierTaxes = []; supplierTaxes = ' . json_encode($getSupplierTaxes) . ';';
				$supplierTaxes = $supplierTaxes . '$("#checksupplier' . $suppliertax->id . '").change(function() {';
				$supplierTaxes = $supplierTaxes . 'if (this.checked) {';
				$supplierTaxes = $supplierTaxes . '$("#supplier' . $suppliertax->id . '").removeAttr("disabled", "disabled");';
				$supplierTaxes = $supplierTaxes . '$("#supplier' . $suppliertax->id . '").val("");';
				$supplierTaxes = $supplierTaxes . '} else {';
				$supplierTaxes = $supplierTaxes . '$("#supplier' . $suppliertax->id . '").attr("disabled", "disabled");';
				$supplierTaxes = $supplierTaxes . '$("#supplier' . $suppliertax->id . '").val("");';
				$supplierTaxes = $supplierTaxes . '}';
				$supplierTaxes = $supplierTaxes . '});';
				$supplierTaxes = $supplierTaxes . '</script>';
			}

			$getProviderTaxes = $this->Master_model->get_provider_taxes_by_origin($this->input->get("originid"));

			$providerTaxes = "";
			foreach ($getProviderTaxes as $providertax) {
				$providerTaxes = $providerTaxes . '<div class="col-md-3">';
				$providerTaxes = $providerTaxes . '<div class="input-group">';
				$providerTaxes = $providerTaxes . '<div class="form-check">';
				$providerTaxes = $providerTaxes . '<input class="form-check-input" id="checkprovider' . $providertax->id . '" name="checkprovider' . $providertax->id . '" type="checkbox" value="1">';

				if ($providertax->number_format == 2) {
					$providerTaxes = $providerTaxes . '<label for="checkprovider' . $providertax->id . '">' . $providertax->tax_name . '(%)' . '</label>';
				} else {
					$providerTaxes = $providerTaxes . '<label for="checkprovider' . $providertax->id . '">' . $providertax->tax_name . '</label>';
				}

				$providerTaxes = $providerTaxes . '</div>';
				$providerTaxes = $providerTaxes . '<div class="input-group">';
				$providerTaxes = $providerTaxes . '<input name="provider' . $providertax->id . '" type="number" step="any" maxlength="8" id="provider' . $providertax->id . '" autocomplete="off" class="form-control" disabled />';
				$providerTaxes = $providerTaxes . '</div>';
				$providerTaxes = $providerTaxes . '<label id="error-provider' . $providertax->id . '" class="error-text">' . $this->lang->line("error_value") . '</label>';
				$providerTaxes = $providerTaxes . '</div>';
				$providerTaxes = $providerTaxes . '</div>';
				$providerTaxes = $providerTaxes . '<script type="text/javascript"> $("#error-provider' . $providertax->id . '").hide(); providerTaxes = []; providerTaxes = ' . json_encode($getProviderTaxes) . ';';
				$providerTaxes = $providerTaxes . '$("#checkprovider' . $providertax->id . '").change(function() {';
				$providerTaxes = $providerTaxes . 'if (this.checked) {';
				$providerTaxes = $providerTaxes . '$("#provider' . $providertax->id . '").removeAttr("disabled", "disabled");';
				$providerTaxes = $providerTaxes . '$("#provider' . $providertax->id . '").val("");';
				$providerTaxes = $providerTaxes . '} else {';
				$providerTaxes = $providerTaxes . '$("#provider' . $providertax->id . '").attr("disabled", "disabled");';
				$providerTaxes = $providerTaxes . '$("#provider' . $providertax->id . '").val("");';
				$providerTaxes = $providerTaxes . '}';
				$providerTaxes = $providerTaxes . '});';
				$providerTaxes = $providerTaxes . '</script>';
			}

			$dataTaxes = array(
				"supplierTaxes" => $supplierTaxes,
				"providerTaxes" => $providerTaxes,
			);

			$Return["result"] = $dataTaxes;
			$Return["redirect"] = false;
			$this->output($Return);
		} else {
			$Return["pages"] = "";
			$Return["redirect"] = true;
			$this->output($Return);
		}
	}

	public function generate_supplier_report()
	{
		try {

			$session = $this->session->userdata('fullname');

			$Return = array(
				'result' => '', 'error' => '', 'redirect' => false, 'csrf_hash' => '',
				'successmessage' => ''
			);

			if (!empty($session)) {

				$Return['csrf_hash'] = $this->security->get_csrf_hash();

				$getSupplierDetailsReport = $this->Master_model->get_supplier_details_report($session["applicable_origins_id"]);

				if (count($getSupplierDetailsReport) > 0) {
					$this->excel->setActiveSheetIndex(0);
					$objSheet = $this->excel->getActiveSheet();
					$objSheet->setTitle($this->lang->line('excel_supplier_title'));
					$objSheet->getParent()->getDefaultStyle()
						->getFont()
						->setName('Calibri')
						->setSize(11);

					$objSheet->SetCellValue('A1', $this->lang->line('s_no'));
					$objSheet->SetCellValue('B1', $this->lang->line('supplier_name'));
					$objSheet->SetCellValue('C1', $this->lang->line('supplier_code'));
					$objSheet->SetCellValue('D1', $this->lang->line('supplier_id'));
					$objSheet->SetCellValue('E1', $this->lang->line('company_name'));
					$objSheet->SetCellValue('F1', $this->lang->line('company_id'));
					$objSheet->SetCellValue('G1', $this->lang->line('address'));
					$objSheet->SetCellValue('H1', $this->lang->line('roles'));
					$objSheet->SetCellValue('I1', $this->lang->line('wood_details'));
					$objSheet->SetCellValue('J1', $this->lang->line('bank_detail'));
					$objSheet->SetCellValue('K1', $this->lang->line('supplier_taxes'));
					$objSheet->SetCellValue('L1', $this->lang->line('provider_taxes'));
					$objSheet->SetCellValue('M1', $this->lang->line('origin'));
					$objSheet->SetCellValue('N1', $this->lang->line('status'));

					$objSheet->getStyle("A1:N1")
						->getFont()
						->setBold(true);

					$objSheet->setAutoFilter('A1:N1');

					// HEADER ALIGNMENT
					$objSheet->getStyle("A1:N1")
						->getAlignment()
						->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

					$objSheet->getColumnDimension('A')->setAutoSize(true);
					$objSheet->getColumnDimension('B')->setAutoSize(true);
					$objSheet->getColumnDimension('C')->setAutoSize(true);
					$objSheet->getColumnDimension('D')->setAutoSize(true);
					$objSheet->getColumnDimension('E')->setAutoSize(true);
					$objSheet->getColumnDimension('F')->setAutoSize(true);
					$objSheet->getColumnDimension('G')->setAutoSize(false);
					$objSheet->getColumnDimension('G')->setWidth(30);
					$objSheet->getColumnDimension('H')->setAutoSize(true);
					$objSheet->getColumnDimension('I')->setAutoSize(true);
					$objSheet->getColumnDimension('J')->setAutoSize(true);
					$objSheet->getColumnDimension('K')->setAutoSize(true);
					$objSheet->getColumnDimension('L')->setAutoSize(true);
					$objSheet->getColumnDimension('M')->setAutoSize(true);
					$objSheet->getColumnDimension('N')->setAutoSize(true);

					$objSheet->getStyle('A1:N1')
						->getFill()
						->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
						->getStartColor()
						->setRGB('add8e6');

					$styleArray = array(
						'borders' => array(
							'allborders' => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN
							)
						)
					);

					$objSheet->getStyle('A1:N1')->applyFromArray($styleArray);

					$i = 1;
					$rowCountData = 2;

					foreach ($getSupplierDetailsReport as $supplier) {
						$objSheet->SetCellValue('A' . $rowCountData, $i);
						$objSheet->SetCellValue('B' . $rowCountData, $supplier->supplier_name);
						$objSheet->SetCellValue('C' . $rowCountData, $supplier->supplier_code);

						$objSheet->setCellValueExplicit(
							'D' . $rowCountData,
							$supplier->supplier_id,
							PHPExcel_Cell_DataType::TYPE_STRING
						);

						$objSheet->SetCellValue('E' . $rowCountData, $supplier->company_name);

						$objSheet->setCellValueExplicit(
							'F' . $rowCountData,
							$supplier->company_id,
							PHPExcel_Cell_DataType::TYPE_STRING
						);

						$objSheet->SetCellValue('G' . $rowCountData, $supplier->supplier_address);
						$objSheet->SetCellValue('H' . $rowCountData, $supplier->roles);
						$objSheet->SetCellValue('I' . $rowCountData, $supplier->products);
						$objSheet->SetCellValue('J' . $rowCountData, $supplier->bankdetails);
						$objSheet->SetCellValue('K' . $rowCountData, $supplier->supplier_taxes);
						$objSheet->SetCellValue('L' . $rowCountData, $supplier->provider_taxes);
						$objSheet->SetCellValue('M' . $rowCountData, $supplier->origin);

						if ($supplier->isactive == 1) {
							$objSheet->SetCellValue('N' . $rowCountData, $this->lang->line('active'));
						} else {
							$objSheet->SetCellValue('N' . $rowCountData, $this->lang->line('inactive'));
						}

						$objSheet->getStyle('G' . $rowCountData . ':L' . $rowCountData)->getAlignment()->setWrapText(true);
						$objSheet->getStyle('A' . $rowCountData . ':N' . $rowCountData)->applyFromArray($styleArray);

						$i++;
						$rowCountData++;
					}

					$objSheet->getSheetView()->setZoomScale(95);

					unset($styleArray);
					$six_digit_random_number = mt_rand(100000, 999999);
					$month_name = ucfirst(date("dmY"));

					$filename =  'SupplierReport_' . $month_name . '_' . $six_digit_random_number . '.xlsx';

					header('Content-Type: application/vnd.ms-excel');
					header('Content-Disposition: attachment;filename="' . $filename . '"');
					header('Cache-Control: max-age=0');

					$objWriter = new PHPExcel_Writer_Excel2007($this->excel);
					$objWriter->save('./reports/SupplierReports/' . $filename);
					$Return['error'] = '';
					$Return['result'] = site_url() . 'reports/SupplierReports/' . $filename;
					$Return['successmessage'] = $this->lang->line('report_downloaded');
					if ($Return['result'] != '') {
						$this->output($Return);
					}
				} else {
					$Return['error'] = $this->lang->line('no_data_reports');
					$Return['result'] = "";
					$Return['redirect'] = false;
					$Return['csrf_hash'] = $this->security->get_csrf_hash();
					$this->output($Return);
					exit;
				}
			} else {
				$Return['error'] = "";
				$Return['result'] = "";
				$Return['redirect'] = true;
				$Return['csrf_hash'] = $this->security->get_csrf_hash();
				$this->output($Return);
				exit;
			}
		} catch (Exception $e) {
			$Return['error'] = $this->lang->line('error_reports');
			$Return['result'] = "";
			$Return['redirect'] = false;
			$Return['csrf_hash'] = $this->security->get_csrf_hash();
			$this->output($Return);
			exit;
		}
	}

	private function supplierCodeSequence()
	{
		$supplier_record_count = $this->Master_model->supplier_record_count();
		$result = '';
		if ($supplier_record_count > 0) {
			$lenDataCount = strlen($supplier_record_count);
			if ($lenDataCount == 1) {
				$result = '0000000' . ($supplier_record_count + 1);
			} else if ($lenDataCount == 2) {
				$result = '000000' . ($supplier_record_count + 1);
			} else if ($lenDataCount == 3) {
				$result = '00000' . ($supplier_record_count + 1);
			} else if ($lenDataCount == 4) {
				$result = '0000' . ($supplier_record_count + 1);
			} else if ($lenDataCount == 5) {
				$result = '000' . ($supplier_record_count + 1);
			} else if ($lenDataCount == 6) {
				$result = '00' . ($supplier_record_count + 1);
			} else if ($lenDataCount == 7) {
				$result = '0' . ($supplier_record_count + 1);
			} else {
				$result = ($supplier_record_count + 1);
			}
		} else {
			$result = '00000001';
		}
		return $result;
	}

	public function deletefilesfromfolder()
	{
		$files = glob(FCPATH . "reports/*.xlsx");
		foreach ($files as $file) {
			if (is_file($file)) {
				unlink($file);
			}
		}

		$files = glob(FCPATH . "reports/SupplierReports/*.xlsx");
		foreach ($files as $file) {
			if (is_file($file)) {
				unlink($file);
			}
		}
	}
}
