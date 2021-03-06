<?php
/**
 * Core Admin CWMPrayer file
 *
 * @package    CWMPrayer.Admin
 * @copyright  2007 - 2015 (C) Joomla Bible Study Team All rights reserved
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link       http://www.JoomlaBibleStudy.org
 * */

defined('_JEXEC') or die('Restricted access');

/**
 * CWM Prayer Uddeim PMS Plugin
 *
 * @package  CWMPrayer.Admin
 *
 * @since    4.0
 */
class CWMPrayerUddeimPMSPlugin extends CWMPrayerPluginHelper
{
	/**
	 * CWM Prayer Load Vars
	 *
	 * @param   int  $newrequesterid  ID
	 *
	 * @return bool
	 *
	 * @since 4.0
	 */
	public function pcpmsloadvars($newrequesterid)
	{
		$app = JFactory::getApplication();

		if (defined('JPATH_ADMINISTRATOR'))
		{
			$ver = new JVersion;

			if (!strncasecmp($ver->RELEASE, "3.1", 3))
			{
				require_once JPATH_SITE . '/components/com_uddeim/uddeimlib31.php';
			}
			elseif (!strncasecmp($ver->RELEASE, "3.0", 3))
			{
				require_once JPATH_SITE . '/components/com_uddeim/uddeimlib30.php';
			}
		}
		else
		{
			require_once $app->get('absolute_path') . '/components/com_uddeim/uddeimlib31.php';
		}

		$pathtoadmin = uddeIMgetPath('admin');

		$pathtouser = uddeIMgetPath('user');

		$pathtosite = uddeIMgetPath('live_site');

		require_once $pathtoadmin . "/admin.shared.php";

		require_once $pathtouser . '/bbparser.php';

		require_once $pathtouser . '/includes.php';

		require_once $pathtouser . '/includes.db.php';

		require_once $pathtouser . '/crypt.class.php';

		require_once $pathtouser . '/getpiclink.php';

		require $pathtoadmin . "/config.class.php";

		$config = new uddeimconfigclass;

		uddeIMcheckConfig($pathtouser, $pathtoadmin, $config);

		uddeIMloadLanguage($pathtoadmin, $config);

		if ($config->timezone == 0)
		{
			$offset = $app->get('config.offset');
			$now = uddetime($offset);
		}
		else
		{
			$now = uddetime($config->timezone);
		}

		// Userid of sender(fromid)
		$senderid = '130';

		return true;
	}

	/**
	 * CWM Prayer Load DB
	 *
	 * @param   string  $senderid       ?
	 * @param   object  $recipid        ?
	 * @param   string  $message        ?
	 * @param   string  $now            ?
	 * @param   object  $config         ?
	 * @param   string  $prayerrequest  ?
	 * @param   string  $subject        ?
	 * @param   string  $time           ?
	 *
	 * @return mixed
	 *
	 * @since 4.0
	 * @throws \Exception
	 */
	public function pcpmsloaddb($senderid, $recipid, $message, $now, $config, $prayerrequest = null, $subject = null, $time = null)
	{
		$insID = uddeIMsaveRAWmessage($senderid, $recipid->id, '', $message, $now, $config, $config->cryptmode, '');

		return $insID;
	}

	/**
	 * CWM Prayer PMS Loads Mail
	 *
	 * @param   object  $insID        ?
	 * @param   int     $var_fromid   ?
	 * @param   object  $var_toid     ?
	 * @param   string  $var_message  ?
	 * @param   object  $emn_option   ?
	 * @param   object  $config       ?
	 *
	 * @return bool
	 *
	 * @since version
	 */
	public function pcpmsloadsmail($insID, $var_fromid, $var_toid, $var_message, $emn_option, $config)
	{
		uddeIMdispatchEMN($insID, '', $config->cryptmode, $var_fromid, $var_toid->id, $var_message, $emn_option, $config);

		return true;
	}

	/**
	 * Remove Bad Tags
	 *
	 * @param   string  $source  Source
	 *
	 * @return mixed
	 *
	 * @since 4.0
	 */
	public function removeBadTags($source)
	{
		$allowedTags = '<br>';

		$source = strip_tags($source, $allowedTags);

		return preg_replace('/<(.*?)>/ie', "'<'.removeBadAttributes('\\1').'>'", $source);
	}

	/**
	 * Remove Bad Attributes
	 *
	 * @param   string  $tagSource  Tage Source
	 *
	 * @return string
	 *
	 * @since 4.0
	 */
	public function removeBadAttributes($tagSource)
	{
		$stripAttrib = 'javascript:|onclick|ondblclick|onmousedown|onmouseup|onmouseover|onmousemove|onmouseout|onkeypress|onkeydown|onkeyup';

		return stripslashes(preg_replace("/$stripAttrib/i", 'forbidden', $tagSource));
	}
}
