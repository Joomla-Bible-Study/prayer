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

jimport('joomla.form.formfield');

/**
 * User Select Field
 *
 * @package     Prayer.Site
 *
 * @since       4.0
 * @deprecated  4.0  will be moving to core user list selecter.
 */
class JFormFieldUserSelect extends JFormField
{
	public $type = 'UserSelect';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getInput()
	{
		$html = array();

		$groups = $this->getGroups();

		$excluded = $this->getExcluded();

		$link = 'index.php?option=com_cwmprayer&amp;view=users&amp;layout=modal&amp;tmpl=component&amp;field=' . $this->id .
			(isset($groups) ? ('&amp;groups=' . base64_encode(json_encode($groups))) : '') . (isset($excluded) ? ('&amp;excluded=' .
				base64_encode(json_encode($excluded))) : '');

		$attr = $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';
		$attr .= $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';

		$onchange = (string) $this->element['onchange'];
		JHtml::_('behavior.modal', 'a.modal_' . $this->id);

		$script   = array();
		$script[] = '  function ltrim(str, chars) {';
		$script[] = '  	chars = chars || "\\s";';
		$script[] = '  	return str.replace(new RegExp("^[" + chars + "]+", "g"), "");';
		$script[] = '  }';
		$script[] = '  function rtrim(str, chars) {';
		$script[] = '  	chars = chars || "\\s";';
		$script[] = '  	return str.replace(new RegExp("[" + chars + "]+$", "g"), "");';
		$script[] = '  }';
		$script[] = '  function dedupe_list(sel)';
		$script[] = '  {';
		$script[] = '  	var count = 0;';
		$script[] = '  	var mainlist = document.getElementById(sel).value;';
		$script[] = '  	mainlist = mainlist.replace(/\r/gi, "");';
		$script[] = '  	mainlist = mainlist.replace(/\n+/gi, "");';
		$script[] = '   mainlist = ltrim(mainlist,",");';
		$script[] = '  	var listvalues = new Array();';
		$script[] = '  	var newlist = new Array();';
		$script[] = '  	listvalues = mainlist.split(",");';
		$script[] = '  	var hash = new Object();';
		$script[] = '  	for (var i=0; i<listvalues.length; i++)';
		$script[] = '  	{';
		$script[] = '  		if (hash[listvalues[i].toLowerCase()] != 1)';
		$script[] = '  		{';
		$script[] = '        if(listvalues[i] != \'No User\'){';
		$script[] = '  			newlist = newlist.concat(listvalues[i]);';
		$script[] = '        hash[listvalues[i].toLowerCase()] = 1';
		$script[] = '  		  }';
		$script[] = '      }';
		$script[] = '  		else { count++; }';
		$script[] = '  	}';
		$script[] = '  	document.getElementById(sel).value = newlist.join(",");';
		$script[] = '  }';
		$script[] = '	function jSelectUser_' . $this->id . '(id, title, email) {';
		$script[] = '		document.getElementById("' . $this->id . '_id").value = id;';
		$script[] = '		document.getElementById("' . $this->id . '_name").value = title;';
		$script[] = '		document.getElementById("' . $this->id . '_extuser").value = id + "-" + title;';
		$script[] = '		' . $onchange;
		$script[] = '   var selectlist = rtrim(\'' . $this->id . '\',\'select\');';
		$script[] = '   selectlist = selectlist + "user_list";';
		$script[] = "		dedupe_list(selectlist);";
		$script[] = '		SqueezeBox.close();';
		$script[] = '	}';

		JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

		$table = JTable::getInstance('user');

		if ($this->value)
		{
			$table->load($this->value);
		}
		else
		{
			$table->username = JText::_('JLIB_FORM_SELECT_USER');
		}

		$html[] = '<div class="fltlft">';

		$html[] = '	<input type="text" id="' . $this->id . '_name"' .
			' value="' . htmlspecialchars($table->name, ENT_COMPAT, 'UTF-8') . '"' .
			' disabled="disabled"' . $attr . ' />';

		$html[] = '</div>';
		$html[] = '<div class="button2-left">';
		$html[] = '  <div class="blank">';

		if ($this->element['readonly'] != 'true')
		{
			$html[] = '		<a class="modal_' . $this->id . '" title="' . JText::_('JLIB_FORM_CHANGE_USER') . '"' .
				' href="' . $link . '"' .
				' rel="{handler: \'iframe\', size: {x: 800, y: 500}}">';
			$html[] = '			' . JText::_('JLIB_FORM_CHANGE_USER') . '</a>';
		}

		$html[] = '  </div>';
		$html[] = '</div>';
		$html[] = '<input type="hidden" id="' . $this->id . '_id" name="' . $this->name . '" value="' . (int) $this->value . '" />';
		$html[] = '<input type="hidden" id="' . $this->id . '_extuser" value="" />';

		return implode("\n", $html);
	}

	/**
	 * Get Groups
	 *
	 * @return null
	 *
	 * @since version
	 */
	protected function getGroups()
	{
		return null;
	}

	/**
	 * Get Excluded
	 *
	 * @return null
	 *
	 * @since version
	 */
	protected function getExcluded()
	{
		return null;
	}
}
