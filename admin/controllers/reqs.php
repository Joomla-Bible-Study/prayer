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

/**
 * CWMPrayer Controller Requests
 *
 * @package  CWMPrayer.Admin
 *
 * @since    4.0
 */
class CWMPrayerControllerReqs extends JControllerAdmin
{
	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  The array of possible config values. Optional.
	 *
	 * @return  CWMPrayerModelReq
	 *
	 * @since   1.6
	 */
	public function getModel($name = 'Req', $prefix = 'CWMPrayerModel', $config = array('ignore_request' => true))
	{
		/** @var  \CWMPrayerModelReq $model */
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}
}
