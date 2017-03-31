<?php
/**
 * Core Admin Church Directory file
 *
 * @package    ChurchDirectory.Admin
 * @copyright  2007 - 2015 (C) Joomla Bible Study Team All rights reserved
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link       http://www.JoomlaBibleStudy.org
 * */
// No Direct Access
defined('_JEXEC') or die;

if (defined('PRAYER_LOADED'))
{
	return;
}

// Manually enable code profiling by setting value to 1
define('PRAYER_PROFILER', 0);

// Load JBSM Class
JLoader::discover('Prayer', JPATH_ROOT . '/components/com_prayer/helpers', 'false', 'true');
JLoader::discover('PrayerTable', JPATH_ROOT . '/components/com_prayer/tables', 'false', 'true');
JLoader::discover('Prayer', JPATH_ADMINISTRATOR . '/components/com_prayer/helpers', 'false', 'true');
JLoader::discover('PrayerTable', JPATH_ADMINISTRATOR . '/components/com_prayer/tables', 'false', 'true');
JLoader::register('PrayerHelper', JPATH_ADMINISTRATOR . '/components/com_prayer/helpers/prayer.php');
JHtml::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_prayer/helpers/html/');

JHtml::stylesheet('media/com_prayer/css/general.css');

// Include the JLog class.
jimport('joomla.log.log');
JLog::addLogger(
	array(
		'text_file' => 'com_prayer.errors.php'
	),
	JLog::ALL,
	'com_prayer'
);

// ChurchDirectory has been initialized
define('PRAYER_LOADED', 1);
