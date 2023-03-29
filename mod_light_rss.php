<?php
/* ------------------------------------------------------------------------
 * lightrss - Light RSS for Joomla 4.x from Prieco Light RSS
 * Version		   2.0.0 
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (C) 2023 ConseilGouz. All Rights Reserved.
 * @author ConseilGouz 
*/
use ConseilGouz\Module\LightRSS\Site\Helper\LightRSSHelper;
// no direct access
defined('_JEXEC') or die('Restricted access');
// Get data from helper class
$light_rss = LightRSSHelper::getFeed($params);
// Run default template script for output
require(JModuleHelper::getLayoutPath('mod_light_rss'));