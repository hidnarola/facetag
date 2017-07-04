<script type="text/javascript" src="assets/admin/js/plugins/media/fancybox.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/tables/datatables/datatables.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/forms/selects/select2.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/notifications/sweet_alert.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/uploaders/dropzone.min.js"></script>
<!--<script type="text/javascript" src="assets/admin/js/pages/gallery_library.js"></script>-->

<script type="text/javascript" src="assets/admin/js/plugins/ui/moment/moment.min.js"></script>
<link href="assets/admin/css/bootstrap-datetimepicker.css">
<script type="text/javascript" src="assets/admin/js/bootstrap-datetimepicker.js"></script>

<!--<script type="text/javascript" src="assets/admin/js/plugins/ui/moment/moment.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/pickers/daterangepicker.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/pickers/anytime.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/pickers/pickadate/picker.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/pickers/pickadate/picker.date.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/pickers/pickadate/picker.time.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/pickers/pickadate/legacy.js"></script>-->

<div class="page-header page-header-default">
    <div class="page-header-content">
        <div class="page-title">
            <h4><i class="icon-images2"></i> <span class="text-semibold">Manage <?php echo $heading ?></span></h4>
        </div>
    </div>
    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="<?php echo site_url('admin/home'); ?>"><i class="icon-home2 position-left"></i> Home</a></li>
            <li><a href="<?php echo site_url('admin/businesses'); ?>"><i class="icon-office position-left"></i> Businesses</a></li>
            <li><a href="<?php echo site_url('admin/businesses/icps/' . $icp_data['business_id']); ?>"><i class="icon-lan2 position-left"></i> ICPs</a></li>
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
                        <!--                        <div class="input-group">
                                                    <span class="input-group-btn">
                                                        <button type="button" class="btn btn-default btn-icon" id="ButtonCreationDemoButton"><i class="icon-calendar3"></i></button>
                                                    </span>
                                                    <input type="text" class="form-control" name="image_capture_time" id="ButtonCreationDemoInput" placeholder="Select a date">
                                                </div>-->
                    </div>
                    <div class="col-sm-9">
                        <button class="btn btn-primary" type="button" id="btn_submit">Upload <i class="icon-arrow-up13 position-right"></i></button>
                    </div>
                </div>
            </div>
        </form>

        <div class="upload-dir-main">
            <div class="upload-dir">
                <div style="text-align: center;">
                    <h1>Or you can give directory path : </h1>
                </div>
                <form action="<?php echo site_url('admin/businesses/upload_image_dir/'); ?>" method="post" id="submit_icp_image_dir_form" class="validate-form" enctype="multipart/form-data">
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
        
        <div class="upload-dir-main">
            <div class="upload-dir">
                <div style="text-align: center;">
                    <h1>Or you can crop and upload image : </h1>
                </div>
                <form action="<?php echo site_url('admin/businesses/upload_crop_image/'.$icp_data['id']); ?>" method="post" id="submit_icp_image_crop_form" class="validate-form" enctype="multipart/form-data">
                    <input type="hidden" name="icp_id" value="<?php echo $icp_data['id'] ?>"/>
                    <div class="panel-body">
                        <div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-lg-10">
                                        <div class="media-left" id="image_preview_div">
                                            
                                        </div>
                                        <!--<input type="file" name="crop_file" id="files" class="form-control">-->
                                        <div class="media-body">
<!--                                            <input type="file" name="logo" id="logo" class="file-styled" onchange="readURL(this);" <?php echo $required ?>>
                                            <span class="help-block">Accepted formats: png, jpg. Max file size 2Mb</span>-->

                                            <input type="file" name="original_img" class="file-styled js-fileinput img-upload form-control" accept="image/jpeg,image/png,image/gif" required="">
                                            <span class="help-block">Accepted formats: png, jpg. Max file size 2Mb</span>
                                            <canvas class="js-editorcanvas"></canvas>
                                            <canvas class="js-previewcanvas" style="display: none;"></canvas><br>
                                            <a href="javascript:void(0);" class="js-export img-export btn_img_crop btn">Crop</a><br><br>
                                            <input type="hidden" name="cropimg" id="cropimg" value="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <div class='input-group date' id='datetimepicker1'>
                                        <span class="input-group-addon"><i class="icon-calendar3"></i></span>
                                        <input type='text' class="form-control" name="image_crop_capture_time" id="ButtonCreationDemoInputCrop" placeholder="Capture time" value="<?php echo date('m-d-Y h:i A'); ?>" required=""/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <button class="btn btn-primary" type="submit" name="btn_submit_crop" id="btn_submit_crop">Upload <i class="icon-arrow-up13 position-right"></i></button>
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
                <a href="<?php echo site_url('admin/businesses/matched_images/' . $icp_data['id']); ?>" class="btn btn-primary btn-labeled"><b><span class="badge"><?php echo $matched_image_count ?></span></b> Matched Images</a>
            </div>
        <?php } ?>
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
            this.limit = options.limit || 500; // default to 600px
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
<script>
    $(function () {
        $('.datatable-basic').dataTable({
            bFilter: false,
            bInfo: false,
            autoWidth: false,
            processing: true,
            serverSide: true,
            "pageLength": 10,
            language: {
                search: '<span>Filter:</span> _INPUT_',
                lengthMenu: '<span>Show:</span> _MENU_',
                paginate: {'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;'}
            },
            dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
            order: [[2, "desc"]],
            ajax: site_url + 'admin/businesses/get_icp_images/<?php echo $icp_data['id'] ?>',
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
                        return '<a href="<?php echo site_url() . 'admin/businesses/get_image?image=' ?>' + base_url + icp_image_path + data + '" data-popup="lightbox"><img src="<?php echo site_url() . 'admin/businesses/get_image?image=' ?>' + base_url + icp_image_path + data + '" alt="" class="img-rounded img-preview"></a>';
//                        return '<a href="' + base_url + icp_image_path + data + '" data-popup="lightbox"><img src="' + base_url + icp_image_path + data + '" alt="" class="img-rounded img-preview"></a>';
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
                        return '<a href="' + site_url + 'admin/businesses/delete_icp_image/' + full.id + '" class="btn border-danger text-danger btn-flat btn-icon btn-rounded btn-xs" onclick="return confirm_alert(this)" title="Delete Image"><i class="icon-cross2"></i></a>';
                    }
                }
            ]
        });

        $('.dataTables_length select').select2({
            minimumResultsForSearch: Infinity,
            width: 'auto'
        });

    });
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

    $('document').ready(function () {
        Dropzone.autoDiscover = false;
        // Removable thumbnails
        $("#dropzone_remove").dropzone({
            paramName: "photo", // The name that will be used to transfer the file
            dictDefaultMessage: 'Drop Images to upload <span>or CLICK</span>',
            maxFilesize: 10, // MB
            addRemoveLinks: true,
            autoProcessQueue: false,
//            maxFiles: 5,
            acceptedFiles: '.jpg, .png, .jpeg',
            init: function () {

                /*
                 this.on("error", function (file) {
                 if (!file.accepted)
                 this.removeFile(file)
                 });
                 */

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
                            myDropzone.options.url = site_url + "admin/businesses/upload_image/" + icp_id;
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
//                        return false;
                    } else if (!file.type.match('image.*')) {
                        return false;
                    } else {
                        if (file.type == 'image/svg+xml')
                            return false;
                        if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                            myDropzone.removeFile(file);
                            window.location.href = site_url + 'admin/businesses/icp_images/<?php echo $icp_data['id'] ?>';
                        }
                    }
                });
                myDropzone.on('sending', function (file, xhr, formData) {
                    formData.append('image_capture_time', $('#ButtonCreationDemoInput').val());
                });
            },
        });

        $('#ButtonCreationDemoButton').click(function (e) {
            $('#ButtonCreationDemoInput').AnyTime_noPicker().AnyTime_picker().focus();
            e.preventDefault();
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
                    window.location.href = '<?php echo base_url(); ?>admin/businesses/icp_images/<?php echo $icp_data['id'] ?>';
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