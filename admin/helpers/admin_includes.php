<?php
/**
 * Core Admin CWMPrayer file
 *
 * @package    CWMPrayer.Admin
 * @copyright  2007 - 2015 (C) Joomla Bible Study Team All rights reserved
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link       http://www.JoomlaBibleStudy.org
 * */
defined('_JEXEC') or die;

require JPATH_ADMINISTRATOR . '/components/com_cwmprayer/helpers/admin.php';

$prayeradmin = new CWMPrayerAdmin;

$pcParams = JComponentHelper::getParams('com_cwmprayer');

$pcParamsArray = $pcParams->toArray();

foreach ($pcParamsArray['params'] as $name => $value)
{
	$pcConfig[(string) $name] = (string) $value;
}
