<?php
    include_once "md-utils.php";
    /* While developing */
    //Md_ShowErrors();
    
    /*
    ** Get the request.
    ** If ends in slash (directory),
    ** get the index file in that directory.
    */
    $request_url = Md_BrowserRequest();
    
    /*
    ** Generate the Markdown filename. The content will be displayed
    ** from the Markdown file indicated by the URL.
    ** e.g.:
    ** http://example.com          = /index.md
    ** http://example.com/         = /index.md
    ** http://example.com/index    = /index.md
    ** http://example.com/file     = /file.md
    ** http://example.com/dir      = /dir/index.md
    ** http://example.com/dir/     = /dir/index.md
    ** http://example.com/dir/file = /dir/file.md
    */
    $path = pathinfo($request_url, PATHINFO_DIRNAME);
    $filename = pathinfo($request_url, PATHINFO_FILENAME).".md";
    /*
    ** Is content in root or in a sub directory 
    */
    if($path!="/md" and $path!=DIRECTORY_SEPARATOR)
        $filename="..".$path."/".$filename;
    elseif($path==DIRECTORY_SEPARATOR)
        $filename="../".$filename;
    /*
    ** Class to perform Markdown parsing.
    */
    include_once "parsedown.php";
    $parsedown = new Parsedown();
    /*
    ** Check for the requested content.
    ** Otherwise 404 if file cannot be found.
    */
    if(file_exists($filename)) {
        $content = file_get_contents($filename);
    } elseif($filename=="../index.md") {
        //no new site home page yet
        $content = file_get_contents("index.md");
    } else {
        //header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
        $content = file_get_contents("404.md");
    }
    /*
    ** Get some settings.
    */
    $config=$_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR."site-config.php";
    if(is_readable($config))
        include_once $config;
    else
        include_once "md-config.php";
    /*
    ** Now do the HTML page.
    */
    /* Get page meta data and separate content*/
    $md_meta = Md_ParsePage($content);
    /* Sort out web page title */
    if(array_key_exists('title',$md_meta))
        $title=$md_meta['title']." | ".$MD_SETTINGS['SITE_NAME'];
    else
        $title=$MD_SETTINGS['SITE_NAME'];
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php
            //Set title
            echo "<title>".$title."</title>\n";
            //Set favicon
            echo "<link rel=\"icon\" href=\"{$MD_SETTINGS['FAVICON']}\"/>\n";
        ?>
        <link rel="stylesheet" href="/md/css/normalize.css">
        <link rel="stylesheet" href="/md/css/md.css">
    </head>
    <body>
        <div class="container">
            <?php
                /*
                ** Process the header
                */
                include_once "md-header.php";
            ?>
            <article>
            <div class="row">
                <div class="nine columns">
                <?php
                    echo "<main>";
                    /* Echo content */
                    echo $parsedown->text($md_meta["DISPLAY_CONTENT"]);
                    /* Add article signature as microdata */
                    echo Md_ArticleSig($md_meta);
                    echo "</main>";
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
            </article>
            <!-- To Do - Add also like options -->
            <?php
                /*
                ** Process the footer
                */
                include_once "md-footer.php";
            ?>
            <div class="twelve columns">
            <?php
                /*
                ** Process page end file
                */
                echo Md_ProcessText($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.$MD_SETTINGS['PAGE_END_FILE'],$parsedown);
            ?>
            </div>
        </div>
    </body>
</html>