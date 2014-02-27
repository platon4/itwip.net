<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title><?php echo isset($this->pageTitle) ? CHtml::encode($this->pageTitle) : Yii::app()->name; ?></title>
    <meta name="description" content="<?= CHtml::encode($this->metaDescription) ?>">
    <meta name="keywords" content="<?= CHtml::encode($this->metaKeywords) ?>">
    <noscript>
        <meta http-equiv="refresh" content="0; URL=/incompatibility">
    </noscript>
</head>
<body>
<div class="wrapper">
    <?php $this->renderPartial('application.views.layouts._header'); ?>
    <div id="content">
        <div id="content_block">
            <div id="container">
                <?php $this->widget('application.widgets.Menu', ['ajax' => false, 'activeBlock' => $this->activeMenu]); ?>
                <div id="content_right">
                    <?php
                    $this->widget('application.widgets.Breadcrumbs', ['data' => $this->breadcrumbs]);
                    ?>
                    <?php if(Yii::app()->user->hasFlash('_messages')): ?>
                        <div class="info">
                            <div class="line_info"
                                 style="margin-bottom: 20px;"><?php echo Yii::app()->user->getFlash('_messages'); ?></div>
                        </div>
                    <?php endif; ?>
                    <?= $content; ?>
                </div>
            </div>
        </div>
    </div>
    <?php $this->renderPartial('application.views.layouts._footer'); ?>
</div>
</body>
</html>