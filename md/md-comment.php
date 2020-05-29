<?php
    // Only add comments if config setup
    if(isset($MD_SETTINGS['HOME'])) {
        //see if article has enabled comments
        if(array_key_exists('comments',$md_meta)) {
            if(empty($md_meta["comments"])) {
                $md_meta["comments"]="no";
            }
            if(strtolower($md_meta['comments']) != "no") {
                //page url for identity for Facebook comments
                $page_url=rtrim($MD_SETTINGS['HOME'],"/").Md_BrowserRequest();
                if(array_key_exists('FBK_ADMIN',$MD_SETTINGS)) {
                    //fbk comments
                    echo "<div class=\"fb-comments\" data-href=\"".$page_url."\" data-width=\"100%\" data-numposts=\"10\"></div>\n";
                }
            }
        }
    }
?>
