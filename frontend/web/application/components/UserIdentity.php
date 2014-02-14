<?php

class UserIdentity extends CUserIdentity {

    private $_id;
    private $data=false;
    
    public function authenticate()
    {
        $record=$this->_getData();

        if($record === null)
            $this->errorCode=self::ERROR_USERNAME_INVALID;
        else if($record->password !== CHelper::_md5($this->password))
            $this->errorCode=self::ERROR_PASSWORD_INVALID;
        else if(trim($record->_allow_ip) != '' AND !$this->allowedIP($record->_allow_ip))
        {
            $this->errorCode=3;
        } else
        {
            $this->_id      =$record->id;
            $this->setState('name',$record->name);
            $this->errorCode=self::ERROR_NONE;
        }
        
        return $this->errorCode == self::ERROR_NONE;
    }
    
    public function _getData()
    {
        if($this->data===false)
        {
            $this->data=User::model()->find('LOWER(email)=?',array(strtolower($this->username)));
        }
        
        return $this->data;
    }
    public function getId()
    {
        return $this->_id;
    }

    public function allowedIP($ip)
    {
        $_IP=CHelper::_getIP();

        $blockip=FALSE;

        $ip_arr=rtrim($ip);

        $ip_check_matches=0;
        $db_ip_split     =explode(".",$ip_arr);
        $this_ip_split   =explode(".",$_IP);

        for($i_i=0; $i_i < 4; $i_i ++)
        {
            if($this_ip_split[$i_i] == $db_ip_split[$i_i] or $db_ip_split[$i_i] == '*')
            {
                $ip_check_matches += 1;
            }
        }

        if($ip_check_matches == 4)
        {
            $blockip=true;
        }

        return $blockip;
    }

}
