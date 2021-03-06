<?php echo $this->element('admin_menu');?>
<?php $this->start('script-embedded'); ?>
<script>
	function openRecord(theme_id, user_id)
	{
		window.open(
			'<?php echo Router::url(array('controller' => 'tasks', 'action' => 'record')) ?>/'+theme_id+'/'+user_id,
			'irohacompass_record',
			'width=1100, height=700, menubar=no, toolbar=no, scrollbars=yes'
		);
	}
	
	function openTestRecord(content_id, record_id)
	{
		window.open(
			'<?php echo Router::url(array('controller' => 'progresses', 'action' => 'record_each')) ?>/'+content_id+'/'+record_id,
			'irohacompass_record',
			'width=1100, height=700, menubar=no, toolbar=no, scrollbars=yes'
		);
	}
	
	function openProgress(user_id, user_name)
	{
		document.getElementById("fraDetail").src = '<?php echo Router::url(array('controller' => 'records', 'action' => 'progress'))?>/' + user_id;
		$('#progressModal .modal-title').text(user_name + ' さんの進捗');
		$('#progressModal').modal();
	}
	
	function downloadCSV()
	{
		var url = '<?php echo Router::url(array('action' => 'csv')) ?>/' + $('#MembersEventEventId').val() + '/' + $('#MembersEventStatus').val() + '/' + $('#MembersEventUsername').val();
		$("#RecordCmd").val("csv");
		$("#RecordAdminIndexForm").submit();
		$("#RecordCmd").val("");
	}
</script>
<?php $this->end(); ?>
<div class="admin-records-index">
	<div class="ib-page-title"><?php echo __('進捗一覧'); ?></div>
	<div class="ib-horizontal">
		<?php
			echo $this->Form->create('Record');
			echo '<div class="ib-search-buttons">';
			echo $this->Form->submit(__('検索'),	array('class' => 'btn btn-info', 'div' => false));
			echo $this->Form->hidden('cmd');
			echo '<button type="button" class="btn btn-default" onclick="downloadCSV()">'.__('CSV出力').'</button>';
			echo '</div>';
			
			echo '<div class="ib-row">';
			echo $this->Form->input('theme_id',			array('label' => '学習テーマ :', 'options'=>$themes, 'selected'=>$theme_id, 'empty' => '全て', 'required'=>false, 'class'=>'form-control'));
			echo $this->Form->input('contenttitle',			array('label' => '課題名 :', 'value'=>$contenttitle, 'class'=>'form-control'));
			echo $this->Form->input('group_id',		array('label' => 'グループ :', 'options'=>$groups, 'selected'=>$group_id, 'empty' => '全て', 'required'=>false, 'class'=>'form-control'));
			echo $this->Form->input('user_id',		array('label' => 'ユーザ :', 'options'=>$users, 'selected'=>$user_id, 'empty' => '全て', 'required'=>false, 'class'=>'form-control'));
			echo '</div>';
			
			echo '<div class="ib-search-date-container">';
			echo $this->Form->input('from_date', array(
				'type' => 'date',
				'dateFormat' => 'YMD',
				'monthNames' => false,
				'timeFormat' => '24',
				'minYear' => date('Y') - 5,
				'maxYear' => date('Y'),
				'separator' => ' / ',
				'label'=> '対象日時 : ',
				'class'=>'form-control',
				'style' => 'display: inline;',
				'value' => $from_date
			));
			echo $this->Form->input('to_date', array(
				'type' => 'date',
				'dateFormat' => 'YMD',
				'monthNames' => false,
				'timeFormat' => '24',
				'minYear' => date('Y') - 5,
				'maxYear' => date('Y'),
				'separator' => ' / ',
				'label'=> '～',
				'class'=>'form-control',
				'style' => 'display: inline;',
				'value' => $to_date
			));
			echo '</div>';
			echo $this->Form->end();
		?>
	</div>
	<table cellpadding="0" cellspacing="0">
	<thead>
	<tr>
		<th nowrap><?php echo $this->Paginator->sort('theme_id', '学習テーマ'); ?></th>
		<th nowrap><?php echo $this->Paginator->sort('task_id', '課題'); ?></th>
		<th nowrap><?php echo $this->Paginator->sort('User.name', '氏名'); ?></th>
		<th nowrap class="ib-col-center"><?php echo $this->Paginator->sort('rate', '進捗率'); ?></th>
		<th nowrap class="ib-col-center"><?php echo $this->Paginator->sort('theme_rate', '進捗率(全体)'); ?></th>
		<th nowrap class="ib-col-center"><?php echo $this->Paginator->sort('is_complete', '完了'); ?></th>
		<th class="ib-col-center" nowrap><?php echo $this->Paginator->sort('record_type', '種別'); ?></th>
		<th class="ib-col-center"><?php echo $this->Paginator->sort('study_sec', '学習時間'); ?></th>
		<th class="ib-col-datetime"><?php echo $this->Paginator->sort('created', '学習日時'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($records as $record): ?>
	<tr>
		<td><a href="<?php echo Router::url(array('controller' => 'tasks', 'action' => 'index', $record['Theme']['id']));?>"><?php echo h($record['Theme']['title']); ?></a></td>
		<td><a href="<?php echo Router::url(array('controller' => 'progresses', 'action' => 'index', $record['Task']['id']));?>"><?php echo h($record['Task']['title']); ?></a></td>
		<td><a href="javascript:openProgress('<?php echo h($record['User']['id']); ?>', '<?php echo h($record['User']['name']); ?>');"><?php echo h($record['User']['name']); ?></a></td>
		<td class="ib-col-center"><?php echo h($record['Record']['rate']); ?>&nbsp;</td>
		<td class="ib-col-center"><?php echo h($record['Record']['theme_rate']); ?>&nbsp;</td>
		<td nowrap class="ib-col-center"><?php echo h(Configure::read('content_status.'.$record['Record']['is_complete'])); ?>&nbsp;</td>
		<td nowrap class="ib-col-center"><?php echo h(Configure::read('record_type.'.$record['Record']['record_type'])); ?>&nbsp;</td>
		<td class="ib-col-center"><?php echo h(Utils::getHNSBySec($record['Record']['study_sec'])); ?>&nbsp;</td>
		<td class="ib-col-date"><?php echo h(Utils::getYMDHN($record['Record']['created'])); ?>&nbsp;</td>
	</tr>
	<?php endforeach; ?>
	</tbody>
	</table>
	<?php echo $this->element('paging');?>
</div>

<!--進捗ダイアログ-->
<div class="modal fade" id="progressModal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">進捗</h4>
			</div>
			<div class="modal-body">
				<iframe id="fraDetail" class="modal-frame"></iframe>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">閉じる</button>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
