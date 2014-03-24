<?php
$this->pageTitle = Yii::app()->name . ' - ' . Yii::t('main', '_twitterList_Title');
$this->metaDescription = Yii::t('main', '_twitterList_Description');

$this->breadcrumbs[] = array(
    0 => array(Yii::t('breadcrumbs', '_twitter'), '/twitter'),
    1 => array(Yii::t('breadcrumbs', '_tw_accounts'), '/twitter/accounts')
);

$_count = count($list);
?>
<?php if(Yii::app()->user->hasFlash('accountsMessagesSuccess')) { ?>
    <div id="_flashDialog" style="margin-bottom: 15px;">
        <div class="line_info ok">
            <div class="errorMessage"><?= Yii::app()->user->getFlash('accountsMessagesSuccess'); ?></div>
        </div>
    </div>
    <script>
        setTimeout(function ction() {
                $('#_flashDialog').fadeOut();
            }
            , 5000);

    </script>
<?php } ?>
<?php if(Yii::app()->user->hasFlash('accountsMessages')) { ?>
    <div id="_flashDialog" style="margin-bottom: 15px;">
        <div class="line_info alert">
            <div class="errorMessage"><?= Yii::app()->user->getFlash('accountsMessages'); ?></div>
        </div>
    </div>
    <script>
        setTimeout(function ction() {
                $('#_flashDialog').fadeOut();
            }
            , 4000);

    </script>
<?php } ?>
<div class="block twitterAccountList">
    <div class="block_title"><div class="block_title_inset"><i class="fa fa-twitter"></i> <h5><?php echo Yii::t('twitterModule.accounts','_twitterAccountList_title'); ?></h5></div></div>
    <div class="block_content">
        <div id="block_1">
            <div class="line_title_noopen no_border_bottom">
                <span id="_stats">
                <b>Всего:</b> <a href="" class="here "><?php echo $all_accounts_count; ?></a> | <i class="fa fa-filter"></i>
                <a href="" class="here">в работе: <?php echo $all_accounts_in_work; ?></a> /
                <a href="" class="here">отключены: <?php echo $all_accounts_moderation; ?></a> /
                <a href="" class="here select">на модерации: <?php echo $all_accounts_moderation; ?></a> /
                <a href="" class="here">требуют обновления ключа: <?php echo $all_accounts_moderation; ?></a>
                </span>
                <span style="float: right; margin-top: -8px; margin-right: 5px;" class="block group_input search"><input id="setQuery" type="text" placeholder="<?php echo Yii::t('twitterModule.accounts','_twitterAccountList_accountSearch'); ?>" onkeyup="Accounts._getFromQuery('setQuery', '_searchButton');" /><button class="button icon" onclick="Accounts._getFromQuery('setQuery', '_searchButton');"><i id="_searchButton" class="fa fa-search"></i></button></span>
            </div>
        </div>
        <div id="block_2">
            <div class="table_head">
                <div class="table_head_inside">
                    <table>
                        <tr>
                            <td class="account"><a href="javascript:;" data-order="last" onclick="Accounts._setOrder(this);
                                                          return false;"><?php echo Yii::t('twitterModule.accounts','_twitterAccountList_tableAccount'); ?> <i class="fa fa-caret-down"></i></a></td>
                            <td class="status"><a href="javascript:;" data-order="status" onclick="Accounts._setOrder(this);
                                                          return false;"><?php echo Yii::t('twitterModule.accounts','_twitterAccountList_tableStatus'); ?> <i class="fa fa-caret-down"></i></a>                                                           </td>
                            <td class="level"><a href="javascript:;" data-order="itr" onclick="Accounts._setOrder(this);
                                                          return false;"><?php echo Yii::t('twitterModule.accounts','_twitterAccountList_tableLevel'); ?> <i class="fa fa-caret-down"></i></a></td>
                            <td class="index"> <span title="<?php echo Yii::t('twitterModule.accounts','_twitterAccountList_tableIndex_title'); ?>"><?php echo Yii::t('twitterModule.accounts','_twitterAccountList_tableIndex'); ?></span>                                                          </td>
                            <td class="application"><a href="javascript:;" data-order="order" onclick="Accounts._setOrder(this);
                                                          return false;"><?php echo Yii::t('twitterModule.accounts','_twitterAccountList_tableApplication'); ?> <i class="fa fa-caret-down"></i></a></td>
                            <td class="posted"><a href="javascript:;" data-order="posted" onclick="Accounts._setOrder(this);
                                                          return false;"><?php echo Yii::t('twitterModule.accounts','_twitterAccountList_tablePosted'); ?> <i class="fa fa-caret-down"></i></a></td>
                            <td class="no_padding earneds">
                                <table>
                                    <tr>
                                        <td colspan="3" class="earned"><?php echo Yii::t('twitterModule.accounts','_twitterAccountList_tableEarneds'); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="today">         <a href="javascript:;" data-order="today" onclick="Accounts._setOrder(this);
                                                                                  return false;"><?php echo Yii::t('twitterModule.accounts','_twitterAccountList_tableToday'); ?> <i class="fa fa-caret-down"></i></a>  </td>
                                        <td class="last">          <a href="javascript:;" data-order="yesterday" onclick="Accounts._setOrder(this);
                                                                                  return false;"><?php echo Yii::t('twitterModule.accounts','_twitterAccountList_tableLast'); ?> <i class="fa fa-caret-down"></i></a>    </td>
                                        <td class="no_border only"><a href="javascript:;" data-order="all" onclick="Accounts._setOrder(this);
                                                                                  return false;"><?php echo Yii::t('twitterModule.accounts','_twitterAccountList_tableOnly'); ?> <i class="fa fa-caret-down"></i></a>      </td>
                                    </tr>
                                </table>
                            </td>
                            <td class="icons"></td>
                            <td class="select"><?php echo Html::checkBox('inset'); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            <div id="_listTwAccounts" class="acconts_list _cHide"><?php $this->renderPartial('_indexList',['list'=>$list,'_count'=>$_count]); ?></div>
            <div class="_loading" style="display: none; text-align: center; padding-top: 10px;"><img src="/i/loads.gif"></div>
            <div class="table_bottom">
                <div class="table_bottom_inside">
                    <div class="page_nav_page">
                        <div id="pagesList" class="_cHide">
                            <?php $this->renderPartial("_pages",array('pages'=>$pages)); ?>
                        </div>
                        <div class="_loading" style="display: none;"><img src="/i/loads.gif"></div>
                    </div>
                        <?php if($_count)
                        { ?>
                        <div class="page_nav_how">
                                <?php echo Yii::t('twitterModule.accounts','_pageNavHow'); ?>
                            <select name="shoOnPage" class="styler" onchange="Accounts._setLimit(this); return false;">
                                <?php foreach($limitList as $option)
                                { ?>
                                    <?php
                                    if(isset(Yii::app()->session['_accountsLimit']) AND Yii::app()->session['_accountsLimit'] == $option['value'])
                                    {
                                        $htmlOption=array('value'=>$option['value'],
                                            'selected'=>'selected');
                                    } else
                                    {
                                        $htmlOption=array('value'=>$option['value']);
                                    }
                                    echo Html::tag('option',$htmlOption,($option['title'] == "_all")?Yii::t('twitterModule.accounts','_pageNavHowAll'):$option['title']);
                                    ?>
                        <?php } ?>
                            </select>
                            <button class="button icon" onclick="Dialog.open('test',{'content':'nlabla'})"><i class="fa fa-wrench"></i></button>
                        </div>
                        <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>