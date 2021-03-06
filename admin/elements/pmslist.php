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

JFormHelper::loadFieldClass('filelist');

/**
 * Prayer PMS List Field
 *
 * @package  Prayer.Admin
 *
 * @since    4.0
 */
class JFormFieldPMSList extends JFormField
{
	protected static $initialised = false;

	protected $type = 'PMSList';

	/**
	 * Get Input
	 *
	 * @return  string  The field input markup.
	 *
	 * @since 4.0
	 */
	protected function getInput()
	{
		jimport('joomla.application.component.helper');

		$comParams = JComponentHelper::getParams('com_cwmprayer');

		$pcParamsArray = $comParams->toArray();

		$pmsrefarray = array(
			1 => array('val' => 'joomla', 'desc' => '' . JText::_(' - (Built-in Joomla Messaging Component. Requires Joomla 3.0 or above)') . ''),
			2 => array('val' => 'privmsg', 'desc' => '' . JText::_(' - (Requires PrivMSG 3.0.0 or above)') . ''),
			3 => array('val' => 'uddeim', 'desc' => '' . JText::_(' - (Requires uddeIM 2.9 or above)') . '')
		);

		$SelFiles = JFolder::files(JPATH_ADMINISTRATOR . '/components/com_cwmprayer/pms/');

		$files = array(JHTML::_('select.option', 0, '- Select -'));

		foreach ($SelFiles as $file)
		{
			if (strrpos($file, "php"))
			{
				preg_match('/^plg\.pms\.(.*)\.php$/', $file, $match);

				if ($match)
				{
					$keyarr = $this->prayer_array_search_recursive($match[1], $pmsrefarray);

					$key = $keyarr[0];

					$pmsfile = $pmsrefarray[$key]['desc'];

					$files[] = JHTML::_('select.option', $match[1], ucfirst($match[1] . $pmsfile));
				}
			}
		}

		$files = JHTML::_('select.genericlist', $files, "jform[params][config_pms_plugin]", 'class="inputbox" size="1" ' .
			'', 'value', 'text', $pcParamsArray['params']['config_pms_plugin']
		);

		return $files;
	}

	/**
	 * PC Array Search Resursive
	 *
	 * @param   string  $needle    ?
	 * @param   array   $haystack  ?
	 *
	 * @return array|null
	 *
	 * @since 4.0
	 */
	private function prayer_array_search_recursive($needle, $haystack)
	{
		$path = null;

		$keys = array_keys($haystack);

		while (!$path && (list($toss, $k) = each($keys)))
		{
			$v = $haystack[$k];

			if (is_scalar($v))
			{
				if (strtolower($v) === strtolower($needle))
				{
					$path = array($k);
				}
			}
			elseif (is_array($v))
			{
				if ($path = $this->prayer_array_search_recursive($needle, $v))
				{
					array_unshift($path, $k);
				}
			}
		}

		return $path;
	}
}
