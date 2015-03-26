<?php
/**
 * Copyright (C) 2014, 2015 Dmytro Dzyubak
 * 
 * This file is part of php-pim.
 * 
 * php-pim is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * php-pim is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with php-pim. If not, see <http://www.gnu.org/licenses/>.
 */

class BookmarksToXml {
    public static function extractURLs($notes) {
        $urlArr = array();
        $dom = new DOMDocument();
        foreach ($notes as $note) {
            if(!empty($note)) {
                $dom->loadHTML($note);
                $links = $dom->getElementsByTagName('a');
                foreach ($links as $link) {
                    $urlArr[] = $link->getAttribute('href');
                }
            }
        }
        if (empty($urlArr)) {
            return NULL; // no URLs found
        }
        $urlArrNoDupes = array_unique($urlArr); // removes duplicate values from an array
        sort($urlArrNoDupes);
        return $urlArrNoDupes;
    }
	
	public static function getTitle($url) {
		// Disable libxml errors and allow user to fetch error information as needed
		//libxml_use_internal_errors(true);
		
		$string = @file_get_contents($url);
		$string = mb_convert_encoding($string, 'utf-8', mb_detect_encoding($string));
		$string = mb_convert_encoding($string, 'html-entities', 'utf-8');
		$dom = new DOMDocument();
		if(!@$dom->loadHTML($string)) {
			return NULL;
		}
		//if(!@$dom->loadHTMLFile($url)) {
			//foreach (libxml_get_errors() as $error) {
				// handle errors here
			//}
			//libxml_clear_errors();
			//return NULL;
		//}
		//libxml_use_internal_errors(false);
		$xpath = new DOMXPath($dom);
		// "//" - Selects nodes in the document from the current node that match the selection no matter where they are, "/" - Selects from the root node
		return $xpath->query('//title')->item(0)->nodeValue;
	}

/*
	// Alternative variant:
    public static function getTitle($url) {
        // url doesn't exist or not reachable results in Warning
		$htmlPage = @file_get_contents($url);
        if($htmlPage === FALSE) { return NULL; }
        if( strlen($htmlPage) > 0 ) {
            preg_match("/\<title\>(.*)\<\/title\>/i", $htmlPage, $title);
            if(isset($title[1])) {
                return $title[1];
            }
        }
    }
*/

    public static function urlsToBookmarks($urls) {
        $bookmarks = array();
        $i = 0;
        foreach ($urls as $url) {
            $bookmarks[$i]['title'] = self::getTitle($url);
            $bookmarks[$i]['url'] = $url;
            $i++;
        }
        return $bookmarks;
    }

    public static function saveAsXML($elements, $filename) {
        $dom = new DOMDocument(); // $dom = new DOMDocument('1.0');
        $dom->preserveWhiteSpace = false; // Do not remove redundant white space. Default to TRUE.
        $dom->formatOutput = true; // Nicely formats output with indentation and extra space.
        $bookmarks = $dom->createElement('bookmarks');
        foreach ($elements as $element) {
            $bookmark = $dom->createElement('bookmark');

            $title = $dom->createElement('title');
            $title->appendChild($dom->createTextNode(trim(htmlspecialchars($element['title']))));
            $bookmark->appendChild($title);

            $url = $dom->createElement('url');
            $url->appendChild($dom->createTextNode(trim(htmlspecialchars($element['url']))));
            $bookmark->appendChild($url);

            $bookmarks->appendChild($bookmark);
        }
        $dom->appendChild($bookmarks);
        //echo htmlspecialchars($dom->saveHTML(), ENT_QUOTES); // output to screen
        $dom->save($filename); // save to file
    }
}