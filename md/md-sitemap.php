<?php
    /**
     * Generate a comprehensive index.
     * Generate a schema.org sitemap for search engines.
     */
    include_once "md-utils.php";        //Utility and other functions
    include_once "md-index-funcs.php";  //Specific indexing functions

    /*
    ** Get some settings.
    */
    $config=$_SERVER['DOCUMENT_ROOT']."/site-config.php";
    if(is_readable($config))
        include_once $config;
    else
        include_once "md-config.php";

    /**
     * Class to perform Markdown parsing.
     */
    include_once "parsedown.php";
    $parsedown = new Parsedown();

    /**
     * Class to to build XML sitemap.
     */
    include_once 'xmlsitemap.php';
	$xmlmap = new xmlsitemap(rtrim($MD_SETTINGS['HOME'],"/"));
    
    /**
     * Walk website to set up
     * data arrays for indexing
     */
    //All dirs
    $rootdir = $_SERVER['DOCUMENT_ROOT'];
    //Store xml sitemap in root
    $xmlmap->setpath($rootdir.DIRECTORY_SEPARATOR);
    //Get sub dirs
    $dirs = Md_GetDirectories($rootdir,TRUE);
    //Ignore some directories ($NO_INDEX)
    $dirs = Md_FilterDirectories($dirs, $NO_INDEX);
    //Generate index sections and titles
    $dirindex = Md_SectionLinks($dirs,$INDEX_RENAME);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Index - <?php $MD_SETTINGS['SITE_NAME'];?></title>
        <?php
            //Set favicon
            echo "<link rel=\"icon\" href=\"{$MD_SETTINGS['FAVICON']}\"/>";
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
                include_once 'md-header.php';
            ?>
            <div class="row">
                <div class="nine columns">
                    <h1>Alphabetical Index</h1>
    <?php
    // Code to produce the list of links to content
    echo '<h2>Categories</h2>';
    echo '<h3>'.Md_SectionAnchors($dirindex,$MD_SETTINGS['EXTENSIONS']).'</h3>';
    //For all sections add the local page links and 
    //each page associated with it.
    foreach($dirindex as $tag => $href) {
        //get a list of all files in the given directory
        $files=Md_FilesInWebDir($href,$MD_SETTINGS['EXTENSIONS']);
        //if there are files then process them
        if(count($files)) {
            //add the link anchor from the tag list
            echo '<hr/>';
            echo '<a name="'.strtolower(str_replace(' ', '', $tag)).'"></a>'.'<h3>'.$tag.'</h3>';
            //add page titles (or URLs) to an array and URLs to XML sitemap
            $pagetitles=Md_MakeContentLinks($files, $href, $xmlmap);
            //generate links to page with title as text
            foreach ($pagetitles as $linkURL => $pagetitle) {
                echo '<a href="'.$linkURL.'">'.$pagetitle.'</a><br/>';
            }
        }
    }
    //write the sitemap
    $xmlmap->createSitemap();    
    ?>
                </div>
                <div class="three columns">
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
