<script type="text/javascript" src="assets/admin/js/plugins/media/fancybox.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/tables/datatables/datatables.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/forms/selects/select2.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/notifications/sweet_alert.min.js"></script>
<div class="page-header page-header-default">
    <div class="page-header-content">
        <div class="page-title">
            <h4><i class="icon-images2"></i> <span class="text-semibold">Matched ICP Images</span></h4>
        </div>
    </div>
    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="<?php echo site_url('business/home'); ?>"><i class="icon-home2 position-left"></i> Home</a></li>
            <li><a href="<?php echo site_url('business/icps'); ?>"><i class="icon-lan2 position-left"></i> ICPs</a></li>
            <li><a href="<?php echo site_url('business/icps/icp_images/' . $icp_data['id']); ?>"><i class="icon-images2"></i> ICP Images</a></li>
            <li class="active">Matched Images</li>
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
                    <th>User Image</th>
                    <th>ICP Image</th>
                    <th>User</th>
                    <th>Is Verified by User</th>
                    <th>Matched On</th>
                </tr>
            </thead>
        </table>
    </div>
    <?php $this->load->view('Templates/footer'); ?>
</div>
<script>
    $(function () {
        $('.datatable-basic').dataTable({
            bFilter: false,
            bInfo: false,
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
            order: [[5, "desc"]],
            ajax: site_url + 'business/icps/get_matched_images/<?php echo $icp_data['id'] ?>',
            columns: [
                {
                    data: "sr_no",
                    visible: true,
                    sortable: false,
                },
                {
                    data: "user_image",
                    visible: true,
                    render: function (data, type, full, meta) {
                        return '<a href="' + user_img_site_path + data + '" data-popup="lightbox"><img src="' + user_img_site_path + data + '" alt="' + full.firstname + ' ' + full.lastname + '" class="img-rounded img-preview"></a>';
                    },
                    sortable: false,
                },
                {
                    data: "icp_image",
                    visible: true,
                    render: function (data, type, full, meta) {
                        return '<a href="' + icp_image_path + data + '" data-popup="lightbox"><img src="' + icp_image_path + data + '" alt="" class="img-rounded img-preview"></a>';
                    },
                    sortable: false,
                },
                {
                    data: "firstname",
                    visible: true,
                    searchable: false,
                    sortable: false,
                    render: function (data, type, full, meta) {
                        status = data + ' ' + full.lastname;
                        return status;
                    }
                },
                {
                    data: "is_user_verified",
                    visible: true,
                    searchable: false,
                    sortable: false,
                    render: function (data, type, full, meta) {
                        status = '';
                        if (data == 1) {
                            status = '<span class="label bg-success">Verified</span>';
                        } else {
                            var status = '<span class="label bg-danger">Not Verified</span>';
                        }
                        return status;
                    }
                },
                {
                    data: "created",
                    visible: true,
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

</script>