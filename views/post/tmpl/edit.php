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

$this->js();

$item = $this->entry->item();

if (!$this->entry->exists())
{
	$this->entry->set('original', 1);
}

$type = 'file'; //strtolower(Request::getWord('type', $item->get('type')));
if (!$type)
{
	$type = 'file';
}
if ($type && !in_array($type, array('file', 'image', 'text', 'link')))
{
	$type = 'link';
}

$base = 'index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=' . $this->name;

$dir = $item->get('id');
if (!$dir)
{
	$dir = 'tmp' . time(); // . rand(0, 100);
}

$jbase = rtrim(Request::base(true), '/');
?>
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
<form action="<?php echo Route::url($base . '&scope=post/save' . ($this->no_html ? '&no_html=' . $this->no_html : '')); ?>" method="post" id="hubForm" class="full" enctype="multipart/form-data">
	<fieldset>
		<legend><?php echo $item->get('id') ? ($this->entry->get('original') ? Lang::txt('Edit post') : Lang::txt('Edit repost')) : Lang::txt('New post'); ?></legend>

		<?php if ($this->entry->get('original')) { ?>
			<div class="field-wrap">
				<div class="asset-uploader">
					<div class="grid">
						<div class="col span-half">
							<div id="ajax-uploader" data-txt-instructions="<?php echo Lang::txt('Click or drop file'); ?>" data-action="<?php echo $jbase; ?>/index.php?option=com_collections&amp;no_html=1&amp;controller=media&amp;task=upload<?php //&amp;dir=echo $dir; ?>" data-list="/index.php?option=com_collections&amp;no_html=1&amp;controller=media&amp;task=list&amp;dir=<?php //echo $dir; ?>">
								<noscript>
									<label for="upload">
										<?php echo Lang::txt('File:'); ?>
										<input type="file" name="upload" id="upload" />
									</label>
								</noscript>
							</div>
							<script src="<?php echo $jbase; ?>/core/assets/js/jquery.fileuploader.js"></script>
							<script src="<?php echo $jbase; ?>/core/plugins/groups/collections/assets/js/fileupload.js"></script>
						</div><!-- / .col span-half -->
						<div class="col span-half omega">
							<div id="link-adder" data-base="<?php echo rtrim(Request::base(true), '/'); ?>" data-txt-delete="<?php echo Lang::txt('JACTION_DELETE'); ?>" data-txt-instructions="<?php echo Lang::txt('Click to add link'); ?>" data-action="<?php echo $jbase; ?>/index.php?option=com_collections&amp;no_html=1&amp;controller=media&amp;task=create&amp;dir=<?php //echo $dir; ?>" data-list="/index.php?option=com_collections&amp;no_html=1&amp;controller=media&amp;task=list&amp;dir=<?php //echo $dir; ?>">
								<noscript>
									<label for="add-link">
										<?php echo Lang::txt('Add a link:'); ?>
										<input type="text" name="assets[-1][filename]" id="add-link" value="http://" />
										<input type="hidden" name="assets[-1][id]" value="0" />
										<input type="hidden" name="assets[-1][type]" value="link" />
									</label>
								</noscript>
							</div>
						</div><!-- / .col span-half -->
					</div>
				</div><!-- / .asset-uploader -->
			</div><!-- / .field-wrap -->
		<?php } ?>

		<div id="post-type-form">
			<div id="post-file" class="fieldset">

				<?php if ($this->entry->get('original')) { ?>
					<div class="field-wrap" id="ajax-uploader-list">
						<?php
							$assets = $item->assets();
							if ($assets->total() > 0)
							{
								$i = 0;
								foreach ($assets as $asset)
								{
						?>
						<p class="item-asset">
							<span class="asset-handle">
							</span>
							<span class="asset-file">
							<?php if ($asset->get('type') == 'link') { ?>
								<input type="text" name="assets[<?php echo $i; ?>][filename]" size="35" value="<?php echo $this->escape(stripslashes($asset->get('filename'))); ?>" placeholder="http://" />
							<?php } else { ?>
								<?php echo $this->escape(stripslashes($asset->get('filename'))); ?>
								<input type="hidden" name="assets[<?php echo $i; ?>][filename]" value="<?php echo $this->escape(stripslashes($asset->get('filename'))); ?>" />
							<?php } ?>
							</span>
							<span class="asset-description">
								<input type="hidden" name="assets[<?php echo $i; ?>][type]" value="<?php echo $this->escape(stripslashes($asset->get('type'))); ?>" />
								<input type="hidden" name="assets[<?php echo $i; ?>][id]" value="<?php echo $this->escape($asset->get('id')); ?>" />
								<a class="delete" data-id="<?php echo $this->escape($asset->get('id')); ?>" href="<?php echo Route::url($base . '&scope=post/' . $this->entry->get('id') . '/edit&remove=' . $asset->get('id')); ?>" title="<?php echo Lang::txt('Delete this asset'); ?>">
									<?php echo Lang::txt('delete'); ?>
								</a>
								<!-- <input type="text" name="assets[<?php echo $i; ?>][description]" size="35" value="<?php echo $this->escape(stripslashes($asset->get('description'))); ?>" placeholder="Brief description" /> -->
							</span>
						</p>
						<?php
									$i++;
								}
							}
						?>
					</div><!-- / .field-wrap -->

					<label for="field-title">
						<?php echo Lang::txt('Title'); ?>
						<input type="text" name="fields[title]" id="field-title" size="35" value="<?php echo $this->escape(stripslashes($item->get('title'))); ?>" />
					</label>
					<input type="hidden" name="fields[type]" value="file" />
				<?php } else { ?>
					<label for="field-title">
						<?php echo Lang::txt('Title'); ?>
						<input type="text" name="fieldstitle" id="field-title" class="disabled" disabled="disabled" value="<?php echo $this->escape(stripslashes($item->get('title'))); ?>" />
					</label>
				<?php } ?>

				<label for="field_description">
					<?php echo Lang::txt('Description'); ?>
					<?php if ($this->entry->get('original')) { ?>
						<?php echo $this->editor('fields[description]', $this->escape(stripslashes($item->description('raw'))), 35, 5, 'field_description', array('class' => 'minimal no-footer')); ?>
					<?php } else { ?>
						<?php echo $this->editor('post[description]', $this->escape(stripslashes($this->entry->description('raw'))), 35, 5, 'field_description', array('class' => 'minimal no-footer')); ?>
					<?php } ?>
				</label>
				<?php if ($this->task == 'save' && !$item->get('description')) { ?>
					<p class="error"><?php echo Lang::txt('PLG_GROUPS_' . strtoupper($this->name) . '_ERROR_PROVIDE_CONTENT'); ?></p>
				<?php } ?>

			</div><!-- / #post-file -->
		</div><!-- / #post-type-form -->

	<?php if ($this->entry->get('original')) { ?>
		<div class="grid">
			<div class="col span6">
	<?php } ?>

		<?php if ($this->collections->total() > 0) { ?>
			<label for="post-collection_id">
				<?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_SELECT_COLLECTION'); ?> <span class="required"><?php echo Lang::txt('JREQUIRED'); ?></span>
				<select name="post[collection_id]" id="post-collection_id">
				<?php foreach ($this->collections as $collection) { ?>
					<option value="<?php echo $this->escape($collection->get('id')); ?>"<?php if ($this->collection->get('id') == $collection->get('id')) { echo ' selected="selected"'; } ?>><?php echo $this->escape(stripslashes($collection->get('title'))); ?></option>
				<?php } ?>
				</select>
				<span class="hint"><?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_SELECT_COLLECTION_HINT'); ?></span>
			</label>
		<?php } else { ?>
			<label for="post-collection_title">
				<?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_NEW_COLLECTION'); ?> <span class="required"><?php echo Lang::txt('JREQUIRED'); ?></span>
				<input type="text" name="collection_title" id="post-collection_title" value="" />
				<span class="hint"><?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_NEW_COLLECTION_HINT'); ?></span>
			</label>
		<?php } ?>

	<?php if ($this->entry->get('original')) { ?>
			</div>
			<div class="col span6 omega">
				<label>
					<?php echo Lang::txt('PLG_GROUPS_' . strtoupper($this->name) . '_FIELD_TAGS'); ?>
					<?php echo $this->autocompleter('tags', 'tags', $this->escape($item->tags('string')), 'actags'); ?>
					<span class="hint"><?php echo Lang::txt('PLG_GROUPS_' . strtoupper($this->name) . '_FIELD_TAGS_HINT'); ?></span>
				</label>
			</div>
		</div>
	<?php } else { ?>
		<input type="hidden" name="tags" value="<?php echo $this->escape($item->tags('string')); ?>" />
	<?php } ?>
	</fieldset>

	<input type="hidden" name="fields[id]" id="field-id" value="<?php echo $this->escape($item->get('id')); ?>" />
	<input type="hidden" name="fields[created]" value="<?php echo $this->escape($item->get('created')); ?>" />
	<input type="hidden" name="fields[created_by]" value="<?php echo $this->escape($item->get('created_by')); ?>" />
	<input type="hidden" name="fields[dir]" id="field-dir" value="<?php echo $this->escape($dir); ?>" />
	<input type="hidden" name="fields[access]" id="field-access" value="<?php echo $this->escape($item->get('access', 0)); ?>" />

	<input type="hidden" name="post[id]" value="<?php echo $this->escape($this->entry->get('id')); ?>" />
	<input type="hidden" name="post[item_id]" id="post-item_id" value="<?php echo $this->escape($this->entry->get('item_id')); ?>" />

	<input type="hidden" name="cn" value="<?php echo $this->escape($this->group->get('cn')); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="active" value="<?php echo $this->name; ?>" />
	<input type="hidden" name="no_html" value="<?php echo $this->no_html; ?>" />
	<input type="hidden" name="action" value="save" />

	<?php echo Html::input('token'); ?>

	<p class="submit">
		<input class="btn btn-success" type="submit" value="<?php echo Lang::txt('PLG_GROUPS_' . strtoupper($this->name) . '_SAVE'); ?>" />

		<?php if ($item->get('id')) { ?>
			<a class="btn btn-secondary" href="<?php echo Route::url($base. ($item->get('id') ? '&scope=' . $this->collection->get('alias') : '')); ?>">
				<?php echo Lang::txt('Cancel'); ?>
			</a>
		<?php } ?>
	</p>
</form>
