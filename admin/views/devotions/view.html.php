<?php/** * prayer Component * * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL * */// No direct accessdefined('_JEXEC') or die('Restricted access');class PrayerViewDevotions extends JViewLegacy{	protected $items;	protected $pagination;	protected $state;	public function display($tpl = null)	{		$this->items = $this->get('Items');		$this->state = $this->get('State');		$this->pagination = $this->get('Pagination');		// Check for errors.		if (count($errors = $this->get('Errors')))		{			JError::raiseError(500, implode("\n", $errors));			return false;		}		PrayerHelper::addSubmenu('devotions');		$this->addToolbar();		$this->sidebar = JHtmlSidebar::render();		return parent::display($tpl);	}	protected function addToolbar()	{		JToolbarHelper::title(JText::_('prayer - Manage Devotionals'));		JToolBarHelper::addNew("adddevotion");		JToolBarHelper::publishList("prayer.publishdevotion");		JToolBarHelper::unpublishList("prayer.unpublishdevotion");		JToolBarHelper::editList("editdevotion");		JToolBarHelper::deleteList("Remove Devotional(s)?", "prayer.remove_devotion", 'Remove');		JHtmlSidebar::setAction('index.php?option=com_prayer');	}	protected function getSortFields()	{		return array(			'a.ordering' => JText::_('JGRID_HEADING_ORDERING'),			'a.name' => JText::_('Name'),			'a.feed' => JText::_('Feed'),			'a.category' => JText::_('Category'),			'a.published' => JText::_('JPUBLISHED'),			'a.id' => JText::_('JGRID_HEADING_ID')		);	}}