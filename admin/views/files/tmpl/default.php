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

$plugin_path = JPATH_COMPONENT_ADMINISTRATOR . '/pms/';

$prayeradmin = new CWMPrayerAdmin;

jimport('joomla.filesystem.folder');

$filesarray = JFolder::files($plugin_path, '.', false, false, ['.svn', 'CVS', '.DS_Store', '__MACOSX', 'index.html']);

$image_path = JPATH_ROOT . '/media/com_cwmprayer/images/';

$imagesarray = JFolder::files($image_path);

$slideshow_path = JPATH_ROOT . '/media/com_cwmprayer/images/slideshow/';

$slideshowarray = JFolder::files($slideshow_path, '.', false, false, ['.svn', 'CVS', '.DS_Store', '__MACOSX', 'index.html']);

$lang_path = JPATH_COMPONENT_ADMINISTRATOR . '/language/';

$langfolderarray = JFolder::folders($lang_path, '.', false, false, ['pdf_fonts']);

$langarray = [];

foreach ($langfolderarray as $langfolder)
{
	$langfilesarray = JFolder::files($lang_path . $langfolder, 'com_cwmprayer.ini', false, true);

	$langarray = array_merge_recursive($langarray, $langfilesarray);
}

$template = JFactory::getApplication()->getTemplate();

$imagedir = 'templates/' . $template . '/images/admin';
?>
<?php if (!empty($this->sidebar)) : ?>
<div id="j-sidebar-container" class="span2">
	<?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span12">
	<?php else : ?>
	<div id="j-main-container">
		<?php endif; ?>
		<div class="well well-small" style="color:#08c;font-size:small;">
			<div class="module-title nav-header">Plugins</div>
			<div class="row-striped">
				<table class="table table-striped">
					<form enctype="multipart/form-data" action="index.php?option=com_cwmprayer&task=showUploadFile"
					      method="POST" name="FileUp">
						<thead>
						<tr>
							<th class="small hidden-phone" width="200" class="key"><?php echo 'File' ?></th>
							<th class="small hidden-phone" width="100"><?php echo 'Type' ?></th>
							<th class="small hidden-phone" width="200" class="key"><?php echo 'Date' ?></th>
							<th class="small hidden-phone" width="100"><?php echo 'Size' ?></th>
							<th class="small hidden-phone" width="100"><?php echo 'Permission' ?></th>
							<th class="small hidden-phone center" width="100"><?php echo 'Delete?' ?></th>
						</tr>
						</thead>
						<?php
						for ($j = 0; $j < count($filesarray); $j++)
						{
							?>
							<tbody>
							<tr style="color:#555;">
								<td width="200" class="small hidden-phone"><?php echo $filesarray[$j]; ?></td>
								<td width="100"
								    class="small hidden-phone"><?php echo $prayeradmin->findext($filesarray[$j]); ?></td>
								<td width="200"
								    class="small hidden-phone nowrap"><?php echo date("d F Y h:i:s A", filemtime($plugin_path . $filesarray[$j])); ?></td>
								<td width="100"
								    class="small hidden-phone"><?php echo round(filesize($plugin_path . $filesarray[$j]) / 1024, 2); ?>
									kb
								</td>
								<td width="100"
								    class="small hidden-phone"><?php echo substr(sprintf('%o', fileperms($plugin_path . $filesarray[$j])), -4); ?>
									(<?php $prayeradmin->fullfileperms(fileperms($plugin_path . $filesarray[$j])); ?>)
								</td>
								<td class="small hidden-phone center"><a
											href="javascript:if(confirm('Delete <?php echo $filesarray[$j];
											?>?')) window.location.href='index.php?option=com_cwmprayer&task=deletefile&controller=prayer&file=<?php
											echo $filesarray[$j]; ?>'; else void(0);">
										<img src="<?php echo $imagedir; ?>/publish_r.png" width="12" height="12"
										     border="0"
										     alt="<?php echo 'Delete'; ?>"/></a>
								</td>
							</tr>
							</tbody>
							<?php
						}
						?>
						<tr>
							<td class="small hidden-phone center" colspan="6">
								<input type="hidden" name="MAX_FILE_SIZE" value="100000"/><b>
									Choose a file to upload:&nbsp;</b><input name="uploadedfile" type="file"
								                                             class="radio small" style="height:22px;"/>
								<input type="submit" class="radio small" value="Upload File"
								       style="height:15px;padding:0 4px 16px 4px;"/>
								<input type="hidden" name="task" value="uploadfile"/>
								<input type="hidden" name="option" value="COM_CWMPRAYER"/>
								<input type="hidden" name="controller" value="prayer"/>
							</td>
						</tr>
					</form>
				</table>
			</div>
		</div>
		<div class="well well-small" style="color:#08c;font-size:small;">
			<div class="module-title nav-header">Language</div>
			<div class="row-striped">
				<table class="table table-striped">
					<form enctype="multipart/form-data" action="index.php?option=com_cwmprayer&task=showUploadFile"
					      method="POST" name="LangFileUp">
						<thead>
						<tr>
							<th class="small hidden-phone" width="200"><?php echo 'File' ?></th>
							<th class="small hidden-phone" width="100"><?php echo 'Folder' ?></th>
							<th class="small hidden-phone" width="200"><?php echo 'Date' ?></th>
							<th class="small hidden-phone" width="100"><?php echo 'Size' ?></th>
							<th class="small hidden-phone" width="100"><?php echo 'Permission' ?></th>
							<th class="small hidden-phone center" width="100"><?php echo 'Delete?' ?></th>
						</tr>
						</thead>
						<?php
						for ($j = 0; $j < count($langarray); $j++)
						{
							$d        = "#[\\\/]#";
							$lf       = preg_split($d, $langarray[$j], -1, PREG_SPLIT_NO_EMPTY);
							$lffile   = count($lf) - 1;
							$lffolder = $lffile - 1;
							?>
							<tbody>
							<tr style="color:#555;">
								<td width="200" class="small hidden-phone"><?php echo $lf[$lffile]; ?></td>
								<td width="100" class="small hidden-phone"><?php echo $lf[$lffolder]; ?></td>
								<td width="200"
								    class="small hidden-phone"><?php echo date("d F Y h:i:s A", filemtime($lang_path . $lf[$lffolder] . '/' . $lf[$lffile])); ?></td>
								<td width="100"
								    class="small hidden-phone"><?php echo ceil(filesize($lang_path . $lf[$lffolder] . '/' . $lf[$lffile]) / 1024); ?>
									kb
								</td>
								<td width="100"
								    class="small hidden-phone"><?php echo substr(sprintf('%o', fileperms($lang_path . $lf[$lffolder] . '/' . $lf[$lffile])), -4); ?>
									(<?php $prayeradmin->fullfileperms(fileperms($lang_path . $lf[$lffolder] . '/' . $lf[$lffile])); ?>
									)
								</td>
								<td class="small hidden-phone center"><a
											href="javascript:if(confirm('Delete <?php echo $langarray[$j];
											?>?')) window.location.href='index.php?option=com_cwmprayer&task=deleteLangfile&controller=prayer&file=<?php
											echo $langarray[$j]; ?>'; else void(0);">
										<img src="<?php echo $imagedir; ?>/publish_r.png" width="12" height="12"
										     border="0"
										     alt="<?php echo 'Delete'; ?>"/></a>
								</td>
							</tr>
							</tbody>
							<?php
						}
						?>
						<tr>
							<td colspan="6" class="small hidden-phone center">
								<input type="hidden" name="MAX_FILE_SIZE" value="100000"/><b>
									Choose a file to upload:&nbsp;</b><input name="uploadedlangfile" type="file"
								                                             class="radio small" style="height:22px;"/>
								<input type="submit" class="radio small" value="Upload File"
								       style="height:15px;padding:0 4px 16px 4px;"/>
								<input type="hidden" name="task" value="uploadLangfile"/>
								<input type="hidden" name="option" value="COM_CWMPRAYER"/>
								<input type="hidden" name="controller" value="prayer"/>
							</td>
						</tr>
					</form>
				</table>
			</div>
		</div>
		<div class="well well-small" style="color:#08c;font-size:small;">
			<div class="module-title nav-header">Images</div>
			<div class="row-striped">
				<table class="table table-striped">
					<form enctype="multipart/form-data" action="index.php?option=com_cwmprayer&task=showUploadFile"
					      method="POST" name="ImageUp">
						<thead>
						<tr>
							<th class="small hidden-phone" width="200"><?php echo 'Image' ?></th>
							<th class="small hidden-phone" width="100"><?php echo 'File Type' ?></th>
							<th class="small hidden-phone" width="200"><?php echo 'Date' ?></th>
							<th class="small hidden-phone" width="100"><?php echo 'Size' ?></th>
							<th class="small hidden-phone" width="100"><?php echo 'Permission' ?></th>
							<th class="small hidden-phone center" width="100"><?php echo 'Delete?' ?></th>
						</tr>
						</thead>
						<?php
						for ($j = 0; $j < count($imagesarray); $j++)
						{
							$imagetype = $prayeradmin->findimageext($imagesarray[$j]);

							if ($imagetype == "JPG" | $imagetype == "PNG" | $imagetype == "GIF")
							{
								?>
								<tbody>
								<tr style="color:#555;">
									<td width="200" class="small hidden-phone"><?php echo $imagesarray[$j]; ?></td>
									<td width="100" class="small hidden-phone"><?php echo $imagetype; ?></td>
									<td width="200"
									    class="small hidden-phone"><?php echo date("d F Y h:i:s A", filemtime($image_path . $imagesarray[$j])); ?></td>
									<td width="100"
									    class="small hidden-phone"><?php echo round(filesize($image_path . $imagesarray[$j]) / 1024, 2); ?>
										kb
									</td>
									<td width="100"
									    class="small hidden-phone"><?php echo substr(sprintf('%o', fileperms($image_path . $imagesarray[$j])), -4); ?>
										(<?php $prayeradmin->fullfileperms(fileperms($image_path . $imagesarray[$j])); ?>
										)
									</td>
									<td class="small hidden-phone center"><a
												href="javascript:if(confirm('Delete <?php echo $imagesarray[$j]; ?>?')) window.location.href='index.php?option=com_cwmprayer&task=deleteimage&controller=prayer&image=<?php echo $imagesarray[$j]; ?>'; else void(0);">
											<img src="<?php echo $imagedir; ?>/publish_r.png" width="12" height="12"
											     border="0" alt="<?php echo 'Delete'; ?>"/></a>
									</td>
								</tr>
								</tbody>
								<?php
							}
						}
						?>
						<tr>
							<td class="small hidden-phone center" colspan="6">
								<input type="hidden" name="MAX_FILE_SIZE" value="100000"/><b>
									Choose a image to upload:&nbsp;</b><input name="uploadedimage" type="file"
								                                              class="radio small" style="height:22px;"/>
								<input type="submit" class="radio small" value="Upload File"
								       style="height:15px;padding:0 4px 16px 4px;"/>
								<input type="hidden" name="task" value="uploadimage"/>
								<input type="hidden" name="option" value="COM_CWMPRAYER"/>
								<input type="hidden" name="controller" value="prayer"/>
							</td>
						</tr>
					</form>
				</table>
			</div>
		</div>
		<div class="well well-small" style="color:#08c;font-size:small;">
			<div class="module-title nav-header">Slideshow Images</div>
			<div class="row-striped">
				<table class="table table-striped">
					<form enctype="multipart/form-data" action="index.php?option=com_cwmprayer&task=showUploadFile"
					      method="POST" name="SSImageUp">
						<thead>
						<tr>
							<th class="small hidden-phone" width="200"><?php echo 'Image' ?></th>
							<th class="small hidden-phone" width="100"><?php echo 'File Type' ?></th>
							<th class="small hidden-phone" width="200"><?php echo 'Date' ?></th>
							<th class="small hidden-phone" width="100"><?php echo 'Size' ?></th>
							<th class="small hidden-phone" width="100"><?php echo 'Permission' ?></th>
							<th class="small hidden-phone center" width="100"><?php echo 'Delete?' ?></th>
						</tr>
						</thead>
						<?php
						for ($j = 0; $j < count($slideshowarray); $j++)
						{
							$imagetype = $prayeradmin->findimageext($slideshowarray[$j]);

							if ($imagetype == "JPG" | $imagetype == "PNG" | $imagetype == "GIF")
							{
								?>
								<tbody>
								<tr style="color:#555;">
									<td width="200" class="small hidden-phone"><?php echo $slideshowarray[$j]; ?></td>
									<td width="100" class="small hidden-phone"><?php echo $imagetype; ?></td>
									<td width="200"
									    class="small hidden-phone"><?php echo date("d F Y h:i:s A", filemtime($slideshow_path . $slideshowarray[$j])); ?></td>
									<td width="100"
									    class="small hidden-phone"><?php echo round(filesize($slideshow_path . $slideshowarray[$j]) / 1024, 2); ?>
										kb
									</td>
									<td width="100"
									    class="small hidden-phone"><?php echo substr(sprintf('%o', fileperms($slideshow_path . $slideshowarray[$j])), -4); ?>
										(<?php $prayeradmin->fullfileperms(fileperms($slideshow_path . $slideshowarray[$j])); ?>
										)
									</td>
									<td class="small hidden-phone center"><a
												href="javascript:if(confirm('Delete <?php echo $slideshowarray[$j]; ?>?')) window.location.href='index.php?option=com_cwmprayer&task=deletessimage&controller=prayer&image=<?php echo $slideshowarray[$j]; ?>'; else void(0);">
											<img src="<?php echo $imagedir; ?>/publish_r.png" width="12" height="12"
											     border="0" alt="<?php echo 'Delete'; ?>"/></a>
									</td>
								</tr>
								</tbody>
								<?php
							}
						}
						?>
						<tr>
							<td colspan="6" class="small hidden-phone center">
								<input type="hidden" name="MAX_FILE_SIZE" value="100000"/><b>
									Choose a image to upload:&nbsp;</b><input name="uploadedssimage" type="file"
								                                              class="radio small" style="height:22px;"/>
								<input type="submit" class="radio small" value="Upload File"
								       style="height:15px;padding:0 4px 16px 4px;"/>
								<input type="hidden" name="task" value="uploadssimage"/>
								<input type="hidden" name="option" value="COM_CWMPRAYER"/>
								<input type="hidden" name="controller" value="prayer"/>
							</td>
						</tr>
					</form>
				</table>
			</div>
		</div>
		<?php
		echo $prayeradmin->PrayerFooter();
		?>
	</div>
