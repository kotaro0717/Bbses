<?php
/**
 * status_label element template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>

<span class="ng-hide" ng-show="<?php echo h($status) ?>"
	  ng-switch="<?php echo h($status) ?>" ng-cloak>

	<span class="label label-warning"
			ng-switch-when="<?php echo NetCommonsBlockComponent::STATUS_APPROVED ?>">
		<?php echo __d('net_commons', 'Approving'); ?>
	</span>

	<!-- NetCommonsBlockComponent::STATUS_REMAND = '4' -->
	<span class="label label-danger"
			ng-switch-when="<?php echo NetCommonsBlockComponent::STATUS_DISAPPROVED ?>">
		<?php echo __d('bbses', 'Remand'); ?>
	</span>

	<!-- NetCommonsBlockComponent::STATUS_DISAPPROVED = '5' -->
	<span class="label label-warning"
			ng-switch-when="<?php echo '5' ?>">
		<?php echo __d('bbses', 'Disapproval'); ?>
	</span>

	<span class="label label-info"
			ng-switch-when="<?php echo NetCommonsBlockComponent::STATUS_IN_DRAFT ?>">
		<?php echo __d('net_commons', 'Temporary'); ?>
	</span>

	<span ng-switch-default=""></span>
</span>
