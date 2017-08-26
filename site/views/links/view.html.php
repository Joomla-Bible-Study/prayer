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
 * View Links Class
 *
 * @package  Prayer.Site
 *
 * @since    4.0
 */
class CWMPrayerViewLinks extends JViewLegacy
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
		$app = JFactory::getApplication();

		$this->prayer = new CWMPrayerSitePrayer;
		$pcConfig = $this->prayer->pcConfig;

		$db = JFactory::getDBO();

		$query = $db->getQuery(true);
		$query->select('a.name,a.url,a.descrip,a.alias,a.catid,c.title as category,c.lft')
			->from('#__cwmprayer_links AS a')
			->leftJoin('#__categories AS c ON (c.id = a.catid)')
			->where('a.published=' . $db->q('1'))
			->order('c.lft,a.ordering');

		$db->setQuery($query);

		$link_array = $db->loadObjectList();

		// Set pathway information
		$this->config_show_page_headers = $pcConfig['config_show_page_headers'];
		$this->link_array = $link_array;
		$this->config_two_columns = $pcConfig['config_two_column'];
		$this->config_show_linkcats = $pcConfig['config_show_linkcats'];
		$this->config_use_gb = $pcConfig['config_use_gb'];
		$pctitle = JText::_('CWMPRAYERTITLE');
		$this->title = $pctitle;
		$pcintro = nl2br(JText::_('CWMPRAYERLISTINTRO'));
		$this->intro = $pcintro;

		return parent::display($tpl);
	}
}
