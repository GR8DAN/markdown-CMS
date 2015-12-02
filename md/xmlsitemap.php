<?php

/**
 * xmlsitemap
 * Originally: Sitemap
 *
 * This class used for generating Google Sitemap files
 *
 * @package    Sitemap
 * @author     Osman Üngür <osmanungur@gmail.com>
 * @copyright  2009-2015 Osman Üngür
 * @license    http://opensource.org/licenses/MIT MIT License
 * @link       http://github.com/o/sitemap-php
 */
class xmlsitemap {

	/**
	 *
	 * @var XMLWriter
	 */
	private $writer;
	private $domain;
	private $path;
	private $filename = 'sitemap';
	private $current_item = 0;
	private $current_sitemap = -1;  //Set to one on first added item
    private $newest_page = 0;
    private $refresh_limit = '-1 week'; //Regenerate sitemap is older than this

	const EXT = '.xml';
	const SCHEMA = 'http://www.sitemaps.org/schemas/sitemap/0.9';
	const DEFAULT_PRIORITY = 0.5;
	const ITEM_PER_SITEMAP = 50000;
	const SEPERATOR = '-';
	const INDEX_SUFFIX = 'index';

	/**
	 *
	 * @param string $domain
	 */
	public function __construct($domain) {
		$this->setDomain($domain);
	}

	/**
	 * Sets root path of the website, starting with http:// or https://
	 *
	 * @param string $domain
	 */
	public function setDomain($domain) {
		$this->domain = $domain;
		return $this;
	}

	/**
	 * Returns root path of the website
	 *
	 * @return string
	 */
	private function getDomain() {
		return $this->domain;
	}

	/**
	 * Returns XMLWriter object instance
	 *
	 * @return XMLWriter
	 */
	private function getWriter() {
		return $this->writer;
	}

	/**
	 * Assigns XMLWriter object instance
	 *
	 * @param XMLWriter $writer 
	 */
	private function setWriter(XMLWriter $writer) {
		$this->writer = $writer;
	}

	/**
	 * Returns path of sitemaps
	 * 
	 * @return string
	 */
	private function getPath() {
		return $this->path;
	}

	/**
	 * Sets paths of sitemaps
	 * 
	 * @param string $path
	 * @return Sitemap
	 */
	public function setPath($path) {
		$this->path = $path;
		return $this;
	}

	/**
	 * Returns filename of sitemap file
	 * 
	 * @return string
	 */
	private function getFilename() {
		return $this->filename;
	}

	/**
	 * Sets filename of sitemap file
	 * 
	 * @param string $filename
	 * @return Sitemap
	 */
	public function setFilename($filename) {
		$this->filename = $filename;
		return $this;
	}

	/**
	 * Returns current item count
	 *
	 * @return int
	 */
	private function getCurrentItem() {
		return $this->current_item;
	}

	/**
	 * Increases item counter
	 * 
	 */
	private function incCurrentItem() {
		$this->current_item = $this->current_item + 1;
	}

	/**
	 * Returns current sitemap file count
	 *
	 * @return int
	 */
	private function getCurrentSitemap() {
		return $this->current_sitemap;
	}

	/**
	 * Increases sitemap file count
	 * 
	 */
	private function incCurrentSitemap() {
		$this->current_sitemap = $this->current_sitemap + 1;
	}

	/**
	 * Prepares sitemap XML document
	 * 
	 */
	private function startSitemap() {
		$this->setWriter(new XMLWriter());
		$this->getWriter()->openURI($this->getSitemapFilename($this->getCurrentSitemap()));    //>0 then indexed sitemaps
		$this->getWriter()->startDocument('1.0', 'UTF-8');
		$this->getWriter()->setIndent(true);
		$this->getWriter()->startElement('urlset');
		$this->getWriter()->writeAttribute('xmlns', self::SCHEMA);
	}

	/**
	 * Adds an item to sitemap
	 *
	 * @param string $loc URL of the page. This value must be less than 2,048 characters. 
	 * @param string $priority The priority of this URL relative to other URLs on your site. Valid values range from 0.0 to 1.0.
	 * @param string $changefreq How frequently the page is likely to change. Valid values are always, hourly, daily, weekly, monthly, yearly and never.
	 * @param string|int $lastmod The date of last modification of url. Unix timestamp or any English textual datetime description.
	 * @return Sitemap
	 */
	public function addItem($loc, $priority = self::DEFAULT_PRIORITY, $changefreq = NULL, $lastmod = NULL) {
		if (($this->getCurrentItem() % self::ITEM_PER_SITEMAP) == 0) {
			if ($this->getWriter() instanceof XMLWriter) {
                //current sitemap
				$this->getWriter()->endElement();
		        $this->getWriter()->endDocument();
			}
            //Start a new sitemap
			$this->startSitemap();
            //Count them (zero based)
			$this->incCurrentSitemap();
		}
		$this->incCurrentItem();
		$this->getWriter()->startElement('url');
		$this->getWriter()->writeElement('loc', $this->getDomain() . $loc);
		$this->getWriter()->writeElement('priority', $priority);
		if ($changefreq)
			$this->getWriter()->writeElement('changefreq', $changefreq);
		if ($lastmod)
			$this->getWriter()->writeElement('lastmod', $this->getLastModifiedDate($lastmod));
		$this->getWriter()->endElement();
		return $this;
	}

	/**
	 * Prepares given date for sitemap
	 *
	 * @param string $date Unix timestamp or any English textual datetime description
	 * @return string Year-Month-Day formatted date.
	 */
	private function getLastModifiedDate($date) {
		if (ctype_digit($date)) {
			return date('Y-m-d', $date);
		} else {
			$date = strtotime($date);
			return date('Y-m-d', $date);
		}
	}

	/**
	 * Writes Google sitemap index for generated sitemap files
	 *
	 * @param string $loc Accessible URL path of sitemaps
	 * @param string|int $lastmod The date of last modification of sitemap. Unix timestamp or any English textual datetime description.
	 */
	public function createSitemapIndex($loc, $lastmod = 'Today') {
        //End last sitemap
		$this->getWriter()->endElement();
		$this->getWriter()->endDocument();
        //Do the index file
        $indexwriter = new XMLWriter();
		$indexwriter->openURI($this->getIndexFilename());
		$indexwriter->startDocument('1.0', 'UTF-8');
		$indexwriter->setIndent(true);
		$indexwriter->startElement('sitemapindex');
		$indexwriter->writeAttribute('xmlns', self::SCHEMA);
		for ($index = 0; $index < $this->getCurrentSitemap(); $index++) {
			$indexwriter->startElement('sitemap');
			$indexwriter->writeElement('loc', $loc . $this->getFilename() . ($index ? self::SEPERATOR . $index : '') . self::EXT);
			$indexwriter->writeElement('lastmod', $this->getLastModifiedDate($lastmod));
			$indexwriter->endElement();
		}
		$indexwriter->endElement();
		$indexwriter->endDocument();
	}

    /**
	 * Generate a sitemap filename
     *
     * @param int $filenaum number suffix, if > 0 returns name-x else just name
     * @return string the sitemap file name
	 */
	private function getSitemapFilename($filenum) {
        if($filenum>0)
            return $this->getPath() . $this->getFilename() . self::SEPERATOR . $this->getCurrentSitemap() . self::EXT;
        else
            return $this->getPath() . $this->getFilename() . self::EXT;
    }

    /**
     * Get the file name for the Index file for all sitemaps generated
	 *
     * @return string the index file name
	 */
	private function getIndexFilename() {
        return $this->getPath() . $this->getFilename() . self::SEPERATOR . self::INDEX_SUFFIX . self::EXT;
    }

    /**
	 * Writes Google sitemap files for generated sitemaps, can call instead of createSitemapIndex for single sitmaps
	 */
	public function createSitemap() {
        if($this->getCurrentSitemap()>0) {
            //More than one (zero indexed count), do index
            $this->createSitemapIndex($this->getDomain()."/", 'Today');
        } else {
            //just end our single (zeroth) one
            $this->getWriter()->endElement();
		    $this->getWriter()->endDocument();
        }
    }

    /**
	 * Returns newest page time (if set)
	 *
	 * @return int
	 */
	public function getNewestPageTime() {
		return $this->newest_page;
	}

	/**
	 * Sets newest page time
	 * 
	 */
	public function setNewestPageTime( $pagetime ) {
		$this->newest_page = $pagetime;
        return $this->newest_page;
	}

    /**
	 * Returns refresh limit (if set)
	 *
	 * @return string refresh limit in PHP format, e.g. "-1 week"
	 */
	public function getRefreshLimit() {
		return $this->refresh_limit;
	}

	/**
	 * Sets refresh limit (beyond which sitemap gets updated)
	 * 
     * @param string refresh limit in PHP format, e.g. "-1 week"
     * @return string refresh limit passed in
	 */
	public function setRefreshLimit( $refreshlimit ) {
		$this->refresh_limit = $refreshlimit;
        return $this->refresh_limit;
	}

    /**
	 * Check the sitemap date to see if it needs updating
	 * 
     * @return boolean TRUE if sitemap needs updating
	 */
    public function checkDate() {
        $bret=FALSE;    //assume no update to start
        $filetime=0;    //stores timestamp of sitemap file
        // does the index file exists
        if( file_exists( $this->getIndexFilename() ) )
            //* if so check date
            $filetime=filemtime($this->getIndexFilename());
        else if( file_exists( $this->getSitemapFilename(0) ) )
            // see if the single sitemap file exists
            // get timestamp 
            $filetime=filemtime($this->getSitemapFilename(0));
        if( $filetime < strtotime($this->refresh_limit) )
            $bret=TRUE;
        return $bret;
    }
}