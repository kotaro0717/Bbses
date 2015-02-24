<div id="nc-bbs-index-<?php echo (int)$frameId; ?>">

	<!-- 管理ボタン:コンテンツが公開できる人ならば表示 -->
	<?php if ($contentPublishable) : ?>
		<div class="text-right">
			<a href="<?php echo $this->Html->url(
				'/bbses/bbses/edit/' . $frameId) ?>" class="btn btn-primary">
				<span class="glyphicon glyphicon-cog"> </span>
			</a>
		</div>
	<?php endif; ?>

	<!-- 掲示板名称:フレームに表示できるならばいらない -->
	<div class="text-left">
		<strong><?php echo $bbses['name']; ?></strong>
	</div>

	<span class="text-left">
		<!-- 記事作成ボタン:コンテンツが作成できる人ならば表示 -->
		<?php if ($contentCreatable) : ?>
			<span class="nc-tooltip" tooltip="<?php echo __d('bbses', 'Create post'); ?>">
				<a href="<?php echo $this->Html->url(
						'/bbses/bbsPosts/add' . '/' . $frameId); ?>" class="btn btn-success">
					<span class="glyphicon glyphicon-plus"> </span></a>
			</span>

			<span class="btn-group">
				<button type="button" class="btn btn-default">
					<?php echo $narrowDown; ?>
				</button>
				<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
					<span class="caret"></span>
					<span class="sr-only">Toggle Dropdown</span>
				</button>
				<ul class="dropdown-menu" role="menu">
					<li><a href="<?php echo $this->Html->url(
							'/bbses/bbses/view' . '/' . $frameId . '/' . 1 . '/' . $sortParams . '/' . $currentVisibleRow . '/' . 1); ?>"><?php echo __d('bbses', 'Display all posts'); ?></a></li>
					<li><a href="<?php echo $this->Html->url(
							'/bbses/bbses/view' . '/' . $frameId . '/' . 1 . '/' . $sortParams . '/' . $currentVisibleRow . '/' . 2); ?>"><?php echo __d('bbses', 'Do not read'); ?></a></li>
					<li><a href="<?php echo $this->Html->url(
							'/bbses/bbses/view' . '/' . $frameId . '/' . 1 . '/' . $sortParams . '/' . $currentVisibleRow . '/' . 3); ?>"><?php echo __d('bbses', 'Published'); ?></a></li>
					<li><a href="<?php echo $this->Html->url(
							'/bbses/bbses/view' . '/' . $frameId . '/' . 1 . '/' . $sortParams . '/' . $currentVisibleRow . '/' . 4); ?>"><?php echo __d('net_commons', 'Temporary'); ?></a></li>
					<li><a href="<?php echo $this->Html->url(
							'/bbses/bbses/view' . '/' . $frameId . '/' . 1 . '/' . $sortParams . '/' . $currentVisibleRow . '/' . 5); ?>"><?php echo __d('bbses', 'Disapproval'); ?></a></li>
					<li><a href="<?php echo $this->Html->url(
							'/bbses/bbses/view' . '/' . $frameId . '/' . 1 . '/' . $sortParams . '/' . $currentVisibleRow . '/' . 6); ?>"><?php echo __d('net_commons', 'Approving'); ?></a></li>
				</ul>
			</span>
		<?php endif; ?>
	</span>

<!-- 記事が0件の時、ソート／表示件数変更を表示しない -->
<?php if ($bbsPostNum) : ?>

	<!-- 右に表示 -->
	<span class="text-left" style="float:right;">

		<!-- 記事件数の表示 -->
		<span class="glyphicon glyphicon-duplicate"><?php echo $bbsPostNum; ?></span>
		<small><?php echo __d('bbses', 'Posts'); ?></small>&nbsp;

		<!-- ソート -->
		<div class="btn-group">
			<button type="button" class="btn btn-default">
				<?php echo $currentPostSortOrder; ?>
			</button>
			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
				<span class="caret"></span>
				<span class="sr-only">Toggle Dropdown</span>
			</button>
			<ul class="dropdown-menu" role="menu">
				<li>
					<a href="<?php echo $this->Html->url(
						'/bbses/bbses/view' . '/' . $frameId . '/' . 1 . '/' . 1 . '/' . $currentVisibleRow . '/' . $narrowDownParams) ?>"><?php echo __d('bbses', 'Latest post order'); ?>
					</a>
				</li>
				<li>
					<a href="<?php echo $this->Html->url(
						'/bbses/bbses/view' . '/' . $frameId . '/' . 1 . '/' . 2 . '/' . $currentVisibleRow . '/' . $narrowDownParams); ?>"><?php echo __d('bbses', 'Older post order'); ?>
					</a>
				</li>
				<li>
					<a href="<?php echo $this->Html->url(
						'/bbses/bbses/view' . '/' . $frameId . '/' . 1 . '/' . 3 . '/' . $currentVisibleRow . '/' . $narrowDownParams); ?>"><?php echo __d('bbses', 'Descending order of comments'); ?>
					</a>
				</li>
			</ul>
		</div>

		<!-- 表示件数 -->
		<div class="btn-group">
			<button type="button" class="btn btn-default">
				<!-- Todo:件をCONST化する -->
				<?php echo $currentVisibleRow . "件"; ?>
			</button>
			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
				<span class="caret"></span>
				<span class="sr-only">Toggle Dropdown</span>
			</button>
			<ul class="dropdown-menu" role="menu">
				<li>
					<a href="<?php echo $this->Html->url(
						'/bbses/bbses/view' . '/' . $frameId . '/' . 1 . '/' . $sortParams . '/' . 1 . '/' . $narrowDownParams); ?>"><?php echo '1' . "件"; ?>
					</a>
				</li>
				<li>
					<a href="<?php echo $this->Html->url(
						'/bbses/bbses/view' . '/' . $frameId . '/' . 1 . '/' . $sortParams . '/' . 5 . '/' . $narrowDownParams); ?>"><?php echo '5' . "件"; ?>
					</a>
				</li>
				<li>
					<a href="<?php echo $this->Html->url(
						'/bbses/bbses/view' . '/' . $frameId . '/' . 1 . '/' . $sortParams . '/' . 10 . '/' . $narrowDownParams); ?>"><?php echo '10' . "件"; ?>
					</a>
				</li>
				<li>
					<a href="<?php echo $this->Html->url(
						'/bbses/bbses/view' . '/' . $frameId . '/' . 1 . '/' . $sortParams . '/' . 20 . '/' . $narrowDownParams); ?>"><?php echo '20' . "件"; ?>
					</a>
				</li>
				<li>
					<a href="<?php echo $this->Html->url(
						'/bbses/bbses/view' . '/' . $frameId . '/' . 1 . '/' . $sortParams . '/' . 50 . '/' . $narrowDownParams); ?>"><?php echo '50' . "件"; ?>
					</a>
				</li>
				<li>
					<a href="<?php echo $this->Html->url(
						'/bbses/bbses/view' . '/' . $frameId . '/' . 1 . '/' . $sortParams . '/' . 100 . '/' . $narrowDownParams); ?>"><?php echo '100' . "件"; ?>
					</a>
				</li>
			</ul>
		</div>
	</span>
	<br /><br />

	<table class="table table-striped">
		<?php foreach ($bbsPosts as $bbsPost) : ?>
			<tr>
				<td>
					<?php echo ($bbsPost['readStatus'])? '' : '<strong>' ; ?>
						<a href="/bbses/bbsPosts/view/<?php echo $frameId; ?>/<?php echo $bbsPost['id']; ?>"
						   class="text-left">

							<!-- 記事のタイトル TODO:最大表示数をCONST化する -->
							<?php echo mb_substr(strip_tags($bbsPost['title']), 0, 30, 'UTF-8'); ?>
							<?php echo (mb_substr(strip_tags($bbsPost['title']), 31, null, 'UTF-8') === '')? '' : '…'; ?>

							<!-- コメント数 -->
							<?php if ($bbsPost['status'] === NetCommonsBlockComponent::STATUS_PUBLISHED) : ?>
								<span class="glyphicon glyphicon-comment"><?php echo $bbsPost['comment_num']; ?></span>
							<?php endif; ?>
						</a>
					<?php echo ($bbsPost['readStatus'])? '' : '</strong>' ; ?>

					<!-- ステータス -->
					<?php echo $this->element('NetCommons.status_label',
								array('status' => $bbsPost['status'])); ?>

					<!-- 作成日時 -->
					<div class="text-left" style="float:right;"><?php echo $bbsPost['create_time']; ?></div>

					<!-- 本文 Todo:最大表示数をCONST化する -->
					<p>
						<?php echo mb_substr(strip_tags($bbsPost['content']), 0, 75, 'UTF-8'); ?>
						<?php echo (mb_substr(strip_tags($bbsPost['title']), 76, null, 'UTF-8') === '')? '' : '…'; ?>
					</p>

					<!-- フッター -->
					<span class="text-left">
						<?php if ($bbsPost['status'] === NetCommonsBlockComponent::STATUS_PUBLISHED) : ?>
							<?php if ($bbses['use_like_button']) : ?>
								<span class="glyphicon glyphicon-thumbs-up"><?php echo $bbsPost['like_num']; ?></span>
							<?php endif; ?>
							<?php if ($bbses['use_unlike_button']) : ?>
								<span class="glyphicon glyphicon-thumbs-down"><?php echo $bbsPost['unlike_num']; ?></span>
							<?php endif; ?>
						<?php endif ?>&nbsp;
					</span>

					<!-- 公開権限があれば編集／削除できる -->
					<!-- もしくは　編集権限があり、公開されていなければ、編集／削除できる -->
					<!-- もしくは 作成権限があり、自分の書いた記事で、公開されていなければ、編集／削除できる -->
					<?php if ($contentPublishable ||
							($contentEditable && $bbsPost['status'] !== NetCommonsBlockComponent::STATUS_PUBLISHED) ||
							($contentCreatable && $bbsPost['status'] !== NetCommonsBlockComponent::STATUS_PUBLISHED) && $bbsPost['created_user'] === $userId): ?>

						<!-- 編集 -->
						<span class="text-left" style="float:right;">
							<a href="<?php echo $this->Html->url(
									'/bbses/bbsPosts/edit' . '/' . $frameId . '/' . $bbsPost['id']); ?>"
									class="btn btn-primary btn-xs" tooltip="<?php echo __d('bbses', 'Edit'); ?>">
									<span class="glyphicon glyphicon-edit"></span>
							</a>
							&nbsp;
						</span>
					<?php endif; ?>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>

	<!-- ページャーの表示 -->
	<div class="text-center">
	<?php echo $this->element('Bbses/pager'); ?>
	</div>

<?php else : ?>

	<hr />
	<!-- メッセージの表示 -->
	<div class="text-left">
		<?php echo __d('bbses', 'There are not posts'); ?>
	</div>

<?php endif; ?>

</div>
