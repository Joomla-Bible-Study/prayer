<?php

defined('_JEXEC') or die;

if (file_exists(JPATH_ROOT . "/administrator/components/com_prayer/config.xml"))
{
	require_once JPATH_ROOT . "/components/com_prayer/helpers/admin_includes.php";
	require_once JPATH_ROOT . "/components/com_prayer/helpers/prayer.php";
	$prayercentermod = new PrayerSitePrayer;
	$pc_rights       = $prayercentermod->intializePCRights();
	$prayercentermod->buildPCMenu(true, $params);
}
else
{
	if (!defined('PRAYERCOMNOTINSTALL'))
	{
		define('PRAYERCOMNOTINSTALL', 'Prayer Component Not Installed');
	}

	echo '<div class="center" style="color:red; font-weight: bold">' . JText::_('PRAYERCOMNOTINSTALL') . '</div>';
}
