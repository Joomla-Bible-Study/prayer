<?php/** * prayer Component * * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL * */defined('_JEXEC') or die('Restricted access');global $pcConfig, $prayeradmin;$lang = JFactory::getLanguage();$lang->load('com_prayer', JPATH_SITE);require(JPATH_ADMINISTRATOR . '/components/COM_PRAYER/helpers/admin_pc_class.php');$prayeradmin = new prayercenteradmin();$pcParams = JComponentHelper::getParams('com_prayer');$pcParamsArray = $pcParams->toArray();foreach ($pcParamsArray['params'] as $name => $value){	$pcConfig[(string) $name] = (string) $value;}?>