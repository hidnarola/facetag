<script type="text/javascript" src="assets/admin/js/plugins/media/fancybox.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/tables/datatables/datatables.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/forms/selects/select2.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/notifications/sweet_alert.min.js"></script>
<div class="page-header page-header-default">
    <div class="page-header-content">
        <div class="page-title">
            <h4><i class="icon-office"></i> <span class="text-semibold">All Businesses</span></h4>
        </div>
    </div>
    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="<?php echo site_url('admin/home'); ?>"><i class="icon-home2 position-left"></i> Home</a></li>
            <li class="active">Businesses</li>
        </ul>
    </div>
</div>
<div class="content">
    <div class="row">
        <div class="col-md-12">
            <?php
            if ($this->session->userdata('invitation_success_msg')) {
                ?>
                <div class="alert alert-success hide-msg">
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Close</span></button>
                    <strong><?php echo $this->session->userdata('invitation_success_msg') ?></strong>
                </div>
                <?php $this->session->unset_userdata('invitation_success_msg') ?>
            <?php } ?>
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
            <!--<a href="<?php echo site_url('admin/businesses/invite'); ?>" class="btn btn-primary btn-labeled"><b><i class="icon-mail5"></i></b> Add Place</a>-->
            <a href="<?php echo site_url('admin/businesses/invite'); ?>" class="btn btn-success btn-labeled"><b><i class="icon-plus-circle2"></i></b> Add Place</a>
            <!--<a href="<?php echo site_url('admin/businesses/add'); ?>" class="btn btn-success btn-labeled"><b><i class="icon-plus-circle2"></i></b> Add new Business</a>-->
        </div>
        <table class="table datatable-basic">
            <thead>
                <tr>
                    <th>Sr No.</th>
                    <th>Business Logo</th>
                    <th>Business Name</th>
                    <th>Address</th>
                    <th>Business User</th>
                    <th>ICPs</th>
                    <th>Added On</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>
    <?php $this->load->view('Templates/footer'); ?>
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
                searchPlaceholder: "Type name,address,user"
            },
            dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
            order: [[6, "desc"]],
            ajax: site_url + 'admin/businesses/get_businesses',
            columns: [
                {
                    data: "sr_no",
                    visible: true,
                    sortable: false,
                },
                {
                    data: "logo",
                    visible: true,
                    sortable: false,
                    render: function (data, type, full, meta) {
                        var business_img = '';
                        if (full.logo != '' && full.logo != null) {
                            business_img = '<a href="' + business_logo_path + full.logo + '" data-popup="lightbox"><img src="assets/timthumb.php?src=' + site_url + business_logo_path + full.logo + '&w=60&h=60&q=100&zc=2"></a>';
                        } else {
                            business_img = '<a href="assets/admin/images/no_logo.png" data-popup="lightbox"><img src="assets/timthumb.php?src=' + site_url + 'assets/admin/images/no_logo.png&w=55&h=55&q=100&zc=2" height="55px" width="55px" alt="' + full.name + '"></a>';
                        }
                        return business_img;
                    }
                },
                {
                    data: "name",
                    visible: true,
                },
//                {
//                    data: "description",
//                    visible: true,
//                },
                {
                    data: "address1",
                    visible: true,
                },
                {
                    data: "firstname",
                    visible: true,
                    render: function (data, type, full, meta) {
                        if (full.firstname != null && full.lastname != null) {
                            return full.firstname + ' ' + full.lastname;
                        } else {
                            return '';
                        }
                    }
                },
                {
                    data: "icp",
                    visible: true,
                    render: function (data, type, full, meta) {
                        return '<a href="' + site_url + 'admin/businesses/icps/' + full.id + '" class="btn bg-teal-400 btn-labeled btn-rounded btn-xs" title="Manage ICPS"><b><i class="icon-lan2"></i></b>' + data + '</a>';
                    }
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
//                        if (full.firstname == null && full.lastname == null && full.login_count == 0) {
                        if (full.is_invite == 2 && full.login_count == 0) {
                            status = '<span class="label bg-grey-400">Business Saved</span>';
                        } else if (full.is_invite == 1 && full.login_count == 0) {
                            status = '<span class="label bg-primary">Invitation sent</span>';
                        } else if (full.user_verified == 0) {
                            status = '<span class="label bg-warning">Mail not verified by User</span>';
                        } else if (data == 0) {
                            status = '<span class="label bg-danger">Blocked</span>';
                        } else if (data == 1) {
                            var status = '<span class="label bg-success">Active</span>';
                        }
                        return status;

                    }
                },
                {
                    data: "is_active",
                    visible: true,
                    searchable: false,
                    sortable: false,
                    render: function (data, type, full, meta) {
                        action = '';
                        action += '<ul class="icons-list">';
                        action += '<li class="dropdown">';
                        action += '<a href="#" class="dropdown-toggle" data-toggle="dropdown">';
                        action += '<i class="icon-menu9"></i>';
                        action += '</a>';
                        action += '<ul class="dropdown-menu dropdown-menu-right">';
                        action += '<li>';
                        if (full.user_verified == 0) {
                            action += '<a href="' + site_url + 'admin/businesses/view/' + full.id + '" title="View Business"><i class="icon-eye2"></i>View Business</a>';
                            action += '<a href="' + site_url + 'admin/businesses/delete/' + full.id + '" onclick="return confirm_alert(this)" title="Delete Business"><i class="icon-trash"></i>Delete Business</a>';
                        }
                        else if (full.is_active == 1) {
                            action += '<a href="' + site_url + 'admin/businesses/dashboard/' + full.id + '" title="View Dashboard"><i class="icon-home2"></i>View Dashboard</a>';
                            action += '&nbsp;&nbsp;<a href="' + site_url + 'admin/businesses/edit/' + full.id + '" title="Edit Business"><i class="icon-pencil7"></i>Edit Business</a>';
                            action += '&nbsp;&nbsp;<a href="' + site_url + 'admin/businesses/edit_private_information/' + full.id + '" title="Edit Business Private Information"><i class="icon-key"></i>Edit Business Private Information</a>';
                            action += '&nbsp;&nbsp;<a href="' + site_url + 'admin/businesses/icps/' + full.id + '" title="Manage ICPs"><i class="icon-lan2"></i>Manage Business ICPs</a>';
                            if (full.is_invite == 2) {
                                action += '&nbsp;&nbsp;<a href="' + site_url + 'admin/businesses/invite_mail/' + full.id + '" title="Invite Business" onclick="return invite_alert(this)" data-email="' + full.email + '"><i class="icon-mail5"></i>Invite Business</a>';
                            }
                            action += '&nbsp;&nbsp;<a href="' + site_url + 'admin/businesses/view/' + full.id + '" title="View Business"><i class="icon-eye2"></i>View Business</a>';
                            action += '&nbsp;&nbsp;<a href="' + site_url + 'admin/hotels/index/' + full.id + '" title="Manage Hotels"><i class="icon-city"></i>Manage Hotels</a>';
                            action += '&nbsp;&nbsp;<a href="' + site_url + 'admin/businesses/block/' + full.id + '" onclick="return block_alert(this,\'block\')" title="Block Business"><i class="icon-blocked"></i>Block Business</a>';
                            action += '&nbsp;&nbsp;<a href="' + site_url + 'admin/businesses/delete/' + full.id + '" onclick="return confirm_alert(this)" title="Delete Business"><i class="icon-trash"></i>Delete Business</a>';
                        } else {
                            action += '<a href="' + site_url + 'admin/businesses/block/' + full.id + '" title="Unblock Business" onclick="return block_alert(this,\'unblock\')" ><i class="icon-checkmark4"></i>Unblock Business</a>';
                            action += '&nbsp;&nbsp;<a href="' + site_url + 'admin/businesses/delete/' + full.id + '" onclick="return confirm_alert(this)" title="Delete Business"><i class="icon-trash"></i>Delete Business</a>';
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
    function invite_alert(e) {
        var email = $(e).attr('data-email');
        swal({
            title: "Are you sure?",
            text: "Invitation email will be sent to " + email + " user!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#FF7043",
            confirmButtonText: "Yes, send it!"
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
    function confirm_alert(e) {
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this business!",
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
    function block_alert(e, type) {
        swal({
            title: "Are you sure?",
            text: "The Business will be " + type + "ed!",
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