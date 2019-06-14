<?php

/**
 * Invoice Controller - Manage invoice module
 * @author ANP
 */
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH . 'libraries/Stripe/lib/Stripe.php');

class Invoice extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('users_model');
        $this->load->model('businesses_model');
        $this->load->model('orders_model');
        $this->load->model('settings_model');
        $this->load->model('invoice_model');
        $this->load->model('stripe/paymentmodel', 'payment');
    }

    /**
     * Load view of business list
     * */
    public function index() {
        $data['title'] = 'facetag | Manage Invoice';
        $businesses = $this->invoice_model->get_all_businesses();
        $new_result_array = array();
        foreach ($businesses as $business) {
            $d["business_details"] = $business;
            $payments = $this->invoice_model->get_total_payment($business['id']);
            $d["payment"] = $payments['payment'];
            if ($payments['payment'] > 0) {
                $new_result_array[] = $d;
            }
        }
        $data['business'] = $new_result_array;
        $this->template->load('default', 'admin/invoice/index', $data);
    }
    
    public function test_date() {
        $row['createddate'] = '2017-12-24';
        echo date('W', strtotime($row['createddate']));
        exit;
    }

    public function invoices($business_id) {
        $data['title'] = 'facetag | Manage Invoice';
        $business_detail = $this->businesses_model->get_business_by_id($business_id);

        $business_settings = $this->businesses_model->get_business_settings_by_id($business_id);
        $data['business_settings'] = $business_settings;
        if (empty($business_settings)) {
            $percentage_Commision_on_digital_product = 25.00;
            $direct_Commision_on_digital_product = 0.50;
            $percentage_Commision_on_physical_product = 25.00;
            $direct_Commision_on_physical_product = 1.00;
        } else {
            $percentage_Commision_on_digital_product = $business_settings['commission_on_digital_image_sales_percentage'];
            $direct_Commision_on_digital_product = $business_settings["commission_on_digital_image_sales"];
            $percentage_Commision_on_physical_product = $business_settings["commission_on_product_sales_percentage"];
            $direct_Commision_on_physical_product = $business_settings["commission_on_product_sales"];
        }
        $where = 'settings_key="creditcard_debitcard_processing_fees" OR settings_key="international_card_processing_fees" OR settings_key="transaction_fees"';
        $settings = $this->settings_model->get_settings($where);

        $settings_arr = array();
        foreach ($settings as $key => $val) {
            $settings_arr[$val['settings_key']] = $val['settings_value'];
        }
        $domestic_shipping_fee = $settings_arr["creditcard_debitcard_processing_fees"];
        $international_shipping_fee = $settings_arr["international_card_processing_fees"];
        $transaction_fee = $settings_arr["transaction_fees"];

        $data['business_name'] = $business_detail['name'];
        $orders = $this->invoice_model->get_invoice_list($business_id);
        $total_weekly_commision = 0;
        $total_weekly_payment = 0;
        $new_result_array = [];
        $average_prices = [];
        foreach ($orders as $row) {

            //Initialize the index's if they dont exist
            if (!isset($new_result_array[date('W', strtotime($row['createddate']))])) {
                $new_result_array[date('W', strtotime($row['createddate']))] = [];
                $new_result_array[date('W', strtotime($row['createddate']))]['weekly_total_amount'] = 0;
                $new_result_array[date('W', strtotime($row['createddate']))]['weekly_total_payment'] = 0;
                $new_result_array[date('W', strtotime($row['createddate']))]['total_cart_orders'] = 0;
                $new_result_array[date('W', strtotime($row['createddate']))]['total_images'] = 0;
                $new_result_array[date('W', strtotime($row['createddate']))]['orders'] = [];
                $new_result_array[date('W', strtotime($row['createddate']))]['cartIds'] = [];
                $new_result_array[date('W', strtotime($row['createddate']))]['deduction'] = 0;
            }
            //Edited to wrap $row['createdDate'] in strtotime() as all dates are returning week 1 
            $d = $this->invoice_model->x_week_range($row['createddate']);
            $new_result_array[date('W', strtotime($row['createddate']))]['start_date'] = $d[0];
            $new_result_array[date('W', strtotime($row['createddate']))]['end_date'] = $d[1];
            $new_result_array[date('W', strtotime($row['createddate']))]['businessId'] = $business_id;
            $is_transfer = $this->invoice_model->is_transfer($business_id, $d[0], $d[1]);
            if($is_transfer != 0) {
                $new_result_array[date('W', strtotime($row['createddate']))]['transfer_status'] = 1;
            }else{
                $new_result_array[date('W', strtotime($row['createddate']))]['transfer_status'] = 0;
            }

            if (!in_array($row["cart_id"], $new_result_array[date('W', strtotime($row['createddate']))]['cartIds'])) {
                array_push($new_result_array[date('W', strtotime($row['createddate']))]['cartIds'], $row["cart_id"]);
            }
            $carts = $new_result_array[date('W', strtotime($row['createddate']))]['cartIds'];
            $new_result_array[date('W', strtotime($row['createddate']))]['total_cart_orders'] = count($new_result_array[date('W', strtotime($row['createddate']))]['cartIds']);
            $new_result_array[date('W', strtotime($row['createddate']))]['total_images'] += 1;

            $commision_on_small = $commision_on_large = $commision_on_printed = $total_price = 0;

            if (($row['lowfree_on_highpurchase'] == 0 && $row['is_small_photo'] == 1) || ($row['is_small_photo'] == 1 && $row['is_low_image_free'] == 0)) {
                $small_image_price = $row['low_resolution_price'];
                $total_price += $small_image_price;
                $percentage_commission_on_small = $small_image_price * ($percentage_Commision_on_digital_product / 100);
                $direct_commission_on_small = $direct_Commision_on_digital_product;
                if ($percentage_commission_on_small > $direct_commission_on_small) {
                    $commision_on_small = $percentage_commission_on_small;
                } else {
                    $commision_on_small = $direct_commission_on_small;
                }
            }

            if ($row['is_large_photo'] == 1 && $row['is_high_image_free'] == 0) {
                $large_image_price = $row['high_resolution_price'];
                $total_price += $large_image_price;
                $percentage_commission_on_large = $large_image_price * ($percentage_Commision_on_digital_product / 100);
                $direct_commission_on_large = $direct_Commision_on_digital_product;
                if ($percentage_commission_on_large > $direct_commission_on_large) {
                    $commision_on_large = $percentage_commission_on_large;
                } else {
                    $commision_on_large = $direct_commission_on_large;
                }
            }

            if ($row['offer_printed_souvenir'] == 1 && $row['is_frame'] == 1) {
                $printed_image_price = $row['printed_souvenir_price'];
                $total_price += $printed_image_price;
                $percentage_commission_on_printed = $printed_image_price * ($percentage_Commision_on_physical_product / 100);
                $direct_commission_on_printed = $direct_Commision_on_physical_product;
                if ($percentage_commission_on_printed > $direct_commission_on_printed) {
                    $commision_on_printed = $percentage_commission_on_printed;
                } else {
                    $commision_on_printed = $direct_commission_on_printed;
                }
            }
            $total_commision = $commision_on_small + $commision_on_large + $commision_on_printed;
            $deduction = $total_commision + $transaction_fee;

            $new_result_array[date('W', strtotime($row['createddate']))]['deduction'] += $deduction;
            If (!empty($total_price) && $total_price != 0) {
                $new_result_array[date('W', strtotime($row['createddate']))]['weekly_total_amount'] += $total_price;
                $new_result_array[date('W', strtotime($row['createddate']))]['weekly_total_payment'] = $new_result_array[date('W', strtotime($row['createddate']))]['weekly_total_amount'] - $new_result_array[date('W', strtotime($row['createddate']))]['deduction'];
            }
            $new_result_array[date('W', strtotime($row['createddate']))]['orders'][] = $row;
        }
//        p($new_result_array);
//        exit;
        $data["invoice_list"] = $new_result_array;
        $this->template->load('default', 'admin/invoice/business_invoices', $data);
    }

    /* list of Weekly Orders. */

    public function weekly_orders($business_id, $invoice_period) {
        $data['title'] = 'facetag | Manage Invoice';
        $business_detail = $this->businesses_model->get_business_by_id($business_id);
        $data['business_name'] = $business_detail['name'];
        $data['businessId'] = $business_id;
        $data["orders"] = $this->invoice_model->get_weekly_invoice_list($business_id, $invoice_period);
//        p($data["orders"]);
//        exit;
        $this->template->load('default', 'admin/invoice/weekly_orders', $data);
    }

    /* Transfer Payment. */

    public function transfer_payment($business_id) {
        $business_settings = $this->businesses_model->get_business_settings_by_id($business_id);
        $invoice_period = $this->input->post('invoice_period');
        $token_id = $this->input->post('token_id');
//        Stripe::setApiKey("sk_test_CQxqoWW70nXXmtFtmG605FvA");
        Stripe::setApiKey("sk_live_JqCgtjMEkJ96lMnsDVxlSn6a");
        if (isset($token_id)) {

            $amount = $this->input->post('amount');

            $description = "Business Payment";
            try {
//                $charge = Stripe_Charge::create(array(
//                            "amount" => $amount,
//                            "currency" => "aud",
//                            "source" => $token_id,
//                            "description" => $description)
//                );
                $recipient = Stripe_Recipient::create(array(
                            "name" => "Kirti Narola",
                            "type" => "individual",
                            "card" => $token_id,
                            "email" => "ku@narola.email")
                );
                $payout = Stripe_Payout::create(array(
                            "amount" => 1, // amount in cents
                            "currency" => "aud",
                            "recipient" => $recipient_id,
                            "bank_account" => $bank_account_id,
                            "statement_descriptor" => "facetag SALES")
                );
                $_SESSION['success_msg'] = "Payment is done Successfully!";
                $data['result'] = "success";
            } catch (Stripe_CardError $e) {
                $_SESSION['error_msg'] = "Payment is not done Successfully!";

                $error = $e->getMessage();
                $data['result'] = "declined1";
            } catch (Stripe_InvalidRequestError $e) {
                $error2 = $e->getMessage();
                $data['result'] = "declined2";
                $_SESSION['error_msg'] = "Payment is not done Successfully!";
            } catch (Stripe_AuthenticationError $e) {
                $data['result'] = "declined3";
                $_SESSION['error_msg'] = "Payment is not done Successfully!";
            } catch (Stripe_ApiConnectionError $e) {
                $_SESSION['error_msg'] = "Payment is not done Successfully!";
                $data['result'] = "declined4";
            } catch (Stripe_Error $e) {
                $_SESSION['error_msg'] = "Payment is not done Successfully!";
                $data['result'] = "declined5";
            } catch (Exception $e) {
                $_SESSION['error_msg'] = "Payment is not done Successfully!";
                $data['result'] = "declined6";
            }
        } else {
            $_SESSION['message'] = "There is an error while payment!";
            $data['result'] = 'error';
        }
        $orders = $this->invoice_model->get_weekly_invoice_list($business_id, $invoice_period);
        $str = '';
        foreach ($orders as $row) {
            $str .= $row['id'] . ',';
        }
        $cart_items = rtrim($str, ',');
        $arr = explode("to", $invoice_period, 2);
        $start_date = $arr[0];
        $end_date = $arr[1];

        $dataArr = array(
            'business_id' => $business_id,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'cart_items' => $cart_items,
            'status' => 1
        );
        $result = $this->invoice_model->insert_data(TBL_INVOICES, $dataArr);
        $data["invoiceid"] = $result;
        return $data;
        exit;
    }

    /* @anp : paypal api call for transfer payment. */

    public function PPHttpPost($methodName_, $nvpStr_) {

        // Set up your API credentials, PayPal end point, and API version.
        // How to obtain API credentials:
        // https://cms.paypal.com/us/cgi-bin/?cmd=_render-content&content_ID=developer/e_howto_api_NVPAPIBasics#id084E30I30RO
        $API_UserName = urlencode('nm.narola-facilitator_api1.narolainfotech.com');
        $API_Password = urlencode('PURBT7QJ8269REDX');
        $API_Signature = urlencode('An5ns1Kso7MWUdW4ErQKJJJ4qi4-ALAY2A-6E0F2AV4GES-mVAHKPval');
        $API_Endpoint = "https://api-3t.sandbox.paypal.com/nvp";
        $version = urlencode('51.0');

        // Set the curl parameters.
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $API_Endpoint);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);

        // Turn off the server and peer verification (TrustManager Concept).
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);

        // Set the API operation, version, and API signature in the request.
        $nvpreq = "METHOD=$methodName_&VERSION=$version&PWD=$API_Password&USER=$API_UserName&SIGNATURE=$API_Signature$nvpStr_";

        // Set the request as a POST FIELD for curl.
        curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);

        // Get response from the server.
        $httpResponse = curl_exec($ch);

        if (!$httpResponse) {
            exit("$methodName_ failed: " . curl_error($ch) . '(' . curl_errno($ch) . ')');
        }

        // Extract the response details.
        $httpResponseAr = explode("&", $httpResponse);

        $httpParsedResponseAr = array();
        foreach ($httpResponseAr as $i => $value) {
            $tmpAr = explode("=", $value);
            if (sizeof($tmpAr) > 1) {
                $httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
            }
        }

        if ((0 == sizeof($httpParsedResponseAr)) || !array_key_exists('ACK', $httpParsedResponseAr)) {
            exit("Invalid HTTP Response for POST request($nvpreq) to $API_Endpoint.");
        }

        return $httpParsedResponseAr;
    }

    /* @anp: transfer payment with PayPal. */

    public function paypal_transfer_payment($business_id) {
        $business_settings = $this->businesses_model->get_business_settings_by_id($business_id);
        $paypal_email_address = $business_settings['paypal_email_address'];
        $invoice_period = $this->input->post('invoice_period');
        $amount = $this->input->post('amount');

        // Set request-specific fields.
        $vEmailSubject = 'Facetag payment';
        $emailSubject = urlencode($vEmailSubject);
        $receiverType = urlencode('EmailAddress');
        $currency = urlencode('USD'); // or other currency ('GBP', 'EUR', 'JPY', 'CAD', 'AUD')
        // Receivers
        // Use '0' for a single receiver. In order to add new ones: (0, 1, 2, 3...)
        // Here you can modify to obtain array data from database.
        $receivers = array(
            array(
                'receiverEmail' => $paypal_email_address,
                'amount' => $amount,
                'note' => " Facetag payment")
        );
        $receiversLenght = count($receivers);

        // Add request-specific fields to the request string.
        $nvpStr = "&EMAILSUBJECT=$emailSubject&RECEIVERTYPE=$receiverType&CURRENCYCODE=$currency";

        $receiversArray = array();

        for ($i = 0; $i < $receiversLenght; $i++) {
            $receiversArray[$i] = $receivers[$i];
        }

        foreach ($receiversArray as $i => $receiverData) {
            $receiverEmail = urlencode($receiverData['receiverEmail']);
            $amount = urlencode($receiverData['amount']);
            $note = urlencode($receiverData['note']);
            $nvpStr .= "&L_EMAIL$i=$receiverEmail&L_Amt$i=$amount&L_NOTE$i=$note";
        }

        // Execute the API operation; see the PPHttpPost function above.
        $httpParsedResponseAr = $this->PPHttpPost('MassPay', $nvpStr);

        if ("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) {
            $orders = $this->invoice_model->get_weekly_invoice_list($business_id, $invoice_period);
            $str = '';
            foreach ($orders as $row) {
                $str .= $row['id'] . ',';
            }
            $cart_items = rtrim($str, ',');
            $arr = explode("to", $invoice_period, 2);
            $start_date = $arr[0];
            $end_date = $arr[1];

            $dataArr = array(
                'business_id' => $business_id,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'cart_items' => $cart_items,
                'status' => 1
            );
            $result = $this->invoice_model->insert_data(TBL_INVOICES, $dataArr);
            $data["invoiceid"] = $result;
            $_SESSION['success_msg'] = "Payment is done Successfully!";
            $data['result'] = "success";
        } else {
            $_SESSION['message'] = "There is an error while payment!";
            $data['result'] = 'error';
        }
        $data['result'] = "success";
        echo json_encode($data);
        exit;
    }

    public function all_invoices() {
        $orders = $this->invoice_model->get_all_invoice_list();
        $final_array = [];
        $new_result_array = [];
        $new_result_arr = [];
        $average_prices = [];
        foreach ($orders as $row) {
            if (!isset($new_result_arr[$row['businessId']])) {
                $new_result_arr[$row['businessId']] = [];
                $new_result_arr[$row['businessId']]['business_id'] = $row['businessId'];
                $new_result_arr[$row['businessId']]['orders'] = [];
            }
            $new_result_arr[$row['businessId']]['orders'][] = $row;
        }
//        foreach ($new_result_array[$row['businessId']]['orders'] as $row) {
//            
//            if (!isset($final_array[date('W', strtotime($row['created']))])) {
//                 $final_array[date('W', strtotime($row['created']))] = [];
//                 $new_result_array[date('W', strtotime($row['created']))]['orders'] = [];
//            }
//            $new_result_array[date('W', strtotime($row['created']))]['orders'] = $row;
//        }
        foreach ($new_result_arr as $row) {
            foreach ($row['orders'] as $row) {

                //Initialize the index's if they dont exist
                if (!isset($new_result_array[date('W', strtotime($row['created']))])) {
                    $new_result_array[date('W', strtotime($row['created']))] = [];
                    $new_result_array[date('W', strtotime($row['created']))]['weekly_total_amount'] = 0;
                    $new_result_array[date('W', strtotime($row['created']))]['total_cart_orders'] = 0;
                    $new_result_array[date('W', strtotime($row['created']))]['total_images'] = 0;
                    $new_result_array[date('W', strtotime($row['created']))]['orders'] = [];
                    $new_result_array[date('W', strtotime($row['created']))]['cartIds'] = [];
                }
                //Edited to wrap $row['createdDate'] in strtotime() as all dates are returning week 1 
                $d = $this->invoice_model->x_week_range($row['created']);
                $new_result_array[date('W', strtotime($row['created']))]['start_date'] = $d[0];
                $new_result_array[date('W', strtotime($row['created']))]['end_date'] = $d[1];
                $new_result_array[date('W', strtotime($row['created']))]['invoice_period'] = $d[0] . ' to ' . $d[1];
//            $new_result_array[date('W', strtotime($row['created']))]['businessId'] = $business_id;

                if (!in_array($row["cart_id"], $new_result_array[date('W', strtotime($row['created']))]['cartIds'])) {
                    $new_result_array[date('W', strtotime($row['created']))]['weekly_total_amount'] += $row['total_amount'];
                    array_push($new_result_array[date('W', strtotime($row['created']))]['cartIds'], $row["cart_id"]);
                }
                $new_result_array[date('W', strtotime($row['created']))]['total_cart_orders'] = count($new_result_array[date('W', strtotime($row['created']))]['cartIds']);
                $new_result_array[date('W', strtotime($row['created']))]['total_images'] += 1;
                $new_result_array[date('W', strtotime($row['created']))]['orders'][] = $row;
            }
            $final_array[] = $new_result_array;
        }
    }

}
