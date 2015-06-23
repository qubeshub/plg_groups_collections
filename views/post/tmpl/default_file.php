<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

$item = $this->row->item();

$content = $this->row->description('parsed');
$content = ($content ?: $item->description('parsed'));

if ($item->get('title')) { ?>
		<h4>
			<?php echo $this->escape(stripslashes($item->get('title'))); ?>
		</h4>
<?php }

$path = $item->filespace() . DS . $item->get('id');
$href = 'index.php?option=com_collections&controller=media&task=download&post=';
$base = 'index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=' . $this->name;

$assets = $item->assets();

if ($assets->total() > 0)
{
	$images = array();
	$files = array();

	foreach ($assets as $asset)
	{
		if ($asset->image())
		{
			$images[] = $asset;
		}
		else
		{
			$files[] = $asset;
		}
	}
	if (count($images) > 0)
	{
		//$assets->rewind();
		$first = array_shift($images);

		list($originalWidth, $originalHeight) = getimagesize($path . DS . $first->file('thumbnail'));
		$ratio = $originalWidth / $originalHeight;
		?>
			<div class="holder">
				<a class="img-link" data-rel="post<?php echo $this->row->get('id'); ?>" href="<?php echo Route::url($href . $this->row->get('id') . '&file=' . ltrim($first->get('filename'), DS) . '&size=medium'); ?>" data-download="<?php echo Route::url($href . $this->row->get('id') . '&file=' . ltrim($first->get('filename'), DS) . '&size=original'); ?>" data-downloadtext="<?php echo JText::_('PLG_GROUPS_COLLECTIONS_DOWNLOAD'); ?>">
					<img src="<?php echo Route::url($href . $this->row->get('id') . '&file=' . ltrim($first->get('filename'), DS) . '&size=thumb'); ?>" alt="<?php echo ($first->get('description')) ? $this->escape(stripslashes($first->get('description'))) : ''; ?>" class="img" style="height: <?php echo (!isset($this->actual) || !$this->actual) ? round($this->params->get('maxWidth', 290) / $ratio, 0, PHP_ROUND_HALF_UP) : $originalHeight; ?>px;" />
				</a>
			</div>
		<?php
		if (count($images) > 0)
		{
			?>
			<div class="gallery">
			<?php
			foreach ($images as $asset)
			{
				?>
				<a class="img-link" data-rel="post<?php echo $this->row->get('id'); ?>" href="<?php echo Route::url($href . $this->row->get('id') . '&file=' . ltrim($asset->get('filename'), DS) . '&size=medium'); ?>" data-download="<?php echo Route::url($href . $this->row->get('id') . '&file=' . ltrim($asset->get('filename'), DS) . '&size=original'); ?>" data-downloadtext="<?php echo JText::_('PLG_GROUPS_COLLECTIONS_DOWNLOAD'); ?>">
					<img src="<?php echo Route::url($href . $this->row->get('id') . '&file=' . ltrim($asset->get('filename'), DS) . '&size=thumb'); ?>" alt="<?php echo ($asset->get('description')) ? $this->escape(stripslashes($asset->get('description'))) : ''; ?>" class="img" width="50" height="50" />
				</a>
				<?php
			}
			?>
				<div class="clearfix"></div>
			</div>
		<?php
		}
	}
	//$assets->rewind();
	if (count($files) > 0)
	{
?>
		<ul class="file-list">
<?php
		foreach ($files as $asset)
		{
?>
				<li class="type-<?php echo $asset->get('type'); ?>">
					<a href="<?php echo ($asset->get('type') == 'link') ? $asset->get('filename') : Route::url($href . $this->row->get('id') . '&file=' . ltrim($asset->get('filename'), DS)); ?>" <?php echo ($asset->get('type') == 'link') ? ' rel="external"' : ''; ?>>
						<?php echo $asset->get('filename'); ?>
					</a>
					<span class="file-meta">
						<span class="file-size">
				<?php if ($asset->get('type') != 'link') { ?>
							<?php echo \Hubzero\Utility\Number::formatBytes(filesize($path . DS . ltrim($asset->get('filename'), DS))); ?>
				<?php } else { ?>
							<?php
							$UrlPtn  = "(?:https?:|mailto:|ftp:|gopher:|news:|file:)" .
							           "(?:[^ |\\/\"\']*\\/)*[^ |\\t\\n\\/\"\']*[A-Za-z0-9\\/?=&~_]";

							if (preg_match("/$UrlPtn/", $asset->get('filename')))
							{
								echo Lang::txt('external link');
							}
							else
							{
								echo Lang::txt('internal link');
							}
							?>
				<?php } ?>
						</span>
				<?php if ($asset->get('description')) { ?>
						<span class="file-description">
							<?php echo \Hubzero\Utility\Number::formatBytes(filesize($path . DS . ltrim($asset->get('filename'), DS))); ?>
						</span>
				<?php } ?>
					</span>
				</li>
<?php
		}
?>
		</ul>
<?php
	}
}
?>
<?php if ($content) { ?>
		<div class="description">
			<?php echo $content; ?>
		</div>
<?php } ?>