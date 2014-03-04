<?php if($model->getAccountsCount()) { ?>
	<table>
    <?php foreach($model->getAccounts() as $row) { ?>
		<tr id="_accountData_<?php echo $row['id']; ?>" data-price="<?php echo round($row['_price'], 2); ?>" data-old="<?php echo $row['tweetsCount']; ?>" data-select="<?php echo $row['tweetsCount'] ? 'yes' : 'no'; ?>" data-count="<?php echo $row['tweetsCount']; ?>">
            <td class="account">
            <div class="account_img">
              <img src="<?php echo ($row['avatar']) ? Html::encode($row['avatar']) : '/i/_default.png'; ?>">
            </div>
            <div class="account_NameLogin">
              <span class="account_Name block"><?php echo Html::encode($row['name']); ?></span>
              <span class="account_Login block"><a href="https://twitter.com/<?php echo Html::encode($row['screen_name']); ?>" target="_blank">@<?php echo Html::encode($row['screen_name']); ?></a></span>
            </div>
            </td>
            <td class="followers"><?php echo $row['followers']; ?></td>
            <td class="level"><?php echo $row['itr']; ?></td>
            <td class="rang"><?php echo $row['yandex_rank']; ?></td>
            <td class="pr"><?php echo $row['google_pr']; ?></td>
            <td class="tape">
                    <?php
					switch($row['tape']) {
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
            <td class="index"><?php if($row['in_yandex']) { ?>
					<img alt="Яндекс" src="/i/elements/yandex.png"><?php }
				else { ?>
					<img alt="Яндекс" src="/i/elements/yandex_no.png"><?php } ?></td>
            <td class="select"><?php echo (!$row['working_in']) ? Yii::t('twitterModule.tweets', '_manual') : Yii::t('twitterModule.tweets', '_auto'); ?></td>
            <td class="price"><?php echo round($row['_price'], 2); ?> руб.</td>
            <td class="text">
                <div id="_tweets_<?php echo $row['id']; ?>">
                    <?php if($row['tweetsCount'] > 0) { ?>
						<span title="<?php echo Yii::t('twitterModule.tweets', '_automatic_selection_post'); ?>"><?php echo $row['tweetsCount']; ?></span> -
						<a class="here" href="javascript:void(0);" onclick="Twitter.o.m.d.addTweets('<?php echo $row['id']; ?>',this);"><?php echo Yii::t('twitterModule.tweets', '_show_select'); ?>
							<i class="fa fa-caret-down"></i></a>
					<?php }
					else { ?>
						<span title="<?php echo Yii::t('twitterModule.tweets', '_add_post_account'); ?>">А</span> -
						<a class="here" href="javascript:void(0);" onclick="Twitter.o.m.d.addTweets('<?php echo $row['id']; ?>',this);"><?php echo Yii::t('twitterModule.tweets', '_select_post'); ?>
							<i class="fa fa-caret-down"></i></a>
					<?php } ?>
                </div>
            </td>
            <td class="check"><?php echo Html::checkBox('', 0, ['id' => 'accounts_' . $row['id'], 'value' => $row['id'], 'onchange' => 'Twitter.o.m.d.toggle(this);', 'data-id' => $row['id']]); ?></td>
        </tr>
	<?php } ?>
</table>
	<div class="table_bottom">
		<div class="table_bottom_inside">
			<div id="pagesList" class="page_nav_page">
				<?php echo $this->renderPartial('application.views.main._pages', array('ajax_query' => 'Twitter.o.m.d.getPage', 'pages' => $model->getPages())); ?>
			</div>
			<?php if($model->getAccountsCount()) { ?>
				<div class="page_nav_how">
					Отображать на страницу:
					<?php echo Html::dropDownList('_limit', $model->limit, [10 => 10, 20 => 20, 30 => 30, 40 => 40, 50 => 50, 100 => 100], ['onchange' => 'Twitter.o.m.d.setLimit(this.value);']); ?>
				</div>
			<?php } ?>
		</div>
	</div>
<?php }
else { ?>
	<div style="text-align: center; padding: 7px;"><?php echo Yii::t('twitterModule.tweets', '_filters_no_accounts'); ?></div>
<?php } ?>