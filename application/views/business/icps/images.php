<script type="text/javascript" src="assets/admin/js/plugins/media/fancybox.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/tables/datatables/datatables.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/forms/selects/select2.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/notifications/sweet_alert.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/uploaders/dropzone.min.js"></script>
<!--<script type="text/javascript" src="assets/admin/js/pages/gallery_library.js"></script>-->

<script type="text/javascript" src="assets/admin/js/plugins/ui/moment/moment.min.js"></script>
<link href="assets/admin/css/bootstrap-datetimepicker.css">
<script type="text/javascript" src="assets/admin/js/bootstrap-datetimepicker.js"></script>
<div class="page-header page-header-default">
    <div class="page-header-content">
        <div class="page-title">
            <h4><i class="icon-images2"></i> <span class="text-semibold">Manage <?php echo $heading ?></span></h4>
        </div>
    </div>
    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="<?php echo site_url('business/home'); ?>"><i class="icon-home2 position-left"></i> Home</a></li>
            <li><a href="<?php echo site_url('business/icps'); ?>"><i class="icon-lan2 position-left"></i> ICPs</a></li>
            <li class="active">ICP Images</li>
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
                        <div class="col-sm-3">
                            <div class="form-group">
                                <div class='input-group date' id='datetimepicker1'>
                                    <span class="input-group-addon"><i class="icon-calendar3"></i></span>
                                    <input type='text' class="form-control" name="image_capture_time" id="ButtonCreationDemoInput" value="<?php echo date('m-d-Y h:i A'); ?>"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-9">
                            <button class="btn btn-primary" type="button" id="btn_submit">Upload <i class="icon-arrow-up13 position-right"></i></button>
                        </div>
                    </div>
                </fieldset>
            </div>
        </form>

        <div class="upload-dir-main">
            <div class="upload-dir">
                <div style="text-align: center;">
                    <h1>Or you can give directory path : </h1>
                </div>
                <form action="<?php echo site_url('business/icps/upload_image_dir/'); ?>" method="post" id="submit_icp_image_dir_form" class="validate-form" enctype="multipart/form-data">
                    <input type="hidden" name="icp_id" value="<?php echo $icp_data['id'] ?>"/>
                    <div class="panel-body">
                        <div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-lg-10">
                                        <input type="file" name="files[]" id="files" multiple="" directory="" webkitdirectory="" mozdirectory="" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <div class='input-group date' id='datetimepicker1'>
                                        <span class="input-group-addon"><i class="icon-calendar3"></i></span>
                                        <input type='text' class="form-control" name="image_dir_capture_time" id="ButtonCreationDemoInputDir" placeholder="Capture time" value="<?php echo date('m-d-Y h:i A'); ?>"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <button class="btn btn-primary" type="submit" name="btn_submit_dir" id="btn_submit_dir">Upload <i class="icon-arrow-up13 position-right"></i></button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="panel panel-flat">
        <?php if ($matched_image_count > 0) { ?>
            <div class="panel-heading text-right">
                <a href="<?php echo site_url('business/icps/matched_images/' . $icp_data['id']); ?>" class="btn btn-primary btn-labeled"><b><span class="badge"><?php echo $matched_image_count ?></span></b> Matched Images</a>
            </div>
        <?php } ?>
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
                    <th>Image Capture time</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>
    <?php $this->load->view('Templates/footer'); ?>
</div>
<style>
    .badge {
        padding: 0px 6px 0px 6px;
    }
</style>
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
            ajax: site_url + 'business/icps/get_icp_images/<?php echo $icp_data['id'] ?>',
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
                        return '<a href="' + base_url + icp_image_path + data + '" data-popup="lightbox"><img src="' + base_url + icp_image_path + data + '" alt="" class="img-rounded img-preview"></a>';
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
                    data: "image_capture_time",
                    visible: true,
                },
                {
                    data: "is_delete",
                    visible: true,
                    searchable: false,
                    sortable: false,
                    render: function (data, type, full, meta) {
                        return '<a href="' + site_url + 'business/icps/delete_icp_image/' + full.id + '" class="btn border-danger text-danger btn-flat btn-icon btn-rounded btn-xs" onclick="return confirm_alert(this)" title="Delete Image"><i class="icon-cross2"></i></a>';
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
            text: "You will not be able to recover this ICP Image!",
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
            maxFilesize: 10, // MB
            addRemoveLinks: true,
            autoProcessQueue: false,
//            maxFiles: 5,
            acceptedFiles: '.jpg, .png, .jpeg',
            init: function () {
                var submitButton = document.querySelector("#btn_submit")
                myDropzone = this;
                submitButton.addEventListener("click", function () {
                    var capture_time = $('#ButtonCreationDemoInput').val();
                    if (capture_time == '') {
                        swal({
                            title: "Please enter valid image capture time!",
                            confirmButtonColor: "#2196F3",
                            type: "info"
                        });
                    } else if (myDropzone.files.length > 0) {
                        valid_file = 1;
                        $.each(myDropzone.files, function (index, file) {
                            if (!file.accepted) {
                                valid_file = 0;
                            }
                        });

                        if (valid_file == 0) {
                            swal({
                                title: "Please upload valid images!",
                                confirmButtonColor: "#2196F3",
                                type: "info"
                            });
                        } else {
                            $('.loading').show();
                            $('#submit_icp_image_form .panel-body').block({
                                message: '<span>Please wait while image(s) are being uploaded for processing</span>&nbsp;&nbsp;<i class="icon-spinner9 spinner"></i>',
                                overlayCSS: {
                                    backgroundColor: '#fff',
                                    opacity: 0.5,
                                    cursor: 'wait',
                                },
                                css: {
                                    border: 0,
                                    padding: 0,
                                    backgroundColor: 'none',
                                }
                            });
                            $('#btn_submit').prop('disabled', true);
                            $('#btn_submit').html('Loading <i class="icon-spinner2 spinner"></i>');
                            icp_id = "<?php echo $icp_data['id'] ?>";

                            myDropzone.options.autoProcessQueue = true;
                            myDropzone.options.url = site_url + "business/icps/upload_image/" + icp_id;
                            myDropzone.processQueue();
                        }
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
                            window.location.href = site_url + 'business/icps/icp_images/<?php echo $icp_data['id'] ?>';
                        }
                    }
                });
                myDropzone.on('sending', function (file, xhr, formData) {
                    formData.append('image_capture_time', $('#ButtonCreationDemoInput').val());
                });
            },
        });
        $("#submit_icp_image_dir_form").submit(function (e) {
            var formData = new FormData($(this)[0]);
            var url = $(this).attr("action"); // the script where you handle the form input.
            $('.loading').show();
            $('#submit_icp_image_dir_form .panel-body').block({
                message: '<span>Please wait while image(s) are being uploaded for processing</span>&nbsp;&nbsp;<i class="icon-spinner9 spinner"></i>',
                overlayCSS: {
                    backgroundColor: '#fff',
                    opacity: 0.5,
                    cursor: 'wait',
                },
                css: {
                    border: 0,
                    padding: 0,
                    backgroundColor: 'none',
                }
            });
            $.ajax({
                type: "POST",
                url: url,
                dataType: 'json',
                data: formData, // serializes the form's elements.
                processData: false,
                contentType: false,
                success: function (data)
                {
//                    alert(data); // show response from the php script.
                    window.location.href = site_url + 'business/icps/icp_images/<?php echo $icp_data['id'] ?>';
                }
            });

            e.preventDefault(); // avoid to execute the actual submit of the form.
        });
    });
    $(function () {
        $('#ButtonCreationDemoInput').datetimepicker({
            maxDate: 'now'
        });
        $('#ButtonCreationDemoInputDir').datetimepicker({
            maxDate: 'now'
        });
    });
</script>