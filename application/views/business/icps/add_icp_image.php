<script type="text/javascript" src="assets/admin/js/plugins/notifications/sweet_alert.min.js"></script>
<script src="assets/js/dropzone.min.js" type="text/javascript"></script>
<link href="assets/css/dropzone.css" rel="stylesheet" type="text/css">
<div class="page-header page-header-default">
    <div class="page-header-content">
        <div class="page-title">
            <h4><i class="icon-image2"></i> <span class="text-semibold"><?php echo $heading; ?></span></h4>
        </div>
    </div>
    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="<?php echo site_url('business/home'); ?>"><i class="icon-home2 position-left"></i> Home</a></li>
            <li><a href="<?php echo site_url('business/icps'); ?>"><i class="icon-lan2 position-left"></i> ICPs</a></li>
            <li><a href="<?php echo site_url('business/icps/icp_images/' . $icp_data['id']); ?>"><i class="icon-images2 position-left"></i> ICP Images</a></li>
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
        <form action="" method="post" id="submit_icp_image_form" class="validate-form" enctype="multipart/form-data">
            <input type="hidden" name="icp_id" value="<?php echo $icp_data['id'] ?>"/>
            <div class="panel-body">
                <fieldset class="content-group">
                    <legend class="text-semibold">
                        Upload ICP Images
                    </legend>
                    <div class="form-group">
                        <div action="#" class="dropzone" id="dropzone_remove"></div>
                    </div>
                    <div>
                        <button class="btn btn-primary" type="button" id="btn_submit">Upload <i class="icon-arrow-up13 position-right"></i></button>
                    </div>
                </fieldset>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript">
    $('document').ready(function () {
        Dropzone.autoDiscover = false;
        // Removable thumbnails
        $("#dropzone_remove").dropzone({
            paramName: "files", // The name that will be used to transfer the file
            dictDefaultMessage: 'Drop files to upload <span>or CLICK</span>',
            maxFilesize: 20, // MB
            addRemoveLinks: true,
            autoProcessQueue: false,
            maxFiles: 5,
            acceptedFiles: '.jpg, .png, .jpeg',
            init: function () {
                var submitButton = document.querySelector("#btn_submit")
                myDropzone = this;
                submitButton.addEventListener("click", function () {
                    $('.loading').show();
                    $('#btn_submit').prop('disabled', true);
                    $('#btn_submit').html('Loading <i class="icon-spinner2 spinner"></i>');
                    icp_id = "<?php echo $icp_data['id'] ?>";
//                    var formElement = document.querySelector("#submit_icp_image_form");
//                    var fd = new FormData(formElement);

//                    url = 'business/icps/add_image/' + icp_id;
//                    $.ajax({
//                        url: url,
//                        type: 'POST',
//                        data: fd,
//                        processData: false,
//                        contentType: false,
//                        success: function (response) {
//                            data = JSON.parse(response);
//                            if (data.insert_id != '') {
                    if (myDropzone.files.length > 0) {
                        myDropzone.options.autoProcessQueue = true;
                        myDropzone.options.url = site_url + "business/icps/upload_image/" + icp_id;
                        myDropzone.processQueue();
                    } else {

                        window.location.href = site_url + 'business/icps/';
                    }
//                            } else {
//                                alert('Failed to upload, Please try again later!');
//                            }
//                        }
//                    });
                });
                myDropzone.on("addedfile", function (file) {
                    if (!file.type.match(/image.*/)) {
                        if (file.type.match(/application.zip/)) {
                            myDropzone.emit("thumbnail", file, "path/to/img");
                        } else {
                            myDropzone.emit("thumbnail", file, "path/to/img");
                        }
                    }
                });
                myDropzone.on("complete", function (file) {
                    $('.loading').hide();
                    if (file.size > 20 * 1024 * 1024) {
                        return false;
                    } else if (!file.type.match('image.*')) {
                        return false;
                    } else {
                        if (file.type == 'image/svg+xml')
                            return false;
                        if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                            myDropzone.removeFile(file);
                            window.location.href = site_url + 'business/icps/icp_images/<?php echo $icp_data['id'] ?>';

                        }
                    }
                });
            },
        });

    });
</script>