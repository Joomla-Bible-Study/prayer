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
jimport('joomla.form.helper');

JFormHelper::loadFieldClass('filelist');

/**
 * Fields for Image List
 *
 * @package  Prayer.Admin
 *
 * @since    4.0
 */
class JFormFieldImageList extends JFormField
{
	protected static $initialised = false;

	protected $type = 'ImageList';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getInput()
	{
		$images = [];

		$livesite = JURI::root();

		if (!self::$initialised)
		{
			$script = array();
			$script[] = '	function showImage(img) {';
			$script[] = '		var site = "' . $livesite . '"';
			$script[] = "		var imgObj = document.images['config_preview'];";
			$script[] = "		imgObj.src = site + 'media/com_cwmprayer/images/' + img;";
			$script[] = '	}';

			JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

			self::$initialised = true;
		}

		$html = array();

		$html[] = '<div class="fltlft"><img name="config_preview" src="' . JURI::root() .
			'media/com_cwmprayer/images/' . $this->value . '" style="height:70px;width:70px;" /></div>';

		$directory = (string) $this->element['directory'];

		$exclude = explode(",", $this->element['exclude']);

		$filter = (string) $this->element['filter'];

		$preview_script = "javascript:showImage(this.value);";

		$files = JFolder::files($directory, $filter, false, false, $exclude);

		if (is_array($files))
		{
			foreach ($files as $file)
			{
				$images[] = JHtml::_('select.option', $file, $file);
			}
		}

		$imagelist = JHtml::_(
			'select.genericlist',
			$images,
			"jform[params][config_imagefile]",
			array(
				'list.attr' => 'class="inputbox" size="1" ' . 'onchange="' . $preview_script . '"',
				'list.select' => $this->value
			)
		);

		$html[] = '&nbsp;&nbsp;<div valign="middle">' . $imagelist . "</div>";

		return implode("\n", $html);
	}
}
