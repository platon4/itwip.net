<div class="table">
    <div class="td" style="width: 50%; padding-right: 10px">
        <div id="" class="block">
        	<div class="block_title"><div class="block_title_inset"><h5>Специальные промо-коды пользователей</h5></div></div>
        	<div class="block_content">
                <div class="greate_promo" style="padding: 15px;">
					<div id="mBoxAdavance"></div>
					<form id="_aForm">
						<?php echo Html::hiddenField('_type','adavance'); ?>
						<input type="text" name="Promo[mark]" placeholder="Метка" style="width: 130px;"/>
						<input type="text" name="Promo[tie]" placeholder="id" style="width: 50px;"/>
						<input type="text" name="Promo[amount]" placeholder="Сумма" style="width: 50px;"/>
						<input type="text" name="Promo[limit]" placeholder="Лимит" style="width: 50px;"/>
						<button onclick="Promo._add('_aForm',this); return false;" class="button  icon"><i class="fa fa-plus"></i></button>
					</form>
                </div>
                <div class="list_promo">
                    <table id="apromo" class="table_style_1" style="width: 100%;">
						<?php $this->renderPartial('_apromo',array('adavancePromoCodes'=>$adavancePromoCodes)); ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="td" style="width: 50%; padding-left: 10px">
        <div id="" class="block">
        	<div class="block_title"><div class="block_title_inset"><h5>Промо-коды сервиса</h5></div></div>
        	<div class="block_content">
                <div class="greate_promo" style="padding: 15px;">
					<div id="mBoxSimple"></div>
					<form id="_sForm">
						<?php echo Html::hiddenField('_type','simple'); ?>
						<input type="text" name="Promo[_count]" placeholder="Кол-во промо-кодов" />
						<input type="text" name="Promo[amount]" placeholder="сумма" style="width: 50px;" />
						<button onclick="Promo._add('_sForm',this); return false;" class="button  icon"><i class="fa fa-plus"></i></button>
					</form>
                </div>
                <div class="list_promo">
                    <div class="period">
                	  <ul id="gTo">
                		<li>Сортировка: </li>
                		<li><a href="javascript:void(0);" onclick="Promo._get('simple','no_use',this);" class="here _show select">Не использован <sup id="_promo_no_use"><?php echo $promo_no_use; ?></sup></a></li>
                		<li><a href="javascript:void(0);" onclick="Promo._get('simple','use',this);" class="here _show">Использован <sup id="_promo_use"><?php echo $poromo_use; ?></sup></a></li>
                   	  </ul>
                	</div>
                    <table id="spromo" class="table_style_1" style="width: 100%;">
						<?php $this->renderPartial('_spromo',array('promoCodes'=>$promoCodes)); ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>