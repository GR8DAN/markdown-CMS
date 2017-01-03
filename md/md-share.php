<?php
    if(isset($MD_SETTINGS['HOME'])) {
        //set local vars
        $page_url=rtrim($MD_SETTINGS['HOME'],"/").Md_BrowserRequest();
        //Just in case, check to see if site title set
        if(array_key_exists('title',$md_meta))
            $page_title=urlencode($md_meta['title']);
        else
            $page_title=urlencode($MD_SETTINGS['SITE_NAME']);
        //First a share prompt
        $social_links = '<img src="/md/images/share.png" alt="Share"/>'; 
        //Twitter
        $social_links .= '<a href="https://twitter.com/share?url='.$page_url.'" target="_blank"><img src="/md/images/tweet-but.png" alt="Submit to Twitter"  onmouseover="this.src=\'/md/images/tweet-but-hov.png\';" onmouseout="this.src=\'/md/images/tweet-but.png\';"/></a>';
        //Facebook
        $social_links .= '<a href="https://www.facebook.com/sharer/sharer.php?u='.$page_url.'" target="_blank"><img src="/md/images/face-but.png" alt="Submit to Facebook"  onmouseover="this.src=\'/md/images/face-but-hov.png\';" onmouseout="this.src=\'/md/images/face-but.png\';"/></a>';
        //Google Plus
        $social_links .= '<a href="https://plus.google.com/share?url='.$page_url.'" target="_blank"><img src="/md/images/gplus-but.png" alt="Submit to Google+"  onmouseover="this.src=\'/md/images/gplus-but-hov.png\';" onmouseout="this.src=\'/md/images/gplus-but.png\';"/></a>';
        //Linkedin
        $social_links .= '<a href="https://www.linkedin.com/shareArticle?mini=true&url='.$page_url.'&title='.$page_title.'" target="_blank"><img src="/md/images/linkin-but.png" alt="Submit to LinkedIn" onmouseover="this.src=\'/md/images/linkin-but-hov.png\';" onmouseout="this.src=\'/md/images/linkin-but.png\';"/></a>';
        //reddit
        $social_links .= '<a href="https://www.reddit.com/submit?url='.$page_url.'&title='.$page_title.'" target="_blank"><img src="/md/images/reddit-but.png" alt="Submit to reddit" onmouseover="this.src=\'/md/images/reddit-but-hov.png\';" onmouseout="this.src=\'/md/images/reddit-but.png\';"/></a>';
        //print
        $social_links .= '<a href="javascript:;" onclick="window.print()"><img src="/md/images/print-but.png" alt="Print Page" onmouseover="this.src=\'/md/images/print-but-hov.png\';" onmouseout="this.src=\'/md/images/print-but.png\';"/></a>';
        //email button optional
        //$social_links .= '<a href="mailto:?subject='.$page_title.'&body='.$page_url.'"><img src="/md/images/email-but.png" alt="Email" onmouseover="this.src=\'/md/images/email-but-hov.png\';" onmouseout="this.src=\'/md/images/email-but.png\';"/></a>';
        echo $social_links;
    }
?>
