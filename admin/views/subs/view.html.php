<?php
/**
 * Core Admin CWMPrayer file
 *
 * @package    CWMPrayer.Admin
 * @copyright  2007 - 2015 (C) Joomla Bible Study Team All rights reserved
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link       http://www.JoomlaBibleStudy.org
 * */

// No direct access
defined('_JEXEC') or die;

/**
 * Prayer Subs
 *
 * @package  Prayer.Admin
 *
 * @since    4.0
 */
class CWMPrayerViewSubs extends JViewLegacy
{
	protected $items;

	protected $pagination;

	protected $state;

	protected $sidebar;

	/**
	 * Display
	 *
	 * @param   string  $tpl  Timplate seting
	 *
	 * @return bool|mixed
	 *
	 * @since 4.0
	 */
	public function display($tpl = null)
	{
		$this->items         = $this->get('Items');
		$this->state         = $this->get('State');
		$this->pagination    = $this->get('Pagination');
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));

			return false;
		}

		CWMPrayerHelper::addSubmenu('managesub');

		$this->addToolbar();

		$this->sidebar = JHtmlSidebar::render();

		return parent::display($tpl);
	}

	/**
	 * Address ToolBar
	 *
	 * @return void
	 *
	 * @since 4.0
	 */
	protected function addToolbar()
	{
		JToolbarHelper::title(JText::_('COM_CWMPRAYER_TITLE_SUBS'));
		JToolBarHelper::publishList("subs.approve", 'COM_CWMPRAYER_APPROVE');
		JToolBarHelper::unpublishList("subs.unapprove", 'COM_CWMPRAYER_UNAPPROVE');
		JToolBarHelper::deleteList("Remove Subscriber(s)?", "subs.del");
	}
}
