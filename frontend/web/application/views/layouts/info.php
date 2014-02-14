<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo isset($this->pageTitle) ? CHtml::encode($this->pageTitle) : Yii::app()->name; ?></title>
<meta name="description" content="<?= CHtml::encode($this->metaDescription) ?>">
<meta name="keywords" content="<?= CHtml::encode($this->metaKeywords) ?>">
</head>
<body>
<div class="wrapper">
<?php if(Yii::app()->user->isGuest) { ?>
<div id="header">
  <div class="center">
	  <div id="logo"><a href="<?php echo Yii::app()->homeUrl; ?>"></a></div>
	  <div id="menu_top">
		  <ul>
		<?php
		$this->widget('zii.widgets.CMenu',array(
			'items'=>array(
				array('label'=>Yii::t('index', '_menu__guest_index'), 'url' => '/', 'active' => Yii::app()->controller->action->id == 'index'),
				array('label'=>Yii::t('index', '_menu__guest_community'), 'url' => 'http://community.itwip.net', 'linkOptions' => array('target' => '_blank')),
				array('label'=>Yii::t('index', '_menu__guest_help'), 'url' => array($this->createUrl('/help')), 'active' => Yii::app()->controller->action->id == 'help'),
				array('label'=>Yii::t('index', '_menu__guest_regulations'), 'url' => array($this->createUrl('/regulations')), 'active' => Yii::app()->controller->action->id == 'regulations'),
			),
			'activeCssClass' => 'menu_active',
		));
		?>
		  </ul>
	  </div>
  </div>
</div>
<?php } else { ?>
<?php 
	Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl . '/css/internal.css');
	$this->renderPartial('application.views.layouts._header'); 
?>
<?php } ?>
<?php echo $content; ?>
<?php $this->renderPartial('application.views.layouts._footer'); ?>
</div>
</body>
</html>
<?php 
	Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl . '/css/main.css');
	Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl . '/css/elements.css');
?>