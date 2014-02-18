<?php if($model->getCount()) { ?>
<table>
    <?php foreach($model->getRows() as $row) { ?>
            <tr>
                <td class="account">
                    <div class="account_img">
                        <img src="<?php echo Html::encode($row['avatar']); ?>">
                    </div>
                    <div class="account_NameLogin">
                        <span class="account_Name block"><?php echo Html::encode($row['name']); ?></span>
                        <span class="account_Login block"><a target="_blank" href="https://twitter.com/<?php echo Html::encode($row['screen_name']); ?>">@<?php echo Html::encode($row['screen_name']); ?></a></span>
                    </div>
                </td>
                <td class="followers"><?php echo $row['followers']; ?></td>
                <td class="level"><?php echo Html::encode($row['itr']); ?></td>
                <td class="tape">
                    <?php
                    switch($row['tape'])
                    {
                        case 1:
                            echo '<span title="Человек и Бот"><i class="fa fa-male"></i><i class="fa fa-android"></i></span>';
                            break;

                        case 2:
                            echo '<span title="Человек"><i class="fa fa-male"></i></span>';
                            break;

                        case 3:
                            echo '<span title="Бот"><i class="fa fa-android"></i></span>';
                            break;

                        default:
                            echo '-';
                    }
                    ?>
                </td>
                <td class="index">
                    <?php if($row['in_yandex']) { ?>
                        <img alt="pic" src="/i/elements/yandex.png" />
                    <?php } else { ?>
                        <img alt="pic" src="/i/elements/yandex_no.png" />
                 <?php } ?>
                </td>
                <td class="black"><?php echo Html::encode($row['black_list']); ?></td>
                <td class="white"><?php echo Html::encode($row['white_list']); ?></td>
                <td class="price"><?php echo CMoney::_c($row['_price'],true); ?></td>
                <td class="add_b_w">
                    <a id="black_<?php echo $row['id']; ?>" title="Занести в чёрный список" href="javascript:;" onclick="Tweets._list('<?php echo $row['id']; ?>', 'black');" class="button black_button icon_small<?php if(in_array($row['id'],$bids)) { echo ' selected'; } ?>"><i class="fa fa-check-square"></i></a>
                    <a id="white_<?php echo $row['id']; ?>" title="Занести в белый список" href="javascript:;" onclick="Tweets._list('<?php echo $row['id']; ?>', 'white');" class="button icon_small<?php if(in_array($row['id'],$wids)) { echo ' selected'; } ?>"><i class="fa fa-check-square-o"></i></a>
                </td>
                <td class="view"><button title="Посмотреть детали аккаунта" class="button icon_small" onclick="Tweets.getAccountInfo('<?php echo $row['id']; ?>', 'Детали twitter аккаунта');"><i class="fa fa-eye"></i></button></td>
            </tr>
    <?php } ?>
</table>
<div class="table_bottom">
	<div class="table_bottom_inside">
		<div class="page_nav_page">
			<div id="pagesList" class="_cHide">
		<?php $this->renderPartial("_pages", array('pages' => $model->getPages())); ?>
			</div>
		</div>
		<?php if($model->getCount()) { ?>
			<div class="page_nav_how">
					<?php echo Yii::t('twitterModule.accounts', '_pageNavHow'); ?>
				<select class="styler" onchange="Tweets._setLimit(this); return false;">
					<?php
					foreach($model->getPageLimits() as $option) {

						if($model->getLimit() == $option['value'])
							$htmlOption = ['value' => $option['value'], 'selected' => 'selected'];
						else
							$htmlOption = ['value' => $option['value']];

						echo Html::tag('option', $htmlOption, ($option['title'] == "_all") ? Yii::t('twitterModule.accounts', '_pageNavHowAll') : $option['title']);
					}
					?>
				</select>
			</div>
		<?php } ?>
	</div>
</div>
<?php } else { ?>
<div style="padding: 8px;">
	<div style="text-align: center;"><?php echo Yii::t('twitterModule.accounts','_not_found_accounts'); ?></div>
</div>
<?php } ?>

