<?php
/**
 * Created by PhpStorm.
 * User: c119
 * Date: 03/03/15
 * Time: 4:16 PM
 */

class BusinessFunction
{
    function __construct()
    {

    }

    public function call_service($service, $postData)
    {
        switch ($service) {
            case "GetAllBusiness": {
                return $this->getAllBusiness($postData);
            }
                break;
            case "SearchBusiness": {
                return $this->searchBusiness($postData);
            }
                break;
            case "CheckInBusiness": {
                return $this->checkInBusiness($postData);
            }
                break;
            case "GetCurrentBusiness": {
                return $this->getCurrentBusiness($postData);
            }
                break;
            case "GetBusinessImage": {
                return $this->getBusinessImage($postData);
            }
                break;
            case "GetBusinessICP": {
                return $this->GetBusinessICP($postData);
            }
                break;
            case "GetBusinessSearchICP": {
                return $this->getBusinessSearchICP($postData);
            }
                break;
            case "GetAllCheckinBusiness":
            {
                return $this->getAllCheckinBusiness($postData);
            }
                break;
            case "GetBusinessSpecificSelfie":
            {
                return $this->getBusinessSpecificSelfie($postData);
            }
                break;
            case "ToggleBusinessLikes":
            {
                return $this->toggleBusinesslikes($postData);
            }
                break;
            case "ToggleBusinessFavorites":
            {
                return $this->toggleBusinessFavorites($postData);
            }
                break;
            case "GetNearByBusiness":
            {
                return $this->getNearByBusiness($postData);
            }
            case "DeleteSelfie":
            {
                return $this->deleteSelfie($postData);
            }
            case "GetBusinessLocationData":
            {
                return $this->getBusinessLocationData($postData);
            }
            case "GetBusinessHotel":
            {
                return $this->getBusinessHotel($postData);
            }
            case "GetSpecificBusinessDetails":
            {
                return $this->getSpecificBusinessDetails($postData);
            }
        }
    }




    public function getSpecificBusinessDetails($postData)
    {
        $connection = $GLOBALS['con'];
        $status = 2;
        $posts = array();
        $errorMsg = "Service responce data";

        $businessId = validateObject($postData, 'businessId', "");
        $businessId = addslashes($businessId);

        $userid = validateObject($postData, 'userid', "");
        $userid = addslashes($userid);

        $select_business = "Select * from " . TABLE_BUSINESS . " where id = ? and is_delete = ?";
        $select_business_stmt = $connection->prepare($select_business);
        $isdelete = 0;
        $select_business_stmt->bind_param("is",$businessId,$isdelete);

        if($select_business_stmt->execute())
        {
            $select_business_stmt->store_result();
            if($select_business_stmt->num_rows > 0)
            {
                while($business = fetch_assoc_all_values($select_business_stmt))
                {

                    //check is like
                    $select_business_likes = "Select * from " . TABLE_LIKES ." where businessid = ? and userid = ?";
                    $select_business_likes = $connection->prepare($select_business_likes);
                    $select_business_likes->bind_param("ii",$business['id'],$userid);
                    $select_business_likes->execute();
                    $select_business_likes->store_result();
                    if($select_business_likes->num_rows > 0)
                    {
                        $businesslikes = fetch_assoc_all_values($select_business_likes);
                        $business['islike'] = $businesslikes['islike'];
                    }
                    else
                    {
                        $business['islike'] = "0";
                    }

                    //check is favorite
                    $select_business_favorite = "Select * from " . TABLE_FAVORITES ." where businessid = ? and userid = ?";
                    $select_business_favorite = $connection->prepare($select_business_favorite);
                    $select_business_favorite->bind_param("ii",$business['id'],$userid);
                    $select_business_favorite->execute();
                    $select_business_favorite->store_result();
                    if($select_business_favorite->num_rows > 0)
                    {
                        $businessfavorites = fetch_assoc_all_values($select_business_favorite);
                        $business['isfavorite'] = $businessfavorites['isfavorite'];
                    }
                    else
                    {
                        $business['isfavorite'] = "0";
                    }

                    $posts[] = $business;
                }

                $status = 1;
                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                $data['message'] = "List of business !!!";
                $data['business'] = $posts;
                return $data;
            }
            else
            {
                $status = 1;
                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                $data['message'] = "Business not found !!!";
                $data['business'] = $posts;
                return $data;
            }
        }
        else
        {
            $status = 2;
            $data['status'] = ($status > 1) ? FAILED : SUCCESS;
            $data['message'] = "Please try again";
            $data['business'] = $posts;
            return $data;
        }




    }
    public function getBusinessHotel($postData)
    {
        $connection = $GLOBALS['con'];
        $status = 2;
        $posts = array();
        $errorMsg = "Service responce data";

        $icpID = validateObject($postData, 'icpId', "");
        $icpID = addslashes($icpID);

        $select_hotel_query = "select * from hotels where FIND_IN_SET( icp_id, ? ) and is_delete = ? ";
        $select_hotel_stmt = $connection->prepare($select_hotel_query);
        $isdelete = "0";
        $select_hotel_stmt->bind_param("ss", $icpID,$isdelete);

        if ($select_hotel_stmt->execute()) {
            $select_hotel_stmt->store_result();
            if ($select_hotel_stmt->num_rows > 0)
            {
                while($hotel = fetch_assoc_all_values($select_hotel_stmt))
                {
                    $posts[] =$hotel;
                }
                $status = 1;
                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                $data['message'] = "List of hotel...";
                $data['Hotels'] = $posts;
                return $data;
            }
            else
            {
                $status = 2;
                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                $data['message'] = "Hotel's not found...";
                return $data;

            }
        }
        $status = 2;
        $data['status'] = ($status > 1) ? FAILED : SUCCESS;
        $data['message'] = "Some thing went wrong please try again...";
        return $data;

    }

    public function getBusinessLocationData($postData)
    {
        $connection = $GLOBALS['con'];
        $status = 2;
        $posts = array();
        $errorMsg = "Service responce data";


        $synchDate = validateObject($postData, 'lastSynchDate', "");
        $synchDate = addslashes($synchDate);



        if($synchDate == '0')
        {
            $select_business_query = "select
        business.id as businessid,
        business.name as name,
        business.logo as logo,
        business.description as description,
        business.address1 as address1,
        business.latitude as latitude,
        business.longitude as longitude,
        business.is_delete as is_delete
        from " . TABLE_BUSINESS . " as business
        where business.is_delete = ? and business.latitude IS NOT NULL and business.longitude IS NOT NULL";

            $select_business_stmt = $connection->prepare($select_business_query);
            $isdelete = "0";
            $select_business_stmt->bind_param("s", $isdelete);
        }
        else
        {
            $select_business_query = "select
        business.id as businessid,
        business.name as name,
        business.logo as logo,
        business.description as description,
        business.address1 as address1,
        business.latitude as latitude,
        business.longitude as longitude,
        business.is_delete as is_delete
        from " . TABLE_BUSINESS . " as business
        where business.is_delete = ? and business.latitude IS NOT NULL and business.longitude IS NOT NULL and modified >= ?";

            $select_business_stmt = $connection->prepare($select_business_query);
            $isdelete = "0";
            $select_business_stmt->bind_param("ss", $isdelete,$synchDate);
        }


        if ($select_business_stmt->execute())
        {
            $select_business_stmt->store_result();
            if ($select_business_stmt->num_rows > 0)
            {
                while($business = fetch_assoc_all_values($select_business_stmt))
                {

                    $select_icp_query = "select count(*) as icpCount from " . TABLE_ICPS . " where business_id = ? and is_delete = ?";
                    $select_icp_stmt = $connection->prepare($select_icp_query);
                    $isdelete = "0";
                    $select_icp_stmt->bind_param("is", $business['businessid'],$isdelete);

                    if ($select_icp_stmt->execute()) {
                        $select_icp_stmt->store_result();
                        if ($select_icp_stmt->num_rows > 0)
                        {
                            $icpCount = fetch_assoc_all_values($select_icp_stmt);
                            if( $icpCount['icpCount'] > 0)
                            {
                                $business['icpCount'] = $icpCount['icpCount'];
                                $posts[] = $business;
                            }

                        }
                    }
                }
                $status = 1;
                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                $data['message'] = "List of business...";
                $data['businessLoc'] = $posts;
                return $data;
            }
            else
            {
                $status = 2;
                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                $data['message'] = "business's not found...";
                return $data;

            }
        }
        $status = 2;
        $data['status'] = ($status > 1) ? FAILED : SUCCESS;
        $data['message'] = "Some thing went wrong please try again...";
        return $data;
    }


    public function deleteSelfie($postData)
    {
        $connection = $GLOBALS['con'];
        $status = 2;
        $posts = array();
        $errorMsg = "Service responce data";

        $selfieid = validateObject($postData, 'selfieid', "");
        $selfieid = addslashes($selfieid);

        $update_delete_query = "update " . TABLE_ICP_IMAGE_TAG . " set is_delete = ? where id = ? ";
        $update_delete_stmt = $connection->prepare($update_delete_query);
        $isdelete = "1";
        $update_delete_stmt->bind_param("ii",$isdelete,$selfieid);
        if($update_delete_stmt->execute())
        {
            $status = 1;
            $data['status'] = ($status > 1) ? FAILED : SUCCESS;
            $data['message'] = "Delete Successfully !!!";
            return $data;
        }
        else
        {
            $status = 2;
            $data['status'] = ($status > 1) ? FAILED : SUCCESS;
            $data['message'] = "Some thing went wrong please try again !!!";
            return $data;
        }
    }

    public function toggleBusinessFavorites($postData)
    {
        $connection = $GLOBALS['con'];
        $status = 2;
        $posts = array();
        $errorMsg = "Service responce data";

        $businessid = validateObject($postData, 'businessid', "");
        $businessid = addslashes($businessid);

        $userid = validateObject($postData, 'userId', "");
        $userid = addslashes($userid);

        $select_favorites = "select id,isfavorite from ". TABLE_FAVORITES ." where userid = ? and businessid = ?";
        $select_favorites_stmt = $connection->prepare($select_favorites);
        $select_favorites_stmt->bind_param("ii",$userid,$businessid);

        if($select_favorites_stmt->execute())
        {
            $select_favorites_stmt->store_result();
            if($select_favorites_stmt->num_rows > 0)
            {
                $isfavorites = fetch_assoc_all_values($select_favorites_stmt);

                if($isfavorites['isfavorite'] == "0")
                {
                    $isfavorite = "1";
                }
                else
                {
                    $isfavorite = "0";
                }

                $update_favorites_query = "update " . TABLE_FAVORITES . " set isfavorite = ? where id = ?";
                $update_favorites_stmt = $connection->prepare($update_favorites_query);
                $update_favorites_stmt->bind_param("si",$isfavorite,$isfavorites['id']);
                if($update_favorites_stmt->execute())
                {
                    if($isfavorite == "0")
                    {
                        $update_favorite_query = "update " . TABLE_BUSINESS . " business set business.favorite = business.favorite  - 1 where  business.id = ?";
                    }
                    else
                    {
                        $update_favorite_query = "update " . TABLE_BUSINESS . " business set business.favorite = business.favorite  + 1 where  business.id = ?";
                    }
                    $update_favorite_stmt = $connection->prepare($update_favorite_query);
                    $update_favorite_stmt->bind_param("i",$businessid);
                    if($update_favorite_stmt->execute())
                    {
                        $status = 1;
                        $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                        $data['message'] = "Favorite Successfully !!!";
                        return $data;
                    }
                    else
                    {
                        $status = 2;
                        $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                        $data['message'] = "Pleasse try again !!!";
                        return $data;
                    }

                }
                else
                {
                    $status = 2;
                    $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                    $data['message'] = "Pleasse try again !!!";
                    return $data;
                }
            }
            else
            {
                //Insert New Like
                $insertFields = "userid,
                                 businessid,
                                 isfavorite,
                                 createddate";

                $valuesFields = "?,?,?,?";
                $insert_query = "Insert into " . TABLE_FAVORITES . " (" . $insertFields . ") values(" . $valuesFields . ")";
                $insert_query_stmt = $connection->prepare($insert_query);
                $isfavorite = "1";
                $currentdate = date("Y-m-d H:i:s");

                $insert_query_stmt->bind_param("iiss",$userid,$businessid,$isfavorite,$currentdate);
                if($insert_query_stmt->execute())
                {
                    //increement favorite count
                    $update_favorite_query = "update " . TABLE_BUSINESS . " business set business.favorite = business.favorite  + 1 where  business.id = ?";
                    $update_favorite_stmt = $connection->prepare($update_favorite_query);
                    $update_favorite_stmt->bind_param("i",$businessid);
                    if($update_favorite_stmt->execute())
                    {
                        $status = 1;
                        $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                        $data['message'] = "Favorite Successfully !!!";
                        return $data;
                    }
                    else
                    {
                        $status = 2;
                        $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                        $data['message'] = "Pleasse try again !!!";
                        return $data;
                    }
                }
                else
                {
                    $status = 2;
                    $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                    $data['message'] = "Pleasse try again !!!";
                    return $data;
                }
            }
        }
        else
        {
            $status = 2;
            $data['status'] = ($status > 1) ? FAILED : SUCCESS;
            $data['message'] = "Pleasse try again !!!";
            return $data;
        }
    }

    public function toggleBusinesslikes($postData)
    {

        $connection = $GLOBALS['con'];
        $status = 2;
        $posts = array();
        $errorMsg = "Service responce data";

        $businessid = validateObject($postData, 'businessid', "");
        $businessid = addslashes($businessid);

        $userid = validateObject($postData, 'userId', "");
        $userid = addslashes($userid);

        $select_likes = "select id,islike from ". TABLE_LIKES ." where userid = ? and businessid = ?";
        $select_likes_stmt = $connection->prepare($select_likes);
        $select_likes_stmt->bind_param("ii",$userid,$businessid);

         if($select_likes_stmt->execute())
        {
            $select_likes_stmt->store_result();
            if($select_likes_stmt->num_rows > 0)
            {
                $islikes = fetch_assoc_all_values($select_likes_stmt);

                if($islikes['islike'] == "0")
                {
                    $islike = "1";
                }
                else
                {
                    $islike = "0";
                }

                $update_likes_query = "update " . TABLE_LIKES . " set islike = ? where id = ?";
                $update_likes_stmt = $connection->prepare($update_likes_query);
                $update_likes_stmt->bind_param("si",$islike,$islikes['id']);
                if($update_likes_stmt->execute())
                {
                    if($islike == "0")
                    {
                        $update_likes_query = "update " . TABLE_BUSINESS . " business set business.likes = business.likes  - 1 where  business.id = ?";
                    }
                    else
                    {
                        $update_likes_query = "update " . TABLE_BUSINESS . " business set business.likes = business.likes  + 1 where  business.id = ?";
                    }
                    $update_likes_stmt = $connection->prepare($update_likes_query);
                    $update_likes_stmt->bind_param("i",$businessid);
                    if($update_likes_stmt->execute())
                    {
                        $status = 1;
                        $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                        $data['message'] = "Like Successfully !!!";
                        return $data;
                    }
                    else
                    {
                        $status = 2;
                        $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                        $data['message'] = "Pleasse try again !!!";
                        return $data;
                    }

                }
                else
                {
                    $status = 2;
                    $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                    $data['message'] = "Pleasse try again !!!";
                    return $data;
                }
            }
            else
            {
                //Insert New Like
                $insertFields = "userid,
                                 businessid,
                                 islike,
                                 createddate";

                $valuesFields = "?,?,?,?";
                $insert_query = "Insert into " . TABLE_LIKES . " (" . $insertFields . ") values(" . $valuesFields . ")";
                $insert_query_stmt = $connection->prepare($insert_query);
                $islike = "1";
                $currentdate = date("Y-m-d H:i:s");

                $insert_query_stmt->bind_param("iiss",$userid,$businessid,$islike,$currentdate);
                if($insert_query_stmt->execute())
                {
                    //increement like count
                    $update_likes_query = "update " . TABLE_BUSINESS . " business set business.likes = business.likes  + 1 where  business.id = ?";
                    $update_likes_stmt = $connection->prepare($update_likes_query);
                    $update_likes_stmt->bind_param("i",$businessid);
                    if($update_likes_stmt->execute())
                    {
                        $status = 1;
                        $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                        $data['message'] = "Like Successfully !!!";
                        return $data;
                    }
                    else
                    {
                        $status = 2;
                        $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                        $data['message'] = "Pleasse try again !!!";
                        return $data;
                    }
                }
                else
                {
                    $status = 2;
                    $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                    $data['message'] = "Pleasse try again !!!";
                    return $data;
                }
            }
        }
        else
        {
            $status = 2;
            $data['status'] = ($status > 1) ? FAILED : SUCCESS;
            $data['message'] = "Pleasse try again !!!";
            return $data;
        }

    }


    public function getBusinessSpecificSelfie($postData)
    {
        $connection = $GLOBALS['con'];
        $status = 2;
        $posts = array();
        $errorMsg = "Service responce data";

        $userid = validateObject($postData, 'userId', "");
        $userid = addslashes($userid);

        $businessid = validateObject($postData, 'businessid', "");
        $businessid = addslashes($businessid);

        $noofrecord = validateObject($postData, 'noofrecord', "");
        $noofrecord = addslashes($noofrecord);

        $offset = validateObject($postData, 'offset', "");
        $offset = addslashes($offset);


        $select_business_image_query = "select icptagimg.id  as selfieid,

                                    icptagimg.is_purchased,

                                     icptagimg.is_small_purchase,
                                     icptagimg.is_large_purchase,
                                     icptagimg.is_printed_purchase,

                                    icptagimg.created as detectedtime,
                                    icptagimg.closingtime as closingtime,
                                    icptagimg.verifiedtime as verifiedtime,
                                    icpiamges.image as selfieimg,
                                    icpiamges.id as icpimgid,
                                    icp.name as icpname,
                                    icp.icp_logo,
                                    icp.business_id as businessid,
                                    icpsettings.*,
                                icp.low_resolution_price,
                                icp.high_resolution_price,
                                icp.offer_printed_souvenir,
                                icp.printed_souvenir_price,
                                business.id as businessid,
                                business.name as businessname,
                                business.logo as businesslogo,
                                business.address1 as address1,
                                business.address2 as address2
                                from " . TABLE_ICPS . " as icp left join
                                      (select icp_id as icpid,
                                      preview_photo,addlogo_to_sharedimage,
                                      is_low_image_free,
                                      is_high_image_free,
                                      lowfree_on_highpurchase,
                                      digital_free_on_physical_purchase,
                                      collection_point_delivery,
                                      local_hotel_delivery,
                                      domestic_shipping,
                                      international_shipping,
                                      collection_address_latitude,
                                      collection_address_longitude,
                                      local_hotel_delivery_free,
                                      local_hotel_delivery_price,
                                      domestic_shipping_free,
                                      domestic_shipping_price,
                                      international_shipping_free,
                                      international_shipping_price,
                                      is_image_timelimited,
                                      image_availabilty_time_limit
                                      from " . TABLE_ICP_SETTING . ") as icpsettings on icp.id = icpsettings.icpid left join
                                      (select * from " . TABLE_ICP_IMAGE . ") as icpiamges on icp.id  = icpiamges.icp_id left join
                                      (select * from " . TABLE_ICP_IMAGE_TAG . ") as icptagimg on icptagimg.icp_image_id = icpiamges.id
                                      left join (select * from " . TABLE_BUSINESS . ") as business on business.id  = icp.business_id
                                      where icp.business_id = ? and icpiamges.is_face_detected = ? and icptagimg.is_currentuser = ? and icptagimg.user_id = ? and icptagimg.is_delete = ? and (icptagimg.closingtime > ? or icptagimg.closingtime is null or icptagimg.closingtime = '')order by icptagimg.verifiedtime desc";


        $select_business_image_stmt = $connection->prepare($select_business_image_query);
        $isfacedetect= "1";
        $iscurrentuser= "1";
        $isdelete = 0;
        $currentdate = date("Y-m-d H:i:s");
        $select_business_image_stmt->bind_param("isiiis",$businessid,$isfacedetect,$iscurrentuser,$userid,$isdelete,$currentdate);
        if($select_business_image_stmt->execute())
        {
            $select_business_image_stmt->store_result();
            if($select_business_image_stmt->num_rows > 0)
            {

                while($businessselfie = fetch_assoc_all_values($select_business_image_stmt)) {
                    $businessselfie['ispromoimg'] = "0";
                    $posts[] =$businessselfie;
                }
                $status = 1;
                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                $data['message'] = "List of Business detected selfie !!!";
                //$data['business_selfie'] = $posts;

            }
            else
            {
                $status = 1;
                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                $data['message'] = "No selfie detected for this business !!!";
                //$data['business_selfie'] = $posts;
            }
        }
        else
        {
            $status = 2;
            $data['status'] = ($status > 1) ? FAILED : SUCCESS;
            $data['message'] = "Pleasse try again !!!";
            return $data;
        }

        $data['noofphotos'] = strval(count($posts));
        //Get Business Promo Images
        $promo = array();
        $select_promo_query = "select id as promoimgid,business_id as businessid,image as promoimg from " . TABLE_BUSINESS_PROMO . " where business_id = ?
        and is_delete = ? order by created desc";
//        echo $select_promo_query;exit;
        $select_promo_query_stmt = $connection->prepare($select_promo_query);
        $isdelete = 0;
        $select_promo_query_stmt->bind_param("ii",$businessid,$isdelete);

        if($select_promo_query_stmt->execute())
        {
            $select_promo_query_stmt->store_result();
            if($select_promo_query_stmt->num_rows > 0)
            {
                while($businesspromo = fetch_assoc_all_values($select_promo_query_stmt)) {
                    $businesspromo['ispromoimg'] = "1";
                    $posts[] =$businesspromo;
                }
                $status = 1;
                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                $data['message'] = "Promo images for this business !!!";
                //$data['business_selfie'] = $posts;
            }
        }
        else
        {
            $status = 2;
            $data['status'] = ($status > 1) ? FAILED : SUCCESS;
            $data['message'] = "Pleasse try again !!!";
            return $data;
        }


        $data['business_selfie'] = array_slice($posts,intval($offset),intval($noofrecord));
        return $data;
    }


    public function getAllCheckinBusiness($postData)
    {
        $connection = $GLOBALS['con'];
        $status = 2;
        $posts = array();
        $errorMsg = "Service responce data";

        $userId = validateObject($postData, 'userId', "");
        $userId = addslashes($userId);

//        $select_business_query = "select distinct business.id,checkin.id as checkinid,business.* from ". TABLE_CHECKIN ." as checkin left join
//        (select * from " . TABLE_BUSINESS . ") as business on checkin.business_id = business.id
//                                  where checkin.user_id  = ? and business.is_delete = ? order by checkin.modified desc";


        $select_business_query = "select distinct businessid as id , name ,
user_id,
reg_no,is_gst_registered,logo,description,street_no,street_name,
address1,latitude,longitude,address2,facebook_url,twitter_url,instagram_url,website_url,
ticket_url,contact_no,contact_email,likes,favorite,checkin,created,modified,address_text,display_text
from
(select distinct business.id as businessid, checkin.id as checkinid,
business.name as name,
business.user_id as user_id,
business.reg_no as reg_no,
business.is_gst_registered as is_gst_registered,
business.logo as logo,
business.description as description,
business.street_no as street_no,
business.street_name as street_name,
business.address1 as address1,
business.latitude as latitude,
business.longitude as longitude,
business.address2 as address2,
business.facebook_url as facebook_url,
business.twitter_url as twitter_url,
business.instagram_url as instagram_url,
business.website_url as website_url,
business.ticket_url as ticket_url,
business.contact_no as contact_no,
business.contact_email as contact_email,
business.likes as likes,
business.favorite as favorite,
business.checkin as checkin,
business.created as created,
business.modified as modified,
business.address_text as address_text,
business.display_text as display_text
from check_in as checkin INNER join
    (select * from businesses) as business on checkin.business_id = business.id
where checkin.user_id  = ? and business.is_delete = ? order by checkin.modified desc) as data";

        //echo $select_business_query;exit;
        $select_business_stmt = $connection->prepare($select_business_query);
        $isdelete = 0;
        $select_business_stmt->bind_param("ii",$userId,$isdelete);

        if($select_business_stmt->execute())
        {
            $select_business_stmt->store_result();
            if($select_business_stmt->num_rows > 0)
            {
                while($business = fetch_assoc_all_values($select_business_stmt)) {

                    //check is favorite business
                    $check_isfavorite_query = "select * from " . TABLE_FAVORITES . " where businessid = ? and userid = ?";
                    $check_isfavorite_stmt = $connection->prepare($check_isfavorite_query);

                    $check_isfavorite_stmt->bind_param("ii",$business['id'],$userId);
                    $check_isfavorite_stmt->execute();
                    $check_isfavorite_stmt->store_result();
                    if($check_isfavorite_stmt->num_rows > 0)
                    {
                        $favorite = fetch_assoc_all_values($check_isfavorite_stmt);
                        $business['isfavorite'] = $favorite['isfavorite'];
                    }
                    else
                    {
                        $business['isfavorite'] = "0";
                    }

                    //chcek is like business
                    $check_islike_query = "select * from " . TABLE_LIKES . " where businessid = ? and userid = ?";
                    $check_islike_stmt = $connection->prepare($check_islike_query);
                    $check_islike_stmt->bind_param("ii",$business['id'],$userId);
                    $check_islike_stmt->execute();
                    $check_islike_stmt->store_result();
                    if($check_islike_stmt->num_rows > 0)
                    {
                        $like = fetch_assoc_all_values($check_islike_stmt);
                        $business['islike'] = $like['islike'];
                    }
                    else
                    {
                        $business['islike'] = "0";
                    }
                    $posts[] =$business;
                }

                $status = 1;
                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                $data['message'] = "List of check in business !!!";
                $data['business'] = $posts;
                return $data;
            }
            else
            {
                $status = 1;
                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                $data['message'] = "No check business found !!!";
                $data['business'] = $posts;
                return $data;

            }
        }
        else
        {
            $status = 2;
            $data['status'] = ($status > 1) ? FAILED : SUCCESS;
            $data['message'] = "Please try again !!!";
            return $data;
        }
    }

    public function getBusinessSearchICP($businessData)
    {
        $connection = $GLOBALS['con'];
        $status = 2;
        $posts = array();
        $errorMsg = "Service responce data";

        $businessid = validateObject($businessData, 'businessid', "");
        $businessid = addslashes($businessid);


        $selct_business_icp = "select
          icps.*
          from " . TABLE_ICPS . "  as icps left join (select  * from " . TABLE_ICP_SETTING .  ") as icpsetting on icpsetting.icp_id = icps.id
          where icps.business_id = ? and icpsetting.allow_manual_search = ? and icps.is_delete = ? and icps.latitude IS NOT NULL and icps.longitude IS NOT NULL";
        //echo $selct_business_icp;exit;
        $selct_business_icp_stmt  = $connection->prepare($selct_business_icp);
        $isallowsearch = "1";
        $isdelete = 0;
        $selct_business_icp_stmt->bind_param("isi",$businessid,$isallowsearch,$isdelete);

        if($selct_business_icp_stmt->execute())
        {
            $selct_business_icp_stmt->store_result();

            if($selct_business_icp_stmt->num_rows > 0)
            {
                while($icp = fetch_assoc_all_values($selct_business_icp_stmt))
                {
                    $posts[] = $icp;
                }
                $status = 1;
                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                $data['message'] = "List of business icps !!!";
                $data['icp'] = $posts;
                return $data;
            }
            else
            {
                $status = 1;
                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                $data['message'] = "No icps found !!!";
                $data['icp'] = $posts;
                return $data;
            }

        }
        else
        {
            $status = 1;
            $data['status'] = ($status > 1) ? FAILED : SUCCESS;
            $data['message'] = "Please try again !!!";
            $data['icp'] = $posts;
            return $data;
        }

    }

    public function GetBusinessICP($businessData)
    {
        $connection = $GLOBALS['con'];
        $status = 2;
        $posts = array();
        $errorMsg = "Service responce data";

        $businessid = validateObject($businessData, 'businessid', "");
        $businessid = addslashes($businessid);

        $userId = validateObject($businessData, 'userId', "");
        $userId = addslashes($userId);



        $select_old_checkin = "select id,business_id,icp_id from " . TABLE_CHECKIN . " where is_checked_in = ? and user_id = ?
        and business_id = ? ";
        $select_old_checkin_stmt = $connection->prepare($select_old_checkin);
        $ischeckin = 1;
        $select_old_checkin_stmt->bind_param("iii",$ischeckin,$userId,$businessid);
        $arrIcp = array();

        if($select_old_checkin_stmt->execute())
        {
            $select_old_checkin_stmt->store_result();
            if($select_old_checkin_stmt->num_rows > 0) {
                $old_checkin_id = fetch_assoc_all_values($select_old_checkin_stmt);
                $arrIcp = explode(',', $old_checkin_id['icp_id']);
            }
        }
        else
        {
            $status = 2;
            $data['status'] = ($status > 1) ? FAILED : SUCCESS;
            $data['message'] = "Please try again";
            return $data;

        }

        $selct_business_icp = "select * from " . TABLE_ICPS . "  where business_id = ? and is_delete = ? and latitude IS NOT NULL and longitude IS NOT NULL ";
        $isdelete = 0;
        $selct_business_icp_stmt  = $connection->prepare($selct_business_icp);
        $selct_business_icp_stmt->bind_param("ii",$businessid,$isdelete);

        if($selct_business_icp_stmt->execute())
        {
            $selct_business_icp_stmt->store_result();

            if($selct_business_icp_stmt->num_rows > 0)
            {
                while($icp = fetch_assoc_all_values($selct_business_icp_stmt))
                {
                    if(count($arrIcp) > 0)
                    {
                        if (in_array($icp['id'], $arrIcp)) {
                            $icp['isCheckIn'] = "1";
                        }
                        else
                        {
                            $icp['isCheckIn'] = "0";
                        }
                    }
                    else
                    {
                        $icp['isCheckIn'] = "0";
                    }
                    $posts[] = $icp;
                }
                $status = 1;
                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                $data['message'] = "List of business icps !!!";
                $data['icp'] = $posts;
                return $data;
            }
            else
            {
                $status = 1;
                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                $data['message'] = "No icps found !!!";
                $data['icp'] = $posts;
                return $data;
            }

        }
        else
        {
            $status = 1;
            $data['status'] = ($status > 1) ? FAILED : SUCCESS;
            $data['message'] = "Please try again !!!";
            $data['icp'] = $posts;
            return $data;
        }
    }


    public function getBusinessImage($businessData)
    {
        $connection = $GLOBALS['con'];
        $status = 2;
        $posts = array();
        $errorMsg = "Service responce data";

        $businessid = validateObject($businessData, 'businessid', "");
        $businessid = addslashes($businessid);

        $selct_business_img = "select * from " . TABLE_ICP_IMAGE . " where icp_id in (select id from " . TABLE_ICPS . "
        where business_id = ?)
                               and is_delete != ? ORDER BY created desc";
        $isdelete = 1;
        $selct_business_img_stmt = $connection->prepare($selct_business_img);
        $selct_business_img_stmt->bind_param("ii",$businessid,$isdelete);
        if($selct_business_img_stmt->execute())
        {
            $selct_business_img_stmt->store_result();

            if($selct_business_img_stmt->num_rows > 0 )
            {
                while($image = fetch_assoc_all_values($selct_business_img_stmt))
                {
                    $posts[] = $image;
                }
                $status = 1;
                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                $data['message'] = "List of business images !!!";
                $data['business'] = $posts;
                return $data;
            }
            else
            {
                $status = 1;
                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                $data['message'] = "Business images not found !!!";
                $data['business'] = $posts;
                return $data;
            }
        }
        else
        {
            $status = 2;
            $data['status'] = ($status > 1) ? FAILED : SUCCESS;
            $data['message'] = "Please try again";
            $data['business'] = $posts;
            return $data;
        }
    }


    public function getNearByBusiness($businessData)
    {
        $connection = $GLOBALS['con'];
        $status = 2;
        $posts = array();
        $errorMsg = "Service responce data";

        $latitude = validateObject($businessData, 'latitude', "");
        $latitude = addslashes($latitude);

        $longitude = validateObject($businessData, 'longitude', "");
        $longitude = addslashes($longitude);

        $userid = validateObject($businessData, 'userId', "");
        $userid = addslashes($userid);

        $select_business = "Select * from " . TABLE_BUSINESS ."  where is_delete = ?" ;
        $isdelete = 0;

        $select_business_stmt = $connection->prepare($select_business);
        $select_business_stmt->bind_param("i",$isdelete);
        if($select_business_stmt->execute())
        {
            $select_business_stmt->store_result();
            if($select_business_stmt->num_rows > 0){

                while($business = fetch_assoc_all_values($select_business_stmt))
                {
                    include_once 'GlobalFunction.php';
                    $objGlobalFunction = new GlobalFunction();
                    $distance = $objGlobalFunction->distanceInkilometer($latitude,$longitude,$business['latitude'],$business['longitude']);
                    $business['distance'] = (string) $distance;
                    $business['userId'] = $userid;

                    if($distance < 1)
                    {
                        $posts[] = $business;
                    }
                }

                if(count($posts) > 0){

                    $sortOrder = array();
                    foreach ($posts as $business) {
                        $sortOrder[$business['distance']]  = $business;
                    }

                    ksort($sortOrder);
                    unset($posts);
                    $posts = array();
                    foreach ($sortOrder as $business)
                    {
                        array_push($posts, $business);
                    }

                    //include_once 'Notification.php';
                    $objNotification = new Notification();

                    $service = "sendNotiWithDeviceToken";

                    $objNotification->call_service($service, $posts[0]);

                    $status = 1;
                    $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                    $data['message'] = "Business found in your range !!!";
                    // $data['business'] = array_slice($posts,0,10);
                    //$data['business'] = $posts;
                    return $data;

                }else
                {
                    $status = 1;
                    $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                    $data['message'] = "Business not found in your range !!!";
                    $data['business'] = $posts;
                    return $data;
                }

            }else{
                $status = 1;
                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                $data['message'] = "Business not found !!!";
                $data['business'] = $posts;
                return $data;
            }
        }
        else {
            $status = 2;
            $data['status'] = ($status > 1) ? FAILED : SUCCESS;
            $data['message'] = "Please try again";
            $data['business'] = $posts;
            return $data;
        }
    }


    public function getCurrentBusiness($businessData)
    {
        $connection = $GLOBALS['con'];
        $status = 2;
        $posts = array();
        $errorMsg = "Service responce data";

        $latitude = validateObject($businessData, 'latitude', "");
        $latitude = addslashes($latitude);

        $longitude = validateObject($businessData, 'longitude', "");
        $longitude = addslashes($longitude);

        $userid = validateObject($businessData, 'userId', "");
        $userid = addslashes($userid);

        $synchDate = validateObject($businessData, 'lastSynchDate', "");
        $synchDate = addslashes($synchDate);

        if($synchDate == '0' || strlen($synchDate) == 0)
        {
            $select_business = "Select * from " . TABLE_BUSINESS ." where latitude IS NOT NULL and longitude IS NOT NULL" ;
            $select_business_stmt = $connection->prepare($select_business);
        }
        else
        {
            $select_business = "Select * from " . TABLE_BUSINESS ." where latitude IS NOT NULL and longitude IS NOT NULL and modified >= ?" ;
            $select_business_stmt = $connection->prepare($select_business);
            $select_business_stmt->bind_param("s",$synchDate);
        }

        //$select_business = "Select * from " . TABLE_BUSINESS ." where is_delete = ? and latitude IS NOT NULL and longitude IS NOT NULL" ;

        if($select_business_stmt->execute())
        {
            $select_business_stmt->store_result();

            if($select_business_stmt->num_rows > 0)
            {
                while($business = fetch_assoc_all_values($select_business_stmt))
                {
                    //get Range

                    //check is like
                    $select_business_likes = "Select * from " . TABLE_LIKES ." where businessid = ? and userid = ?";
                    $select_business_likes = $connection->prepare($select_business_likes);
                    $select_business_likes->bind_param("ii",$business['id'],$userid);
                    $select_business_likes->execute();
                    $select_business_likes->store_result();
                    if($select_business_likes->num_rows > 0)
                    {
                        $businesslikes = fetch_assoc_all_values($select_business_likes);
                        $business['islike'] = $businesslikes['islike'];
                    }
                    else
                    {
                        $business['islike'] = "0";
                    }

                    //check is favorite
                    $select_business_favorite = "Select * from " . TABLE_FAVORITES ." where businessid = ? and userid = ?";
                    $select_business_favorite = $connection->prepare($select_business_favorite);
                    $select_business_favorite->bind_param("ii",$business['id'],$userid);
                    $select_business_favorite->execute();
                    $select_business_favorite->store_result();
                    if($select_business_favorite->num_rows > 0)
                    {
                        $businessfavorites = fetch_assoc_all_values($select_business_favorite);
                        $business['isfavorite'] = $businessfavorites['isfavorite'];
                    }
                    else
                    {
                        $business['isfavorite'] = "0";
                    }

                    include_once 'GlobalFunction.php';
                    $objGlobalFunction = new GlobalFunction();
                    $distance = $objGlobalFunction->distanceInkilometer($latitude,$longitude,$business['latitude'],$business['longitude']);
                    $business['distance'] = (string) $distance;

                    //check current user is checked in or not

                    $checkin_query = "select * from ". TABLE_CHECKIN . " where user_id = ? and is_checked_in = ? and business_id = ? ";
                    $checkin_stmt = $connection->prepare($checkin_query);
                    $isCheckin = "1";
                    $checkin_stmt->bind_param("isi",$userid,$isCheckin,$business['id']);
                    $checkin_stmt->execute();
                    $checkin_stmt->store_result();
                    if($checkin_stmt->num_rows > 0)
                    {
                        $business['ischeckin'] = "1";
                    }
                    else
                    {
                        $business['ischeckin'] = "0";
                    }

                    $posts[] = $business;
                }

                if(count($posts) > 0)
                {
                    $sortOrder = array();
                    foreach ($posts as $business) {

                        $sortOrder[$business['distance']]  = $business;
                    }

                    ksort($sortOrder);
                    unset($posts);
                    $posts = array();
                    foreach ($sortOrder as $business)
                    {
                        array_push($posts, $business);
                    }
                    //print_r($posts);exit;
                    $status = 1;
                    $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                    $data['message'] = "List of business!!!";
                   // $data['business'] = array_slice($posts,0,10);
                    $data['business'] = $posts;
                    return $data;
                }
                else
                {
                    $status = 1;
                    $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                    $data['message'] = "Business not found in your range !!!";
                    $data['business'] = $posts;
                    return $data;
                }
            }
            else
            {
                $status = 1;
                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                $data['message'] = "Business not found !!!";
                $data['business'] = $posts;
                return $data;
            }
        }
        else
        {
            $status = 2;
            $data['status'] = ($status > 1) ? FAILED : SUCCESS;
            $data['message'] = "Please try again";
            $data['business'] = $posts;
            return $data;
        }
    }


    public function checkInBusiness($businessData)
    {
        $connection = $GLOBALS['con'];
        $status = 2;
        $posts = array();
        $errorMsg = "Service responce data";

        $userid = validateObject($businessData, 'userId', "");
        $userid = addslashes($userid);

        $businessid = validateObject($businessData, 'businessid', "");
        $businessid = addslashes($businessid);

        $icpid = validateObject($businessData, 'icpid', "");
        $icpid = addslashes($icpid);

        //Select old one
        $select_old_checkin = "select id,business_id from " . TABLE_CHECKIN . " where is_checked_in = ? and business_id =?  and user_id = ?";
        $select_old_checkin_stmt = $connection->prepare($select_old_checkin);
        $ischeckin = 1;
        $select_old_checkin_stmt->bind_param("iii",$ischeckin,$businessid,$userid);

        if($select_old_checkin_stmt->execute()) {
            $select_old_checkin_stmt->store_result();

            if($select_old_checkin_stmt -> num_rows > 0)
            {
                $old_checkin_id = fetch_assoc_all_values($select_old_checkin_stmt);

                // update modified date
                $update_checkin_query = "Update " . TABLE_CHECKIN . " set
                                         modified = ?  where id = ? ";

                $update_checkin_stmt = $connection->prepare($update_checkin_query);
                $currentdate = date("Y-m-d H:i:s");
                $update_checkin_stmt->bind_param("si", $currentdate, $old_checkin_id['id']);
                $update_checkin_stmt->execute();

                //echo $old_checkin_id['business_id'];exit;
                if($old_checkin_id['business_id'] == $businessid)
                {
                    //echo "1";exit;
                    $update_old_checkin = "update " . TABLE_CHECKIN . " set icp_id = ? , modified = ?  where id = ?";
                    $update_old_checkin_stmt = $connection->prepare($update_old_checkin);
                    $currentdate = date("Y-m-d H:i:s");
                    $update_old_checkin_stmt->bind_param("ssi",$icpid,$currentdate,$old_checkin_id['id']);
                    if($update_old_checkin_stmt->execute())
                    {
                        $select_new_checkin = "select * from " . TABLE_CHECKIN . " as checkin left join (select * from ". TABLE_BUSINESS .") as business on checkin.business_id = business.id  where checkin.id = ?";
                        //echo $select_new_checkin;exit;
                        $select_new_checkin_stmt = $connection->prepare($select_new_checkin);
                        $select_new_checkin_stmt->bind_param("i",$old_checkin_id['id']);
                        $select_new_checkin_stmt->execute();
                        $select_new_checkin_stmt->store_result();

                        $posts[] = fetch_assoc_all_values($select_new_checkin_stmt);

//                        //update checkin count
//                        $update_checkin_query = "update " . TABLE_BUSINESS . " business set business.checkin = business.checkin  + 1 where  business.id = ?";
//                        $update_checkin_stmt = $connection->prepare($update_checkin_query);
//                        $update_checkin_stmt->bind_param("i",$businessid);
//                        $update_checkin_stmt->execute();

                        $status = 1;
                        $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                        $data['message'] = "Checkin successfully";
                        $data['isNewCheckIn'] = "0";
                        $data['checkin'] = $posts;
                        return $data;
                    }
                    else
                    {
                        $status = 1;
                        $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                        $data['message'] = "Please try again!!!";
                        $data['checkin'] = $posts;
                        return $data;
                    }
                }
                else
                {
//                    $update_old_checkin = "update " . TABLE_CHECKIN . " set is_checked_in = ?  where id = ?";
//                    $update_old_checkin_stmt = $connection->prepare($update_old_checkin);
//                    $ischeckin = 0;
//                    $update_old_checkin_stmt->bind_param("ii",$ischeckin,$old_checkin_id['id']);
//
//                    if($update_old_checkin_stmt->execute())
//                    {

                    $ischeckin = 1;
                    $currentdate = date("Y-m-d H:i:s");

                    $insert_query = "insert into ". TABLE_CHECKIN ." (user_id,business_id,icp_id,is_checked_in,modified ) values ('" . $userid . "','" .$businessid . "','"
                        . $icpid ."','" .$ischeckin ."','" .$currentdate ."')";
                    //echo $insert_query;
                    //exit;
                    $insert_query_stmt = $connection->prepare($insert_query);

//                        $insert_query_stmt->bind_param("iisis",$userid,$businessid,$icpid,$ischeckin,$currentdate);

                    if($insert_query_stmt->execute())
                    {
                        $checkin_inserted_id = mysqli_insert_id($connection);

                        $select_new_checkin = "select * from " . TABLE_CHECKIN . " as checkin left join (select * from ". TABLE_BUSINESS .") as business on checkin.business_id = business.id  where checkin.id = ?";
                        $select_new_checkin_stmt = $connection->prepare($select_new_checkin);
                        $select_new_checkin_stmt->bind_param("i",$checkin_inserted_id);
                        $select_new_checkin_stmt->execute();
                        $select_new_checkin_stmt->store_result();

                        $posts[] = fetch_assoc_all_values($select_new_checkin_stmt);

                        //update checkin count
//                        $update_checkin_query = "update " . TABLE_BUSINESS . " business set business.checkin = business.checkin  + 1 where  business.id = ?";
//                        $update_checkin_stmt = $connection->prepare($update_checkin_query);
//                        $update_checkin_stmt->bind_param("i",$businessid);
//                        $update_checkin_stmt->execute();

                        $status = 1;
                        $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                        $data['isNewCheckIn'] = "0";
                        $data['message'] = "Checkin successfully";
                        $data['checkin'] = $posts;
                        return $data;

                    }
                    else
                    {
                        $status = 2;
                        $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                        $data['message'] = "Please try again";
                        $data['checkin'] = $posts;
                        return $data;
                    }
                    //  }
//                    else
//                    {
//                        $status = 2;
//                        $data['status'] = ($status > 1) ? FAILED : SUCCESS;
//                        $data['message'] = "Please try again";
//                        $data['checkin'] = $posts;
//                        return $data;
//                    }
                }
            }
            else
            {

                $insertFields = "user_id,
                             business_id,
                             icp_id,
                             is_checked_in,
                             modified";

                $valuesFields = "?,?,?,?";
//                $insert_query = "Insert into " . TABLE_CHECKIN . " (" . $insertFields . ") values(" . $valuesFields . ")";

                $ischeckin = 1;
                $currentdate = date("Y-m-d H:i:s");
                //$insert_query_stmt->bind_param("iiiis",$userid,$businessid,$icpid,$ischeckin,$currentdate);
                $insert_query = "insert into ". TABLE_CHECKIN ." (user_id,business_id,icp_id,is_checked_in,modified ) values ('" . $userid . "','" .$businessid . "','"
                    . $icpid ."','" .$ischeckin ."','" .$currentdate ."')";

//                echo $insert_query;
//                exit;

                $insert_query_stmt = $connection->prepare($insert_query);
                if($insert_query_stmt->execute())
                {
                    $checkin_inserted_id = mysqli_insert_id($connection);

                    $select_new_checkin = "select * from " . TABLE_CHECKIN . " as checkin left join (select * from ". TABLE_BUSINESS .")
                    as business on checkin.business_id = business.id  where checkin.id = ?";

                    $select_new_checkin_stmt = $connection->prepare($select_new_checkin);
                    $select_new_checkin_stmt->bind_param("i",$checkin_inserted_id);
                    $select_new_checkin_stmt->execute();
                    $select_new_checkin_stmt->store_result();

                    $posts[] = fetch_assoc_all_values($select_new_checkin_stmt);

                    //update checkin count
                    $update_checkin_query = "update " . TABLE_BUSINESS . " business set business.checkin = business.checkin  + 1 where  business.id = ?";
                    $update_checkin_stmt = $connection->prepare($update_checkin_query);
                    $update_checkin_stmt->bind_param("i",$businessid);
                    $update_checkin_stmt->execute();

                    $status = 1;
                    $data['isNewCheckIn'] = "1";
                    $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                    $data['message'] = "Checkin successfully";
                    $data['checkin'] = $posts;
                    return $data;

                }
                else
                {

                    $status = 2;
                    $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                    $data['message'] = "Please try again";
                    $data['checkin'] = $posts;
                    return $data;
                }
            }

        }
        else
        {
            $status = 2;
            $data['status'] = ($status > 1) ? FAILED : SUCCESS;
            $data['message'] = "Please try again";
            $data['checkin'] = $posts;
            return $data;
        }


    }

    public function checkInBusiness1($businessData)
    {
        $connection = $GLOBALS['con'];
        $status = 2;
        $posts = array();
        $errorMsg = "Service responce data";

        $userid = validateObject($businessData, 'userId', "");
        $userid = addslashes($userid);

        $businessid = validateObject($businessData, 'businessid', "");
        $businessid = addslashes($businessid);

        $icpid = validateObject($businessData, 'icpid', "");
        $icpid = addslashes($icpid);

        //Select old one
        $select_old_checkin = "select id,business_id from " . TABLE_CHECKIN . " where is_checked_in = ? and user_id = ?";
        $select_old_checkin_stmt = $connection->prepare($select_old_checkin);
        $ischeckin = 1;
        $select_old_checkin_stmt->bind_param("ii",$ischeckin,$userid);

        if($select_old_checkin_stmt->execute()) {
            $select_old_checkin_stmt->store_result();

            if($select_old_checkin_stmt -> num_rows > 0)
            {
                $old_checkin_id = fetch_assoc_all_values($select_old_checkin_stmt);
                //echo $old_checkin_id['business_id'];exit;
                if($old_checkin_id['business_id'] == $businessid)
                {
                    //echo "1";exit;
                    $update_old_checkin = "update " . TABLE_CHECKIN . " set icp_id = ? , modified = ?  where id = ?";
                    $update_old_checkin_stmt = $connection->prepare($update_old_checkin);
                    $currentdate = date("Y-m-d H:i:s");
                    $update_old_checkin_stmt->bind_param("ssi",$icpid,$currentdate,$old_checkin_id['id']);
                    if($update_old_checkin_stmt->execute())
                    {
                        $select_new_checkin = "select * from " . TABLE_CHECKIN . " as checkin left join (select * from ".
                            TABLE_BUSINESS .") as business on checkin.business_id = business.id  where checkin.id = ?";
//                        echo $select_new_checkin;exit;
                        $select_new_checkin_stmt = $connection->prepare($select_new_checkin);
                        $select_new_checkin_stmt->bind_param("i",$old_checkin_id['id']);
                        $select_new_checkin_stmt->execute();
                        $select_new_checkin_stmt->store_result();

                        $posts[] = fetch_assoc_all_values($select_new_checkin_stmt);

                        //update checkin count
                        $update_checkin_query = "update " . TABLE_BUSINESS . " business set business.checkin = business.checkin  + 1 where  business.id = ?";
                        $update_checkin_stmt = $connection->prepare($update_checkin_query);
                        $update_checkin_stmt->bind_param("i",$businessid);
                        $update_checkin_stmt->execute();

                        $status = 1;
                        $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                        $data['message'] = "Checkin successfully";
                        $data['checkin'] = $posts;
                        return $data;
                    }
                    else
                    {
                        $status = 1;
                        $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                        $data['message'] = "Please try again!!!";
                        $data['checkin'] = $posts;
                        return $data;
                    }
                }
//                else
//                {
//                    $update_old_checkin = "update " . TABLE_CHECKIN . " set is_checked_in = ?  where id = ?";
//                    $update_old_checkin_stmt = $connection->prepare($update_old_checkin);
//                    $ischeckin = 0;
//                    $update_old_checkin_stmt->bind_param("ii",$ischeckin,$old_checkin_id['id']);
//
//                    if($update_old_checkin_stmt->execute())
//                    {
//
//                        $ischeckin = 1;
//                        $currentdate = date("Y-m-d H:i:s");
//
//                        $insert_query = "insert into ". TABLE_CHECKIN ." (user_id,business_id,icp_id,is_checked_in,modified ) values ('" . $userid . "','" .$businessid . "','"
//                        . $icpid ."','" .$ischeckin ."','" .$currentdate ."')";
//                        //echo $insert_query;
//                        //exit;
//                        $insert_query_stmt = $connection->prepare($insert_query);
//
////                        $insert_query_stmt->bind_param("iisis",$userid,$businessid,$icpid,$ischeckin,$currentdate);
//
//                        if($insert_query_stmt->execute())
//                        {
//                            $checkin_inserted_id = mysqli_insert_id($connection);
//
//                            $select_new_checkin = "select * from " . TABLE_CHECKIN . " as checkin left join (select * from ". TABLE_BUSINESS .") as business on checkin.business_id = business.id  where checkin.id = ?";
//                            $select_new_checkin_stmt = $connection->prepare($select_new_checkin);
//                            $select_new_checkin_stmt->bind_param("i",$checkin_inserted_id);
//                            $select_new_checkin_stmt->execute();
//                            $select_new_checkin_stmt->store_result();
//
//                            $posts[] = fetch_assoc_all_values($select_new_checkin_stmt);
//
//                            //update checkin count
//                            $update_checkin_query = "update " . TABLE_BUSINESS . " business set business.checkin = business.checkin  + 1 where  business.id = ?";
//                            $update_checkin_stmt = $connection->prepare($update_checkin_query);
//                            $update_checkin_stmt->bind_param("i",$businessid);
//                            $update_checkin_stmt->execute();
//
//                            $status = 1;
//                            $data['status'] = ($status > 1) ? FAILED : SUCCESS;
//                            $data['message'] = "Checkin successfully";
//                            $data['checkin'] = $posts;
//                            return $data;
//
//                        }
//                        else
//                        {
//                            $status = 2;
//                            $data['status'] = ($status > 1) ? FAILED : SUCCESS;
//                            $data['message'] = "Please try again";
//                            $data['checkin'] = $posts;
//                            return $data;
//                        }
//                    }
//                    else
//                    {
//                        $status = 2;
//                        $data['status'] = ($status > 1) ? FAILED : SUCCESS;
//                        $data['message'] = "Please try again";
//                        $data['checkin'] = $posts;
//                        return $data;
//                    }
//                }
            }
            else
            {
                $insertFields = "user_id,
                             business_id,
                             icp_id,
                             is_checked_in,
                             modified";

                $valuesFields = "?,?,?,?,?";
//                $insert_query = "Insert into " . TABLE_CHECKIN . " (" . $insertFields . ") values(" . $valuesFields . ")";

                $ischeckin = 1;
                $currentdate = date("Y-m-d H:i:s");
                //$insert_query_stmt->bind_param("iiiis",$userid,$businessid,$icpid,$ischeckin,$currentdate);
                $insert_query = "insert into ". TABLE_CHECKIN ." (user_id,business_id,icp_id,is_checked_in,modified ) values ('" . $userid . "','" .$businessid . "','"
                    . $icpid ."','" .$ischeckin ."','" .$currentdate ."')";
                $insert_query_stmt = $connection->prepare($insert_query);
                if($insert_query_stmt->execute())
                {
                    $checkin_inserted_id = mysqli_insert_id($connection);

                    $select_new_checkin = "select * from " . TABLE_CHECKIN . " as checkin left join (select * from ". TABLE_BUSINESS .") as business on checkin.business_id = business.id  where checkin.id = ?";
                    $select_new_checkin_stmt = $connection->prepare($select_new_checkin);
                    $select_new_checkin_stmt->bind_param("i",$checkin_inserted_id);
                    $select_new_checkin_stmt->execute();
                    $select_new_checkin_stmt->store_result();

                    $posts[] = fetch_assoc_all_values($select_new_checkin_stmt);

                    $status = 1;
                    $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                    $data['message'] = "Checkin successfully";
                    $data['checkin'] = $posts;
                    return $data;

                }
                else
                {
                    $status = 2;
                    $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                    $data['message'] = "Please try again";
                    $data['checkin'] = $posts;
                    return $data;

                }
            }

        }
        else
        {
            $status = 2;
            $data['status'] = ($status > 1) ? FAILED : SUCCESS;
            $data['message'] = "Please try again";
            $data['checkin'] = $posts;
            return $data;
        }


    }

    public function searchBusiness($businessData)
    {
        $connection = $GLOBALS['con'];
        $status = 2;
        $posts = array();
        $errorMsg = "Service responce data";

        $businessname  = validateObject($businessData, 'businessname', "");
        $businessname = addslashes($businessname);

        $userid = validateObject($businessData, 'userId', "");
        $userid = addslashes($userid);

        $select_business = "Select * from " . TABLE_BUSINESS . " where name LIKE ? and is_delete = ?
                            union
                            select * from ". TABLE_BUSINESS ." where id in (select business_id from ". TABLE_ICPS ." where name like ?) and is_delete = ? ";

        $select_business_stmt = $connection->prepare($select_business);
        $searchKey = "%{$businessname}%";
        $isdelete = 0;
        $select_business_stmt->bind_param("sisi",$searchKey,$isdelete,$searchKey,$isdelete);

        if($select_business_stmt->execute())
        {
            $select_business_stmt->store_result();
            if($select_business_stmt->num_rows > 0)
            {
                while($business = fetch_assoc_all_values($select_business_stmt))
                {

                    //check is like
                    $select_business_likes = "Select * from " . TABLE_LIKES ." where businessid = ? and userid = ?";
                    $select_business_likes = $connection->prepare($select_business_likes);
                    $select_business_likes->bind_param("ii",$business['id'],$userid);
                    $select_business_likes->execute();
                    $select_business_likes->store_result();
                    if($select_business_likes->num_rows > 0)
                    {
                        $businesslikes = fetch_assoc_all_values($select_business_likes);
                        $business['islike'] = $businesslikes['islike'];
                    }
                    else
                    {
                        $business['islike'] = "0";
                    }

                    //check is favorite
                    $select_business_favorite = "Select * from " . TABLE_FAVORITES ." where businessid = ? and userid = ?";
                    $select_business_favorite = $connection->prepare($select_business_favorite);
                    $select_business_favorite->bind_param("ii",$business['id'],$userid);
                    $select_business_favorite->execute();
                    $select_business_favorite->store_result();
                    if($select_business_favorite->num_rows > 0)
                    {
                        $businessfavorites = fetch_assoc_all_values($select_business_favorite);
                        $business['isfavorite'] = $businessfavorites['isfavorite'];
                    }
                    else
                    {
                        $business['isfavorite'] = "0";
                    }

                    $posts[] = $business;
                }

                $status = 1;
                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                $data['message'] = "List of business !!!";
                $data['business'] = $posts;
                return $data;
            }
            else
            {
                $status = 1;
                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                $data['message'] = "Business not found !!!";
                $data['business'] = $posts;
                return $data;
            }
        }
        else
        {
            $status = 2;
            $data['status'] = ($status > 1) ? FAILED : SUCCESS;
            $data['message'] = "Please try again";
            $data['business'] = $posts;
            return $data;
        }
    }


    public function getAllBusiness($businessData)
    {
        $connection = $GLOBALS['con'];
        $status = 2;
        $posts = array();
        $errorMsg = "Service responce data";

        $select_business = "Select * from " . TABLE_BUSINESS ." where is_delete = ?";

        $select_business_stmt = $connection->prepare($select_business);
        $isdelete = 0;
        $select_business_stmt->bind_param("i",$isdelete);

        if($select_business_stmt->execute())
        {
            $select_business_stmt->store_result();
            if($select_business_stmt->num_rows > 0)
            {
                while($business = fetch_assoc_all_values($select_business_stmt))
                {
                    $posts[] = $business;
                }
                $status = 1;
                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                $data['message'] = "List of business !!!";
                $data['business'] = $posts;
                return $data;
            }
            else
            {
                $status = 1;
                $data['status'] = ($status > 1) ? FAILED : SUCCESS;
                $data['message'] = "Business not found !!!";
                $data['business'] = $posts;
                return $data;
            }
        }
        else
        {
            $status = 2;
            $data['status'] = ($status > 1) ? FAILED : SUCCESS;
            $data['message'] = "Please try again";
            $data['business'] = $posts;
            return $data;
        }

    }
}