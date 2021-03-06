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

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$livesite = JURI::base();
$lang     = JFactory::getLanguage();
$lang->load('com_cwmprayer', JPATH_SITE);

JHTML::_('script', 'media/com_cwmprayer/js/admin_pc.js');
$pcversion   = new CWMPrayerVersion;
$db          = JFactory::getDBO();
$prayeradmin = new CWMPrayerAdmin;
$version     = new JVersion;
$supportinfo = "";
$supportinfo .= 'System Information%0D%0A%0D%0A';
$supportinfo .= 'Database Version:%20' . $db->getVersion() . '%0D%0A';
$supportinfo .= 'PHP Version:%20' . phpversion() . '%0D%0A';
$supportinfo .= 'Web Server:%20' . $prayeradmin->pc_get_server_software() . '%0D%0A';
$supportinfo .= 'Joomla! Version:%20' . $pcversion->getLongVersion() . '%0D%0A%0D%0A';
$supportinfo .= 'Relevant PHP Settings%0D%0A';
$supportinfo .= 'Magic Quotes GPC:%20' . $prayeradmin->pc_get_php_setting('magic_quotes_gpc') . '%0D%0A';
$supportinfo .= 'Short Open Tags:%20' . $prayeradmin->pc_get_php_setting('short_open_tag') . '%0D%0A';
$supportinfo .= 'Disabled Functions:%20' . (($df = ini_get('disable_functions')) ? $df : 'none') . '%0D%0A';
$xmlObj      = $pcversion->getCWMPrayerVersion();
?>
<?php if (!empty($this->sidebar)) : ?>
<div id="j-sidebar-container" class="span2">
	<?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
	<?php else : ?>
	<div id="j-main-container">
		<?php endif; ?>
		<div class=" span9">
			<div class="well well-small" style="color:#08c;font-size:small;">
				<div class="module-title nav-header">Component Information</div>
				<div class="row-striped">
					<div class="row-fluid small">
						<div class="span6">
							<strong class="row-title"
							        style="margin-left:20px;"><?php echo JText::_('Installed version'); ?></strong>
						</div>
						<div class="span3" style="white-space:nowrap;">
							<?php
							if (trim($pcversion->getShortVersion()) < trim($xmlObj->update->version))
							{
								$image            = JHTML::_('image', '/templates/' . $template .
									'/images/menu/icon-16-install.png', null, 'style=vertical-align:bottom;', true);
								$attribs['rel']   = 'nofollow';
								$attribs['class'] = 'upgrade';
								?>
								<style type="text/css">
									a:link.upgrade, a:visited.upgrade {
										display: inline;
										font-weight: bold;
										color: #FFFFFF;
										background-color: #98bf21;
										width: 120px;
										text-align: center;
										padding: 4px;
										text-decoration: none;
										font-size: x-small;
										}

									a:hover.upgrade, a:active.upgrade {
										background-color: #7A991A;
										}
								</style>
								<?php
								echo $pcversion->getLongVersion() . '&nbsp;&nbsp;' . JHTML::_('link', JRoute::_($upglink), JText::_($xmlObj->update->version .
										' Update Available'), $attribs);
							}
							else
							{
								echo $pcversion->getLongVersion() . '&nbsp;<span style="color:green;font-size:x-small;">[Latest version installed]</span>';
							}
							?>
						</div>
					</div>
					<div class="row-fluid small">
						<div class="span6">
							<strong class="row-title"
							        style="margin-left:20px;"><?php echo JText::_('Copyright'); ?></strong>
						</div>
						<div class="span3" style="white-space:nowrap;">
							<?php echo $pcversion->getShortCopyright(); ?>
						</div>
					</div>
					<div class="row-fluid small">
						<div class="span6">
							<strong class="row-title"
							        style="margin-left:20px;"><?php echo JText::_('License'); ?></strong>
						</div>
						<div class="span3" style="white-space:nowrap;">
							<a href="http://www.gnu.org/copyleft/gpl.html" target="_blank"><img
										src="<?php echo JURI::root(); ?>media/com_cwmprayer/images/gplv3-88x31.png"
										border="0" title="<?php echo JText::_('GNU General Public License v3'); ?>"
										width="60px"/></a>&nbsp;<?php echo JText::_('GNU/GPL3'); ?>
						</div>
					</div>
					<?php
					$query = $db->getQuery(true);
					$query->select('p.extension_id');
					$query->from('#__extensions AS p');
					$query->where('p.element=' . $db->Quote('cwmprayeremail') . ' AND p.type=' . $db->Quote('plugin'));
					$db->setQuery($query);
					$plugid = $db->loadResult();

					if (JPluginHelper::isEnabled('system', 'cwmprayeremail'))
					{
						$plugenabled    = 'Enabled';
						$plugstatus     = 'green';
						$plugiconstatus = 'icon-publish';
						$plugstatustext = 'Edit Plugin';
					}
					else
					{
						$plugenabled    = 'Disabled';
						$plugstatus     = 'red';
						$plugiconstatus = 'icon-unpublish';
						$plugstatustext = 'Edit Plugin';
					}

					$pdir    = JPATH_ROOT . '/plugins/system/cwmprayeremail';
					$xmlfile = $pdir . '/cwmprayeremail.xml';
					$xmldata = $prayeradmin->PCparseXml($xmlfile);

					if ($xmldata)
					{
					$xmldataCD = $xmldata['creationDate'];
					?>
					<div class="row-fluid small">
						<div class="span12">
							<div class="span6"><strong class="row-title"
							                           style="margin-left:20px;"><?php echo JText::_('Email Plugin Status'); ?></strong></span>
							</div>
							<div class="span4 right nowrap"><span
										style="color:green;font-weight:bold;"><?php echo JText:: _('Installed ( v.' . $xmldata['version'] . ' - ' . $xmldataCD . ')'); ?></span>
							</div>
							<div class="span2 center"><font
										color="<?php echo $plugstatus; ?>"><?php echo JText::_($plugenabled); ?></font>
								<a href="index.php?option=com_plugins&task=plugin.edit&extension_id=<?php echo $plugid; ?>"><span
											title="<?php echo $plugstatustext; ?>" style="float:right;"><i
												class="<?php echo $plugiconstatus; ?>"></i></span></a>
							</div>
						</div>
						<?php
						}
						else
						{
						?>
						<div class="row-fluid small">
							<div class="span6">
								<strong class="row-title"
								        style="margin-left:20px;"><?php echo JText::_('Email Plugin Status'); ?></strong>
							</div>
							<div class="span3" style="white-space:nowrap;">
								<font color="red"><?php echo JText:: _('Not Installed'); ?></font>
							</div>
							<?php
							}
							?>
						</div>
						<div class="row-fluid small">
							<div class="span6">
								<strong class="row-title"
								        style="margin-left:20px;"><?php echo JText::_('Donations'); ?></strong>
							</div>
							<div class="span3">
								<form action="https://www.paypal.com/cgi-bin/webscr" method="post" >
								<input type="hidden" name="cmd" value="_donations"/>
								<input type="hidden" name="business" value="info@joomlabiblestudy.com"/>
								<input type="hidden" name="item_name" value="Joomla Bible Study - Prayer Donations"/>
								<input type="hidden" name="item_number" value="MSD1"/>
								<input type="hidden" name="no_shipping" value="1"/>
								<input type="hidden" name="return" value=""/>
								<input type="hidden" name="no_note" value="1"/>
								<input type="hidden" name="currency_code" value="USD"/>
								<input type="hidden" name="tax" value="0"/>
								<input type="hidden" name="bn" value="joomlabiblestudy"/>
								<input type="image"
								       src="<?php echo JURI::root(); ?>media/com_cwmprayer/images/donate.gif"
								       border="0" name="submit"
								       title="Make donations with PayPal - it's fast, free and secure!"/>
								</form>
							</div>
						</div>
					</div>
				</div>
				<div class="well well-small span12" style="color:#08c;font-size:small;margin-left:0;">
					<div class="module-title nav-header">Optional Modules - Status</div>
					<div class="row-striped">
						<?php
						$modArray = [1 => ['module' => 'mod_cwmprayer_submit_request', 'name' => 'Submit Request'],
						             2 => ['module' => 'mod_cwmprayer_subscribe', 'name' => 'Subscribe'],
						             3 => ['module' => 'mod_cwmprayer_menu', 'name' => 'Menu'],
						             4 => ['module' => 'mod_cwmprayer_latest', 'name' => 'Latest Requests']];

						foreach ($modArray as $module)
						{
							$query = $db->getQuery(true);
							$query->select('published, id');
							$query->from('#__modules');
							$query->where('module = ' . $db->Quote($module['module']));
							$db->setQuery($query);
							$moduleenabled = $db->loadObject();

							if (!empty($moduleenabled))
							{
								$moduleenabled->published ? $modstatus = 'icon-publish' : $modstatus = 'icon-unpublish';
								$moduleenabled->published ? $modstatusstate = 'Enabled' : $modstatusstate = 'Disabled';
								$moduleenabled->published ? $modstatuscolor = 'green' : $modstatuscolor = 'red';
								$modstatustext = 'Edit Module';
							}
							else
							{
								$modstatus     = "icon-upload";
								$modstatustext = "Install Module";
								$modstatusstate = 'Disabled';
								$modstatuscolor = 'red';
							}

							$moduledir = JPATH_ROOT . '/modules';
							$xmlfile   = $moduledir . '/' . $module['module'] . '/' . $module['module'] . '.xml';
							$xmldata   = $prayeradmin->PCparseXml($xmlfile);

							if ($xmldata)
							{
								?>
								<div class="row-fluid small">
									<div class="span12">
										<div class="span6"><i class="icon-cube"></i>
											<span><?php echo $module['name']; ?></span>
										</div>
										<div class="span4 right nowrap"><span
													style="color:green;font-weight:bold;"><?php echo JText:: _('Installed'); ?>
												( v.<?php echo $xmldata['version']; ?>
												- <?php echo $xmldata['creationDate']; ?>
												)</span></div>
										<div class="span2 center"><font
													color="<?php echo $modstatuscolor; ?>"><?php echo JText::_($modstatusstate); ?></font>
											<a href="index.php?option=com_modules&task=module.edit&id=<?php echo @$moduleenabled->id; ?>"><span
														title="<?php echo $modstatustext; ?>" style="float:right;"><i
															class="<?php echo $modstatus; ?>"></i></span></a></div>
									</div>
								</div>
								<?php
							}
							else
							{
								?>
								<div class="row-fluid small">
									<div class="span12">
										<div class="span6"><i class="icon-cube"></i>
											<span><?php echo $module['name']; ?></span>
										</div>
										<div class="span4 right nowrap"><span
													style="color:red;font-weight:bold;"><?php echo JText:: _('Not Installed'); ?></span>
										</div>
										<div class="span2"><a href="index.php?option=com_installer"><span
														title="<?php echo $modstatustext; ?>" style="float:right;"><i
															class="<?php echo $modstatus; ?>"></i></span></a></div>
									</div>
								</div>
								<?php
							}
						}
						?>
					</div>
				</div>
				<?php
				$cstring       = "";
				$pcParams      = JComponentHelper::getParams('com_cwmprayer');
				$pcParamsArray = $pcParams->toArray();
				foreach ($pcParamsArray['params'] as $name => $value)
				{
					$this->config[(string) $name] = (string) $value;
				}
				foreach ($this->config as $name => $value)
				{
					$cstring .= $name . ' = ' . $value . '\n';
				}
				?>
				<input type="hidden" name="pcconfigstr" id="config_content"
				       value="<?php echo str_replace('\n', "\n", $cstring); ?>"/>
				<input type="hidden" name="task" value=""/>
			</div>
			<div class="span3">
				<div class="well well-small">
					<div class="module-title nav-header">Quick Links</div>
					<div class="row-striped">
						<div class="row-fluid">
							<div class="span12"><a href="https://github.com/Joomla-Bible-Study/prayer/issues"><i
											class="icon-support"></i>
									<span><?php echo JText::_('Issues Tracker'); ?></span></a>
							</div>
						</div>
						<div class="row-fluid">
							<div class="span12"><a
										href="mailto:info@joomlabiblestudy.com?subject=Prayer%20Support%20Inquiry&body=<?php echo $supportinfo; ?>"><i
											class="icon-mail"></i> <span><?php echo JText::_('Support Email'); ?></span></a>
							</div>
						</div>
						<div class="row-fluid">
							<div class="span12"><a href="http://www.joomlabiblestudy.com"><i class="icon-home"></i>
									<span><?php echo JText::_('Website'); ?></span></a></div>
						</div>
						<div class="row-fluid">
							<div class="span12"><a
										href="<?php echo JURI::base(); ?>index.php?option=com_cwmprayer&task=utilities"><i
											class="icon-tools"></i>
									<span><?php echo JText::_('Component Utilities'); ?></span></a>
							</div>
						</div>
						<?php
						if (trim($pcversion->getShortVersion()) < trim($xmlObj->update->version))
						{
							$message = JText::_('Component Update [' . $xmlObj->update->version . ' Available]');
						}
						else
						{
							$message = JText::_('Component Update [None Available]');
						}
						?>
						<div class="row-fluid" id="plg_quickicon_extensionupdate">
							<div class="span12"><a
										href="<?php echo JURI::base(); ?>index.php?option=com_installer&view=update"><i
											class="icon-asterisk"></i> <span><?php echo $message; ?></span></a></div>
						</div>
						<div class="row-fluid">
							<div class="span12"><a
										href="<?php echo JURI::base(); ?>index.php?option=com_admin&view=sysinfo"><i
											class="icon-question-sign"></i>
									<span><?php echo JText::_('System Information'); ?></span></a></div>
						</div>
						<div class="row-fluid">
							<div class="span12"><a href="<?php echo JURI::base(); ?>index.php"><i
											class="icon-dashboard"></i>
									<span><?php echo JText::_('Joomla Control Panel'); ?></span></a></div>
						</div>
						<div class="row-fluid">
							<div class="span12"><a href="<?php echo JURI::base(); ?>index.php?option=com_config"><i
											class="icon-cog"></i>
									<span><?php echo JText::_('Joomla Global Configuration'); ?></span></a></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="clearfix"></div>
	<br/><br/>
	<div>
		<?php
		echo $prayeradmin->PrayerFooter();
		?>
	</div>
</div>