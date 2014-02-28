<div id="block_1_1_block">
    <div id="block_1_1">
        <h3 class="top_title"><?php echo Yii::t('twitterModule.tweets','_title_main_parameters_accounts'); ?></h3>
        <table>
            <tr>
				<td class="info_param">
					<?php echo Yii::t('twitterModule.tweets','_price_post'); ?>
				</td>
				<td class="param input"><?php echo Yii::t('twitterModule.tweets', '_from'); ?>
					<?php echo Html::activeTextField($model, 'price_post_ot', ['class' => 'posting_select_input']); ?>
					<?php echo Yii::t('twitterModule.tweets', '_to'); ?>
					<?php echo Html::activeTextField($model, 'price_post_do', ['class' => 'posting_select_input']); ?>
				</td>
			</tr>
            <tr>
				<td class="info_param">
					<?php echo Yii::t('twitterModule.tweets','_itr'); ?>
					<i class="tooltip" title="<?php echo Yii::t('twitterModule.tweets','_itr_info'); ?>">?</i>
				</td>
				<td class="param input">
					<?php echo Yii::t('twitterModule.tweets','_from'); ?>
					<?php echo Html::activeTextField($model, 'ot_itr', ['class' => 'posting_select_input']); ?>
					<?php echo Yii::t('twitterModule.tweets','_to'); ?>
					<?php echo Html::activeTextField($model, 'do_itr', ['class' => 'posting_select_input']); ?>
				</td>
			</tr>
            <tr>
				<td class="info_param">
					<?php echo Yii::t('twitterModule.tweets','_followers'); ?>
					<i class="tooltip" title="<?php echo Yii::t('twitterModule.tweets','_followers_info'); ?>">?</i>
				</td>
				<td class="param input">
					<?php echo Yii::t('twitterModule.tweets','_from'); ?>
					<?php echo Html::activeTextField($model, 'followers_ot', ['class' => 'posting_select_input']); ?>
					<?php echo Yii::t('twitterModule.tweets','_to'); ?>
					<?php echo Html::activeTextField($model, 'followers_do', ['class' => 'posting_select_input']); ?>
				</td>
			</tr>
            <tr>
				<td class="info_param">
					<?php echo Yii::t('twitterModule.tweets','_ya_rang'); ?>
					<i class="tooltip" title="<?php echo Yii::t('twitterModule.tweets','_ya_rang_info'); ?>">?</i>
				</td>
				<td class="param input">
					<?php echo Yii::t('twitterModule.tweets','_from'); ?>
					<?php echo Html::activeTextField($model, 'ya_r_ot', ['class' => 'posting_select_input']); ?>
					<?php echo Yii::t('twitterModule.tweets','_to'); ?>
					<?php echo Html::activeTextField($model, 'ya_r_do', ['class' => 'posting_select_input']); ?>
				</td>
			</tr>
            <tr>
				<td class="info_param">
					<?php echo Yii::t('twitterModule.tweets','_googl_rang'); ?>
					<i class="tooltip" title="<?php echo Yii::t('twitterModule.tweets','_googl_rang_info'); ?>">?</i>
				</td>
				<td class="param input">
					<?php echo Yii::t('twitterModule.tweets','_from'); ?>
					<?php echo Html::activeTextField($model, 'googl_rang_ot', ['class' => 'posting_select_input']); ?>
					<?php echo Yii::t('twitterModule.tweets','_to'); ?>
					<?php echo Html::activeTextField($model, 'googl_rang_do', ['class' => 'posting_select_input']); ?>
				</td></tr>
            <tr>
				<td class="info_param">
					<?php echo Yii::t('twitterModule.tweets','_age_blog'); ?>
					<i class="tooltip" title="<?php echo Yii::t('twitterModule.tweets','_age_blog_info'); ?>">?</i>
				</td>
				<td class="param input">
					<?php echo Yii::t('twitterModule.tweets','_from'); ?>
					<?php echo Html::activeTextField($model, 'age_blog_ot', ['class' => 'posting_select_input']); ?>
					<?php echo Yii::t('twitterModule.tweets','_to'); ?>
					<?php echo Html::activeTextField($model, 'age_blog_do', ['class' => 'posting_select_input']); ?>
				</td>
			</tr>
            <tr>
                <td class="info_param"><?php echo Yii::t('twitterModule.tweets','_themes_blog'); ?></td>
                <td class="param" id="_subjectsBox">
                    <?php echo Html::GroupDropDownList('Twitter[blogging_topics][]', 0, $model->getSubjects(), ['classes' => ['h_list', 'list'], 'empty' => [0 => Yii::t('twitterModule.accounts', '_topicAny')], 'class' => 'styler']); ?>
                </td>
            </tr>
            <tr>
                <td class="info_param"><?php echo Yii::t('twitterModule.tweets','_age_blogger'); ?> </td>
                <td class="param">
					<?php echo Html::DropDownList('Twitter[_age]', '0', $model->getAges()); ?>
                </td>
            </tr>
            <tr>
                <td class="info_param"><?php echo Yii::t('twitterModule.tweets','_blogs_tape'); ?> </td>
                <td class="param">
                    <?php echo Html::dropDownList('Twitter[tape]', '', [0 => 'Не указано', 1 => 'Человек и Бот', 2 => 'Человек',3=>'Бот']); ?>
                </td>
            </tr>
            <tr>
                <td class="info_param  radios"><?php echo Yii::t('twitterModule.tweets','_floor_blogger'); ?></td>
                <td class="param  radios">
					<?php echo Html::radioButton('Twitter[gender]', false, ['value' => 2]); ?> <label for="Accounts__gender_0">Мужской</label>&nbsp;<?php echo Html::radioButton('Twitter[gender]',false, ['value' => 1]); ?> <label for="Accounts__gender_1">Женский</label>&nbsp;<?php echo Html::radioButton('Twitter[gender]', true, ['value' => 0]); ?> <label for="Accounts__gender_2">Не важно</label></span>
                </td>
            </tr>
        </table>
    </div>
    <div id="block_1_2"></div>
    <div id="block_1_3">
        <h3 class="top_title"><?php echo Yii::t('twitterModule.tweets','_title_more_settings'); ?></h3>
        <table>
            <tr>
                <td class="info_param"><?php echo Yii::t('twitterModule.tweets','_in_yandex'); ?></td>
                <td class="param">
                    <select name="Twitter[in_yandex]" class="styler">
                        <option value="matter"><?php echo Yii::t('main','_does_not_matter'); ?></option>
                        <option value="yes"><?php echo Yii::t('main','_yes'); ?></option>
                        <option value="no"><?php echo Yii::t('main','_no'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="info_param"><?php echo Yii::t('twitterModule.tweets','_language_blog'); ?></td>
                <td class="param">
                    <select name="Twitter[language_blog]" class="styler">
                        <option value="matter"><?php echo Yii::t('main','_does_not_matter'); ?></option>
                        <option value="ru">Русский</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="info_param"><?php echo Yii::t('twitterModule.tweets','_added_system'); ?> <i class="tooltip" title="<?php echo Yii::t('twitterModule.tweets','_added_system_info'); ?>">?</i></td>
                <td class="param">
                    <select name="Twitter[added_system]" class="styler">
                        <option value="all"><?php echo Yii::t('twitterModule.tweets','_added_system_param_1'); ?></option>
                        <option value="today"><?php echo Yii::t('twitterModule.tweets','_added_system_param_2'); ?></option>
                        <option value="three_days"><?php echo Yii::t('twitterModule.tweets','_added_system_param_3'); ?></option>
                        <option value="seven_days"><?php echo Yii::t('twitterModule.tweets','_added_system_param_4'); ?></option>
                        <option value="month"><?php echo Yii::t('twitterModule.tweets','_added_system_param_5'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="info_param radios"><?php echo Yii::t('twitterModule.tweets','_confirmation_applications'); ?> <i class="tooltip" title="<?php echo Yii::t('twitterModule.tweets','_confirmation_applications_info'); ?>">?</i></td>
                <td class="param radios">
					<?php echo Html::activeCheckBox($model, 'pType[manual]'); ?> <?php echo Yii::t('main', '_manual'); ?> <?php echo Html::activeCheckBox($model, 'pType[auto]'); ?> <?php echo Yii::t('main', '_auto'); ?>
				</td>
            </tr>
            <tr>
                <td class="info_param radios">Способ оплаты</td>
                <td class="param radios">
                    <?php echo Html::activeCheckBox($model, 'payMethod[rv]'); ?> <?php echo Yii::t('twitterModule.tweets', '_balance_spend_personal'); ?> <?php echo Html::activeCheckBox($model, 'payMethod[bv]'); ?> <?php echo Yii::t('twitterModule.tweets', '_balance_spend_bonus'); ?>
                </td>
            </tr>
        </table>
        <h3 class="top_title"><?php echo Yii::t('twitterModule.tweets','_title_personal_accounts_filtering'); ?></h3>
        <table>
            <tr>
                <td class="info_param">Черно-белый список</td>
                <td class="param">
                    <?php echo Html::dropDownList('Twitter[bw]', '', [0 => Yii::t('main', '_does_not_matter'), 1 => Yii::t('twitterModule.tweets', '_accounts_filtering_4'), 2 => Yii::t('twitterModule.tweets', '_accounts_filtering_5')]); ?>
                </td>
            </tr>
        </table>
    </div>
</div>