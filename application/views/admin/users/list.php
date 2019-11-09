<script type="text/javascript" src="assets/admin/js/plugins/media/fancybox.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/tables/datatables/datatables.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/forms/selects/select2.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/notifications/sweet_alert.min.js"></script>
<div class="page-header page-header-default">
    <div class="page-header-content">
        <div class="page-title">
            <h4><i class="icon-users4"></i> <span class="text-semibold">All Users</span></h4>
        </div>
    </div>
    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="<?php echo site_url('admin/home'); ?>"><i class="icon-home2 position-left"></i> Home</a></li>
            <li class="active">Users</li>
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
        <table class="table datatable-basic">
            <thead>
                <tr>
                    <th>Sr No.</th>
                    <th>User Selfie</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Registered Device</th>
                    <th>Registered On</th>
                    <!--<th>Status</th>-->
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
            },
            dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
            order: [[5, "desc"]],
            ajax: site_url + 'admin/users/get_users',
            columns: [
                {
                    data: "sr_no",
                    visible: true,
                    sortable: false,
                },
                {
                    data: "image",
                    visible: true,
                    sortable: false,
                    render: function (data, type, full, meta) {
                        var user_img = '';
                        if (full.image != '' && full.image != null) {
                            user_img = '<a href="' + user_img_site_path + full.image + '" data-popup="lightbox"><img src="assets/timthumb.php?src=' + user_img_site_path + full.image + '&w=60&h=60&q=100&zc=2" height="55px" width="55px"></a>';
                        } else {
                            user_img = '<a href="assets/admin/images/no_logo.png" data-popup="lightbox"><img src="assets/timthumb.php?src="'+site_url+'assets/admin/images/no_logo.png&w=60&h=60&q=100&zc=2" height="55px" width="55px" alt="' + full.firstname + '"></a>';
                        }
                        return user_img;
                    }
                },
                {
                    data: "firstname",
                    visible: true,
                    render: function (data, type, full, meta) {
                        return full.firstname + ' ' + full.lastname;
                    }
                },
                {
                    data: "email",
                    visible: true,
                },
                {
                    data: "device_type",
                    render: function (data, type, full, meta) {
                        if (data == 0) {
                            return 'Android';
                        } else {
                            return 'Iphone';
                        }
                    }
                },
                {
                    data: "created",
                    visible: true,
                },
                {
                    data: "is_delete",
                    visible: true,
                    searchable: false,
                    sortable: false,
                    render: function (data, type, full, meta) {
                        action = '<a href="' + site_url + 'admin/users/delete/' + full.id + '" class="btn border-danger text-danger btn-flat btn-icon btn-rounded btn-xs" onclick="return confirm_alert(this)" title="Delete User"><i class="icon-trash"></i></a>';
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
    function confirm_alert(e) {
        swal({
            title: "Are you sure?",
            text: "User will be deleted!",
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
</script>