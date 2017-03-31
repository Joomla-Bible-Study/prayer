<?php/** * @package     Joomla.Administrator * @subpackage  COM_PRAYER * * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved. * @license     GNU General Public License version 2 or later; see LICENSE.txt */defined('_JEXEC') or die;/** * Weblinks helper. * * @package     Joomla.Administrator * @subpackage  COM_PRAYER * @since       1.6 */class PrayerHelper extends JHelperContent{	public static $extension = 'com_prayer';	/**	 * Configure the Linkbar.	 *	 * @param   string  $vName  The name of the active view.	 *	 * @return void	 *	 * @since   1.6	 */	public static function addSubmenu($vName)	{		JHtmlSidebar::addEntry(			JText::_('CPanel'),			'index.php?option=com_prayer',			$vName == 'prayer'		);		JHtmlSidebar::addEntry(			JText::_('Requests'),			'index.php?option=com_prayer&view=managereq',			$vName == 'managereq'		);		JHtmlSidebar::addEntry(			JText::_('Subscribers'),			'index.php?option=com_prayer&view=managesub',			$vName == 'managesub'		);		JHtmlSidebar::addEntry(			JText::_('CSS'),			'index.php?option=com_prayer&view=managecss',			$vName == 'managecss'		);		JHtmlSidebar::addEntry(			JText::_('Files'),			'index.php?option=com_prayer&view=managefiles',			$vName == 'managefiles'		);		JHtmlSidebar::addEntry(			JText::_('Language Files'),			'index.php?option=com_prayer&view=managelang',			$vName == 'managelang'		);		JHtmlSidebar::addEntry(			JText::_('Devotionals'),			'index.php?option=com_prayer&view=managedevotions',			$vName == 'managedevotions'		);		JHtmlSidebar::addEntry(			JText::_('Links'),			'index.php?option=com_prayer&view=managelink',			$vName == 'managelink'		);		JHtmlSidebar::addEntry(			JText::_('Categories'),			'index.php?option=com_categories&extension=com_prayer',			$vName == 'categories'		);		if ($vName == 'categories')		{			JToolbarHelper::title(				JText::sprintf('COM_CATEGORIES_CATEGORIES_TITLE', JText::_('com_prayer')),				'prayer-categories');		}	}	/**	 * Gets a list of the actions that can be performed.	 *	 * @param   string   $component  The component name.	 * @param   string   $section    The access section name.	 * @param   integer  $id         The item ID.	 *	 * @return  JObject	 *	 * @since   3.2	 */	public static function getActions($component = '', $section = '', $id = 0)	{		$user   = JFactory::getUser();		$result = new JObject;		if (empty($component))		{			$component = self::$extension;		}		$path = JPATH_ADMINISTRATOR . '/components/' . $component . '/access.xml';		if ($section && $id)		{			$assetName = $component . '.' . $section . '.' . (int) $id;		}		else		{			$assetName = $component;		}		$actions = JAccess::getActionsFromFile($path, "/access/section[@name='component']/");		foreach ($actions as $action)		{			$result->set($action->name, $user->authorise($action->name, $assetName));		}		return $result;	}}