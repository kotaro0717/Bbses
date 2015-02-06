<?php echo $this->Html->script('/net_commons/base/js/workflow.js', false); ?>
<?php echo $this->Html->script('/net_commons/base/js/wysiwyg.js', false); ?>
<?php echo $this->Html->script('/bbses/js/bbses.js', false); ?>

<div id="nc-bbs-post-view-<?php echo (int)$frameId; ?>"
		ng-controller="Bbses"
		ng-init="initialize(<?php echo h(json_encode($this->viewVars)); ?>)">

<!-- パンくずリスト -->
<ol class="breadcrumb">
	<li><a href="<?php echo $this->Html->url(
				'/bbses/bbses/index/' . $frameId) ?>">
		<?php echo $dataForView['bbses']['name']; ?></a>
	</li>
	<li class="active"><?php echo $dataForView['bbsPosts']['title']; ?></li>
</ol>

<!-- 記事タイトル -->
	<h3><?php echo $dataForView['bbsPosts']['title']; ?></h3>
<?php //debug($dataForView['bbsPosts']); ?>
<div class="text-right">
<!-- コメント数 -->
	<span class="glyphicon glyphicon-comment"><?php echo $dataForView['bbsPosts']['commentNum']; ?>&nbsp;</span>
<!-- ソート用プルダウン -->
	<div class="btn-group">
		<button type="button" class="btn btn-default"><?php echo $dataForView['currentCommentSortOrder']; ?></button>
		<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
			<span class="caret"></span>
			<span class="sr-only">Toggle Dropdown</span>
		</button>
		<ul class="dropdown-menu" role="menu">
			<li><a href="<?php echo $this->Html->url(
							'/bbses/bbsPosts/view' . '/' . $frameId . '/' . 1 . '/' . 1); ?>"><?php echo __d('bbses', 'Latest comment order'); ?></a></li>
			<li><a href="<?php echo $this->Html->url(
							'/bbses/bbsPosts/view' . '/' . $frameId . '/' . 1 . '/' . 2); ?>"><?php echo __d('bbses', 'Older comment order'); ?></a></li>
			<?php if ($contentCreatable) : ?>
				<li><a href="<?php echo $this->Html->url(
							'/bbses/bbsPosts/view' . '/' . $frameId . '/' . 1 . '/' . 3); ?>"><?php echo __d('bbses', 'Status order'); ?></a></li>
			<?php endif; ?>
		</ul>
	</div>
<!-- 表示件数 -->
	<div class="btn-group">
		<button type="button" class="btn btn-default"><?php echo '10' . "件"; ?></button>
		<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
			<span class="caret"></span>
			<span class="sr-only">Toggle Dropdown</span>
		</button>
		<ul class="dropdown-menu" role="menu">
			<li><a href="#"><?php echo '1' . "件"; ?></a></li>
			<li><a href="#"><?php echo '5' . "件"; ?></a></li>
			<li><a href="#"><?php echo '10' . "件"; ?></a></li>
			<li><a href="#"><?php echo '20' . "件"; ?></a></li>
			<li><a href="#"><?php echo '50' . "件"; ?></a></li>
			<li><a href="#"><?php echo '100' . "件"; ?></a></li>
		</ul>
	</div>
</div>
<hr />

<!-- 親記事 -->
	<!-- id -->
	1.<span><?php echo $this->Html->image('/bbses/img/avatar.PNG', array('alt'=>'アバターが設定されていません')); ?></span>
	<!-- ユーザ情報 -->
	<span><a href=""><?php echo $dataForView['bbsPosts']['username']; ?></a></span>
	<!-- 作成時間 -->
	<span><?php echo $dataForView['bbsPosts']['created']; ?></span>
	<!-- ステータス -->
	<span><?php echo $this->element('NetCommons.status_label',
						array('status' => $dataForView['bbsPosts']['status'])); ?></span>
	<!-- 本文 -->
	<div><?php echo $dataForView['bbsPosts']['content']; ?></div>
	<!-- いいね！ -->
	<div class="text-left">
		<div class="text-left" style="float:right;">
			<?php if ($contentCreatable && $dataForView['bbses']['commentFlag']
						&& $dataForView['bbsPosts']['status'] === NetCommonsBlockComponent::STATUS_PUBLISHED) : ?>

				<a href="<?php echo $this->Html->url(
					'/bbses/bbsPosts/add' . '/' . $frameId . '/' . $dataForView['bbsPosts']['id'] . '/' . 2); ?>"
					class="btn btn-success" tooltip="<?php echo __d('bbses', 'Write comment'); ?>">
					<span class="glyphicon glyphicon-comment"></span></a>
			<?php endif; ?>

			<?php if ($dataForView['bbsPosts']['createdUser'] === $dataForView['userId'] && $contentCreatable
							&& $dataForView['bbsPosts']['status'] !== NetCommonsBlockComponent::STATUS_PUBLISHED
						|| $contentPublishable) : ?>

				<a href="<?php echo $this->Html->url(
						'/bbses/bbsPosts/edit' . '/' . $frameId . '/' . $dataForView['bbsPosts']['id']); ?>"
						class="btn btn-primary" tooltip="<?php echo __d('bbses', 'Edit'); ?>">
						<span class="glyphicon glyphicon-edit"></span></a>

				<button ng-click="delete(<?php echo $dataForView['bbsPosts']['id']; ?>)"
						class="btn btn-danger" tooltip="<?php echo __d('bbses', 'Delete'); ?>"><span class="glyphicon glyphicon-trash"></span>
				</button>
			<?php endif; ?>
		</div>
		<span class="glyphicon glyphicon-thumbs-up"><?php echo $dataForView['bbsPosts']['upVoteNum']; ?></span>
		<span class="glyphicon glyphicon-thumbs-down"><?php echo $dataForView['bbsPosts']['downVoteNum']; ?></span>
	</div>
<hr />

<!-- 全体の段落下げ -->
<?php foreach ($dataForView['bbsComments'] as $comment) { ?>
<div class="col-md-offset-1 col-md-offset-1 col-xs-offset-1">
<div class="col-sm-13 col-sm-13 col-xs-13">
	<!-- id -->
	<?php echo $comment['id']; ?>.<span><?php echo $this->Html->image('/bbses/img/avatar.PNG', array('alt'=>'アバターが設定されていません')); ?></span>
	<!-- ユーザ情報 -->
	<span><a href=""><?php echo $comment['username']; ?></a></span>
	<!-- タイトル -->
	<a href="<?php echo $this->Html->url(
					'/bbses/bbsComments/view' . '/' . $frameId . '/' . $comment['postId'] . '/' . $comment['parentId']); ?>">
					<h4 style="display:inline;"><strong><?php echo $comment['title']; ?></strong></h4></a>
	<!-- 時間 -->
	<span><?php echo $comment['created']; ?></span>
	<!-- ステータス -->
	<span><?php echo $this->element('NetCommons.status_label',
						array('status' => $comment['status'])); ?></span>
	<!-- 本文 -->
	<?php if ($comment['postId'] !== $comment['parentId']) : ?>
		<div><a href="<?php echo $this->Html->url(
					'/bbses/bbsComments/view' . '/' . $frameId . '/' . $comment['postId'] . '/' . $comment['parentId']); ?>">
				>><?php echo $comment['parentId']; ?></a></div>
	<?php endif; ?>
	<div><?php echo $comment['content']; ?></div>
	<!-- いいね！ -->
	<div class="text-left">
		<div class="text-left" style="float:right;">
			<!-- コメント作成/編集/削除 -->
			<?php if ($contentCreatable && $dataForView['bbses']['commentFlag']
						&& $comment['status'] === NetCommonsBlockComponent::STATUS_PUBLISHED) : ?>

				<a href="<?php echo $this->Html->url(
						'/bbses/bbsPosts/add' . '/' . $frameId . '/' . $comment['id'] . '/' . 2); ?>"
						class="btn btn-success" tooltip="<?php echo __d('bbses', 'Write comment'); ?>"><span class="glyphicon glyphicon-comment"></span></a>
			<?php endif; ?>

			<?php if ($comment['createdUser'] === $dataForView['userId'] && $contentCreatable
							&& $comment['status'] !== NetCommonsBlockComponent::STATUS_PUBLISHED
						|| $contentPublishable) : ?>

				<a href="<?php echo $this->Html->url(
						'/bbses/bbsPosts/edit' . '/' . $frameId . '/' . $comment['id']); ?>"
						class="btn btn-primary" tooltip="<?php echo __d('bbses', 'Edit'); ?>"><span class="glyphicon glyphicon-edit"></span></a>

				<button ng-click="delete(<?php echo $comment['id']; ?>)"
						class="btn btn-danger" tooltip="<?php echo __d('bbses', 'Delete'); ?>"><span class="glyphicon glyphicon-trash"></span>
				</button>
			<?php endif; ?>
		</div>
		<span class="glyphicon glyphicon-thumbs-up"><?php echo $like_num="12"; ?></span>
		<span class="glyphicon glyphicon-thumbs-down"><?php echo $unlike_num="2"; ?></span>
	</div>
	<hr />
</div>
</div>
<?php } ?>


<!-- ページャーの表示 -->
<div class="text-center">
	<?php echo $this->element('pager'); ?>
</div>

</div>