<?php
/**
 * Core Admin CWMPrayer file
 *
 * @package    CWMPrayer.Admin
 * @copyright  2007 - 2015 (C) Joomla Bible Study Team All rights reserved
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link       http://www.JoomlaBibleStudy.org
 * */
defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');

JFormHelper::loadFieldClass('list');

/**
 * Day List Field
 *
 * @package  Prayer.Admin
 *
 * @since    4.0
 */
class JFormFieldDaylist extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var        string
	 * @since    1.6
	 */
	protected $type = 'Daylist';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  array  The field input markup.
	 *
	 * @since   1.6
	 */
	protected function getOptions()
	{
		$day  = '';
		$days = array();

		for ($i = 0; $i < 7; $i++)
		{
			$days[$i] = new stdClass;
			$days[$i]->id = $i;

			if ($i == 0)
			{
				$day = 'Sun';
			}
			elseif ($i == 1)
			{
				$day = 'Mon';
			}
			elseif ($i == 2)
			{
				$day = 'Tue';
			}
			elseif ($i == 3)
			{
				$day = 'Wed';
			}
			elseif ($i == 4)
			{
				$day = 'Thu';
			}
			elseif ($i == 5)
			{
				$day = 'Fri';
			}
			elseif ($i == 6)
			{
				$day = 'Sat';
			}

			$days[$i]->name = JHTML::Date($day, 'D');
		}

		$options = array();

		foreach ($days as $day)
		{
			$options[] = JHtml::_('select.option', $day->id, $day->name);
		}

		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
