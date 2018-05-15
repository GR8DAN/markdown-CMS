<?php
    // Only add Facebook comments admin if config setup
    if(isset($MD_SETTINGS['HOME'])) {
        //see if article has enabled comments
        if(array_key_exists('comments',$md_meta)) {
            if(empty($md_meta["comments"])) {
                $md_meta["comments"]="no";
            }
            if(strtolower($md_meta['comments']) != "no") {
                //page url for identity for comments
                $page_url=rtrim($MD_SETTINGS['HOME'],"/").Md_BrowserRequest();
                //Get the comment code
                echo Md_ProcessText("md-fb-comms.txt", NULL);
            }
        }
    }
?>