<?php
$this->pageTitle = Yii::app()->name . ' - ' . Yii::t('main', '_prepared_Title');
$this->metaDescription = Yii::t('main', '_prepared_Description');
$this->breadcrumbs[] = array(
    0 => array(Yii::t('breadcrumbs', '_twitter'), ''),
    1 => array(Yii::t('breadcrumbs', '_tw_advertiser'), ''),
    2 => array(Yii::t('breadcrumbs', '_tw_trained_posts'), '')
);
?>
<div id="prepared" class="block">
    <div class="block_title"><div class="block_title_inset"><i class="fa fa-list"></i> <h5><?php echo Yii::t('main', '_prepared_Title'); ?></h5></div></div>
    <div class="block_content">
        <div class="no_border_bottom" id="info_page">
            <div class="icon"><i class="fa fa-exclamation"></i></div>
            <div class="text"><?php echo Yii::t('twitterModule.prepared', '_page_info'); ?></div>
        </div>
        <div style="cursor: default" class="line_title no_border_bottom">
            <?php echo Yii::t('twitterModule.prepared', '_save_list'); ?>: <span id="_all_count"><?php echo $model->rosterCount(); ?></span>
            <span style="margin: -4px 2px 0px 0px;" class="group_input search float_right"><input type="text" id="setQuery" placeholder="<?php echo Yii::t('twitterModule.prepared', '_search_name'); ?>" onkeyup="Tweets._getPreparedFromQuery('setQuery', '_searchButton');"><button id="_searchButton" class="button icon" onclick="Tweets._getPreparedFromQuery('setQuery', '_searchButton');"><i class="fa fa-search"></i></button></span>
        </div>
        <div class="table_head">
            <div class="table_head_inside">
                <table>
                    <tbody>
                        <tr>
                            <td class="id"><a href="javascript: void(0);" onclick="Tweets._setPreparedOrder('id', this);"><?php echo Yii::t('twitterModule.prepared', '_id'); ?> <i class="fa fa-caret-down"></i></a></td>
                            <td class="date"><a href="javascript: void(0);" onclick="Tweets._setPreparedOrder('date', this);"><?php echo Yii::t('twitterModule.prepared', '_date'); ?> <i class="fa fa-caret-down"></i></a></td>
                            <td class="name"><?php echo Yii::t('twitterModule.prepared', '_name'); ?></td>
                            <td class="tweet"><?php echo Yii::t('twitterModule.prepared', '_tweets'); ?></td>
                            <td class="edit"> </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div id="preparedList" class="acconts_list">
            <?php echo $this->renderPartial('_prepared_rows', array('model' => $model)); ?>
        </div>
    </div>
</div>