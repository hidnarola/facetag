<!DOCTYPE html>
<html lang="en">
    <head>
        <link href='https://fonts.googleapis.com/css?family=Raleway:400,400italic,700,700italic,600,600italic' rel='stylesheet' type='text/css'>
        <style type="text/css">
            body {background-color: #eee; margin:0; padding:0; -webkit-font-smoothing: antialiased;font-family: Georgia, Times, serif;padding:0 15px;}
            table {border-collapse: collapse;}	  
        </style>
    </head>
    <body>
        <div class="wrapper" style="border: 1px solid;background-color:#fff;padding:0;max-width: 550px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);margin: 20px auto;border-radius:3px;overflow:hidden;">
            <div style="padding:30px; margin:0; display: block; box-shadow: 0 1px 12px 0px rgba(51, 51, 51, 0.23);">
                <h3 style="color:#fff;font-size:24px;margin:0;padding:0;font-family:Arial, Helvetica, sans-serif;font-weight:400;">
                    <center>
                        <img src="<?php echo base_url() ?>assets/images/logo-dark.png">
                    </center>
                </h3>
            </div>
            <div style="padding:30px;margin:0; display: block;vertical-align:top; text-align:left;">
                <p style='margin: 1em 0; '>
                    Hello <?php echo $firstname . " " . $lastname; ?><br><br>
                    Welcome to facetag!<br><br>
                    Please click on the link below to verify your email address and confirm your request to register for facetag.<br><br>
                    If you have not requested access to Facetag's facial recognition photo distribution platform please disregard this email.
                </p>
                <hr>
                <p style='margin: 1em 0; '>
                    <a href="<?php echo $url ?>" style="padding: 6px 20px;display:inline-block;color:#fff;text-decoration: none; background-color: #ff6600;border-bottom: 3px solid #000;border-radius: 20px;font-size: 16px;line-height: 16px; text-transform: uppercase;">Click me!</a>
                </p>
            </div>

            <div style="padding:20px; margin:0; text-align:center; background:#F5F5F5;" >
                <p style="font-family:Arial, Helvetica, sans-serif; color:#666; font-size:13px; font-weight:400; line-height:16px; padding:0; margin:0 0 10px;">
                    &copy; <?php echo date('Y') ?> - Facetag
                </p>
            </div>
        </div>
    </body>
</html>