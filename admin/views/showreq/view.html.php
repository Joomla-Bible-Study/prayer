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
 * Class view for Req
 *
 * @package  Prayer.Admin
 *
 * @since    4.0
 */
class CWMPrayerViewReq extends JViewLegacy
{
	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 *
	 * @see     JViewLegacy::loadTemplate()
	 * @since   3.0
	 */
	public function display($tpl = null)
	{
		$this->version = new JVersion;

		$eid = JInput::getInt('id');

		$db = JFactory::getDBO();

		$db->setQuery("SELECT * FROM #__cwmprayer WHERE id='" . $eid . "'");

		$this->erow = $db->loadObjectList();

		return parent::display($tpl);
	}
}
