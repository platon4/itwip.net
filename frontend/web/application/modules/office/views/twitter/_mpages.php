<?php

$this->widget('CLinkPager',array(
    'pages'=>$pages,
    'cssFile'=>false,
    'firstPageLabel'=>false,
    'lastPageLabel'=>false,
    'nextPageLabel'=>Yii::t('internal','_pageNavNext').' <i class="icon-angle-right"></i>',
    'prevPageLabel'=>'<i class="icon-angle-left"></i> '.Yii::t('internal','_pageNavPrev'),
    'header'=>false,
    'ajax_fnct'=>'_M._getPage',
));
