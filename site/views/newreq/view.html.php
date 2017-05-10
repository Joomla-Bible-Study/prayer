<?php
/* *************************************************************************************
Title          prayer Component for Joomla
Author         Mike Leeper
License        This program is free software: you can redistribute it and/or modify
               it under the terms of the GNU General Public License as published by
               the Free Software Foundation, either version 3 of the License, or
               (at your option) any later version.
               This program is distributed in the hope that it will be useful,
               but WITHOUT ANY WARRANTY; without even the implied warranty of
               MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
               GNU General Public License for more details.
               You should have received a copy of the GNU General Public License
               along with this program.  If not, see <http://www.gnu.org/licenses/>.
Copyright      2006-2014 - Mike Leeper (MLWebTechnologies) 
****************************************************************************************
No direct access*/
defined('_JEXEC') or die('Restricted access');

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
		$this->action = $uri->toString();
		$pctitle = JText::_('CWMPRAYERTITLE');
		$this->title = $pctitle;
		$this->config_show_page_headers = $pcConfig['config_show_page_headers'];
		$pcdirections = nl2br(JText::_('CWMPRAYERREQDIRECTIONS'));
		$this->directions = $pcdirections;
		$this->config_editor = $pcConfig['config_editor'];
		$this->config_show_xtd_buttons = $pcConfig['config_show_xtd_buttons'];
		$this->config_captcha = $pcConfig['config_captcha'];
		$this->config_use_admin_alert = $pcConfig['config_use_admin_alert'];
		$this->show_priv_option = $pcConfig['config_show_priv_option'];
		$this->email_option = $pcConfig['config_email_option'];
		$this->config_captcha_bypass = $pcConfig['config_captcha_bypass_4member'];

		return parent::display($tpl);
	}
}
