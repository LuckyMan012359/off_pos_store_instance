<?php

/*
  ###########################################################
  # PRODUCT NAME:   Off POS
  ###########################################################
  # AUTHER:   Door Soft
  ###########################################################
  # EMAIL:   info@doorsoft.co
  ###########################################################
  # COPYRIGHTS:   RESERVED BY Door Soft
  ###########################################################
  # WEBSITE:   https://www.doorsoft.co
  ###########################################################
  # This is ApiPurchaseController
  ###########################################################
 */
defined('BASEPATH') or exit('No direct script access allowed');

require(APPPATH . 'libraries/REST_Controller.php');

class ApiPurchaseController extends REST_Controller
{
    /**
     * load constructor
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('API_model');
        $this->load->model('Common_model');
        $this->load->model('Master_model');
        $this->load->library('form_validation');
    }

    /**
     * addSale_post
     * @access public
     * @param no
     * @return json
     */
    public function addPurchase_post()
    {
        $purchase_info = json_decode(file_get_contents("php://input"), true);

        $outlet_info = $purchase_info['outlet_info'];

        $supplier_info = $purchase_info['supplier_info'];

        $company_info = getCompanyInfoByAPIKey($outlet_info['token']);
        $error = false;
        if ($company_info) {
            $purchaseArr = array();

            $purchaseArr['reference_no'] = $purchase_info['reference_no'];
            $purchaseArr['invoice_no'] = $purchase_info['invoice_no'];
            $purchaseArr['supplier_id'] = $this->Common_model->getSupplierDataByMulipleField($supplier_info['name'], 'name', 'tbl_suppliers', 0, $company_info->id, $supplier_info);
            $purchaseArr['date'] = $purchase_info['date'];
            $purchaseArr['other'] = $purchase_info['other'];
            $purchaseArr['grand_total'] = $purchase_info['grand_total'];
            $purchaseArr['paid'] = $purchase_info['paid'];
            $purchaseArr['due_amount'] = $purchase_info['due_amount'];
            $purchaseArr['note'] = $purchase_info['note'];
            $purchaseArr['discount'] = $purchase_info['discount'];
            $purchaseArr['user_id'] = 0;
            $purchaseArr['outlet_id'] = $this->Common_model->fieldNameCheckingByFieldNameForAPI($outlet_info['outlet_name'], 'outlet_name', 'tbl_outlets', 0, $company_info->id);
            $purchaseArr['company_id'] = $company_info->id;
            $purchaseArr['added_date'] = $purchase_info['added_date'];
            $purchaseArr['verify_code'] = $purchase_info['verify_code'];

            $item_info = [];

            foreach ($purchase_info['code'] as $key => $value) {
                $item_data = $this->Common_model->getDataByField($value, 'tbl_items', 'code');
                $item_info[] = $item_data[0]->id;
            }

            if (count($item_info) > 0) {
                $purchase_id = $this->Common_model->insertInformation($purchaseArr, "tbl_purchase");
                $this->savePurchaseDetails($item_info, $purchase_id, 'tbl_purchase_details', $purchase_info);

                if (isset($purchase_info['payment_id']) && $purchase_info['payment_id']) {
                    $this->savePaymentMethod($purchase_info['payment_id'], $purchase_id, 'tbl_purchase_payments', $purchase_info);
                }
            } else {
                $response = array(
                    'status' => 404,
                    'message' => 'Item not found'
                );
            }

            if ($purchase_id) {
                $response = array(
                    'status' => 200,
                    'message' => "Purchase create successfully",
                    'ids' => $item_info,
                );
            } else {
                $response = array(
                    'status' => 400,
                    'message' => "Purchase failded something wrong",
                );
            }
        } else {
            $response = array(
                'status' => 500,
                'message' => 'API Key is not valid',
                'outlet_info' => $outlet_info,
                'api_key' => $outlet_info['token']
            );
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }


    /**
     * updateSale_post
     * @access public
     * @param no
     * @return json
     */
    public function updatePurchase_post()
    {
        $find_purchase_id = json_decode(file_get_contents("php://input"), true);

        $verify_code = $find_purchase_id['verify_code'];

        $find_purchase_id = $this->Common_model->getDataByField($verify_code, 'tbl_purchase', 'verify_code');

        if ($find_purchase_id) {
            $purchase_updated_id = $find_purchase_id[0]->id;
            $purchase_info = json_decode(file_get_contents("php://input"), true);

            $supplier_info = $purchase_info['supplier_info'];

            $outlet_info = $purchase_info['outlet_info'];

            $company_info = getCompanyInfoByAPIKey($outlet_info['token']);
            $error = false;
            if ($company_info) {
                $purchaseArr = array();
                $purchaseArr['supplier_id'] = $this->Common_model->getSupplierDataByMulipleField($supplier_info['name'], 'name', 'tbl_suppliers', 0, $company_info->id, $supplier_info);
                $purchaseArr['reference_no'] = $purchase_info['reference_no'];
                $purchaseArr['invoice_no'] = $purchase_info['invoice_no'];
                $purchaseArr['supplier_id'] = $purchase_info['supplier_id'];
                $purchaseArr['other'] = $purchase_info['other'];
                $purchaseArr['grand_total'] = $purchase_info['grand_total'];
                $purchaseArr['paid'] = $purchase_info['paid'];
                $purchaseArr['due_amount'] = $purchase_info['due_amount'];
                $purchaseArr['note'] = $purchase_info['note'];
                $purchaseArr['discount'] = $purchase_info['discount'];
                $purchaseArr['user_id'] = 0;
                $purchaseArr['outlet_id'] = $this->Common_model->fieldNameCheckingByFieldNameForAPI($outlet_info['outlet_name'], 'outlet_name', 'tbl_outlets', 0, $company_info->id);
                $purchaseArr['company_id'] = $company_info->id;

                $item_info = [];

                foreach ($purchase_info['code'] as $key => $value) {
                    $item_data = $this->Common_model->getDataByField($value, 'tbl_items', 'code');
                    $item_info[] = $item_data[0]->id;
                }

                if (count($item_info) > 0) {
                    $this->Common_model->updateInformation($purchaseArr, $purchase_updated_id, "tbl_purchase");
                    $this->Common_model->deletingMultipleFormData('purchase_id', $purchase_updated_id, 'tbl_purchase_details');
                    $this->savePurchaseDetails($item_info, $purchase_updated_id, 'tbl_purchase_details', $purchase_info);
                    $this->Common_model->deletingMultipleFormData('purchase_id', $purchase_updated_id, 'tbl_purchase_payments');
                    if (isset($purchase_info['payment_id']) && $purchase_info['payment_id']) {
                        $this->savePaymentMethod($purchase_info['payment_id'], $purchase_updated_id, 'tbl_purchase_payments', $purchase_info);
                    }
                }

                if ($purchase_updated_id) {
                    $response = array(
                        'status' => 200,
                        'message' => "Purchase create successfully",
                        'ids' => $item_info,
                    );
                } else {
                    $response = array(
                        'status' => 400,
                        'message' => "Purchase failded something wrong",
                    );
                }
            } else {
                $response = array(
                    'status' => 500,
                    'message' => 'API Key is not valid',
                    'outlet_info' => $outlet_info,
                    'api_key' => $outlet_info['token']
                );
            }
        } else {
            $response = array(
                'status' => 404,
                'message' => 'Sale Not Found',
                'ramdom_code' => $verify_code,
            );
        }
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }


    /**
     * deleteSale_post
     * @access public
     * @param no
     * @return json
     */
    public function deletePurchase_post()
    {
        $find_purchase_id = json_decode(file_get_contents("php://input"), true);
        $verify_code = $find_purchase_id['verify_code'];
        $find_purchase_id = $this->Common_model->getDataByField($verify_code, 'tbl_purchase', 'verify_code');
        if ($find_purchase_id) {
            $purchase_id = $find_purchase_id[0]->id;
            $sale_info = json_decode(file_get_contents("php://input"), true);
            $company_info = getCompanyInfoByAPIKey($sale_info['api_auth_key']);
            if ($company_info) {
                $this->Common_model->deleteStatusChangeWithChild($purchase_id, $purchase_id, "tbl_purchase", "tbl_purchase_details", 'id', 'purchase_id');
                $this->Common_model->deleteStatusChangeByFieldName($purchase_id, 'purchase_id', 'tbl_purchase_payments');
                $response = [
                    'status' => 200,
                    'data' => 'Item Deleted Successfully',
                ];
            } else {
                $response = array(
                    'status' => 500,
                    'message' => 'API Key is not valid',
                );
            }
        } else {
            $response = [
                'status' => 404,
                'data' => 'Data Not Found!',
                'data1' => json_decode(file_get_contents("php://input"), true),
                'random_code' => $verify_code,
            ];
        }
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }



    /**
     * saveSaleDetails
     * @access public
     * @param string
     * @param int
     * @param int
     * @param string
     * @param int
     * @return void
     */
    public function saveSaleDetails($sale_details, $insertedId, $user_id, $outlet_name, $company_id)
    {
        foreach ($sale_details as $key => $sale) {
            $fmi = array();

            if ($sale['is_promo_item'] == "Yes") {
                $p_price = 0;
            } else {
                $p_price = getLastThreePurchaseAmount($sale['item_id'], '');
            }
            $fmi['food_menu_id'] = $sale['item_id'];
            $fmi['qty'] = $sale['quantity'];
            $fmi['menu_price_without_discount'] = $sale['menu_price_without_discount'];
            $fmi['menu_price_with_discount'] = $sale['menu_price_with_discount'];
            $fmi['menu_unit_price'] = $sale['menu_unit_price'];
            $fmi['purchase_price'] = trim_checker($p_price);
            $fmi['menu_taxes'] = getItemTaxByItemId($sale['item_id']);
            $fmi['menu_discount_value'] = $sale['menu_discount_value'];
            $fmi['discount_type'] = $sale['discount_type'];
            $fmi['menu_note'] = $sale['menu_note'];
            $fmi['discount_amount'] = $sale['discount_amount'];
            $fmi['item_type'] = $sale['item_type'];
            $fmi['expiry_imei_serial'] = $sale['expiry_imei_serial'];
            $fmi['sales_id'] = $insertedId;
            $fmi['is_promo_item'] = $sale['is_promo_item'];
            $fmi['promo_parent_id'] = $sale['promo_parent_id'];
            $fmi['item_seller_id'] = $sale['item_seller_id'];
            $fmi['delivery_status'] = $sale['delivery_status'];
            $fmi['loyalty_point_earn'] = $sale['loyalty_point_earn'];
            $fmi['outlet_id'] = $outlet_name;
            $fmi['user_id'] = $user_id;
            $fmi['company_id'] = $company_id;
            $this->Common_model->insertInformation($fmi, 'tbl_sales_details');
        }
    }


    /**
     * saveSaleDetails
     * @access public
     * @param string
     * @param int
     * @param int
     * @param string
     * @param int
     * @return void
     */
    public function saveSalePaymentDetails($sale_payment_details, $insertedId, $user_id, $outlet_name, $company_id)
    {
        foreach ($sale_payment_details as $key => $payment_details) {
            $fmi = array();
            $fmi['payment_id'] = $this->Common_model->fieldNameCheckingByFieldNameForAPI($payment_details['payment_name'], 'name', 'tbl_payment_methods', $user_id, $company_id);
            $fmi['date'] = date('Y-m-d');
            $fmi['currency_type'] = $payment_details['currency_type'];
            $fmi['multi_currency'] = $payment_details['multi_currency'];
            $fmi['multi_currency_rate'] = $payment_details['multi_currency_rate'];
            $fmi['amount'] = $payment_details['amount'];
            $fmi['usage_point'] = $payment_details['usage_point'];
            $fmi['sale_id'] = $insertedId;
            $fmi['added_date'] = date('Y-m-d H:i:s');
            $fmi['user_id'] = $user_id;
            $fmi['outlet_id'] = $outlet_name;
            $fmi['company_id'] = $company_id;
            $this->Common_model->insertInformation($fmi, 'tbl_sale_payments');
        }
    }

    public function savePurchaseDetails($purchase_items, $purchase_id, $table_name, $purchase_info)
    {
        foreach ($purchase_items as $row => $item_id):
            if ($item_id != null) {
                $fmi = array();
                $fmi['item_id'] = $purchase_info['item_id'][$row];
                $fmi['item_type'] = $purchase_info['item_type'][$row];
                if (isset($purchase_info['expiry_imei_serial'])) {
                    $fmi['expiry_imei_serial'] = $purchase_info['expiry_imei_serial'][$row];
                }
                $fmi['unit_price'] = $purchase_info['unit_price'][$row];
                if (!empty((int) $purchase_info['conversion_rate'][$row])) {
                    $fmi['divided_price'] = round(($purchase_info['unit_price'][$row] / $purchase_info['conversion_rate'][$row]), 2);
                } else {
                    $fmi['divided_price'] = $purchase_info['unit_price'][$row] / 1;
                }
                $fmi['quantity_amount'] = $purchase_info['quantity_amount'][$row];
                $fmi['total'] = $purchase_info['total'][$row];
                $fmi['purchase_id'] = $purchase_id;
                $fmi['outlet_id'] = $this->session->userdata('outlet_id');
                $fmi['company_id'] = $this->session->userdata('company_id');
                $this->Common_model->insertInformation($fmi, "tbl_purchase_details");
                setAveragePrice($item_id);
            }
        endforeach;
    }

    public function savePaymentMethod($payment_method, $purchase_id, $table_name, $purchase_info)
    {
        foreach ($payment_method as $row => $payment_id):
            $fmi = array();
            $fmi['added_date'] = date('Y-m-d');
            $fmi['purchase_id'] = $purchase_id;
            $fmi['payment_id'] = $purchase_info['payment_id'][$row];
            $fmi['amount'] = $purchase_info['payment_value'][$row];
            $fmi['outlet_id'] = $this->session->userdata('outlet_id');
            $fmi['user_id'] = $this->session->userdata('user_id');
            $fmi['company_id'] = $this->session->userdata('company_id');
            $this->Common_model->insertInformation($fmi, $table_name);
        endforeach;
    }
}

?>