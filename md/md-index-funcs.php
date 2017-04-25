<?php
    /**
     * Function Md_GetDirectories is used to return all directories at and below
     * a given directory
     */
     function Md_GetDirectories($startdir, $includestartdir=FALSE) {
        $result=array();
        if($includestartdir)
            $result[]=$startdir;
        while($dirs = glob($startdir.DIRECTORY_SEPARATOR.'*', GLOB_ONLYDIR|GLOB_NOSORT)) {
            $startdir .= DIRECTORY_SEPARATOR.'*';
            $result = array_merge($result, $dirs);
        }
        return $result;
     }

     /**
     * Function Md_FilterDirectories removes certain directories from an array.
     * @param array $dirs_to_filter
     * @param array $filter_dirs
     * @return array of the filter directories
     */
     function Md_FilterDirectories($dirs_to_filter, $filter_dirs) {
        $rootdir = $_SERVER['DOCUMENT_ROOT'];
         // Build exclusions (set up in config file)
        $removedirs=array();
        foreach($filter_dirs as $directory => $file) {
            if($file=='*')
                $removedirs[]=$rootdir.DIRECTORY_SEPARATOR.$directory;
        }
        return array_diff($dirs_to_filter,$removedirs);  //remove the non-indexed directories
     }

     /**
     * Function Md_SectionLinks sets up the href entries and titles for
     * index listings of pages.
     * @param array $dirs_to_index
     * @param array $name_override
     * @return array of named sections and index page hrefs to them
     */
     function Md_SectionLinks($dirs_to_index, $name_override) {
        $rootdir=$_SERVER['DOCUMENT_ROOT']; //root of the web files
        //walk all directories
        $section_index=array(); //store the section reference info
        foreach($dirs_to_index as $directory) {
            $href=substr($directory, strlen($rootdir)+1);  //remove base dir
            $hash=str_replace(DIRECTORY_SEPARATOR, ' ',$href);  //replace directory separator
            //check if specific name is defined for the directory
            if(array_key_exists($hash,$name_override)) {
                //use that specific name
                $section=$name_override[$hash];
            } else {
                //underscore represent spaces and all words (directory and sub directory names) start upper case
                $section=ucwords(str_replace('_',' ',$hash));
                if($section=='')
                    $section='Home';    //root of web site is "Home"
            }
            $section_index[$section]=$href;  //save section name and directory location
        }
        //sort
        uksort($section_index, "strnatcmp");
        return $section_index;
     }

     /**
	 * Function Md_SectionAnchors makes HTML links to given index sections
     * @param array $sections with possible pages
     * @param string $file_filter filter for possible pages of content
     * @return string containing the section links     
     */
    function Md_SectionAnchors($sections, $file_filter){
        $links='';  //generate the links (for content that exists)
        foreach($sections as $section => $href) {
            $rootdir=$_SERVER['DOCUMENT_ROOT']; //root of the web files
            $files=glob($rootdir.DIRECTORY_SEPARATOR.$href.DIRECTORY_SEPARATOR.'*.{'.$file_filter.'}', GLOB_BRACE|GLOB_NOSORT);
            //set up jump links
            if(count($files)) {
                //content exists
                if ($links!='')
                    $links.=' | ';   //separate for neat display
                //create jump link, use section link as a #tag hover
                $links.='<a title="#'.strtolower(str_replace(' ', '', $section)).'" href="#'.strtolower(str_replace(' ', '', $section)).'">'.$section.'</a>';  //add the anchor and attach to all anchors
            }
        }
        return $links;
    }

    /**
    * Function Md_FilesInWebDir returns file names in directory holding web content
    * @param string $rel_dir href of directory relative to root
    * @param string $extensions filter of file types
    * @return array of file names
    */
    function Md_FilesInWebDir($rel_dir, $extensions) {
        $rootdir=$_SERVER['DOCUMENT_ROOT']; //root of the web files
        //get a list of all files in the given directory
        if($rel_dir!='') {
            //All directories except home
            $dir_to_list=$rootdir.DIRECTORY_SEPARATOR.$rel_dir.DIRECTORY_SEPARATOR.'*.{'.$extensions.'}'; 
        } else {
            //Home directory
            $dir_to_list=$rootdir.DIRECTORY_SEPARATOR.'*.{'.$extensions.'}'; 
        }
        return glob($dir_to_list, GLOB_BRACE|GLOB_NOSORT);
    }

    /**
	 * Function Md_MakeLinkHref makes a href HTML link given a path and file name
     * @param $localhref the relative path to the file
     * @param $filename the name of the file
     * @return array containing the metadata
	 */
    function Md_MakeLinkHref($localhref,$filename) {
        //correct possible directory separators
        $localhref=str_replace("\\","/",$localhref);
        //start at root
        $linkURL='/';
        //add trailing slash for non-root URLs
        if($localhref!='')
            $linkURL.=$localhref.'/';
        //md extension not needed (default file type)
        if(strtolower(pathinfo($filename, PATHINFO_EXTENSION))=='md') {
            $linkURL.=pathinfo($filename, PATHINFO_FILENAME);
        } else {
            //otherwise filename with extension
            $linkURL.=pathinfo($filename, PATHINFO_BASENAME);
        }
        return $linkURL;
    }

    /**
     * Function Md_CheckForCMSFile returns 1 if a filename is
     * @param $filename the name of the file to process for special handling.
     * @return string for the text to use in the index link (empty means don't index).
     * 
     */
    function Md_CheckForCMSFile($filename) {
        $basename=pathinfo($filename, PATHINFO_BASENAME);
        $display_file=TRUE; //assume file is OK to display
        //Here are the CMS files not to diplay in Index
        $file_filters = array ("/\.php$/", 
                               "/md-/", 
                               "/footer\.[0-9]\.md$/",
                               "/404/",
                               "/menu.main.md$/",
                               "/^google[0-9a-f]+\.html$/");
        foreach($file_filters as $file_filter) {
            if(preg_match($file_filter,$basename)==1) {
                //matches filter so don't display
                $display_file=FALSE;
                break;
            }
        }
        return $display_file;
     }      

     /**
	 * Function Md_MakeContentLinks creates array of page titles and URLS
     * @param $filenames the files to get the titles from
     * @param $rel_loc the relative location of the files
     * @param $xmlsitemap the xmlsitemap class instance for the xml sitemap
     * @return array containing the titles (sorted alphabetically) and links
	 */
     function Md_MakeContentLinks($filenames, $rel_loc, $xmlsitemap) {
        $pages = array();
        foreach ($filenames as $filename) {
            //ignore system files
            if(Md_CheckForCMSFile($filename)) { 
                //derive link URL
                $linkURL=Md_MakeLinkHref($rel_loc,$filename);
                //read contents for page title for link
                $md_meta = Md_GetMetaData($filename);
                //store title for link
                if(isset($md_meta['title'])) {
                    $pages[$linkURL]=$md_meta['title'];
                } else {
                    //no title default to file name as text
                    $pages[$linkURL]=pathinfo($linkURL, PATHINFO_BASENAME);
                }
                //xml sitemap generated if class active
                if(isset($xmlsitemap)) {
                    //get date of file to determine last update
                    $lastmod=date("Y-m-d",filemtime($filename));
                    $changefreq=Md_FileAgeCheck($filename,$xmlsitemap);
                    if(isset($md_meta['priority']))
                        $priority=$md_meta['priority'];
                    else
                        $priority="0.5";
                    //set up the xml sitemap entry
                    $xmlsitemap->additem($linkURL, $priority, $changefreq, $lastmod);
                }
            }
            //sort array on title
            uasort($pages, "strnatcmp");
        }
        return $pages;
     }

     /**
	 * Function Md_FileAgeCheck checks age of page for xml sitemap
     * @param $thisfile full path to check age against current time
     * @param $xmlmapobj stores date of most recent page
     */
     function Md_FileAgeCheck($thisfile, $xmlmapobj) {
        $currenttime=time();
        $filetime=filemtime($thisfile);
        if(isset($xmlmapobj))
            if($filetime > $xmlmapobj->getNewestPageTime()) $xmlmapobj->setNewestPageTime($filetime);
        $agediff=$currenttime-$filetime;
        if($agediff < strtotime("+1 hour",$currenttime)-$currenttime)
            return "always";
        elseif($agediff < strtotime("+1 day",$currenttime)-$currenttime)
            return "hourly";
        elseif($agediff < strtotime("+1 week",$currenttime)-$currenttime)
            return "daily";
        elseif($agediff < strtotime("+1 month",$currenttime)-$currenttime)
            return "weekly";
        elseif($agediff < strtotime("+1 year",$currenttime)-$currenttime)
            return "monthly";
        elseif($agediff < strtotime("+2 years",$currenttime)-$currenttime)
            return "yearly";
        return "never";
     }
?>