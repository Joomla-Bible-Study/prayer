<?php
/**
 * Core Admin CWMPrayer file
 *
 * @package    CWMPrayer.Admin
 * @copyright  2007 - 2015 (C) Joomla Bible Study Team All rights reserved
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link       http://www.JoomlaBibleStudy.org
 * */

// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * CWM Prayer Version Class
 *
 * @package  CWMPrayer.Admin
 *
 * @since    4.0
 */
class CWMPrayerVersion
{
	/** @var string Product
	 * @since 4.0
	 */
	public $PRODUCT = 'CWMPrayer';

	/** @var int Main Release Level
	 * @since 4.0
	 * */
	public $RELEASE = '4';

	/** @var int Sub Release Level
	 * @since 4.0
	 */
	public $DEV_LEVEL = '0';

	/** @var string Patch Level
	 * @since 4.0
	 */
	public $PATCH_LEVEL = '0';

	/** @var string Development Status
	 * @since 4.0
	 */
	public $DEV_STATUS = '20170622';

	/** @var string Copyright Text
	 * @since 4.0
	 */
	public $COPYRIGHT = 'Christian Web Ministries &copy; 2017-';

	/** @var string Copyright Text
	 * @since 4.0
	 */
	public $COPYRIGHTBY = 'Christian Web Ministries';

	/** @var string LINK
	 * @since 4.0
	 */
	public $LINK = 'http://www.christianwebministries.org';

	/**
	 * ??
	 *
	 * @return CWMPrayerVersion
	 *
	 * @since 4.0
	 */
	public static function &getInstance()
	{
		static $instance;

		if ($instance == null)
		{
			$instance = new CWMPrayerVersion;
		}

		return $instance;
	}

	/**
	 * ?
	 *
	 * @param   string  $property  Name
	 *
	 * @return null
	 *
	 * @since 4.0
	 */
	public function get($property)
	{
		if (isset($this->$property))
		{
			return $this->$property;
		}

		return null;
	}

	/**
	 * Return the URL
	 *
	 * @return string URL
	 *
	 * @since 4.0
	 */
	public function getUrl()
	{
		return $this->LINK;
	}

	/**
	 * Return Copyright
	 *
	 * @return string short Copyright
	 *
	 * @since 4.0
	 */
	public function getShortCopyright()
	{
		return $this->COPYRIGHT . date('Y');
	}

	/**
	 * Long Copyright
	 *
	 * @return string long Copyright
	 *
	 * @since 4.0
	 */
	public function getLongCopyright()
	{
		$copyright = $this->COPYRIGHT . date('Y');

		return $copyright . ' ' . $this->COPYRIGHTBY;
	}

	/**
	 * Long Version
	 *
	 * @return string Long format version
	 *
	 * @since 4.0
	 */
	public function getLongVersion()
	{
		return ' v.' . $this->getShortVersion();
	}

	/**
	 * Short Version
	 *
	 * @return string Short version format
	 *
	 * @since 4.0
	 */
	public function getShortVersion()
	{
		return $this->RELEASE . '.' . $this->DEV_LEVEL . '.' . $this->PATCH_LEVEL . ' ' . $this->DEV_STATUS;
	}

	/**
	 * Return version form upgrade server
	 *
	 * @return SimpleXMLElement|stdClass
	 *
	 * @since 4.0
	 */
	public function getCWMPrayerVersion()
	{
		$url         = "http://www.joomlabiblestudy.org/index.php?option=com_ars&view=update&task=stream&format=xml&id=10&dummy=extension.xml";
		$data        = @file_get_contents($url);

		if ($data)
		{
			$xmlObj  = simplexml_load_string($data);

			if (isset($xmlObj->update))
			{
				$xmlObj->upglink = $xmlObj->update->downloads->downloadurl;
			}
		}
		else
		{
			$xmlObj = new stdClass;
			$xmlObj->update = new stdClass;
			$xmlObj->update->version = '';
			$xmlObj->upglink = '';
		}

		return $xmlObj;
	}
}
