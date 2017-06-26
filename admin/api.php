<?php
/**
 * Core Admin CWMPrayer file
 *
 * @package    CWMPrayer.Admin
 * @copyright  2007 - 2015 (C) Joomla Bible Study Team All rights reserved
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link       http://www.JoomlaBibleStudy.org
 * */
// No Direct Access
defined('_JEXEC') or die;

if (defined('CWMPRAYER_LOADED'))
{
	return;
}

// Manually enable code profiling by setting value to 1
define('CWMPRAYER_PROFILER', 0);

// Load JBSM Class
JLoader::discover('CWMPrayer', JPATH_ROOT . '/components/com_cwmprayer/helpers', 'false', 'true');
JLoader::discover('CWMPrayerTable', JPATH_ROOT . '/components/com_cwmprayer/tables', 'false', 'true');
JLoader::discover('CWMPrayer', JPATH_ADMINISTRATOR . '/components/com_cwmprayer/helpers', 'false', 'true');
JLoader::discover('CWMPrayerTable', JPATH_ADMINISTRATOR . '/components/com_cwmprayer/tables', 'false', 'true');
JLoader::register('CWMPrayerHelper', JPATH_ADMINISTRATOR . '/components/com_cwmprayer/helpers/cwmprayer.php');
JHtml::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_cwmprayer/helpers/html/');

JHtml::stylesheet('media/com_cwmprayer/css/general.css');

// If phrase is not found in specific language file, load english language file:
$language = JFactory::getLanguage();
$language->load('com_cwmprayer', JPATH_ADMINISTRATOR . '/components/com_cwmprayer', 'en-GB', true);
$language->load('com_cwmprayer',  JPATH_ADMINISTRATOR . '/components/com_cwmprayer', null, true);
$language->load('com_cwmprayer', JPATH_SITE . '/components/com_cwmprayer', 'en-GB', true);
$language->load('com_cwmprayer',  JPATH_SITE . '/components/com_cwmprayer', null, true);

// Include the JLog class.
jimport('joomla.log.log');
JLog::addLogger(
	array(
		'text_file' => 'com_cwmprayer.errors.php'
	),
	JLog::ALL,
	'com_cwmprayer'
);

// ChurchDirectory has been initialized
define('CWMPRAYER_LOADED', 1);
