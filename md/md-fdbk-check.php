<?php
    /**
     * Process the website feedback form
     */
    include_once "md-utils.php";        //Utility and other functions
    /*
    ** Get some settings.
    */
    $config=$_SERVER['DOCUMENT_ROOT']."/site-config.php";
    if(is_readable($config))
        include_once $config;
    else
        include_once "md-config.php";
    /*
    ** Class to perform Markdown parsing.
    */
    include_once "parsedown.php";
    $parsedown = new Parsedown();
?>
<!DOCTYPE html>
<html lang="<?php if(array_key_exists('LANGUAGE',$MD_SETTINGS)) echo $MD_SETTINGS['LANGUAGE']; else echo 'en-GB'; ?>">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="generator" content="markdown-CMS" />
        <meta name="description" content="The <?php if(array_key_exists('FDBK_DOMAIN',$MD_SETTINGS)) echo $MD_SETTINGS['FDBK_DOMAIN'];?> website feedback form processing." />
        <meta name="robots" content="noindex" />
        <?php
            //Set title
            echo "<title>Website feedback processing - "; if(array_key_exists('FDBK_DOMAIN',$MD_SETTINGS)) echo $MD_SETTINGS['FDBK_DOMAIN']; echo "</title>\n";
            //Set favicon
            echo "<link rel=\"icon\" href=\"{$MD_SETTINGS['FAVICON']}\"/>\n";
        ?>
        <link rel="stylesheet" href="/md/css/normalize.css">
        <link rel="stylesheet" href="/md/css/md.css">
<!-- Google Auto Ads-->
<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<script>(adsbygoogle = window.adsbygoogle || []).push({
    google_ad_client: "ca-pub-7374540092398389",
    enable_page_level_ads: true
});</script>
<!-- End Google Auto Ads -->
    </head>
    <body>
        <?php //Google analytics support
            include_once "md-analytics.php";
        ?>
        <div class="container">
            <?php // Process the header
                include_once 'md-header.php';
            ?>
            <div class="row">
                <div class="nine columns">
                    <p>Processing feedback...</p>
                    <?php 
                        //Set an error message
                        $submission_error = "Sorry, a submission error occurred: ";
                        // Define a filter function for data sanitization
                        function clean_data($data) {
                            // trim whitespace
                            $data = trim($data);
                            // reduce website manipulation via HTML tags
                            $data = htmlspecialchars($data);
                            return $data;
                        }
                        //Check for reCAPTCHA token
                        if (isset($_POST['g-recaptcha-response'])) {
                            $captcha = $_POST['g-recaptcha-response'];
                        } else {
                            $captcha = false;
                        }
                        //If no reCAPTCA token, show an error, otherwise verify data.
                        if (!$captcha) {
                            //Do something with no reCAPTCHA available
                             echo "<p>".$submission_error." No verification data.</p>";
                        } else {
                            if(array_key_exists('RECAPTCHA_SECRET',$MD_SETTINGS)) {
                                $secret=$MD_SETTINGS['RECAPTCHA_SECRET'];

                                //Verify with Google reCAPTCHA servers
                                //Construct query paramters
                                $query_content = http_build_query(array('secret'=>$secret,'response'=>$captcha));
                                //Start curl
                                $curl=curl_init();
                                //Set options for cURL: url, return as value, do a post
                                curl_setopt($curl, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
                                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                                curl_setopt($curl, CURLOPT_POST, true);
                                curl_setopt($curl, CURLOPT_POSTFIELDS,$query_content);
                                //Do the post to Google servers
                                $response=curl_exec($curl);
                                //Close cURL resource, free up system resources
                                curl_close($curl);
                                //Check curl response
                                if ($response === false)
                                    //set error in JSON format
                                    $response='{"success": false, "error-codes": ["connection-failed"]}';
                                // Use json_decode to extract json response
                                $response = json_decode($response);
                                //See if verification failed
                                if ($response->success===false) {
                                    //Do something with failure
                                    echo "<p>".$submission_error." Data verification failed.</p>";
                                } else {
                                    //If the reCAPTCHA is valid use the data
                                    //Otherwise filter out bad submissions
                                    //Change acceptable score as required
                                    if ($response->score <= $fdbk_score) {
                                        //Do something to deny access
                                         echo "<p>".$submission_error." Data check failed.</p>";
                                    } else {
                                        if(isset($_POST['message'])) {
                                            //Sanitize the message
                                            $message=clean_data($_POST['message']);
                                            //Add the senders name
                                            if(isset($_POST['name'])) {
                                                $name=clean_data($_POST['name']);
                                                $message="From: ".$name."\r\n".$message;
                                            }
                                            //Add the source web page
                                            if(isset($_POST['referer'])) {
                                                $message="Regards:".clean_data($_POST['referer'])."\r\n".$message;
                                            }
                                            //Set person to send the message to
                                            if(array_key_exists('FDBK_EMAIL',$MD_SETTINGS)) {
                                                $to=$MD_SETTINGS['FDBK_EMAIL'];
                                                //Use an appropriate email subject
                                                if(array_key_exists('FDBK_DOMAIN',$MD_SETTINGS))
                                                    $subject=$MD_SETTINGS['FDBK_DOMAIN'].' feedback';
                                                else
                                                    $subject='Website feedback';
                                                //Add a from                                   
                                                $headers='From:'.$subject;
                                                //Wordwrap long content
                                                $message=wordwrap($message, 70, "\r\n");
                                                //Send the email
                                                if(mail($to, $subject, $message, $headers)) {
                                                    echo "<p>Thank you ".$name.".<br\>".
                                                        " Your feedback is welcome.</p>";
                                                } else {
                                                    echo "<p>".$submission_error." Failed to send feedback.</p>";
                                                }
                                            } else {
                                                echo "<p>".$submission_error." Failed to compose feedback.</p>";
                                            }
                                        } else {
                                            echo "<p>".$submission_error." No message to submit.</p>";
                                        }
                                    }
                                }
                            } else {
                                echo "<p>".$submission_error." Verification failure.</p>";
                            }
                        }
                        if(isset($_POST['referer'])) {
                            if(array_key_exists('FDBK_DOMAIN',$MD_SETTINGS)) {
                                $domain=$MD_SETTINGS['FDBK_DOMAIN'];
                                $referer_domain=parse_url($_POST['referer'],PHP_URL_HOST);
                                if($referer_domain==$domain)
                                    echo '<a href="'.$_POST['referer'].'">Please click here to return to the previous page.</a>';
                            }
                        }
                    ?>
                </div>
                <div class="three columns">
                    <?php
                        /*
                        ** Process the sidebar
                        */
                        include_once "md-sidebar.php";
                    ?>          
                </div>
            </div>
            <?php
                /*
                ** Process the footer
                */
                include_once "md-footer.php";
            ?>
        </div>
    </body>
</html>