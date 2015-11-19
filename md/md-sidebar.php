<?php
    /* For each sidebar.x.md or sidebar.x.txt file in the site root
    ** read the file and echo the contents, converting .md (Markdown) files. 
    ** x must be a single number between 0 and 9, lowest echoed first.
    */
    foreach (glob($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR."sidebar.[0-9].*") as $sidebar_filename) {
        //Note glob sorts by default
        echo Md_ProcessText($sidebar_filename,$parsedown);
    }
?>