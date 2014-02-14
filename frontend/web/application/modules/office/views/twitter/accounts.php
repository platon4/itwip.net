<div id="twitter_accounts_office" class="block">
	<div class="block_title"><div class="block_title_inset"><i class="fa fa-twitter"></i> <h5>Twitter-аккаунты сервиса</h5></div></div>
	<div class="block_content">
    <div class="accounts_sort">
    	<div class="period">
    	  <ul id="gTo">
    		<li>Статусы: </li>
    		<li><a class="here _show select" onclick="">На модерации <sup>323</sup></a></li>
    		<li><a class="here _show" onclick="">Прошедшие модерацию <sup>5323</sup></a></li>
    		<li></li>
    		<li>Ещё:</li>
    		<li><a class="here _show" onclick="">Не допущен <sup>3</sup></a></li> <!-- Все что отключены, по причине связанной с твиттером - ключ, читатели, доступ и т.д. -->
    		<li><a class="here _show" onclick="">Снят с работы <sup>2</sup></a></li> <!-- Модератором по какойто причине -->
    		<li><a class="here _show" onclick="">Отключен <sup>51</sup></a></li>  <!-- отключен пользователем -->
    		<li><a class="here _show" onclick="">Работает <sup>5823</sup></a></li> <!-- -->
       	  </ul>
    	</div>
    </div>
    <div class="accouns_list_view">
        <div style="cursor: default" class="line_title no_border_bottom">
            Аккаунтов: 0
        	<span style="margin: -5px 4px" class="group_input search float_right"><input type="text" id="setQuery" placeholder="Найти по логину" onkeyup="Affiliate._getFromQuery(this.value);"></span>
        </div>
        <div class="table_head">
    		<div class="table_head_inside">
    			<table>
    			 <tbody><tr>
    			  <td class="account">Аккаунт</td>
                  <td class="date">Дата добавления</td>
    			  <td class="status">Статус</td>
    			  <td class="itr">iTR</td>
    			  <td class="kf">КФ</td>
    			  <td class="icons"></td>
    			 </tr>
    			</tbody></table>
    		</div>
        </div>
        <div id="_listTwAccounts" class="acconts_list">
				<?php $this->renderPartial('_list',array('accounts'=>$accounts)); ?>
        </div>
		<div class="table_bottom">
		<div class="table_bottom_inside">
			<div class="page_nav_page">
				<div id="pagesList" class="_cHide">
					<?php $this->renderPartial("_pages", array('pages' => $pages)); ?>
				</div>
			</div>
		</div>
		</div>		
    </div>
    </div>
</div>