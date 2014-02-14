<?php
$ageData =require Yii::app()->getModulePath().'/twitter/data/_age.php';
$subjects=Html::groupByKey(Subjects::model()->_getAll(array('order'=>'sort')),'id','_key','parrent');

$_subjects    =$fmodel->_themes_blog;
$_subject_html="";

if(count($_subjects))
{
    $i=0;

    foreach($_subjects as $_id)
    {
        $_subject_html .= $this->renderPartial('application.modules.twitter.views.default._subjectsDropDownList',array(
            'remove'=>(!$i)?0:1,'selected'=>$_id,'bid'=>'_subjects_0','subjects'=>$subjects),true);

        foreach($subjects as $zs3q=> $a3q2z)
        {
            if($zs3q == $_id)
            {
                unset($subjects[$zs3q]);
            } else
            {
                if(is_array($a3q2z))
                {
                    foreach($a3q2z as $u3n6=> $bb3q)
                    {
                        foreach($bb3q as $ak=> $av)
                        {
                            if($ak == $_id)
                            {
                                unset($subjects[$zs3q][$u3n6][$ak]);
                            }
                        }
                    }
                }
            }
        }

        $i ++;
    }
} else
{
    $_subject_html=$this->renderPartial('application.modules.twitter.views.default._subjectsDropDownList',array(
        'selected'=>0,'bid'=>'_subjects_0','subjects'=>$subjects),true);
}
?>
<form id="_filterForm">
    <div id="block_1_1_block">
        <div id="block_1_1">
            <h3 class="top_title"><?php echo Yii::t('twitterModule.tweets','_title_main_parameters_accounts'); ?></h3>
            <table>
                 <tr>
                    <td class="info_param">
                        <?php echo Yii::t('twitterModule.tweets','_price_post'); ?>
                    </td>
                    <td class="param input">
                        <?php echo Yii::t('twitterModule.tweets','_from'); ?> <?php
                        echo CHtml::activeTextField($fmodel,'_price_post_ot',array(
                            'class'=>'posting_select_input'));
                        ?> <?php echo Yii::t('twitterModule.tweets','_to'); ?> <?php
                        echo CHtml::activeTextField($fmodel,'_price_post_do',array(
                            'class'=>'posting_select_input'));
                        ?>
                    </td>
                </tr>               
                <tr>
                    <td class="info_param">
                        <?php echo Yii::t('twitterModule.tweets','_itr'); ?> <i class="tooltip" title="<?php echo Yii::t('twitterModule.tweets','_itr_info'); ?>">?</i>
                    </td>
                    <td class="param input">
                        <?php echo Yii::t('twitterModule.tweets','_from'); ?> <?php
                        echo CHtml::activeTextField($fmodel,'ot_itr',array(
                            'class'=>'posting_select_input'));
                        ?> <?php echo Yii::t('twitterModule.tweets','_to'); ?> <?php
                        echo CHtml::activeTextField($fmodel,'do_itr',array(
                            'class'=>'posting_select_input'));
                        ?>
                    </td>
                </tr>
                 <tr>
                    <td class="info_param">
                        <?php echo Yii::t('twitterModule.tweets','_followers'); ?>
                    </td>
                    <td class="param input">
                        <?php echo Yii::t('twitterModule.tweets','_from'); ?> <?php
                        echo CHtml::activeTextField($fmodel,'followers_ot',array(
                            'class'=>'posting_select_input'));
                        ?> <?php echo Yii::t('twitterModule.tweets','_to'); ?> <?php
                        echo CHtml::activeTextField($fmodel,'followers_do',array(
                            'class'=>'posting_select_input'));
                        ?>
                    </td>
                </tr>               
                
                              <tr>
                    <td class="info_param">
                        <?php echo Yii::t('twitterModule.tweets','_ya_rang'); ?> <i class="tooltip" title="<?php echo Yii::t('twitterModule.tweets','_ya_rang_info'); ?>">?</i>
                    </td>
                    <td class="param input">
                        <?php echo Yii::t('twitterModule.tweets','_from'); ?> <?php
                        echo CHtml::activeTextField($fmodel,'_ya_r_ot',array(
                            'class'=>'posting_select_input'));
                        ?> <?php echo Yii::t('twitterModule.tweets','_to'); ?> <?php
                        echo CHtml::activeTextField($fmodel,'_ya_r_do',array(
                            'class'=>'posting_select_input'));
                        ?>
                    </td>
                </tr>
                <tr>
                    <td class="info_param">
                        <?php echo Yii::t('twitterModule.tweets','_googl_rang'); ?> <i class="tooltip" title="<?php echo Yii::t('twitterModule.tweets','_googl_rang_info'); ?>">?</i>
                    </td>
                    <td class="param input">
<?php echo Yii::t('twitterModule.tweets','_from'); ?> <?php
echo CHtml::activeTextField($fmodel,'_googl_rang_ot',array(
    'class'=>'posting_select_input'));
?> <?php echo Yii::t('twitterModule.tweets','_to'); ?> <?php
                        echo CHtml::activeTextField($fmodel,'_googl_rang_do',array(
                            'class'=>'posting_select_input'));
?>
                    </td>
                </tr>
                <tr>
                    <td class="info_param">
<?php echo Yii::t('twitterModule.tweets','_age_blog'); ?> <i class="tooltip" title="<?php echo Yii::t('twitterModule.tweets','_age_blog_info'); ?>">?</i>
                    </td>
                    <td class="param input">
                        <?php echo Yii::t('twitterModule.tweets','_from'); ?> <?php
                        echo CHtml::activeTextField($fmodel,'_age_blog_ot',array(
                            'class'=>'posting_select_input'));
                        ?> <?php echo Yii::t('twitterModule.tweets','_to'); ?> <?php
                        echo CHtml::activeTextField($fmodel,'_age_blog_do',array(
                            'class'=>'posting_select_input'));
                        ?>
                    </td>
                </tr>
                <tr>
                    <td class="info_param">
                        <?php echo Yii::t('twitterModule.tweets','_themes_blog'); ?>
                    </td>
                    <td class="param">
                        <div  id="_subjectsBox">
                        <?php echo $_subject_html; ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="info_param">
                        <?php echo Yii::t('twitterModule.tweets','_age_blogger'); ?> 
                    </td>
                    <td class="param">
<?php echo Html::activeDropDownList($fmodel,'_age',$ageData,array(
    'class'=>'styler'));
?>
                    </td>
                </tr>
                <tr>
                    <td class="info_param"><?php echo Yii::t('twitterModule.tweets','_blogs_tape'); ?> </td>
                    <td class="param">
                        <?php echo Html::activeDropDownList($fmodel,'tape',array(
                            0=>'Не указано',1=>'Человек и Бот',2=>'Человек',3=>'Бот')); ?> 
                    </td>
                </tr>               
                <tr>
                    <td class="info_param  radios">
<?php echo Yii::t('twitterModule.tweets','_floor_blogger'); ?>
                    </td>
                    <td class="param  radios">
<?php
echo Html::activeRadioButtonList($fmodel,'_gender',array('2'=>Yii::t('twitterModule.accounts','_twitterAccountSetting_men'),
    '1'=>Yii::t('twitterModule.accounts','_twitterAccountSetting_woman'),'0'=>Yii::t('main','_does_not_matter')),array(
    'separator'=>'&nbsp;'));
?>
                    </td>
                </tr>
            </table>
        </div>
        <div id="block_1_2"></div>
        <div id="block_1_3">
            <h3 class="top_title"><?php echo Yii::t('twitterModule.tweets','_title_more_settings'); ?></h3>
            <table>
                <tr>
                    <td class="info_param">
                        <?php echo Yii::t('twitterModule.tweets','_in_yandex'); ?>
                    </td>
                    <td class="param">
                        <?php
                        echo Html::activeDropDownList($fmodel,'_in_yandex',array(
                            'matter'=>Yii::t('main','_does_not_matter'),
                            'yes'=>Yii::t('main','_yes'),'no'=>Yii::t('main','_no')));
                        ?>
                    </td>
                </tr>
                <tr>
                    <td class="info_param">
                        <?php echo Yii::t('twitterModule.tweets','_language_blog'); ?>
                    </td>
                    <td class="param">
                        <?php
                        echo Html::activeDropDownList($fmodel,'_language_blog',Html::_getLang(false,true),array(
                            'class'=>'styler'));
                        ?>
                    </td>
                </tr>
                <tr>
                    <td class="info_param">
                        <?php echo Yii::t('twitterModule.tweets','_added_system'); ?> <i class="tooltip" title="<?php echo Yii::t('twitterModule.tweets','_added_system_info'); ?>">?</i>
                    </td>
                    <td class="param">
                        <?php
                        echo Html::activeDropDownList($fmodel,'_added_system',array(
                            'all'=>Yii::t('twitterModule.tweets','_added_system_param_1'),
                            'today'=>Yii::t('twitterModule.tweets','_added_system_param_2'),
                            'three_days'=>Yii::t('twitterModule.tweets','_added_system_param_3'),
                            'seven_days'=>Yii::t('twitterModule.tweets','_added_system_param_4'),
                            'month'=>Yii::t('twitterModule.tweets','_added_system_param_5')),array(
                            'class'=>'styler'));
                        ?>
                    </td>
                </tr>
                <tr>
                    <td class="info_param radios">
                        <?php echo Yii::t('twitterModule.tweets','_confirmation_applications'); ?> <i class="tooltip" title="<?php echo Yii::t('twitterModule.tweets','_confirmation_applications_info'); ?>">?</i>
                    </td>
                    <td class="param radios">
<?php echo Html::activeCheckBox($fmodel,'pType[manual]'); ?> <?php echo Yii::t('main','_manual'); ?> <?php echo Html::activeCheckBox($fmodel,'pType[auto]'); ?> <?php echo Yii::t('main','_auto'); ?>
                    </td>
                </tr>
                <tr>
                    <td class="info_param radios">
                        <?php echo Yii::t('twitterModule.tweets','_balance_spend'); ?> <i class="tooltip" title="<?php echo Yii::t('twitterModule.tweets','_balance_spend_info'); ?>">?</i>
                    </td>
                    <td class="param radios">
<?php
echo Html::activeRadioButtonList($fmodel,'pay_method',array(
    0=>Yii::t('twitterModule.tweets','_balance_spend_personal'),
    1=>Yii::t('twitterModule.tweets','_balance_spend_bonus')),array(
    'separator'=>'&nbsp;'));
?>
                    </td>
                </tr>           
            </table>
            <h3 class="top_title"><?php echo Yii::t('twitterModule.tweets','_title_personal_accounts_filtering'); ?></h3>
            <table>
                <tr>
                    <td>
                        <?php echo Html::activeCheckBox($fmodel,'_allow_adult'); ?> <?php echo Yii::t('twitterModule.tweets','_accounts_filtering_1'); ?>
                    </td>
                </tr>
                <tr>
                    <td>
<?php echo Html::activeCheckBox($fmodel,'_allow_profanity'); ?> <?php echo Yii::t('twitterModule.tweets','_accounts_filtering_2'); ?>
                    </td>
                </tr>
                <tr>
                    <td>
<?php echo Html::activeCheckBox($fmodel,'_show_only_white_list'); ?> <?php echo Yii::t('twitterModule.tweets','_accounts_filtering_4'); ?>
                    </td>
                </tr>
                <tr>
                    <td>
<?php echo Html::activeCheckBox($fmodel,'_not_black_list'); ?> <?php echo Yii::t('twitterModule.tweets','_accounts_filtering_5'); ?>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</form>