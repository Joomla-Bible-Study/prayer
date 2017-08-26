<?php
/** No direct access */
defined('_JEXEC') or die;

class CWMPrayerViewNewReq extends JViewLegacy
{
	protected $prayer;

	public function display($tpl = null)
	{
		$lang = Jfactory::getLanguage();
		$lang->load('com_cwmprayer', JPATH_SITE);
		$uri = new JUri;

		$this->prayer = new CWMPrayerSitePrayer;
		$pcConfig     = $this->prayer->pcConfig;

		// Set pathway information
		$this->action                   = $uri->toString();
		$this->title                    = JText::_('CWMPRAYERTITLE');
		$this->config_show_page_headers = $pcConfig['config_show_page_headers'];
		$this->directions               = nl2br(JText::_('CWMPRAYERREQDIRECTIONS'));
		$this->config_editor            = $pcConfig['config_editor'];
		$this->config_show_xtd_buttons  = $pcConfig['config_show_xtd_buttons'];
		$this->config_captcha           = $pcConfig['config_captcha'];
		$this->config_use_admin_alert   = $pcConfig['config_use_admin_alert'];
		$this->show_priv_option         = $pcConfig['config_show_priv_option'];
		$this->email_option             = $pcConfig['config_email_option'];
		$this->config_captcha_bypass    = $pcConfig['config_captcha_bypass_4member'];

		return parent::display($tpl);
	}
}
