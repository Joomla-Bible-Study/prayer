<?php
/**
 * Core Site CWMPrayer file
 *
 * @package    CWMPrayer.Site
 * @copyright  2007 - 2015 (C) Joomla Bible Study Team All rights reserved
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link       http://www.JoomlaBibleStudy.org
 * */
defined('_JEXEC') or die('Restricted access');

/**
 * Class view for Prayer
 *
 * @package  Prayer.Admin
 *
 * @since    4.0
 */
class CWMPrayerViewPrayer extends JViewLegacy
{
	/**
	 * @var  array
	 * @since 4.0
	 */
	protected $prayer;

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
		$this->prayer = new CWMPrayerSitePrayer;

		// Set pathway information
		$this->title = JText::_('CWMPRAYERTITLE');
		$this->intro = nl2br(JText::_('CWMPRAYERLISTINTRO'));

		$this->config_show_page_headers = $this->prayer->pcConfig['config_show_page_headers'];

		return parent::display($tpl);
	}
}
