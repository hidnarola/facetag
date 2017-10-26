<script type="text/javascript" src="assets/admin/js/plugins/tables/datatables/datatables.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/forms/selects/select2.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/notifications/sweet_alert.min.js"></script>
<div class="page-header page-header-default">
    <div class="page-header-content">
        <div class="page-title">
            <h4><i class="icon-credit-card"></i> <span class="text-semibold">Manage Transactions</span></h4>
        </div>
    </div>
    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="<?php echo site_url('business/home'); ?>"><i class="icon-home2 position-left"></i> Home</a></li>
            <li class="active">Transactions</li>
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
                    <th>Order Id</th>
                    <th>Transaction Id</th>
                    <th>Amount</th>
                    <th>Payment Status</th>
                    <th>Added On</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>
    <?php $this->load->view('Templates/footer'); ?>
</div>
<script>
    $(function () {
        $('.datatable-basic').dataTable({
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
            order: [[4, "desc"]],
            ajax: site_url + 'business/orders/get_orders',
            columns: [
                {
                    data: "sr_no",
                    visible: true,
                    sortable: false,
                },
//                {
//                    data: "icp_logo",
//                    visible: true,
//                    sortable: false,
//                    render: function (data, type, full, meta) {
//                        var icp_img = '';
//                        if (data != '' && data != null) {
//                            icp_img = '<img src="' + icp_logo + data + '" height="55px" width="55px" alt="' + full.name + '">';
//                        } else {
//                            icp_img = '<img src="assets/admin/images/no_logo.png" height="55px" width="55px" alt="' + full.name + '">';
//                        }
//                        return icp_img;
//                    }
//                },
                {
                    data: "icp_image_id",
                    visible: true,
                },
                {
                    data: "price",
                    visible: true,
                },
                {
                    data: "quantity",
                    visible: true,
                },
                {
                    data: "created",
                    visible: true,
                },
                {
                    data: "status",
                    visible: true,
                    searchable: false,
                    sortable: false,
                    render: function (data, type, full, meta) {
                        action = '<a href="' + site_url + 'business/orders/icp_images/' + full.id + '" class="btn border-info text-info-600 btn-flat btn-icon btn-rounded btn-xs" title="Manage Images"><i class="icon-images2"></i></a>';
                        action += '&nbsp;&nbsp;<a href="' + site_url + 'business/orders/delete/' + full.id + '" class="btn border-danger text-danger btn-flat btn-icon btn-rounded btn-xs" onclick="return confirm_alert(this)" title="Delete Icp"><i class="icon-cross2"></i></a>';
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
</script>