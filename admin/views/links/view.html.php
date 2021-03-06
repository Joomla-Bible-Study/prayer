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
 * Class view for Links
 *
 * @package  Prayer.Admin
 *
 * @since    4.0
 */
class CWMPrayerViewLinks extends JViewLegacy
{
	protected $items;

	protected $pagination;

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
		$this->items = $this->get('Items');
		$this->state = $this->get('State');
		$this->pagination = $this->get('Pagination');
		$this->filterForm    = $this->get('FilterForm');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));

			return false;
		}

		CWMPrayerHelper::addSubmenu('links');

		$this->addToolbar();

		$this->sidebar = JHtmlSidebar::render();

		return parent::display($tpl);
	}

	/**
	 * Add Toolbar
	 *
	 * @return void
	 *
	 * @since 4.0
	 */
	protected function addToolbar()
	{
		JToolbarHelper::title(JText::_('COM_CWMPRAYER_TITLE_LINKS'));
		JToolBarHelper::addNew("link.add");
		JToolBarHelper::publishList("link.publish");
		JToolBarHelper::unpublishList("link.unpublish");
		JToolBarHelper::editList("link.edit");
		JToolBarHelper::deleteList("Remove Link(s)?", "link.delete");
	}
}
