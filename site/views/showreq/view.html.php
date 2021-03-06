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

/**
 * View Class Show Request
 *
 * @package  Prayer.Site
 *
 * @since    4.0
 */
class CWMPrayerViewShowReq extends JViewLegacy
{
	/**
	 * @var  \CWMPrayerSitePrayer
	 * @since 4.0
	 */
	public $prayer;

	/**
	 * @var \JInput
	 * @since 4.0
	 */
	public $input;

	public $title;

	public $intro;

	public $config_date_format;

	public $config_time_format;

	public $aconfig_allowed_plugins;

	public $results;

	public $pop;

	public $prt;

	public $prv;

	public $sessionid;

	public $eid;

	/**
	 * Display
	 *
	 * @param   string  $tpl  ?
	 *
	 * @return void
	 *
	 * @since 4.0
	 */
	public function display($tpl = null)
	{
		$this->prayer = new CWMPrayerSitePrayer;

		$input = new JInput;
		$this->input = $input;

		$eid = $input->getInt('id', null);
		$pop = $input->getInt('pop', null);
		$prt = $input->getInt('prt', null);
		$prv = $input->getInt('prv', null);

		$sessionid = $input->getString('sessionid', null);

		$eid = JFilterOutput::cleanText($eid);
		$pop = JFilterOutput::cleanText($pop);
		$prt = JFilterOutput::cleanText($prt);
		$prv = JFilterOutput::cleanText($prv);

		$sessionid = JFilterOutput::cleanText($sessionid);

		$results = '';

		if (($prv && is_numeric($eid) && $this->prayer->PCSIDvalidate($sessionid)) || !$prv)
		{
			// Model
			$db = JFactory::getDBO();

			/** @var \CWMPrayerModelPrayer $model */
			$model = JModelLegacy::getInstance('Prayer', 'CWMPrayerModel');
			$results = $model->getEditData($eid);

			if (!$pop || ($pop && $prv))
			{
				$session = JFactory::getSession();
				$reqid = 'pc_req_viewed' . $eid;

				if (!$session->has($reqid))
				{
					$query = 'UPDATE #__cwmprayer SET hits=hits+1 WHERE id=' . (int) $eid;

					$db->setQuery($query);

					$db->execute();

					$session->set($reqid, '1');
				}
			}
		}

		$config_dformat     = $this->prayer->pcConfig['config_date_format'];
		$config_date_format = '';

		if ($config_dformat == 0)
		{
			$config_date_format = 'm-d-Y';
		}

		if ($config_dformat == 1)
		{
			$config_date_format = 'd-m-Y';
		}

		if ($config_dformat == 2)
		{
			$config_date_format = 'Y-m-d';
		}

		$config_tformat     = $this->prayer->pcConfig['config_time_format'];
		$config_time_format = '';

		if ($config_tformat == 0)
		{
			$config_time_format = 'h:i:s A';
		}

		if ($config_tformat == 1)
		{
			$config_time_format = 'H:i:s';
		}

		$config_allowed_plugins = preg_split('/[,]/', $this->prayer->pcConfig['config_allowed_plugins'], -1, PREG_SPLIT_NO_EMPTY);

		// Set pathway information
		$this->title = JText::_('CWMPRAYERTITLE');
		$this->intro = nl2br(JText::_('CWMPRAYERLISTINTRO'));
		$this->config_date_format = $config_date_format;
		$this->config_time_format = $config_time_format;
		$this->aconfig_allowed_plugins = $config_allowed_plugins;
		$this->results = $results[0];
		$this->pop = $pop;
		$this->prt = $prt;
		$this->prv = $prv;
		$this->sessionid = $sessionid;
		$this->eid = $eid;

		parent::display($tpl);
	}
}
