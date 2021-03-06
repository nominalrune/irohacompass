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

/**
 * Notes Controller
 *
 * @property Note $Note
 * @property PaginatorComponent $Paginator
 */
class NotesController extends AppController
{

	/**
	 * Components
	 *
	 * @var array
	 */
	public $components = array(
		'Paginator',
		'Security' => array(
			'csrfUseOnce' => false,
			'unlockedActions' => array('leaf_control', 'link_control', 'webpage')
		),
		'Session',
		'RequestHandler',
	);

	/**
	 * index method
	 *
	 * @return void
	 */
	public function index()
	{
		$this->layout = '';
		$this->autoRender = FALSE;
		
		$options = array(
			'conditions' => array(
				'Note.user_id' => 'admin'
			)
		);
		
		$notes = $this->Note->find('all', $options);
		
		$xmlArray = array('root' => array('note' => array()));
		
		$list = array();
		
		foreach ($notes as $note)
		{
			$list[count($list)] = $note['Note'];
		}
		
		$xmlArray['root']['note'] = $list;
		
		$xmlObject = Xml::fromArray($xmlArray, array('format' => 'tags')); // Xml::build() を使うこともできます
		$xmlString = $xmlObject->asXML();
		
		$this->response->type('xml');

		header('Task-Type: text/xml');

		echo $xmlString;
	}

	/**
	 * index method
	 *
	 * @return void
	 */
	public function page($page_id, $mode = 'readonly')
	{
		$this->layout = '';
		$this->set(compact('page_id', 'mode'));
	}
}
