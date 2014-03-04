<div id="to-do" class="block">
	<div class="block_title"><div class="block_title_inset"><i class="fa fa-bug"></i> <h5>Памятки о работе (To-Do) </h5></div></div>
	<div class="block_content">
	<ul id="to_do" class="todo-list">
		<?php $this->renderPartial('_do_list',array('do_list'=>$do_list)); ?>
	</ul>
    <div class="block_bottom">
        <span class="block group_input"><input id="_toDoText" type="text" name="_toDoText" placeholder="Добавить новый to-do в список"><button class="button  icon" onclick="ToDo.send($('#_toDoText').val(),this);"><i class="fa fa-plus"></i></button></span>
    </div>
    </div>
</div>


<script>
ToDo = {
	s: {
		_w:false,
	},
	send:function(text,e)
	{
		if(!trim(text)) return false;
		
		var $e=$(e);
		
		_ajax({
			url:'/office?act=add',
			data:{"text":text},
			success:function(obj)
			{
				if(obj.code==200)
				{
					ToDo.get();
					$('#_toDoText').val('');
				}
				else
					Dialog.open(_info,{content:obj.html});
			},
			beforeSend:function()
			{
				$e.children().removeClass('fa-trash-o').addClass('fa-spinner fa-spin');
			},
			complete:function()
			{
				$e.children().removeClass('fa-spinner fa-spin').addClass('fa-trash-o');
			}
		});		
	},
	_change:function(id,e)
	{
		var $e=$(e);
		
		_ajax({
			url:'/office?act=change&id='+id,
			beforeSend:function()
			{
				if($e.parent().hasClass('finished'))
				{
					$e.parent().removeClass('finished');
				}
				else {
					$e.parent().addClass('finished');
				}
			}
		});
	},
	_remove:function(id,e)
	{
		if(this.s._w==true) return false;
		
		var $e=$(e);
		
		_ajax({
			url:'/office?act=remove&id='+id,
			success:function(obj)
			{
				if(obj.code==200)
				{
					ToDo.get();
				}
				else
					Dialog.open(_info,{content:obj.html});
			},
			beforeSend:function()
			{
				$e.children().removeClass('fa-trash-o').addClass('fa-spinner fa-spin');
			},
			complete:function()
			{
				$e.children().removeClass('fa-spinner fa-spin').addClass('fa-trash-o');
			}
		});	
	},
	get:function()
	{
		_ajax({
			url:'/office?act=get',
			success:function(obj)
			{
				if(obj.code==200)
				{
					$('#to_do').fadeOut('fast',function(){
						$(this).html(obj.html).fadeIn();
					});
				}
				else
					Dialog.open(_info,{content:obj.html});
			},
			beforeSend:function()
			{
				ToDo.s._w=true;
			},
			complete:function()
			{
				ToDo.s._w=false;
			}
		});	
	}
}
</script>