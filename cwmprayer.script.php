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
 * Prayer Installer Script
 *
 * @package  Prayer.Site
 *
 * @since    4.0
 */
class Com_CWMPrayerInstallerScript
{
	private $release = '4.0.0';

	private $params;

	private $config;

	/**
	 * The list of extra modules and plugins to install
	 *
	 * @author Nicholas K. Dionysopoulos
	 * @var   array $_installation_queue Array of Items to install
	 * @since 1.7.0
	 */
	private $installation_queue = [
		// -- modules => { (folder) => { (module) => { (position), (published) } }* }*
		'modules' => [
			'site'  => ['cwmprayer_latest' => 0, 'cwmprayer_menu' => 0, 'cwmprayer_submit_request' => 0, 'cwmprayer_subscribe' => 0]
		],
		// -- plugins => { (folder) => { (element) => (published) }* }*
		'plugins' => [
			'system' => ['cwmprayeremail' => 1]
		],
	];

	protected $versions = array(
		'PHP'     => array(
			'5.6' => '5.6.30',
			'0'   => '7.0.14' // Preferred version
		),
		'MySQL'   => array(
			'5.1' => '5.1',
			'0'   => '5.5' // Preferred version
		),
		'Joomla!' => array(
			'3.7' => '3.7',
			'0'   => '3.7.2' // Preferred version
		)
	);

	/**
	 * Install
	 *
	 * @param   JInstallerFile  $parent  Where it is coming from
	 *
	 * @return void
	 *
	 * @since 4.0
	 */
	public function install($parent)
	{
		$this->params = $this->getParams();
	}

	/**
	 * Get Params
	 *
	 * @return array|null
	 *
	 * @since 4.0
	 */
	private function getParams()
	{
		$xml = simplexml_load_file(JPATH_ROOT . '/administrator/components/com_cwmprayer/config.xml');

		$ini = array();

		/** @var array $fieldsets */
		$fieldsets = $xml->fields->fieldset;

		$fieldscount = count($fieldsets);

		for ($i = 0; $i < $fieldscount; $i++)
		{
			if (!count($fieldsets[$i]->children()))
			{
				return null;
			}

			foreach ($fieldsets[$i] as $field)
			{
				if (($name = $field->attributes()->name) === null)
				{
					continue;
				}

				if (($value = $field->attributes()->default) === null)
				{
					continue;
				}

				if ($name != '@spacer')
				{
					$ini[(string) $name] = (string) $value;
				}
			}
		}

		return $ini;
	}

	/**
	 * Set Params
	 *
	 * @param   array  $param_array  ?
	 *
	 * @return void
	 *
	 * @since version
	 */
	public function setParams($param_array)
	{
		if (count($param_array) > 0)
		{
			$params = [];
			$db     = JFactory::getDbo();

			foreach ($param_array as $name => $value)
			{
				$params['params'][(string) $name] = (string) $value;
			}

			$paramString = json_encode($params);

			$db->setQuery('UPDATE #__extensions SET params=' . $db->quote($paramString) . ' WHERE element="com_cwmprayer"');

			$db->execute();
		}
	}

	/**
	 * Uninstall
	 *
	 * @param   JInstallerFile  $parent  Where it is coming from
	 *
	 * @return void
	 *
	 * @since  4.0
	 */
	public function uninstall($parent)
	{
		$this->_uninstallSubextensions($parent);
	}

	/**
	 * Update
	 *
	 * @param   JInstallerFile  $parent  Where it is coming from
	 *
	 * @return void
	 *
	 * @since 4.0
	 */
	public function update($parent)
	{
		echo '<p>' . JText::_('COM_CWMPRAYER_UPDATE_TEXT') . '</p>';
	}

	/**
	 * Pre Flight
	 *
	 * @param   string          $type    Type of install
	 * @param   JInstallerFile  $parent  Where it is coming from
	 *
	 * @return bool
	 *
	 * @since  4.0
	 */
	public function preflight($type, $parent)
	{
		// Install subextensions
		$this->_installSubextensions($parent);

		$JVersion = new JVersion;

		if (version_compare($JVersion->getShortVersion(), '3.0', 'lt'))
		{
			JError::raiseWarning(null, 'Cannot install Prayer in a Joomla release prior to 3.0');

			return false;
		}

		if ($type == 'update')
		{
			include_once 'components/com_cwmprayer/helpers/version.php';

			$pcversion = &CWMPrayerVersion::getInstance();

			$oldrelease = $pcversion->getShortVersion();

			$rel = $oldrelease . ' to ' . $this->release;

			if (version_compare($this->release, $oldrelease, 'le'))
			{
				JError::raiseWarning(null, 'Incorrect version sequence.  Cannot upgrade ' . $rel);

				return false;
			}
			else
			{
				$rel = $this->release;
			}
		}

		return true;
	}

	/**
	 * Check Requirements
	 *
	 * @param   string  $version  JBSM version to check for.
	 *
	 * @return bool
	 *
	 * @since 7.1.0
	 */
	public function checkRequirements($version)
	{
		$db   = JFactory::getDbo();
		$pass = $this->checkVersion('PHP', phpversion());
		$pass &= $this->checkVersion('Joomla!', JVERSION);
		$pass &= $this->checkVersion('MySQL', $db->getVersion());

		return $pass;
	}

	/**
	 * Check Verions of JBSM
	 *
	 * @param   string  $name     Name of version
	 * @param   string  $version  Version to look for
	 *
	 * @return bool
	 *
	 * @throws \Exception
	 *
	 * @since 7.1.0
	 */
	protected function checkVersion($name, $version)
	{
		$app   = JFactory::getApplication();
		$major = $minor = 0;

		foreach ($this->versions[$name] as $major => $minor)
		{
			if (!$major || version_compare($version, $major, '<'))
			{
				continue;
			}

			if (version_compare($version, $minor, '>='))
			{
				return true;
			}

			break;
		}

		if (!$major)
		{
			$minor = reset($this->versions[$name]);
		}

		$recommended = end($this->versions[$name]);
		$app->enqueueMessage(
			sprintf("%s %s is not supported. Minimum required version is %s %s, but it is higly recommended to use %s %s or later.",
				$name, $version, $name, $minor, $name, $recommended
			), 'notice'
		);

		return false;
	}

	/**
	 * Post Flight
	 *
	 * @param   string          $type    Type of install
	 * @param   JInstallerFile  $parent  Where it is coming from
	 *
	 * @return void
	 *
	 * @since 4.0
	 */
	public function postflight($type, $parent)
	{
		if ($type == 'update')
		{
			$pcParams = JComponentHelper::getParams('com_cwmprayer');

			$pcParamsArray = $pcParams->toArray();

			foreach ($pcParamsArray['params'] as $name => $value)
			{
				$this->config[(string) $name] = (string) $value;
			}

			$this->setRules($this->config);

			$this->updateSendTo();

			$parent->getParent()->setRedirectURL('index.php?option=com_installer&view=update');
		}
		elseif ($type == 'install')
		{
			$this->setParams($this->params);

			$this->setRules();

			$this->addPrayerCategory();

			$this->updateSendTo();

			$parent->getParent()->setRedirectURL('index.php?option=com_cwmprayer');
		}
	}

	/**
	 * Installs subextensions (modules, plugins) bundled with the main extension
	 *
	 * @param   JInstallerAdapter  $parent  is the class calling this method.
	 *
	 * @return Object The subextension installation status
	 *
	 * @since 1.7.0
	 */
	private function _installSubextensions($parent)
	{
		$src = $parent->getParent()->getPath('source');

		$db = JFactory::getDbo();

		$status          = new stdClass;
		$status->modules = [];
		$status->plugins = [];

		// Modules installation
		if (count($this->installation_queue['modules']))
		{
			foreach ($this->installation_queue['modules'] as $folder => $modules)
			{
				if (count($modules))
				{
					foreach ($modules as $module => $modulePreferences)
					{
						// Install the module
						if (empty($folder))
						{
							$folder = 'site';
						}

						$path = "$src/modules/$folder/$module";

						if (!is_dir($path))
						{
							$path = "$src/modules/$folder/mod_$module";
						}

						if (!is_dir($path))
						{
							$path = "$src/modules/$module";
						}

						if (!is_dir($path))
						{
							$path = "$src/modules/mod_$module";
						}

						if (!is_dir($path))
						{
							continue;
						}

						// Was the module already installed?
						$sql = $db->getQuery(true)->select('COUNT(*)')
							->from('#__modules')
							->where($db->qn('module') . ' = ' . $db->q('mod_' . $module));
						$db->setQuery($sql);
						$count             = $db->loadResult();
						$installer         = new JInstaller;
						$result            = $installer->install($path);
						$status->modules[] = [
							'name'   => 'mod_' . $module,
							'client' => $folder,
							'result' => $result
						];

						// Modify where it's published and its published state
						if (!$count)
						{
							// A. Position and state
							list($modulePosition, $modulePublished) = $modulePreferences;

							if ($modulePosition == 'cpanel')
							{
								$modulePosition = 'icon';
							}

							$sql = $db->getQuery(true)
								->update($db->qn('#__modules'))
								->set($db->qn('position') . ' = ' . $db->q($modulePosition))
								->where($db->qn('module') . ' = ' . $db->q('mod_' . $module));

							if ($modulePublished)
							{
								$sql->set($db->qn('published') . ' = ' . $db->q('1'));
							}

							$db->setQuery($sql);
							$db->execute();

							// B. Change the ordering of back-end modules to 1 + max ordering
							if ($folder == 'admin')
							{
								$query = $db->getQuery(true);
								$query->select('MAX(' . $db->qn('ordering') . ')')
									->from($db->qn('#__modules'))
									->where($db->qn('position') . '=' . $db->q($modulePosition));
								$db->setQuery($query);
								$position = $db->loadResult();
								$position++;

								$query = $db->getQuery(true);
								$query->update($db->qn('#__modules'))
									->set($db->qn('ordering') . ' = ' . $db->q($position))
									->where($db->qn('module') . ' = ' . $db->q('mod_' . $module));
								$db->setQuery($query);
								$db->execute();
							}

							// C. Link to all pages
							$query = $db->getQuery(true);
							$query->select('id')
								->from($db->qn('#__modules'))
								->where($db->qn('module') . ' = ' . $db->q('mod_' . $module));
							$db->setQuery($query);
							$moduleid = $db->loadResult();

							$query = $db->getQuery(true);
							$query->select('*')
								->from($db->qn('#__modules_menu'))
								->where($db->qn('moduleid') . ' = ' . $db->q($moduleid));
							$db->setQuery($query);
							$assignments = $db->loadObjectList();
							$isAssigned  = !empty($assignments);

							if (!$isAssigned)
							{
								$o = (object) [
									'moduleid' => $moduleid,
									'menuid'   => 0
								];
								$db->insertObject('#__modules_menu', $o);
							}
						}
					}
				}
			}
		}

		// Plugins installation
		if (count($this->installation_queue['plugins']))
		{
			foreach ($this->installation_queue['plugins'] as $folder => $plugins)
			{
				if (count($plugins))
				{
					foreach ($plugins as $plugin => $published)
					{
						$path = "$src/plugins/$folder/$plugin";

						if (!is_dir($path))
						{
							$path = "$src/plugins/$folder/plg_$plugin";
						}

						if (!is_dir($path))
						{
							$path = "$src/plugins/$plugin";
						}

						if (!is_dir($path))
						{
							$path = "$src/plugins/plg_$plugin";
						}

						if (!is_dir($path))
						{
							continue;
						}

						// Was the plugin already installed?
						$query = $db->getQuery(true)
							->select('COUNT(*)')
							->from($db->qn('#__extensions'))
							->where($db->qn('element') . ' = ' . $db->q($plugin))
							->where($db->qn('folder') . ' = ' . $db->q($folder));
						$db->setQuery($query);
						$count = $db->loadResult();

						$installer = new JInstaller;
						$result    = $installer->install($path);

						$status->plugins[] = [
							'name'   => 'plg_' . $plugin,
							'group'  => $folder,
							'result' => $result
						];

						if ($published && !$count)
						{
							$query = $db->getQuery(true)
								->update('#__extensions')
								->set($db->qn('enabled') . ' = ' . $db->q('1'))
								->where($db->qn('element') . ' = ' . $db->q($plugin))
								->where($db->qn('folder') . ' = ' . $db->q($folder));
							$db->setQuery($query);
							$db->execute();
						}
					}
				}
			}
		}

		return $status;
	}

	/**
	 * Uninstalls subextensions (modules, plugins) bundled with the main extension
	 *
	 * @param   JInstallerAdapter  $parent  is the class calling this method.
	 *
	 * @return void
	 *
	 * @since 1.7.0
	 */
	private function _uninstallSubextensions($parent)
	{
		jimport('joomla.installer.installer');

		$db = JFactory::getDbo();

		// Modules uninstalling
		if (count($this->installation_queue['modules']))
		{
			foreach ($this->installation_queue['modules'] as $folder => $modules)
			{
				if (count($modules))
				{
					foreach ($modules as $module => $modulePreferences)
					{
						// Find the module ID
						$sql = $db->getQuery(true)
							->select($db->qn('extension_id'))
							->from($db->qn('#__extensions'))
							->where($db->qn('element') . ' = ' . $db->q('mod_' . $module))
							->where($db->qn('type') . ' = ' . $db->q('module'));
						$db->setQuery($sql);
						$id = $db->loadResult();

						// Uninstall the module
						if ($id)
						{
							$installer         = new JInstaller;
							$result            = $installer->uninstall('module', $id, 1);
							$this->status->modules[] = [
								'name'   => 'mod_' . $module,
								'client' => $folder,
								'result' => $result
							];
						}
					}
				}
			}
		}

		// Plugins uninstalling
		if (count($this->installation_queue['plugins']))
		{
			foreach ($this->installation_queue['plugins'] as $folder => $plugins)
			{
				if (count($plugins))
				{
					foreach ($plugins as $plugin => $published)
					{
						$sql = $db->getQuery(true)
							->select($db->qn('extension_id'))
							->from($db->qn('#__extensions'))
							->where($db->qn('type') . ' = ' . $db->q('plugin'))
							->where($db->qn('element') . ' = ' . $db->q($plugin))
							->where($db->qn('folder') . ' = ' . $db->q($folder));
						$db->setQuery($sql);

						$id = $db->loadResult();

						if ($id)
						{
							$installer         = new JInstaller;
							$result            = $installer->uninstall('plugin', $id, 1);
							$this->status->plugins[] = [
								'name'   => 'plg_' . $plugin,
								'group'  => $folder,
								'result' => $result
							];
						}
					}
				}
			}
		}

		return;
	}

	/**
	 * Set Rules
	 *
	 * @param   array  $param_array  ?
	 *
	 * @return void
	 *
	 * @since 4.0
	 */
	private function setRules($param_array = array())
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('rules');
		$query->from('#__assets');
		$query->group('id, rules, lft');
		$query->where('(name = ' . $db->q('com_cwmprayer') . ')');

		$db->setQuery($query);

		$result = $db->loadColumn();

		if ($result[0] == '{}')
		{
			$rules = json_encode(
				array(
					'cwmprayer.view' => array(1 => 1),
					'cwmprayer.post' => array(1 => 1),
					'cwmprayer.publish' => array(7 => 1, 8 => 1),
					'cwmprayer.subscribe' => array(1 => 1),
					'cwmprayer.devotional' => array(1 => 1),
					'cwmprayer.links' => array(1 => 1),
					'core.admin' => array(),
					'core.manage' => array(),
					'core.create' => array(),
					'core.delete' => array(),
					'core.edit' => array(),
					'core.edit.state' => array(),
					'core.edit.own' => array()
				)
			);

			$db->setQuery("UPDATE #__assets SET rules=" . $db->q($rules) . " WHERE name='com_cwmprayer'");

			$db->execute();
		}
	}

	/**
	 * Update Send To
	 *
	 * @return mixed
	 *
	 * @since 4.0
	 */
	private function updateSendTo()
	{
		jimport('joomla.date.date');

		$dateset = new JDate;

		$now = $dateset->format('Y-m-d H:i:s');

		$db = JFactory::getDBO();

		$db->setQuery("SHOW COLUMNS FROM #__cwmprayer LIKE 'sendto'");

		$cksendtotype = $db->loadObjectList();

		if (!empty($cksendtotype) && ($cksendtotype[0]->Field == 'sendto' && $cksendtotype[0]->Type != 'datetime'))
		{
			$db->setQuery("ALTER TABLE #__cwmprayer MODIFY sendto datetime NOT NULL DEFAULT '0000-00-00 00:00:00'");

			if (!$db->execute())
			{
				return JError::raiseWarning(500, $db->stderr());
			}

			$sql = "UPDATE #__cwmprayer SET sendto='" . $now . "' WHERE publishstate=0";

			$db->setQuery($sql);

			if (!$db->execute())
			{
				JError::raiseWarning(66508, JText::_('Could not modify SendTo status in prayer database'));
			}
		}

		$db->setQuery("SHOW COLUMNS FROM #__cwmprayer LIKE 'praise'");

		$cksendtotype2 = $db->loadObjectList();

		if (count($cksendtotype2) > 0)
		{
			$db->setQuery("ALTER TABLE #__cwmprayer DROP praise");

			if (!$db->execute())
			{
				return JError::raiseWarning(500, $db->stderr());
			}
		}

		$db->setQuery("SHOW COLUMNS FROM #__cwmprayer LIKE 'adminsendto'");

		$cksendtotype3 = $db->loadObjectList();

		if (count($cksendtotype3) < 1)
		{
			$db->setQuery("ALTER TABLE #__cwmprayer ADD adminsendto datetime NOT NULL DEFAULT '0000-00-00 00:00:00'");

			if (!$db->execute())
			{
				return JError::raiseWarning(500, $db->stderr());
			}

			$sql = "UPDATE #__cwmprayer SET adminsendto='" . $now . "' WHERE publishstate=0";

			$db->setQuery($sql);

			if (!$db->execute())
			{
				JError::raiseWarning(66508, JText::_('Could not modify AdminSendTo status in prayer database'));
			}
		}

		return null;
	}

	/**
	 * Add Prayer Category
	 *
	 * @return mixed
	 *
	 * @since 4.0
	 */
	private function addPrayerCategory()
	{
		// Create categories for our component
		$basePath = JPATH_ADMINISTRATOR . '/components/com_categories';

		require_once $basePath . '/models/category.php';

		$config = array('table_path' => $basePath . '/tables');

		$catmodel = new CategoriesModelCategory($config);

		$catData = array(
			'id' => 0,
			'parent_id' => 0,
			'level' => 1,
			'path' => 'uncategorized',
			'extension' => 'com_cwmprayer',
			'title' => 'Uncategorized',
			'alias' => 'uncategorized',
			'description' => '<p>This is the default prayer category</p>',
			'published' => 1,
			'language' => '*'
		);

		$status = $catmodel->save($catData);

		if (!$status)
		{
			// Error::raiseWarning(500, JText::_('Unable to create default category!'));
		}

		$id1 = $catmodel->getItem()->id;

		$db = JFactory::getDbo();

		$db->setQuery("SHOW COLUMNS FROM #__cwmprayer_links LIKE 'catid'");

		$lwtable_nm1 = $db->loadObjectList();

		if (count($lwtable_nm1) > 0)
		{
			$db->setQuery("UPDATE #__cwmprayer_links SET catid=" . (int) $id1);

			if (!$db->execute())
			{
				return JError::raiseWarning(500, $db->stderr());
			}
		}

		$db->setQuery("SHOW COLUMNS FROM #__cwmprayer_devotions LIKE 'catid'");

		$lwtable_nm2 = $db->loadObjectList();

		if (count($lwtable_nm2) > 0)
		{
			$db->setQuery("UPDATE #__cwmprayer_devotions SET catid=" . (int) $id1);

			if (!$db->execute())
			{
				return JError::raiseWarning(500, $db->stderr());
			}
		}

		return null;
	}
}
