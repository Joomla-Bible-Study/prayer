<?php

defined('_JEXEC') or die;

if (file_exists(JPATH_ROOT . "/administrator/components/com_cwmprayer/config.xml"))
{
	require_once JPATH_ROOT . "/components/com_cwmprayer/helpers/pc_includes.php";
	require_once JPATH_ROOT . "/components/com_cwmprayer/helpers/siteprayer.php";

	$prayercenterlmod = new CWMPrayerSitePrayer;
	$pc_rights        = $prayercenterlmod->intializePCRights();
	$itemid           = $prayercenterlmod->PCgetItemid();
	$lang             = Jfactory::getLanguage();
	$lang->load('com_cwmprayer', JPATH_SITE);
	$pclmodhelper = new ModCWMPrayerLatestHelper;
	$count        = $params->get('count');
	$wordcount    = $params->get('word_count');
	$link         = JRoute::_('index.php?option=com_cwmprayer&Itemid=' . (int) $itemid);
	$request      = "";
	$rows         = $pclmodhelper->getPrayerLModData($count);

	if ($pc_rights->get('pc.view'))
	{
		if (count($rows) > 0)
		{
			?>
			<style>
				div.moduletable ul.pcl {
					margin-left: 0;
					list-style-type: none;
					padding-left: 0;
					}
			</style>
			<div cellpadding="0" cellspacing="0" class="moduletable<?php echo $params->get('moduleclass_sfx'); ?>">
				<br/>
				<ul class="pcl<?php echo $params->get('moduleclass_sfx'); ?>">
					<?php
					for ($i = 0; $i < count($rows); $i++)
					{
						if ($wordcount)
						{
							$texts = explode(' ', $rows[$i]->request);
							$count = count($texts);

							if ($count > $wordcount)
							{
								for ($j = 0; $j < $wordcount; $j++)
								{
									$request .= ' ' . $texts[$j];
								}

								$request .= '...';
							}
							else
							{
								$request = $rows[$i]->request;
							}
						}
						else
						{
							$request = $rows[$i]->request;
						}

						$viewlink = JRoute::_('index.php?option=com_cwmprayercenter&view=req&id=' . (int) $rows[$i]->id . '&Itemid=' . (int) $itemid);
						?>
						<li style="padding-bottom:10px">
							<?php if ($i > 0 && $i != count($rows)) { ?>
								<br />
							<?php } ?>
							<?php echo '<b>' . JText::_('CWMPRAYERPOSTEDBY') . '</b><br />' .
								wordwrap($rows[$i]->requester, 22, "<br />\n", true) . '<br />'; ?>
							<?php echo '(' . date("M j,Y", strtotime($rows[$i]->date)) . ')<br />'; ?><br/>
							<?php echo '<i>"' . $prayercenterlmod->PCkeephtml($request) . '"</i><small>&nbsp;&nbsp;&nbsp;<a href="' .
								$viewlink . '" /><i>' . JText::_('CWMPRAYERREADMORE') . '</i></a></small>'; ?>
						</li>
						<?php
						echo '<hr style="padding:0;margin:2px;">';
						$request = "";
					}
					?>
				</ul>
				<br/>
				<small><a class="readon" style="margin-top: 4px;margin-right: 2px;"
				          href="<?php echo $link; ?>"><?php echo JText::_('CWMPRAYERVIEWLIST'); ?></a></small>
			</div>
			<?php
		}
		else
		{
			echo '<div><center><b>';
			echo wordwrap(JText::_('CWMPRAYERNOREQUEST'), 20, "<br />\n", true);
			echo '</b></center></div>';
		}
	}
	else
	{
		echo '<div>&nbsp;</div>';
	}
}
else
{
	if (!defined('CWMPRAYERCOMNOTINSTALL'))
	{
		define('CWMPRAYERCOMNOTINSTALL', 'CWM Center Component Not Installed');
	}

	echo '<div class="center" style="color:red; font-weight: bold;">' . JText::_('CWMPRAYERCOMNOTINSTALL') . '</div>';
}
