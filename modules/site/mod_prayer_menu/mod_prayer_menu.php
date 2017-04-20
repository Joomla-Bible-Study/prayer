<?php
/**
 * @package     Prayer.Site
 * @subpackage  mod_prayer_menu
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

// Include the latest functions only once
JLoader::register('ModPrayerMenuHelper', __DIR__ . '/helper.php');

require JModuleHelper::getLayoutPath( 'mod_prayer_menu', $params->get('layout', 'default'));
