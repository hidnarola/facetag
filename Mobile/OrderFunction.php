<?php
/**
 * Created by PhpStorm.
 * User: c119
 * Date: 03/03/15
 * Time: 4:16 PM
 */
require_once 'SecurityFunctions.php';
//require_once "../Stripe/init.php";
require_once "Stripe/init.php";

class OrderFunction
{
    function __construct()
    {


        \Stripe\Stripe::setApiKey("sk_test_usZEvRtgKNGX9EoRRDLHEmCG");

    }

    public function call_service($service, $postData)
    {
        switch ($service) {
            case "PutOrder": {
                return $this->putOrder($postData);
            }
                break;
            case "PurchaseFreeImage": {
                return $this->purchaseFreeImage($postData);
            }
                break;
            case "GetUserOrder": {
                return $this->getUserOrder($postData);
            }
                break;
            case  "GetUserOrderData": {
                return $this->getUserOrderData($postData);
            }
        }
    }


    public function getUserOrder($orderData)
    {

        $connection = $GLOBALS['con'];
        $status = 2;
        $posts = array();
        $order = array();
        $errorMsg = "Service responce data";

        $userid = validateObject($orderData, 'userId', "");
        $userid = addslashes($userid);


        //Get cartid
        $select_cart_query = "select  * from " . TABLE_CART . " where user_id = ? order by created desc";
        //echo $select_cart_query;exit;
        $select_cart_stmt = $connection->prepare($select_cart_query);
        $select_cart_stmt->bind_param("i", $userid);

        if ($select_cart_stmt->execute()) {
            $select_cart_stmt->store_result();
            if ($select_cart_stmt->num_rows > 0) {
                while ($cart = fetch_assoc_all_values($select_cart_stmt)) {
                    //cart detilas found within $cart
                    //select cart items

                    $select_items_query = "select cartitem.id as cartitemid,cartitem.is_small_photo as issmall,cartitem.is_large_photo as islarge,
                                          cartitem.is_frame as isframe,cartitem.selfie_id as selfieid,cartitem.created as purchasedate,
                                          icpimagetag.*,
                                          icpimage.image,
                                          icps.*,
                                          businesses.*,
                                          icpsettings.preview_photo
                                          from " . TABLE_CART_ITEM . " as cartitem
                                          left join (select id,icp_image_id,is_small_purchase,is_large_purchase,is_printed_purchase from " . TABLE_ICP_IMAGE_TAG . ") as icpimagetag on cartitem.selfie_id = icpimagetag.id
                                          left join (select id,icp_id,image from " . TABLE_ICP_IMAGE . ") as icpimage on icpimage.id = icpimagetag.icp_image_id
                                          left join (select id as icpid,business_id,low_resolution_price,high_resolution_price,printed_souvenir_price from " . TABLE_ICPS . ") as icps on icps.icpid = icpimage.icp_id
                                          left join (select id,icp_id,preview_photo from ". TABLE_ICP_SETTING .") as icpsettings on icps.icpid = icpsettings.icp_id
                                          left join (select id as businessid,name as businessname from " . TABLE_BUSINESS . ") as businesses on businesses.businessid = icps.business_id
                                          where cart_id = ?";

                    //echo $select_items_query;exit;
                    //echo $cart['id'];
                    $select_items_stmt = $connection->prepare($select_items_query);
                    $select_items_stmt->bind_param("i", $cart['id']);
                    if ($select_items_stmt->execute()) {
                        $cartitems = array();
                        $select_items_stmt->store_result();
                        while ($cartitem = fetch_assoc_all_values($select_items_stmt)) {
                            array_push($cartitems, $cartitem);
                        }
                        //print_r($cartitems);exit;
                        //$order = $cartitems;
                        $order = $cart;
                        $order["cartitem"] = $cartitems;
                        $posts[] = $order;
                    } else {
                        $status = 2;
                        $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                        $data['message'] = "Please try again !!!" . $select_cart_stmt->error;
                        return $data;
                    }
                }

                $status = 1;
                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                $data['message'] = "List of order items !!!" . $select_cart_stmt->error;
                $data['order'] = $posts;
                return $data;
            } else {
                $status = 1;
                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                $data['message'] = "Order not found !!!" . $select_cart_stmt->error;
                return $data;
            }
        } else {
            $status = 2;
            $data['status'] = ($status > 1) ? FAILED : SUCCESS;
            $data['message'] = "Please try again !!!";
            return $data;

        }

    }

    public function getUserOrderData($orderData)
    {

        $connection = $GLOBALS['con'];
        $status = 2;
        $posts = array();
        $order = array();
        $errorMsg = "Service responce data";

        $userid = validateObject($orderData, 'userId', "");
        $userid = addslashes($userid);

        $noofrecord = validateObject($orderData, 'noofrecord', "");
        $noofrecord = addslashes($noofrecord);

        $offset = validateObject($orderData, 'offset', "");
        $offset = addslashes($offset);

        //Get cartid
        $select_cart_query = "select  * from " . TABLE_CART . " where user_id = ? order by created desc limit ?,?";
        //echo $select_cart_query;exit;
        $select_cart_stmt = $connection->prepare($select_cart_query);
        $select_cart_stmt->bind_param("iii", $userid, $offset, $noofrecord);

        if ($select_cart_stmt->execute()) {
            $select_cart_stmt->store_result();
            if ($select_cart_stmt->num_rows > 0) {
                while ($cart = fetch_assoc_all_values($select_cart_stmt)) {
                    //cart detilas found within $cart
                    //select cart items

                    $select_items_query = "select cartitem.id as cartitemid,cartitem.is_small_photo as issmall,cartitem.is_large_photo as islarge,
                                          cartitem.is_frame as isframe,cartitem.selfie_id as selfieid,cartitem.created as purchasedate,
                                          icpimagetag.*,
                                          icpimage.image,
                                          icps.*,
                                          businesses.*
                                          from " . TABLE_CART_ITEM . " as cartitem
                                          left join (select id,icp_image_id,is_small_purchase,is_large_purchase,is_printed_purchase from " . TABLE_ICP_IMAGE_TAG . ")
                                           as icpimagetag on cartitem.selfie_id = icpimagetag.id
                                          left join (select id,icp_id,image from " . TABLE_ICP_IMAGE . ") as icpimage on icpimage.id = icpimagetag.icp_image_id
                                          left join (select id as icpid,icp_logo,business_id,low_resolution_price,high_resolution_price,printed_souvenir_price
                                          from " . TABLE_ICPS . ") as icps on icps.icpid = icpimage.icp_id
                                          left join (select id as businessid,name as businessname from " . TABLE_BUSINESS . ") as businesses on
                                          businesses.businessid = icps.business_id
                                          where cart_id = ?";

                    //echo $select_items_query;exit;
                    //echo $cart['id'];
                    $select_items_stmt = $connection->prepare($select_items_query);
                    $select_items_stmt->bind_param("i", $cart['id']);
                    if ($select_items_stmt->execute()) {
                        $cartitems = array();
                        $select_items_stmt->store_result();
                        while ($cartitem = fetch_assoc_all_values($select_items_stmt)) {
                            array_push($cartitems, $cartitem);
                        }
                        //print_r($cartitems);exit;
                        //$order = $cartitems;
                        $order = $cart;
                        $order["cartitem"] = $cartitems;
                        $posts[] = $order;
                    } else {
                        $status = 2;
                        $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                        $data['message'] = "Please try again !!!" . $select_cart_stmt->error;
                        return $data;
                    }
                }

                $status = 1;
                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                $data['message'] = "List of order items !!!" . $select_cart_stmt->error;
                $data['order'] = $posts;
                return $data;
            } else {
                $status = 1;
                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                $data['message'] = "Order not found !!!" . $select_cart_stmt->error;
                return $data;
            }
        } else {
            $status = 2;
            $data['status'] = ($status > 1) ? FAILED : SUCCESS;
            $data['message'] = "Please try again !!!";
            return $data;

        }

    }


    function gettoken($userdata)
    {

        // print_r($userdata);exit();
        $token = \Stripe\Token::create(array(
            "card" => array(
                "number" => $userdata['card'],
                "exp_month" => $userdata['mnth'],
                "exp_year" => $userdata['yr'],
                "cvc" => $userdata['cvc']
            )
        ));

        return $token->id;

    }

    private function getCentsFromDollar($dollars)
    {
        //$cents = bcmul($dollars, 100);
        $cents = $dollars * 100;
        return $cents;
    }

    public function purchaseFreeImage($orderData)
    {

        $connection = $GLOBALS['con'];
        $status = 2;
        $posts = array();
        $errorMsg = "Service responce data";

        $userid = validateObject($orderData, 'userId', "");
        $userid = addslashes($userid);

        $totalAmount = validateObject($orderData, 'total_amount', "");
        $totalAmount = addslashes($totalAmount);

        $cartItem = array();
        $cartItem = validateObject($orderData, 'cartItem', "");

        $addressId = validateObject($orderData, 'addressId', "");
        $addressId = addslashes($addressId);

        $address = array();
        $address = validateObject($orderData, 'address', "");

            //Add shipping Address

            if (strlen($addressId) == 0) {

                $company = validateObject($address[0], 'company', "");
                $building_description = validateObject($address[0], 'building_description', "");
                $city = validateObject($address[0], 'city', "");
                $state = validateObject($address[0], 'state', "");
                $country_id = validateObject($address[0], 'country_id', "");
                $post_code = validateObject($address[0], 'post_code', "");
                $phone_no = validateObject($address[0], 'phone_no', "");
                $is_permanent_address = validateObject($address[0], 'is_permanent_address', "");
                $modified = validateObject($address[0], 'modified', "");

                $insertFields = "user_id,
                             company,
                             building_description,
                             city,
                             state,
                             country_id,
                             post_code,
                             phone_no,
                             is_permanent_address,
                             modified
                             ";

                $valuesFields = "?,?,?,?,?,?,?,?,?,?";
                $insert_query = "" . "Insert into " . TABLE_ADDRESS . " (" . $insertFields . ") values (" . $valuesFields . ")";
                $insert_address_stmt = $connection->prepare($insert_query);
                $modifieddate = date("Y-m-d H:i:s");
                $insert_address_stmt->bind_param('issiiissss',
                    $userid, $company, $building_description, $city,
                    $state, $country_id, $post_code, $phone_no, $is_permanent_address, $modifieddate);

                if ($insert_address_stmt->execute()) {
                    $addressId = mysqli_insert_id($connection);
                } else {
                    $status = 2;
                    $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                    $data['message'] = "Fail to save address !!!";
                    return $data;
                }
            }

            //Place Order Details

            //Add cart data
            $insertFields = "user_id,
                             total_amount,
                             payment_type,
                             card_id,
                             modified";

            $valuesFields = "?,?,?,?,?";
            $insert_query = "" . "Insert into " . TABLE_CART . " (" . $insertFields . ") values (" . $valuesFields . ")";
            $insert_cart_stmt = $connection->prepare($insert_query);
            $modifieddate = date("Y-m-d H:i:s");
            $cardId = "";
            $paymentType = "";
            $insert_cart_stmt->bind_param('iiiis', $userid, $totalAmount, $paymentType, $cardId, $modifieddate);

            if ($insert_cart_stmt->execute()) {
                $cartId = mysqli_insert_id($connection);
            } else {
                $status = 2;
                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                $data['message'] = "Fail to save cart data !!!" . $insert_cart_stmt->error;
                return $data;
            }

            //Attach address with cart
            $insertFields = "cart_id,
                             shipping_address_id,
                             modified";

            $valuesFields = "?,?,?";
            $insert_query = "" . "Insert into " . TABLE_ORDER_DETAILS . " (" . $insertFields . ") values (" . $valuesFields . ")";
            $insert_order_details_stmt = $connection->prepare($insert_query);
            $modifieddate = date("Y-m-d H:i:s");
            $insert_order_details_stmt->bind_param('iis', $cartId, $addressId, $modifieddate);

            if (!($insert_order_details_stmt->execute())) {
                $status = 2;
                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                $data['message'] = "Fail to attch address with order !!!" . $insert_order_details_stmt->error;
                return $data;
            }

            //Save cart item
            foreach ($cartItem as $item) {
                $selfieid = validateObject($item, 'selfieid', "");
                $businessid = validateObject($item, 'businessid', "");
                $is_small_photo = validateObject($item, 'is_small_photo', "");
                $is_large_photo = validateObject($item, 'is_large_photo', "");
                $is_frame = validateObject($item, 'is_frame', "");

                $insertFields = "cart_id,
                             business_id,
                             selfie_id,
                             is_small_photo,
                             is_large_photo,
                             is_frame,
                             modified";

                $valuesFields = "?,?,?,?,?,?,?";
                $insert_query = "" . "Insert into " . TABLE_CART_ITEM . " (" . $insertFields . ") values (" . $valuesFields . ")";
                $insert_cart_item_stmt = $connection->prepare($insert_query);
                $modifieddate = date("Y-m-d H:i:s");
                $insert_cart_item_stmt->bind_param('iiiiiis', $cartId,$businessid, $selfieid, $is_small_photo, $is_large_photo, $is_frame, $modifieddate);

                if (!$insert_cart_item_stmt->execute()) {
                    $status = 2;
                    $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                    $data['message'] = "Fail to save cart item data !!!" . $insert_cart_item_stmt->error;
                    return $data;
                }
            }

            //With consideration payment is done
            $isPaymentDone = true;

            if ($isPaymentDone) {
                //update payment done for cart

                $largeImages = array();

                $update_cart_payment = "update " . TABLE_CART . " set is_payment_done = ? where id = ?";
                $update_cart_payment_stmt = $connection->prepare($update_cart_payment);
                $paymentDone = "1";
                $update_cart_payment_stmt->bind_param("si", $paymentDone, $cartId);

                if ($update_cart_payment_stmt->execute()) {
                    //update is purchage flag for selfie

                    foreach ($cartItem as $item) {
                        $selfieid = validateObject($item, 'selfieid', "");
                        $is_small_photo = validateObject($item, 'is_small_photo', "");
                        $is_large_photo = validateObject($item, 'is_large_photo', "");
                        $is_frame = validateObject($item, 'is_frame', "");
                        $is_purchased = 1;

                        if ($is_large_photo == 1) {
                            $largeImages[] = $selfieid;
                        }

                        //print_r($largeImages);

                        $update_purchase_flag = "update " . TABLE_ICP_IMAGE_TAG . " set is_small_purchase = ? , is_large_purchase = ? ,
                        is_printed_purchase = ? , is_purchased = ? where id = ?
                        and user_id = ?";

                        $update_purchase_flag_stmt = $connection->prepare($update_purchase_flag);
                        $update_purchase_flag_stmt->bind_param("iiiiii", $is_small_photo, $is_large_photo, $is_frame, $is_purchased, $selfieid, $userid);

                        if (!$update_purchase_flag_stmt->execute()) {
                            $status = 2;
                            $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                            $data['message'] = "Fail to update is_purchage for photo !!!" . $insert_cart_item_stmt->error;
                            return $data;
                        }
                    }
//
//                    $img_path = array();
//
//                    foreach ($largeImages as $image) {
//
//                        $mail_images_query = "select icp_image.image from icp_image_tag as tag_image left join (select * from icp_images) as
//                        icp_image on tag_image.icp_image_id = icp_image.id where tag_image.id = ? and tag_image.user_id = ?";
//
//                        $mail_images_stmt = $connection->prepare($mail_images_query);
//
//                        $mail_images_stmt->bind_param("ii", $image, $userid);
//                        if ($mail_images_stmt->execute()) {
//
//                            $mail_images_stmt->store_result();
//                            if($mail_images_stmt->num_rows > 0){
//                                while ($path = fetch_assoc_all_values($mail_images_stmt))
//                                {
//                                    $img_path[] = $path;
//                                }
//                            }
//                        }
//                    }
                } else {
                    $status = 2;
                    $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                    $data['message'] = "Fail to update payment flag for cart !!!" . $insert_cart_item_stmt->error;
                    return $data;
                }
            }

        $status = 1;
        $data['status'] = ($status > 1) ? FAILED : SUCCESS;
        $data['message'] = "Order placed successfully !!!";
        return $data;

    }

    public function putOrder($orderData)
    {

        $connection = $GLOBALS['con'];
        $status = 2;
        $posts = array();
        $errorMsg = "Service responce data";

        $userid = validateObject($orderData, 'userId', "");
        $userid = addslashes($userid);

        $totalAmount = validateObject($orderData, 'total_amount', "");
        $totalAmount = addslashes($totalAmount);

//        $isPaymentDone = validateObject($orderData, 'isPaymentDone', "");
//        $isPaymentDone = addslashes($isPaymentDone);

        $paymentType = validateObject($orderData, 'paymentType', "");
        $paymentType = addslashes($paymentType);

        $cardId = validateObject($orderData, 'cardId', "");
        $cardId = addslashes($cardId);

        $cardInfo = array();
        $cardInfo = validateObject($orderData, 'cardInfo', "");

//        print_r($orderData);
//        exit;

        $cartItem = array();
        $cartItem = validateObject($orderData, 'cartItem', "");

        $addressId = validateObject($orderData, 'addressId', "");
        $addressId = addslashes($addressId);

        $address = array();
        $address = validateObject($orderData, 'address', "");

        $isValidCard = false;



        if (strlen($cardId) == 0) {

            //print_r($cardInfo);exit;
            $card_number = validateObject($cardInfo[0], 'card_number', "");
            $name_on_card = validateObject($cardInfo[0], 'name_on_card', "");
            $expiry_month = validateObject($cardInfo[0], 'expiry_month', "");
            $expiry_year = validateObject($cardInfo[0], 'expiry_year', "");
            $cvv_code = validateObject($cardInfo[0], 'cvv_code', "");
            $is_saved = validateObject($cardInfo[0], 'is_saved', "");

            if ($totalAmount != 0) {
                $userdata['card'] = $card_number;
                $userdata['mnth'] = $expiry_month;
                $userdata['yr'] = $expiry_year;
                $userdata['cvc'] = $cvv_code;

                try {
                    $token = $this->gettoken($userdata);
                    $isValidCard = true;
                } catch (Exception $ex) {
                    $isValidCard = false;
                    $status = 2;
                    $data = array();
                    $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                    $data['message'] = $ex->getMessage();
                    return $data;
                }
            }

            $select_card_query = "select * from " . TABLE_CARD . " where card_number = ? and user_id = ?";
            $select_card_stmt = $connection->prepare($select_card_query);
            $select_card_stmt->bind_param('si', $card_number, $userid);

            if ($select_card_stmt->execute()) {
                $select_card_stmt->store_result();
                if ($select_card_stmt->num_rows > 0) {
                    //update card
                    $card = fetch_assoc_all_values($select_card_stmt);
                    $cardId = $card["id"];
                    $update_card_query = "update " . TABLE_CARD . " set is_saved = ? where id = ?";
                    $update_card_stmt = $connection->prepare($update_card_query);
                    $update_card_stmt->bind_param('ii', $is_saved, $cardId);
                    if (!$update_card_stmt->execute()) {
                        $status = 2;
                        $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                        $data['message'] = "Fail to save card data !!!";
                        return $data;
                    }
                } else {
                    $insertFields = "user_id,
                             card_number,
                             name_on_card,
                             expiry_month,
                             expiry_year,
                             cvv_code,
                             is_saved,
                             modified
                             ";

                    $valuesFields = "?,?,?,?,?,?,?,?";
                    $insert_query = "" . "Insert into " . TABLE_CARD . " (" . $insertFields . ") values (" . $valuesFields . ")";
                    $insert_card_stmt = $connection->prepare($insert_query);
                    $modifieddate = date("Y-m-d H:i:s");
                    $insert_card_stmt->bind_param('iisssiis', $userid, $card_number, $name_on_card, $expiry_month, $expiry_year, $cvv_code, $is_saved, $modifieddate);

                    if ($insert_card_stmt->execute()) {
                        $cardId = mysqli_insert_id($connection);
                    } else {

                        $status = 2;
                        $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                        $data['message'] = "Please try again";
                        return $data;
                    }
                }
            }
        } else {

            //If card is saved
            if ($totalAmount != 0) {

                $select_card_query = "select * from " . TABLE_CARD . " where id = ?";
                $select_card_stmt = $connection->prepare($select_card_query);
                $select_card_stmt->bind_param('i', $cardId);
                if ($select_card_stmt->execute()) {
                    $select_card_stmt->store_result();
                    if ($select_card_stmt->num_rows > 0) {

                        while ($card = fetch_assoc_all_values($select_card_stmt)) {

                            $userdata['card'] = $card['card_number'];
                            $userdata['mnth'] = $card['expiry_month'];
                            $userdata['yr'] = $card['expiry_year'];
                            $userdata['cvc'] = $card['cvv_code'];

                            try {
                                $token = $this->gettoken($userdata);
                                $isValidCard = true;
                            } catch (Exception $ex) {
                                $isValidCard = false;

                                $status = 2;
                                $data = array();
                                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                                $data['message'] = $ex->getMessage();
                                return $data;
                            }

                        }
                    }

                }
            }
        }


            //Add shipping Address
                if ($isValidCard) {

                    if (strlen($addressId) == 0) {

                        $company = validateObject($address[0], 'company', "");
                        $building_description = validateObject($address[0], 'building_description', "");
                        $city = validateObject($address[0], 'city', "");
                        $state = validateObject($address[0], 'state', "");
                        $country_id = validateObject($address[0], 'country_id', "");
                        $post_code = validateObject($address[0], 'post_code', "");
                        $phone_no = validateObject($address[0], 'phone_no', "");
                        $is_permanent_address = validateObject($address[0], 'is_permanent_address', "");
                        $modified = validateObject($address[0], 'modified', "");

                        $insertFields = "user_id,
                             company,
                             building_description,
                             city,
                             state,
                             country_id,
                             post_code,
                             phone_no,
                             is_permanent_address,
                             modified
                             ";

                        $valuesFields = "?,?,?,?,?,?,?,?,?,?";
                        $insert_query = "" . "Insert into " . TABLE_ADDRESS . " (" . $insertFields . ") values (" . $valuesFields . ")";
                        $insert_address_stmt = $connection->prepare($insert_query);
                        $modifieddate = date("Y-m-d H:i:s");
                        $insert_address_stmt->bind_param('issiiissss',
                            $userid, $company, $building_description, $city,
                            $state, $country_id, $post_code, $phone_no, $is_permanent_address, $modifieddate);

                        if ($insert_address_stmt->execute()) {
                            $addressId = mysqli_insert_id($connection);
                        } else {
                            $status = 2;
                            $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                            $data['message'] = "Fail to save address !!!";
                            return $data;
                        }
                    }

                    //Place Order Details

                    //Add cart data
                    $insertFields = "user_id,
                             total_amount,
                             payment_type,
                             card_id,
                             modified";

                    $valuesFields = "?,?,?,?,?";
                    $insert_query = "" . "Insert into " . TABLE_CART . " (" . $insertFields . ") values (" . $valuesFields . ")";
                    $insert_cart_stmt = $connection->prepare($insert_query);
                    $modifieddate = date("Y-m-d H:i:s");
                    $insert_cart_stmt->bind_param('iiiis', $userid, $totalAmount, $paymentType, $cardId, $modifieddate);

                    if ($insert_cart_stmt->execute()) {
                        $cartId = mysqli_insert_id($connection);
                    } else {
                        $status = 2;
                        $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                        $data['message'] = "Fail to save cart data !!!" . $insert_cart_stmt->error;
                        return $data;
                    }

                    //Attach address with cart
                    $insertFields = "cart_id,
                             shipping_address_id,
                             modified";

                    $valuesFields = "?,?,?";
                    $insert_query = "" . "Insert into " . TABLE_ORDER_DETAILS . " (" . $insertFields . ") values (" . $valuesFields . ")";
                    $insert_order_details_stmt = $connection->prepare($insert_query);
                    $modifieddate = date("Y-m-d H:i:s");
                    $insert_order_details_stmt->bind_param('iis', $cartId, $addressId, $modifieddate);

                    if (!($insert_order_details_stmt->execute())) {
                        $status = 2;
                        $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                        $data['message'] = "Fail to attch address with order !!!" . $insert_order_details_stmt->error;
                        return $data;
                    }

                    //Save cart item
                    foreach ($cartItem as $item) {
                        $businessid = validateObject($item, 'businessid', "");
                        $selfieid = validateObject($item, 'selfieid', "");
                        $is_small_photo = validateObject($item, 'is_small_photo', "");
                        $is_large_photo = validateObject($item, 'is_large_photo', "");
                        $is_frame = validateObject($item, 'is_frame', "");

                        $insertFields = "cart_id,
                             business_id,
                             selfie_id,
                             is_small_photo,
                             is_large_photo,
                             is_frame,
                             modified";

                        $valuesFields = "?,?,?,?,?,?,?";
                        $insert_query = "" . "Insert into " . TABLE_CART_ITEM . " (" . $insertFields . ") values (" . $valuesFields . ")";
                        $insert_cart_item_stmt = $connection->prepare($insert_query);
                        $modifieddate = date("Y-m-d H:i:s");
                        $insert_cart_item_stmt->bind_param('iiiiiis', $cartId,$businessid, $selfieid, $is_small_photo, $is_large_photo, $is_frame, $modifieddate);

                        if (!$insert_cart_item_stmt->execute()) {
                            $status = 2;
                            $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                            $data['message'] = "Fail to save cart item data !!!" . $insert_cart_item_stmt->error;
                            return $data;
                        }
                    }


                    //Make Payment with Stripe
                    try {

                        $token = $this->gettoken($userdata);
                        $cents = $this->getCentsFromDollar($totalAmount);
                        $var = (int)$cents;
                        //echo "Hi" .$var; exit();

                        $chargeArray = array(
                            "amount" => $var,
                            "currency" => "usd",
                            "source" => $token,
                            "description" => "Charge for facetag"
                        );


                        $charge = \Stripe\Charge::create($chargeArray);

//                        echo json_encode($charge);
//                        exit;

                        $payStatus = json_encode($charge['status']);
                        if($payStatus == '"succeeded"')
                        {
                            $isPaymentDone = true;
                        }
                        else
                        {
                            $isPaymentDone = false;
                        }


                    } catch (Exception $ex) {

                        $status = 2;
                        $data = array();
                        $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                        $data['message'] = $ex->getMessage();
                        return $data;
                    }

                }
                else
                {
                    $status = 2;
                    $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                    $data['message'] = "Card detils not valid !!!";
                    return $data;
                }

            //With consideration payment is done
            //$isPaymentDone = true;

            if ($isPaymentDone) {
                //update payment done for cart

                $largeImages = array();

                $update_cart_payment = "update " . TABLE_CART . " set is_payment_done = ? where id = ?";
                $update_cart_payment_stmt = $connection->prepare($update_cart_payment);
                $paymentDone = "1";
                $update_cart_payment_stmt->bind_param("si", $paymentDone, $cartId);

                if ($update_cart_payment_stmt->execute()) {
                    //update is purchage flag for selfie

                    foreach ($cartItem as $item) {
                        $selfieid = validateObject($item, 'selfieid', "");
                        $is_small_photo = validateObject($item, 'is_small_photo', "");
                        $is_large_photo = validateObject($item, 'is_large_photo', "");
                        $is_frame = validateObject($item, 'is_frame', "");
                        $is_purchased = 1;

                        if ($is_large_photo == 1) {
                            $largeImages[] = $selfieid;
                        }

                        //print_r($largeImages);

                        $update_purchase_flag = "update " . TABLE_ICP_IMAGE_TAG . " set is_small_purchase = ? , is_large_purchase = ? ,
                        is_printed_purchase = ? , is_purchased = ? where id = ?
                        and user_id = ?";

                        $update_purchase_flag_stmt = $connection->prepare($update_purchase_flag);
                        $update_purchase_flag_stmt->bind_param("iiiiii", $is_small_photo, $is_large_photo, $is_frame, $is_purchased, $selfieid, $userid);

                        if (!$update_purchase_flag_stmt->execute()) {
                            $status = 2;
                            $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                            $data['message'] = "Fail to update is_purchage for photo !!!" . $insert_cart_item_stmt->error;
                            return $data;
                        }
                    }


                    $img_path = array();
                    foreach ($largeImages as $image)
                    {
                        $mail_images_query = "select icp_image.image,user.username ,user.email from ". TABLE_ICP_IMAGE_TAG ." as tag_image
                        left join (select * from ". TABLE_ICP_IMAGE .") as icp_image on tag_image.icp_image_id = icp_image.id
                        left join (select id,username,email from ". TABLE_USER .") as user on user.id = tag_image.user_id
                        where tag_image.icp_image_id = ? and tag_image.user_id = ?";
                        $mail_images_stmt = $connection->prepare($mail_images_query);
                        $mail_images_stmt->bind_param("ii", $image, $userid);

                        if ($mail_images_stmt->execute())
                        {
                            $mail_images_stmt->store_result();
                            if($mail_images_stmt->num_rows > 0){
                               // $user = fetch_assoc_all_values($mail_images_stmt);

                                while ($path = fetch_assoc_all_values($mail_images_stmt))
                                {
                                    $path = "../uploads/icp_images/".$path['image'];
                                    $img_path[] = $path;

                                }

                                $body = "Hi, please download the facetag detected selfie.";
                                $subject = "Facetag Selfie";
                                include_once 'SendAttachmentMail.php';
                                $objEmail = new SendAttachmentMail();
                                $objEmail->sendEmail($body,"ank@narola.email",$subject,$img_path);
                            }
                        }
                    }




                } else {
                    $status = 2;
                    $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                    $data['message'] = "Fail to update payment flag for cart !!!" . $insert_cart_item_stmt->error;
                    return $data;
                }
            }

        $status = 1;
        $data['status'] = ($status > 1) ? FAILED : SUCCESS;
        $data['message'] = "Order placed successfully !!!";
        return $data;
    }
}

?>