<?php

class Breadcrumbs extends CWidget {

    /**
     * @var string the tag name for the breadcrumbs container tag. Defaults to 'div'.
     */
    public $tagName    ='div';

    /**
     * @var array the HTML attributes for the breadcrumbs container tag.
     */
    public $htmlOptions=array('class'=>'breadcrumbs');
    /**
     * @var string the separator between links in the breadcrumbs. Defaults to ' &raquo; '.
     */
    public $separator=' <i class="fa fa-caret-right"></i> ';
    public $blockList=array('index','default','main');
    public $data=array();
    protected $bread_list=array();

    public function run()
    {
        $this->bread_list[]=array(Yii::t('breadcrumbs','_home'),Yii::app()->homeUrl);
        $linkArr=array();
        
        if(!count($this->data))
            return false;
        
        $b=0;   
        foreach($this->data as $k=>$list)
        {
            if(is_array($list[$b]))
            {
                foreach($list as $vlist)
                {
                   $this->bread_list[]=array($vlist[0],$vlist[1]);
                } 
            }
            else {
                $this->bread_list[]=array($list[0],$list[1]);
            }
            
            $b++;
        }

        for($j=0; $j <= count($this->bread_list) - 1; $j ++)
        {
            $linkArr[]=($j == (count($this->bread_list) - 1) OR trim($this->bread_list[$j][1]) =='')?Yii::t('breadcrumbs',$this->bread_list[$j][0]):Html::link(Yii::t('breadcrumbs',$this->bread_list[$j][0]),$this->bread_list[$j][1]);
        }

        echo CHtml::openTag($this->tagName,$this->htmlOptions)."\n";
        echo implode($this->separator,$linkArr);
        echo CHtml::closeTag($this->tagName);
    }

}
