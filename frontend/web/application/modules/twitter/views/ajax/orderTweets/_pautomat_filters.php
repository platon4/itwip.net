<div id="block_manual">
        <div id="block_manual_setting">
            <div id="block_1_1_block">
              <div id="block_1_1">
                <h3 class="top_title"><?php echo Yii::t('twitterModule.tweets', '_title_main_parameters_accounts'); ?></h3>
                <table>
                    <tr><td class="info_param"><?php echo Yii::t('twitterModule.tweets', '_itr'); ?> <i class="tooltip" title="<?php echo Yii::t('twitterModule.tweets', '_itr_info'); ?>">?</i></td><td class="param input"><input type="text" style="width: 165px;" class="posting_select_input " placeholder="1-100"/></td></tr>
                    <tr><td class="info_param"><?php echo Yii::t('twitterModule.tweets', '_price_post'); ?></td><td class="param input"><input type="text" value="0" style="width: 165px;color: #AFAFAF;" class="posting_select_input " disabled="disabled"/></td></tr>
                    <tr><td class="info_param"><?php echo Yii::t('twitterModule.tweets', '_ya_rang'); ?> <i class="tooltip" title="<?php echo Yii::t('twitterModule.tweets', '_ya_rang_info'); ?>">?</i></td><td class="param input"><?php echo Yii::t('twitterModule.tweets', '_from'); ?> <input type="text" value="0" class="posting_select_input"/> <?php echo Yii::t('twitterModule.tweets', '_to'); ?> <input type="text" value="4000000" class="posting_select_input"/></td></tr>
                    <tr><td class="info_param"><?php echo Yii::t('twitterModule.tweets', '_googl_rang'); ?> <i class="tooltip" title="<?php echo Yii::t('twitterModule.tweets', '_googl_rang_info'); ?>">?</i></td><td class="param input"><?php echo Yii::t('twitterModule.tweets', '_from'); ?> <input type="text" value="0" class="posting_select_input"/> <?php echo Yii::t('twitterModule.tweets', '_to'); ?> <input type="text" value="10" class="posting_select_input"/></td></tr>
                    <tr><td class="info_param"><?php echo Yii::t('twitterModule.tweets', '_age_blog'); ?> <i class="tooltip" title="<?php echo Yii::t('twitterModule.tweets', '_age_blog_info'); ?>">?</i></td><td class="param input"><?php echo Yii::t('twitterModule.tweets', '_from'); ?> <input type="text" value="1" class="posting_select_input"/> <?php echo Yii::t('twitterModule.tweets', '_to'); ?> <input type="text" value="52" class="posting_select_input"/></td></tr>
                    <tr><td class="info_param"><?php echo Yii::t('twitterModule.tweets', '_themes_blog'); ?></td><td class="param"><select class="styler"><option>Не важно</option><option>Да</option></select><button onclick="Settings._addSubject($('#_subjects_0'), this); return false;" title="" class="button icon" type="button"><i class="icon-plus"></i></button></td></tr>
                    <tr><td class="info_param"><?php echo Yii::t('twitterModule.tweets', '_age_blogger'); ?> </td><td class="param"><select class="styler"><option>Не важно</option><option>Да</option></select></td></tr>
                    <tr><td class="info_param  radios"><?php echo Yii::t('twitterModule.tweets', '_floor_blogger'); ?></td><td class="param  radios"><span id="Accounts__gender"><input type="radio" name="Accounts[_gender]" checked="checked" value="2" id="Accounts__gender_0" class="styler" style="position: absolute; left: -9999px;"><span style="display: inline-block" class="radio styler checked" id="Accounts__gender_0-styler"><span></span></span> <label for="Accounts__gender_0">Мужской</label>&nbsp;<input type="radio" name="Accounts[_gender]" value="1" id="Accounts__gender_1" class="styler" style="position: absolute; left: -9999px;"><span style="display: inline-block" class="radio styler" id="Accounts__gender_1-styler"><span></span></span> <label for="Accounts__gender_1">Женский</label>&nbsp;<input type="radio" name="Accounts[_gender]" value="0" id="Accounts__gender_2" class="styler" style="position: absolute; left: -9999px;"><span style="display: inline-block" class="radio styler" id="Accounts__gender_2-styler"><span></span></span> <label for="Accounts__gender_2">Не важно</label></span></td></tr>
                </table>

              </div>
              <div id="block_1_2"></div>
              <div id="block_1_3">
                <h3 class="top_title"><?php echo Yii::t('twitterModule.tweets', '_title_more_settings'); ?></h3>
                  <table>
                    <tr><td class="info_param"><?php echo Yii::t('twitterModule.tweets', '_in_yandex'); ?></td><td class="param"><select class="styler"><option><?php echo Yii::t('main', '_does_not_matter'); ?></option><option><?php echo Yii::t('main', '_yes'); ?></option><option><?php echo Yii::t('main', '_no'); ?></option></select></td></tr>
                    <tr><td class="info_param"><?php echo Yii::t('twitterModule.tweets', '_in_google'); ?></td><td class="param"><select class="styler"><option><?php echo Yii::t('main', '_does_not_matter'); ?></option><option><?php echo Yii::t('main', '_yes'); ?></option><option><?php echo Yii::t('main', '_no'); ?></option></select></td></tr>
                    <tr><td class="info_param"><?php echo Yii::t('twitterModule.tweets', '_language_blog'); ?></td><td class="param"><select class="styler"><option><?php echo Yii::t('main', '_does_not_matter'); ?></option><option>Русский</option></select></td></tr>
                    <tr><td class="info_param"><?php echo Yii::t('twitterModule.tweets', '_added_system'); ?> <i class="tooltip" title="<?php echo Yii::t('twitterModule.tweets', '_added_system_info'); ?>">?</i></td><td class="param"><select class="styler"><option><?php echo Yii::t('twitterModule.tweets', '_added_system_param_1'); ?></option><option><?php echo Yii::t('twitterModule.tweets', '_added_system_param_2'); ?></option><option><?php echo Yii::t('twitterModule.tweets', '_added_system_param_3'); ?></option><option><?php echo Yii::t('twitterModule.tweets', '_added_system_param_4'); ?></option><option><?php echo Yii::t('twitterModule.tweets', '_added_system_param_5'); ?></option></select></td></tr>
                    <tr><td class="info_param radios"><?php echo Yii::t('twitterModule.tweets', '_confirmation_applications'); ?> <i class="tooltip" title="<?php echo Yii::t('twitterModule.tweets', '_confirmation_applications_info'); ?>">?</i></td><td class="param radios"><input type="radio" class="styler"/> <?php echo Yii::t('main', '_manual'); ?> <input type="radio" class="styler"/> <?php echo Yii::t('main', '_auto'); ?></td></tr>
                  </table>

                <h3 class="top_title"><?php echo Yii::t('twitterModule.tweets', '_title_personal_accounts_filtering'); ?></h3>
                  <table>
                    <tr><td><input type="checkbox" class="styler"/> <?php echo Yii::t('twitterModule.tweets', '_accounts_filtering_1'); ?></td></tr>
                    <tr><td><input type="checkbox" class="styler"/> <?php echo Yii::t('twitterModule.tweets', '_accounts_filtering_2'); ?></td></tr>
                    <tr><td><input type="checkbox" class="styler"/> <?php echo Yii::t('twitterModule.tweets', '_accounts_filtering_3'); ?></td></tr>
                    <tr><td><input type="checkbox" class="styler"/> <?php echo Yii::t('twitterModule.tweets', '_accounts_filtering_4_1'); ?></td></tr>
                    <tr><td><input type="checkbox" checked="checked" class="styler"/> <?php echo Yii::t('twitterModule.tweets', '_accounts_filtering_5_1'); ?></td></tr>
                  </table>
              </div>
            </div>
            <h3 class="top_title" style=" color: #767676; font-size: 11px;"><?php echo Yii::t('twitterModule.tweets', '_search_results'); ?> <b>223</b> </h3>
			<?php echo $this->renderPartial('_time_target',array('_post_to'=>true)); //Временой таргентинг ?>
            <div id="more_options">
              <h3 class="top_title"><?php echo Yii::t('twitterModule.tweets', '_additional_network_settings'); ?></h3>
              <div class="options">
                  <input type="checkbox" class="styler"> <?php echo Yii::t('twitterModule.tweets', '_network_settings_1'); ?>
              </div>
              <div class="options">
                  <input type="checkbox" class="styler"> <?php echo Yii::t('twitterModule.tweets', '_network_settings_2'); ?>
              </div>
            </div>

            <div id="block_1_6">
                <span style="float: left; padding-top: 7px; font-weight: bold"><?php echo Yii::t('twitterModule.tweets', '_the_selected_posts'); ?> 59, <?php echo Yii::t('twitterModule.tweets', '_amount'); ?> 229.58 руб. </span>
                <button class="button"><?php echo Yii::t('twitterModule.tweets', '_save_filter'); ?></button>
                <button class="button btn_blue"><?php echo Yii::t('twitterModule.tweets', '_place_posts'); ?></button>
            </div>
        </div>

    </div>