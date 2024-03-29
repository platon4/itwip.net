<?php

class IPValidator extends CValidator
{

	public $v4pattern = '/^(([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/';

	/* Thanks to Rich Brown for ipv6 regex code (http://forums.dartware.com/viewtopic.php?t=452) */
	public $v6pattern = '/^\s*((([0-9A-Fa-f]{1,4}:){7}([0-9A-Fa-f]{1,4}|:))|(([0-9A-Fa-f]{1,4}:){6}(:[0-9A-Fa-f]{1,4}|((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})|:))|(([0-9A-Fa-f]{1,4}:){5}(((:[0-9A-Fa-f]{1,4}){1,2})|:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})|:))|(([0-9A-Fa-f]{1,4}:){4}(((:[0-9A-Fa-f]{1,4}){1,3})|((:[0-9A-Fa-f]{1,4})?:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){3}(((:[0-9A-Fa-f]{1,4}){1,4})|((:[0-9A-Fa-f]{1,4}){0,2}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){2}(((:[0-9A-Fa-f]{1,4}){1,5})|((:[0-9A-Fa-f]{1,4}){0,3}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){1}(((:[0-9A-Fa-f]{1,4}){1,6})|((:[0-9A-Fa-f]{1,4}){0,4}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(:(((:[0-9A-Fa-f]{1,4}){1,7})|((:[0-9A-Fa-f]{1,4}){0,5}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:)))(%.+)?\s*$/';

	public $allowEmpty = true;

	public $version = 'v4';

	protected function validateAttribute($object,$attribute)
	{
		$value=$object->$attribute;
		if($this->allowEmpty && ($value===null || $value===''))
			return;

		switch(strtolower($this->version))
		{
			case "v6":
					$valid=preg_match($this->v6pattern, $value);
				break;
				
			case "v4":
					$valid=preg_match($this->v4pattern,$value);
				break;
				
			case "subnetwork":
			
					$this_ip_split = explode( ".", $value );
					$ip_check_valid=0;
					
					for($i = 0; $i < 4; $i ++) {
						if(($this_ip_split[$i] == '*') OR (is_numeric($this_ip_split[$i]) AND $this_ip_split[$i] >= 0 AND $this_ip_split[$i] <= 255)) {
							$ip_check_valid += 1;
						}
					}
					
					$valid=($ip_check_valid==4)?true:false;
				break;
		}

		if(!$valid)
		{
			$message=$this->message!==null?$this->message:Yii::t('yii','{attribute} is not a valid IP' . $this->version . ' address.');
			$this->addError($object,$attribute,$message);
		}
	}
}

