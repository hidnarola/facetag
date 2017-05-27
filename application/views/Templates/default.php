<!DOCTYPE html>
<html lang="en">
    <head>
        <?php
        $this->load->view('Templates/header');
        ?>
        <script type="text/javascript">
            //-- Set common javascript vairable
            var site_url = "<?php echo site_url() ?>";
            var base_url = "<?php echo base_url() ?>";
            var profile_img_path = "<?php echo PROFILE_IMAGES ?>";
            var business_logo_path = "<?php echo BUSINESS_LOGO_IMAGES ?>";
            var hotel_images_path = "<?php echo HOTEL_IMAGES ?>";
            var icp_image_path = "<?php echo ICP_IMAGES ?>";
            var business_promo_feature_img_path = "<?php echo BUSINESS_PROMO_IMAGES ?>";
            var icp_logo = "<?php echo ICP_LOGO ?>";
            var user_img_site_path = "<?php echo base_url() . USER_IMAGE_SITE_PATH ?>";

            $(document).ready(function () {
                //--Hide the alert message 
                window.setTimeout(function () {
                    $(".hide-msg").fadeTo(500, 0).slideUp(500, function () {
                        $(this).remove();
                    });
                }, 7000);
            });
        </script>
    </head>
    <body>
        <!-- Main navbar -->
        <div class="navbar navbar-inverse">
            <div class="navbar-header">
                <?php if ($this->session->userdata('facetag_admin')['user_role'] == 1) { ?>
                    <a class="navbar-brand" href="<?php echo site_url('admin/home'); ?>" style="padding: 5px 20px">
                        <!--<img src="assets/images/logo-dark.png"/>-->
                        <img src="assets/images/logo-admin-1.png" alt="" style="height: 30px;">
                        <!--<img src="assets/admin/images/logo-light.png">-->
                    </a>
                <?php } else { ?>
                    <a class="navbar-brand" href="<?php echo site_url('business/home'); ?>" style="padding: 5px 20px">
                        <!--<img src="assets/admin/images/logo_light.png" alt="">-->
                        <img src="assets/images/logo-admin-1.png" alt="" style="height: 30px;">
                    </a>
                <?php } ?>
                <ul class="nav navbar-nav visible-xs-block">
                    <li><a data-toggle="collapse" data-target="#navbar-mobile"><i class="icon-tree5"></i></a></li>
                    <li><a class="sidebar-mobile-main-toggle"><i class="icon-paragraph-justify3"></i></a></li>
                </ul>
            </div>
            <div class="navbar-collapse collapse" id="navbar-mobile">
                <ul class="nav navbar-nav">
                    <li><a class="sidebar-control sidebar-main-toggle hidden-xs"><i class="icon-paragraph-justify3"></i></a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown dropdown-user">
                        <a class="dropdown-toggle" data-toggle="dropdown">
                            <?php if ($this->session->userdata('facetag_admin')['profile_image'] != '' && file_exists(PROFILE_IMAGES . $this->session->userdata('facetag_admin')['profile_image'])) { ?>
                                <img src="<?php echo PROFILE_IMAGES . $this->session->userdata('facetag_admin')['profile_image'] ?>" alt="<?php echo $this->session->userdata('facetag_admin')['firstname'] ?>">
                            <?php } else { ?>
                                <img src="assets/admin/images/placeholder.jpg" alt="<?php echo $this->session->userdata('facetag_admin')['firstname'] ?>">
                            <?php } ?>
                            <span><?php echo $this->session->userdata('facetag_admin')['firstname'] ?></span>
                            <i class="caret"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <?php if ($this->session->userdata('facetag_admin')['user_role'] == 1) { ?>
                                <li><a href="<?php echo site_url('admin/change_password') ?>"><i class="icon-cog5"></i> Account settings</a></li>
                                <li><a href="<?php echo site_url('admin_logout'); ?>"><i class="icon-switch2"></i> Logout</a></li>
                            <?php } else { ?>
                                <li><a href="<?php echo site_url('business/account_profile') ?>"><i class="icon-cog5"></i> Manage Profile</a></li>
                                <li><a href="<?php echo site_url('business/change_password') ?>"><i class="icon-key"></i> Change password</a></li>
                                <li><a href="<?php echo site_url('logout') ?>"><i class="icon-switch2"></i> Logout</a></li>
                            <?php } ?>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
        <!-- /main navbar -->
        <!-- Page container -->
        <div class="page-container">
            <!-- Page content -->
            <div class="page-content">
                <!-- Main sidebar -->
                <div class="sidebar sidebar-main">
                    <div class="sidebar-content">
                        <!-- User menu -->
                        <div class="sidebar-user">
                            <div class="category-content">
                                <div class="media">
                                    <?php if ($this->session->userdata('facetag_admin')['profile_image'] != '' && file_exists(PROFILE_IMAGES . $this->session->userdata('facetag_admin')['profile_image'])) { ?>
                                        <a class="media-left"><img src="<?php echo PROFILE_IMAGES . $this->session->userdata('facetag_admin')['profile_image'] ?>" class="img-circle img-sm" alt="<?php echo $this->session->userdata('facetag_admin')['firstname'] ?>"></a>
                                    <?php } else { ?>
                                        <a class="media-left"><img src="assets/admin/images/placeholder.jpg" class="img-circle img-sm" alt="<?php echo $this->session->userdata('facetag_admin')['firstname'] ?>"></a>
                                    <?php } ?>
                                    <div class="media-body">
                                        <?php if ($this->session->userdata('facetag_admin')['user_role'] == 1) { ?>
                                            <span class="media-heading text-semibold">facetag</span>
                                            <div class="text-size-mini text-muted">
                                                <i class="icon-user text-size-small"></i> &nbsp;Admin
                                            </div>
                                        <?php } else { ?>
                                            <span class="media-heading text-semibold"><?php echo $this->session->userdata('facetag_admin')['firstname'] . ' ' . $this->session->userdata('facetag_admin')['lastname'] ?></span>
                                            <div class="text-size-mini text-muted">
                                                <i class="icon-user text-size-small"></i> &nbsp;Business Admin
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /user menu -->

                        <!-- Main navigation -->
                        <div class="sidebar-category sidebar-category-visible">
                            <div class="category-content no-padding">
                                <ul class="navigation navigation-main navigation-accordion">
                                    <?php
                                    $controller = $this->router->fetch_class();
                                    $action = $this->router->fetch_method();
                                    ?>

                                    <?php if ($this->session->userdata('facetag_admin')['user_role'] == 1) { ?>

                                        <li class="<?php echo ($controller == 'home' && $action == 'index') ? 'active' : ''; ?>"><a href="<?php echo site_url('admin/home'); ?>"><i class="icon-home4"></i> <span>Dashboard</span></a></li>
                                        <li class="<?php echo ($controller == 'businesses' || $controller == 'hotels') ? 'active' : ''; ?>"><a href="<?php echo site_url('admin/businesses/'); ?>"><i class="icon-office"></i> <span>Manage Businesses</span></a></li>
                                        <li class="<?php echo ($controller == 'settings') ? 'active' : ''; ?>"><a href="<?php echo site_url('admin/settings') ?>"><i class="icon-cog3"></i> <span>Settings</span></a></li>
                                        <li class="<?php echo ($controller == 'logs') ? 'active' : ''; ?>"><a href="<?php echo site_url('admin/logs') ?>"><i class="icon-history"></i> <span>Business User Logs</span></a></li>
                                        <li class="<?php echo ($controller == 'subscribed_users') ? 'active' : ''; ?>"><a href="<?php echo site_url('admin/subscribed_users') ?>"><i class="icon-users"></i> <span>Subscribed Users</span></a></li>
                                        <li class="<?php echo ($controller == 'users') ? 'active' : ''; ?>"><a href="<?php echo site_url('admin/users') ?>"><i class="icon-users4"></i> <span>Users</span></a></li>
                                        <li class="<?php echo ($controller == 'orders') ? 'active' : ''; ?>"><a href="<?php echo site_url('admin/orders'); ?>"><i class="icon-list2"></i> <span>Manage Orders</span></a></li>
                                        <li class="<?php echo ($controller == 'invoice') ? 'active' : ''; ?>"><a href="<?php echo site_url('admin/invoice'); ?>"><i class="icon-list3"></i> <span>Manage Payment</span></a></li>
                                        <li><a href="<?php echo site_url('admin_logout'); ?>"><i class="icon-switch2"></i> <span>Logout</span></a></li>

                                    <?php } else if ($this->session->userdata('facetag_admin')['user_role'] == 2) { ?>

                                        <li class="<?php echo ($controller == 'home' && $action == 'index') ? 'active' : ''; ?>"><a href="<?php echo site_url('business/home'); ?>"><i class="icon-home4"></i> <span>Dashboard</span></a></li>
                                        <li class="<?php echo ($this->uri->segment(2) == 'profile') ? 'active' : ''; ?>"><a href="<?php echo site_url('business/profile'); ?>"><i class="icon-profile"></i> <span>Business Profile</span></a></li>
                                        <li class="<?php echo ($this->uri->segment(2) == 'private_information') ? 'active' : ''; ?>"><a href="<?php echo site_url('business/private_information'); ?>"><i class="icon-profile"></i> <span>Business Private Information</span></a></li>
                                        <li class="<?php echo ($controller == 'hotels') ? 'active' : ''; ?>"><a href="<?php echo site_url('business/hotels/index/'.$this->session->userdata('facetag_admin')['business_id']); ?>"><i class="icon-city"></i> <span>Manage Hotels</span></a></li>
                                        <li class="<?php echo ($controller == 'icps') ? 'active' : ''; ?>"><a href="<?php echo site_url('business/icps'); ?>"><i class="icon-lan2"></i> <span>Manage ICPs</span></a></li>
                                        <li class="<?php echo ($controller == 'orders') ? 'active' : ''; ?>"><a href="<?php echo site_url('business/orders'); ?>"><i class="icon-list2"></i> <span>Manage Orders</span></a></li>
                                        <li><a href="<?php echo site_url('logout'); ?>"><i class="icon-switch2"></i> <span>Logout</span></a></li>

                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                        <!-- /main navigation -->
                    </div>
                </div>
                <!-- /main sidebar -->
                <!-- Main content -->
                <div class="content-wrapper">
                    <!-- Page header -->
                    <?php echo $body; ?>
                </div>
                <!-- /main content -->
            </div>
            <!-- /page content -->
        </div>
        <!-- /page container -->
    </body>
</html>
