<?php
    // Only add comments if config setup
    if(isset($MD_SETTINGS['HOME'])) {
        //see if article has enabled comments
        if(array_key_exists('comments',$md_meta)) {
            if(empty($md_meta["comments"])) {
                $md_meta["comments"]="no";
            }
            if(strtolower($md_meta['comments']) != "no") {
                //page url for identity for Disqus or Facebook comments
                $page_url=rtrim($MD_SETTINGS['HOME'],"/").Md_BrowserRequest();
                if(array_key_exists('FBK_ADMIN',$MD_SETTINGS)) {
                    //fbk comments
                    echo "<div class=\"fb-comments\" data-href=\"".$page_url."\" data-width=\"100%\" data-numposts=\"10\"></div>\n";
                } else {
                    //Disqus comments          
                    //Get the comment code and add the site settings
                    $comments_code = Md_ProcessText("md-disqus.txt", NULL);
                    // Set the Disqus page URL and site shortname
                    $comments_code = str_replace("PAGE_URL", $page_url, $comments_code);
                    if(array_key_exists('pageId',$md_meta))
                        $comments_code = str_replace("PAGE_IDENTIFIER", $md_meta['comments'], $comments_code);
                    else
                        $comments_code = str_replace("PAGE_IDENTIFIER", Md_BrowserRequest(), $comments_code);
                    $comments_code = str_replace("EXAMPLE", $MD_SETTINGS['DISQUS-SHORTNAME'], $comments_code);
                }
                echo $comments_code;
            }
        }
    }
?>
