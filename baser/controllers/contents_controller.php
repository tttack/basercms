<?php
/* SVN FILE: $Id$ */
/**
 * コンテンツコントローラー
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.controllers
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * コンテンツコントローラー
 *
 * @package       cake
 * @subpackage    cake.baser.controllers
 */
class ContentsController extends AppController {
/**
 * クラス名
 *
 * @var		array
 * @access	public
 */
	var $name = 'Contents';
/**
 * モデル
 *
 * @var		array
 * @access	public
 */
	var $uses = array('Content', 'Page');
/**
 * コンポーネント
 *
 * @var     array
 * @access  public
 */
	var $components = array();
/**
 * ヘルパー
 *
 * @var		array
 * @access	public
 */
	var $helpers = array('TextEx', 'FormEx');
/**
 * beforeFilter
 *
 * @return	void
 * @access	public
 */
	function beforeFilter() {
		parent::beforeFilter();
		$this->Security->enabled = false;
	}
/**
 * コンテンツ検索
 *
 * @return	void
 * @access	public
 */
	function search() {
		
		$datas = array();
		$query = array();

		$default = array('named' => array('num' => 10));
		$this->setViewConditions('Content', array('default' => $default, 'type' => 'get'));

		if(!empty($this->params['url']['q'])) {
		
			$this->paginate = array(
				'conditions'=> $this->_createSearchConditions($this->data),
				'order'		=> 'Content.modified DESC',
				'limit'		=> $this->passedArgs['num']
			);

			$datas = $this->paginate('Content');
			$query = $this->_parseQuery($this->params['url']['q']);
			
		}

		$this->set('query', $query);
		$this->set('datas', $datas);
		$this->pageTitle = '検索結果一覧';

	}
/**
 * 検索キーワードを分解し配列に変換する
 *
 * @param string $query
 * @return array
 * @access protected
 */
	function _parseQuery($query) {
		
		$query = str_replace('　', ' ', $query);
		if(strpos($query, ' ') !== false) {
			$query = explode(' ', $query);
		} else {
			$query = array($query);
		}
		return $query;
		
	}
/**
 * 検索条件を生成する
 *
 * @param	array	$data
 * @return	array	$conditions
 * @access	protected
 */
	function _createSearchConditions($data) {
		
		$conditions = array('Content.status' => true);
		$query = '';
		if(isset($data['Content']['q'])) {
			$query = $data['Content']['q'];
			unset($data['Content']['q']);
		}
		if(isset($data['Content']['c'])) {
			if($data['Content']['c']) {
				$data['Content']['category'] = $data['Content']['c'];
			}
			unset($data['Content']['c']);
		}
		
		$conditions = am($conditions, $this->postConditions($data));

		if($query) {
			$query = $this->_parseQuery($query);
			foreach($query as $key => $value) {
				$conditions['and'][$key]['or'][] = array('Content.title LIKE' => "%{$value}%");
				$conditions['and'][$key]['or'][] = array('Content.detail LIKE' => "%{$value}%");
			}
		}
		
		return $conditions;
		
	}
/**
 * ページリストを取得する
 * 
 * @param mixid $parentCategoryId / '' / 0
 * @return type 
 */
	function get_page_list_recursive($parentCategoryId = null) {
		
		return $this->__getPageListRecursive($parentCategoryId);
		
	}
/**
 * ページリストを取得する（再帰）
 * 
 * @param mixid $parentCategoryId / '' / 0
 * @return string 
 */
	function __getPageListRecursive($parentCategoryId = null) {

		$direct = false;
		$mobileId = $this->Page->PageCategory->getMobileId();
		if($parentCategoryId === 0) {
			$direct = true;
			$parentCategoryId = null;
		}elseif(!$parentCategoryId && Configure::read('Mobile.on')) {
			$parentCategoryId = $mobileId;
		}
		
		// ページリスト取得
		$conditions = array('Page.page_category_id' => $parentCategoryId);
		$conditions = am($conditions, $this->Page->getConditionAllowPublish());
		$pages = $this->Page->find('all', array(
			'conditions' => $conditions,
			'fields' => array('name', 'title', 'url'), 
			'order' => 'Page.sort',
			'recursive' => -1
		));

		foreach($pages as $key => $page) {
			$pages[$key]['Page']['url'] = preg_replace('/^\/mobile/', '/m', $page['Page']['url']);
		}
		
		if(!$direct) {
			// カテゴリリスト取得
			$conditions = array('PageCategory.parent_id' => $parentCategoryId);
			if(!$parentCategoryId) {
				$conditions['PageCategory.id <>'] = $mobileId;
			}
			$pageCategories = $this->Page->PageCategory->find('all', array(
				'conditions' => $conditions, 
				'fields' => array('id', 'title'),
				'order' => 'PageCategory.lft',
				'recursive' => -1
			));
		} else {
			$pageCategories = array();
		}
		
		// カテゴリごとの子カテゴリ取得
		$children = array();
		if($pageCategories) {
			foreach ($pageCategories as $key => $pageCategory) {
				$children = $this->__getPageListRecursive($pageCategory['PageCategory']['id']);
				if($children) {
					$pageCategories[$key]['children'] = $children;
				}
				if(isset($children['pages'])) {
					$paths = Set::extract('/Page/name', $children['pages']);
					if(in_array('index', $paths)) {
						$cats = $this->Page->PageCategory->getPath($pageCategory['PageCategory']['id'], array('name'), -1);
						$cats = Set::extract('/PageCategory/name', $cats);
						if($cats) {
							$parentCategoryPath = '/'.implode('/', $cats).'/';
						} else {
							$parentCategoryPath = '/';
						}
						$parentCategoryPath = preg_replace('/^\/mobile/', '/m', $parentCategoryPath);
						$pageCategories[$key]['PageCategory']['url'] = $parentCategoryPath.'index';
					}
				}
			}
		}

		$result = array();
		if($pages) {
			$result['pages'] = $pages;
		}
		if($pageCategories) {
			$result['pageCategories'] = $pageCategories;
		}
		
		return $result;
		
	}
	
}