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
        //\Stripe\Stripe::setApiKey("sk_test_usZEvRtgKNGX9EoRRDLHEmCG"); // Test
        //\Stripe\Stripe::setApiKey("pk_live_n98FZPvZlmZ0djsAm6QMulLh"); // live

        //Live Strip Key
        \Stripe\Stripe::setApiKey("abc"); // live
    }

    public function call_service($service, $postData)
    {
        switch ($service)
        {
            case "SaveOrder":
            {
                return $this->saveOrder($postData);
            }
                break;
            case "SaveOrderData":
            {
                return $this->saveOrderData($postData);
            }
                break;
            case "CancelOrder":
            {
                return $this->cancelOrder($postData);
            }
                break;
            case  "MakePaymentWithPayPal":
            {
                return $this->makePaymentWithPayPal($postData);
            }

            case  "MakePaymentWithStripe":
            {
                return $this->makePaymentWithStripe($postData);
            }

            case "PutOrder":
            {
                return $this->putOrder($postData);
            }
                break;
            case "PurchaseFreeImage":
            {
                return $this->purchaseFreeImage($postData);
            }
                break;
            case "GetUserOrder":
            {
                return $this->getUserOrder($postData);
            }
                break;
            case  "GetUserOrderData":
            {
                return $this->getUserOrderData($postData);
            }
            case  "PurchaseWithPaypal":
            {
                return $this->purchaseWithPaypal($postData);
            }
            case  "UpdatePaymentStatus":
            {
                return $this->updatePaymentStatus($postData);
            }

        }
    }

    public function makePaymentWithStripe($orderData)
    {

        $connection = $GLOBALS['con'];
        $status = 2;
        $posts = array();
        $errorMsg = "Service responce data";

        $userid = validateObject($orderData, 'userId', "");
        $userid = addslashes($userid);

        $userName = validateObject($orderData, 'username', "");
        $userName = addslashes($userName);

        $email = validateObject($orderData, 'email', "");
        $email = addslashes($email);

        $orderId = validateObject($orderData, 'orderId', "");
        $orderId = addslashes($orderId);

        $totalAmount = validateObject($orderData, 'total_amount', "");
        $totalAmount = addslashes($totalAmount);

        $paymentType = validateObject($orderData, 'paymentType', "");
        $paymentType = addslashes($paymentType);

        $cardId = validateObject($orderData, 'cardId', "");
        $cardId = addslashes($cardId);

        $cardInfo = array();
        $cardInfo = validateObject($orderData, 'cardInfo', "");

        $isValidCard = false;

        if (strlen($cardId) == 0)
        {
            //print_r($cardInfo);exit;
            $card_number = validateObject($cardInfo[0], 'card_number', "");
            $name_on_card = validateObject($cardInfo[0], 'name_on_card', "");
            $expiry_month = validateObject($cardInfo[0], 'expiry_month', "");
            $expiry_year = validateObject($cardInfo[0], 'expiry_year', "");
            $cvv_code = validateObject($cardInfo[0], 'cvv_code', "");
            $is_saved = validateObject($cardInfo[0], 'is_saved', "");

                $userdata['card'] = $card_number;
                $userdata['mnth'] = $expiry_month;
                $userdata['yr'] = $expiry_year;
                $userdata['cvc'] = $cvv_code;

                try
                {
                    $token = $this->gettoken($userdata);
                    $isValidCard = true;
                }
                catch (Exception $ex)
                {
                    $isValidCard = false;
                    $status = 2;
                    $data = array();
                    $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                    $data['message'] = $ex->getMessage();
                    return $data;
                }


            $select_card_query = "select * from " . TABLE_CARD . " where card_number = ? and user_id = ?";
            $select_card_stmt = $connection->prepare($select_card_query);
            $select_card_stmt->bind_param('si', $card_number, $userid);

            if ($select_card_stmt->execute())
            {
                $select_card_stmt->store_result();
                if ($select_card_stmt->num_rows > 0)
                {
                    //update card
                    $card = fetch_assoc_all_values($select_card_stmt);
                    $cardId = $card["id"];
                    $update_card_query = "update " . TABLE_CARD . " set is_saved = ? where id = ?";
                    $update_card_stmt = $connection->prepare($update_card_query);
                    $update_card_stmt->bind_param('ii', $is_saved, $cardId);

                    if (!$update_card_stmt->execute())
                    {
                        $status = 2;
                        $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                        $data['message'] = "Fail to save card data !!!";
                        return $data;
                    }
                }
                else
                {
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

                    if ($insert_card_stmt->execute())
                    {
                        $cardId = mysqli_insert_id($connection);
                    }
                    else
                    {
                        $status = 2;
                        $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                        $data['message'] = "Please try again";
                        return $data;
                    }
                }
            }
        }
        else
        {
                $select_card_query = "select * from " . TABLE_CARD . " where id = ?";
                $select_card_stmt = $connection->prepare($select_card_query);
                $select_card_stmt->bind_param('i', $cardId);
                if ($select_card_stmt->execute())
                {
                    $select_card_stmt->store_result();
                    if ($select_card_stmt->num_rows > 0)
                    {

                        while ($card = fetch_assoc_all_values($select_card_stmt))
                        {
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
                    else
                    {
                        $status = 2;
                        $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                        $data['message'] = "Card not found please try again...";
                        return $data;
                    }
                }
                else
                {
                    $status = 2;
                    $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                    $data['message'] = "Fail to fetch payment card data...";
                    return $data;
                }
            }


        //Add shipping Address
        if ($isValidCard)
        {
            $select_order_query = "select * from ". TABLE_ORDER_DETAILS . "  where id = ? and is_deleted = '0'";
            $select_order_stmt = $connection->prepare($select_order_query);
            $select_order_stmt->bind_param("i",$orderId);

            if ($select_order_stmt->execute())
            {
                $select_order_stmt->store_result();
                $order = fetch_assoc_all_values($select_order_stmt);



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
                    $payStatus = json_encode($charge['status']);
                    if($payStatus == '"succeeded"')
                    {
                        $isPaymentDone = true;
                    }
                    else
                    {
                        $isPaymentDone = false;

                        $status = 2;
                        $data = array();
                        $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                        $data['message'] = "Please try again payment failed...";
                        return $data;

                    }
                }
                catch (Exception $ex)
                {

                    $status = 2;
                    $data = array();
                    $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                    $data['message'] = $ex->getMessage();
                    return $data;
                }

                if ($isPaymentDone)
                {

                    $select_cart_item = "select selfie_id from ". TABLE_CART_ITEM ." where cart_id = ?";
                    $select_cart_item_stmt = $connection->prepare($select_cart_item);
                    $select_cart_item_stmt->bind_param("i",$order['cart_id']);
                    if ($select_cart_item_stmt->execute())
                    {
                        $select_cart_item_stmt->store_result();

                        while ($item = fetch_assoc_all_values($select_cart_item_stmt))
                        {
                            //update is delete flag
                            $update_cart_delete_query  = "update " . TABLE_ICP_IMAGE_TAG . " set is_delete = ?  where id = ?";
                            $update_cart_delete_stmt = $connection->prepare($update_cart_delete_query);
                            $isdelete = "0";
                            $update_cart_delete_stmt->bind_param("si",$isdelete,$item['selfie_id']);
                            if (!$update_cart_delete_stmt->execute())
                            {
                                $data['status'] = FAILED;
                                $data['message'] = "Some thing went wrong please try again...";
                                return $data;
                            }
                        }
                    }
                    else
                    {
                        $data['status'] = FAILED;
                        $data['message'] = "Some thing went wrong please try again...";
                        return $data;
                    }

                    //Update payment status
                    $update_cart_payment = "update " . TABLE_CART . " set is_payment_done = '1' ,payment_type = ?,card_id = ? where id = ?";
                    $update_cart_payment_stmt = $connection->prepare($update_cart_payment);

                    $update_cart_payment_stmt->bind_param("sis",$paymentType, $cardId,$order['cart_id']);
                    if ($update_cart_payment_stmt->execute())
                    {


                            //update payment done for cart
                            $largeImages = array();
                            $select_cart_items = "select * from ". TABLE_CART_ITEM ." where cart_id = ?";
//                            echo $order['cart_id'];
//                            echo $select_cart_items;
//                            exit;

                            $select_cart_items_stmt = $connection->prepare($select_cart_items);
                            $select_cart_items_stmt->bind_param("i",$order['cart_id']);
                            if ($select_cart_items_stmt->execute())
                            {
                                $select_cart_items_stmt->store_result();
                                while ($cartItem = fetch_assoc_all_values($select_cart_items_stmt))
                                {

                                    $selfieid = $cartItem['selfie_id'];
                                    $is_small_photo = $cartItem['is_small_photo'];
                                    $is_large_photo = $cartItem['is_large_photo'];
                                    $is_frame = $cartItem['is_frame'];
                                    $is_purchased = 1;

                                    if ($is_large_photo == 1) {
                                        $largeImages[] = $selfieid;
                                    }


                                    $update_purchase_flag = "update " . TABLE_ICP_IMAGE_TAG . " set is_small_purchase = ? , is_large_purchase = ? ,
                        is_printed_purchase = ? , is_purchased = ? where id = ?
                        and user_id = ?";

                                    $update_purchase_flag_stmt = $connection->prepare($update_purchase_flag);
                                    $update_purchase_flag_stmt->bind_param("iiiiii", $is_small_photo, $is_large_photo, $is_frame, $is_purchased, $selfieid, $userid);

                                    if (!$update_purchase_flag_stmt->execute())
                                    {
                                        $status = 1;
                                        $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                                        $data['message'] = "Successfully update payment status !!!";
                                        $data['extraMessage'] = "Fail to update is_purchage for photo !!!" . $update_purchase_flag_stmt->error;
                                        return $data;
                                    }
                                }

                                $img_path = array();
                                foreach ($largeImages as $image)
                                {
                                    $mail_images_query = "select icp_image.image,user.username ,user.email from ". TABLE_ICP_IMAGE_TAG ." as tag_image
                        left join (select * from ". TABLE_ICP_IMAGE .") as icp_image on tag_image.icp_image_id = icp_image.id
                        left join (select id,username,email from ". TABLE_USER .") as user on user.id = tag_image.user_id
                        where tag_image.id = ? and tag_image.user_id = ?";


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

                                        }
                                    }
                                }

                                if(count($largeImages) > 0)
                                {
                                    $body = "Hi ". $userName.", <br> <br> Thank you for your order, your  &#39;large&#39; Photo is attached.
                                    <br><br> Regards <br> The Facetag Team.<br><br> Order number:".$orderId;

                                    $subject = "Facetag Photo";
                                    include_once 'SendAttachmentMail.php';
                                    $objEmail = new SendAttachmentMail();
                                    $objEmail->sendEmail($body,$email,$subject,$img_path);
                                }


                                $status = 1;
                                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                                $data['message'] = "Successfully update payment status !!!";
                                return $data;
                            }
                            else
                            {
                                $status = 1;
                                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                                $data['message'] = "Successfully update payment status !!!";
                                $data['extraMessage'] = "Fail to get cart item !!!" . $select_cart_items_stmt->error;
                                return $data;
                            }
                    }
                    else
                    {
                        $status = 2;
                        $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                        $data['message'] = "Fail to update payment status !!!" . $update_cart_payment_stmt->error;
                        return $data;
                    }

                }
                else
                {
                    $status = 2;
                    $data = array();
                    $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                    $data['message'] = "Payment unsuccesfull...";
                    return $data;
                }
            }
            else
            {
                $status = 2;
                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                $data['message'] = "Fail to fetch order details !!!" . $select_order_stmt->error;
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

    }


    public function makePaymentWithPayPal($orderData)
    {

        $connection = $GLOBALS['con'];
        $status = 2;
        $posts = array();
        $errorMsg = "Service responce data";

        $userid = validateObject($orderData, 'userId', "");
        $userid = addslashes($userid);


        $userName = validateObject($orderData, 'username', "");
        $userName = addslashes($userName);

        $email = validateObject($orderData, 'email', "");
        $email = addslashes($email);


        $orderId = validateObject($orderData, 'orderId', "");
        $orderId = addslashes($orderId);

        $paymentStatus = validateObject($orderData, 'paymentStatus', "");
        $paymentStatus = addslashes($paymentStatus);

        $paymentType = validateObject($orderData, 'paymentType', "");
        $paymentType = addslashes($paymentType);

        $select_order_query = "select * from ". TABLE_ORDER_DETAILS . "  where id = ? and is_deleted = '0'";
        $select_order_stmt = $connection->prepare($select_order_query);
        $select_order_stmt->bind_param("i",$orderId);
        if ($select_order_stmt->execute())
        {
            $select_order_stmt->store_result();
            $order = fetch_assoc_all_values($select_order_stmt);

            //Select cart item
            $select_cart_item = "select selfie_id from ". TABLE_CART_ITEM ." where cart_id = ?";
            $select_cart_item_stmt = $connection->prepare($select_cart_item);
            $select_cart_item_stmt->bind_param("i",$order['cart_id']);
            if ($select_cart_item_stmt->execute())
            {
                $select_cart_item_stmt->store_result();

                while ($item = fetch_assoc_all_values($select_cart_item_stmt))
                {
                    //update is delete flag
                    $update_cart_delete_query  = "update " . TABLE_ICP_IMAGE_TAG . " set is_delete = ?  where id = ?";
                    $update_cart_delete_stmt = $connection->prepare($update_cart_delete_query);
                    $isdelete = "0";
                    $update_cart_delete_stmt->bind_param("si",$isdelete,$item['selfie_id']);
                    if (!$update_cart_delete_stmt->execute())
                    {
                        $data['status'] = FAILED;
                        $data['message'] = "Some thing went wrong please try again...";
                        return $data;
                    }
                }
            }
            else
            {
                $data['status'] = FAILED;
                $data['message'] = "Some thing went wrong please try again...";
                return $data;
            }

            //Update payment status
            $update_cart_payment = "update " . TABLE_CART . " set is_payment_done = ? ,payment_type = ? where id = ?";
            $update_cart_payment_stmt = $connection->prepare($update_cart_payment);
            $update_cart_payment_stmt->bind_param("ssi", $paymentStatus,$paymentType, $order['cart_id']);
            if ($update_cart_payment_stmt->execute())
            {
                    $largeImages = array();
                    $select_cart_items = "select * from ". TABLE_CART_ITEM ." where cart_id = ?";
                    $select_cart_items_stmt = $connection->prepare($select_cart_items);
                    $select_cart_items_stmt->bind_param("i",$order['cart_id']);
                    if ($select_cart_items_stmt->execute()) {
                        $select_cart_items_stmt->store_result();
                        while ($cartItem = fetch_assoc_all_values($select_cart_items_stmt))
                        {

                            $selfieid = $cartItem['selfie_id'];
                            $is_small_photo = $cartItem['is_small_photo'];
                            $is_large_photo = $cartItem['is_large_photo'];
                            $is_frame = $cartItem['is_frame'];
                            $is_purchased = 1;

                            if ($is_large_photo == 1) {
                                $largeImages[] = $selfieid;
                            }

                            $update_purchase_flag = "update " . TABLE_ICP_IMAGE_TAG . " set is_small_purchase = ? , is_large_purchase = ? ,
                        is_printed_purchase = ? , is_purchased = ? where id = ?
                        and user_id = ?";

                            $update_purchase_flag_stmt = $connection->prepare($update_purchase_flag);
                            $update_purchase_flag_stmt->bind_param("iiiiii", $is_small_photo, $is_large_photo, $is_frame, $is_purchased, $selfieid, $userid);

                            if (!$update_purchase_flag_stmt->execute()) {
                                $status = 2;
                                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                                $data['message'] = "Fail to update is_purchage for photo !!!" . $update_purchase_flag_stmt->error;
                                return $data;
                            }
                        }

                        $img_path = array();

                        foreach ($largeImages as $image)
                        {
                            $mail_images_query = "select icp_image.image,user.username ,user.email from ". TABLE_ICP_IMAGE_TAG ." as tag_image
                        left join (select * from ". TABLE_ICP_IMAGE .") as icp_image on tag_image.icp_image_id = icp_image.id
                        left join (select id,username,email from ". TABLE_USER .") as user on user.id = tag_image.user_id
                        where tag_image.id = ? and tag_image.user_id = ?";
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
                                }
                            }
                        }

                        if(count($largeImages) > 0)
                        {
                            $body = "Hi ". $userName.", <br> <br> Thank you for your order, your  &#39;large&#39; Photo is attached.
                                    <br><br> Regards <br> The Facetag Team.<br><br> Order number:".$orderId;

                            $subject = "Facetag Photo";
                            include_once 'SendAttachmentMail.php';
                            $objEmail = new SendAttachmentMail();
                            $objEmail->sendEmail($body,$email,$subject,$img_path);
                        }

                    }
                    else
                    {
                        $status = 2;
                        $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                        $data['message'] = "Fail to get cart item !!!" . $select_cart_items_stmt->error;
                        return $data;
                    }

                $status = 1;
                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                $data['message'] = "Successfully update payment status !!!";
                return $data;


            }
            else
            {
                $status = 2;
                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                $data['message'] = "Fail to update payment status !!!" . $update_cart_payment_stmt->error;
                return $data;
            }

        }
        else
        {
            $status = 2;
            $data['status'] = ($status > 1) ? FAILED : SUCCESS;
            $data['message'] = "Fail to fetch order details !!!" . $select_order_stmt->error;
            return $data;
        }
    }


    public function cancelOrder($orderData)
    {
        $connection = $GLOBALS['con'];
        $status = 2;
        $posts = array();
        $errorMsg = "Service responce data";

        $orderId = validateObject($orderData, 'orderId', "");
        $orderId = addslashes($orderId);

        $nonUserPhoto = validateObject($orderData, 'nonUserPhoto', "");
        $nonUserPhoto = addslashes($nonUserPhoto);

        $userId = validateObject($orderData, 'userId', "");
        $userId = addslashes($userId);

        $arrIcpImgID = explode(',', $nonUserPhoto);

        foreach ($arrIcpImgID as $icpImgID)
        {
            $update_image_tag_query = "update " . TABLE_ICP_IMAGE_TAG . " set is_delete = '1' where icp_image_id = ? and user_id = ? and is_purchased = ?";
            $update_image_tag_stmt = $connection->prepare($update_image_tag_query);
            $isPurchase = "0";
            $update_image_tag_stmt->bind_param("iis", $icpImgID,$userId,$isPurchase);

            if (!$update_image_tag_stmt->execute())
            {
                $data['status'] = FAILED;
                $data['message'] = "Some thing went wrong please try again...";
                return $data;
            }
        }


        $select_cart_id_query = "select cart_id from ". TABLE_ORDER_DETAILS ." where id = ?";


        $select_cart_id_stmt = $connection->prepare($select_cart_id_query);
        $select_cart_id_stmt->bind_param("i",$orderId);
        if ($select_cart_id_stmt->execute())
        {
            $select_cart_id_stmt->store_result();
            $order = fetch_assoc_all_values($select_cart_id_stmt);

            $delete_cart_query = "delete from ". TABLE_CART ." where id = ? ";
            $delete_cart_stmt = $connection->prepare($delete_cart_query);
            $delete_cart_stmt->bind_param("i",$order['cart_id']);
            if ($delete_cart_stmt->execute())
            {
                $update_order_status = "update " . TABLE_ORDER_DETAILS . " set is_deleted = ? where id = ?";
                $update_order_status_stmt = $connection->prepare($update_order_status);
                $isdelete = "1";
                $update_order_status_stmt->bind_param("si", $isdelete,$orderId);

                if ($update_order_status_stmt->execute())
                {
                    $status = 1;
                    $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                    $data['message'] = "Order cancel successfully...";
                    return $data;
                }
                else
                {
                    $status = 2;
                    $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                    $data['message'] = "Fail to save cart data !!!" . $update_order_status_stmt->error;
                    return $data;
                }

            }
            else
            {
                $data['status'] = FAILED;
                $data['message'] = "Some thing went wrong please try again...";
                return $data;
            }
        }
        else
        {
            $data['status'] = FAILED;
            $data['message'] = "Some thing went wrong please try again...";
            return $data;
        }

    }


    //Hipping Details Module
    public function saveOrderData($orderData)
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


        $transactionid = validateObject($orderData, 'transactionid', "");
        $transactionid = addslashes($transactionid);



        //Add cart data
        $insertFields = "user_id,
                             total_amount,
                             transactionId,
                             modified";

        $valuesFields = "?,?,?,?";
        $insert_query = "" . "Insert into " . TABLE_CART . " (" . $insertFields . ") values (" . $valuesFields . ")";
        $insert_cart_stmt = $connection->prepare($insert_query);
        $modifieddate = date("Y-m-d H:i:s");
        $insert_cart_stmt->bind_param('isss', $userid, $totalAmount,$transactionid, $modifieddate);

        if ($insert_cart_stmt->execute()) {
            $cartId = mysqli_insert_id($connection);
        } else {
            $status = 2;
            $data['status'] = ($status > 1) ? FAILED : SUCCESS;
            $data['message'] = "Fail to save cart data !!!" . $insert_cart_stmt->error;
            return $data;
        }


        $orderId = time().mt_rand();

        //Save Cart Item
        foreach ($cartItem as $item) {
            $businessid = validateObject($item, 'businessid', "");
            $selfieid = validateObject($item, 'selfieid', "");
            $is_small_photo = validateObject($item, 'is_small_photo', "");
            $is_large_photo = validateObject($item, 'is_large_photo', "");
            $is_frame = validateObject($item, 'is_frame', "");

            $smallprice = validateObject($item, 'smallprice', "");
            $largprice = validateObject($item, 'largprice', "");
            $printedprice = validateObject($item, 'printedprice', "");

            $isuserphoto = validateObject($item, 'isuserphoto', "");
            $icpimgid = validateObject($item, 'icpimgid', "");



            if($isuserphoto == "0")
            {
                //Insert to get selfie ID
                $insertSelfie = "icp_image_id,
                            is_user_verified,
                             user_id,created,is_delete";

                $valuesFields = "?,?,?,?,?";
                $created = date("Y-m-d H:i:s");

                $insert_selfie_query = "" . "Insert into " . TABLE_ICP_IMAGE_TAG . " (" . $insertSelfie . ") values (" . $valuesFields . ")";
                $insert_selfie_stmt = $connection->prepare($insert_selfie_query);
                $isverified  = "1";
                $isdeleted  = "1";
                $insert_selfie_stmt->bind_param('isiss', $icpimgid,$isverified,$userid,$created,$isdeleted);

                if ($insert_selfie_stmt->execute())
                {
                    $selfieid = mysqli_insert_id($connection);
                }
                else
                {
                    $status = 2;
                    $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                    $data['message'] = "Some thing went wrong please try again...";
                    return $data;
                }
            }

            $insertFields = "cart_id,
                             business_id,
                             selfie_id,
                             is_small_photo,
                             is_large_photo,
                             is_frame,
                             small_price,
                             larg_price,
                             printed_price,
                             modified";

            $valuesFields = "?,?,?,?,?,?,?,?,?,?";
            $insert_query = "" . "Insert into " . TABLE_CART_ITEM . " (" . $insertFields . ") values (" . $valuesFields . ")";
            $insert_cart_item_stmt = $connection->prepare($insert_query);
            $modifieddate = date("Y-m-d H:i:s");
            $insert_cart_item_stmt->bind_param('iiiiiissss', $cartId,$businessid, $selfieid, $is_small_photo, $is_large_photo, $is_frame,
                $smallprice,$largprice,$printedprice,$modifieddate);

            if (!$insert_cart_item_stmt->execute()) {
                $status = 2;
                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                $data['message'] = "Fail to save cart item data !!!" . $insert_cart_item_stmt->error;
                return $data;
            }

            //Save Shipping details
            $saveShippingDetails =  $this->saveShippingDetails($item,$orderId,$cartId);
            if($saveShippingDetails['status'] == "0")
            {
                    $data['status'] = FAILED;
                    $data['message'] = $saveShippingDetails['message'];
                    return $data;
            }

//            $shipping_type = validateObject($item, 'shipping_type', "");
//            $shipping_hotel_id = validateObject($item, 'shipping_hotel_id', "");
//            $collection_point_id = validateObject($item, 'collection_point_id', "");
//            $domestic_address_id = validateObject($item, 'domestic_address_id', "");
//            $international_address_id = validateObject($item, 'international_address_id', "");
//            $shipping_address = validateObject($item, 'shipping_address', "");
//
//
//
//            if($shipping_type == "0")
//            {
//                $insertShippingData = "id,cart_id,
//                             selfieid,
//                             is_printed,
//                             shipping_type
//                             ";
//
//                $valuesFields = "?,?,?,?,?";
//
//                $insert_shipping_query = "" . "Insert into " . TABLE_ORDER_DETAILS . " (" . $insertShippingData . ") values (" . $valuesFields . ")";
//
//
//                $insert_shiping_stmt = $connection->prepare($insert_shipping_query);
//
//                $insert_shiping_stmt->bind_param('siiss', $orderId,$cartId,$selfieid, $is_frame, $shipping_type);
//
//                if (!$insert_shiping_stmt->execute()) {
//                    $status = 2;
//                    $data['status'] = ($status > 1) ? FAILED : SUCCESS;
//                    $data['message'] = "Fail to save shipping data !!!" . $insert_cart_item_stmt->error;
//                    return $data;
//                }
//            }
//                elseif($shipping_type == "1")
//                {
//                    $insertShippingData = "id,cart_id,
//                             selfieid,
//                             is_printed,
//                             shipping_type,
//                             shipping_hotel_id,
//                             shipping_address";
//
//                    $valuesFields = "?,?,?,?,?,?,?";
//
//                    $insert_shipping_query = "" . "Insert into " . TABLE_ORDER_DETAILS . " (" . $insertShippingData . ") values (" . $valuesFields . ")";
//
//
//                    $insert_shiping_stmt = $connection->prepare($insert_shipping_query);
//
//                    $insert_shiping_stmt->bind_param('siissis', $orderId,$cartId,$selfieid, $is_frame, $shipping_type, $shipping_hotel_id, $shipping_address);
//
//                    if (!$insert_shiping_stmt->execute()) {
//                        $status = 2;
//                        $data['status'] = ($status > 1) ? FAILED : SUCCESS;
//                        $data['message'] = "Fail to save shipping data !!!" . $insert_cart_item_stmt->error;
//                        return $data;
//                    }
//                }
//                elseif($shipping_type == "2")
//                {
//                    $insertShippingData = "id,cart_id,
//                             selfieid,
//                             is_printed,
//                             shipping_type,
//                             collection_point_id,
//                             shipping_address";
//
//                    $valuesFields = "?,?,?,?,?,?,?";
//
//                    $insert_shipping_query = "" . "Insert into " . TABLE_ORDER_DETAILS . " (" . $insertShippingData . ") values (" . $valuesFields . ")";
//                    $insert_shiping_stmt = $connection->prepare($insert_shipping_query);
//
//                    $insert_shiping_stmt->bind_param('siissis', $orderId,$cartId,$selfieid, $is_frame, $shipping_type, $collection_point_id, $shipping_address);
//
//                    if (!$insert_shiping_stmt->execute()) {
//                        $status = 2;
//                        $data['status'] = ($status > 1) ? FAILED : SUCCESS;
//                        $data['message'] = "Fail to save shipping data !!!" . $insert_cart_item_stmt->error;
//                        return $data;
//                    }
//                }
//                elseif($shipping_type == "3")
//                {
//                    $insertShippingData = "id,cart_id,
//                             selfieid,
//                             is_printed,
//                             shipping_type,
//                             domestic_address_id,
//                             shipping_address";
//
//                    $valuesFields = "?,?,?,?,?,?,?";
//
//                    $insert_shipping_query = "" . "Insert into " . TABLE_ORDER_DETAILS . " (" . $insertShippingData . ") values (" . $valuesFields . ")";
//                    $insert_shiping_stmt = $connection->prepare($insert_shipping_query);
//
//                    $insert_shiping_stmt->bind_param('siissis', $orderId,$cartId,$selfieid, $is_frame, $shipping_type, $domestic_address_id, $shipping_address);
//
//                    if (!$insert_shiping_stmt->execute()) {
//                        $status = 2;
//                        $data['status'] = ($status > 1) ? FAILED : SUCCESS;
//                        $data['message'] = "Fail to save shipping data !!!" . $insert_cart_item_stmt->error;
//                        return $data;
//                    }
//                }
//                elseif($shipping_type == "4")
//                {
//                    $insertShippingData = "id,cart_id,
//                             selfieid,
//                             is_printed,
//                             shipping_type,
//                             international_address_id,
//                             shipping_address";
//
//                    $valuesFields = "?,?,?,?,?,?,?";
//
//                    $insert_shipping_query = "" . "Insert into " . TABLE_ORDER_DETAILS . " (" . $insertShippingData . ") values (" . $valuesFields . ")";
//                    $insert_shiping_stmt = $connection->prepare($insert_shipping_query);
//
//                    $insert_shiping_stmt->bind_param('siissis',$orderId,$cartId,$selfieid, $is_frame, $shipping_type, $international_address_id, $shipping_address);
//
//                    if (!$insert_shiping_stmt->execute()) {
//                        $status = 2;
//                        $data['status'] = ($status > 1) ? FAILED : SUCCESS;
//                        $data['message'] = "Fail to save shipping data !!!" . $insert_cart_item_stmt->error;
//                        return $data;
//                    }
//                }




        }

        $status = 1;
        $data['status'] = ($status > 1) ? FAILED : SUCCESS;
        $data['message'] = "Order save successfully !!!";
        $data['transactionid'] = strval($transactionid);
        $data['orderID'] = strval($orderId);
        return $data;
    }


    public function saveShippingDetails($item,$orderId,$cartId)
    {
        $connection = $GLOBALS['con'];

        $businessid = validateObject($item, 'businessid', "");
        $selfieid = validateObject($item, 'selfieid', "");
        $is_small_photo = validateObject($item, 'is_small_photo', "");
        $is_large_photo = validateObject($item, 'is_large_photo', "");
        $is_frame = validateObject($item, 'is_frame', "");
        $smallprice = validateObject($item, 'smallprice', "");
        $largprice = validateObject($item, 'largprice', "");
        $printedprice = validateObject($item, 'printedprice', "");
        $isuserphoto = validateObject($item, 'isuserphoto', "");
        $icpimgid = validateObject($item, 'icpimgid', "");
        $shipping_type = validateObject($item, 'shipping_type', "");
        $shipping_hotel_id = validateObject($item, 'shipping_hotel_id', "");
        $collection_point_id = validateObject($item, 'collection_point_id', "");
        $domestic_address_id = validateObject($item, 'domestic_address_id', "");
        $international_address_id = validateObject($item, 'international_address_id', "");
        $shipping_address = validateObject($item, 'shipping_address', "");



        if($shipping_type == "0")
        {
            $insertShippingData = "id,cart_id,
                             selfieid,
                             is_printed,
                             shipping_type
                             ";

            $valuesFields = "?,?,?,?,?";

            $insert_shipping_query = "" . "Insert into " . TABLE_ORDER_DETAILS . " (" . $insertShippingData . ") values (" . $valuesFields . ")";


            $insert_shiping_stmt = $connection->prepare($insert_shipping_query);

            $insert_shiping_stmt->bind_param('siiss', $orderId,$cartId,$selfieid, $is_frame, $shipping_type);

            if (!$insert_shiping_stmt->execute()) {
                $data['status'] = "0";
                $data['message'] = "Fail to save shipping data !!!" . $insert_shiping_stmt->error;
                return $data;
            }
            else
            {
                $data['status'] = "1";
                return $data;
            }
        }
        elseif($shipping_type == "1")
        {
            $insertShippingData = "id,cart_id,
                             selfieid,
                             is_printed,
                             shipping_type,
                             shipping_hotel_id,
                             shipping_address";

            $valuesFields = "?,?,?,?,?,?,?";

            $insert_shipping_query = "" . "Insert into " . TABLE_ORDER_DETAILS . " (" . $insertShippingData . ") values (" . $valuesFields . ")";


            $insert_shiping_stmt = $connection->prepare($insert_shipping_query);

            $insert_shiping_stmt->bind_param('siissis', $orderId,$cartId,$selfieid, $is_frame, $shipping_type, $shipping_hotel_id, $shipping_address);

            if (!$insert_shiping_stmt->execute()) {
                $data['status'] = "0";
                $data['message'] = "Fail to save shipping data !!!" . $insert_shiping_stmt->error;
                return $data;
            }
            else
            {
                $data['status'] = "1";
                return $data;
            }
        }
        elseif($shipping_type == "2")
        {
            $insertShippingData = "id,cart_id,
                             selfieid,
                             is_printed,
                             shipping_type,
                             collection_point_id,
                             shipping_address";

            $valuesFields = "?,?,?,?,?,?,?";

            $insert_shipping_query = "" . "Insert into " . TABLE_ORDER_DETAILS . " (" . $insertShippingData . ") values (" . $valuesFields . ")";
            $insert_shiping_stmt = $connection->prepare($insert_shipping_query);

            $insert_shiping_stmt->bind_param('siissis', $orderId,$cartId,$selfieid, $is_frame, $shipping_type, $collection_point_id, $shipping_address);

            if (!$insert_shiping_stmt->execute()) {
                $data['status'] = "0";
                $data['message'] = "Fail to save shipping data !!!" . $insert_shiping_stmt->error;
                return $data;
            }
            else
            {
                $data['status'] = "1";
                return $data;
            }
        }
        elseif($shipping_type == "3")
        {
            $insertShippingData = "id,cart_id,
                             selfieid,
                             is_printed,
                             shipping_type,
                             domestic_address_id,
                             shipping_address";

            $valuesFields = "?,?,?,?,?,?,?";

            $insert_shipping_query = "" . "Insert into " . TABLE_ORDER_DETAILS . " (" . $insertShippingData . ") values (" . $valuesFields . ")";
            $insert_shiping_stmt = $connection->prepare($insert_shipping_query);

            $insert_shiping_stmt->bind_param('siissis', $orderId,$cartId,$selfieid, $is_frame, $shipping_type, $domestic_address_id, $shipping_address);

            if (!$insert_shiping_stmt->execute()) {
                $data['status'] = "0";
                $data['message'] = "Fail to save shipping data !!!" . $insert_shiping_stmt->error;
                return $data;
            }
            else
            {
                $data['status'] = "1";
                return $data;
            }
        }
        elseif($shipping_type == "4")
        {
            $insertShippingData = "id,cart_id,
                             selfieid,
                             is_printed,
                             shipping_type,
                             international_address_id,
                             shipping_address";

            $valuesFields = "?,?,?,?,?,?,?";

            $insert_shipping_query = "" . "Insert into " . TABLE_ORDER_DETAILS . " (" . $insertShippingData . ") values (" . $valuesFields . ")";
            $insert_shiping_stmt = $connection->prepare($insert_shipping_query);

            $insert_shiping_stmt->bind_param('siissis',$orderId,$cartId,$selfieid, $is_frame, $shipping_type, $international_address_id, $shipping_address);

            if (!$insert_shiping_stmt->execute()) {
                $data['status'] = "0";
                $data['message'] = "Fail to save shipping data !!!" . $insert_shiping_stmt->error;
                return $data;
            }
            else
            {
                $data['status'] = "1";
                return $data;
            }
        }
    }

    public function saveOrder($orderData)
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

        $hotelid = validateObject($orderData, 'hotelid', "");
        $hotelid = addslashes($hotelid);

        $transactionid = validateObject($orderData, 'transactionid', "");
        $transactionid = addslashes($transactionid);



        //Add cart data
        $insertFields = "user_id,
                             total_amount,
                             transactionId,
                             modified";

        $valuesFields = "?,?,?,?";
        $insert_query = "" . "Insert into " . TABLE_CART . " (" . $insertFields . ") values (" . $valuesFields . ")";
        $insert_cart_stmt = $connection->prepare($insert_query);
        $modifieddate = date("Y-m-d H:i:s");
        $insert_cart_stmt->bind_param('isss', $userid, $totalAmount,$transactionid, $modifieddate);

        if ($insert_cart_stmt->execute()) {
            $cartId = mysqli_insert_id($connection);
        } else {
            $status = 2;
            $data['status'] = ($status > 1) ? FAILED : SUCCESS;
            $data['message'] = "Fail to save cart data !!!" . $insert_cart_stmt->error;
            return $data;
        }

        //Attach address with cart
        $insertFields =     "cart_id,
                             shipping_hotel_id,
                             modified";

        $valuesFields = "?,?,?";
        $insert_query = "" . "Insert into " . TABLE_ORDER_DETAILS . " (" . $insertFields . ") values (" . $valuesFields . ")";
        $insert_order_details_stmt = $connection->prepare($insert_query);
        $modifieddate = date("Y-m-d H:i:s");
        $insert_order_details_stmt->bind_param('iis', $cartId, $hotelid, $modifieddate);


        if ($insert_order_details_stmt->execute())
        {
            $orderId = mysqli_insert_id($connection);
        }
        else
        {
            $status = 2;
            $data['status'] = ($status > 1) ? FAILED : SUCCESS;
            $data['message'] = "Fail to attch address with order !!!" . $insert_order_details_stmt->error;
            return $data;
        }




        //Save Cart Item
        foreach ($cartItem as $item) {
            $businessid = validateObject($item, 'businessid', "");
            $selfieid = validateObject($item, 'selfieid', "");
            $is_small_photo = validateObject($item, 'is_small_photo', "");
            $is_large_photo = validateObject($item, 'is_large_photo', "");
            $is_frame = validateObject($item, 'is_frame', "");

            $smallprice = validateObject($item, 'smallprice', "");
            $largprice = validateObject($item, 'largprice', "");
            $printedprice = validateObject($item, 'printedprice', "");

            $isuserphoto = validateObject($item, 'isuserphoto', "");
            $icpimgid = validateObject($item, 'icpimgid', "");

            if($isuserphoto == "0")
            {
                //Insert to get selfie ID
                $insertSelfie = "icp_image_id,
                            is_user_verified,
                             user_id,created,is_delete";

                $valuesFields = "?,?,?,?,?";
                $created = date("Y-m-d H:i:s");

                $insert_selfie_query = "" . "Insert into " . TABLE_ICP_IMAGE_TAG . " (" . $insertSelfie . ") values (" . $valuesFields . ")";
                $insert_selfie_stmt = $connection->prepare($insert_selfie_query);
                $isverified  = "1";
                $isdeleted  = "1";
                $insert_selfie_stmt->bind_param('isiss', $icpimgid,$isverified,$userid,$created,$isdeleted);

                if ($insert_selfie_stmt->execute())
                {
                    $selfieid = mysqli_insert_id($connection);
                }
                else
                {
                    $status = 2;
                    $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                    $data['message'] = "Some thing went wrong please try again...";
                    return $data;
                }
            }

            $insertFields = "cart_id,
                             business_id,
                             selfie_id,
                             is_small_photo,
                             is_large_photo,
                             is_frame,
                             small_price,
                             larg_price,
                             printed_price,
                             modified";

            $valuesFields = "?,?,?,?,?,?,?,?,?,?";
            $insert_query = "" . "Insert into " . TABLE_CART_ITEM . " (" . $insertFields . ") values (" . $valuesFields . ")";
            $insert_cart_item_stmt = $connection->prepare($insert_query);
            $modifieddate = date("Y-m-d H:i:s");
            $insert_cart_item_stmt->bind_param('iiiiiissss', $cartId,$businessid, $selfieid, $is_small_photo, $is_large_photo, $is_frame,
                $smallprice,$largprice,$printedprice,$modifieddate);

            if (!$insert_cart_item_stmt->execute()) {
                $status = 2;
                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                $data['message'] = "Fail to save cart item data !!!" . $insert_cart_item_stmt->error;
                return $data;
            }
        }

        $status = 1;
        $data['status'] = ($status > 1) ? FAILED : SUCCESS;
        $data['message'] = "Order save successfully !!!";
        $data['transactionid'] = strval($transactionid);
        $data['orderID'] = strval($orderId);
        return $data;
    }


    public function updatePaymentStatus($orderData)
    {

        $connection = $GLOBALS['con'];
        $status = 2;
        $posts = array();
        $order = array();
        $errorMsg = "Service responce data";

        $userid = validateObject($orderData, 'userId', "");
        $userid = addslashes($userid);

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
        $select_cart_query = "select  * from " . TABLE_CART . " where user_id = ? and is_payment_done = ? order by created desc";
        //echo $select_cart_query;exit;
        $select_cart_stmt = $connection->prepare($select_cart_query);
        $payment_done = "1";
        $select_cart_stmt->bind_param("is", $userid,$payment_done);

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
                                          cartitem.small_price as low_resolution_price,
                                          cartitem.larg_price as high_resolution_price,
                                          cartitem.printed_price as printed_souvenir_price,
                                          icpimagetag.*,
                                          icpimage.image,
                                          icps.*,
                                          businesses.*
                                          from " . TABLE_CART_ITEM . " as cartitem
                                          left join (select id,icp_image_id,is_small_purchase,is_large_purchase,is_printed_purchase from " . TABLE_ICP_IMAGE_TAG . ")
                                           as icpimagetag on cartitem.selfie_id = icpimagetag.id
                                          left join (select id,icp_id,image from " . TABLE_ICP_IMAGE . ") as icpimage on icpimage.id = icpimagetag.icp_image_id
                                          left join (select id as icpid,icp_logo,business_id
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

        $email = validateObject($orderData, 'email', "");
        $email = addslashes($email);

        $username = validateObject($orderData, 'username', "");
        $username = addslashes($username);

        $totalAmount = validateObject($orderData, 'total_amount', "");
        $totalAmount = addslashes($totalAmount);

        $cartItem = array();
        $cartItem = validateObject($orderData, 'cartItem', "");

        $hotelid = validateObject($orderData, 'hotelid', "");
        $hotelid = addslashes($hotelid);

        $transactionid = validateObject($orderData, 'transactionid', "");
        $transactionid = addslashes($transactionid);

            //Place Order Details

            //Add cart data
            $insertFields = "user_id,
                             total_amount,
                             payment_type,
                             card_id,
                             transactionId,
                             modified";

            $valuesFields = "?,?,?,?,?,?";
            $insert_query = "" . "Insert into " . TABLE_CART . " (" . $insertFields . ") values (" . $valuesFields . ")";


            $insert_cart_stmt = $connection->prepare($insert_query);
            $modifieddate = date("Y-m-d H:i:s");
            $cardId = "";
            $paymentType = "0";
            $insert_cart_stmt->bind_param('iisiss', $userid, $totalAmount, $paymentType, $cardId,$transactionid, $modifieddate);

            if ($insert_cart_stmt->execute()) {
                $cartId = mysqli_insert_id($connection);
            } else {
                $status = 2;
                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                $data['message'] = "Fail to save cart data !!!" . $insert_cart_stmt->error;
                return $data;
            }


            $orderId = time().mt_rand();

            //Save cart item
            foreach ($cartItem as $item) {
                $selfieid = validateObject($item, 'selfieid', "");
                $businessid = validateObject($item, 'businessid', "");
                $is_small_photo = validateObject($item, 'is_small_photo', "");
                $is_large_photo = validateObject($item, 'is_large_photo', "");
                $is_frame = validateObject($item, 'is_frame', "");

                $smallprice = "0.00";
                $largprice = "0.00";
                $printedprice = "0.00";

                $smallprice = validateObject($item, 'smallprice', "");
                $largprice = validateObject($item, 'largprice', "");
                $printedprice = validateObject($item, 'printedprice', "");


                $isuserphoto = validateObject($item, 'isuserphoto', "");
                $icpimgid = validateObject($item, 'icpimgid', "");

                if($isuserphoto == "0")
                {

                    $insertSelfie = "icp_image_id,is_user_verified,is_currentuser,is_purchased,user_id,created";
                    $valuesFields = "?,?,?,?,?,?";
                    $created = date("Y-m-d H:i:s");
                    $insert_selfie_query = "" . "Insert into " . TABLE_ICP_IMAGE_TAG . " (" . $insertSelfie . ") values (" . $valuesFields . ")";
                    $insert_selfie_stmt = $connection->prepare($insert_selfie_query);
                    $isVerified = "1";

                    $insert_selfie_stmt->bind_param('isssis', $icpimgid,$isVerified,$isVerified,$isVerified,$userid,$created);
                    if ($insert_selfie_stmt->execute())
                    {
                        $selfieid = mysqli_insert_id($connection);
                    }
                    else
                    {
                        $data['status'] = FAILED;
                        $data['message'] = "Some thing went wrong please try again...";
                        return $data;
                    }

                }

                $insertFields = "cart_id,
                             business_id,
                             selfie_id,
                             is_small_photo,
                             is_large_photo,
                             is_frame,
                             small_price,
                             larg_price,
                             printed_price,
                             modified";

                $valuesFields = "?,?,?,?,?,?,?,?,?,?";
                $insert_query = "" . "Insert into " . TABLE_CART_ITEM . " (" . $insertFields . ") values (" . $valuesFields . ")";
                $insert_cart_item_stmt = $connection->prepare($insert_query);
                $modifieddate = date("Y-m-d H:i:s");
                $insert_cart_item_stmt->bind_param('iiiiiissss', $cartId,$businessid, $selfieid, $is_small_photo, $is_large_photo, $is_frame,
                    $smallprice,$largprice,$printedprice,$modifieddate);

                if (!$insert_cart_item_stmt->execute()) {
                    $status = 2;
                    $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                    $data['message'] = "Fail to save cart item data !!!" . $insert_cart_item_stmt->error;
                    return $data;
                }

                //Save Shipping details
                $saveShippingDetails =  $this->saveShippingDetails($item,$orderId,$cartId);
                if($saveShippingDetails['status'] == "0")
                {
                    $data['status'] = FAILED;
                    $data['message'] = $saveShippingDetails['message'];
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

                } else {
                    $status = 2;
                    $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                    $data['message'] = "Fail to update payment flag for cart !!!" . $insert_cart_item_stmt->error;
                    return $data;
                }
            }



        $img_path = array();
        foreach ($largeImages as $image)
        {
            $mail_images_query = "select icp_image.image,user.username ,user.email from ". TABLE_ICP_IMAGE_TAG ." as tag_image
                        left join (select * from ". TABLE_ICP_IMAGE .") as icp_image on tag_image.icp_image_id = icp_image.id
                        left join (select id,username,email from ". TABLE_USER .") as user on user.id = tag_image.user_id
                        where tag_image.id = ? and tag_image.user_id = ?";
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
                }
            }
        }

        if(count($largeImages) > 0)
        {
            $body = "Hi ". $username.", <br> <br> Thank you for your order, your  &#39;large&#39; Photo is attached.
                                    <br><br> Regards <br> The Facetag Team.<br><br> Order number:".$orderId;
            $subject = "Facetag Photo";
            include_once 'SendAttachmentMail.php';
            $objEmail = new SendAttachmentMail();
            $objEmail->sendEmail($body,$email,$subject,$img_path);
        }

        $status = 1;
        $data['status'] = ($status > 1) ? FAILED : SUCCESS;
        $data['message'] = "Order placed successfully !!!";
        $data['transactionid'] = $transactionid;

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

        $paymentType = validateObject($orderData, 'paymentType', "");
        $paymentType = addslashes($paymentType);

        $cardId = validateObject($orderData, 'cardId', "");
        $cardId = addslashes($cardId);

        $cardInfo = array();
        $cardInfo = validateObject($orderData, 'cardInfo', "");

        $cartItem = array();
        $cartItem = validateObject($orderData, 'cartItem', "");

        $hotelid = validateObject($orderData, 'hotelid', "");
        $hotelid = addslashes($hotelid);


        $transactionid = validateObject($orderData, 'transactionid', "");
        $transactionid = addslashes($transactionid);

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
                if ($isValidCard)
                {


                    //Add cart data
                    $insertFields = "user_id,
                             total_amount,
                             payment_type,
                             card_id,
                             transactionId,
                             modified";

                    $valuesFields = "?,?,?,?,?,?";
                    $insert_query = "" . "Insert into " . TABLE_CART . " (" . $insertFields . ") values (" . $valuesFields . ")";
                    $insert_cart_stmt = $connection->prepare($insert_query);
                    $modifieddate = date("Y-m-d H:i:s");
                    $insert_cart_stmt->bind_param('iiiiss', $userid, $totalAmount, $paymentType, $cardId,$transactionid, $modifieddate);

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
                             shipping_hotel_id,
                             modified";

                    $valuesFields = "?,?,?";
                    $insert_query = "" . "Insert into " . TABLE_ORDER_DETAILS . " (" . $insertFields . ") values (" . $valuesFields . ")";
                    $insert_order_details_stmt = $connection->prepare($insert_query);
                    $modifieddate = date("Y-m-d H:i:s");
                    $insert_order_details_stmt->bind_param('iis', $cartId, $hotelid, $modifieddate);

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
                        $insert_cart_item_stmt->bind_param('iiiiiis', $cartId,$businessid, $selfieid, $is_small_photo, $is_large_photo, $is_frame
                            ,$modifieddate);

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
                                $subject = "Facetag Photo";
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
        $data['transactionid'] = $transactionid;

        return $data;
    }


    public function purchaseWithPaypal($orderData)
    {

        $connection = $GLOBALS['con'];
        $status = 2;
        $posts = array();
        $errorMsg = "Service responce data";

        $userid = validateObject($orderData, 'userId', "");
        $userid = addslashes($userid);

        $hotelid = validateObject($orderData, 'hotelid', "");
        $hotelid = addslashes($hotelid);

        $totalAmount = validateObject($orderData, 'total_amount', "");
        $totalAmount = addslashes($totalAmount);

        $cartItem = array();
        $cartItem = validateObject($orderData, 'cartItem', "");

        $transactionid = validateObject($orderData, 'transactionid', "");
        $transactionid = addslashes($transactionid);

        //Place Order Details


        //Add cart data
        $insertFields = "user_id,
                             total_amount,
                             payment_type,
                             transactionId,
                             modified";

        $valuesFields = "?,?,?,?,?";
        $insert_query = "" . "Insert into " . TABLE_CART . " (" . $insertFields . ") values (" . $valuesFields . ")";

        $insert_cart_stmt = $connection->prepare($insert_query);
        $modifieddate = date("Y-m-d H:i:s");
        $cardId = "";
        $paymentType = "2";
        $insert_cart_stmt->bind_param('iiiss', $userid, $totalAmount, $paymentType,$transactionid, $modifieddate);

        if ($insert_cart_stmt->execute()) {
            $cartId = mysqli_insert_id($connection);
        } else {
            $status = 2;
            $data['status'] = ($status > 1) ? FAILED : SUCCESS;
            $data['message'] = "Fail to save cart data !!!" . $insert_cart_stmt->error;
            return $data;
        }

        //Attach address with cart
//        $insertFields = "cart_id,
//                             shipping_hotel_id,
//                             modified";
//
//        $valuesFields = "?,?,?";
//        $insert_query = "" . "Insert into " . TABLE_ORDER_DETAILS . " (" . $insertFields . ") values (" . $valuesFields . ")";
//        $insert_order_details_stmt = $connection->prepare($insert_query);
//        $modifieddate = date("Y-m-d H:i:s");
//        $insert_order_details_stmt->bind_param('iis', $cartId, $hotelid, $modifieddate);
//
//        if (!($insert_order_details_stmt->execute())) {
//            $status = 2;
//            $data['status'] = ($status > 1) ? FAILED : SUCCESS;
//            $data['message'] = "Fail to attch address with order !!!" . $insert_order_details_stmt->error;
//            return $data;
//        }


        $orderId = time().mt_rand();
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

            //Save Shipping details
            $saveShippingDetails =  $this->saveShippingDetails($item,$orderId,$cartId);
            if($saveShippingDetails['status'] == "0")
            {
                $data['status'] = FAILED;
                $data['message'] = $saveShippingDetails['message'];
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
        $data['transactionid'] = $transactionid;

        return $data;

    }
}

?>
