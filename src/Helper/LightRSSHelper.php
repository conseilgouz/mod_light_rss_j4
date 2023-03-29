<?php
/* ------------------------------------------------------------------------
 * lightrss - Light RSS for Joomla 4.x from Prieco Ligth RSS
 * Version		   2.0.0 
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (C) 2023 ConseilGouz. All Rights Reserved.
 * @author ConseilGouz 
*/
namespace ConseilGouz\Module\LightRSS\Site\Helper;
// no direct access
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Feed\FeedFactory;
class LightRSSHelper {

	static function getFeed($params) {
		//global $mainframe;
		$light_rss = array(); //init feed array
		// check if cache directory exists and is writeable
		$cacheDir = JPATH_BASE .'/cache';
		if (!is_writable($cacheDir)) {
			$light_rss['error'][] = 'Cache folder is unwriteable. Solution: chmod 777 ' . $cacheDir;
			$cache_exists = false;
		} else {
			$cache_exists = true;
		}
		//get local module parameters from xml file module config settings
		$rssurl = $params->get('rssurl', NULL);
		$rssitems = $params->get('rssitems', 5);
		$rssdesc = $params->get('rssdesc', 1);
		$rssimage = $params->get('rssimage', 1);
		$rssitemtitle_words = $params->get('rssitemtitle_words', 0);
		$rssitemdesc = $params->get('rssitemdesc', 0);
		$rssitemdesc_images = $params->get('rssitemdesc_images', 1);
		$rssitemdesc_words = $params->get('rssitemdesc_words', 0);
		$rsstitle = $params->get('rsstitle', 1);
		$rsscache = $params->get('rsscache', 3600);
		$link_target = $params->get('link_target', 1);
		$no_follow = $params->get('no_follow', 0);
		$enable_tooltip = $params->get('enable_tooltip', 'yes');
		$tooltip_desc_words = $params->get('t_word_count_desc', 25);
		$tooltip_desc_images = $params->get('tooltip_desc_images', 1);
		$tooltip_title_words = $params->get('t_word_count_title', 25);

		$light_rss['error'] = [];

		if (!$rssurl) {
			$light_rss['error'][] = 'Invalid feed url. Please enter a valid url in the module settings.';
			return $light_rss; //halt if no valid feed url supplied
		}

		switch ($link_target) { //open links in current or new window
			case 1:
				$link_target = '_blank';
				break;
			case 0:
				$link_target = '_self';
				break;
			default:
				$link_target = '_blank';
				break;
		}
		$light_rss['target'] = $link_target;
		$light_rss['nofollow'] = '';
		if ($no_follow) {
			$light_rss['nofollow'] = 'rel="nofollow"';
		}

		//Load and build the feed array
		$rss = new FeedFactory;;
		$feed = $rss->getFeed($rssurl);

		// feed title			
		if ($feed->title && $rsstitle) {
			$light_rss['title']['link'] = $feed->link->uri;
			$light_rss['title']['title'] = $feed->title;
		}
		// feed description
		if ($rssdesc) {
			$light_rss['description'] = $feed->description;
		}
		// feed image
		if ($rssimage && $feed->image) {
			$light_rss['image']['url'] = $feed->image_uri;
			$light_rss['image']['title'] = $feed->image_title;
		}
		//end feed meta-info
		//start processing feed items
		//if there are items in the feed
		if (count($feed)) {
			if (count($feed) < $rssitems) $rssitems = count($feed);
			//start looping through the feed items
			$light_rss_item = 0; //item counter for array indexing in the loop
			for ($z = 0;$z< $rssitems;$z++) {
                $currItem = $feed[$z];
				// item title							
				$item_title = trim($currItem->title);
				// item title word limit check
				if ($rssitemtitle_words) {
					$item_titles = explode(' ', $item_title);
					$count = count($item_titles);
					if ($count > $rssitemtitle_words) {
						$item_title = '';
						for ($i = 0; $i < $rssitemtitle_words; $i++) {
							$item_title .= ' ' . $item_titles[$i];
						}
						$item_title .= '...';
					}
				}
				$light_rss['items'][$light_rss_item]['title'] = $item_title; // Item Title
				$light_rss['items'][$light_rss_item]['link'] = $currItem->uri;

				// item description
				if ($rssitemdesc) {
					$desc = trim($currItem->content);
					if (!$rssitemdesc_images) {
						$desc = preg_replace("/<img[^>]+\>/i", "", $desc); //strip image tags
					}

					//item description word limit check
					if ($rssitemdesc_words) {
						$texts = explode(' ', $desc);
						$count = count($texts);
						if ($count > $rssitemdesc_words) {
							$desc = '';
							for ($i = 0; $i < $rssitemdesc_words; $i++) {
								$desc .= ' ' . $texts[$i]; //build words
							}
							$desc .= '...';
						}
					}
					$light_rss['items'][$light_rss_item]['description'] = $desc; //Item Description
				}

				// tooltip text
				if ($enable_tooltip == 'yes') {

					//tooltip item title
					$t_item_title = trim($currItem->title);

					// tooltip title word limit check
					if ($tooltip_title_words) {
						$t_item_titles = explode(' ', $t_item_title);
						$count = count($t_item_titles);
						if ($count > $tooltip_title_words) {
							$tooltip_title = '';
							for ($i = 0; $i < $tooltip_title_words; $i++) {
								$tooltip_title .= ' ' . $t_item_titles[$i];
							}
							$tooltip_title .= '...';
						} else {
							$tooltip_title = $t_item_title;
						}
					} else {
						$tooltip_title = $t_item_title;
					}

					$tooltip_title = preg_replace("/(\r\n|\n|\r)/", " ", $tooltip_title); //replace new line characters in tooltip title, important!
					$tooltip_title = htmlspecialchars(html_entity_decode($tooltip_title), ENT_QUOTES); //format text for tooltip
					$light_rss['items'][$light_rss_item]['tooltip']['title'] = $tooltip_title; //Tooltip Title
					//tooltip item description
					$text = trim($currItem->content);
					if (!$tooltip_desc_images) {
						$text = preg_replace("/<img[^>]+\>/i", "", $text);
					}

					// tooltip desc word limit check
					if ($tooltip_desc_words) {
						$texts = explode(' ', $text);
						$count = count($texts);
						if ($count > $tooltip_desc_words) {
							$text = '';
							for ($i = 0; $i < $tooltip_desc_words; $i++) {
								$text .= ' ' . $texts[$i];
							}
							$text .= '...';
						}
					}
					$text = preg_replace("/(\r\n|\n|\r)/", " ", $text); //replace new line characters in tooltip, important!
					$text = htmlspecialchars(html_entity_decode($text), ENT_QUOTES); //format text for tooltip
					$light_rss['items'][$light_rss_item]['tooltip']['description'] = $text; //Tooltip Body
				} else {
					$light_rss['items'][$light_rss_item]['tooltip'] = array(); //blank
				}

				$light_rss_item++; //increment item counter
			}
		} //end item quantity check if statement
		//return the feed data structure for the template	
		return $light_rss;
	}

}