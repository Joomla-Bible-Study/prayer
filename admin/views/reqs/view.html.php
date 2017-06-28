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
 * Class view for Reqs
 *
 * @package  Prayer.Admin
 *
 * @since    4.0
 */
class CWMPrayerViewReqs extends JViewLegacy
{
	protected $items;

	protected $pagination;

	/** @var  Joomla\Registry\Registry
	 * @since 4.0
	 */
	protected $state;

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
		$this->items         = $this->get('Items');
		$this->state         = $this->get('State');
		$this->pagination    = $this->get('Pagination');
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JFactory::getApplication()->enqueueMessage(implode("\n", $errors), 'error');

			return false;
		}

		CWMPrayerHelper::addSubmenu('reqs');

		$this->addToolbar();

		$this->sidebar = JHtmlSidebar::render();

		return parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since    4.0
	 * @return void
	 */
	protected function addToolbar()
	{
		$canDo = JHelperContent::getActions('com_cwmprayer', 'category', $this->state->get('filter.category_id'));

		JToolbarHelper::title(JText::_('COM_CWMPRAYER_TITLE_REQS'));

		if (($canDo->get('core.edit')) || ($canDo->get('core.edit.own')))
		{
			JToolBarHelper::editList("req.edit");
		}


		if ($canDo->get('core.edit.state'))
		{
			JToolBarHelper::publishList("reqs.publish", 'JTOOLBAR_PUBLISH');
			JToolBarHelper::unpublishList("reqs.unpublish", 'JTOOLBAR_UNPUBLISH');
			JToolBarHelper::archiveList("reqs.archive");
			JToolBarHelper::unarchiveList("reqs.unarchive");
		}

		if ($this->state->get('filter.state') == -2 && $canDo->get('core.delete'))
		{
			JToolBarHelper::deleteList('JGLOBAL_CONFIRM_DELETE', "reqs.delete", 'JTOOLBAR_EMPTY_TRASH');
		}
		elseif ($canDo->get('core.edit.state'))
		{
			JToolbarHelper::trash('reqs.trash');
		}

		$cb = JToolBar::getInstance('toolbar');

		$cb->appendButton(
			'confirm',
			JText::_('COM_CWMPRAYER_CUSTOM_BUTTON'),
			'apply',
			'COM_CWMPRAYER_PURGE',
			'req.purge',
			false
		);
	}
}
