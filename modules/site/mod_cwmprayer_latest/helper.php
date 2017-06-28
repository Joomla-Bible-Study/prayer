<?php
/**
 * CWMPrayer file
 *
 * @package     CWMPrayer.Site
 * @subpackage  mod_cwmpryaer_latest
 * @copyright   2007 - 2017 (C) CWM Team All rights reserved
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.JoomlaBibleStudy.org
 * */
defined('_JEXEC') or die;

/**
 * Prayer Installer Script
 *
 * @package     CWMPrayer.Site
 * @subpackage  mod_cwmpryaer_latest
 *
 * @since    4.0
 */
class ModCWMPrayerLatestHelper
{
	/**
	 * Get CWM Prayer Load Module Data
	 *
	 * @param   int  $count  Limit Return of records
	 *
	 * @return mixed
	 *
	 * @since 4.0
	 * @todo Convert SQL to Joomla Driver
	 */
	public function getPrayerLModData($count)
	{
		$db    = JFactory::getDBO();
		$query = "SELECT a.id, a.requester, a.request, TIMESTAMP(CONCAT( a.date,' ', a.time)) AS date "
			. "\n FROM #__cwmprayer AS a"
			. "\n WHERE a.state='1' AND a.displaystate='1'"
			. "\n ORDER BY date DESC"
			. "\n LIMIT $count";
		$db->setQuery($query);
		$rows = $db->loadObjectList();

		return $rows;
	}
}
