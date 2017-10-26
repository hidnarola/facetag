<?php
/**
 * Created by PhpStorm.
 * User: c174
 * Date: 09/10/17
 * Time: 4:54 PM
 */

require_once 'SecurityFunctions.php';

class ShippingFunction
{
    function __construct()
    {

    }

    public function call_service($service, $postData)
    {
        switch ($service) {
            case "AddShippingAddress":
            {
                return $this->addShippingAddress($postData);
            }
                break;
            case "UpdateAddress":
            {
                return $this->updateAddress($postData);
            }
                break;
            case "DeleteAddress":
            {
                return $this->deleteAddress($postData);
            }
                break;
            case "GetUserAddress":
            {
                return $this->getUserAddress($postData);
            }
                break;
            case "GetShippingDetails":
            {
                return $this->getShippingDetails($postData);
            }
                break;
        }
    }

    public function getShippingDetails($postdata)
    {
        $connection = $GLOBALS['con'];
        $status = 2;
        $posts = array();
        $errorMsg = "Service responce data";

        $icpId = validateObject($postdata, 'icpId', "");
        $icpId = addslashes($icpId);

        //Select Shipping Details For ICP

        $select_settings_query = "select collection_point_delivery,
                                         local_hotel_delivery,
        domestic_shipping,international_shipping,
        collection_address,collection_address_latitude,collection_address_longitude,collection_address_instructions,local_hotel_delivery_free,
        local_hotel_delivery_price,domestic_shipping_free,domestic_shipping_price,international_shipping_free,international_shipping_price  from ". TABLE_ICP_SETTING .
            " where icp_id = ? order by modified desc";


        $select_settings_stmt = $connection->prepare($select_settings_query);
        $select_settings_stmt->bind_param("i",$icpId);
        $select_settings_stmt->execute();
        $select_settings_stmt->store_result();

        if ($select_settings_stmt->num_rows > 0)
        {
            while($shipping = fetch_assoc_all_values($select_settings_stmt))
            {
                $data['shipping'][] =  $shipping;
            }

            $data['status'] = SUCCESS;
            $data['message'] = "List of user address";
            return $data;
        }
        else
        {
            $data['status'] = FAILED;
            $data['message'] = "Shipping details not available". $select_settings_stmt->error;
            return $data;
        }
    }

    public function getUserAddress($postdata)
    {
        $connection = $GLOBALS['con'];
        $status = 2;
        $posts = array();
        $errorMsg = "Service responce data";

        $userId = validateObject($postdata, 'userId', "");
        $userId = addslashes($userId);



        $isDomestic = validateObject($postdata, 'isDomestic', "");
        $isDomestic = addslashes($isDomestic);


        $select_address = "select address.name ,
                                address.address ,
                                address.city ,
                                 address.post_code ,
                                 address.phone_no
                                from " . TABLE_ADDRESS ." as address where
                                address.user_id =  ?  and
                                address.is_deleted = ? and
                                address.is_permanent_address = ? and
                                address.is_domestic = ?";


        $isdeleted = "0";
        $issaved = "1";

        $select_address_stmt = $connection->prepare($select_address);
        $select_address_stmt->bind_param("ssss",$userId,$isdeleted,$issaved,$isDomestic);



        if ($select_address_stmt->execute())
        {
            $select_address_stmt->store_result();
            while($address = fetch_assoc_all_values($select_address_stmt))
            {
                $data['address'][] =  $address;
            }

            $data['status'] = SUCCESS;
            $data['message'] = "List of user address";
            return $data;
        }
        else
        {
            $data['status'] = FAILED;
            $data['message'] = "Update successfull but something went wrong". $select_address_stmt->error;
            return $data;
        }
    }


    public function deleteAddress($postdata)
    {
        $connection = $GLOBALS['con'];
        $status = 2;
        $posts = array();
        $errorMsg = "Service responce data";

        $addressid = validateObject($postdata, 'addressid', "");
        $addressid = addslashes($addressid);


        $update_query = "Update " . TABLE_ADDRESS . " set
                             is_deleted = ?
                             where id = ?";

        $isdelete = "1";
        $update_address_stmt = $connection->prepare($update_query);
        $update_address_stmt->bind_param("si",$isdelete,$addressid);

        if($update_address_stmt->execute())
        {
            $data['status'] = SUCCESS;
            $data['message'] = "Address is deleted successfully";
            return $data;
        }
        else
        {
            $data['status'] = FAILED;
            $data['message'] = "Address is not deleted successfully". $update_address_stmt->error;
            return $data;
        }
    }

    public function updateAddress($postdata)
    {

        $connection = $GLOBALS['con'];
        $status = 2;
        $posts = array();
        $errorMsg = "Service responce data";

        $addressid = validateObject($postdata, 'addressid', "");
        $addressid = addslashes($addressid);

        $title = validateObject($postdata, 'title', "");
        $title = addslashes($title);

        $address = validateObject($postdata, 'address', "");
        $address = addslashes($address);

        $city = validateObject($postdata, 'city', "");
        $city = addslashes($city);

        $postalcode = validateObject($postdata, 'postalcode', "");
        $postalcode = addslashes($postalcode);

        $phonenumber = validateObject($postdata, 'phonenumber', "");
        $phonenumber = addslashes($phonenumber);

        $update_query = "Update " . TABLE_ADDRESS . " set
                             name = ? ,
                             address = ? ,
                             city = ?,
                             post_code = ? ,
                             phone_no = ?
                             where id = ?";

        $update_address_stmt = $connection->prepare($update_query);
        $update_address_stmt->bind_param("sssssi",$title,$address,$city,$postalcode,$phonenumber,$addressid);

        if($update_address_stmt->execute())
        {

            $select_address = "select * from " . TABLE_ADDRESS ." as address where
                                address.id =  ?";

            $select_address_stmt = $connection->prepare($select_address);
            $select_address_stmt->bind_param("i",$addressid);
            $select_address_stmt->execute();
            $select_address_stmt->store_result();

            if ($select_address_stmt->num_rows > 0)
            {
                $address = fetch_assoc_all_values($select_address_stmt);
                $data['address'][] =  $address;
                $data['status'] = SUCCESS;
                $data['message'] = "Address save successfully";
                return $data;
            }
            else
            {
                $data['status'] = FAILED;
                $data['message'] = "Update successfull but something went wrong". $select_address_stmt->error;
                return $data;
            }
        }
        else
        {
            $data['status'] = FAILED;
            $data['message'] = "Address is not update successfully". $update_address_stmt->error;
            return $data;
        }
    }

    public function addShippingAddress($postdata)
    {

        $connection = $GLOBALS['con'];
        $status = 2;
        $posts = array();
        $errorMsg = "Service responce data";



        $userid = validateObject($postdata, 'userId', "");
        $userid = addslashes($userid);

        $title = validateObject($postdata, 'title', "");
        $title = addslashes($title);

        $address = validateObject($postdata, 'address', "");
        $address = addslashes($address);

        $city = validateObject($postdata, 'city', "");
        $city = addslashes($city);

        $postalcode = validateObject($postdata, 'postalcode', "");
        $postalcode = addslashes($postalcode);

        $phonenumber = validateObject($postdata, 'phonenumber', "");
        $phonenumber = addslashes($phonenumber);

        $ispermanant = validateObject($postdata, 'ispermanant', "");
        $ispermanant = addslashes($ispermanant);

        $isdomestic = validateObject($postdata, 'isdomestic', "");
        $isdomestic = addslashes($isdomestic);

        //Save in database

        $insertFields = "user_id,
                             name,
                             address,
                             city,
                             post_code,
                             phone_no,
                             is_permanent_address,
                             is_domestic
                             ";


        $valuesFields = "?,?,?,?,?,?,?,?";
        $insert_query = "" . "Insert into " . TABLE_ADDRESS . " (" . $insertFields . ") values (" . $valuesFields . ")";
        $select_insert_address_stmt = $connection->prepare($insert_query);
        $select_insert_address_stmt->bind_param('isssssss', $userid, $title, $address, $city, $postalcode,
            $phonenumber, $ispermanant,$isdomestic);


        if ($select_insert_address_stmt) {
            if ($select_insert_address_stmt->execute())
            {
                $addressId = mysqli_insert_id($connection);

                $select_address = "select
                                address.name ,
                                address.address ,
                                address.city ,
                                address.post_code ,
                                address.phone_no from " . TABLE_ADDRESS ." as address where
                                address.id =  ?";

                $select_address_stmt = $connection->prepare($select_address);
                $select_address_stmt->bind_param("i",$addressId);
                $select_address_stmt->execute();
                $select_address_stmt->store_result();

                if ($select_address_stmt->num_rows > 0)
                {
                    $address = fetch_assoc_all_values($select_address_stmt);
                    $data['address'][] =  $address;
                    $data['status'] = SUCCESS;
                    $data['message'] = "Address save successfully";
                    return $data;
                }
                else
                {
                    $data['status'] = FAILED;
                    $data['message'] = "Save successfull but something went wrong". $select_address_stmt->error;
                    return $data;
                }


            }
            else
            {
                $data['status'] = FAILED;
                $data['message'] = "Address not save successfully". $select_insert_address_stmt->error;
                return $data;
            }

        }
        else
        {

            $data['status'] = FAILED;
            $data['message'] = "Address not save successfully". $select_insert_address_stmt->error;
            return $data;
        }
    }
}