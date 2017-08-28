<?php
/**
 * Core Site CWMPrayer file
 *
 * @package    CWMPrayer.Site
 * @copyright  2007 - 2015 (C) CWM Team All rights reserved
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link       https://www.christianwebministries.org/
 * */
defined('_JEXEC') or die;

$JVersion = new JVersion;

$prayer = new CWMPrayerSitePrayer;
$prayer->PCgetAuth('view_devotional');
$k = 0;

if ($this->config_use_gb)
{
	JHtml::_('behavior.modal');
	$attribs = [];
	$attribs['rel'] = "{handler: 'iframe', size: {x: 800, y: 450}}";
	$attribs['class'] = 'modal';
}

echo '<div>';

if ($this->config_show_page_headers)
{
	echo '<div class="componentheading"><h2>' . htmlentities($this->title . ' - ' . JText::_('CWMPRAYERDEVOTIONALS')) . '</h2></div>';
}

echo '<div>';
$prayer->buildPCMenu();
echo '</div><div>';
echo $prayer->writePCImage() . '</div><div>';
echo $prayer->writePCHeader($this->intro) . '</div>';
echo '<fieldset class="pcmod"><legend>' . htmlentities(JText::_('CWMPRAYERDEVOTIONALS')) . '</legend>';
echo '<div class="moddevotion">';

if (count($this->feed_array) > 0)
{
	$simplepie = new SimplePie();

	foreach ($this->feed_array as $feedfile)
	{
		$k++;

		if (!is_null($feedfile->feed))
		{
			$options = array();
			$options['rssUrl'] = $feedfile->feed;

			if ($this->config_enable_cache)
			{
				$options['cache_time'] = $this->config_update_time;
			}
			else
			{
				$options['cache_time'] = null;
			}

			$simplepie->enable_cache(false);
			$simplepie->set_feed_url($options['rssUrl']);
			$simplepie->force_feed(true);
			$simplepie->init();

			$rssDoc = $simplepie;
		}

		if ($rssDoc != false)
		{
			$feed = new stdclass;
			$feed->title = $rssDoc->get_title();
			$feed->link = $rssDoc->get_link();
			$feed->description = $rssDoc->get_description();
			$feed->image = new stdclass;
			$feed->image->url = $rssDoc->get_image_url();
			$feed->image->title = $rssDoc->get_image_title();
			$items = $rssDoc->get_items();
			$feed->items = array_slice($items, 0, $this->config_item_limit);
		}
		else
		{
			$feed = new stdclass;
			$feed->title = JText::_('ERROR LOADING FEED DATA');
			$feed->link = null;
			$feed->description = $feedfile->feed;
			$feed->image = new stdclass;
			$feed->image->url = null;
			$feed->image->title = null;
			$items = null;
			$feed->items = null;
		}

		$iUrl = isset($feed->image->url) ? $feed->image->url : null;
		$iTitle = isset($feed->image->title) ? $feed->image->title : null;

		if ($k > 1)
		{
			echo '<hr>';
		}
		?>
        <div>
            <dl style="padding:10px 0px;">
                <dt><span class="devtitle">
				<?php
				if ($iUrl && $this->config_feed_image)
				{
					?>
                    <img src="<?php echo $iUrl; ?>" title="<?php echo $iTitle; ?>" class="devimg"/>
					<?php
				}
				?>
                        <font size="4">&nbsp;&nbsp;
							<?php
							$attribs['target'] = '_blank';
							$feedlink = JHTML::_('link', JRoute::_($feed->link), $feed->title, $attribs);
							?>
							<?php echo $feedlink; ?>
          </font></span>
					<?php
					if ($this->config_feed_descr)
					{
						?>&nbsp;&nbsp;<span class="devtitledescrip">-&nbsp;<?php echo $feed->description; ?></span>
						<?php
					}

					$actualItems = count($feed->items);
					?></dt>
            </dl>
            <br/>
            <dl class="mod">
				<?php
				for ($j = 0; $j < $actualItems; $j++)
				{
					$currItem =& $feed->items[$j];
					?>
                    <dt>
						<?php
						if ($currItem->get_link())
						{
							?>
                            <font size="3">
								<?php
								$attribs['target'] = '_blank';
								$currItemlink = JHTML::_('link', JRoute::_($currItem->get_link()), $currItem->get_title(), $attribs);
								?>
								<?php echo $currItemlink; ?>
                            </font>
							<?php
						}

						if ($this->config_item_descr)
						{
							$text = html_entity_decode($currItem->get_description());
							$text = str_replace('&apos;', "'", $text);
							$num = $this->config_word_count;

							if ($num > -1)
							{
								$texts = explode(' ', $text);
								$count = count($texts);

								if ($count > $num)
								{
									$text = '';

									for ($i = 0; $i < $num; $i++)
									{
										$text .= ' ' . $texts[$i];
									}

									$text .= '...';
								}
							}

							echo '<br /><br /><dd>' . $text;
						}
						?>
                    </dd></dt>
					<?php
					if ($j < $actualItems - 1)
					{
						echo '<br />';
					}
				}
				?>
            </dl>
        </div>
		<?php
		if ($k < count($this->feed_array))
		{
			echo '<div>&nbsp;</div>';
		}

		unset($rssDoc);
	}

	echo '<div colspan="2">&nbsp;</div>';
	echo '</div></fieldset>';
	echo '</div>';
}
else
{
	echo '<br /><div><strong><div class="content"><br />' . htmlentities(JText::_('CWMPRAYERNODEVOTIONS')) . '</div></strong>
            </div><tfoot><tr><td colspan="2">&nbsp;</div><br /></fieldset></div><br />';
}

echo $prayer->PrayerFooter();