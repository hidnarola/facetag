<script type="text/javascript" src="assets/admin/js/plugins/media/fancybox.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/tables/datatables/datatables.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/forms/selects/select2.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/notifications/sweet_alert.min.js"></script>
<div class="page-header page-header-default">
    <div class="page-header-content">
        <div class="page-title">
            <h4><i class="icon-lan2"></i> <span class="text-semibold">Manage <?php echo $heading ?></span></h4>
        </div>
    </div>
    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="<?php echo site_url('business/home'); ?>"><i class="icon-home2 position-left"></i> Home</a></li>
            <li class="active">ICPS</li>
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
        <div class="panel-heading text-right">
            <a href="<?php echo site_url('business/icps/add'); ?>" class="btn btn-success btn-labeled"><b><i class="icon-plus-circle2"></i></b> Add ICP</a>
        </div>
        <table class="table datatable-basic">
            <thead>
                <tr>
                    <th>Sr No.</th>
                    <th>Logo</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Total Images</th>
                    <th>Matched Images</th>
                    <th>Added On</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>
    <?php $this->load->view('Templates/footer'); ?>
</div>
<!-- Model for auto upload images-->
<div id="autoUploadImagesModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Automatic upload images</h4>
            </div>
            <div class="modal-body">
                <p>
                <form id="frm_autoUpload" class="form-horizontal" action="<?php echo base_url() . 'business/icps/generate_script'; ?>" role="form" method="post">
                    <input type="hidden" name="icp_id" id="icp_id" value="">
                    <div class="form-group">
                        <label class="control-label col-sm-3" for="tag">Enter your local path :</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="local_path" name="local_path" placeholder="Enter your local path here" value="<?php echo set_value('local_path'); ?>" required>
                            <div class="error" id="tag_error" style="display: none;">Tag already exist.</div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-9">
                            <button type="submit" id="generateScript" class="btn btn-success">Submit</button>
                        </div>
                    </div>
                </form>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
    // Lightbox
    $('[data-popup="lightbox"]').fancybox({
        padding: 3
    });
    $(function () {
        $('.datatable-basic').dataTable({
            autoWidth: false,
            processing: true,
            serverSide: true,
            "pageLength": 100,
            language: {
                search: '<span>Filter:</span> _INPUT_',
                lengthMenu: '<span>Show:</span> _MENU_',
                paginate: {'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;'},
                searchPlaceholder: "Type name,description"
            },
            dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
            order: [[6, "desc"]],
            ajax: site_url + 'business/icps/get_icps',
            columns: [
                {
                    data: "sr_no",
                    visible: true,
                    sortable: false,
                },
                {
                    data: "icp_logo",
                    visible: true,
                    sortable: false,
                    render: function (data, type, full, meta) {
                        var icp_img = '';
                        if (data != '' && data != null) {
                            icp_img = '<a href="' + icp_logo + data + '" data-popup="lightbox"><img src="' + icp_logo + data + '" height="55px" width="55px" alt="' + full.name + '"></a>';
                        } else {
                            icp_img = '<a href="assets/admin/images/no_logo.png" data-popup="lightbox"><img src="assets/admin/images/no_logo.png" height="55px" width="55px" alt="' + full.name + '"></a>';
                        }
                        return icp_img;
                    }
                },
                {
                    data: "name",
                    visible: true,
                },
                {
                    data: "description",
                    visible: true,
                },
                {
                    data: "icp_images",
                    visible: true,
                },
                {
                    data: "matched_images",
                    visible: true,
                },
                {
                    data: "created",
                    visible: true,
                },
                {
                    data: "is_active",
                    visible: true,
                    searchable: false,
                    sortable: false,
                    render: function (data, type, full, meta) {
                        status = '';
                        if (data == 0) {
                            status = '<span class="label bg-danger">Deactivated</span>';
                        } else if (data == 1) {
                            var status = '<span class="label bg-success">Active</span>';
                        }
                        return status;

                    }
                },
                {
                    data: "status",
                    visible: true,
                    searchable: false,
                    sortable: false,
                    render: function (data, type, full, meta) {
                        $("#icp_id").val(full.id);
                        action = '';
                        action += '<ul class="icons-list">';
                        action += '<li class="dropdown">';
                        action += '<a href="#" class="dropdown-toggle" data-toggle="dropdown">';
                        action += '<i class="icon-menu9"></i>';
                        action += '</a>';
                        action += '<ul class="dropdown-menu dropdown-menu-right">';
                        action += '<li>';
                        if (full.is_active == 1) {
                            action += '<a href="' + site_url + 'business/icps/edit/' + full.id + '" title="Edit Icp"><i class="icon-pencil3"></i>Edit Icp</a>';
                            action += '&nbsp;&nbsp;<a href="' + site_url + 'business/icps/icp_images/' + full.id + '" title="Manage Images"><i class="icon-images2"></i>Manage Images</a>';
                            if (full.local_hotel_delivery == 1 && full.offer_printed_souvenir == 1) {
                                action += '&nbsp;&nbsp;<a href="' + site_url + 'business/hotels/index/' + full.id + '" title="Manange Hotels"><i class="icon-city"></i>Manange Hotels</a>';
                            }
                            action += '&nbsp;&nbsp;<a href="' + site_url + 'business/icps/block/' + full.id + '" onclick="return block_alert(this,\'block\')" title="Deactivate ICP"><i class="icon-blocked"></i>Deactivate ICP</a>';
                        } else {
                            action = '<a href="' + site_url + 'business/icps/block/' + full.id + '" title="Activate ICP" onclick="return block_alert(this,\'unblock\')" ><i class="icon-checkmark4"></i>Activate ICP</a>';
                        }
                        action += '&nbsp;&nbsp;<a href="' + site_url + 'business/icps/delete/' + full.id + '" onclick="return confirm_alert(this)" title="Delete Icp"><i class="icon-cross2"></i>Delete Icp</a>';
                        action += '&nbsp;&nbsp;<a href="javascript:void(0)" onclick="show_popup(this)" data-target="#autoUploadImagesModal" data-id="' + full.id + '" title="Upload images automatically"><i class="icon-file-download2"></i>Upload images automatically</a>';
                        
                        if(full.image_url) {
                            action += '&nbsp;&nbsp;<a onclick="return disconnect_alert(this)" href="' + site_url + 'business/icps/disconnect_fb/' + full.id + '" title="' + full.account_name + ' is connected"><i class="icon-facebook"></i>Disconnect Facebook</a>';
                        }else {
                        action += '&nbsp;&nbsp;<a href="' + site_url + 'business/icps/connect_fb/' + full.id + '" title="Connect to Facebook"><i class="icon-facebook"></i>Connect to Facebook</a>';
                            }
                        action += '</li>';
                        action += '</ul>';
                        action += '</li>';
                        action += '</ul>';

//                        action='<ul class="icons-list"><li class="text-teal-600"><a href="" id="edit" class="edit"><i class="icon-pencil7"></i></a></li><li class="text-purple-700"><a href="" id="view_" data-record="" class="view"><i class="icon-eye"></i></a></li><li class="text-danger-600"><a id="delete_" data-record="" class="delete"><i class="icon-trash"></i></a></li></ul>'
                        return action;
                    }
                },
            ]
        });

        $('.dataTables_length select').select2({
            minimumResultsForSearch: Infinity,
            width: 'auto'
        });

    });
    function show_popup(e) {
        var icp_id = $(e).attr('data-id');
        $('#icp_id').val(icp_id);
        $('#autoUploadImagesModal').modal();

    }
    function confirm_alert(e) {
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this ICP!",
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
    function disconnect_alert(e) {
        swal({
            title: "Are you sure?",
            text: "You are about to disconnect this ICP from connected facebook account!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#FF7043",
            confirmButtonText: "Yes, disconnect it!"
        },
                function (isConfirm) {
                    if (isConfirm) {
                        window.location.href = $(e).attr('href');
                        return true;
                    } else {
                        return false;
                    }
                });
        return false;
    }
    function block_alert(e, type) {
        swal({
            title: "Are you sure?",
            text: "The ICP will be " + type + "ed!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#FF7043",
            confirmButtonText: "Yes, " + type + " it!"
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
</script>