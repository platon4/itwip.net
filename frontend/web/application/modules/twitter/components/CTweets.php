<?php

class CTweets {

    private $tweetsCount=0;
    private $tweets     =array();
    private $errors     =array();
    private $rules      =array('tweets'=>array());
    private $_status=0;
    protected $domen;
    protected $only_new;
    protected $identifier;
    
    protected $template='{url} {title}';
    protected $excludeUrl;
    protected $excludeWords;
    
    public function __construct()
    {
        $this->identifier=CHelper::generateID();
    }

    public function getCount()
    {
        return $this->tweetsCount;
    }

    public function process($data,$type,$only_new=0,$template=false)
    {
        $allow_procces =array('txt','xml');
        $this->only_new=$only_new;
        
        if($template)
        {
            $w=array();
            foreach(explode("\n",$template['words']) as $word)
            {
               $w[]=trim($word); 
            }
            
            $words=implode(",",$w);

            $this->template=(isset($template['url']) AND trim($template['url'])!='')?$template['url']:'{url} {title}';
            $this->excludeUrl=(isset($template['exclude']) AND trim($template['exclude'])!='')?explode("\n",$template['exclude']):array();
            $this->excludeWords=(isset($template['words']) AND trim($template['words']))?$words:'';           
        }
        
        if(in_array($type,$allow_procces))
        {
            switch($type)
            {
                case 'xml':
                    $this->pXml($data);
                    break;

                case 'txt':
                    $this->pText($data);
                    break;
            }
        } else
            $this->setError(Yii::t('twitterModule.tweets','_no_fail_proccess_support'),5);
    }

    public function getTweets()
    {
        return $this->tweets;
    }

    public function getidentifier()
    {
        return $this->identifier;
    }

    public function _filter($data)
    {
        $data=CHelper::toUnicode($data);

        return trim($data);
    }

    public function rules($rules=array())
    {
        $this->addRules($rules);
    }

    public function pText($data)
    {
        $tweets=explode("\n",str_replace("<br>","\n",$data));
        $c     =0;

        foreach($tweets as $tweet)
        {
            $tweetText=$this->_filter($tweet);

            if($tweetText)
            {
                $this->tweets[]=CHelper::toUnicode($tweetText);
                $c ++;
            }
        }

        $this->tweetsCount=$c;
    }

    public function pXml($data)
    {
        if(trim($data) != "" AND (preg_match('/<urlset[^>]*>(.*?)<\/urlset>/si',$data) OR preg_match('/<sitemapindex[^>]*>(.*?)<\/sitemapindex>/si',$data)))
        {
            if(!isset(Yii::app()->session['_psitemap']))
            {
                $xml   =new SimpleXMLElement($data);

                $values=array();
                $params=array(); 
                $domens=array();
                  
                $data_url=(count($xml->sitemap))?$xml->sitemap:$xml->url;
                $urls_count=count($data_url);             
 
                if($urls_count)
                {  
                    $urls=array();
                    foreach($data_url as $url)
                    {
                        $url=(string)$url->loc;
                        if(!in_array(CHelper::_getDomen($url),$domens))
                           $domens[]=CHelper::_getDomen($url);
                           
                        if(!in_array($url,$this->excludeUrl))
                          $urls[]=$url;                     
                    } 

                    $excludeList=array();
                    $count=0;
                     
                    if($this->only_new)
                    {
                        $_e=array();
                        $eprm=array(':owner_id'=>Yii::app()->user->id);
                        
                        foreach($domens as $_k=> $e)
                        {
                            $_e[]          =':d_'.$_k;
                            $eprm[':d_'.$_k]=$e;
                        }  
               
                        $sth=Yii::app()->db->createCommand("SELECT _hash FROM {{tw_tweets_sitemap}} WHERE owner_id=:owner_id AND _domen IN(".implode(", ",$_e).")")
                               ->queryAll(true,$eprm);
                               
                         foreach($sth as $st)
                         {
                            $excludeList[]=$st['_hash'];
                         }      
                    }
  
                    for($i=0;$i<=count($urls)-1;$i++)
                    {
                       $_url=$urls[$i]; 
                       $hash=md5($_url);   
                        
                        if(!in_array($hash,$excludeList))
                        {
                            $break++;
                            $count++;
                            $values[]="(:owner_id_{$i},:_domen_{$i},:_url_{$i},:_hash_{$i},:uid,:_template,:_excule_words)";
                            $params[':owner_id_'.$i]=Yii::app()->user->id;
                            $params[':_domen_'.$i]=CHelper::_getDomen($_url);
                            $params[':_url_'.$i]=$_url;
                            $params[':_hash_'.$i]=$hash;
                            $params[':uid']=$this->identifier;
                            
                            $params[':_template']=$this->template;
                            $params[':_excule_words']=$this->excludeWords;

                            if(($break>=300) OR ($i==count($urls)-1))
                            {
                                Yii::app()->db->createCommand("INSERT INTO {{tw_tweets_sitemap}} (owner_id, _domen, _url, _hash, _uid,_template,_excule_words) VALUES ".implode(', ',$values))->execute($params);
                                $values=array();
                                $params=array();  
                                $break=0;                        
                            }                               
                        }                        
                    } 
 
                } else
                    $this->setError(Yii::t('internal','_error_collection_sitemap_url'),18);

                if($urls_count)
                {
                    if($count)
                    {
                        $this->_status    =1;
                        $this->tweetsCount=$count;
                        
                        if(!$this->only_new AND count($domens))
                        {
                            $_d=array();
                            $dprm=array(':owner_id'=>Yii::app()->user->id,':uid'=>$this->identifier);
                            
                            foreach($domens as $k=> $d)
                            {
                                $_d[]          =':d_'.$k;
                                $dprm[':d_'.$k]=$d;
                            }
                       
                            Yii::app()->db->createCommand("DELETE FROM {{tw_tweets_sitemap}} WHERE owner_id=:owner_id AND _uid!=:uid AND _domen IN(".implode(", ",$_d).")")->execute($dprm);                           
                        }
                    } else
                        $this->setError(Yii::t('twitterModule.tweets','_error_parse_sitemap_no_new_link'),546);
                } else
                    $this->setError(Yii::t('twitterModule.tweets','_error_parse_sitemap'),545);
            } else
                $this->setError(Yii::t('twitterModule.tweets','_sitemap_in_proccess'),543);
        } else
            $this->setError(Yii::t('twitterModule.tweets','_error_parse_sitemap'),7);
    }

    public function collection($action='')
    {
        if($action == "start")
        {
            CHelper::wget("http://itwip.net/twitter/tweets/tcollection?uid=".$this->identifier);
        } else
            return ($this->_status == 1)?true:false;
    }

    public function validate()
    {
        if(!count($this->errors))
        {
            foreach($this->rules as $m=> $p)
            {
                $methodName='_check'.$m;

                if(method_exists($this,$methodName))
                {
                    $this->$methodName($p);
                } else
                    throw new CHttpException('500','Unknown "'.$m.'" method, in the "'.__CLASS__.'" class.');
            }

            return (count($this->errors))?false:true;
        } else
            return false;
    }

    public function getError($key)
    {
        return (isset($this->errors[0][$key]))?$this->errors[0][$key]:null;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * protected functions
     */
    protected function addRules($rules)
    {
        if(is_array($rules))
        {
            foreach($rules as $rule)
            {
                if(isset($rule[0]))
                {
                    $ruleName=$rule[0];
                    unset($rule[0]);
                    $prm     =array();

                    foreach($rule as $k=> $v)
                    {
                        if(is_array($v))
                        {
                            foreach($v as $_k=> $_v)
                                $prm[$_k]=$_v;
                        } else
                            $prm[$k]=$v;
                    }

                    $this->rules[$ruleName]=$prm;
                } else
                    throw new CHttpException('500','Unknown "'.$rule[0].'" rule, in the "'.__CLASS__.'" class.');
            }
        }
    }

    protected function _checkurl($prm)
    {
        $this->domen=CHelper::_getDomen($prm[0]);
    }

    protected function _checktweets($p=array())
    {
        if(!$this->getCount())
        {
            $this->setError(Yii::t('twitterModule.tweets','_tweets_error_collection'),3);
        }
    }

    protected function setError($txt,$code=0)
    {
        if(!(int)$code)
            $code=0;

        $this->errors[]=array('error'=>$txt,'code'=>$code);
    }
}
