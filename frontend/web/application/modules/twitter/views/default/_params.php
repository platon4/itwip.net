<div id="block_1_1_block">
    <div id="block_1_1">
        <h3 class="top_title"><?php echo Yii::t('twitterModule.tweets','_title_main_parameters_accounts'); ?></h3>
        <table>
            <tr><td class="info_param"><?php echo Yii::t('twitterModule.tweets','_price_post'); ?></td><td class="param input"><?php echo Yii::t('twitterModule.tweets','_from'); ?> <input type="text" name="Params[post_price_ot]" value="0.5" class="posting_select_input"/> <?php echo Yii::t('twitterModule.tweets','_to'); ?> <input name="Params[post_price_do]" type="text" value="1500" class="posting_select_input"/></td></tr>
            <tr><td class="info_param"><?php echo Yii::t('twitterModule.tweets','_itr'); ?> <i class="tooltip" title="<?php echo Yii::t('twitterModule.tweets','_itr_info'); ?>">?</i></td><td class="param input"><?php echo Yii::t('twitterModule.tweets','_from'); ?> <input type="text" value="1" name="Params[itr_ot]" class="posting_select_input"/> <?php echo Yii::t('twitterModule.tweets','_to'); ?> <input type="text" name="Params[itr_do]" value="70" class="posting_select_input"/></td></tr>
            <tr><td class="info_param"><?php echo Yii::t('twitterModule.tweets','_followers'); ?> <i class="tooltip" title="<?php echo Yii::t('twitterModule.tweets','_followers_info'); ?>">?</i></td><td class="param input"><?php echo Yii::t('twitterModule.tweets','_from'); ?> <input type="text" value="500" name="Params[followers_ot]" class="posting_select_input"/> <?php echo Yii::t('twitterModule.tweets','_to'); ?> <input type="text" name="Params[followers_do]" value="5000000" class="posting_select_input"/></td></tr>
            <tr><td class="info_param"><?php echo Yii::t('twitterModule.tweets','_ya_rang'); ?> <i class="tooltip" title="<?php echo Yii::t('twitterModule.tweets','_ya_rang_info'); ?>">?</i></td><td class="param input"><?php echo Yii::t('twitterModule.tweets','_from'); ?> <input type="text" name="Params[yav_ot]" value="0" class="posting_select_input"/> <?php echo Yii::t('twitterModule.tweets','_to'); ?> <input type="text" name="Params[yav_do]" value="4000000" class="posting_select_input"/></td></tr>
            <tr><td class="info_param"><?php echo Yii::t('twitterModule.tweets','_googl_rang'); ?> <i class="tooltip" title="<?php echo Yii::t('twitterModule.tweets','_googl_rang_info'); ?>">?</i></td><td class="param input"><?php echo Yii::t('twitterModule.tweets','_from'); ?> <input type="text" value="0" name="Params[glp_ot]" class="posting_select_input"/> <?php echo Yii::t('twitterModule.tweets','_to'); ?> <input type="text" name="Params[glp_do]" value="10" class="posting_select_input"/></td></tr>
            <tr><td class="info_param"><?php echo Yii::t('twitterModule.tweets','_age_blog'); ?> <i class="tooltip" title="<?php echo Yii::t('twitterModule.tweets','_age_blog_info'); ?>">?</i></td><td class="param input"><?php echo Yii::t('twitterModule.tweets','_from'); ?> <input type="text" name="Params[age_ot]" value="1" class="posting_select_input"/> <?php echo Yii::t('twitterModule.tweets','_to'); ?> <input name="Params[age_do]" type="text" value="<?php echo round(((time() - strtotime('15.07.2006 00:00:00')) / 86400) / 31); ?>" class="posting_select_input"/></td></tr>
            <tr>
                <td class="info_param"><?php echo Yii::t('twitterModule.tweets','_themes_blog'); ?></td>
                <td class="param" id="_subjectsBox">
                    <?php echo Html::GroupDropDownList('Params[_subject]',0,$subjects,array('classes'=>array('h_list','list'),'empty'=>array(0=>Yii::t('twitterModule.accounts','_topicAny')),'class'=>'styler')); ?>
                </td>
            </tr>
            <tr>
                <td class="info_param"><?php echo Yii::t('twitterModule.tweets','_age_blogger'); ?> </td>
                <td class="param">
					<?php echo Html::DropDownList('Params[_age]','0',$ageData,array('class'=>'styler')); ?>
                </td>
            </tr>
            <tr>
                <td class="info_param"><?php echo Yii::t('twitterModule.tweets','_blogs_tape'); ?> </td>
                <td class="param">
                    <?php echo Html::dropDownList('Params[tape]', '', array(0 => 'Не указано', 1 => 'Человек и Бот', 2 => 'Человек',3=>'Бот')); ?>
                </td>
            </tr>
            <tr>
                <td class="info_param  radios"><?php echo Yii::t('twitterModule.tweets','_floor_blogger'); ?></td>
                <td class="param  radios">
<?php echo Html::radioButton('Params[_gender]',false,array('value'=>2)); ?> <label for="Accounts__gender_0">Мужской</label>&nbsp;<?php echo Html::radioButton('Params[_gender]',false,array(
    'value'=>1)); ?> <label for="Accounts__gender_1">Женский</label>&nbsp;<?php echo Html::radioButton('Params[_gender]',false,array(
    'value'=>0)); ?> <label for="Accounts__gender_2">Не важно</label></span>
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
                    <select name="Params[in_yandex]" class="styler">
                        <option value="matter"><?php echo Yii::t('main','_does_not_matter'); ?></option>
                        <option value="yes"><?php echo Yii::t('main','_yes'); ?></option>
                        <option value="no"><?php echo Yii::t('main','_no'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="info_param"><?php echo Yii::t('twitterModule.tweets','_language_blog'); ?></td>
                <td class="param">
                    <select name="Params[_lang]" class="styler">
                        <option value="matter"><?php echo Yii::t('main','_does_not_matter'); ?></option>
                        <option value="ru">Русский</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="info_param"><?php echo Yii::t('twitterModule.tweets','_added_system'); ?> <i class="tooltip" title="<?php echo Yii::t('twitterModule.tweets','_added_system_info'); ?>">?</i></td>
                <td class="param">
                    <select name="Params[time_add]" class="styler">
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
                <td class="param radios"><?php echo Html::radioButton('Params[pType]',false,array(
    'value'=>0)); ?> <?php echo Yii::t('main','_manual'); ?> <?php echo Html::radioButton('Params[pType]',false,array(
    'value'=>1)); ?> <?php echo Yii::t('main','_auto'); ?></td>
            </tr>
        </table>

        <h3 class="top_title"><?php echo Yii::t('twitterModule.tweets','_title_personal_accounts_filtering'); ?></h3>
        <table>
            <tr><td><?php echo Html::checkBox('Params[show_only_white_list]','',array('value'=>'yes')); ?> <?php echo Yii::t('twitterModule.tweets','_accounts_filtering_4'); ?></td></tr>
            <tr><td><?php echo Html::checkBox('Params[no_show_block_list]','',array('value'=>'yes')); ?> <?php echo Yii::t('twitterModule.tweets','_accounts_filtering_5'); ?></td></tr>
            <tr><td><?php echo Html::checkBox('Params[allow_bonus]','',array('value'=>'yes')); ?> <?php echo Yii::t('twitterModule.tweets','_accounts_filtering_6'); ?></td></tr>
        </table> 
    </div>
</div>