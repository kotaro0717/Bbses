<?php
/**
 * iframes edit form element template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Kotaro Hokada <kotaro.hokada@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>

<div class="form-group">
	<label class="control-label">
		<?php echo __d('bbses', 'Title'); ?>
	</label>
	<?php echo $this->element('NetCommons.required'); ?>

	<div  class="nc-bbs-add-post-title-alert">
		<?php echo $this->Form->input('title',
					array(
						'label' => false,
						'class' => 'form-control',
						'ng-model' => 'bbsPosts.title',
						'required' => 'required',
						'autofocus' => true,
					)) ?>
	</div>

	<div class="has-error">
		<?php if ($this->validationErrors['BbsPost']): ?>
		<?php foreach ($this->validationErrors['BbsPost']['title'] as $message): ?>
			<div class="help-block">
				<?php echo $message ?>
			</div>
		<?php endforeach; ?>
		<?php else : ?>
			<br />
		<?php endif; ?>
	</div>
</div>

<div class="form-group">
	<label class="control-label">
		<?php echo __d('bbses', 'Content'); ?>
	</label>
	<?php echo $this->element('NetCommons.required'); ?>

	<div class="nc-wysiwyg-alert">
		<?php echo $this->Form->textarea('content',
					array(
						'label' => false,
						'class' => 'form-control',
						'ui-tinymce' => 'tinymce.options',
						'ng-model' => 'bbsPosts.content',
						'rows' => 5,
						'required' => 'required',
					)) ?>
	</div>

	<div class="has-error">
		<?php if ($this->validationErrors['BbsPost']): ?>
		<?php foreach ($this->validationErrors['BbsPost']['content'] as $message): ?>
			<div class="help-block">
				<?php echo $message; ?>
			</div>
		<?php endforeach; ?>
		<?php else : ?>
			<br />
		<?php endif; ?>
	</div>
</div>