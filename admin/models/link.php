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
 * CWM Prayer Model Link class
 *
 * @package  CWMPrayer.Admin
 *
 * @since    4.0
 */
class CWMPrayerModelLink extends JModelAdmin
{
	protected $text_prefix = 'com_cwmprayer';

	/**
	 * Method to get a table object, load it if necessary.
	 *
	 * @param   string  $name     The table name. Optional.
	 * @param   string  $prefix   The class prefix. Optional.
	 * @param   array   $options  Configuration array for model. Optional.
	 *
	 * @return  JTable  A JTable object
	 *
	 * @since   4.0
	 * @throws  Exception
	 */
	public function getTable($name = 'Links', $prefix = 'CWMPrayerTable', $options = array())
	{
		return JTable::getInstance($name, $prefix, $options);
	}

	/**
	 * Get Form
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  JForm|boolean  A JForm object on success, false on failure
	 *
	 * @since 4.0
	 */
	public function getForm($data = array(), $loadData = true)
	{
		$form = $this->loadForm('com_cwmprayer.link', 'link', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * @return  void
	 *
	 * @note    Calling getState in this method will result in recursion.
	 * @since   4.0
	 */
	protected function populateState()
	{
		parent::populateState();

		$input = JFactory::getApplication()->input;
		$user = JFactory::getUser();

		$this->setState('user.id', $user->get('id'));
		$edit = $input->get('edit');

		$this->setState('edit', $edit);
		$planid = $input->get('cid');

		$this->setState('id', $planid[0]);
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  object  The default data is an empty array.
	 *
	 * @since   1.6
	 */
	protected function loadFormData()
	{
		if (empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Prepare and sanitise the table data prior to saving.
	 *
	 * @param   \CWMPrayerTableLinks  $table  A reference to a JTable object.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function prepareTable($table)
	{
		$date = JFactory::getDate();
		$user = JFactory::getUser();

		$table->name = htmlspecialchars_decode($table->name, ENT_QUOTES);

		$table->alias = JApplicationHelper::stringURLSafe($table->alias);

		if (empty($table->alias))
		{
			$table->alias = JApplicationHelper::stringURLSafe($table->name);
		}

		if (empty($table->id))
		{
			// Set the values
			// Set ordering to the last item if not set
			if (empty($table->ordering))
			{
				$db = JFactory::getDbo();

				$db->setQuery('SELECT MAX(ordering) FROM #__cwmprayer_links');

				$max = $db->loadResult();

				$table->ordering = $max + 1;
			}
			else
			{
				// Set the values
				$table->modified = $date->toSql();

				$table->modified_by = $user->get('id');
			}
		}
	}
}
