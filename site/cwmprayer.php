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

// Always load JBSM API if it exists.
$api = JPATH_ADMINISTRATOR . '/components/com_cwmprayer/api.php';

if (file_exists($api))
{
	require_once $api;
}

$prayer = new CWMPrayerSitePrayer;

$pcConfig = JComponentHelper::getParams('com_cwmprayer');

if (!empty($pcConfig['config_pms_plugin'])
	&& file_exists(JPATH_ROOT . '/administrator/components/com_cwmprayer/pms/plg.pms.' . $pcConfig['config_pms_plugin'] . '.php'))
{
	require_once JPATH_ROOT . '/administrator/components/com_cwmprayer/helpers/pluginhelper.php';
	$PrayerPluginHelper = new CWMPrayerPluginHelper;
	$pluginfile = 'plg.pms.' . $pcConfig['config_pms_plugin'] . '.php';
	$PrayerPluginHelper->importPlugin('pms', $pluginfile);
}

$user = JFactory::getUser();

if ($pcConfig['config_allow_purge'])
{
	if ($user->get('usertype') == 'Administrator' || $user->get('usertype') == 'Super Administrator')
	{
		$prayer->PCautoPurge($pcConfig['config_request_retention'], $pcConfig['config_archive_retention']);
	}
}

$controller = JControllerLegacy::getInstance('CWMPrayer');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
