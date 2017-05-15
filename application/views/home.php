<section id="home" class="home-wrap home-default">
    <div id="myCarousel" class="carousel slide main-slider" data-ride="carousel">
        <div class="carousel-inner" role="listbox">
            <div class="item active">
                <img src="<?php echo base_url(); ?>assets/images/AdobeStock_104204655.jpg" alt="Chania">
            </div>

            <div class="item">
                <img src="<?php echo base_url(); ?>assets/images/AdobeStock_31230300.jpg" alt="Chania">
            </div>

            <div class="item">
                <img src="<?php echo base_url(); ?>assets/images/shutterstock_115589419.jpg" alt="Flower">
            </div>

            <div class="item">
                <img src="<?php echo base_url(); ?>assets/images/shutterstock_113815783.jpg" alt="Flower">
            </div>

            <div class="item">
                <img src="<?php echo base_url(); ?>assets/images/AdobeStock_96473480.jpg" alt="Chania">
            </div>
        </div>

        <!--        Left and right controls -->
        <a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
            <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
            <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>
    <div class="black-overlay">
        <div class="container">
            <div class="row">
                <div class="col-md-10 col-md-offset-1">
                    <div class="home-intro">
                        <div class="intro-wrap">
                            <!--                                Header text -->
                            <h1>Welcome to facetag</h1>
                            <h2>The complete solution for Guest photo distribution - bringing your visitor attraction photography into the digital age.</h2>
                            <h5>Easily triple your Guest photography sales by seamlessly integrating our free, state of the art face recognition cloud software. Join free here</h5>
                            <!--                                Subscription form -->
                            <div class="subscribe-wrap">

                                <form id="frmSubscribe" class="mailchimp form-inline" method="post" action="<?php echo site_url('register/subscribe') ?>">

                                    <!--                                        SUBSCRIPTION SUCCESSFUL OR ERROR MESSAGES -->
                                    <h6 class="subscription-success"></h6>
                                    <h6 class="subscription-error"></h6>

                                    <div class="btn-group">
                                        <input id="first_name" name="first_name" type="text" placeholder="First name" class="form-control subscribe-input subscribe-name input-lg" autocomplete="off" required>
                                        <input id="last_name" name="last_name" type="text" placeholder="Last name" class="form-control subscribe-input input-lg" autocomplete="off" required>
                                        <input type="email" name="email" id="subscriber-email" placeholder="Email" class="form-control subscribe-input input-lg" autocomplete="off" required>
                                        <!--                                            SUBSCRIBE BUTTON -->
                                        <button type="submit" id="subscribe-button" class="btn btn-primary btnxs btn-lg">Join now <i class="fa fa-angle-double-right" aria-hidden="true"></i></button>
                                    </div>
                                    <?php echo validation_errors(); ?>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
<section id="how-it-works" class="sections">
    <div class="container">
        <div class="row">
            <div class="heading wow fadeIn animated" data-wow-offset="120" data-wow-duration="1.5s">
                <div class="title text-center"><h1 class="text-center">How does facetag work?</h1></div>
                <div class="separator_wrap"> <div class="separator2"></div></div>
            </div>
            <div class="col-md-6 title">
                <h4>facetag is the perfect image distribution solution for any attraction, nightclub, resort    or event photographer. Enabling the delivery of free or monetized Guest experience     photos in a seamless, online environment.</h4>    
            </div>
            <div class="col-md-6 text-center video">
                <iframe width="560" height="315" src="https://www.youtube.com/embed/IwMAq8u8mE0" frameborder="0" allowfullscreen></iframe>
            </div>
        </div>
    </div>
</section>
<!--whale section-->
<section id="whale">
    <div class="whale-overlay-img">
        <div class="whale-content">

        </div>
    </div>
</section>
<section id="features" class="sections"><!-- Service Section-->
    <div class="container">
        <div class="row coloricon">

            <!--  Heading-->
            <div class="heading wow fadeIn animated" data-wow-offset="120" data-wow-duration="1.5s">
                <div class="subtitle text-center "><h5>By utilizing facetag's state of the art facial recognition algorithm, facetag effortlessly searches and sorts all images uploaded by you and notifies your Guests of matches via an app on their mobile device - allowing them to purchase, share and download their images through the pricing model set by you.</h5></div>
                <div class="separator_wrap"> <div class="separator2"></div></div>
            </div>


            <div class="col-sm-4">
                <div class="features text-center wow fadeInLeft" data-wow-offset="120" data-wow-duration="1.5s">
                    <img src="<?php echo base_url(); ?>assets/images/service1.jpg" alt="">
                    <h4>Increase Revenue</h4>
                    <p>
                        By freeing your Guest photography from physical constraints, you eliminate bottlenecks and missed opportunities by delivering every image to the right person. facetag rids your sales of the narrow window of opportunity that traditional Guest photography suffers from.
                    </p>
                </div>
            </div><!--end 4 col-->

            <div class="col-sm-4">

                <div class="features text-center wow fadeIn" data-wow-offset="120" data-wow-duration="1.5s">
                    <img src="<?php echo base_url(); ?>assets/images/service2.jpg" alt="">
                    <h4>Social Integration</h4>
                    <p>
                        Give your Guests images how they want to use them. With Facetag, Guests can easily share purchased images to Facebook, Instagram and Twitter and by giving you the option to brand the images it offers a huge social media boost for you too.
                    </p>
                </div>
            </div><!--end 4 col-->

            <div class="col-sm-4">
                <div class="features text-center wow fadeInRight" data-wow-offset="120" data-wow-duration="1.5s">
                    <img src="<?php echo base_url(); ?>assets/images/service3.jpg" alt="">
                    <h4>Streamlined & Engaging delivery system</h4>
                    <p>
                        Your Guests simply download the facetag iOS or Android App, take a selfie and then our algorithm does the rest - capable of processing millions of images per second from a total dataset of up to 1 billion faces, facetag ensures you never miss a selling opportunity again with the most accurate facial recognition technology in the world.  
                    </p>
                </div>
            </div><!--end 4 col-->
        </div><!--end row-->
    </div>
</section>
<!--giraffe section-->
<section id="giraffe">
    <div class="giraffe-overlay-img">
        <div class="giraffe-content">

        </div>
    </div>
</section>
<section id="describe" class="sections"> <!-- Describe Section-->
    <div class="container">
        <div class="heading wow fadeIn animated" data-wow-offset="120" data-wow-duration="1.5s">
            <div class="title text-center"><h1 class="text-center">Built for Business</h1></div>
            <div class="separator_wrap"> <div class="separator2"></div></div>
        </div>
        <div class="row">
            <div class="col-md-6 col-sm-6 business-admin-imgs">
                <div class="text-center">
                    <img src="<?php echo base_url(); ?>assets/images/business-admin-dashboard.jpg">
                </div>
            </div>
            <div class="col-md-6 col-sm-6 business-admin-imgs">
                <div class="text-center">
                    <img src="<?php echo base_url(); ?>assets/images/business-admin-profile.png">
                </div>
            </div>
            <div class="col-md-6 col-sm-6">

                <!--  Heading-->
                <div class="heading-left margin-top-0  wow fadeIn" data-wow-offset="120" data-wow-duration="1.5s">
                    <div class="title-half"><h3>Facetag has been built and designed from the ground up specifically to fit the flexibility needed for the entertainment industries.</h3></div>
                    <div class="separator_wrap-left"> <div class="separator2"></div></div>
                </div>

                <div class="describe-details wow fadeInLeft" data-wow-offset="10" data-wow-duration="1.5s">
                    <p>Facetag can be used as a standalone service or alongside your existing Point Of Sale setup seamlessly.</p>
                    <p>Facetag is the perfect solution for Theme Parks, Water Parks, Visitor attractions, Museums, Aquariums, Zoos, Excursions & Tours, NightClubs, Bars, Themed Restaurants, Hotels & Resorts, Sporting Events, Concerts, Festivals, Weddings, Photo-Booths, Conventions and Expo's enhancing visitor engagement and boosting revenue.</p>

                    <!--<p class="margin-bottom-60">&nbsp;</p>-->

                    <div class="describe-list">
                        <div class="describe-list-icon">
                            <i class="fa fa-laptop"></i>
                        </div>
                        <div class="describe-list-content">
                            <h5>Free Account </h5>
                            <p>Signing up couldn't be easier, it's free, and takes just minutes, there is no contract and you can cancel at any time by simply 'logging out' We've built facetag to be as fully automated as possible - upload, drag and drop images and Facetag does all the heavy lifting. We only charge a commission on the photo's you sell through the platform.</p>
                        </div>

                    </div>
                    <div class="describe-list">
                        <div class="describe-list-icon">
                            <i class="fa fa-bar-chart"></i>
                        </div>
                        <div class="describe-list-content">
                            <h5>Intuitive UI and robust management tools </h5>
                            <p>facetag utilizes a simple and practical business focused User Interface that allows you to take complete control of your pricing, packages and products. facetag also offers a wealth of analytics to help you keep track of and improve your Guest photography experience. From KPI tracking of unlimited Image Capture Points and ride photos, Guest check-in's at your venue, in depth statistics and purchase data; facetag is the perfect package.</p>
                        </div>
                    </div>

                </div>

            </div>


            <div class="col-md-6 col-sm-6">
                <div class="text-center describe-images wow fadeInRight" data-wow-offset="10" data-wow-duration="1.5s">
                    <img src="<?php echo base_url(); ?>assets/images/AdobeStock_57267484.jpg" alt="">
                </div>
            </div>

        </div><!--end row-->
    </div><!--end container-->
</section><!--end section-->
<!--green screen section-->
<section id="green-screen">
    <div class="green-screen-overlay-img">
        <div class="green-screen-content">

        </div>
    </div>
</section>
<section id="describes" class="sections"> <!-- Describe Section-->
    <div class="container">

        <div class="row">
            <div class="heading wow fadeIn animated" data-wow-offset="120" data-wow-duration="1.5s">
                <div class="title text-center"><h1 class="text-center">The Facetag App</h1></div>
                <div class="separator_wrap"> <div class="separator2"></div></div>
            </div>
            <div class="col-md-12 store-img-main">
                <div class="col-md-4 col-sm-4 text-center describe-images wow fadeInRight" data-wow-offset="10" data-wow-duration="1.5s">
                    <img src="<?php echo base_url(); ?>assets/images/iphone1.png" alt="">
                </div>
                <div class="col-md-4 col-sm-4 text-center describe-images wow fadeInRight" data-wow-offset="10" data-wow-duration="1.5s">
                    <img src="<?php echo base_url(); ?>assets/images/iphone2.png" alt="">
                </div>
                <div class="col-md-4 col-sm-4 text-center describe-images wow fadeInRight" data-wow-offset="10" data-wow-duration="1.5s">
                    <img src="<?php echo base_url(); ?>assets/images/iphone3.png" alt="">
                </div>
            </div>


            <div class="col-md-12">

                <div class="clearfix"></div>
                <div class="describe-details wow fadeInLeft" data-wow-offset="10" data-wow-duration="1.5s">
                    <p>This is where the magic of your new Guest experience starts. You simply promote the facetag app throughout your venue encouraging your Guests to download the app and take a selfie. Guest signup is streamlined to take less than a minute boosting engagement. Through the app Guests will receive notifications of any matches - all images at this point are watermarked and restricted - once they verify a match they enter into your distribution and pricing model. Chosen images are then digitally delivered allowing them to edit, share or print.</p>
                </div>
            </div>
            <ul class="store-img">
                <li><a href="#"><img src="<?php echo base_url(); ?>assets/images/apple-store.png" alt=""></a></li>
                <li><a href="#"><img class="play-store-img" src="<?php echo base_url(); ?>assets/images/google-store.png" alt=""></a></li>
            </ul>


        </div><!--end row-->
    </div><!--end container-->
</section><!--end section--><section id="timeline" class="timeline-overview">

    <div class="overlay-img">
        <div class="container">
            <div class="row ">

                <!--  Heading-->
                <div class="heading wow fadeIn" data-wow-offset="120" data-wow-duration="1.5s">
                    <div class="title text-center"><h1>About Us</h1></div>
                    <div class="subtitle text-center "><h5>Our team is drawn from many different backgrounds with one unifying vision - translating our technological expertise into the ultimate Guest experience. Our team contains international theme park, resort and hospitality specialists, and experts in artificial intelligence algorithms, machine and deep learning facial recognition software all devoted to the perfect software package.</h5></div>
                    <!--<div class="separator_wrap"> <div class="separator2"></div></div>-->
                </div>


                <!--TIMELINE START -->

                <!-- End Timeline ul --> 
            </div>

        </div>
    </div>

</section>
<?php $this->load->view('service-plugin.php'); ?>
<!--<section class="accordion-nipl sections">
    <div class = "container">
        <div class="row">
            <div class="heading wow fadeIn animated" data-wow-offset="120" data-wow-duration="1.5s">
                <div class="title text-center"><h1 class="text-center">FAQ's</h1></div>
                <div class="separator_wrap"> <div class="separator2"></div></div>
            </div>
            <div class="panel-group" id="accordion">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
                                Why wouldn't the Guests just take screenshots of our images without purchasing?
                            </a>
                        </h4>
                    </div>
                    <div id="collapseOne" class="panel-collapse collapse in">
                        <div class="panel-body">
                            <p>The images are only viewable in thumbnail or half screen. we subtly degrade the image sharpness quality and we then add a watermark until it has been purchased.</p>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">
                                I own a NightClub, is there any cost if I just want to give branded photos away through the app free to my patrons so they can share them on social media giving exposure to my business.
                            </a>
                        </h4>
                    </div>
                    <div id="collapseTwo" class="panel-collapse collapse">
                        <div class="panel-body">
                            <p>No. You can upload upto 3000 images a month free and your brand logo can be displayed on every image. The only cost to you is to effectively promote the facetag app throughout your Venue to ensure as many of the images you upload reach as many as your patrons </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>  end container 

</section>-->
<?php //include 'faq-plugin.php'; ?>
<section id="subscribes" class="sections"><!-- Describe Section-->
    <div class="container">
        <div class="row">
            <div class="col-sm-3 text-right">
                <div class="newsletter-icon">
                    <i class="fa fa-newspaper-o"></i>
                </div>
            </div>
            <div class="col-sm-9">
                <!--Subscription form--> 
                <div class="subscribe-wrap body-subscribe">
                    <h1>Keep me up to date</h1>
                    <p>Sign up below to be the first to hear news about facetag</p>
                    <form id="" class="mailchimp form-inline">

                        <!-- SUBSCRIPTION SUCCESSFUL OR ERROR MESSAGES -->
                        <h6 class="subscription-success"></h6>
                        <h6 class="subscription-error"></h6>

                        <div class="btn-group">
                            <input id="first_name" name="MERGE1" type="text" placeholder="Name" class="form-control subscribe-input subscribe-name input-lg" required>
                            <input type="email" name="email" id="subscriber-email" placeholder="Email" class="form-control subscribe-input input-lg">
                            <!-- SUBSCRIBE BUTTON -->
                            <button type="submit" id="subscribe-button" class="btn btn-primary btnxs btn-lg">Submit <i class="fa fa-envelope-o hidden-xs"></i></button>
                        </div>
                        <div class="message editContent">
                            Your data is treated with complete confidentiality; we don't share your details with anyone.
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</section>

<!--Tweet section-->
<section id="tweets" class="tweets">
    <div class="overlay-img">
        <div class="container">
            <div class="row">
                <div class="col-sm-10 col-sm-offset-1 text-center">
                    <div class="twitter-icon"><i class="fa fa-twitter"></i></div>
                    <div id="tweet"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="contact" class="sections"><!-- Contact form -->
    <div class="container">
        <div class="row contact-2">

            <!-- Heading-->
            <div class="heading wow fadeIn animated" data-wow-offset="120" data-wow-duration="1.5s">
                <div class="title text-center"><h1>Contact Us</h1></div>
                <div class="subtitle text-center "><h5>Contact Facetag below. We will be in touch within 24 hours.</h5></div>
                <div class="separator_wrap"> <div class="separator2"></div></div>
            </div>


            <div class="col-md-12 ">
                <!-- CONTACT FORM -->

                <div id="error-message" class="text-center"></div>

                <form method="post" action="<?php echo site_url('home/contact_us') ?>" class="contact-form" id="contact_form">
                    <div id="fancy-inputs">
                        <div class="col-md-6">

                            <div class="">
                                <label class="input">
                                    <input type="text" name="contact_name" id="contact_name" class="name" required>
                                    <span><span>Name</span></span>
                                </label>

                            </div>

                            <div class="">
                                <label class="input">
                                    <input type="text" name="contact_email" id="contact_email" required>
                                    <span><span>Email</span></span>
                                </label>
                            </div>

                            <div class="">
                                <label class="input">
                                    <input type="text" name="contact_subject" id="contact_subject" required>
                                    <span><span>Subject</span></span>
                                </label>
                            </div></div>
                        <div class="col-md-6">

                            <label class="textarea">
                                <textarea name="contact_message" id="contact_message" cols="30" rows="10"></textarea>
                                <span><span>Message</span></span>
                            </label>

                        </div>
                        <div class="submit-button">
                            <!--<button class="btn btn-primary btnxs btn-lg" type="button" id="submit" name="submit" data-loading-text="Loading..."><i class="fa fa-paper-plane"></i> Send Message</button>-->
                            <button class="btn btn-primary btnxs btn-lg btn-contact-form" id="submit" name="submit"><i class="fa fa-paper-plane"></i> Send Message</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<script src="<?php echo base_url(); ?>assets/js/smoothscroll.js"></script>
<script src="<?php echo base_url(); ?>assets/js/smoothscroll.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/js/plugins/forms/validation/validate.min.js"></script>
<script>
//    jQuery(document).on('submit', "#contactform", function (event) {
//        event.preventDefault();
//        console.log('here');
//    });
//    $(document).on('click','.btn-contact-form',function(e){
//        e.preventdefault();
//       console.log('here'); 
//    });
//    $('.btn-contact-form').click(function() {
//        console.log('here');
//    });
//    function contact_form() {
//        e.p
//        console.log('here');
//        return false;
//    }
    $("#contact_form").validate({
        errorClass: 'error-custom error-input',
        errorElement: 'span',
        errorPlacement: function (error, element) {
            if (element.parents('div').hasClass("checkbox-wrapper")) {
                error.insertAfter(element.next('label').next('span'));
            } else {
                error.insertAfter(element.next('span'));
            }
        },
        rules: {
            contact_name: {
                required: true
            },
            contact_email: {
                required: true,
                email: true,
            },
            contact_subject: {
                required: true
            },
            contact_message: {
                required: true
            }
        },
        submitHandler: function (form) {
            $('.btn-contact-form').prop('disabled', true);
            form.submit();
        },
//        invalidHandler: function () {
//            console.log('eror');
//        }
    });
</script>
<!--<script type="text/javascript">
    $(document).ready(function () {
        $('.landing-slider').owlCarousel({
            loop: true,
            margin: 10,
            nav: true,
            navText: [
                "<i class='fa fa-chevron-left'></i>",
                "<i class='fa fa-chevron-right'></i>",
            ],
            dots: false,
            autoplay: true,
            autoplayTimeout: 4000,
            responsive: {
                0: {
                    items: 1
                },
                600: {
                    items: 1
                },
                1000: {
                    items: 1
                }
            }
        })

    });

</script>-->


