<?php
/**
 * iroha Compass Project
 *
 * @author        Kotaro Miura
 * @copyright     2015-2018 iroha Soft, Inc. (http://irohasoft.jp)
 * @link          http://irohacompass.irohasoft.jp
 * @license       http://www.gnu.org/licenses/gpl-3.0.en.html GPL License
 */

App::uses('AppController', 'Controller');

class GroupsController extends AppController
{

	public $components = array(
		'Paginator',
		'Security' => array(
			'csrfUseOnce' => false,
		),
	);

	/**
	 * グループ一覧を表示
	 */
	public function admin_index()
	{
		$this->Group->recursive = 0;
		$this->Group->virtualFields['theme_title'] = 'GroupTheme.theme_title'; // 外部結合テーブルのフィールドによるソート用
		
		$this->Paginator->settings = array(
			'fields' => array('*', 'GroupTheme.theme_title'),
			'limit' => 20,
			'order' => 'created desc',
			'joins' => array(
				array('type' => 'LEFT OUTER', 'alias' => 'GroupTheme',
						'table' => '(SELECT gc.group_id, group_concat(c.title order by c.id SEPARATOR \', \') as theme_title FROM ib_groups_themes gc INNER JOIN ib_themes c ON c.id = gc.theme_id  GROUP BY gc.group_id)',
						'conditions' => 'Group.id = GroupTheme.group_id')
			)
		);
		
		$this->set('groups', $this->Paginator->paginate());
	}

	/**
	 * グループの追加
	 */
	public function admin_add()
	{
		$this->admin_edit();
		$this->render('admin_edit');
	}

	/**
	 * グループの編集
	 * @param int $group_id 編集するグループのID
	 */
	public function admin_edit($group_id = null)
	{
		if ($this->action == 'edit' && ! $this->Group->exists($group_id))
		{
			throw new NotFoundException(__('Invalid group'));
		}
		if ($this->request->is(array(
				'post',
				'put'
		)))
		{
			if ($this->Group->save($this->request->data))
			{
				$this->Flash->success(__('グループ情報を保存しました'));
				return $this->redirect(array(
						'action' => 'index'
				));
			}
			else
			{
				$this->Flash->error(__('The group could not be saved. Please, try again.'));
			}
		}
		else
		{
			$options = array(
					'conditions' => array(
					'Group.' . $this->Group->primaryKey => $group_id
					)
			);
			$this->request->data = $this->Group->find('first', $options);
		}
		
		$users   = $this->Group->User->find('list');
		$themes = $this->Group->Theme->find('list');
		
		$this->set(compact('themes', 'users'));
	}

	/**
	 * グループの削除
	 * @param int $group_id 削除するグループのID
	 */
	public function admin_delete($group_id = null)
	{
		$this->Group->id = $group_id;
		if (! $this->Group->exists())
		{
			throw new NotFoundException(__('Invalid group'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->Group->delete())
		{
			$this->Flash->success(__('グループ情報を削除しました'));
		}
		else
		{
			$this->Flash->error(__('The group could not be deleted. Please, try again.'));
		}
		return $this->redirect(array(
				'action' => 'index'
		));
	}
}
