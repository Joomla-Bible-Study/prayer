<?php

defined('_JEXEC') or die;

if (file_exists(JPATH_ROOT . "/administrator/components/com_cwmprayer/config.xml"))
{
	require_once JPATH_ROOT . "/components/com_cwmprayer/helpers/admin_includes.php";
	require_once JPATH_ROOT . "/components/com_cwmprayer/helpers/prayer.php";
	$prayercentermod = new CWMPrayerSitePrayer;
	$pc_rights       = $prayercentermod->intializePCRights();
	$prayercentermod->buildPCMenu(true, $params);
}
else
{
	if (!defined('CWMPRAYERCOMNOTINSTALL'))
	{
		define('CWMPRAYERCOMNOTINSTALL', 'CWM Prayer Component Not Installed');
	}

	echo '<div class="center" style="color:red; font-weight: bold">' . JText::_('CWMPRAYERCOMNOTINSTALL') . '</div>';
}
