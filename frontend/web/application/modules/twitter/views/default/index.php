<?php
$this->pageTitle = Yii::app()->name . ' - ' . Yii::t('main', '_twitter_Title');
$this->metaDescription = Yii::t('main', '_twitter_Description');

$this->breadcrumbs[] = array(
	0 => array(Yii::t('breadcrumbs', '_twitter'), '/twitter'),
);
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
					<?php $this->renderPartial('_params', array('ageData' => $ageData, 'subjects' => $subjects)); ?>
                </form>
                <div id="block_1_2_block">
                    <button class="button" onclick="Tweets.resetParams(this);">Сбросить параметры</button>
                    <button class="button" onclick="Tweets.setParams(this);">Искать</button>
                </div>
            </div>
            <div class="line_title_noopen no_border_bottom">
                <span id="_stats"><?php $this->renderPartial('_stats', array('accounts_count_in_blacklist' => $accounts_count_in_blacklist,
						'_count' => $_count, 'accounts_count_in_whitelist' => $accounts_count_in_whitelist)); ?></span>
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
<?php $this->renderPartial('_list', array('wids' => $wids, 'bids' => $bids, 'list' => $model)); ?>
            </div>
            <div class="table_bottom">
                <div class="table_bottom_inside">
                    <div class="page_nav_page">
                        <div id="pagesList" class="_cHide">
                    <?php $this->renderPartial("_pages", array('pages' => $pages)); ?>
                        </div>
                    </div>
					<?php if($_count) {
						?>
						<div class="page_nav_how">
                                <?php echo Yii::t('twitterModule.accounts', '_pageNavHow'); ?>
							<select class="styler" onchange="Tweets._setLimit(this);
                                    return false;">
                                <?php foreach($limitList as $option) {
									?>
									<?php
									if(isset(Yii::app()->session['_accountsTLimit']) AND Yii::app()->session['_accountsTLimit'] == $option['value']) {
										$htmlOption = array('value' => $option['value'],
											'selected' => 'selected');
									}
									else {
										$htmlOption = array('value' => $option['value']);
									}
									echo Html::tag('option', $htmlOption, ($option['title'] == "_all") ? Yii::t('twitterModule.accounts', '_pageNavHowAll') : $option['title']);
									?>
								<?php } ?>
                            </select>
                        </div>
					<?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>