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
    $is404 = FALSE;
    if(file_exists($filename)) {
        $content = file_get_contents($filename);
    } elseif($filename=="../index.md") {
        //no new site home page yet
        $content = file_get_contents("index.md");
    } else {
        //no content, 404 it
        $is404 = TRUE;  //used to prevent share buttons showing
        header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
        $filename="404.md";
        if(file_exists("..".DIRECTORY_SEPARATOR.$filename))
            $content = file_get_contents("..".DIRECTORY_SEPARATOR.$filename);
        else
            $content = file_get_contents($filename);
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
<html lang="<?php if(array_key_exists('LANGUAGE',$MD_SETTINGS)) echo $MD_SETTINGS['LANGUAGE']; else echo 'en-GB'; ?>">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <?php
            //Add any meta data tags
            Md_ProcessMeta($md_meta);
            //Set title
            echo "<title>".$title."</title>\n";
            //Set favicon
            echo "\t\t<link rel=\"icon\" href=\"{$MD_SETTINGS['FAVICON']}\"/>\n";
        ?>
        <link rel="stylesheet" href="/md/css/normalize.css">
        <link rel="stylesheet" href="/md/css/md.css">
        <?php
            //Does article require code syntax highlighting?
            if(!empty($md_meta["syntax"])) {
                //Default style if none specified
                if(empty($md_meta["synstyle"])) {
                    $md_meta["synstyle"]="default";
                }
                if(strtolower($md_meta["syntax"])=="yes") {
                    //Add cdn hosted syntax highlighter
                    echo "<link rel=\"stylesheet\" href=\"//cdn.jsdelivr.net/highlight.js/9.2.0/styles/{$md_meta['synstyle']}.min.css\">\n";
                    echo "<script src=\"//cdn.jsdelivr.net/highlight.js/9.2.0/highlight.min.js\"></script>\n";
                } elseif(strtolower($md_meta["syntax"])!="no"){
                    //Link to local syntax highligher
                    //Syntax setting set to name of folder under md folder
                    echo "<link rel=\"stylesheet\" href=\"/md/{$md_meta['syntax']}/styles/{$md_meta['synstyle']}.css\">\n";
                    echo "<script src=\"/md/{$md_meta['syntax']}/highlight.pack.js\"></script>\n";
                }
                echo "<script>hljs.initHighlightingOnLoad();</script>";
            }
            //Handle cookie consent, https://www.osano.com/cookieconsent/download/
            if(!array_key_exists('NO_COOKIE_MESSAGE',$MD_SETTINGS))
                //Add cdn hosted cookie consent css
                echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"https://cdn.jsdelivr.net/npm/cookieconsent@3/build/cookieconsent.min.css\">\n";
            //Optional Google analytics support
            include_once "md-analytics.php";
        ?>
    </head>
    <body>
        <div class="container">
            <?php // Process the header
                include_once "md-header.php";
            ?>
            <div class="row">
                <div class="nine columns">
                <?php
                    echo "<main><article>";
                    /* Echo content */
                    echo $parsedown->text($md_meta["DISPLAY_CONTENT"])."\n";
                    /* Add article signature as microdata */
                    echo Md_ArticleSig($md_meta);
                    echo "</article></main>\n";
                    /* Add sharing buttons */
                    if(array_key_exists('SHARE_BUTTONS',$MD_SETTINGS) && !$is404)
                        include_once "md-share.php";
                    /* Website feedback system */
                    if(array_key_exists('comments',$md_meta)) {
                        if(strtolower($md_meta['comments']) != "no")
                            //Get the comment html form
                            echo str_replace('RECAPTCHA_SITE_KEY',$MD_SETTINGS['RECAPTCHA_SITE'],Md_ProcessText("md-fdbk-form.txt", NULL));
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
            <div class="twelve columns">
            <?php
                /*
                ** Process page end file
                */
                echo Md_ProcessText($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.$MD_SETTINGS['PAGE_END_FILE'],$parsedown);
            ?>
            </div>
        </div>
        <?php
            //Add coookie consent script if enabled
            if(!array_key_exists('NO_COOKIE_MESSAGE',$MD_SETTINGS))
                echo file_get_contents("md-cookie.txt");
        ?>
    </body>
</html>
