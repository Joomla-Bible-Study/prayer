<?php
/**
 * CWMPrayer file
 *
 * @package     CWMPrayer.Site
 * @subpackage  mod_cwmpryaer_menu
 * @copyright   2007 - 2017 (C) CWM Team All rights reserved
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.JoomlaBibleStudy.org
 * */
defined('_JEXEC') or die;

// Include the latest functions only once
JLoader::register('ModCWMPrayerMenuHelper', __DIR__ . '/helper.php');

require JModuleHelper::getLayoutPath('mod_cwmprayer_menu', $params->get('layout', 'default'));
