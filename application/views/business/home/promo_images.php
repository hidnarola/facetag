<script type="text/javascript" src="assets/admin/js/plugins/media/fancybox.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/tables/datatables/datatables.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/forms/selects/select2.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/notifications/sweet_alert.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/uploaders/dropzone.min.js"></script>
<!--<script type="text/javascript" src="assets/admin/js/pages/gallery_library.js"></script>-->
<div class="page-header page-header-default">
    <div class="page-header-content">
        <div class="page-title">
            <h4><i class="icon-images2"></i> <span class="text-semibold"><?php echo $heading ?></span></h4>
        </div>
    </div>
    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="<?php echo site_url('business/home'); ?>"><i class="icon-home2 position-left"></i> Home</a></li>
            <li class="active">Promo Feature Images</li>
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
            <div class="panel-body">
                <fieldset class="content-group">
                    <legend class="text-semibold">
                        Upload Business Promo Feature Images
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
    <div class="panel panel-flat">
        <!--        <div class="panel-heading text-right">
                    <a href="<?php echo site_url('business/icps/add_image/' . $icp_data['id']); ?>" class="btn btn-success btn-labeled"><b><i class="icon-image2"></i></b> Add ICP Image</a>
                </div>-->
        <table class="table datatable-basic">
            <thead>
                <tr>
                    <th>Sr No.</th>
                    <th>Preview</th>
                    <th>Date</th>
                    <th>File info</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>
    <?php $this->load->view('Templates/footer'); ?>
</div>
<script>
    //-- Initialize datatable
    $(function () {
        $('.datatable-basic').dataTable({
            bFilter: false,
            autoWidth: false,
            processing: true,
            serverSide: true,
            "pageLength": 100,
            language: {
                search: '<span>Filter:</span> _INPUT_',
                lengthMenu: '<span>Show:</span> _MENU_',
                paginate: {'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;'}
            },
            dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
            order: [[2, "desc"]],
            ajax: site_url + 'business/home/get_promo_images',
            columns: [
                {
                    data: "sr_no",
                    visible: true,
                    sortable: false,
                },
                {
                    data: "image",
                    visible: true,
                    render: function (data, type, full, meta) {
//                        return '';
                        return '<a href="' + business_promo_feature_img_path + data + '" data-popup="lightbox"><img src="' + business_promo_feature_img_path + data + '" alt="" class="img-rounded img-preview"></a>';
                    },
                    sortable: false,
                },
                {
                    data: "created",
                    visible: true,
                },
                {
                    data: "fileinfo",
                    visible: true,
                    render: function (data, type, full, meta) {
                        var fileinfo = '<ul class="list-condensed list-unstyled no-margin">';
                        fileinfo += '<li><span class="text-semibold">Size:</span> ' + full.filesize + '</li>';
                        fileinfo += '<li><span class="text-semibold">Format:</span> ' + full.fileformat + '</li>';
                        fileinfo += '</ul>';
                        return fileinfo;
                    },
                    sortable: false
                },
                {
                    data: "is_delete",
                    visible: true,
                    searchable: false,
                    sortable: false,
                    render: function (data, type, full, meta) {
                        return '<a href="' + site_url + 'business/home/delete_promo_image/' + full.id + '" class="btn border-danger text-danger btn-flat btn-icon btn-rounded btn-xs" onclick="return confirm_alert(this)" title="Delete Image"><i class="icon-cross2"></i></a>';
                    }
                }
            ]
        });

        $('.dataTables_length select').select2({
            minimumResultsForSearch: Infinity,
            width: 'auto'
        });

    });
    //-- Displays confirm alert
    function confirm_alert(e) {
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this Promo Image!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#FF7043",
            confirmButtonText: "Yes, delete it!"
        },
        function (isConfirm) {
            if (isConfirm) {
                window.location.href = $(e).attr('href');
                return true;
            }
            else {
                return false;
            }
        });
        return false;
    }
    // Lightbox
    $('[data-popup="lightbox"]').fancybox({
        padding: 3
    });
    //-- Dropdoze to upload files
    $('document').ready(function () {
        Dropzone.autoDiscover = false;
        // Removable thumbnails
        $("#dropzone_remove").dropzone({
            paramName: "files", // The name that will be used to transfer the file
            dictDefaultMessage: 'Drop Images to upload <span>or CLICK</span>',
            maxFilesize: 20, // MB
            addRemoveLinks: true,
            autoProcessQueue: false,
//            maxFiles: 5,
            acceptedFiles: '.jpg, .png, .jpeg',
            init: function () {
                var submitButton = document.querySelector("#btn_submit")
                myDropzone = this;
                submitButton.addEventListener("click", function () {

                    if (myDropzone.files.length > 0) {

                        $('.loading').show();
                        $('#btn_submit').prop('disabled', true);
                        $('#btn_submit').html('Loading <i class="icon-spinner2 spinner"></i>');

                        myDropzone.options.autoProcessQueue = true;
                        myDropzone.options.url = site_url + "business/home/upload_promo_image/";
                        myDropzone.processQueue();
                    } else {

                        swal({
                            title: "You have not selected any image to upload!",
                            text: "Please Drop images or click to upload",
                            confirmButtonColor: "#2196F3",
                            type: "info"
                        });
                    }
                });
                myDropzone.on("addedfile", function (file) {
                    if (!file.type.match(/image.*/)) {
                        if (file.type.match(/application.zip/)) {
                            myDropzone.emit("thumbnail", file, "path/to/img");
                        } else {
                            myDropzone.emit("thumbnail", file, "path/to/img");
                        }
                    }

                    //-- Added below code to don't allow duplicate file upload
                    if (this.files.length) {
                        var _i, _len;
                        for (_i = 0, _len = this.files.length; _i < _len - 1; _i++) // -1 to exclude current file
                        {
                            if (this.files[_i].name === file.name && this.files[_i].size === file.size && this.files[_i].lastModifiedDate.toString() === file.lastModifiedDate.toString())
                            {
                                this.removeFile(file);
                            }
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
                            window.location.href = site_url + 'business/private_information';

                        }
                    }
                });
            },
        });

    });
</script>