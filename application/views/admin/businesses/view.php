<style>
    .ui-sortable-helper{
        background: #ddd !important;
    }
    .dz-error-message{
        opacity: 1 !important;
    }
</style>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?libraries=geometry,places&key=AIzaSyBR_zVH9ks9bWwA-8AzQQyD6mkawsfF9AI"></script>
<script type="text/javascript" src="assets/admin/js/plugins/media/fancybox.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/tables/datatables/datatables.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/forms/selects/select2.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/notifications/sweet_alert.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/uploaders/dropzone.min.js"></script>
<script type="text/javascript" src="assets/js/notify.js"></script>
<script type="text/javascript" src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
<script type="text/javascript" src="https://mpryvkin.github.io/jquery-datatables-row-reordering/1.2.3/jquery.dataTables.rowReordering.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js"></script>
<div class="page-header page-header-default">
    <div class="page-header-content">
        <div class="page-title">
            <h4><i class="icon-eye4"></i> <span class="text-semibold"><?php echo $heading; ?></span></h4>
        </div>
    </div>
    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="<?php echo site_url('admin/home'); ?>"><i class="icon-home2 position-left"></i> Home</a></li>
            <li><a href="<?php echo site_url('admin/businesses'); ?>"><i class="icon-office"></i> Businesses</a></li>
            <li class="active"><?php echo $heading; ?></li>
        </ul>
    </div>
</div>
<div class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-body border-top-info">
                <div class="row">
                    <div class="col-md-2">
                        <!-- Horizontal form -->
                        <div class="thumbnail">
                            <div class="thumb thumb-slide">
                                <?php if ($business_data['logo'] != '' && file_exists(BUSINESS_LOGO_IMAGES . $business_data['logo'])) { ?>
                                    <img src="<?php echo BUSINESS_LOGO_IMAGES . $business_data['logo'] ?>" alt="<?php echo $business_data['name'] ?>">
                                <?php } else { ?>
                                    <img src="assets/admin/images/no_logo.png" alt="<?php echo $business_data['name'] ?>">
                                <?php } ?>
                            </div>
                        </div>
                        <!-- /horizotal form -->
                    </div>
                    <?php if ($business_data['latitude'] != '' && $business_data['longitude'] != '') { ?>
                        <div class="col-md-10">
                            <div class="thumbnail" id="mapContainer">
                                <div id="map-canvas" style="height:290px;display: none"></div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <div class="row">
                    <div class="col-md-6"><p class="content-group-sm text-muted">Business Information</p>
                        <div class="well">
                            <dl class="dl-horizontal">
                                <dt>Business Name</dt>
                                <dd><?php echo $business_data['name'] ?></dd>
                                <dt>Business Reg Number</dt>
                                <dd><?php echo $business_data['reg_no'] ?></dd>
                                <dt>GST/VAT tax registered?</dt>
                                <dd>
                                    <?php
                                    if ($business_data['is_gst_registered'] == 1) {
                                        echo 'Yes';
                                    } else {
                                        echo 'No';
                                    }
                                    ?>
                                </dd>
                                <?php if ($business_data['is_gst_registered'] == 1) { ?>
                                    <dt>GST/VAT Number</dt>
                                    <dd><?php echo $business_data['gst_no'] ?></dd>
                                <?php } ?>
                                <dt>Address1</dt>
                                <dd><?php echo $business_data['address1'] ?></dd>
                                <dt>Address2</dt>
                                <dd><?php echo $business_data['address2'] ?></dd>
                                <?php if ($business_data['business_type'] != '') { ?>
                                    <dt>Business Types</dt>
                                    <dd><?php
                                        $business_type = $business_data['business_type'];
                                        $business_type = rtrim($business_type);
                                        echo rtrim($business_type, ",")
                                        ?>
                                    </dd>
                                <?php } ?>
                                    <?php if ($business_data['hear_about'] != '') { ?>
                                    <dt>Hear about facetag from</dt>
                                    <dd><?php
                                        $hear_about = $business_data['hear_about'];
                                        $hear_about = rtrim($hear_about);
                                        echo rtrim($hear_about, ",")
                                        ?>
                                    </dd>
                                <?php } ?>
                                <?php if ($business_data['daily_visitors'] != '') { ?>
                                    <dt>Average daily visitors</dt>
                                    <dd><?php echo $business_data['daily_visitors'] ?></dd>
                                <?php } ?>
                                <?php if ($business_data['visitor_photographs'] != '') { ?>
                                    <dt>Average number of Visitor photographs taken daily</dt>
                                    <dd><?php echo $business_data['visitor_photographs'] ?></dd>
                                <?php } ?>
                                <?php if ($business_data['distribute_photograph'] != '') { ?>
                                    <dt>Distribute visitor photographs for</dt>
                                    <dd><?php echo ucfirst($business_data['distribute_photograph']) ?></dd>
                                <?php } ?>
                                <?php if ($business_data['distribute_photograph'] == 'forsale' || $business_data['distribute_photograph'] == 'both') { ?>
                                    <dt>Sale Price</dt>
                                    <dd><?php echo $business_data['distribute_photograph']; ?></dd>
                                <?php } ?>
                                <hr>
                                <p class="content-group-sm text-muted">Social links</p>
                                <dt>Facebook</dt>
                                <dd><?php echo $business_data['facebook_url'] ?></dd>
                                <dt>Twitter</dt>
                                <dd><?php echo $business_data['twitter_url'] ?></dd>
                                <dt>Instagram</dt>
                                <dd><?php echo $business_data['instagram_url'] ?></dd>
                                <dt>Website URL </dt>
                                <dd><?php echo $business_data['website_url'] ?></dd>
                                <dt>Ticket URL </dt>
                                <dd><?php echo $business_data['ticket_url'] ?></dd>
                            </dl>
                        </div>
                    </div>
                    <div class="col-md-6"><p class="content-group-sm text-muted">About Business</p>
                        <div class="well">
                            <dl class="dl-horizontal">
                                <dt>Promotional description</dt>
                                <dd><?php echo $business_data['description'] ?></dd>
                                <?php if ($business_data['open_times'] != NULL) { ?>
                                    <dt>Open times</dt>
                                    <dd>
                                        <?php
                                        $days = array('mon' => 'Monday', 'tue' => 'Tuesday', 'wed' => 'Wednesday', 'thu' => 'Thursday', 'fri' => 'Friday', 'sat' => 'Saturday', 'sun' => 'Sunday');
                                        $business_hours_arr = json_decode($business_data['open_times']);
                                        ?>
                                        <?php if ($business_hours_arr) { ?>
                                            <table>
                                                <tr><th>Day</th><th>Start time</th><th>End time</th></tr>
                                                <?php
                                                foreach ($days as $key => $val) {
                                                    if ($business_hours_arr->$key->open == 1) {
                                                        echo "<tr><td>" . $val . "</td><td>" . $business_hours_arr->$key->starttime . "</td><td>" . $business_hours_arr->$key->endtime . "</td></tr>";
                                                    }
                                                }
                                                ?>
                                            </table>
                                        <?php } ?>
                                    </dd>
                                <?php } ?>
                                <hr>
                                <p class="content-group-sm text-muted">Contact (customer service/enquiry)</p>
                                <dt>Phone number</dt>
                                <dd><?php echo $business_data['contact_no'] ?></dd>
                                <dt>Email </dt>
                                <dd><?php echo $business_data['contact_email'] ?></dd>
                                <hr>
                                <p class="content-group-sm text-muted">Business User</p>
                                <dt>Name of User </dt>
                                <dd><?php echo $business_data['firstname'] . ' ' . $business_data['lastname'] ?></dd>
                                <dt>User Email</dt>
                                <dd><?php echo $business_data['email'] ?></dd>
                                <dt>Contact Number of User </dt>
                                <dd><?php echo $business_data['phone_no'] ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="panel-heading">
        <div class="col-md-6">
            <h6 class="content-group text-semibold">
                Business Promo Feature Images
            </h6>
        </div>
        <div class="col-md-6 text-right">
            <a class="btn btn-success btn-labeled" data-toggle="modal" data-target="#modal_default"><b><i class="icon-images2"></i></b> Add Promo Images</a>
        </div>
    </div>
    <div class="col-md-12">
        <div class="panel panel-body border-top-info">
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
    </div>
    <?php $this->load->view('Templates/footer'); ?>
</div>
<div id="modal_default" class="modal fade">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h5 class="modal-title">Upload promo images</h5>
            </div>
            <div class="modal-body">
                <form action="" method="post" id="submit_icp_image_form" class="validate-form" enctype="multipart/form-data">
                    <div class="form-group">
                        <div action="#" class="dropzone" id="dropzone_remove"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
                <button class="btn btn-primary" type="button" id="btn_submit">Upload <i class="icon-arrow-up13 position-right"></i></button>
            </div>
        </div>
    </div>
</div>
<!--<a onclick="test()">Test</a>-->
<script type="text/javascript">
    var business_promo_images_cnt = <?php echo $business_promo_images ?>;
<?php if (isset($business_data) && $business_data['latitude'] != '' && $business_data['longitude'] != '') { ?>
        var infowindow = new google.maps.InfoWindow();
        var map = new google.maps.Map(document.getElementById("map-canvas"));
        //        console.log('latitude and longitude');
        lat = '<?php echo $business_data['latitude'] ?>';
        long = '<?php echo $business_data['longitude'] ?>';
        generateMap(lat, long);
<?php } ?>
    //Generates the map from latitude and longitude
    function generateMap(latitude, longitude) {
        $('#map-canvas').show();
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
        });
        geocoder.geocode({'latLng': latlngPos}, function (results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                if (results[0]) {
                    infowindow.setContent(results[0].formatted_address);
                    infowindow.open(map, marker);
                }
            }
        });
    }
    $(function () {
//        if (business_promo_images_cnt > 0) {
        var table = $('.datatable-basic').dataTable({
            bFilter: false,
            autoWidth: false,
            processing: true,
//            serverSide: true,
            rowReorder: true,
            "pageLength": 100,
            language: {
                search: '<span>Filter:</span> _INPUT_',
                lengthMenu: '<span>Show:</span> _MENU_',
                paginate: {'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;'}
            },
            dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
            order: [[2, "desc"]],
            ajax: site_url + 'admin/businesses/get_promo_images/<?php echo $business_data['id'] ?>',
            createdRow: function (row, data, dataIndex) {
                $(row).attr('id', 'row_' + data.id + '_' + dataIndex);
                $(row).attr('data-id', data.id);
            },
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
                        return '<a href="' + site_url + 'admin/businesses/delete_promo_image/' + full.id + '" class="btn border-danger text-danger btn-flat btn-icon btn-rounded btn-xs" onclick="return confirm_alert(this)" title="Delete Image"><i class="icon-cross2"></i></a>';
                    }
                }
            ]
        });
        
        table.rowReordering({
            fnUpdateCallback: function (row) {
//                $("#" + row.id).attr("data-id", row.toPosition);
                var IDs = [];
                var business_id = <?php echo $business_data['id'] ?>;
                IDs = $(".ui-sortable tr[id]") // find spans with ID attribute
                        .map(function () {
                            console.log($(this).data('id'));
                            return $(this).data('id');
                        }) // convert to set of IDs
                        .get(); // convert to instance of Array (optional)
                $.ajax({
                    url: '<?php echo base_url() . "admin/businesses/change_promo_images_order" ?>',
                    type: 'POST',
                    dataType: "json",
                    data: {reorderlist: IDs, business_id: business_id},
                    success: function (data) {
                        if (data.result == "success") {
                            $.notify("Promo feature images rearranged successfully!", "success");
                        } else {
                            $.notify("Please try again!", "error");
                        }
                    }
                });
            }
        });
        
        $('.dataTables_length select').select2({
            minimumResultsForSearch: Infinity,
            width: 'auto'
        });
//        }
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
            maxFiles: 30,
            acceptedFiles: '.jpg, .png, .jpeg',
            init: function () {
                var submitButton = document.querySelector("#btn_submit")
                myDropzone = this;

                submitButton.addEventListener("click", function () {

                    if (myDropzone.files.length > 0) {
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
                            $('#btn_submit').prop('disabled', true);
                            $('#btn_submit').html('Loading <i class="icon-spinner2 spinner"></i>');

                            myDropzone.options.autoProcessQueue = true;
                            myDropzone.options.url = site_url + "admin/businesses/upload_promo_image/<?php echo $business_data['id'] ?>";
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
                            window.location.href = site_url + 'admin/businesses/view/<?php echo $business_data['id'] ?>';
                        }
                    }
                });
            },
            /*
             thumbnail: function (file, dataUrl) {
             var reader = new FileReader();
             reader.onload = function (e) {
             console.log('here ' + e.target.result);
             $(file.previewElement).removeClass("dz-file-preview").addClass("dz-image-preview");
             $(file.previewElement).find(".dz-image img").attr("src", e.target.result);
             $("#preview").attr("src", e.target.result);
             }
             reader.readAsDataURL(file);
             
             //                // Display the image in your file.previewElement
             //                $(file.previewElement).removeClass("dz-file-preview").addClass("dz-image-preview");
             //                $(file.previewElement).find(".dz-image img").attr("src", dataUrl);
             //                $("#preview").attr("src", dataUrl);
             }, */
             
        });
    });
//    function test(){
//        console.log(drop_zone.files);
//    }
</script>