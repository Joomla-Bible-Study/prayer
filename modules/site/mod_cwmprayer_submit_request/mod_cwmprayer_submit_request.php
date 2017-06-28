<?php
/**
 * CWMPrayer file
 *
 * @package     CWMPrayer.Site
 * @subpackage  mod_cwmpryaer_request
 * @copyright   2007 - 2017 (C) CWM Team All rights reserved
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.JoomlaBibleStudy.org
 * */
defined('_JEXEC') or die;

require_once dirname(__FILE__) . '/helper.php';

$moduleclasssfx = $params->get('moduleclass_sfx');

require JModuleHelper::getLayoutPath('mod_cwmprayer_submit_request');
