<?php
$this->pageTitle = Yii::app()->name . ' - ' . Yii::t('main', '_twitter_Title');
$this->metaDescription = Yii::t('main', '_twitter_Description');

$this->breadcrumbs[] = [0 => [Yii::t('breadcrumbs', '_twitter'), '/twitter']];
?>
<div id="twitter" class="block">
    <div class="block_title"><div class="block_title_inset"><i class="fa fa-twitter"></i> <h5><?php echo Yii::t('twitterModule.index', '_twitter_title'); ?></h5></div></div>
    <div class="block_content">
        <div id="block_1">
            <div id="info_page" class="no_border_bottom">
                <div class="icon"><i class="fa fa-info"></i></div>
                <div class="text"><?php echo Yii::t('twitterModule.index', '_twitter_title_info'); ?></div>
            </div>
            <div class="line_title no_border_bottom" onclick="Tweets.accordion(this); return false;">
				<?php echo Yii::t('twitterModule.index', '_title_search_parameters'); ?>
				<div class="open_icon"><i class="fa fa-caret-down"></i></div>
            </div>
            <div id="sort" style="display:none;">
                <form id="fParams" action="" method="GET" onsubmit="return false;">
					<?php $this->renderPartial('_params', ['model' => $model]); ?>
                </form>
                <div id="block_1_2_block">
                    <button class="button" onclick="Tweets.resetParams(this);">Сбросить параметры</button>
                    <button class="button" onclick="Tweets.setParams(this);">Искать</button>
                </div>
            </div>
            <div class="line_title_noopen no_border_bottom">
                <span id="_stats"><?php $this->renderPartial('_stats', ['model' => $model]); ?></span>
                <span class="block group_input search" style="float: right; margin-top: -8px; margin-right: 5px;"><input type="text" onkeyup="Tweets._getFromQuery('setQuery', '_searchButton');" placeholder="Найти аккаунт" id="setQuery"><button onclick="Tweets._getFromQuery('setQuery', '_searchButton');" class="button icon"><i class="fa fa-search" id="_searchButton"></i></button></span>
            </div>
        </div>
        <div id="block_2">
            <div class="table_head">
                <div class="table_head_inside">
                    <table>
                        <tr>
                            <td class="account">Аккаунт</td>
                            <td class="followers"><a href="javascript:;" onclick="Tweets._setOrder('followers', this);"> Читателей <i class="fa fa-caret-down"></i></a></td>
                            <td class="level"><a href="javascript:;" onclick="Tweets._setOrder('itr', this);">iTR <i class="fa fa-caret-down"></i></a></td>
                            <td class="tape"><a href="javascript:;" onclick="Tweets._setOrder('tape', this);"><i class="fa fa-comments-o"></i> <i class="fa fa-caret-down"></i></a></td>
                            <td class="index"> <span title="Наличие быстроробота яндекс на аккаунте.">Б.робот</span></td>
                            <td class="black"><span title="Кол-во пользователей у которых этот аккаунт в чёрном списке"><a href="javascript:;" onclick="Tweets._setOrder('blist', this);">Чёрный <i class="fa fa-caret-down"></i></a></span></td>
                            <td class="white"><span title="Кол-во пользователей у которых этот аккаунт в белом списке"><a href="javascript:;" onclick="Tweets._setOrder('wlist', this);">Белый <i class="fa fa-caret-down"></i></a></span></td>
                            <td class="price"><a href="javascript:;" onclick="Tweets._setOrder('price', this);">Цена поста <i class="fa fa-caret-down"></i></a></td>
                            <td class="add_b_w"><span class="fa fa-check-square"></span>&nbsp;&nbsp;&nbsp;&nbsp;<span class="fa fa-check-square-o"></span></td>
                            <td class="view no_border"></td>
                        </tr>
                    </table>
                </div>
            </div>
            <div id="lContent" class="acconts_list">
				<?php $this->renderPartial('_list', array('model' => $model)); ?>
            </div>
        </div>
    </div>
</div>