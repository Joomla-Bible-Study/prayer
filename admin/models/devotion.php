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
 * Model for Devotion
 *
 * @since  4.0.0
 */
class CWMPrayerModelDevotion extends JModelAdmin
{
	/**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $text_prefix = 'COM_CWMPRAYER';

	/**
	 * The type alias for this content type.
	 *
	 * @var    string
	 * @since  3.2
	 */
	public $typeAlias = 'com_cwmprayer.devotion';

	/**
	 * The context used for the associations table
	 *
	 * @var    string
	 * @since  3.4.4
	 */
	protected $associationsContext = 'com_cwmprayer.item';

	/**
	 * Batch copy/move command. If set to false,
	 * the batch copy/move command is not supported
	 *
	 * @var    string
	 * @since  3.4
	 */
	protected $batch_copymove = 'category_id';

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
	public function getTable($name = 'Devotion', $prefix = 'CWMPrayerTable', $options = [])
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
	 * @since   1.6
	 */
	public function getForm($data = [], $loadData = true)
	{
		JForm::addFieldPath(JPATH_ADMINISTRATOR . '/components/com_users/models/fields');

		$form = $this->loadForm('com_cwmprayer.devotion', 'devotion', ['control' => 'jform', 'load_data' => $loadData]);

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  array  The default data is an empty array.
	 *
	 * @since   1.6
	 */
	protected function loadFormData()
	{
		$app = JFactory::getApplication();

		// Check the session for previously entered form data.
		$data = $app->getUserState('com_cwmprayer.edit.devotion.data', array());

		if (empty($data))
		{
			$data = $this->getItem();

			// Prime some default values.
			if ($this->getState('contact.id') == 0)
			{
				$data->set('catid', $app->input->get('catid', $app->getUserState('com_cwmprayer.devotions.filter.category_id'), 'int'));
			}
		}

		$this->preprocessData('com_cwmprayer.devotion', $data);

		return $data;
	}

	/**
	 * Prepare and sanitise the table data prior to saving.
	 *
	 * @param   CWMPrayerTableDevotion  $table  A reference to a JTable object.
	 *
	 * @return  void
	 *
	 * @since   4.0
	 */
	protected function prepareTable($table)
	{
		$date = JFactory::getDate()->toSql();
		$user = JFactory::getUser();

		$table->name = htmlspecialchars_decode($table->name, ENT_QUOTES);

		if (empty($table->id))
		{
			// Set the values
			// Set ordering to the last item if not set
			if (empty($table->ordering))
			{
				$db = JFactory::getDbo();
				$db->setQuery('SELECT MAX(ordering) FROM #__cwmprayer_devotions');
				$max             = $db->loadResult();
				$table->ordering = $max + 1;
			}
			else
			{
				// Set the values
				$table->modified    = $date;
				$table->modified_by = $user->get('id');
			}

			// Increment the content version number.
			$table->version++;
		}
	}
}
