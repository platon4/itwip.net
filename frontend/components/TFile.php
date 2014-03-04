<?php

class TFile {

    const TMP_DIR='tmp';

    private static $_file=array();
    private $file        =array();
    private $is_url      =false;
    private $errors      =array();
    private $url;
    private $filePatch;
    private $fileName;
    private $fileType    =false;
    protected $rules     =array();

    public function __construct($file)
    {
        $this->init($file);
    }

    public static function file($sfile,$is_url=false,$className=__CLASS__)
    {
        if(isset(self::$_file[$className]))
            return self::$_file[$className];
        else
        {
            $file=self::$_file[$className]=new $className($sfile,$is_url);
            return $file;
        }
    }

    public function init($file)
    {
        if(preg_match('/^http|https:\/\/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)/i',$file))
        {
            $this->url   =$file;
            $this->is_url=true;
        } elseif(isset($_FILES[$file]))
        {
            $this->file     =$_FILES[$file];
            $this->fileName =$this->file['name'];
            $this->filePatch=$_FILES[$file]['tmp_name'];
        } else
            $this->setError(Yii::t('internal','_tfile_unknow_action'),3);
    }

    public function getType()
    {
        if($this->isUrl())
        {
            $type=$this->url_mime_type($this->url);
        } else
        {
            if(!$this->fileType)
            {
                $farr=explode(".",$this->file['name']);
                $type=end($farr);
            } else
                $type=$this->fileType;
        }

        return $type;
    }

    public function getSize()
    {
        return ($this->isUrl())?$this->fsize($this->url):@filesize($this->getFilePatch());
    }

    public function getFile()
    {
        if($this->isUrl())
        {
            $data=CHelper::_getURL($this->url);

            if($data['code'] == 200)
            {
                return $data['response'];
            } else
                $this->setError($data['error'],$data['code']);
        }
        else
        {
            $data=@file_get_contents($this->getFilePatch());

            if($data)
            {
                return $data;
            } else
                $this->setError(Yii::t('internal','_error_get_file_contents'),25);
        }
    }

    public function getName()
    {
        if($this->is_url)
        {
            $file_name=end(explode("/",$this->url));
        } else
            $file_name=$this->fileName;

        $z3a6q=explode(".",$file_name);
        unset($z3a6q[count($z3a6q) - 1]);

        $name=implode(".",$z3a6q);

        return $name;
    }

    public function getFullName($url=false)
    {
        return ($url AND $this->isUrl())?$this->url:$this->getName().'.'.$this->getType();
    }

    public function isUrl()
    {
        return ($this->is_url)?true:false;
    }

    public function save($dir,$isFull=false)
    {
        if($isFull)
        {
            $dir_save=$dir;
        } else
            $dir_save=HOME_DIR.DIRECTORY_SEPARATOR.$dir;

        if(move_uploaded_file($this->filePatch,$dir_save.DIRECTORY_SEPARATOR.$this->saveFileName()))
        {
            return 1;
        } else
            return 0;
    }

    public function saveFileName($name=false)
    {
        if(!$name)
        {
            if(function_exists('openssl_random_pseudo_bytes'))
            {

                $stronghash=md5(openssl_random_pseudo_bytes(15));
            } else
                $stronghash=md5(uniqid(mt_rand(),TRUE));

            $salt=sha1(str_shuffle("abchefghjkmnpqrstuvwxyz0123456789").$stronghash);
            $hash='';

            for($i=0; $i < rand(15,30); $i ++)
            {
                $hash .= $salt{mt_rand(0,39)};
            }
            $fileName      =$hash.'.'.$this->getType();
            $this->fileName=$fileName;
            return $fileName;
        } else
        {
            $fileName      =$name.'.'.$this->getType();
            $this->fileName=$fileName;
            return $fileName;
        }
    }

    public function abort()
    {
        if(!$this->is_url)
        {
            unlink($this->getTmpFile());
        }
    }

    public function getError($key)
    {
        return (isset($this->errors[0][$key]))?$this->errors[0][$key]:null;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function fsize($path)
    {
        $fp =@fopen($path,"r");
        $inf=stream_get_meta_data($fp);
        fclose($fp);

        foreach($inf["wrapper_data"] as $v)
            if(stristr($v,"content-length"))
            {
                $v=explode(":",$v);
                return trim($v[1]);
            }
    }

    public function rules($rules=array())
    {
        $this->addRules($rules);
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
                        if(!(int)$k)
                        {
                            $prm[$k]=$v;
                        } else
                            $prm[]=$v;
                    }

                    $this->rules[$ruleName]=$prm;
                } else
                    throw new CHttpException('500','Unknown "'.$rule[0].'" rule, in the "'.__CLASS__.'" class.');
            }
        }
    }

    protected function getTmpFile()
    {
        return Yii::app()->getRuntimePath().DIRECTORY_SEPARATOR.self::TMP_DIR.DIRECTORY_SEPARATOR.$this->getName();
    }

    protected function _checkcurrent($c)
    {
        if(is_array($c) AND isset($c[0]))
        {
            $c=$c[0];
        } else
            throw new CHttpException('500','Check current error.');

        if(!in_array($this->getType(),$c))
        {
            $this->setError(Yii::t('internal','_file_type_selected',array('{type}'=>Html::encode(implode(", ",$c)))),24);
            return true;
        } else
            return true;
    }

    protected function _checksize($prm)
    {
        if(is_array($prm))
        {
            $required=array('max');

            foreach($required as $key)
            {
                if(!array_key_exists($key,$prm))
                {
                    throw new CHttpException('500','There is no parameter "'.$key.'"  in the method "size", in the "'.__CLASS__.'" class.');
                }
            }

            $maxSize=$this->cSize($prm['max']);

            if($maxSize['size'] < $this->getSize())
            {
                $this->setError((isset($prm['messageToBig']))?$prm['messageToBig']:Yii::t('internal','_file_is_big',array(
                                    '{size}'=>$maxSize['osize'].' '.$maxSize['format'])),22);
            }

            if(isset($prm['min']) AND intval($prm['min']))
            {
                $minSize=$this->cSize($prm['min']);
                if($this->getSize() < $minSize['size'])
                {
                    $this->setError((isset($prm['messageToSmall']))?$prm['messageToSmall']:Yii::t('internal','_file_is_small',array(
                                        '{size}'=>$minSize['osize'].' '.$minSize['format'])),22);
                }
            }
        } else
            throw new CHttpException('500','There is no parameter list  in the method "size", in the "'.__CLASS__.'" class.');
    }

    protected function cSize($prm)
    {
        if(is_array($prm))
        {
            if(count($prm) AND isset($prm[0]))
            {
                $s=(int)$prm[0];

                if(isset($prm[1]))
                {
                    $d=$prm[1];
                }
            } else
                throw new CHttpException('500','There is no parameter in the method "cSize", in the "'.__CLASS__.'" class.');
        } else
            $s=(int)$prm;

        switch($d)
        {
            case "mb":
                $size=$s * 1024 * 1024;
                break;

            default:
                $d   ="kb";
                $size=$s * 1024;
        }

        return array('osize'=>$s,'size'=>$size,'format'=>strtoupper($d));
    }

    protected function _checkformats($formats)
    {
        if(is_array($formats) AND isset($formats[0]))
        {
            $formats=$formats[0];
        } else
            throw new CHttpException('500','Check formats error.');

        if(!in_array($this->getType(),$formats))
        {
            $this->setError(Yii::t('internal','_file_type_not_allowed',array('{format}'=>Yii::t('internal','_formats',array(
                            count($formats),'{types}'=>implode(", ",$formats))))),21);
            return false;
        } else
            return true;
    }

    protected function _checkblacklist($prm)
    {
        if(isset($prm[0]))
        {
            $blacklist=explode(',',$prm[0]);
            foreach($blacklist as $item)
            {
                if(preg_match("/$item\$/i",$this->getFullname()))
                {
                    $this->setError(Yii::t('internal','_file_blacklist'),25);
                    break;
                }
            }
        } else
            $this->setError(Yii::t('internal','_blacklist_no_set'),23);
    }

    protected function _checkimage($prm)
    {
        if(isset($prm['types']))
        {
            $formats       =explode(',',$prm['types']);
            $mimeImage     =array('image/gif'=>'gif','image/jpg'=>'jpg','image/png'=>'png',
                'image/jpeg'=>'jpeg');
            $mimeImageCheck=array();

            foreach($mimeImage as $mimeType=> $format)
            {
                $mimeImageCheck[]=$mimeType;
            }

            $image=@getimagesize($this->getFilePatch());

            if(!in_array($image['mime'],$mimeImageCheck))
            {
                $this->setError(Yii::t('internal','_file_type_not_allowed',array(
                            '{format}'=>Yii::t('internal','_formats',array(count($formats),
                                '{types}'=>implode(", ",$formats))))),21);
            } else
                $this->fileType=$mimeImage[$image['mime']];
        } else
            $this->setError(Yii::t('internal','_image_no_types_allowed'),23);
    }

    protected function setError($txt,$code=0)
    {
        if(!(int)$code)
            $code=0;

        $this->errors[]=array('error'=>$txt,'code'=>$code);
    }

    protected function getFilePatch()
    {
        return $this->filePatch;
    }

    protected function url_mime_type($url)
    {
        $ch=curl_init($url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_FOLLOWLOCATION,false);
        curl_setopt($ch,CURLOPT_HEADER,1);
        curl_setopt($ch,CURLOPT_NOBODY,1);
        curl_exec($ch);
        return $this->returnNormalFormat(curl_getinfo($ch,CURLINFO_CONTENT_TYPE));
    }

    protected function returnNormalFormat($f)
    {
        $frm=explode(";",$f);

        $mimeData=array('text/plain'=>'txt','application/xml'=>'xml','text/xml'=>'xml','text/html'=>'xml');

        if(isset($mimeData[$frm[0]]))
        {
            return $mimeData[$frm[0]];
        } else
            return $frm[0];
    }

}
