<div id="slide">
    <div class="center">
        <div id="persona"></div>
        <?php $this->widget('application.components.CMainStat'); ?>
        <div id="form_logged">
            <div class="form_logged_h">
                <a href="javascript:;" onclick="tabClick('tab1');" id="tab1" class="tabs active" style="margin-right: 35px;"><?php echo Yii::t('index','_login'); ?></a>
                <a href="javascript:;" onclick="tabClick('tab2');" id="tab2" class="tabs"><?php echo Yii::t('index','_registration'); ?></a>
            </div>
            <div id="con_tab1" class="tabs active">
                <div id="authContainer">
                    <form method="post" action="/accounts/auth">
                        <div style="display:none">
                            <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="_token" />
                        </div>
                        <input type="text" maxlength="55" id="Auth_email" name="Auth[email]" class="" placeholder="<?php echo Yii::t('index','_email_reg_place'); ?>">
                        <input type="password" id="Auth_password" name="Auth[password]" placeholder="<?php echo Yii::t('index','_password_reg_place'); ?>"><div class="checkbox_p">
                            <label>
                                <?php echo Html::checkBox('Auth[rememberMe]','',array(
                                    'uncheckValue'=>null)); ?> Запомнить меня
                            </label>
                            <div style="margin-top: 10px;">
                                <a href="/accounts/lost">Восстановить пароль</a>
                            </div>
                        </div>
                        <button type="submit" class="mAccounts button btn_blue" id="authButton"><?php echo Yii::t('index','_authButton'); ?></button>
                        <span style="display: block; display: block; padding-top: 15px; padding-left: 15px;"><a href="http://community.itwip.net/faq/quest12.html">Не получается войти ?</a></span>
                    </form>
                </div>
            </div>
            <div id="con_tab2" class="tabs">
                <div id="newContainer">
                    <form action="/accounts/new" method="post">
                        <div style="display:none">
                            <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="_token" />
                        </div>   
                        <input type="hidden" value="1" name="_step" id="_step" />   
                        <input autocomplete="off" placeholder="<?php echo Yii::t('index','_name_reg_place'); ?>" class="" name="newAccount[name]" id="newAccount_name" type="text" maxlength="255" />
                        <input autocomplete="off" placeholder="<?php echo Yii::t('index','_email_reg_place'); ?>" class="" name="newAccount[email]" id="newAccount_email" type="text" maxlength="55" />
                        <input autocomplete="off" placeholder="<?php echo Yii::t('index','_password_reg_place'); ?>" name="newAccount[password]" id="newAccount_password" type="password" />        
                        <div class="checkbox_p">
                            <label><?php
                                echo Html::checkBox('newAccount[agreed]','',array(
                                    'uncheckValue'=>null));
                                ?></label>
                            <?php
                            echo Yii::t('index','_agreed_reg_place',array(
                                '{link}'=>Yii::app()->createUrl('/regulations')));
                            ?>              
                        </div>
                        <button class="mAccounts button btn_blue" data-action="newContainer" data-send="/accounts/new" onclick="_iAction(this);
                                return false;"><?php echo Yii::t('index','_newButton'); ?></button>
                    </form>                
                </div>
            </div>		
        </div>
    </div>
</div>