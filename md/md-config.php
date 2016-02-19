<?php
 //markdown CMS default config (same as ../site-config.php until changed for new website)
 $MD_SETTINGS = array(
        "SITE_NAME" => "↓markdown↓CMS↓",
        "SITE_LOGO" => "/md/images/markdown-cms-logo.png",
        "FAVICON" => "/md/images/markdown-cms.ico",
        "HOME" => Md_RequestRoot(),
        "EXTENSIONS" => "md,php,html,txt,zip",
        "INFO_TOP_FILE" => "info.top.txt",
        "SITE_SEARCH" => "search.code.txt",
        "PAGE_END_FILE" => "page.end.txt",
        "XML_SITEMAP_REFRESH" => "-1 week"
    );

    //Filters for index page and sitemap.
    $NO_INDEX=array('images'=>'*',
                    'md'=>'*');
    //Array used to rename entries on Index page
    $INDEX_RENAME=array();

    date_default_timezone_set("UTC");   //change as required
?>