<?php/** * @package     Joomla.Administrator * @subpackage  COM_PRAYER * * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved. * @license     GNU General Public License version 2 or later; see LICENSE.txt */defined('_JEXEC') or die;/** * Weblinks helper. * * @package     Joomla.Administrator * @subpackage  COM_PRAYER * @since       1.6 */class PrayerHelper{	public static $extension = 'com_prayer';	/**	 * Configure the Linkbar.	 *	 * @param   string    The name of the active view.	 *	 * @since   1.6	 */	public static function addSubmenu($vName = 'prayer')	{		JSubMenuHelper::addEntry(			JText::_('CPanel'),			'index.php?option=com_prayer',			$vName == 'prayer'		);		JSubMenuHelper::addEntry(			JText::_('Requests'),			'index.php?option=com_prayer&task=manage_req',			$vName == 'managereq'		);		JSubMenuHelper::addEntry(			JText::_('Subscribers'),			'index.php?option=com_prayer&task=manage_sub',			$vName == 'managesub'		);		JSubMenuHelper::addEntry(			JText::_('CSS'),			'index.php?option=com_prayer&task=manage_css',			$vName == 'managecss'		);		JSubMenuHelper::addEntry(			JText::_('Files'),			'index.php?option=com_prayer&task=manage_files',			$vName == 'managefiles'		);		JSubMenuHelper::addEntry(			JText::_('Language Files'),			'index.php?option=com_prayer&task=manage_lang',			$vName == 'managelang'		);		JSubMenuHelper::addEntry(			JText::_('Devotionals'),			'index.php?option=com_prayer&task=manage_dev',			$vName == 'managedevotions'		);		JSubMenuHelper::addEntry(			JText::_('Links'),			'index.php?option=com_prayer&task=manage_link',			$vName == 'managelink'		);		JSubMenuHelper::addEntry(			JText::_('Categories'),			'index.php?option=com_categories&extension=com_prayer',			$vName == 'categories'		);		if ($vName == 'categories')		{			JToolbarHelper::title(				JText::sprintf('COM_CATEGORIES_CATEGORIES_TITLE', JText::_('com_prayer')),				'prayer-categories');		}	}	/**	 * Gets a list of the actions that can be performed.	 *	 * @param   integer  The category ID.	 *	 * @return  JObject	 * @since   1.6	 */	public static function getActions($categoryId = 0)	{		$user = JFactory::getUser();		$result = new JObject;		if (empty($categoryId))		{			$assetName = 'com_prayer';			$level = 'component';		}		else		{			$assetName = 'COM_PRAYER.category.' . (int) $categoryId;			$level = 'category';		}		$actions = JAccess::getActions('com_prayer', $level);		foreach ($actions as $action)		{			$result->set($action->name, $user->authorise($action->name, $assetName));		}		return $result;	}}