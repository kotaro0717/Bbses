<?php
/**
 * BbsComments Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Kotaro Hokada <kotaro.hokada@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('BbsesAppController', 'Bbses.Controller');

/**
 * Bbses Controller
 *
 * @author Kotaro Hokada <kotaro.hokada@gmail.com>
 * @package NetCommons\Bbses\Controller
 */
class BbsCommentsController extends BbsesAppController {

/**
 * use helpers
 *
 * @var array
 */
	public $useTable  = array(
		'bbs_posts'
	);

/**
 * use models
 *
 * @var array
 */
	public $uses = array(
		'Users.User',
		'Bbses.Bbs',
		'Bbses.BbsFrameSetting',
		'Bbses.BbsPost',
	);

/**
 * use components
 *
 * @var array
 */
	public $components = array(
		'NetCommons.NetCommonsBlock',
		'NetCommons.NetCommonsFrame',
		'NetCommons.NetCommonsRoomRole' => array(
			//コンテンツの権限設定
			'allowedActions' => array(
				'contentEditable' => array('add', 'edit', 'delete'),
				'contentCreatable' => array('add', 'edit', 'delete'),
			),
		),
	);

/**
 * use helpers
 *
 * @var array
 */
	public $helpers = array(
		'NetCommons.Token'
	);

/**
 * view method
 *
 * @return void
 */
	public function view($frameId, $postId, $commentId, $currentPage = '',
			$sortParams = '', $visibleResponsRow = '', $narrowDownParams = '') {

		//プラグイン名からアクション名までのurlを$baseUrlにセット
		$baseUrl = Inflector::variable($this->plugin) . '/' .
				Inflector::variable($this->name) . '/' . $this->action;
		$this->set('baseUrl', $baseUrl);

		//現在の一覧表示ページ番号をセット
		$currentPage = ($currentPage === '')? 1: (int)$currentPage;
		$this->set('currentPage', $currentPage);

		//現在のソートパラメータをセット
		$sortParams = ($sortParams === '')? '1': $sortParams;
		$this->set('sortParams', $sortParams);

		//現在の絞り込みをセット
		$narrowDownParams = ($narrowDownParams === '')? '1' : $narrowDownParams;
		$this->set('narrowDownParams', $narrowDownParams);

		//コメント表示数をセット
		$this->__setBbsSetting();

		//表示件数をセット
		$visibleResponsRow =
			($visibleResponsRow === '')? $this->viewVars['bbsSettings']['visible_comment_row'] : $visibleResponsRow;
		$this->set('currentVisibleRow', $visibleResponsRow);

		//掲示板名等をセット
		$this->__setBbs();

		//親記事情報をセット
		$this->__setPost($postId);

		//選択したコメントをセット
		$this->__setCurrentComment($commentId);

		//レスデータをセット
		$this->__setComment($commentId, $currentPage, $sortParams, $visibleResponsRow, $narrowDownParams);

	}

/**
 * view method
 *
 * @return void
 */
	public function add($frameId, $parentId, $postId) {
		//引用フラグをURLパラメータからセット
		$this->set('quotFlag', $this->params->query['quotFlag']);

		$this->__setBbs();

		$this->__setPost($postId);

		//コメント新規登録のためにデータ生成
		$bbsComment = $this->BbsPost->create();
		$bbsComment['BbsPost']['title'] = '新規コメント' . 'X';
		$this->set('bbsComments', $bbsComment['BbsPost']);

		//Todo:見なおす　記事追加の場合、ステータスを別途セットする（とりあえず）
		$this->set(array('contentStatus' => '0'));

		if ($this->request->isGet()) {
			CakeSession::write('backUrl', $this->request->referer());
		}

		if ($this->request->isPost()) {
			if (!$status = $this->__parseStatus()) {
				return;
			}

			$data = Hash::merge(
				$this->data,
				['BbsPost' => ['status' => $status]]
			);

			//新規登録のため、データ生成
			$bbsComment = $this->BbsPost->create(['key' => Security::hash('bbsPost' . mt_rand() . microtime(), 'md5')]);
			//初期化
			$bbsComment['BbsPost']['bbs_id'] = 0;
			$data = Hash::merge($bbsComment, $data);

			if (!$bbsComment = $this->BbsPost->savePost($data)) {
				if (!$this->__handleValidationError($this->BbsPost->validationErrors)) {
					return;
				}
			}

			//親記事のコメント数の更新処理
			$parentPosts = $this->BbsPost->getPosts(
					$data['Bbs']['id'],
					$this->viewVars['userId'],
					$this->viewVars['contentEditable'],
					$this->viewVars['contentCreatable'],
					$parentId,
					null,
					null,
					null
					);
			//Treeビヘイビアでコメント数を取得してセットして登録
			$parentPosts['BbsPost']['comment_num'] = $this->BbsPost->childCount($parentId);
			//PostするためにBbsIdをセット
			$parentPosts['Bbs']['id'] = $data['Bbs']['id'];
			if (!$bbsComment = $this->BbsPost->savePost($parentPosts)) {
				if (!$this->__handleValidationError($this->BbsPost->validationErrors)) {
					return;
				}
			}

			if (!$this->request->is('ajax')) {
				$backUrl = CakeSession::read('backUrl');
				CakeSession::delete('backUrl');
				$this->redirect($backUrl);
			}
			return;
        }
	}

/**
 * edit method
 *
 * @return void
 */
	public function edit($frameId, $postId) {
		//掲示板名を取得
		$this->__setBbs();
		if (!isset($this->viewVars['bbses'])) {
			throw new NotFoundException(__d('net_commons', 'Not Found'));
		}

		//編集する記事を取得
		$this->__setPost($postId);
		if (!isset($this->viewVars['bbsPosts'])) {
			throw new NotFoundException(__d('net_commons', 'Not Found'));
		}

		//記事追加の場合、ステータスを別途セットする（とりあえず）
		$this->set(array('contentStatus' => '0'));

		if ($this->request->isGet()) {
			$referer = $this->request->referer();
			if (! strstr($referer, '/bbses')) {
				CakeSession::write('backUrl', $this->request->referer());
			}
		}

        if ($this->request->isPost()) {
			if (!$status = $this->__parseStatus()) {
				return;
			}

			$data = Hash::merge(
				$this->data,
				['BbsPost' => ['status' => $status]]
			);

			//新規登録のため、データ生成
			$bbsComment = $this->BbsPost->getPosts(
					$data['Bbs']['id'],
					$this->viewVars['userId'],
					$this->viewVars['contentEditable'],
					$this->viewVars['contentCreatable'],
					$postId,
					null,
					null,
					null
					);
			//編集者のIDを格納
			$bbsComment['BbsPost']['created_user'] = $data['User']['id'];

			unset($bbsComment['BbsPost']['created'], $bbsComment['BbsPost']['modified']);

			$data = Hash::merge($bbsComment, $data);
			if (!$bbsComment = $this->BbsPost->savePost($data)) {
				if (!$this->__handleValidationError($this->BbsPost->validationErrors)) {
					return;
				}
			}

			if (!$this->request->is('ajax')) {
				$backUrl = CakeSession::read('backUrl');
				CakeSession::delete('backUrl');
				$this->redirect($backUrl);
			}
			return;
		}
	}

/**
 * Parse content status from request
 *
 * @throws BadRequestException
 * @return mixed status on success, false on error
 */
	private function __parseStatus() {
		if ($matches = preg_grep('/^save_\d/', array_keys($this->data))) {
			list(, $status) = explode('_', array_shift($matches));
		} else {
			if ($this->request->is('ajax')) {
				$this->renderJson(
					['error' => ['validationErrors' => ['status' => __d('net_commons', 'Invalid request.')]]],
					__d('net_commons', 'Bad Request'), 400
				);
			} else {
				throw new BadRequestException(__d('net_commons', 'Bad Request'));
			}
			return false;
		}
		return $status;
	}

/**
 * Handle validation error
 *
 * @param array $errors validation errors
 * @return bool true on success, false on error
 */
	private function __handleValidationError($errors) {
		if (is_array($errors)) {
			$this->validationErrors = $errors;
			if ($this->request->is('ajax')) {
				$results = ['error' => ['validationErrors' => $errors]];
				$this->renderJson($results, __d('net_commons', 'Bad Request'), 400);
			}
			return false;
		}
		return true;
	}

/**
 * __initBbs method
 *
 * @return void
 */
	private function __setBbsSetting() {
		//掲示板の表示設定情報を取得
		$bbsSettings = $this->BbsFrameSetting->getBbsSetting(
										$this->viewVars['frameKey']);
		$results = array(
			'bbsSettings' => $bbsSettings['BbsFrameSetting'],
		);
		$this->set($results);
	}

/**
 * __initBbs method
 *
 * @return void
 */
	private function __setBbs() {
		//ログインユーザIDを取得し、Viewにセット
		$this->set('userId', $this->Session->read('Auth.User.id'));

		//掲示板データを取得
		$bbses = $this->Bbs->getBbs(
				$this->viewVars['blockId'],
				$this->viewVars['userId'],
				$this->viewVars['contentCreatable'],
				$this->viewVars['contentEditable'],
				false	//記事一覧ではない
			);

		//Viewにセット
		$this->set(array(
			'bbses' => $bbses['Bbs']
		));
	}

/**
 * __setPost method
 *
 * @return void
 */
	private function __setPost($postId) {
		//Memo:BbsPostsController->__setPost()と同じ
		//選択した記事を一件取得
		$bbsPosts = $this->BbsPost->getPosts(
				$this->viewVars['bbses']['id'],
				$this->viewVars['userId'],
				$this->viewVars['contentEditable'],
				$this->viewVars['contentCreatable'],
				$postId,	//選択された記事のId
				null,
				null,
				null
			);

		//取得した記事の作成者IDからユーザ情報を取得
		$user = $this->User->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'id' => $bbsPosts['BbsPost']['created_user'],
				)
			)
		);

		$results = array(
			'bbsPosts' => $bbsPosts['BbsPost'],
		);
		//取得した記事の配列にユーザ名を追加
		$results['bbsPosts']['username'] = $user['User']['username'];
		$results['bbsPosts']['userId'] = $user['User']['id'];
		$this->set($results);
	}

/**
 * Set current comment method
 *
 * @return void
 */
	private function __setCurrentComment($postId) {
		$posts = $this->BbsPost->getCurrentComments(
				$this->viewVars['bbses']['id'],
				$postId,
				$this->viewVars['contentEditable'],
				$this->viewVars['contentCreatable']
			);

		//取得した記事の作成者IDからユーザ情報を取得
		$user = $this->User->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					//[0]は気持ち悪い
					'id' => $posts['BbsPost']['created_user'],
				)
			)
		);

		//camelize
		$results = array(
			'bbsCurrentComments' => $posts['BbsPost'],
		);
		$results['bbsCurrentComments']['username'] = $user['User']['username'];
		$results['bbsCurrentComments']['userId'] = $user['User']['id'];
		$this->set($results);
	}

/**
 * __setPost method
 *
 * @return void
 */
	private function __setComment($postId, $currentPage, $sortParams,
			$visibleCommentRow, $narrowDownParams) {
		//ソート条件をセット
		$sortOrder = $this->__setSortOrder($sortParams);

		//絞り込み条件をセット
		$conditions = $this->__setNarrowDown($narrowDownParams);
		$conditions['bbs_id'] =	$this->viewVars['bbses']['id'];
		$conditions['or']['and']['lft >'] = $this->viewVars['bbsCurrentComments']['lft'];
		$conditions['or']['and']['rght <'] = $this->viewVars['bbsCurrentComments']['rght'];

		$bbsCommnets = $this->BbsPost->getComments(
				$this->viewVars['userId'],
				$this->viewVars['contentEditable'],
				$this->viewVars['contentCreatable'],
				$sortOrder,			//order by指定
				$visibleCommentRow,	//limit指定
				$currentPage,		//ページ番号指定
				$conditions
			);

		//コメントなしの場合
		if (empty($bbsCommnets)) {
			//空配列を渡す
			//Todo:空ではなく、falseを渡して、Viewでは「空ならメッセージ」に修正
			$this->set('bbsComments', array());
			$this->set('commentNum', 0);
			return;
		}

		foreach ($bbsCommnets as $bbsComment) {
			//取得した記事の作成者IDからユーザ情報を取得
			$user = $this->User->find('first', array(
					'recursive' => -1,
					'conditions' => array(
						'id' => $bbsComment['BbsPost']['created_user'],
					)
				)
			);
			//取得した記事の配列にユーザ名を追加
			$bbsComment['BbsPost']['username'] = $user['User']['username'];
			$bbsComment['BbsPost']['userId'] = $user['User']['id'];
			$results[] = $bbsComment['BbsPost'];
		}
		$this->set('bbsComments', $results);
		//コメント数をセット
		$this->set('commentNum', count($results));

		//前のページがあるか取得
		if ($currentPage === 1) {
			$this->set('hasPrevPage', false);
		} else {
			$prevPage = $currentPage - 1;
			$prevPosts = $this->BbsPost->getComments(
					$this->viewVars['userId'],
					$this->viewVars['contentEditable'],
					$this->viewVars['contentCreatable'],
					$sortOrder,			//order by指定
					$visibleCommentRow,	//limit指定
					$prevPage,			//前のページ番号指定
					$conditions
				);
			$hasPrevPage = (empty($prevPosts))? false : true;
			$this->set('hasPrevPage', $hasPrevPage);
		}

		//次のページがあるか取得
		$nextPage = $currentPage + 1;
		$nextPosts = $this->BbsPost->getComments(
				$this->viewVars['userId'],
				$this->viewVars['contentEditable'],
				$this->viewVars['contentCreatable'],
				$sortOrder,			//order by指定
				$visibleCommentRow,	//limit指定
				$nextPage,			//次のページ番号指定
				$conditions
			);
		$hasNextPage = (empty($nextPosts))? false : true;
		$this->set('hasNextPage', $hasNextPage);

		//2ページ先のページがあるか取得
		$nextSecondPage = $currentPage + 2;
		$nextSecondPosts = $this->BbsPost->getComments(
				$this->viewVars['userId'],
				$this->viewVars['contentEditable'],
				$this->viewVars['contentCreatable'],
				$sortOrder,			//order by指定
				$visibleCommentRow,	//limit指定
				$nextSecondPage,	//2ページ先の番号指定
				$conditions
			);
		$hasNextSecondPage = (empty($nextSecondPosts))? false : true;
		$this->set('hasNextSecondPage', $hasNextSecondPage);

		//1,2ページの時のみ4,5ページがあるかどうか取得（モックとしてとりあえず）
		//if ($currentPage === 1 || $currentPage === 2) {
			//4ページがあるか取得（モックとしてとりあえず）
			$posts = $this->BbsPost->getComments(
					$this->viewVars['userId'],
					$this->viewVars['contentEditable'],
					$this->viewVars['contentCreatable'],
					$sortOrder,			//order by指定
					$visibleCommentRow,	//limit指定
					4,					//4ページ先の番号指定
					$conditions
				);
			$hasFourPage = (empty($posts))? false : true;
			$this->set('hasFourPage', $hasFourPage);

			//5ページがあるか取得（モックとしてとりあえず）
			$posts = $this->BbsPost->getComments(
					$this->viewVars['userId'],
					$this->viewVars['contentEditable'],
					$this->viewVars['contentCreatable'],
					$sortOrder,			//order by指定
					$visibleCommentRow,	//limit指定
					5,					//5ページ先の番号指定
					$conditions
				);
			$hasFivePage = (empty($posts))? false : true;
			$this->set('hasFivePage', $hasFivePage);
		//}
	}

/**
 * __setSortOrder method
 *
 * @param $sortParams
 * @return void
 */
	private function __setSortOrder($sortParams) {
		//Todo:BbsesAppControllerで纏める
		switch ($sortParams) {
		case '1':
		default :
			//最新の投稿順
			$sortStr = __d('bbses', 'Latest comment order');
			$this->set('currentCommentSortOrder', $sortStr);
			return 'BbsPost.created DESC';
		case '2':
			//古い投稿順
			$sortStr = __d('bbses', 'Older comment order');
			$this->set('currentCommentSortOrder', $sortStr);
			return 'BbsPost.created ASC';
		case '3':
			//ステータス順
			$sortStr = __d('bbses', 'Status order');
			$this->set('currentCommentSortOrder', $sortStr);
			return 'BbsPost.status DESC';
		}
	}

/**
 * __setNarrowDown method
 *
 * @param $narrowDownParams
 * @return string order for search
 */
	private function __setNarrowDown($narrowDownParams) {
		//BbsControllerと同様
		//Todo:BbsesAppControllerで纏める
		switch ($narrowDownParams) {
		case '1':
		default :
			//全件表示
			$narrowDownStr = __d('bbses', 'Display all posts');
			$this->set('narrowDown', $narrowDownStr);
			return array();

		case '2':
			//未読
			$narrowDownStr = __d('bbses', 'Do not read');
			$this->set('narrowDown', $narrowDownStr);
			//__setPostの未読or既読セット中に未読のみ取得する
			return array();

		case '3':
			//公開中
			$narrowDownStr = __d('bbses', 'Published');
			$this->set('narrowDown', $narrowDownStr);
			$conditions = array(
					'status' => NetCommonsBlockComponent::STATUS_PUBLISHED
				);
			return $conditions;

		case '4':
			//一時保存
			$narrowDownStr = __d('net_commons', 'Temporary');
			$this->set('narrowDown', $narrowDownStr);
			$conditions = array(
					'status' => NetCommonsBlockComponent::STATUS_IN_DRAFT
				);
			return $conditions;

		case '5':
			//非承認
			$narrowDownStr = __d('bbses', 'Disapproval');
			$this->set('narrowDown', $narrowDownStr);
			$conditions = array(
					'status' => NetCommonsBlockComponent::STATUS_DISAPPROVED
				);
			return $conditions;

		case '6':
			//承認待ち
			$narrowDownStr = __d('net_commons', 'Approving');
			$this->set('narrowDown', $narrowDownStr);
			$conditions = array(
					'status' => NetCommonsBlockComponent::STATUS_APPROVED
				);
			return $conditions;

		}
	}

}
