<?php
	$this->widget('CLinkPager', array(
									'pages' => $pages,
									'cssFile' => false,
									'firstPageLabel' => false,
									'lastPageLabel' => false,
									'nextPageLabel' => Yii::t('internal', '_pageNavNext') . ' <i class="fa fa-angle-double-right"></i>',
									'prevPageLabel' => '<i class="fa fa-angle-double-left"></i> ' . Yii::t('internal', '_pageNavPrev'),
									'header' => false,
									'ajax_fnct'=>'Tweets._getPage',
								)); 
?>	