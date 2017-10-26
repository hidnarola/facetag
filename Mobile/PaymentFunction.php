<?php
/**
 * Created by PhpStorm.
 * User: c119
 * Date: 03/03/15
 * Time: 4:16 PM
 */

class PaymentFunction
{
    function __construct()
    {

    }

    public function call_service($service, $postData)
    {
        switch ($service) {
            case "SaveCardDetails": {
                return $this->saveCardDetails($postData);
            }
                break;
            case "GetCardDetails": {
                return $this->getCardDetails($postData);
            }
                break;
            case "EditCard": {
                    return $this->editCard($postData);
                }
                break;
            case "DeleteCard": {
                return $this->deleteCard($postData);
            }
                break;
        }
    }

    public function deleteCard($businessData)
    {

        $connection = $GLOBALS['con'];
        $status = 2;
        $posts = array();
        $errorMsg = "Service responce data";

        $cardid = validateObject($businessData, 'cardid', "");
        $cardid = addslashes($cardid);

        $update_card = "update " . TABLE_CARD . " set
                        is_deleted = ? ,
                        modified = ?
                        where id = ?" ;

        $update_card_stmt = $connection->prepare($update_card);
        $isdeleted = 1;
        $currentdate = date("Y-m-d H:i:s");
        $update_card_stmt->bind_param("isi",$isdeleted,$currentdate,$cardid);

        if($update_card_stmt->execute())
        {
            $select_card = "select * from " . TABLE_CARD . " where id = ?";
            $select_card_stmt = $connection->prepare($select_card);
            $select_card_stmt->bind_param("i",$cardid);
            if( $select_card_stmt->execute())
            {
                $select_card_stmt->store_result();
                $posts[] = fetch_assoc_all_values($select_card_stmt);

                $status = 1;
                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                $data['message'] = "Card deleted successfully !!!";
                $data['card'] = $posts;
                return $data;
            }
            else
            {
                $status = 2;
                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                $data['message'] = "Please try again !!!";
                $data['card'] = $posts;
                return $data;
            }

        }
        else
        {
            $status = 2;
            $data['status'] = ($status > 1) ? FAILED : SUCCESS;
            $data['message'] = "Please try again !!!";
            $data['card'] = $posts;
            return $data;
        }


    }
    public function editCard($businessData)
    {

        $connection = $GLOBALS['con'];
        $status = 2;
        $posts = array();
        $errorMsg = "Service responce data";

        $cardid = validateObject($businessData, 'cardid', "");
        $cardid = addslashes($cardid);

        $cardnumber = validateObject($businessData, 'cardnumber', "");
        $cardnumber = addslashes($cardnumber);

        $nameoncard = validateObject($businessData, 'nameoncard', "");
        $nameoncard = addslashes($nameoncard);

        $expirymonth = validateObject($businessData, 'expirymonth', "");
        $expirymonth = addslashes($expirymonth);

        $expiryyear = validateObject($businessData, 'expiryyear', "");
        $expiryyear = addslashes($expiryyear);

        $cvvcode = validateObject($businessData, 'cvvcode', "");
        $cvvcode = addslashes($cvvcode);

        $update_card = "update " . TABLE_CARD . " set
                        card_number = ? ,
                        name_on_card = ? ,
                        expiry_month = ? ,
                        expiry_year = ? ,
                        cvv_code = ?,
                        modified = ?
                        where id = ?";

        $update_card_stmt = $connection->prepare($update_card);
        $currentdate = date("Y-m-d H:i:s");
        $update_card_stmt->bind_param("isiiisi",$cardnumber,$nameoncard,$expirymonth,$expiryyear,$cvvcode,$currentdate,$cardid);


        if($update_card_stmt->execute())
        {
            $select_card = "select * from " . TABLE_CARD . " where id = ?"  ;
            $select_card_stmt = $connection->prepare($select_card);
            $select_card_stmt->bind_param("i",$cardid);
            $select_card_stmt->execute();
            $select_card_stmt->store_result();

            $posts[] = fetch_assoc_all_values($select_card_stmt);

            $status = 1;
            $data['status'] = ($status > 1) ? FAILED : SUCCESS;
            $data['message'] = "Card updated successfully !!!";
            $data['card'] = $posts;
            return $data;
        }
        else
        {
            $status = 2;
            $data['status'] = ($status > 1) ? FAILED : SUCCESS;
            $data['message'] = "Please try again !!!";
            $data['card'] = $posts;
            return $data;
        }


    }

    public function getCardDetails($cardData)
    {

        $connection = $GLOBALS['con'];
        $status = 2;
        $posts = array();
        $errorMsg = "Service responce data";

        $userid = validateObject($cardData, 'userId', "");
        $userid = addslashes($userid);

        $select_card = "select * from " . TABLE_CARD . " where user_id = ? and is_deleted != ? and is_saved = ?" ;
        $select_card_stmt = $connection->prepare($select_card);
        $isdelete = 1;
        $issaved = 1;
        $select_card_stmt->bind_param("iii",$userid,$isdelete,$issaved);
        if($select_card_stmt->execute())
        {
            $select_card_stmt->store_result();
            if($select_card_stmt->num_rows > 0)
            {
                while($card = fetch_assoc_all_values($select_card_stmt))
                {
                    $posts[] = $card;
                }
                $status = 1;
                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                $data['message'] = "List of cards !!!";
                $data['card'] = $posts;
                return $data;
            }
            else
            {
                $status = 2;
                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                $data['message'] = "Card not found !!!";
                return $data;
            }
        }
        else
        {
            $status = 2;
            $data['status'] = ($status > 1) ? FAILED : SUCCESS;
            $data['message'] = "Please try again !!!".$insert_query_stmt->error;
            $data['card'] = $posts;
            return $data;
        }


    }
    public function saveCardDetails($cardData)
    {

        $connection = $GLOBALS['con'];
        $status = 2;
        $posts = array();
        $errorMsg = "Service responce data";

        $userid = validateObject($cardData, 'userId', "");
        $userid = addslashes($userid);

        $cardnumber = validateObject($cardData, 'cardnumber', "");
        $cardnumber = addslashes($cardnumber);

        $nameoncard = validateObject($cardData, 'nameoncard', "");
        $nameoncard = addslashes($nameoncard);

        $expirymonth = validateObject($cardData, 'expirymonth', "");
        $expirymonth = addslashes($expirymonth);

        $expiryyear = validateObject($cardData, 'expiryyear', "");
        $expiryyear = addslashes($expiryyear);

        $cvvcode = validateObject($cardData, 'cvvcode', "");
        $cvvcode = addslashes($cvvcode);

        $insertFields =     "user_id,
                             card_number,
                             name_on_card,
                             expiry_month,
                             expiry_year,
                             cvv_code,
                             modified";
        $valuesFields = "?,?,?,?,?,?,?";
        $insert_query = "Insert into " . TABLE_CARD . " (" . $insertFields . ") values(" . $valuesFields . ")";
        $insert_query_stmt = $connection->prepare($insert_query);
        $currentdate = date("Y-m-d H:i:s");
        $insert_query_stmt->bind_param("iisiiis",$userid,$cardnumber,$nameoncard,$expirymonth,$expiryyear,$cvvcode,$currentdate);

        if($insert_query_stmt->execute())
        {
            $card_id = mysqli_insert_id($connection);

            $select_card = "select * from " . TABLE_CARD . " where id = ?";
            $select_card_stmt = $connection->prepare($select_card);
            $select_card_stmt->bind_param("i",$card_id);
            $select_card_stmt->execute();
            $select_card_stmt->store_result();
            $card[] = fetch_assoc_all_values($select_card_stmt);

            $status = 1;
            $data['status'] = ($status > 1) ? FAILED : SUCCESS;
            $data['message'] = "Card save successfully!!!";
            $data['card'] = $card;
            return $data;

        }
        else
        {
            $status = 2;
            $data['status'] = ($status > 1) ? FAILED : SUCCESS;
            $data['message'] = "Please try again !!!".$insert_query_stmt->error;
            $data['card'] = $posts;
            return $data;
        }


    }

}
?>