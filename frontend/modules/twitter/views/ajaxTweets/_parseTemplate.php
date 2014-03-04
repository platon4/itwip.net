<div id="modal_setting_parser">
    <div class="formula_post">
        <h3 class="top_title" style="margin: 0px -10px;">Формула генерации постов</h3>
        <div class="no_border_bottom" id="info_page">
            <div class="icon"><i class="icon-info"></i></div><div class="text">В формулу можно добавить хеш теги, и другие символы помимо главных переменных:<br>{url} - адрес страницы<br>{title} - заголовок получаемой страницы</div>
         </div>
         <textarea onchange="Tweets.parseChange('parseUrl',this.value);"><?php echo $template; ?></textarea>
     </div>
     <div class="delete_url"><h3 class="top_title" style="margin: 0px -10px;">{url} Исключаемые адреса страниц</h3>
            <div class="no_border_bottom" id="info_page"><div class="icon"><i class="icon-info"></i></div><div class="text">Используется для исключения ненужных страниц при парсинге, вы можете ввести как часть URL, так и полный адрес.<br> <b>Советуем</b> обязательно использовать если есть переадресация на ссылках (предупреждения о переходе на другой сайт).</div></div>
            <textarea onchange="Tweets.parseChange('parseExclide',this.value);" placeholder="Вводите части, или полный URL разделяя их нажатием - ENTER"><?php echo $excludeUrl; ?></textarea>
     </div>
     <div class="delete_title">
            <h3 class="top_title" style="margin: 0px -10px;">{title} Исключаемые слова из Заголовка страницы </h3>
            <div class="no_border_bottom" id="info_page"><div class="icon"><i class="icon-info"></i></div><div class="text">Исключает введёные Вами слова при генерациии постов.<br> Например ваш заголовок: "Скачать фильм Мумия-3, скачать бесплатно."<br> Исключая слова: "скачать, бесплатно"<br> Мы можем составить отредактировав формулу более красивое: "Рекомендую к скачиванию фильм в хорошем качестве Мумия-3, на сайте {url}"</div></div>
            <textarea onchange="Tweets.parseChange('parseWords',this.value);" placeholder="Вводите слова разделяя их нажатием - ENTER"><?php echo $excludeWords; ?></textarea>
      </div>
</div>