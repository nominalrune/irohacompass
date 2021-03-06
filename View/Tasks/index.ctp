<?php if(!$is_user) echo $this->element('admin_menu');?>
<?php $this->start('css-embedded'); ?>
<!--[if !IE]><!-->
<style>
<?php if($this->action=='admin_record') {?>
.ib-navi-item
{
	display: none;
}

.ib-logo a
{
	pointer-events: none;
}
<?php }?>
</style>
<!--<![endif]-->
<?php $this->end(); ?>
<?php $this->start('script-embedded'); ?>
	<script>
		//var _markedRenderer = new marked.Renderer;
		$(function(){
			if($("#learning_target"))
			{
				var html = marked($('#learning_target').val(),
				{
	//				renderer: _markedRenderer,
					breaks: true,
					sanitize: true
				});
				
				$("#learning_target").before(html);
			}
		});
		
		function openNote(page_id)
		{
			window.open('<?php echo Router::url(array('controller' => 'notes', 'action' => 'page', 'admin' => false))?>/'+page_id, '_note', 'width=1000,height=700,resizable=yes');
		}
	</script>
<?php $this->end(); ?>
<div class="tasks-index">
	<div class="ib-breadcrumb">
	<?php
	$controller = ($is_user) ? 'users_themes' : 'themes';
	
	if($this->action!='admin_record')
	{
		$this->Html->addCrumb('<< '.__('学習テーマ一覧'), array(
			'controller' => $controller,
			'action' => 'index'
		));
	}
	
	echo $this->Html->getCrumbs(' / ');
	//debug($tasks);

	?>
	</div>

	<div class="panel panel-info">
	<div class="panel-heading lead"><?php echo h($theme['Theme']['title']); ?></div>
	<div class="panel-body">
	<div class="well">
		<?php if($theme['Theme']['learning_target']!='') {?>
		<?php echo $this->Form->hidden('learning_target', array('value' => $theme['Theme']['learning_target'], 'id' => 'learning_target'));?>
		<?php }?>
		<div>
			<?php echo Utils::getNoteLink($theme['Theme']['page_id'], $this->Html);?>
		</div>
		<div>
			<button type="button" class="btn btn-primary btn-success" onclick="location.href='<?php echo Router::url(array('controller' => 'themes', 'action' => 'edit', $theme['Theme']['id'])) ?>'"><span data-localize='edit'>編集</span></button>
		</div>
	</div>
	<div class="buttons_container">
		<button type="button" class="btn btn-primary btn-add" onclick="location.href='<?php echo Router::url(array('action' => 'add', $theme['Theme']['id'])) ?>'"><span data-localize='add_task'>+ 課題を追加</span></button>
	</div>
	<div class="ib-horizontal">
		<?php
			$status_list = Configure::read('task_status');
			$status_list[99] = '完了以外';
			
			echo $this->Form->create('Task');
			echo $this->Form->input('status', array(
				'label'		=> '<span data-localize="status">ステータス</span> : ', 
				'options'	=> $status_list, 
				'selected'	=> $status, 
				'empty'		=> '全て', 
				'required'	=> false, 
				'class'		=> 'form-control',
				'onchange'	=> 'submit(this.form);'
			));
			
			echo $this->Form->input('keyword',		array('label' => '<span data-localize="keyword">キーワード</span> : ', 'value' => $keyword, 'required' => false));
		?>
		<button class="btn btn-info btn-add"><span data-localize="search">検索</span></button>
		<?php
			echo $this->Form->end();
		?>
	</div>
	<table class="responsive-table">
		<thead>
			<tr>
				<th nowrap><span data-localize='task'><?php echo $this->Paginator->sort('title',			'課題名'); ?></span></th>
				<th class="text-center" nowrap><span data-localize='status'><?php echo $this->Paginator->sort('status',			'状態'); ?></span></th>
				<th class="text-center" nowrap><span data-localize='progress_rate'><?php echo $this->Paginator->sort('rate',	'進捗率'); ?></span></th>
				<th class="text-center" nowrap><span data-localize='priority'><?php echo $this->Paginator->sort('priority',		'優先度'); ?></span></th>
				<th class="text-center" nowrap><span data-localize='deadline'><?php echo $this->Paginator->sort('deadline',		'期日'); ?></span></th>
				<th class="ib-col-date" nowrap><span data-localize='created_date'><?php echo $this->Paginator->sort('created',	'作成日時'); ?></span></th>
				<th class="ib-col-date" nowrap><span data-localize='updated_date'><?php echo $this->Paginator->sort('modified',	'更新日時'); ?></span></th>
				<th class="actions text-center" nowrap><?php echo __('Actions'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($tasks as $task): ?>
			<tr>
				<td>
					<?php
						echo $this->Html->link($task['Task']['title'], array('controller' => 'progresses', 'action' => 'index', $task['Task']['id']));
						echo $this->Form->hidden('id', array('id'=>'', 'class'=>'task_id', 'value'=>$task['Task']['id']));
					?>
				</td>
				<td class="text-center" nowrap><?php echo Configure::read('task_status.'.$task['Task']['status']); ?>&nbsp;</td>
				<td class="text-center" nowrap><?php echo $task['Task']['rate']; ?>%&nbsp;</td>
				<td class="text-center" nowrap><?php echo Configure::read('task_priority.'.$task['Task']['priority']); ?>&nbsp;</td>
				<td class="text-center" nowrap><?php echo Utils::getYMD($task['Task']['deadline']); ?>&nbsp;</td>
				<td class="ib-col-date" nowrap><?php echo Utils::getYMDHN($task['Task']['created']); ?>&nbsp;</td>
				<td class="ib-col-date" nowrap><?php echo Utils::getYMDHN($task['Task']['modified']); ?>&nbsp;</td>
				<td class="ib-col-action">
					<button type="button" class="btn btn-success" onclick="location.href='<?php echo Router::url(array('action' => 'edit', $theme['Theme']['id'], $task['Task']['id'])) ?>'"><span data-localize='edit'>編集</span></button>
					<?php
						echo $this->Form->postLink(__('削除'),
							array('action' => 'delete', $task['Task']['id']),
							array('class'=>'btn btn-danger', 'data-localize' => 'delete'),
							__('[%s] を削除してもよろしいですか?', $task['Task']['title'])
						);
					?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<?php echo $this->element('paging');?>

	</div>
	</div>
</div>
