<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo $title; ?></title>

        <base href="<?php echo base_url(); ?>">
        <link href="assets/images/favicon.ico" rel='shortcut icon' type='image/x-icon'>

        <meta name="description" content="">
        <meta name="keywords" content="">
        <meta name="author" content="Paul Suggitt">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <meta name="robots" content="noindex, nofollow">
        <meta name="format-detection" content="telephone=no" />
        <!--<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,300,700' rel='stylesheet' type='text/css'>-->

        <link rel="apple-touch-icon" href="apple-touch-icon.png">
        <!-- Place favicon.ico in the root directory -->

        <link rel="stylesheet" href="assets/css/bootstrap.min.css">
        <link rel="stylesheet" href="assets/css/icon-font.css">
        <link rel="stylesheet" href="assets/css/plugins.css">
        <link rel="stylesheet" href="assets/css/style.css">
        <link href="assets/css/iconfont.css" rel="stylesheet">

        <link rel="stylesheet" href="assets/css/responsive.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
        <link href="<?php echo base_url(); ?>assets/css/owl.carousel.css" rel="stylesheet">


        <!-- COLORS -->
        <link href="assets/css/colors/orange.css" rel="stylesheet" id="colors">
        <!--<script src="assets/js/vendor/modernizr-2.8.3-respond-1.4.2.min.js"></script>-->
        <script src="assets/js/vendor/modernizr-custom.js"></script>
        <!--<script src="../../..//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>-->
        <script src="assets/js/vendor/jquery-1.11.2.min.js"></script>
        <script src="assets/js/vendor/bootstrap.min.js"></script>
        <script src="assets/js/jquery.localScroll.min.js"></script>
        <script src="assets/js/jquery.scrollTo.min.js"></script>
        <script src="assets/js/jquery.counterup.min.js"></script>
        <script src="assets/js/smoothscroll.js"></script>
        <script src="assets/js/twitterFetcher_min.js"></script>
        <script src="assets/js/jquery-contact.js"></script>
        <script src="assets/js/wow.min.js"></script>
        <script src="assets/js/plugins.js"></script>
        <script src="assets/js/main.js"></script>

        <script type="text/javascript" src="assets/js/bootstrap-multiselect/bootstrap-multiselect.js"></script>
        <link rel="stylesheet" href="assets/css/bootstrap-multiselect/bootstrap-multiselect.css" type="text/css"/>
        <script src='https://www.google.com/recaptcha/api.js'></script>


        <!--Google Analytics Here-->

        <!-- End Google Analytics -->

    </head>
    <body data-spy="scroll" data-target="#main-navbar">
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <!--<div class='preloader'><div class='loaded'>&nbsp;</div></div>-->


        <div id="page" class="page">

            <div class="mainnav">
                <nav class="navbar fixed-nav navbar-default  navbar-fixed-top" id="main-navbar">
                    <div class="container">
                        <div class="row">
                            <div class="navbar-header">
                                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse">
                                    <span class="sr-only">Toggle navigation</span>
                                    <span class="icon-bar"></span>
                                    <span class="icon-bar"></span>
                                    <span class="icon-bar"></span>
                                </button>
                                <a href="<?php echo site_url('/') ?>" class="navbar-brand top-logo"><img src="assets/images/logo-new.png" alt="logo"></a>
                            </div>  <!--end navbar-header -->

                            <div class="collapse navbar-collapse" id="navbar-collapse">
                                <ul class="social-menu nav navbar-nav navbar-right visible-m">
                                    <li class="propClone"><a href="<?php echo site_url('login') ?>" class="btn btn-default btnxs">Login</a></li>
                                    <li class="propClone"><a href="<?php echo site_url('register') ?>" class="btn btn-primary btnxs">Join</a></li>
                                    <li class="mobile-number"><i class="fa fa-phone" aria-hidden="true"></i><span>1300 290 993</span></li>
                                </ul>
                                <ul class="nav navbar-nav navbar-left">
                                    <li class="propClone"><a href="#home">Home</a></li>
                                    <li class="propClone"><a href="#how-it-works">How It Works</a></li>
                                    <!--<li class="propClone"><a href="#features">Features</a></li>-->
                                    <li class="propClone"><a href="#describe">Built for Business</a></li>
                                    <li class="propClone"><a href="#describes">The App</a></li>
                                    <!--<li class="propClone"><a href="#service">Service</a></li>-->
                                    <li class="propClone"><a href="#contact">Contact Us</a></li>
                                </ul>
                                <ul class="social-menu nav navbar-nav navbar-right hidden-m">
                                    <li class="propClone"><a href="<?php echo site_url('login') ?>" >Login</a></li>
                                    <li class="propClone"><a href="<?php echo site_url('register') ?>" >Join</a></li>
                                    <li class="mobile-number"><i class="fa fa-phone" aria-hidden="true"></i><span>1300 290 993</span></li>
                                </ul>

                            </div>  <!--end collapse -->
                        </div>
                    </div>  <!--end container -->
                </nav>
            </div>
            <!-- Main content -->
            <div>
                <?php echo $body; ?>
            </div>
            <!-- /main content -->
            <footer id="footer" class="footer-default"> 

                <div class="container">
                    <div class="row">
                        <div class="footer-wrap">
                            <div class="col-md-12 text-center">
                                <div class="footer-logo"><img src="assets/images/logo-new.png" alt=""></div>
                            </div>
                            <div class="col-md-12 footer-bottom">
                                <div class="col-md-12">
                                    <p class="copyright text-center">Copyright &copy; <?php echo date('Y'); ?> facetag Pty Ltd. All Rights Reserved. International (PCT) Patent Pending</p>
                                </div>
                                <div class="col-md-12">
                                    <div class="social-link">
                                        <a href="https://www.facebook.com/facetagApp/"><i class="fa fa-facebook"></i></a>
                                        <a href="https://twitter.com/FacetagApp"><i class="fa fa-twitter"></i></a>
                                        <a href="https://www.instagram.com/facetagapp/"><i class="fa fa-instagram"></i></a>
                                        <a href="https://www.youtube.com/channel/UCXka9gEAkrYsU3qkAOKvelw"><i class="fa fa-youtube"></i></a>
                                    </div>
                                </div>
                            </div> 
                        </div>
                    </div> 
                </div>
            </footer>
        </div>
    </body>
</html>