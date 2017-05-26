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
<script type="text/javascript" src="assets/admin/js/plugins/uploaders/fileinput.min.js"></script>
<script type="text/javascript" src="assets/admin/js/pages/form_validation.js"></script>
<div class="page-header page-header-default">
    <div class="page-header-content">
        <div class="page-title">
            <h4>
                <?php
                if (isset($hotel_data))
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
            <li><a href="<?php echo site_url('business/home'); ?>"><i class="icon-home2 position-left"></i> Home</a></li>
            <li><a href="<?php echo site_url('business/hotels/index/' . $business_data['id']); ?>"><i class="icon-city"></i> Hotels</a></li>            
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
            <form class="form-horizontal form-validate-jquery" action="" id="business_info" method="post" enctype="multipart/form-data">
                <fieldset class="content-group">
                    <legend class="text-bold">Hotel Information</legend>
                    <div class="form-group">
                        <label class="col-lg-2 control-label">Upload Hotel Image</label>
                        <div class="col-lg-5">
                            <div class="media no-margin-top preview_photo_div_wrapper">
                                <div class="media-left" id="preview_photo_div">
                                    <?php
                                if (isset($hotel_data) && $hotel_data['hotel_pic'] != '') {
                                    $required = '';
                                    ?>
                                    <img src="<?php echo HOTEL_IMAGES . $hotel_data['hotel_pic'] ?>" style="width: 58px; height: 58px; border-radius: 2px;" alt="">
                                    <?php
                                } else {
                                    $required = 'required="required"';
                                    ?>
                                    <img src="assets/admin/images/placeholder.jpg" style="width: 58px; height: 58px; border-radius: 2px;" alt="">
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
                    <div class="form-group">
                        <label class="col-lg-2 control-label">Hotel Name<span class="text-danger">*</span></label>
                        <div class="col-lg-5">
                            <input type="text" name="name" id="name" placeholder="Enter Hotel Name" required="required" class="form-control" value="<?php echo (isset($hotel_data)) ? $hotel_data['name'] : set_value('name'); ?>">
                            <?php
                            echo '<label id="name-error" class="validation-error-label" for="name">' . form_error('name') . '</label>';
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-2 control-label">Hotel Address<span class="text-danger">*</span></label>
                        <div class="col-lg-5">
                            <textarea name="address" id="address" placeholder="Enter Hotel Address" required="required" class="form-control" rows="3"><?php echo (isset($hotel_data)) ? $hotel_data['address'] : set_value('address'); ?></textarea>
                            <?php
                            echo '<label id="address-error" class="validation-error-label" for="address">' . form_error('address') . '</label>';
                            ?>
                        </div>
                    </div>
                </fieldset>
                <div>
                    <button class="btn btn-success" type="submit">Save <i class="icon-arrow-right14 position-right"></i></button>
                </div>
            </form>
        </div>
    </div>
    <?php $this->load->view('Templates/footer'); ?>
</div>
<script>
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
//                img1.onload = start1;
                img1.src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
