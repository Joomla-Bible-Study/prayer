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

/**
 * View Subscribe Class
 *
 * @package  CWMPrayer.Site
 *
 * @since    4.0
 */
class CWMPrayerViewSubscribe extends JViewLegacy
{
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
		$pcConfig = $this->prayer->pcConfig;
		$uri          = new JUri;

		// Set pathway information
		$this->action = $uri->toString();

		$this->title = JText::_('CWMPRAYERTITLE');

		$this->intro = nl2br(JText::_('CWMPRAYERLISTINTRO'));

		$this->config_show_page_headers = $pcConfig['config_show_page_headers'];

		$this->config_captcha = $pcConfig['config_captcha'];

		$this->config_captcha_bypass = $pcConfig['config_captcha_bypass_4member'];

		return parent::display($tpl);
	}
}
