		<div class="line_title">
			Выгрузка постов из Sitemap.xml
			<div class="open_icon"><i class="icon-caret-down"></i></div>
		</div>
	    <div id="block_3" style="display: none;">
			<div id="block_3_1">
			  <div id="block_3_1_1">
				<input type="text" style="width: 471px;" placeholder="Загрузить из URL, вставьте полный адрес до sitemap.xml"> <button class="button icon">загрузить</button>
			  </div>
			  <div id="block_3_1_2">
					<ol>
						<li>http://site.net/sitemap.xml - 465 <a href="" class="delete_post"><i class="icon-remove"></i></a></li>
						<li>http://site.org/sitemap.xml - 12 <a href="" class="delete_post"><i class="icon-remove"></i></a></li>
					</ol>
			  </div>
			  <div id="block_3_1_3">
				<span class="group_input search"><input style="width: 402px;" type="text" placeholder="Выгрузить посты из файла sitemap.xml"><button class="button icon">выбрать</button></span>
				<button class="button icon">загрузить</button>
			  </div>
			  <div id="block_3_1_4">
					<ol>
						<li>Название файла - 60 <a href="" class="delete_post"><i class="icon-remove"></i></a></li>
					</ol>
			  </div>
			</div>
			<div id="block_3_2">
			  <h5>Подсказка по загрузке sitemap.xml по URL:</h5>
			  <ul>
				  <li>- Одна строка в файле, это один пост.</li>
			  </ul>
			  <h5>Подсказка, выгрузке sitemap.xml из файла:</h5>
			  <ul>
				  <li>- Одна строка в файле, это один пост.</li>
			  </ul>
			</div>
		</div>
		<div class="line_title">
			Выбрать мои списки постов
			<div class="open_icon"><i class="icon-caret-down"></i></div>
		</div>
		<div id="block_4" style="display: none;">
			<div id="block_4_1">
			  <div>
				<select class="styler">
					<option>У вас нет списка постов</option>
				</select>
				<button class="button icon">загрузить</button>
			  </div>
			  <div id="block_3_1_4">
					<ol>
						<li>Название списка - 60 <a href="" class="delete_post"><i class="icon-remove"></i></a></li>
						<li>Название списка - 34 <a href="" class="delete_post"><i class="icon-remove"></i></a></li>
					</ol>
			  </div>
			</div>
			<div id="block_4_2">
			  <h5>Подсказка по спискам постов:</h5>
			  <ul>
				  <li>- Создать список можно на следующей странице сохранив полученные посты.</li>
				  <li>- Редактировать или скачать списки можно на странице: "Списки постов"</li>
			  </ul>
			</div>
		</div>
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
<?php
    $this->pageTitle = Yii::app()->name . ' - ' . Yii::t('twitterModule.tweets', '_twitterEditingPosts_Title');
    $this->metaDescription =  Yii::t('twitterModule.tweets', '_twitterEditingPosts_Description');
?>
<div class="block editingPosts">
    <div class="block_title"><div class="block_title_inset"><i class="icon-pencil"></i> <h5><?php echo Yii::t('twitterModule.tweets', '_twitterEditingPosts_title'); ?></h5></div></div>

    <div class="block_content">
        <div id="info_page">
			<div class="icon"><i class="icon-info"></i></div>
			<div class="text">Эта страница предназначена, для первоначальной подготовки постов перед их публикацией. Посты можно загрузить любыми способами, в общий список, который можно отредактировать на следующей странице. Список можно будет сохранить, для использования.</div>
		</div>
		
        <div id="block_1">
          <div id="block_1_1">
              <div id="block_ok"><i class="icon-sign-blank"></i> Всего загружено постов: 0</div>
              <div id="block_link"><i class="icon-sign-blank"></i> <a class="link_here" href=""><i class="icon-eye-open"></i> Превышено кол-во ссылок</a>: 0 <a class="delete_post" href="" title="Удалить посты"><i class="icon-remove"></i></a></div>
              <div id="block_text"><i class="icon-sign-blank"></i> <a class="link_here" href=""><i class="icon-eye-open"></i> Посты не проходят по кол-ву символов</a>: 88 <a class="delete_post" href=""><i class="icon-remove"></i></a></div>
          </div>
          <div id="block_1_2">
              <div id="block_ok"><i class="icon-camera"></i> <a class="link_here" href=""><i class="icon-eye-open"></i> Посты с вложенной картинкой</a>: 0 <a class="delete_post" href="" title="Удалить посты"><i class="icon-remove"></i></a></div>
              <div id="block_censor"><i class="icon-sign-blank"></i> <a class="link_here" href=""><i class="icon-eye-open"></i> Содержание цензуры в посте</a>: 0 <a class="delete_post" href="" title="Удалить посты"><i class="icon-remove"></i></a></div>
              <div id="block_porn"><i class="icon-sign-blank"></i> <a class="link_here" href=""><i class="icon-eye-open"></i> Присутствие порно контента</a>: 0 <a class="delete_post" href="" title="Удалить посты"><i class="icon-remove"></i></a></div>
          </div>
        </div>
		
        <div id="block_2">
        <div id="block_2_top"><div id="block_2_top_inset">
            <a class="button icon" href="#block_2_bottom" title="Опустится в конец списка постов"><i class="icon-arrow-down"></i></a>
            <a class="button icon" href="#block_2_bottom" title="Отметить все посты перечисленные в списке"><i class="icon-ok"></i></a>
        </div></div>
        <div id="block_2_list">
            <div class="post no_border_top">
                <div class="number">
                    <i class="icon-sign-blank ok"></i> №1
                </div>
                <div class="text_edit">
                    <div class="text">Ждём бомбу в Июне, без денег никто не останется ! - http://itwip.net</div>
                    <div class="edit">  </div>
                </div>
                <div class="check">
                    <a href="" class="edit"><i class="icon-pencil" title="Редактирование поста"></i></a>
                    <a href="" class="delete"><i class="icon-remove"  title="Удалить пост"></i></a>
                    <input type="checkbox" class="styler"/>
                </div>
            </div>

            <div class="post">
                <div class="number">
                    <i class="icon-sign-blank censor"></i> №2
                </div>
                <div class="text_edit">
                    <div class="text">Ждём бомбу в Июне, без денег никто не останется ! Аккаунтов twitter участвующих в постинге Аккаунтов twitter участвующих в постинге - http://itwip.net </div>
                    <div class="edit">  </div>
                </div>
                <div class="check">
                 <i class="icon-camera" title='<img src="http://www.cy-pr.com/images/rk.jpg">'></i>
                 <i class="icon-pencil"></i> <i class="icon-remove"></i>
                    <input type="checkbox" class="styler"/>
                </div>
            </div>

            <div class="post">
                <div class="number">
                    <i class="icon-sign-blank porn"></i> №3
                </div>
                <div class="text_edit">
                    <div class="text">Ждём бомбу в Июне, без денег никто не останется ! Аккаунтов twitter участвующих в постинге Аккаунтов twitter участвующих в постинге - http://itwip.net</div>
                    <div class="edit">  </div>
                </div>
                <div class="check">
                 <i class="icon-pencil"></i> <i class="icon-remove"></i>
                    <input type="checkbox" class="styler"/>
                </div>
            </div>

            <div class="post">
                <div class="number">
                    <i class="icon-sign-blank link"></i> №4
                </div>
                <div class="text_edit">
                    <div class="text">Ждём бомбу в Июне, без денег никто не останется ! Аккаунтов twitter участвующих в постинге Аккаунтов twitter участвующих в постинге - http://itwip.net</div>
                    <div class="edit">  </div>
                </div>
                <div class="check">
                 <i class="icon-pencil"></i> <i class="icon-remove"></i>
                    <input type="checkbox" class="styler"/>
                </div>
            </div>

            <div class="post">
                <div class="number">
                    <i class="icon-sign-blank text"></i> №5
                </div>
                <div class="text_edit">
                    <div class="text">Ждём бомбу в Июне, без денег никто не останется ! Аккаунтов twitter участвующих в постинге Аккаунтов twitter участвующих в постинге - http://itwip.net</div>
                    <div class="edit">  </div>
                </div>
                <div class="check">
                 <i class="icon-pencil"></i> <i class="icon-remove"></i>
                    <input type="checkbox" class="styler"/>
                </div>
            </div>

            <div class="post">
                <div class="number">
                    <i class="icon-sign-blank ok"></i> №6
                </div>
                <div class="text_edit">
                    <div class="text">Ждём бомбу в Июне, без денег никто не останется ! Аккаунтов twitter участвующих в постинге Аккаунтов twitter участвующих в постинге - http://itwip.net</div>
                    <div class="edit">  </div>
                </div>
                <div class="check">
                 <i class="icon-pencil"></i> <i class="icon-remove"></i>
                    <input type="checkbox" class="styler"/>
                </div>
            </div>

            <div class="post">
                <div class="number">
                    <i class="icon-sign-blank ok"></i> №7
                </div>
                <div class="text_edit">
                    <div class="text">Ждём бомбу в Июне, без денег никто не останется ! Аккаунтов twitter участвующих в постинге Аккаунтов twitter участвующих в постинге - http://itwip.net</div>
                    <div class="edit">  </div>
                </div>
                <div class="check">
                 <i class="icon-pencil"></i> <i class="icon-remove"></i>
                    <input type="checkbox" class="styler"/>
                </div>
            </div>

            <div class="post">
                <div class="number">
                    <i class="icon-sign-blank ok"></i> №8
                </div>
                <div class="text_edit">
                    <div class="text">Ждём бомбу в Июне, без денег никто не останется ! Аккаунтов twitter участвующих в постинге Аккаунтов twitter участвующих в постинге - http://itwip.net</div>
                    <div class="edit">  </div>
                </div>
                <div class="check">
                 <i class="icon-pencil"></i> <i class="icon-remove"></i>
                    <input type="checkbox" class="styler"/>
                </div>
            </div>

            <div class="post">
                <div class="number">
                    <i class="icon-sign-blank ok"></i> №9
                </div>
                <div class="text_edit">
                    <div class="text">Ждём бомбу в Июне, без денег никто не останется ! Аккаунтов twitter участвующих в постинге Аккаунтов twitter участвующих в постинге - http://itwip.net</div>
                    <div class="edit">  </div>
                </div>
                <div class="check">
                 <i class="icon-pencil"></i> <i class="icon-remove"></i>
                    <input type="checkbox" class="styler"/>
                </div>
            </div>

            <div class="post">
                <div class="number">
                    <i class="icon-sign-blank censor"></i> №10
                </div>
                <div class="text_edit">
                    <div class="text">Ждём бомбу в Июне, без денег никто не останется ! Аккаунтов twitter участвующих в постинге Аккаунтов twitter участвующих в постинге - http://itwip.net</div>
                    <div class="edit">  </div>
                </div>
                <div class="check">
                 <i class="icon-pencil"></i> <i class="icon-remove"></i>
                    <input type="checkbox" class="styler"/>
                </div>
            </div>

            <div class="post">
                <div class="number">
                    <i class="icon-sign-blank ok"></i> №11
                </div>
                <div class="text_edit">
                    <div class="text">Ждём бомбу в Июне, без денег никто не останется ! Аккаунтов twitter участвующих в постинге Аккаунтов twitter участвующих в постинге - http://itwip.net</div>
                    <div class="edit">  </div>
                </div>
                <div class="check">
                 <i class="icon-pencil"></i> <i class="icon-remove"></i>
                    <input type="checkbox" class="styler"/>
                </div>
            </div>

            <div class="post">
                <div class="number">
                    <i class="icon-sign-blank ok"></i> №12
                </div>
                <div class="text_edit">
                    <div class="text">Ждём бомбу в Июне, без денег никто не останется ! Аккаунтов twitter участвующих в постинге Аккаунтов twitter участвующих в постинге - http://itwip.net</div>
                    <div class="edit">  </div>
                </div>
                <div class="check">
                 <i class="icon-pencil"></i> <i class="icon-remove"></i>
                    <input type="checkbox" class="styler"/>
                </div>
            </div>

            <div class="post">
                <div class="number">
                    <i class="icon-sign-blank censor"></i> №13
                </div>
                <div class="text_edit">
                    <div class="text">Ждём бомбу в Июне, без денег никто не останется ! Аккаунтов twitter участвующих в постинге Аккаунтов twitter участвующих в постинге - http://itwip.net</div>
                    <div class="edit">  </div>
                </div>
                <div class="check">
                 <i class="icon-pencil"></i> <i class="icon-remove"></i>
                    <input type="checkbox" class="styler"/>
                </div>
            </div>

            <div class="post">
                <div class="number">
                    <i class="icon-sign-blank ok"></i> №14
                </div>
                <div class="text_edit">
                    <div class="text">Ждём бомбу в Июне, без денег никто не останется ! Аккаунтов twitter участвующих в постинге Аккаунтов twitter участвующих в постинге - http://itwip.net</div>
                    <div class="edit">  </div>
                </div>
                <div class="check">
                 <i class="icon-pencil"></i> <i class="icon-remove"></i>
                    <input type="checkbox" class="styler"/>
                </div>
            </div>

            <div class="post">
                <div class="number">
                    <i class="icon-sign-blank ok"></i> №15
                </div>
                <div class="text_edit">
                    <div class="text">Ждём бомбу в Июне, без денег никто не останется ! Аккаунтов twitter участвующих в постинге Аккаунтов twitter участвующих в постинге - http://itwip.net</div>
                    <div class="edit">  </div>
                </div>
                <div class="check">
                 <i class="icon-pencil"></i> <i class="icon-remove"></i>
                    <input type="checkbox" class="styler"/>
                </div>
            </div>

            <div class="post">
                <div class="number">
                    <i class="icon-sign-blank ok"></i> №16
                </div>
                <div class="text_edit">
                    <div class="text">Ждём бомбу в Июне, без денег никто не останется ! Аккаунтов twitter участвующих в постинге Аккаунтов twitter участвующих в постинге - http://itwip.net</div>
                    <div class="edit">  </div>
                </div>
                <div class="check">
                 <i class="icon-pencil"></i> <i class="icon-remove"></i>
                    <input type="checkbox" class="styler"/>
                </div>
            </div>

            <div class="post">
                <div class="number">
                    <i class="icon-sign-blank ok"></i> №17
                </div>
                <div class="text_edit">
                    <div class="text">Ждём бомбу в Июне, без денег никто не останется ! Аккаунтов twitter участвующих в постинге Аккаунтов twitter участвующих в постинге - http://itwip.net</div>
                    <div class="edit">  </div>
                </div>
                <div class="check">
                 <i class="icon-pencil"></i> <i class="icon-remove"></i>
                    <input type="checkbox" class="styler"/>
                </div>
            </div>

            <div class="post">
                <div class="number">
                    <i class="icon-sign-blank censor"></i> №18
                </div>
                <div class="text_edit">
                    <div class="text">Ждём бомбу в Июне, без денег никто не останется ! Аккаунтов twitter участвующих в постинге Аккаунтов twitter участвующих в постинге - http://itwip.net</div>
                    <div class="edit">  </div>
                </div>
                <div class="check">
                 <i class="icon-pencil"></i> <i class="icon-remove"></i>
                    <input type="checkbox" class="styler"/>
                </div>
            </div>

            <div class="post">
                <div class="number">
                    <i class="icon-sign-blank ok"></i> №19
                </div>
                <div class="text_edit">
                    <div class="text">Ждём бомбу в Июне, без денег никто не останется ! Аккаунтов twitter участвующих в постинге Аккаунтов twitter участвующих в постинге - http://itwip.net</div>
                    <div class="edit">  </div>
                </div>
                <div class="check">
                 <i class="icon-pencil"></i> <i class="icon-remove"></i>
                    <input type="checkbox" class="styler"/>
                </div>
            </div>


            <div class="post no_border_bottom">
                <div class="number">
                    <i class="icon-sign-blank ok"></i> №20
                </div>
                <div class="text_edit">
                    <div class="text">Ждём бомбу в Июне, без денег никто не останется ! - http://itwip.net</div>
                    <div class="edit">  </div>
                </div>
                <div class="check">
                 <i class="icon-pencil"></i> <i class="icon-remove"></i>
                    <input type="checkbox" class="styler"/>
                </div>
            </div>
        </div>

        <div id="block_2_bottom"><div id="block_2_bottom_inset">
            <select class="styler">
                <option class="disabled">Действие с выбранными</option>
                <option>Удалить</option>
            </select>
            <a class="button">ок</a>
            <a class="button icon" href="#block_2_top" title="Поднятся на верх списка постов"><i class="icon-arrow-up"></i></a>
            <a class="button icon" href="#block_2_bottom" title="Отметить все посты перечисленные в списке"><i class="icon-ok"></i></a>

        </div></div>
        </div>

        <div id="block_bottom">
            <button class="button"> Сохранить в мои списки постов</button>
            <a href="/twitter/tweets/edit" class="button btn_blue">Перейти к размещению постов <i class="icon-double-angle-right"></i></a>
        </div>

    </div>
</div>

<!--
<div class="ui-dialog ui-widget ui-widget-content ui-corner-all ui-draggable ui-dialog-buttons" style="outline: 0px none; z-index: 1002; height: auto; width: 300px; top: 213px; left: 410.5px;" tabindex="-1" role="dialog" aria-labelledby="ui-id-1">

<div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix">
<span id="ui-id-1" class="ui-dialog-title">Редактирование поста №1</span>

<a href="#" class="ui-dialog-titlebar-close ui-corner-all" role="button"><span class="ui-icon ui-icon-closethick">X</span></a></div>

<div style="width: auto; min-height: 51px; height: auto;" id="dialog-message" class="ui-dialog-content ui-widget-content" scrolltop="0" scrollleft="0">
<div class="ui-dialog-content-text">
<textarea class="modal">Ждём бомбу в Июне, без денег никто не останется ! - http://itwip.net</textarea>
</div>
</div>
<div class="ui-dialog-buttonpane ui-widget-content ui-helper-clearfix"><div class="ui-dialog-buttonset">
<a title="Опустится в конец списка постов" href="#block_2_bottom" class="button icon"><i class="icon-camera"></i></a>
<button class="button btn_orange">Отменить</button>
<button class="button btn_blue">Сохранить</button>
</div></div>
</div>-->
		
		