<?php
    /**
     * Function Md_ShowErrors can be used to display errors when debugging.
     */
    function Md_ShowErrors() {
        error_reporting(E_ALL);
        ini_set('display_errors', 'on');
    }

    /**
     * Function Md_PrintArray can be used to display array when debugging.
     */
    function Md_PrintArray($anarray) {
        print "<pre>";
        print_r($anarray);
        print "</pre>";
    }

    /**
     * Function to return bowser request
     */
    function Md_BrowserRequest() {
        $requestUrl = (isset($_SERVER['REQUEST_URI'])) ? $_SERVER['REQUEST_URI'] : '';
        /* deal with trailing slash
        ** e.g. http://example.com/dir/ = http://example.com/dir/index
        */
        if(substr($requestUrl,-1)=='/')
            $requestUrl.='index';
        return $requestUrl;
    }

    /**
     * Function Md_IsSSL is used to check for a browser request made via SSL
     * @return boolean TRUE is SSL request from browser, else FALSE
     */
    function Md_IsSSL() {
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            return TRUE;
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') {
            return TRUE;
        } else
            return FALSE;
    }

    /**
     * Function to return the root (home) of the requested URL
     */
    function Md_RequestRoot() {
        return (Md_IsSSL()?'https':'http').'://'.$_SERVER['HTTP_HOST'].'/';
    }

    /**
     * Function ToHTMLEntities converts a string to HTML enitites
     * to obvuscate data (e.g. email addresses) from malicious crawlers
     * @param string $string_to_convert
     * @return string Encoded into HTML entities
     */
    function ToHTMLEntities($string_to_convert) {
        $convert_map = array(0x80, 0x10ffff, 0, 0xffffff);
        return mb_encode_numericentity($string_to_convert, $convert_map, 'UTF-8');
    }

    /**
	 * Function Md_ProcessMenu reads the menu definition file (main.menu.md)
     * in the website root. Just put a Markdown formatted list that file, e.g.:
     *  * [Home](/ "Home Page")
     *  * [Shop](/shop "Our Shop")
     *  * [About](/about "About Us")
     * If the menu definition is not present it outputs the defualt menu.
	 */
	function Md_ProcessMenu($markdownParser) {
        $menu_filename=dirname(__DIR__)."/menu.main.md";
        if(!file_exists($menu_filename)) {
            /* No menu file provided, use default */
            $menu_filename="md-menu.md";
        }
        echo "<nav>";
        echo $markdownParser->text(file_get_contents($menu_filename));
        echo "</nav>";
	}

    /**
	 * Function Md_ParsePage Extracts page data and meta-data information from the page content
     * markdown, returning an array of meta-data values and display content.
	 * @param $page_content from the markdown file.
	 * @return array storing key/value meta-data and display data for the page.
	 */
	function Md_ParsePage($page_content) {
        /* Get rid of starting and ending white space */
        $page_content = trim($page_content);
        /* Page meta-data stored in comment block, assume no meta to start with. */
        $meta_present = FALSE;
        /* determine type of comments in use */
        /* default is C block style */
        $start ='/*';
        $end = '*/';
        /* see if standard c block comment present */
        if(substr($page_content, 0, 2) != $start) {
            /* No? Check for HTML/XML comment style. */
            if(substr($page_content, 0, 4)=='<!--') {
                /* HTML/XML */
                $start='<!--';
                $end = '-->';
                $meta_present = TRUE;
            } else if(substr($page_content, 0, 3)=='/**') {
                /* No? Check for DocBook style comment. */
                $start='/**';
                /* DocBook comment ends same as C block style (set above) */
                $meta_present = TRUE;
            }
        } else {
            /* Must be equal to C style comment */
            $meta_present = TRUE;
        }
        if($meta_present==TRUE) {
            /* get and parse page meta data */
            $metaPart = trim(substr($page_content, strlen($start), strpos($page_content, $end) - (strlen($end) + 1)));
            /*meta data not empty */
            $parsed_content = Md_ParseMeta($metaPart);
            $parsed_content['DISPLAY_CONTENT']=trim(substr($page_content,strpos($page_content, $end) + strlen($end)+1));
        } else {
            /* Whole page is just for direct display */
            $parsed_content = array('DISPLAY_CONTENT'=>$page_content,);
        }
        return $parsed_content;
    }

    /**
	 * Function Md_SplitNewLine chops up text at newlines and
     * allows for Windows/Unix files.
     */
    function Md_SplitNewLine($text) {
        $code=preg_replace('/\n$/','',preg_replace('/^\n/','',preg_replace('/[\r\n]+/',"\n",$text)));
        return explode("\n",$code);
    }

    /**
	 * Function Md_ParseMeta Extracts meta information from the page content and 
     * creates an array containing it.
	 * @param $metadata_string containing all the meta-data markdown.
	 * @return array storing key/value meta-data for the page
	 */
	function Md_ParseMeta($metadata_string) {
		// split by new lines
        $headers = Md_SplitNewLine($metadata_string);
        $result  = array(); //store metadata key values
		foreach ($headers as $line) {
            $parts = explode(':', $line, 2);
            $key = preg_replace('/[^\w+]/', '_', strtolower(array_shift($parts))); // replace all special characters with underscores
            $val = implode($parts);
            $result[$key] = trim($val);
		}
		return $result;
	}

    /**
	 * Function Md_GetMetaData reads a Markdown file and pulls out
     * the content as meta data
     * @param $file_to_read the full system path to the Markdown file
     * @return array containing the metadata
	 */
    function Md_GetMetaData($file_to_read) {
        //read contents
        if(file_exists($file_to_read))
            /* Get page meta data from file content*/
            $md_meta=Md_ParsePage(file_get_contents($file_to_read));
        else
            $md_meta=array();
        return $md_meta;
    }


    /**
	 * Function Md_ProcessText reads the given file returns the HTML,
     * if a .md file runs the text through the Markdown converter,
     * if a .txt file assumes content is already converted to HTML.
     * @param $file_to_read the full system path to the text file
     * @param $md_converter the Markdown converter class
	 * @return string containing the text to output on a HTML page
	 */
     function Md_ProcessText($file_to_read, $md_converter) {
         $ret_string = "";
         //see if file exists
         if(file_exists($file_to_read)) {
             //get contents
             $ret_string=file_get_contents($file_to_read);
             //see if .md or .txt
             if(strtolower(pathinfo($file_to_read, PATHINFO_EXTENSION))=="md") {
                 //Markdown
                 if(empty($md_converter)==FALSE)
                     $ret_string=$md_converter->text($ret_string);
             }
         }
         return $ret_string;
     }

    /**
	 * Function Md_ArticleSig adds the signature (author, pub date, update date, archive date) to an article
     * @param $integer_to_convert the integer to convert to english
	 * @return string containing the english version or empty string if outside range
	 */
    function Md_ArticleSig(&$article_data) {
        $sig = "<div itemscope><p><small>";
        if(array_key_exists("author", $article_data)) {
            if(!empty($article_data["author"]))
                $sig .= 'Author:<span itemprop="author">'.$article_data["author"].'</span>&nbsp;&nbsp;';
        }
        if(array_key_exists("published", $article_data)) {
            if(!empty($article_data["published"]))
                $sig .= 'Published:<span itemprop="datePublished"><time>'.$article_data["published"].'</time></span>&nbsp;&nbsp;';
        }
        if(array_key_exists("updated", $article_data)) {
            if(!empty($article_data["updated"]))
                $sig .= 'Updated:<span itemprop="dateModified"><time>'.$article_data["updated"].'</time></span>&nbsp;&nbsp;';
        }
        if(array_key_exists("archived", $article_data)) {
            if(!empty($article_data["archived"]))
                $sig .= 'Archived:<time>'.$article_data["archived"].'</time>';
        }
        return $sig."</small></p></div>";
    }



    /**
	 * Function OneToTenInEnglish converts an integer 0-9 to english name (one, two, etc.)
     * @param $integer_to_convert the integer to convert to english
	 * @return string containing the english version or empty string if outside range
	 */
    function OneToTenInEnglish($integer_to_convert) {
        $english_numbers=array('','one','two','three','four','five','six','seven','eight','nine','ten');
        if($integer_to_convert<1 or $integer_to_convert >10)
            $integer_to_convert=0;
        return $english_numbers[$integer_to_convert];
    }
    /* End of Md CMS utility functions */
?>
