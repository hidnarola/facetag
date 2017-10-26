<script type="text/javascript" src="assets/admin/js/plugins/media/fancybox.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/tables/datatables/datatables.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/forms/selects/select2.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/notifications/sweet_alert.min.js"></script>
<div class="page-header page-header-default">
    <div class="page-header-content">
        <div class="page-title">
            <h4><i class="icon-city"></i> <span class="text-semibold">Manage Hotels of '<?php echo $business_data['name'] ?>'</span></h4>
        </div>
    </div>
    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="<?php echo site_url('admin/home'); ?>"><i class="icon-home2 position-left"></i> Home</a></li>
            <li><a href="<?php echo site_url('admin/businesses'); ?>"><i class="icon-office position-left"></i> Businesses</a></li>
            <li class="active">Hotels</li>
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
            <a href="<?php echo site_url('admin/hotels/add/' . $business_data['id']); ?>" class="btn btn-success btn-labeled"><b><i class="icon-plus-circle2"></i></b> Add Hotel</a>
        </div>
        <table class="table datatable-basic">
            <thead>
                <tr>
                    <th>Sr No.</th>
                    <th>Hotel Image</th>
                    <th>Hotel Name</th>
                    <th>Hotel Address</th>
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
                paginate: {'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;'}
            },
            dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
            order: [[3, "desc"]],
            ajax: site_url + 'admin/hotels/get_hotels/<?php echo $business_data['id'] ?>',
            columns: [
                {
                    data: "sr_no",
                    visible: true,
                    sortable: false,
                },
                {
                    data: "hotel_pic",
                    visible: true,
                     render: function (data, type, full, meta) {
                        var hotel_img = '';
                        if (full.hotel_pic != '' && full.hotel_pic != null) {
                            hotel_img = '<a href="' + site_url + hotel_images_path + full.hotel_pic + '" data-popup="lightbox"><img src="assets/timthumb.php?src=' + site_url + hotel_images_path + full.hotel_pic + '&w=60&h=60&q=100&zc=2"></a>';
                        } else {
                            hotel_img = '<a href="assets/admin/images/no_logo.png" data-popup="lightbox"><img src="assets/timthumb.php?src='+site_url+'assets/admin/images/no_logo.png&w=55&h=55&q=100&zc=2" height="55px" width="55px" alt="' + full.name + '"></a>';
                        }
                        return hotel_img;
                    },
                    sortable: false,
                },
                {
                    data: "name",
                    visible: true,
                },
                {
                    data: "address",
                    visible: true,
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
                        status = '';
                        if (data == 1) {
                            status = '<span class="label bg-danger">Deleted</span>';
                        } else if (data == 0) {
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
                        action = '<a href="' + site_url + 'admin/hotels/edit/<?php echo $business_data['id'] ?>/' + full.id + '" class="btn border-primary text-primary btn-flat btn-icon btn-rounded btn-xs" title="Edit Business"><i class="icon-pencil3"></i></a>';
                         if (full.is_delete == 0) {
                        action += '&nbsp;&nbsp;<a href="' + site_url + 'admin/hotels/delete/' + full.id + '" class="btn border-danger text-danger btn-flat btn-icon btn-rounded btn-xs" onclick="return confirm_alert(this)" title="Delete Business"><i class="icon-cross2"></i></a>';
                    }
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
            text: "You will not be able to recover this hotel!",
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