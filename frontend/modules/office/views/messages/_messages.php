<div class="message_sort">
	<div class="period">
	  <ul id="gTo">
		<li>Статусы: </li>
		<li><a href="javascript:;" onclick="Support._gTo('wait',this);" class="here _show select"><i class="fa fa-clock-o"></i> - Ожидают</a></li>
		<li><a href="javascript:;" onclick="Support._gTo('process',this);" class="here _show"><i class="fa fa-coffee"></i> - Выполняем</a></li>
		<li><a href="javascript:;" onclick="Support._gTo('reply',this);" class="here _show"><i class="fa fa-comment-o"></i> - Мы ответили</a></li>
		<li><a href="javascript:;" onclick="Support._gTo('close',this);"class="here _show"><i class="fa fa-smile-o"></i> - Закрыт</a></li>
		<li><a href="javascript:;" onclick="Support._gTo('remove',this);" class="here _show"><i class="fa fa-trash-o"></i> - Удалено</a></li>
		<li></li>
		<li>Важность: </li>
		<li><a href="javascript:;" onclick="Support._gTo('low',this,'order');" class="here _order">Низкая</a></li>
		<li><a href="javascript:;" onclick="Support._gTo('middle',this,'order');" class="here _order">Средняя</a></li>
		<li><a href="javascript:;" onclick="Support._gTo('important',this,'order');" class="here _order">Важно</a></li>
		<li><a href="javascript:;" onclick="Support._gTo('urgent',this,'order');" class="here _order select"><i class="fa fa-fire"></i> Срочное</a></li>
	  </ul>
	</div>
</div>
<div class="message_list">
   <div class="table_head">
		<div class="table_head_inside">
			<table>
			 <tbody><tr>
			  <td class="status"></td>
			  <td class="id">ID</td>
			  <td class="date_ot">Дата запроса</td>
			  <td class="date_do">Последний ответ</td>
			  <td class="text">Тема запроса</td>
			  <td class="answered no_border">Переписка</td>
			 </tr>
			</tbody></table>
		</div>
   </div>
   <div id="_messagesList" class="message_list_fix_height">
		<?php $this->renderPartial('_messages_list', array('messages'=>$messages)); ?>
   </div>
</div>
<div class="message_read" style="height: 370px;">
	<div class="message_no" id="_message">
		<div class="td">Не выбран запрос для чтения и ответа</div>
	</div>
</div>