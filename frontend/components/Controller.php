<?php

/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
    /**
     * @var array context menu items. This property will be assigned to {@link CMenu::items}.
     */
    public $menu = array();

    /**
     * @var array the breadcrumbs of the current page. The value of this property will
     * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
     * for more details on how to specify this property.
     */
    public $breadcrumbs = array();
    public $metaDescription;
    public $metaKeywords;
    public $moneyType = 'ru';
    public $activeMenu = 'main';

    public function init()
    {
        if(!Yii::app()->user->isGuest AND Yii::app()->user->_get('status') == 2)
            $this->_message(Yii::t('main', 'account_is_suspended'));

        $_url = Yii::app()->request->baseUrl;

        if(isset($_GET['_r']) AND CHelper::validReferralCode($_GET['_r']) AND Yii::app()->user->isGuest)
            Yii::app()->session['_referral_code'] = $_GET['_r'];

        $_f = Yii::app()->clientScript;
        $_f->scriptMap = ['jquery.js' => 'http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js'];
        $_f->registerCoreScript('jquery');
        $_f->registerScriptFile($_url . '/js/jquery.custom.min.js');
        $_f->registerScriptFile($_url . '/js/www-lang-core.js');
        $_f->registerScriptFile($_url . '/js/www-jcore.js');

        if(Yii::app()->user->isGuest) {
            $_f->registerCssFile($_url . '/css/main.css');
            $_f->registerCssFile($_url . '/css/index.css');

            $this->layout = '//layouts/unauthorized';
        } else {
            $_f->registerScriptFile($_url . '/js/www-core-z6q33.js');
            $_f->registerCssFile($_url . '/css/main.css');
            $_f->registerCssFile($_url . '/css/internal.css');
            $_f->registerCssFile($_url . '/css/elements.css');

            $this->layout = '//layouts/authorized';
        }

        $_f->registerScript('_sparams', '	var it = { _host: \'' . Yii::app()->homeUrl . '\', _token: \'' . Yii::app()->request->csrfToken . '\', _lang: \'ru\'}', CClientScript::POS_BEGIN);
    }

    public function _message($text, $title = '', $link = '', $is_html = FALSE)
    {
        if(CHelper::isEmpty($title, TRUE))
            $title = Yii::t('main', '_error');

        if(Yii::app()->request->isAjaxRequest)
            echo json_encode(array('code' => 502, 'message' => $text));
        else
            $this->render('application.views.main.info', ['title' => $title, 'message' => $text, 'link' => $link, 'is_html' => $is_html]);

        Yii::app()->end();
    }
}
