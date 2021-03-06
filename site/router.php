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
 * Method to Build the Route
 *
 * @param   array  &$query  Info to Query
 *
 * @return array
 *
 * @since 4.0
 */
function CWMPrayerBuildRoute(&$query)
{
	$segments = array();

	if (isset($query['id']) && strpos($query['id'], ':'))
	{
		list($query['id'], $query['alias']) = explode(':', $query['id'], 2);
	}

	if (isset($query['task']))
	{
		$segments[] = $query['task'];

		unset($query['task']);
	}

	if (isset($query['id']))
	{
		if (isset($query['alias']))
		{
			$query['id'] .= ':' . $query['alias'];
		}

		if (isset($query['view']))
		{
			$segments[] = $query['view'];
		}

		$segments[] = $query['id'] . '-request';

		unset($query['view']);

		unset($query['id']);
	}


	if (isset($query['pop']))
	{
		$segments[] = $query['pop'];

		unset($query['pop']);
	}

	if (isset($query['Itemid']) && isset($query['alias']))
	{
		unset($query['Itemid']);
	}

	return $segments;
}

/**
 * Method to parse the Route
 *
 * @param   array  $segments  Parse Route Info
 *
 * @return array
 *
 * @since 4.0
 */
function CWMPrayerParseRoute($segments)
{
	$vars = array();

	// Count route segments
	$count = count($segments);

	// @todo need to find cout for route
	if ($count == '1')
	{
		$count--;

		$segment = array_shift($segments);

		$vars['task'] = $segment;
	}

	if ($count == '2')
	{
		$count--;

		$segment = array_shift($segments);

		$seg = explode(":", $segment);

		$vars['id'] = $seg[0];
	}

	if ($count == '3')
	{
		$count--;

		$segment = array_shift($segments);

		if (is_numeric($segment))
		{
			$vars['pop'] = $segment;
		}
	}

	if ($count == '4')
	{
		$count--;

		$segment = array_shift($segments);

		if (is_numeric($segment))
		{
			$vars['listtype'] = $segment;
		}
	}

	if ($count == '5')
	{
		$count--;

		$segment = array_shift($segments);

		if (is_numeric($segment))
		{
			$vars['title'] = $segment;
		}
	}

	if ($count == '6')
	{
		$count--;

		$segment = array_shift($segments);

		if (is_numeric($segment))
		{
			$vars['format'] = $segment;
		}
	}

	return $vars;
}
