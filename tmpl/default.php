<?php
/* ------------------------------------------------------------------------
 * lightrss - Light RSS for Joomla 4.x/5.x from Prieco Ligth RSS
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (C) 2025 ConseilGouz. All Rights Reserved.
 * @author ConseilGouz 
*/
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use ConseilGouz\Module\LightRSS\Site\Helper\LightRSSHelper;

// security check - no direct access
defined('_JEXEC') or die('Restricted access');

$light_rss = LightRSSHelper::getFeed($params);

//error handling: output any error message to everybody
if (count($light_rss['error'])) {
	// only show errors to admin group users
	print '<div style="color:red;"><b>Error(s):</b><ul style="margin-left:4px;padding-left:4px;">';
	foreach ($light_rss['error'] as $error) {
		print '<li>' . $error . '</li>';
	}
	print '</ul></div>';
	return true;
}
$tooltips = false;
//check to enable tooltip js for lightTip classed elements
if ($params->get('enable_tooltip') == 1)  {
	HTMLHelper::_('bootstrap.tooltip', 'a', []);
	$tooltips = true;
}
$rssrtl="";
//begin output
?>
<div class="light-rss-container <?php echo $params->get('moduleclass_sfx'); ?>" style="direction: <?php echo $rssrtl ? 'rtl' : 'ltr'; ?>; text-align: <?php echo $rssrtl ? 'right' : 'left'; ?>">
	<?php
//feed title
	if ($params->get('rsstitle', 1) && $light_rss['title']['link']) {
		print '<div><a href="' . $light_rss['title']['link'] . '" target="' . $light_rss['target'] . '">' . $light_rss['title']['title'] . '</a></div>';
	}
//feed desc
	if ($params->get('rssdesc', 1) && $light_rss['description']) {
		print '<div class="light-rss-desc">' . $light_rss['description'] . '</div>';
	}
//feed image
	if ($params->get('rssimage', 0) && isset($light_rss['image']) && $light_rss['image']['url']) {
		print '<img src="' . $light_rss['image']['url'] . '" title="' . $light_rss['image']['title'] . '" class="light-rss-img">';
	}
	?>
	<ul class="light-rss-list" style="margin-left:0px;padding-left:0px;">
		<?php
		if ($light_rss['items']) {
			foreach ($light_rss['items'] as $item) {
				if ($tooltips) {
					$pos = $params->get('tooltip_position', 'bottom');
					$title = $item['tooltip']['title'] . '::' . $item['tooltip']['description'];
					$bootstrap =  ' data-bs-toggle="tooltip" data-bs-placement="'.$pos.'" ';
				} else {
				    $title = $item['description'] ? $item['description'] : '';
					$bootstrap = "";
				}
				//item desc
				$desc = "";
				if ($params->get('rssitemdesc', 0) && $item['description']) {
					$desc = $item['description'];
				}
				print '
        <li class="light-rss-item">
        <a href="' . $item['link'] . '" title="' . $title . '" '.$bootstrap.' target="' . $light_rss['target'] . '" ' . $light_rss['nofollow'] . '>' . $item['title'] . '</a>';
				print '<div class="light-rss-item-desc">' . $desc . '</div>';
				print '</li>';
			}
		}
		?>
	</ul>
</div>
