<script type="text/javascript" src="assets/admin/js/plugins/media/fancybox.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/tables/datatables/datatables.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/forms/selects/select2.min.js"></script>
<script type="text/javascript" src="assets/admin/js/plugins/notifications/sweet_alert.min.js"></script>
<!-- Stripe.js to collect payment details -->
<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
<style>
    /* Start by setting display:none to make this hidden.
   Then we position it in relation to the viewport window
   with position:fixed. Width, height, top and left speak
   for themselves. Background we set to 80% white with
   our animation centered, and no-repeating */
    .modal {
        display:    none;
        position:   fixed;
        z-index:    1000;
        top:        0;
        left:       0;
        height:     100%;
        width:      100%;
        background: rgba( 255, 255, 255, .6 ) 
            url('assets/images/loader.gif') 
            50% 50% 
            no-repeat;
    }

    /* When the body has the loading class, we turn
       the scrollbar off with overflow:hidden */
    body.loading {
        overflow: hidden;   
    }

    /* Anytime the body has the loading class, our
       modal element will be visible */
    body.loading .modal {
        display: block;
    }
</style>
<div class="page-header page-header-default">
    <div class="page-header-content">
        <div class="page-title">
            <h4><i class="icon-office"></i> <span class="text-semibold"><?php echo $business_name; ?> : Weekly Invoices</span></h4>
        </div>
    </div>
    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="<?php echo site_url('admin/home'); ?>"><i class="icon-home2 position-left"></i> Home</a></li>
            <li><a href="<?php echo site_url('admin/invoice'); ?>"><i class="icon-office position-left"></i> Business</a></li>
            <li class="active"><?php echo $business_name; ?> : Weekly Invoices</li>
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
        <form action="" method="POST" id="payment-form">
            <input type="hidden" name="paypal_email_address" id="paypal_email_address" value="<?php echo $business_settings['paypal_email_address'] ?>">
            <input type="hidden" name="account_name" id="account_name" value="<?php echo $business_settings['account_name'] ?>">
            <input type="hidden" name="bsb" id="bsb" value="<?php echo $business_settings['bsb'] ?>">
            <input type="hidden" name="account_number" id="account_number" value="<?php echo $business_settings['account_number'] ?>">
            <input type="hidden" name="token_id" id="token_id" value="">
        </form>
        <table class="table datatable-basic">
            <thead>
                <tr>
                    <th>Sr No.</th>
                    <th>Invoice Period</th>
                    <th>Total Orders</th>
                    <th>Total ICPs</th>
                    <th>Total Amount</th>
                    <th>Total Payment</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($invoice_list) {
                    $i = 1;
                    foreach ($invoice_list as $row) {
                        ?>
                        <tr>
                            <td><?php echo $i; ?></td>
                            <td><?php echo $row["start_date"] . ' to ' . $row["end_date"]; ?></td>
                            <td><?php echo $row["total_cart_orders"]; ?></td>
                            <td><?php echo $row["total_images"]; ?></td>
                            <td><?php echo $row["weekly_total_amount"]; ?></td>
                            <td><?php echo $row["weekly_total_payment"]; ?></td>
                            <td>
                                <?php
                                $status = '<span class="label bg-danger">Not Transfer</span>';
                                echo $status;
                                ?>
                            </td>
                            <td class="text-center">
                                <ul class="icons-list">
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                            <i class="icon-menu9"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-right">
                                            <li>
                                                <a href="<?php echo site_url() . 'admin/invoice/weekly_orders/' . $row["businessId"] . '/' . $row["start_date"] . 'to' . $row["end_date"]; ?>" title="View All Orders"><i class="icon-eye4 text-teal-600"></i>View All Orders</a>
                                                <?php if ($row["weekly_total_payment"] != 0) { ?>
                                                    <a onclick="transfer_payment(this)" data-startdate="<?php echo $row["start_date"]; ?>" data-enddate="<?php echo $row["end_date"]; ?>"  data-href="<?php echo site_url() . 'admin/invoice/transfer_payment/' . $row["businessId"]; ?>" href="javascript:void(0);" title="Transfer Payment with Stripe"><i class="icon-wallet text-blue-600"></i> Pay with Stripe</a>
                                                    <?php if (isset($business_settings['paypal_email_address']) && $business_settings['paypal_email_address'] != "") { ?>
                                                        <a onclick="paypal_transfer_payment(this)" data-startdate="<?php echo $row["start_date"]; ?>" data-enddate="<?php echo $row["end_date"]; ?>"  data-href="<?php echo site_url() . 'admin/invoice/paypal_transfer_payment/' . $row["businessId"]; ?>" href="javascript:void(0);" title="Transfer Payment with PayPal"><i class="icon-wallet text-slate-600"></i> Pay with PayPal</a>
                                                    <?php } ?>
                                                <?php } ?>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </td>
                        </tr>
                        <?php
                        $i++;
                    }
                } else {
                    echo "<td colspan='9'><center>No business found</center></td>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <?php $this->load->view('Templates/footer'); ?>
</div>
<div class="modal"><!-- Place at bottom of page --></div>
<script>
    var token = '';
    var start_date = '';
    var end_date = '';
    var url = '';
    // Set your Stripe publishable API key here
    Stripe.setPublishableKey('pk_test_JbP14VMrlPG6ep0gEUodtBNj');

    function stripeResponseHandler(status, response) {
        if (response.error) {
            alert("error!");
        } else { // Token was created!
            // Get the token ID:
            token = response.id;
            $("#token_id").val(token);
            $.ajax({
                url: url,
                type: 'POST',
                data: {invoice_period: start_date + 'to' + end_date, token_id: token, amount: 1},
                success: function (data) {
                    alert(data);
                    console.log(data);
                    swal("Done!", "You have successfully transfer payment!", "success");
                }
            });
        }
    }
    // Lightbox
    $('[data-popup="lightbox"]').fancybox({
        padding: 3
    });
    $(function () {
        $('.datatable-basic').dataTable({
            autoWidth: false,
            "aoColumnDefs": [{"bSortable": false, "aTargets": [0, 6]}],
            pageLength: 100,
            language: {
                search: '<span>Filter:</span> _INPUT_',
                lengthMenu: '<span>Show:</span> _MENU_',
                paginate: {'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;'},
                searchPlaceholder: "search"
            },
            dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
            order: [[1, "desc"]]
        });
        $('.dataTables_length select').select2({
            minimumResultsForSearch: Infinity,
            width: 'auto'
        });
    });
    function transfer_payment(e) {
        swal({
            title: "Are you sure?",
            text: "You are about to transfer Payment to this business!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#FF7043",
            confirmButtonText: "Yes, please transfer!"
        },
                function (isConfirm) {
                    if (!isConfirm) {
                        return;
                    }
                    // Request a token from Stripe:
                    var bankAccountParams = {
                        country: "AU",
                        currency: "aud",
                        routing_number: $('#bsb').val(),
                        account_number: $('#account_number').val(),
                        account_holder_name: $('#account_name').val(),
                        account_holder_type: "individual"
                    }
                    start_date = $(e).data('startdate');
                    end_date = $(e).data('enddate');
                    url = $(e).data('href');
                    Stripe.bankAccount.createToken(bankAccountParams, stripeResponseHandler);
                });
        return false;
    }
    
    // transfer payment with paypal
    function paypal_transfer_payment(e) {
        swal({
            title: "Are you sure?",
            text: "You are about to transfer Payment to this business!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#FF7043",
            confirmButtonText: "Yes, please transfer!"
        },
                function (isConfirm) {
                    if (!isConfirm) {
                        return;
                    }
                    // Request a token from Stripe:
                    var paypal_email_address = $('#paypal_email_address').val();
                    start_date = $(e).data('startdate');
                    end_date = $(e).data('enddate');
                    url = $(e).data('href');
                    $body = $("body");
                    $('.loading').show();
                    $body.addClass("loading");
                    $.ajax({
                        url: url,
                        type: 'POST',
                        dataType: "json",
                        data: {paypal_email_address: paypal_email_address, invoice_period: start_date + 'to' + end_date, amount: '1'},
                        success: function (data) {
                            $body.removeClass("loading");
                            if (data.result == "success") {
                                swal("Done!", "You have successfully transfer payment!", "success");
                            } else {
                                swal("Oops...", "Transfer payment failed, Please try again!", "error");
                            }
                        }
                    });
                });
        return false;
    }
    
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
                    } else {
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
                    } else {
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
                    } else {
                        return false;
                    }
                });
        return false;
    }
</script>