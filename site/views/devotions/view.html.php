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
 * View Devotions Class
 *
 * @package  Prayer.Site
 *
 * @since    4.0
 */
class CWMPrayerViewDevotions extends JViewLegacy
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
		$sitehelper = new CWMPrayerSitePrayer;
		$pcConfig   = $sitehelper->pcConfig;

		$db = JFactory::getDBO();
		$db->setQuery("SELECT feed FROM #__cwmprayer_devotions WHERE published='1' ORDER BY ordering");

		$feed_array = $db->loadObjectList();

		// Set pathway information
		$this->afeed_array              = $feed_array;
		$this->config_show_page_headers = $pcConfig['config_show_page_headers'];
		$this->config_update_time       = $pcConfig['config_update_time'];
		$this->config_enable_cache      = $pcConfig['config_enable_cache'];
		$this->config_update_time       = $pcConfig['config_update_time'];
		$this->config_feed_image        = $pcConfig['config_feed_image'];
		$this->config_feed_descr        = $pcConfig['config_feed_descr'];
		$this->config_item_descr        = $pcConfig['config_item_descr'];
		$this->config_word_count        = $pcConfig['config_word_count'];
		$this->config_item_limit        = $pcConfig['config_item_limit'];
		$this->config_use_gb            = $pcConfig['config_use_gb'];

		$pctitle     = JText::_('CWMPRAYERTITLE');
		$this->title = $pctitle;

		$pcintro = nl2br(JText::_('CWMPRAYERLISTINTRO'));
		$this->intro = $pcintro;

		return parent::display($tpl);
	}
}
