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

class CWMPrayerViewPrayer extends JViewLegacy
{
	/**
	 * @var  array
	 * @since 4.0
	 */
	protected $prayer;

	public function display($tpl = null)
	{
		$this->prayer = new CWMPrayerSitePrayer;

		// Set pathway information
		$this->title = JText::_('CWMPRAYERTITLE');
		$this->intro = nl2br(JText::_('CWMPRAYERLISTINTRO'));

		$this->config_show_page_headers = $this->prayer->pcConfig['config_show_page_headers'];

		parent::display($tpl);
	}
}
