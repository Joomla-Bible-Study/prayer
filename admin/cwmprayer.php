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

if (!JFactory::getUser()->authorise('core.manage', 'com_cwmprayer'))
{
	throw new JAccessExceptionNotallowed(JText::_('JERROR_ALERTNOAUTHOR'), 404);
}

// Always load JBSM API if it exists.
$api = JPATH_ADMINISTRATOR . '/components/com_cwmprayer/api.php';

if (file_exists($api))
{
	require_once $api;
}

// Require_once JPATH_ADMINISTRATOR . '/components/com_cwmprayer/helpers/admin_includes.php';

// Require helper file
$controller = JControllerLegacy::getInstance('CWMPrayer');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
