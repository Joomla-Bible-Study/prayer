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

$pcParams = JComponentHelper::getParams('com_cwmprayer');

$pcParamsArray = $pcParams->toArray();

foreach ($pcParamsArray['params'] as $name => $value)
{
	$pcConfig[(string) $name] = (string) $value;
}

$document = JFactory::getDocument();

$document->addScript('media/com_cwmprayer/js/pc.js');

$document->addStyleSheet(JURI::base() . 'media/com_cwmprayer/css/cwmprayer.css');
