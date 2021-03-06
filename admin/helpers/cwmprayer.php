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
 * Weblinks helper.
 *
 * @package     Joomla.Administrator
 * @subpackage  COM_CWMPRAYER
 * @since       1.6
 */
class CWMPrayerHelper extends JHelperContent
{
	public static $extension = 'com_cwmprayer';

	/**
	 * Configure the Linkbar.
	 *
	 * @param   string  $vName  The name of the active view.
	 *
	 * @return void
	 *
	 * @since   1.6
	 */
	public static function addSubmenu($vName)
	{
		JHtmlSidebar::addEntry(
			JText::_('COM_CWMPRAYER_SUBMENU_CPANEL'),
			'index.php?option=com_cwmprayer',
			$vName == 'prayer'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_CWMPRAYER_SUBMENU_REQUESTS'),
			'index.php?option=com_cwmprayer&view=reqs',
			$vName == 'reqs'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_CWMPRAYER_SUBMENU_SUBSCRIBES'),
			'index.php?option=com_cwmprayer&view=subs',
			$vName == 'subs'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_CWMPRAYER_SUBMENU_CSS'),
			'index.php?option=com_cwmprayer&view=css',
			$vName == 'css'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_CWMPRAYER_SUBMENU_FILES'),
			'index.php?option=com_cwmprayer&view=files',
			$vName == 'files'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_CWMPRAYER_SUBMENU_DEVOTIONALS'),
			'index.php?option=com_cwmprayer&view=devotions',
			$vName == 'devotions'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_CWMPRAYER_SUBMENU_LINKS'),
			'index.php?option=com_cwmprayer&view=links',
			$vName == 'links'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_CWMPRAYER_SUBMENU_CATEGORIES'),
			'index.php?option=com_categories&extension=com_cwmprayer',
			$vName == 'categories'
		);

		if ($vName == 'categories')
		{
			JToolbarHelper::title(
				JText::sprintf('COM_CATEGORIES_CATEGORIES_TITLE', JText::_('com_cwmprayer')),
				'prayer-categories');
		}
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @param   string   $component  The component name.
	 * @param   string   $section    The access section name.
	 * @param   integer  $id         The item ID.
	 *
	 * @return  JObject
	 *
	 * @since   3.2
	 */
	public static function getActions($component = '', $section = '', $id = 0)
	{
		// Check for deprecated arguments order
		if (is_int($component) || is_null($component))
		{
			$result = JHelperContent::getActions($component, $section, $id);

			return $result;
		}

		$assetName = $component;

		if ($section && $id)
		{
			$assetName .= '.' . $section . '.' . (int) $id;
		}

		$result = new JObject;

		$user = JFactory::getUser();

		$actions = JAccess::getActionsFromFile(
			JPATH_ADMINISTRATOR . '/components/' . $component . '/access.xml', '/access/section[@name="component"]/'
		);

		if ($actions === false)
		{
			JLog::add(
				JText::sprintf('JLIB_ERROR_COMPONENTS_ACL_CONFIGURATION_FILE_MISSING_OR_IMPROPERLY_STRUCTURED', $component), JLog::ERROR, 'jerror'
			);

			return $result;
		}

		foreach ($actions as $action)
		{
			$result->set($action->name, $user->authorise($action->name, $assetName));
		}

		return $result;
	}
}
