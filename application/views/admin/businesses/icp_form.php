<style>
    .img-wrap{position:relative;}
    .img-wrap .delete {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 30px;
        height: 30px;
        line-height: 32px;
        font-size: 10px !important;
        text-align: center;
        background: rgba(0,0,0,0.8);
        color: #fff;
        /* border: solid 1px #ccc; */
        border-radius: 33px;}
    .img-wrap .delete i{font-size: 12px;}
    .img-wrap .delete:hover{background:rgba(0,0,0,1);}

    .preview_images
    {
        position:absolute;
        top: 0px;
        left: 0px;
    }
    #preview_photo_img1
    {
        z-index: 10;
    }
    #preview_photo_img2
    {
        z-index: 20;
    }
    #preview_photo_div{
        width: 90px;
    }
    #preview_photo_div +  .media-body{
        width: 86%;
    }
    @media(max-width:767px){
        .icp_logo_wrapper .uploader,
        .preview_photo_div_wrapper .uploader{
            width: 82%;
            display: block;
        }
        .icp_logo_wrapper .filename,.preview_photo_div_wrapper .filename{    
            display: block;
        }
        .icp_logo_wrapper  .action,.preview_photo_div_wrapper  .action{    
            position: absolute;
            right: 0;
            top: 0;
        }
    }
    @media(max-width:767px){
        .icp_logo_wrapper .media-body{
            display: block;
            width: 100%;
            margin-top: 10px;
        }
        .preview_photo_div_wrapper .media-body{
            display: block;
            width: 100%;
            margin-top: 10px;
        }
        .preview_photo_div_wrapper .media-left #preview_photo_img1 {
            position: relative;
        }

    }
    @media(max-width:380px){
        .icp_logo_wrapper .uploader,
        .preview_photo_div_wrapper .uploader{
            width: 97%;
            display: block;
        }#preview_photo_div +  .media-body{
            width: 100%;
        }
    }
    .hidden {
        display: none;
    }

    .img-export {
        display: block;
    }
    .js-editorcanvas, .js-export.img-export{
        display: none;
    }
    .btn_img_crop{
        border:2px solid #166dba;
        color:#166dba;
        padding:10px 30px;
        top:10px;
        font-weight: 500;
    }
    .btn_img_crop:hover{
        background-color: #166dba;
        color:#fff;
        text-decoration: none;
    }
</style>
<script type="text/javascript" src="assets/admin/js/plugins/forms/validation/validate.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/forms/inputs/touchspin.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/forms/selects/select2.min.js"></script>
<script type="text/javascript" src="assets/admin/js/pages/form_validation.js"></script>
<!--<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false&libraries=geometry,places&v=3.7&key=AIzaSyBR_zVH9ks9bWwA-8AzQQyD6mkawsfF9AI"></script>-->
<script type="text/javascript" src="http://maps.google.com/maps/api/js?libraries=geometry,places&key=AIzaSyBR_zVH9ks9bWwA-8AzQQyD6mkawsfF9AI"></script>
<script type="text/javascript" src="assets/admin/js/plugins/uploaders/fileinput.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/notifications/sweet_alert.min.js"></script>
<div class="page-header page-header-default">
    <div class="page-header-content">
        <div class="page-title">
            <h4>
                <?php
                if (isset($icp_data))
                    echo '<i class="icon-pencil3"></i>';
                else
                    echo '<i class="icon-plus-circle2"></i>';
                ?> 
                <span class="text-semibold"><?php echo $heading; ?></span>
            </h4>
        </div>
    </div>
    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="<?php echo site_url('admin/home'); ?>"><i class="icon-home2 position-left"></i> Home</a></li>
            <li><a href="<?php echo site_url('admin/businesses'); ?>"><i class="icon-office"></i> Businesses</a></li>
            <li><a href="<?php echo site_url('admin/businesses/icps/' . $business_data['id']); ?>"><i class="icon-lan2"></i> ICPS</a></li>
            <li class="active"><?php echo $heading; ?></li>
        </ul>
    </div>
</div>
<div class="content">
    <div class="row">
        <div class="col-md-12">
            <?php
            if ($this->session->flashdata('success')) {
                ?>
                <div class="alert alert-success hide-msg">
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Close</span></button>
                    <strong><?php echo $this->session->flashdata('success') ?></strong>
                </div>
            <?php } ?>
            <?php if ($this->session->flashdata('error')) {
                ?>
                <div class="alert alert-danger hide-msg">
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Close</span></button>
                    <strong><?php echo $this->session->flashdata('error') ?></strong>
                </div>
            <?php } ?>
        </div>
    </div>
    <div class="panel panel-flat">
        <div class="panel-body">
            <form class="form-horizontal form-validate-jquery" action="" id="icp_info" method="post" enctype="multipart/form-data" onsubmit="return validateForm();">
                <div class="form-group">
                    <label class="control-label col-lg-3">ICP Logo
                        <!--<span class="text-danger">*</span>-->
                    </label>
                    <div class="col-lg-5">
                        <div class="media no-margin-top icp_logo_wrapper">
                            <div class="media-left" id="image_preview_div">
                                <?php
                                if (isset($icp_data) && $icp_data['icp_logo'] != '') {
                                    $required = '';
                                    ?>
                                    <img src="<?php echo ICP_LOGO . $icp_data['icp_logo'] ?>" style="width: 58px; height: 58px; border-radius: 2px;" alt="">
                                    <?php
                                } else {
                                    $required = 'required="required"';
                                    ?>
                                    <img src="assets/admin/images/placeholder.jpg" style="width: 58px; height: 58px; border-radius: 2px;" alt="">
                                <?php } ?>
                            </div>
                            <div class="media-body">
                                
                                <input type="file" class="file-styled js-fileinput img-upload" accept="image/jpeg,image/png,image/gif">
                                    <span class="help-block">Accepted formats: png, jpg. Max file size 2Mb</span>
                                    <canvas class="js-editorcanvas"></canvas>
                                    <canvas class="js-previewcanvas" style="display: none;"></canvas><br>
                                    <a href="javascript:void(0);" class="js-export img-export btn_img_crop btn">Crop</a><br><br>
                                    <input type="hidden" name="cropimg" id="cropimg" value="">
                            </div>
                        </div>
                        <?php
                        if (isset($icp_logo_validation))
                            echo '<label id="icp_logo-error" class="validation-error-label" for="icp_logo">' . $icp_logo_validation . '</label>';
                        ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-3 control-label">ICP Name
                        <!--<span class="text-danger">*</span>-->
                    </label>
                    <div class="col-lg-5">
                        <!--<input type="text" name="name" id="name" placeholder="Enter ICP Name" required="required" class="form-control" value="<?php echo (isset($icp_data)) ? $icp_data['name'] : set_value('name'); ?>">-->
                        <input type="text" name="name" id="name" placeholder="Enter ICP Name" class="form-control" value="<?php echo (isset($icp_data)) ? $icp_data['name'] : set_value('name'); ?>">
                        <?php
                        echo '<label id="name-error" class="validation-error-label" for="name">' . form_error('name') . '</label>';
                        ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-3 control-label">Description of the ICP  <a data-popup="popover-custom" data-trigger="hover" data-placement="top" data-content="Describe about ICP in 160 max characters"><i class="icon-question4"></i></a>
                        <!--<span class="text-danger">*</span>-->
                    </label>
                    <div class="col-lg-5">
                        <!--<textarea name="description" id="description" placeholder="Enter Description of ICP" required="required" class="form-control" rows="4"><?php echo (isset($icp_data)) ? $icp_data['description'] : set_value('description'); ?></textarea>-->
                        <textarea name="description" id="description" placeholder="Enter Description of ICP" class="form-control" rows="4"><?php echo (isset($icp_data)) ? $icp_data['description'] : set_value('description'); ?></textarea>
                        <?php
                        echo '<label id="description-error" class="validation-error-label" for="description">' . form_error('description') . '</label>';
                        ?>
                        <span id="spn-description-error" class="validation-error-label"></span>
                    </div>
                </div>
                <?php
                $unique_location_checked = '';
                $location_div_style = 'style="display:none"';
                $lat_lng_div_style = 'style="display:none"';
                if (isset($icp_data) && $icp_data['address'] != NULL) {
                    $unique_location_checked = 'checked="checked"';
                    $location_div_style = '';
                    $lat_lng_div_style = '';
                }
                ?>
                <div class="form-group">
                    <label class="col-lg-3 control-label">Set unique location for ICP?  <a data-popup="popover-custom" data-trigger="hover" data-placement="top" data-content="If ICP has different location then set uniqe location"><i class="icon-question4"></i></a>
                        <!--<span class="text-danger">*</span>-->
                    </label>
                    <div class="col-lg-5">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" class="styled" name="unique_location_for_icp" id="unique_location_for_icp" <?php echo $unique_location_checked ?>>
                            </label>
                        </div>
                    </div>
                </div>
                <div id="location-div" <?php echo $location_div_style ?>>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">Location
                            <!--<span class="text-danger">*</span>-->
                        </label>
                        <div class="col-lg-5">
                            <!--<input type="text" name="address" id="address" class="form-control" required="required" placeholder="Address" value="<?php echo (isset($icp_data)) ? $icp_data['address'] : set_value('address'); ?>">-->
                            <input type="text" name="address" id="address" class="form-control" placeholder="Address" value="<?php echo (isset($icp_data)) ? $icp_data['address'] : set_value('address'); ?>">
                            <?php
                            echo '<label id="address-error" class="validation-error-label" for="address">' . form_error('address') . '</label>';
                            ?>
                            <span id="spn-address-error" class="validation-error-label"></span>
                        </div>
                    </div>
                    <div class="form-group" id="mapContainer">
                        <div id="map-canvas" style="height:350px;display: none"></div>
                    </div>
                    <div class="form-group" id="lat-lng-div" <?php echo $lat_lng_div_style ?>>
                        <div class="col-sm-6">
                            <input class="form-control" type="text" placeholder="Latitude" id="latitude" name="latitude" value="<?php echo (isset($icp_data['latitude'])) ? $icp_data['latitude'] : set_value('latitude'); ?>" readonly>
                            <?php
                            echo '<label id="latitude-error" class="validation-error-label" for="latitude">' . form_error('latitude') . '</label>';
                            ?>
                        </div>
                        <div class="col-sm-6">
                            <input class="form-control" type="text" placeholder="Longitude" id="longitude" name="longitude" value="<?php echo (isset($icp_data['longitude'])) ? $icp_data['longitude'] : set_value('longitude'); ?>" readonly>
                            <?php
                            echo '<label id="longitude-error" class="validation-error-label" for="longitude">' . form_error('longitude') . '</label>';
                            ?>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-3 control-label">Upload the logo to be displayed on the photo (Preview photo?)</label>
                    <div class="col-lg-5">
                        <div class="media no-margin-top preview_photo_div_wrapper">
                            <div class="media-left" id="preview_photo_div">
                                <?php
                                if (isset($icp_data) && $icp_data['preview_photo'] != '') {
                                    ?>
                                    <img src="assets/admin/images/placeholder.jpg" style="width: 58px; height: 58px; border-radius: 2px;" alt="" id="preview_photo_img1" class="preview_images">
                                    <img src="<?php echo ICP_PREVIEW_IMAGES . $icp_data['preview_photo'] ?>" style="width: 58px; height: 58px; border-radius: 2px;" alt="" id="preview_photo_img2" class="preview_images">
                                    <?php
                                } else {
                                    ?>
                                    <img src="assets/admin/images/placeholder.jpg" style="width: 58px; height: 58px; border-radius: 2px;" alt="" class="preview_images" id="preview_photo_img1">
                                <?php } ?>
                            </div>
                            <div class="media-body">
                                <input type="file" name="preview_photo" id="preview_photo" class="previewfile-styled" onchange="readpreview_photo(this);" >
                                <span class="help-block">Accepted formats: png, jpg. Max file size 2Mb</span>
                            </div>
                            <div style="opacity: 0;height: 0;">
                                <canvas id="canvas1" width="58" height="58" border="0"></canvas>
                            </div>
                        </div>
                        <?php
                        if (isset($preview_photo_validation))
                            echo '<label id="preview_photo-error" class="validation-error-label" for="preview_photo">' . $preview_photo_validation . '</label>';
                        ?>
                        <span id="spn-preview_photo-error" class="validation-error-label"></span>
                    </div>
                </div>
                <?php
                $addlogo_to_sharedimage_checked = '';
                $addlogo_to_sharedimage_style = 'style="display:none"';

                if (isset($icp_data) && $icp_data['preview_photo'] != NULL) {
                    if (isset($icp_data) && $icp_data['addlogo_to_sharedimage'] == 1) {
                        $addlogo_to_sharedimage_checked = 'checked="checked"';
                    }
                    $addlogo_to_sharedimage_style = '';
                }
                ?>
                <div class="form-group" <?php echo $addlogo_to_sharedimage_style ?> id="addlogo_to_sharedimage_div">
                    <label class="col-lg-3 control-label">Add the logo to every image shared on social media?</label>
                    <div class="col-lg-4">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" class="styled" name="addlogo_to_sharedimage" id="addlogo_to_sharedimage" <?php echo $addlogo_to_sharedimage_checked ?>>
                            </label>
                        </div>
                    </div>
                </div>
                <fieldset class="content-group">
                    <legend class="text-bold">Set purchase options and prices</legend>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">Low resolution version (webpic) Price
                            <!--<span class="text-danger">*</span>-->
                        </label>
                        <div class="col-lg-3">
                            <!--<input type="text" name="low_resolution_price" class="form-control" required="required" placeholder="Low Resolution/Web Pic Price" value="<?php echo (isset($icp_data)) ? $icp_data['low_resolution_price'] : set_value('low_resolution_price'); ?>">-->
                            <input type="text" name="low_resolution_price" id="low_resolution_price" class="form-control" placeholder="Low Resolution/Web Pic Price" value="<?php echo (isset($icp_data)) ? $icp_data['low_resolution_price'] : set_value('low_resolution_price'); ?>">
                            <?php
                            echo '<label id="low_resolution_price-error" class="validation-error-label" for="low_resolution_price">' . form_error('low_resolution_price') . '</label>';
                            ?>
                            <span id="spn-low_resolution_price-error" class="validation-error-label"></span>
                        </div>
                        <label class="col-lg-2 control-label">OR offer this image FREE</label>
                        <div class="col-lg-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" class="styled" name="is_low_image_free" id="is_low_image_free" <?php echo (isset($icp_data) && $icp_data['is_low_image_free'] == 1) ? 'checked="checked"' : '' ?>>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">High resolution version (printable)
                            <!--<span class="text-danger">*</span>-->
                        </label>
                        <div class="col-lg-3">
                            <!--<input type="text" name="high_resolution_price" class="form-control" required="required" placeholder="High Resolution/Printable Version Price" value="<?php echo (isset($icp_data)) ? $icp_data['high_resolution_price'] : set_value('high_resolution_price'); ?>">-->
                            <input type="text" name="high_resolution_price" id="high_resolution_price" class="form-control" placeholder="High Resolution/Printable Version Price" value="<?php echo (isset($icp_data)) ? $icp_data['high_resolution_price'] : set_value('high_resolution_price'); ?>">
                            <?php
                            echo '<label id="high_resolution_price-error" class="validation-error-label" for="high_resolution_price">' . form_error('high_resolution_price') . '</label>';
                            ?>
                            <span id="spn-high_resolution_price-error" class="validation-error-label"></span>
                        </div>
                        <label class="col-lg-2 control-label">OR offer this image FREE</label>
                        <div class="col-lg-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" class="styled" name="is_high_image_free" id="is_high_image_free" <?php echo (isset($icp_data) && $icp_data['is_high_image_free'] == 1) ? 'checked="checked"' : '' ?>>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-6 control-label">If the Guest buy's the high resolution version, do you want to include the low resolution version FREE?</label>
                        <div class="col-lg-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" class="styled" name="lowfree_on_highpurchase" id="lowfree_on_highpurchase" <?php echo (isset($icp_data) && $icp_data['lowfree_on_highpurchase'] == 1) ? 'checked="checked"' : '' ?>/>
                                </label>
                            </div>
                        </div>
                    </div>
                    <?php
                    $style = 'style="display:none"';
                    $checked = '';
                    if (isset($printed_souvenir_image_validation)) {
                        $style = '';
                        $checked = 'checked="checked"';
                    } else if ((isset($icp_data) && $icp_data['offer_printed_souvenir'] == 1) || !empty(form_error('printed_souvenir_price'))) {
                        $style = '';
                        $checked = 'checked="checked"';
                    }
                    ?>
                    <div class="form-group">
                        <label class="col-lg-6 control-label">Do you want to offer a physical printed souvenir product for this ICP?
                            <!--<span class="text-danger">*</span>-->
                        </label>
                        <div class="col-lg-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" class="styled" name="offer_printed_souvenir" id="offer_printed_souvenir" <?php echo $checked ?>>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div id="printed_souvenir_price_div" <?php echo $style ?>>
                        <div class="form-group">
                            <label class="col-lg-3 control-label">Printed Souvenir Price
                                <!--<span class="text-danger">*</span>-->
                            </label>
                            <div class="col-lg-4">
                                <input type="text" name="printed_souvenir_price" id="printed_souvenir_price" class="form-control" placeholder="Printed Souvenir Price" value="<?php echo (isset($icp_data)) ? $icp_data['printed_souvenir_price'] : set_value('printed_souvenir_price'); ?>">
                                <?php
                                echo '<label id="printed_souvenir_price-error" class="validation-error-label" for="printed_souvenir_price">' . form_error('printed_souvenir_price') . '</label>';
                                ?>
                                <span id="spn-printed_souvenir_price-error" class="validation-error-label"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label">Upload images of the physical product</label>
                            <div class="col-lg-6">

                                <input type="file" class="file-input" multiple="multiple" name="printed_souvenir_images[]">
                                <!--<span class="help-block">Automatically convert a file input to a bootstrap file input widget by setting its class as <code>file-input</code>.</span>-->
                                <?php
                                if (isset($printed_souvenir_image_validation))
                                    echo '<label id="printed_souvenir_images-error" class="validation-error-label" for="printed_souvenir_images">' . $printed_souvenir_image_validation . '</label>';
                                ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-lg-6 col-lg-offset-3"> 
                                <?php
                                if (isset($icp_data) && $physical_product_images) {
                                    foreach ($physical_product_images as $val) {
                                        echo '<div class="file-preview-frame img-wrap" id="file_preview_image_' . $val['id'] . '"><img src="' . base_url() . ICP_PHYSICAL_PRODUCT_IMAGES . $val['image'] . '" class="file-preview-image" alt="" width="160px"><a href="javascript:void(0)" data-href="' . site_url() . 'admin/businesses/delete_physical_product_image/' . $val['id'] . '" class="delete" data-id="file_preview_image_' . $val['id'] . '"  onclick="return confirm_alert(this)" title="Delete Image"><i class="icon-trash"></i></a></div>';
                                    }
                                }
                                ?>
                            </div>
                        </div>
                        <fieldset class="content-group">
                            <legend class="text-bold">How do you want your visitors to receive this product?</legend>
                            <div class="form-group">
                                <div class="col-lg-4">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" class="styled" name="collection_point_delivery" id="collection_point_delivery" <?php echo (isset($icp_data) && $icp_data['collection_point_delivery'] == 1) ? 'checked="checked"' : '' ?>>
                                            Collection Point
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div id="collection_point_delivery_div" <?php echo ((isset($icp_data) && $icp_data['collection_point_delivery'] == 1)) ? '' : 'style="display:none"' ?>>
                                <div class="form-group">
                                    <label class="col-lg-2 col-lg-offset-1 control-label">Enter Location</label>
                                    <div class="col-lg-6">
                                        <input type="text" name="collection_address" id="collection_address" class="form-control" placeholder="Enter Location" value="<?php echo (isset($icp_data)) ? $icp_data['collection_address'] : set_value('collection_address'); ?>">
                                        <?php
                                        echo '<label id="collection_address-error" class="validation-error-label" for="collection_address">' . form_error('collection_address') . '</label>';
                                        ?>
                                        <span id="spn-collection_address-error" class="validation-error-label"></span>
                                    </div>
                                </div>
                                <div class="form-group" id="collectionmapContainer">
                                    <div id="collectionmap-canvas" class="col-lg-offset-1 col-lg-8" style="height:350px;display: none"></div>
                                </div>
                                <div class="form-group" id="collection_latlngdiv">
                                    <div class="col-sm-4 col-lg-offset-1">
                                        <input class="form-control" type="text" placeholder="Latitude" id="collection_address_latitude" name="collection_address_latitude" value="<?php echo (isset($icp_data['collection_address_latitude'])) ? $icp_data['collection_address_latitude'] : set_value('collection_address_latitude'); ?>" readonly>
                                        <?php
                                        echo '<label id="collection_address_latitude-error" class="validation-error-label" for="collection_address_latitude">' . form_error('collection_address_latitude') . '</label>';
                                        ?>
                                    </div>
                                    <div class="col-sm-4">
                                        <input class="form-control" type="text" placeholder="Longitude" id="collection_address_longitude" name="collection_address_longitude" value="<?php echo (isset($icp_data['collection_address_longitude'])) ? $icp_data['collection_address_longitude'] : set_value('collection_address_longitude'); ?>" readonly>
                                        <?php
                                        echo '<label id="collection_address_longitude-error" class="validation-error-label" for="collection_address_longitude">' . form_error('collection_address_longitude') . '</label>';
                                        ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-2 col-lg-offset-1 control-label">Enter Directions/Instructions</label>
                                    <div class="col-lg-6">
                                        <textarea name="collection_address_instructions" id="collection_address_instructions" class="form-control" placeholder="Enter Description/Instructions"  rows="4"><?php echo (isset($icp_data)) ? $icp_data['collection_address_instructions'] : set_value('collection_address_instructions'); ?></textarea>
                                        <?php
                                        echo '<label id="collection_address_instructions-error" class="validation-error-label" for="collection_address_instructions">' . form_error('collection_address_instructions') . '</label>';
                                        ?>
                                        <span id="spn-collection_address_instructions-error" class="validation-error-label"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-lg-4">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" class="styled" name="local_hotel_delivery" id="local_hotel_delivery" <?php echo (isset($icp_data) && $icp_data['local_hotel_delivery'] == 1) ? 'checked="checked"' : '' ?>>
                                            Local Hotel Delivery
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div id="local_hotel_delivery_div" <?php echo ((isset($icp_data) && $icp_data['local_hotel_delivery'] == 1)) ? '' : 'style="display:none"' ?>>
                                <div class="form-group">
                                    <label class="col-lg-1 col-lg-offset-1 control-label">FREE</label>
                                    <div class="col-lg-4">
                                        <label class="radio-inline">
                                            <input type="radio" name="local_hotel_delivery_free" id="local_hotel_delivery_free" class="local_hotel_delivery_free styled" value="1" <?php
                                            if (isset($icp_data) && $icp_data['local_hotel_delivery_free'] == 1)
                                                echo 'checked';
                                            else
                                                echo 'checked';
                                            ?>>
                                            Yes
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="local_hotel_delivery_free" id="local_hotel_delivery_free" class="local_hotel_delivery_free styled" value="0" <?php if (isset($icp_data) && $icp_data['local_hotel_delivery_free'] == 0) echo 'checked' ?>>
                                            No
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group" id="local_hotel_delivery_free_div" <?php echo ((isset($icp_data) && $icp_data['local_hotel_delivery_free'] == 0)) ? '' : 'style="display:none"' ?>>
                                    <label class="col-lg-1 col-lg-offset-1 control-label">Delivery fee</label>
                                    <div class="col-lg-4">
                                        <input type="text" name="local_hotel_delivery_price" id="local_hotel_delivery_price" class="form-control" placeholder="Enter delivery fee" value="<?php echo (isset($icp_data)) ? $icp_data['local_hotel_delivery_price'] : set_value('local_hotel_delivery_price'); ?>">
                                        <span id="spn-local_hotel_delivery_price-error" class="validation-error-label"></span>
                                    </div>
                                </div>
                                <?php if (isset($icp_data)) { ?>
                                    <div class="form-group">
                                        <div class="col-lg-offset-1">
                                            <!--<button type="button" class="btn bg-teal-400 btn-labeled" data-toggle="modal" data-target="#modal_default"><b><i class="icon-office"></i></b> Manage Hotels</button>-->
                                            <!--<a href="<?php echo site_url('admin/hotels/index/' . $icp_data['id']) ?>" class="btn bg-teal-400 btn-labeled" target="_blank"><b><i class="icon-city"></i></b> Manage Hotels</a>-->
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="form-group">
                                <div class="col-lg-4">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" class="styled" name="domestic_shipping" id="domestic_shipping" <?php echo (isset($icp_data) && $icp_data['domestic_shipping'] == 1) ? 'checked="checked"' : '' ?>>
                                            Domestic shipping
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div id="domestic_shipping_div" <?php echo ((isset($icp_data) && $icp_data['domestic_shipping'] == 1)) ? '' : 'style="display:none"' ?>>
                                <div class="form-group">
                                    <label class="col-lg-1 col-lg-offset-1 control-label">FREE</label>
                                    <div class="col-lg-4">
                                        <label class="radio-inline">
                                            <input type="radio" name="domestic_shipping_free" id="domestic_shipping_free" class="styled" value="1" <?php
                                            if (isset($icp_data) && $icp_data['domestic_shipping_free'] == 1)
                                                echo 'checked';
                                            else
                                                echo 'checked';
                                            ?>>
                                            Yes
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="domestic_shipping_free" id="domestic_shipping_free" class="styled" value="0" <?php if (isset($icp_data) && $icp_data['domestic_shipping_free'] == 0) echo 'checked' ?>>
                                            No
                                        </label>
                                        <?php
                                        echo '<label id="domestic_shipping_free-error" class="validation-error-label" for="domestic_shipping_free">' . form_error('domestic_shipping_free') . '</label>';
                                        ?>
                                    </div>
                                </div>
                                <div class="form-group" id="domestic_shipping_free_div" <?php echo ((isset($icp_data) && $icp_data['domestic_shipping_free'] == 0)) ? '' : 'style="display:none"' ?>>
                                    <label class="col-lg-1 col-lg-offset-1 control-label">Delivery fee</label>
                                    <div class="col-lg-4">
                                        <input type="text" name="domestic_shipping_price" id="domestic_shipping_price" class="form-control" placeholder="Enter delivery fee" value="<?php echo (isset($icp_data)) ? $icp_data['domestic_shipping_price'] : set_value('domestic_shipping_price'); ?>">
                                        <span id="spn-domestic_shipping_price-error" class="validation-error-label"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-lg-4">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" class="styled" name="international_shipping" id="international_shipping" <?php echo (isset($icp_data) && $icp_data['international_shipping'] == 1) ? 'checked="checked"' : '' ?>>
                                            International Shipping
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div id="international_shipping_div" <?php echo ((isset($icp_data) && $icp_data['international_shipping'] == 1)) ? '' : 'style="display:none"' ?>>
                                <div class="form-group">
                                    <label class="col-lg-1 col-lg-offset-1 control-label">FREE</label>
                                    <div class="col-lg-4">
                                        <label class="radio-inline">
                                            <input type="radio" name="international_shipping_free" id="international_shipping_free" class="styled" value="1" <?php
                                            if (isset($icp_data) && $icp_data['international_shipping_free'] == 1)
                                                echo 'checked';
                                            else
                                                echo 'checked';
                                            ?>>
                                            Yes
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="international_shipping_free" id="international_shipping_free" class="styled" value="0" <?php if (isset($icp_data) && $icp_data['international_shipping_free'] == 0) echo 'checked' ?>>
                                            No
                                        </label>
                                        <?php
                                        echo '<label id="international_shipping_free-error" class="validation-error-label" for="international_shipping_free">' . form_error('international_shipping_free') . '</label>';
                                        ?>
                                    </div>
                                </div>
                                <div class="form-group" id="international_shipping_free_div" <?php echo ((isset($icp_data) && $icp_data['international_shipping_free'] == 0)) ? '' : 'style="display:none"' ?>>
                                    <label class="col-lg-1 control-label col-lg-offset-1">Delivery fee</label>
                                    <div class="col-lg-4">
                                        <input type="text" name="international_shipping_price" id="international_shipping_price" class="form-control" placeholder="Enter delivery fee" value="<?php echo (isset($icp_data)) ? $icp_data['international_shipping_price'] : set_value('international_shipping_price'); ?>">
                                        <span id="spn-international_shipping_price-error" class="validation-error-label"></span>
                                    </div>
                                </div>
                            </div>
                            <span id="spn-purchase_options_and_prices-error" class="validation-error-label"></span>
                        </fieldset>
                    </div>
                </fieldset>
                <fieldset class="content-group">
                    <?php
                    $is_image_timelimited_not_checked = 'checked';
                    $is_image_timelimited_checked = '';
                    if (isset($icp_data)) {
                        if ($icp_data['is_image_timelimited'] == 1) {
                            $is_image_timelimited_not_checked = '';
                            $is_image_timelimited_checked = 'checked';
                        }
                    }
                    ?>
                    <legend class="text-bold">Image availability time limit</legend>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">Do you want to offer a time limit for users to buy this image once a match has been verified? </label>
                        <div class="col-lg-6">
                            <label class="radio-inline">
                                <input type="radio" name="is_image_timelimited" id="is_image_timelimited" class="styled" value="1" <?php echo $is_image_timelimited_checked ?>>
                                Yes
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="is_image_timelimited" id="is_image_timelimited" class="styled" value="0" <?php echo $is_image_timelimited_not_checked ?>>
                                No
                            </label>
                        </div>
                    </div>
                    <div class="form-group" id="is_image_timelimited_div" <?php echo ((isset($icp_data) && $icp_data['is_image_timelimited'] == 1)) ? '' : 'style="display:none"' ?>>
                        <label class="col-lg-3 control-label">Select Time Limit</label>
                        <div class="col-lg-4">
                            <select name="image_availabilty_time_limit" id="image_availabilty_time_limit" class="select">
                                <option value="24" <?php echo (isset($icp_data) && ($icp_data['image_availabilty_time_limit'] == '24')) ? 'selected' : '' ?>>24 Hrs</option>
                                <option value="48" <?php echo (isset($icp_data) && ($icp_data['image_availabilty_time_limit'] == '48')) ? 'selected' : '' ?>>48 Hrs</option>
                                <option value="168" <?php echo (isset($icp_data) && ($icp_data['image_availabilty_time_limit'] == '168')) ? 'selected' : '' ?>>1 Week</option>
                                <option value="720" <?php echo (isset($icp_data) && ($icp_data['image_availabilty_time_limit'] == '720')) ? 'selected' : '' ?>>1 Month</option>
                            </select>
                        </div>
                    </div>
                </fieldset>
                <fieldset>
                    <?php
                    $allow_manual_search_not_checked = 'checked';
                    $allow_manual_search_checked = '';
                    if (isset($icp_data)) {
                        if ($icp_data['allow_manual_search'] == 1) {
                            $allow_manual_search_not_checked = '';
                            $allow_manual_search_checked = 'checked';
                        }
                    }
                    ?>
                    <legend class="text-bold">Settings and permissions for your Visitors to Manually Search your image database for this ICP  <a data-popup="popover-custom" data-trigger="hover" data-placement="top" data-content="Disclaimer: You may be in breach of privacy laws in relation to this function depending on the nature of your business and/or the jurisdiction you operate. You acknowledge this and warrant you have obtained suitable legal advice and indemnify Facetag Pty Ltd from any claims and action brought by the use of this feature"><i class="icon-question4"></i></a>
                    </legend>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">Do you wish to allow your Visitors to manually search the images for this ICP?  </label>
                        <div class="col-lg-6">
                            <label class="radio-inline">
                                <input type="radio" name="allow_manual_search" id="allow_manual_search" class="styled" value="1" <?php echo $allow_manual_search_checked ?>>
                                Yes
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="allow_manual_search" id="allow_manual_search" class="styled" value="0" <?php echo $allow_manual_search_not_checked ?>>
                                No
                            </label>
                        </div>
                    </div>
                    <div id="allow_manual_search_div" <?php echo ((isset($icp_data) && $icp_data['allow_manual_search'] == 1)) ? '' : 'style="display:none"' ?>>
                        <div class="form-group">
                            <div class="col-lg-6">
                                <div class="checkbox">
                                    <label>
                                        <input type="radio" class="styled allow_manual_search_for_date" name="allow_manual_search_for_date" <?php echo (isset($icp_data) && $icp_data['allow_manual_search_for_date'] == 1) ? 'checked="checked"' : 'checked="checked"' ?> value="1">
                                        Only for the date the User 'checks in' while physically at your venue?
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-lg-6">
                                <div class="checkbox">
                                    <label>
                                        <input type="radio" class="styled allow_manual_search_for_date" name="allow_manual_search_for_date" <?php echo (isset($icp_data) && $icp_data['allow_manual_search_for_date'] == 2) ? 'checked="checked"' : '' ?> value="2">
                                        Allow Users to check-in remotely after their visit and search images for up to 7 days prior?
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>
                <div>
                    <button class="btn btn-success" type="submit" id="btn_submit">Save <i class="icon-arrow-right14 position-right"></i></button>
                </div>
            </form>
        </div>
    </div>
    <?php $this->load->view('Templates/footer'); ?>
</div>
<!-- Manage Hotels modal -->
<div id="modal_default" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h5 class="modal-title">Manage Hotels for Local Hotel Delivery</h5>
            </div>
            <div class="modal-body">
                <form id="add_hotel_form" method="post">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Hotel Name</th>
                                <th>Hotel Address</th>
                                <!--<th>Action</th>-->
                            </tr>
                        </thead>
                        <tbody>
<!--                            <tr>
                                <td>#</td>
                                <td><input type="text" name="hotel_name" placeholder="Enter Hotel Name" class="form-control" required="required"/></td>
                                <td><input type="text" name="hotel_address" placeholder="Enter Hotel Address" class="form-control" required="required"/></td>
                                <td>
                                    <textarea name="hotel_address" placeholder="Enter Hotel Address" class="form-control" required="required"></textarea>
                                <td>
                                    <ul class='icons-list'>
                                        <li class='text-success-600'><a href='javascript:void(0)' title="Add Hotel" onclick="addHotel();"><i class='icon-plus-circle2'></i></a></li>
                                    </ul>
                                </td>
                            </tr>-->
                            <?php
                            if ($hotels) {
                                $i = 1;
                                foreach ($hotels as $val) {
                                    echo "<tr>
                                    <td>" . $i . "</td>
                                    <td>" . $val['name'] . "</td>
                                    <td>" . $val['address'] . "</td>";
//                                    echo "<td>
//                                        <ul class='icons-list'>
//                                            <li class='text-primary-600'><a href='javascript:void(0)' title='Edit Hotel'><i class='icon-pencil7'></i></a></li>
//                                            <li class='text-danger-600'><a href='javascript:void(0)' title='Delete Hotel'><i class='icon-trash'></i></a></li>
//                                        </ul>
//                                    </td>";
                                    echo "</tr>";
                                    $i++;
                                }
                            } else {
                                echo "<tr><td colspan='4'><center>No hotels have been entered!</center></td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Manage Hotels modal -->
<script>
                                /**
                                 * @file Allows uploading, cropping (with automatic resizing) and exporting
                                 * of images.
                                 * @author Billy Brown
                                 * @license MIT
                                 * @version 2.1.0
                                 */

                                /** Class used for uploading images. */
                                class Uploader {
                                    /**
                                     * <p>Creates an Uploader instance with parameters passed as an object.</p>
                                     * <p>Available parameters are:</p>
                                     * <ul>
                                     *  <li>exceptions {function}: the exceptions handler to use, function that takes a string.</li>
                                     *  <li>input {HTMLElement} (required): the file input element. Instantiation fails if not provided.</li>
                                     *  <li>types {array}: the file types accepted by the uploader.</li>
                                     * </ul>
                                     *
                                     * @example
                                     * var uploader = new Uploader({
                                     *  input: document.querySelector('.js-fileinput'),
                                     *  types: [ 'gif', 'jpg', 'jpeg', 'png' ]
                                     * });
                                     * *
                                     * @param {object} options the parameters to be passed for instantiation
                                     */
                                    constructor(options) {
                                        if (!options.input) {
                                            throw '[Uploader] Missing input file element.';
                                        }
                                        this.fileInput = options.input;
                                        this.types = options.types || ['gif', 'jpg', 'jpeg', 'png'];
                                    }

                                    /**
                                     * Listen for an image file to be uploaded, then validate it and resolve with the image data.
                                     */
                                    listen(resolve, reject) {
                                        this.fileInput.onchange = (e) => {
                                            // Do not submit the form
                                            e.preventDefault();

                                            // Make sure one file was selected
                                            if (!this.fileInput.files || this.fileInput.files.length !== 1) {
                                                reject('[Uploader:listen] Select only one file.');
                                            }

                                            let file = this.fileInput.files[0];
                                            let reader = new FileReader();
                                            // Make sure the file is of the correct type
                                            if (!this.validFileType(file.type)) {
                                                reject(`[Uploader:listen] Invalid file type: ${file.type}`);
                                            } else {
                                                // Read the image as base64 data
                                                reader.readAsDataURL(file);
                                                // When loaded, return the file data
                                                reader.onload = (e) => resolve(e.target.result);
                                            }
                                        };
                                    }

                                    /** @private */
                                    validFileType(filename) {
                                        // Get the second part of the MIME type
                                        let extension = filename.split('/').pop().toLowerCase();
                                        // See if it is in the array of allowed types
                                        return this.types.includes(extension);
                                    }
                                }

                                function squareContains(square, coordinate) {
                                    return coordinate.x >= square.pos.x
                                            && coordinate.x <= square.pos.x + square.size.x
                                            && coordinate.y >= square.pos.y
                                            && coordinate.y <= square.pos.y + square.size.y;
                                }

                                /** Class for cropping an image. */
                                class Cropper {
                                    /**
                                     * <p>Creates a Cropper instance with parameters passed as an object.</p>
                                     * <p>Available parameters are:</p>
                                     * <ul>
                                     *  <li>size {object} (required): the dimensions of the cropped, resized image. Must have 'width' and 'height' fields. </li>
                                     *  <li>limit {integer}: the longest side that the cropping area will be limited to, resizing any larger images.</li>
                                     *  <li>canvas {HTMLElement} (required): the cropping canvas element. Instantiation fails if not provided.</li>
                                     *  <li>preview {HTMLElement} (required): the preview canvas element. Instantiation fails if not provided.</li>
                                     * </ul>
                                     *
                                     * @example
                                     * var editor = new Cropper({
                                     *  size: { width: 128, height: 128 },
                                     *  limit: 600,
                                     *  canvas: document.querySelector('.js-editorcanvas'),
                                     *  preview: document.querySelector('.js-previewcanvas')
                                     * });
                                     *
                                     * @param {object} options the parameters to be passed for instantiation
                                     */
                                    constructor(options) {
                                        // Check the inputs
                                        if (!options.size) {
                                            throw 'Size field in options is required';
                                        }
                                        if (!options.canvas) {
                                            throw 'Could not find image canvas element.';
                                        }
                                        if (!options.preview) {
                                            throw 'Could not find preview canvas element.';
                                        }

                                        // Hold on to the values
                                        this.imageCanvas = options.canvas;
                                        this.previewCanvas = options.preview;
                                        this.c = this.imageCanvas.getContext("2d");


                                        // Images larger than options.limit are resized
                                        this.limit = options.limit || 600; // default to 600px
                                        // Create the cropping square with the handle's size
                                        this.crop = {
                                            size: {x: options.size.width, y: options.size.height},
                                            pos: {x: 0, y: 0},
                                            handleSize: 10
                                        };

                                        // Set the preview canvas size
                                        this.previewCanvas.width = options.size.width;
                                        this.previewCanvas.height = options.size.height;

                                        // Bind the methods, ready to be added and removed as events
                                        this.boundDrag = this.drag.bind(this);
                                        this.boundClickStop = this.clickStop.bind(this);
                                    }

                                    /**
                                     * Set the source image data for the cropper.
                                     *
                                     * @param {String} source the source of the image to crop.
                                     */
                                    setImageSource(source) {
                                        this.image = new Image();
                                        this.image.src = source;
                                        this.image.onload = (e) => {
                                            // Perform an initial render
                                            this.render();
                                            // Listen for events on the canvas when the image is ready
                                            this.imageCanvas.onmousedown = this.clickStart.bind(this);
                                        }
                                    }

                                    /**
                                     * Export the result to a given image tag.
                                     *
                                     * @param {HTMLElement} img the image tag to export the result to.
                                     */
                                    export(img) {
//                                        img.setAttribute('src', this.previewCanvas.toDataURL());
                                        $("#cropimg").val(this.previewCanvas.toDataURL());
                                        var html = '<img src="' + this.previewCanvas.toDataURL() + '" style="width: 58px; height: 58px; border-radius: 2px;" alt="">';
                                        $('#image_preview_div').html(html);
                                    }

                                    /** @private */
                                    render() {
                                        this.c.clearRect(0, 0, this.imageCanvas.width, this.imageCanvas.height);
                                        this.displayImage();
                                        this.preview();
                                        this.drawCropWindow();
                                        $('.js-editorcanvas').show();
                                        $('.js-export.img-export').show();
                                    }

                                    /** @private */
                                    clickStart(e) {
                                        // Get the click position and hold onto it for the expected mousemove
                                        const position = {x: e.offsetX, y: e.offsetY};
                                        this.lastEvent = {
                                            position: position,
                                            resizing: this.isResizing(position),
                                            moving: this.isMoving(position)
                                        };
                                        // Listen for mouse movement and mouse release
                                        this.imageCanvas.addEventListener('mousemove', this.boundDrag);
                                        this.imageCanvas.addEventListener('mouseup', this.boundClickStop);
                                    }

                                    /** @private */
                                    clickStop(e) {
                                        // Stop listening for mouse movement and mouse release
                                        this.imageCanvas.removeEventListener("mousemove", this.boundDrag);
                                        this.imageCanvas.removeEventListener("mouseup", this.boundClickStop);
                                    }

                                    /** @private */
                                    isResizing(coord) {
                                        const size = this.crop.handleSize;
                                        const handle = {
                                            pos: {
                                                x: this.crop.pos.x + this.crop.size.x - size / 2,
                                                y: this.crop.pos.y + this.crop.size.y - size / 2
                                            },
                                            size: {x: size, y: size}
                                        };
                                        return squareContains(handle, coord);
                                    }

                                    /** @private */
                                    isMoving(coord) {
                                        return squareContains(this.crop, coord);
                                    }

                                    /** @private */
                                    drag(e) {
                                        const position = {
                                            x: e.offsetX,
                                            y: e.offsetY
                                        };
                                        // Calculate the distance that the mouse has travelled
                                        const dx = position.x - this.lastEvent.position.x;
                                        const dy = position.y - this.lastEvent.position.y;
                                        // Determine whether we are resizing, moving, or nothing
                                        if (this.lastEvent.resizing) {
                                            this.resize(dx, dy);
                                        } else if (this.lastEvent.moving) {
                                            this.move(dx, dy);
                                        }
                                        // Update the last position
                                        this.lastEvent.position = position;
                                        this.render();
                                    }

                                    /** @private */
                                    resize(dx, dy) {
                                        let handle = {
                                            x: this.crop.pos.x + this.crop.size.x,
                                            y: this.crop.pos.y + this.crop.size.y
                                        };
                                        // Maintain the aspect ratio
                                        const amount = Math.abs(dx) > Math.abs(dy) ? dx : dy;
                                        // Make sure that the handle remains within image bounds
                                        if (this.inBounds(handle.x + amount, handle.y + amount)) {
                                            this.crop.size.x += amount;
                                            this.crop.size.y += amount;
                                        }
                                    }

                                    /** @private */
                                    move(dx, dy) {
                                        // Get the opposing coordinates
                                        const tl = {
                                            x: this.crop.pos.x,
                                            y: this.crop.pos.y
                                        };
                                        const br = {
                                            x: this.crop.pos.x + this.crop.size.x,
                                            y: this.crop.pos.y + this.crop.size.y
                                        };
                                        // Make sure they are in bounds
                                        if (this.inBounds(tl.x + dx, tl.y + dy) &&
                                                this.inBounds(br.x + dx, tl.y + dy) &&
                                                this.inBounds(br.x + dx, br.y + dy) &&
                                                this.inBounds(tl.x + dx, br.y + dy)) {
                                            this.crop.pos.x += dx;
                                            this.crop.pos.y += dy;
                                        }
                                    }

                                    /** @private */
                                    displayImage() {
                                        // Resize the original to the maximum allowed size
                                        const ratio = this.limit / Math.max(this.image.width, this.image.height);
                                        this.image.width *= ratio;
                                        this.image.height *= ratio;
                                        // Fit the image to the canvas
                                        this.imageCanvas.width = this.image.width;
                                        this.imageCanvas.height = this.image.height;
                                        this.c.drawImage(this.image, 0, 0, this.image.width, this.image.height);
                                    }

                                    /** @private */
                                    drawCropWindow() {
                                        const pos = this.crop.pos;
                                        const size = this.crop.size;
                                        const radius = this.crop.handleSize / 2;
                                        this.c.strokeStyle = 'red';
                                        this.c.fillStyle = 'red';
                                        // Draw the crop window outline
                                        this.c.strokeRect(pos.x, pos.y, size.x, size.y);
                                        // Draw the draggable handle
                                        const path = new Path2D();
                                        path.arc(pos.x + size.x, pos.y + size.y, radius, 0, Math.PI * 2, true);
//                                        path.arc(100,75,50,0,2*Math.PI);
                                        this.c.fill(path);
                                    }

                                    /** @private */
                                    preview() {
                                        const pos = this.crop.pos;
                                        const size = this.crop.size;
                                        // Fetch the image data from the canvas
                                        const imageData = this.c.getImageData(pos.x, pos.y, size.x, size.y);
                                        if (!imageData) {
                                            return false;
                                        }
                                        // Prepare and clear the preview canvas
                                        const ctx = this.previewCanvas.getContext('2d');
                                        ctx.clearRect(0, 0, this.previewCanvas.width, this.previewCanvas.height);
                                        // Draw the image to the preview canvas, resizing it to fit
                                        ctx.drawImage(this.imageCanvas,
                                                // Top left corner coordinates of image
                                                pos.x, pos.y,
                                                // Width and height of image
                                                size.x, size.y,
                                                // Top left corner coordinates of result in canvas
                                                0, 0,
                                                // Width and height of result in canvas
                                                this.previewCanvas.width, this.previewCanvas.height);
                                    }

                                    /** @private */
                                    inBounds(x, y) {
                                        return squareContains({
                                            pos: {x: 0, y: 0},
                                            size: {
                                                x: this.imageCanvas.width,
                                                y: this.imageCanvas.height
                                            }
                                        }, {x: x, y: y});
                                    }
                                }

                                function exceptionHandler(message) {
                                    console.log(message);
                                }

// Auto-resize the cropped image
                                var dimensions = {width: 128, height: 128};

                                try {
                                    var uploader = new Uploader({
                                        input: document.querySelector('.js-fileinput'),
                                        types: ['gif', 'jpg', 'jpeg', 'png']
                                    });

                                    var editor = new Cropper({
                                        size: dimensions,
                                        canvas: document.querySelector('.js-editorcanvas'),
                                        preview: document.querySelector('.js-previewcanvas')
                                    });

                                    // Make sure both were initialised correctly
                                    if (uploader && editor) {
                                        // Start the uploader, which will launch the editor
                                        uploader.listen(editor.setImageSource.bind(editor), (error) => {
                                            throw error;
                                        });
                                    }
                                    // Allow the result to be exported as an actual image
                                    var img = document.createElement('img');
                                    document.body.appendChild(img);
                                    document.querySelector('.js-export').onclick = (e) => editor.export(img);

                                } catch (error) {
                                    exceptionHandler(error.message);
                                }
</script>

<script type="text/javascript">
    var img1 = new Image();
    var canvas1 = document.getElementById("canvas1");
    var ctx1 = canvas1.getContext("2d");
    var valid_preview_image = 0;
    // Custom color
    $('[data-popup=popover-custom]').popover({
        template: '<div class="popover border-teal-400"><div class="arrow"></div><h3 class="popover-title bg-teal-400"></h3><div class="popover-content"></div></div>'
    });

    // File input
    $(".previewfile-styled").uniform({
        fileButtonClass: 'action btn bg-pink-400'
    });
    // Display the preview of image on image upload
    function readpreview_photo(input) {
        $('#addlogo_to_sharedimage_div').show();
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                var html = '<img src="assets/admin/images/placeholder.jpg" style="width: 58px; height: 58px; border-radius: 2px;" alt="" id="preview_photo_img1" class="preview_images"><img src="' + e.target.result + '" style="width: 58px; height: 58px; border-radius: 2px;" alt="" id="preview_photo_img2" class="preview_images">';
                $('#preview_photo_div').html(html);
                img1.onload = start1;
                img1.src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    function start1() {
        canvas1.width = img1.width;
        canvas1.height = img1.height;

        ctx1.drawImage(img1, 0, 0);
        var imgData = ctx1.getImageData(0, 0, canvas1.width, canvas1.height);
        var data = imgData.data;
        var found1 = 'Left canvas does not have transparency';
        for (var i = 0; i < data.length; i += 4) {
            if (data[i + 3] < 255) {
                found1 = 'Left canvas does have transparency';
                valid_preview_image = 1;
            }
        }
        console.log(found1);
    }

    var infowindow = new google.maps.InfoWindow();
    var map = new google.maps.Map(document.getElementById("map-canvas"));

<?php if (isset($icp_data) && $icp_data['latitude'] != '' && $icp_data['longitude'] != '') { ?>
        lat = '<?php echo $icp_data['latitude'] ?>';
        long = '<?php echo $icp_data['longitude'] ?>';
        generateMap(lat, long);
<?php } ?>
    // Display the preview of image on image upload
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                var html = '<img src="' + e.target.result + '" style="width: 58px; height: 58px; border-radius: 2px;" alt="">';
                $('#image_preview_div').html(html);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    //-- Set unique location checkbox selection
    $("#unique_location_for_icp").change(function () {
        if ($(this).is(':checked')) {
            $('#location-div').show();
        } else {
            $('#location-div').hide();
        }
    });
    //-- Distribute checkbox selection
    $("#offer_printed_souvenir").change(function () {
        if ($(this).is(':checked')) {
            $('#printed_souvenir_price_div').show();
        } else {
            $('#printed_souvenir_price_div').hide();
        }
    });

    //Google auotocomplete for business address
    google.maps.event.addDomListener(window, 'load', function () {
        var options = {
            types: ['establishment'],
        };
        var businessAddress = new google.maps.places.Autocomplete(document.getElementById('address'), options);
        google.maps.event.addListener(businessAddress, 'place_changed', function () {
            var place = businessAddress.getPlace();
            var address = place.formatted_address;
            /*Use to get latitude and longitude */

            var latitude = place.geometry.location.lat();
            var longitude = place.geometry.location.lng();

            var mesg = "Address: " + address;
            mesg += "\nLatitude: " + latitude;
            mesg += "\nLongitude: " + longitude;

            business_address = $("#address1_en").val();
            $("#latitude").val(latitude);
            $("#longitude").val(longitude);
            generateMap(latitude, longitude);
        });
    });
    //Generates the map from latitude and longitude
    function generateMap(latitude, longitude) {
        $('#map-canvas').show();
        $('#lat-lng-div').show();
        var latlngPos = new google.maps.LatLng(latitude, longitude);
        //var infowindow = new google.maps.InfoWindow();
        var geocoder = new google.maps.Geocoder();
        map = new google.maps.Map(document.getElementById("map-canvas"), {
            center: new google.maps.LatLng(latitude, longitude),
            zoom: 13,
            mapTypeId: 'roadmap'
        });
        marker = new google.maps.Marker({
            map: map,
            position: latlngPos,
            draggable: true
        });
        geocoder.geocode({'latLng': latlngPos}, function (results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                if (results[0]) {
                    infowindow.setContent(results[0].formatted_address);
                    infowindow.open(map, marker);
                }
            }
        });
        google.maps.event.addListener(marker, 'dragend', function () {
            geocoder.geocode({
                latLng: marker.getPosition()
            }, function (responses) {
                if (responses && responses.length > 0) {

                    $('#address').val(responses[0].formatted_address);
                    infowindow.setContent(responses[0].formatted_address);
                    $("#latitude").val(marker.getPosition().lat());
                    $("#longitude").val(marker.getPosition().lng());
                    //updateMarkerAddress(responses[0].formatted_address);
                } else {
                    $('#address').val('');
                    infowindow.setContent('');

                    $("#latitude").val('');
                    $("#longitude").val('');
                    //updateMarkerAddress('Cannot determine address at this location.');
                }
            });
        });

    }
    //--Resize the map on according to browser window
    $(window).resize(function () {
        google.maps.event.trigger(map, 'resize');
    });

    // Basic example
    $('.file-input').fileinput({
        browseLabel: 'Browse',
        browseIcon: '<i class="icon-file-plus"></i>',
        uploadIcon: '<i class="icon-file-upload2"></i>',
        removeIcon: '<i class="icon-cross3"></i>',
        layoutTemplates: {
            icon: '<i class="icon-file-check"></i>'
        },
        initialCaption: "No file selected",
        maxFilesNum: 10,
        maxFileSize: 2048,
        overwriteInitial: true,
        allowedFileExtensions: ["jpg", "jpeg", "gif", "png"],
        showUpload: false,
    });

    var collectionmap = new google.maps.Map(document.getElementById("collectionmap-canvas"));
<?php if (isset($icp_data) && $icp_data['collection_address_latitude'] != '' && $icp_data['collection_address_longitude'] != '') { ?>
        collectionlat = '<?php echo $icp_data['collection_address_latitude'] ?>';
        collectionlong = '<?php echo $icp_data['collection_address_longitude'] ?>';
        generateCollectionMap(collectionlat, collectionlong);
<?php } ?>
    //-- Delivery checkbox selection
    $("#collection_point_delivery,#local_hotel_delivery,#domestic_shipping,#international_shipping").change(function () {
        var div_id = $(this).attr('id');
        if ($(this).is(':checked')) {
            $('#' + div_id + '_div').show();
        } else {
            $('#' + div_id + '_div').hide();
        }
    });
    //-- Yes no radio button selection of time limit and manual image search
    $("#is_image_timelimited,#allow_manual_search").change(function () {
        var div_id = $(this).attr('id');
        if ($(this).val() == 1) {
            $('#' + div_id + '_div').show();
        } else {
            $('#' + div_id + '_div').hide();
        }
    });
    //-- Yes no radio button of local hotel,Domestic, International Shipping delievery free radio buttons
    $("#local_hotel_delivery_free,#domestic_shipping_free,#international_shipping_free").change(function () {
        var div_id = $(this).attr('id');
        if ($(this).val() == 0) {
            $('#' + div_id + '_div').show();
        } else {
            $('#' + div_id + '_div').hide();
        }
    });
    //Google auotocomplete for business address
    google.maps.event.addDomListener(window, 'load', function () {
        var options = {
            types: ['establishment'],
        };
        var collection_address = new google.maps.places.Autocomplete(document.getElementById('collection_address'), options);
        google.maps.event.addListener(collection_address, 'place_changed', function () {
            var place = collection_address.getPlace();

            /*Use to get latitude and longitude */
            var latitude = place.geometry.location.lat();
            var longitude = place.geometry.location.lng();

            $("#collection_address_latitude").val(latitude);
            $("#collection_address_longitude").val(longitude);
            generateCollectionMap(latitude, longitude);
        });
    });
    //Generates the map from latitude and longitude
    function generateCollectionMap(latitude, longitude) {
        $('#collectionmap-canvas').show();
        $('#collection-latlngdiv').show();
        var latlngPos = new google.maps.LatLng(latitude, longitude);
        //var infowindow = new google.maps.InfoWindow();
        var geocoder = new google.maps.Geocoder();
        collectionmap = new google.maps.Map(document.getElementById("collectionmap-canvas"), {
            center: new google.maps.LatLng(latitude, longitude),
            zoom: 13,
            mapTypeId: 'roadmap'
        });
        collectionmarker = new google.maps.Marker({
            map: collectionmap,
            position: latlngPos,
            draggable: true
        });
        geocoder.geocode({'latLng': latlngPos}, function (results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                if (results[0]) {
                    infowindow.setContent(results[0].formatted_address);
                    infowindow.open(collectionmap, collectionmarker);
                }
            }
        });
        google.maps.event.addListener(collectionmarker, 'dragend', function () {
            geocoder.geocode({
                latLng: collectionmarker.getPosition()
            }, function (responses) {
                if (responses && responses.length > 0) {
                    $('#collection_address').val(responses[0].formatted_address);
                    infowindow.setContent(responses[0].formatted_address);
                    $("#collection_address_latitude").val(collectionmarker.getPosition().lat());
                    $("#collection_address_longitude").val(collectionmarker.getPosition().lng());
                } else {
                    $('#collection_address').val('');
                    infowindow.setContent('');
                    $("#collection_address_latitude").val('');
                    $("#collection_address_longitude").val('');
                }
            });
        });

    }
    //--Resize the map on according to browser window
    $(window).resize(function () {
        google.maps.event.trigger(collectionmap, 'resize');
    });

    //--validates form before submit
    function validateForm() {
        var flag = 0;
        if ($('#description').val() != '' && $('#description').val().length > 160) {
            $('#spn-description-error').html('Description can not be more than 160 characters!');
            $('#description').focus();
            //            flag = 1;
            return false;
        } else {
            $('#spn-description-error').html('');
        }
        if ($('#unique_location_for_icp').is(':checked') && ($('#latitude').val() == '' || $('#longitude').val() == '')) {
            $('#spn-address-error').html('Please enter valid address!');
            $('#address').focus();
//            flag = 1;
            return false;
        } else {
            $('#spn-address-error').html('');
        }

        if ($('#preview_photo').val() != '') {
            if (valid_preview_image == 0) {
                $('#spn-preview_photo-error').html('Preview image should be transparent. Please upload transparent image!');
                $('#preview_photo').focus();
//            flag = 1;
                return false;
            }
        } else {
            $('#spn-preview_photo-error').html('');
        }
        if ((!$('#is_low_image_free').is(':checked')) && (!validatePrice($('#low_resolution_price').val()))) {
            $('#spn-low_resolution_price-error').html('Please enter valid low resolution version (webpic) Price!');
            $('#low_resolution_price').focus();
//            flag = 1;
            return false;
        } else {
            $('#spn-low_resolution_price-error').html('');
        }
        if ((!$('#is_high_image_free').is(':checked')) && (!validatePrice($('#high_resolution_price').val()))) {
            $('#spn-high_resolution_price-error').html('Please enter valid high resolution version (printable) Price!');
            $('#high_resolution_price').focus();
//            flag = 1;
            return false;
        } else {
            $('#spn-high_resolution_price-error').html('');
        }

        if (($('#offer_printed_souvenir').is(':checked')) && (!validatePrice($('#printed_souvenir_price').val()))) {
            $('#spn-printed_souvenir_price-error').html('Please enter valid price for Printed Souvenir!');
            $('#printed_souvenir_price').focus();
//            flag = 1;
            return false;
        } else {
            $('#spn-printed_souvenir_price-error').html('');
        }
        if ($('#offer_printed_souvenir').is(':checked')) {
            if ((!$('#collection_point_delivery').is(':checked')) && (!$('#local_hotel_delivery').is(':checked')) && (!$('#domestic_shipping').is(':checked')) && (!$('#international_shipping').is(':checked'))) {
                $('#spn-purchase_options_and_prices-error').html('Please select any of the above option for product delivery!');
                $('#collection_point_delivery').focus();
//            flag = 1;
                return false;
            } else {
                $('#spn-purchase_options_and_prices-errorr').html('');
            }
            if (($('#collection_point_delivery').is(':checked')) && ($('#collection_address').val() == '') && ($('#collection_address_latitude').val() == '') && ($('#collection_address_longitude').val() == '')) {
                $('#spn-collection_address-error').html('Please enter valid collection location!');
                $('#collection_address').focus();
//            flag = 1;
                return false;
            } else {
                $('#spn-collection_address-error').html('');
            }
            if ($('#collection_point_delivery').is(':checked') && $('#collection_address_instructions').val() == '') {
                $('#spn-collection_address_instructions-error').html('Please enter valid collection location!');
                $('#collection_address_instructions').focus();
//            flag = 1;
                return false;
            } else {
                $('#spn-collection_address_instructions-error').html('');
            }
            if (($('#local_hotel_delivery').is(':checked')) && ($('input[name="local_hotel_delivery_free"]:checked').val() == 0) && (!validatePrice($('#local_hotel_delivery_price').val()))) {
                $('#spn-local_hotel_delivery_price-error').html('Please enter valid price for local hotel delivery!');
                $('#local_hotel_delivery_price').focus();
//            flag = 1;
                return false;
            } else {
                $('#spn-local_hotel_delivery_price-error').html('');
            }
            if (($('#domestic_shipping').is(':checked')) && ($('input[name="domestic_shipping_free"]:checked').val() == 0) && (!validatePrice($('#domestic_shipping_price').val()))) {
                $('#spn-domestic_shipping_price-error').html('Please enter valid price for Domestic shipping!');
                $('#domestic_shipping_price').focus();
//            flag = 1;
                return false;
            } else {
                $('#spn-domestic_shipping_price-error').html('');
            }
            if (($('#international_shipping').is(':checked')) && ($('input[name="international_shipping_free"]:checked').val() == 0) && (!validatePrice($('#international_shipping_price').val()))) {
                $('#spn-international_shipping_price-error').html('Please enter valid price for Domestic shipping!');
                $('#international_shipping_free').focus();
//            flag = 1;
                return false;
            } else {
                $('#spn-international_shipping_price-error').html('');
            }
        }
        if (flag == 1) {
            return false;
        } else {
            $('#btn_submit').prop('disabled', true);
            return true;
        }
    }

    function validatePrice(price) {
        if (price != 0) {
//        return /^(?:\d+|\d{1,3}(?:,\d{3})+)(?:\.\d+)?$/.test(price);
            return /^\d+(\.\d{1,4})?$/.test(price);
        } else {
            return false;
        }
    }
    function confirm_alert(e) {
        swal({
            title: "Are you sure?",
            text: "This physical product image will be removed!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#FF7043",
            confirmButtonText: "Yes, delete it!"
        },
        function (isConfirm) {
            if (isConfirm) {
                url = $(e).attr('data-href');
                data_id = $(e).attr('data-id');
                $.ajax({
                    url: url,
                    type: 'POST',
                    success: function (data) {
                        $('#' + data_id).remove();
                    }
                });
                return true;
            }
            else {
                return false;
            }
        });
        return false;
    }

    $("#add_hotel_form").validate({
        errorClass: 'validation-error-label',
        highlight: function (element, errorClass) {
            $(element).removeClass(errorClass);
        },
        unhighlight: function (element, errorClass) {
            $(element).removeClass(errorClass);
        },
        rules: {
            hotel_name: "required",
            hotel_address: "required",
        },
    });
    function addHotel() {
        if ($('#add_hotel_form').valid()) {
            console.log('valid');
        } else {
            console.log('not valid');
        }
    }
</script>
