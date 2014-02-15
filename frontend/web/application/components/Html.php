<?php

class Html extends CHtml
{
	private static $_modelNameConverter;

	public static function encode($text, $type = '')
	{
		if($type === 'javascript') {
			$text = str_replace("\n", '\n', str_replace('"', '\"', addcslashes(str_replace("\r", '', (string)$text), "\0..\37'\\")));;

			return htmlspecialchars($text, ENT_QUOTES, Yii::app()->charset, true);
		}
		else
			return parent::encode($text);
	}

	public static function json($data, $end = true)
	{
		echo json_encode($data);

		if($end === true) {
			Yii::app()->end();
		}
	}

	public static function staticFileUrl($file)
	{
		return $file;
	}

	public static function _date($format, $stamp = '')
	{
		if(!$stamp)
			$stamp = time();

		$data = Yii::t('main', '_dates');

		return strtr(date($format, $stamp), $data);
	}

	public static function twStatus($s, $notice = '')
	{
		$status = array(
			0 => Yii::t('main', '_status_0'), //Модерация
			1 => Yii::t('main', '_status_1'), //Работает
			2 => Yii::t('main', '_status_2', array('{text}' => $notice)), //недопушен
			3 => Yii::t('main', '_status_3'), //забанен
			4 => Yii::t('main', '_status_4'), //нет доступа
			5 => Yii::t('main', '_status_5'), //
			6 => Yii::t('main', '_status_6'), //Не соотвествует требованьям
			7 => Yii::t('main', '_status_7'), //Отключен
		);

		return (isset($status[$s])) ? $status[$s] : Yii::t('main', '_status_undefined');
	}

	public static function ajaxButton($text, $url, $ajaxOptions = array(), $htmlOptions = array())
	{
		$ajaxOptions['url']  = $url;
		$htmlOptions['ajax'] = $ajaxOptions;
		self::clientChange('click', $htmlOptions);

		return self::tag('button', $htmlOptions, $text);
	}

	public static function activeRadioButtonList($model, $attribute, $data, $htmlOptions = array())
	{
		self::resolveNameID($model, $attribute, $htmlOptions);
		$selection = self::resolveValue($model, $attribute);
		if($model->hasErrors($attribute))
			self::addErrorCss($htmlOptions);
		$name = $htmlOptions['name'];
		unset($htmlOptions['name']);

		if(array_key_exists('uncheckValue', $htmlOptions)) {
			$uncheck = $htmlOptions['uncheckValue'];
			unset($htmlOptions['uncheckValue']);
		}
		else
			$uncheck = '';

		$hiddenOptions = isset($htmlOptions['id']) ? array('id' => self::ID_PREFIX . $htmlOptions['id']) : array(
			'id' => false);
		$hidden        = $uncheck !== NULL ? self::hiddenField($name, $uncheck, $hiddenOptions) : '';

		return $hidden . self::radioButtonList($name, $selection, $data, $htmlOptions);
	}

	public static function activeRadioButton($model, $attribute, $htmlOptions = array())
	{
		self::resolveNameID($model, $attribute, $htmlOptions);
		if(!isset($htmlOptions['value']))
			$htmlOptions['value'] = 1;
		if(!isset($htmlOptions['checked']) && self::resolveValue($model, $attribute) == $htmlOptions['value'])
			$htmlOptions['checked'] = 'checked';
		self::clientChange('click', $htmlOptions);

		if(array_key_exists('uncheckValue', $htmlOptions)) {
			$uncheck = $htmlOptions['uncheckValue'];
			unset($htmlOptions['uncheckValue']);
		}
		else
			$uncheck = '0';

		$hiddenOptions = isset($htmlOptions['id']) ? array('id' => self::ID_PREFIX . $htmlOptions['id']) : array(
			'id' => false);
		$hidden        = $uncheck !== NULL ? self::hiddenField($htmlOptions['name'], $uncheck, $hiddenOptions) : '';

		if(array_key_exists('style', $htmlOptions)) {
			$htmlOptions['style'] = $htmlOptions['style'] . ' position: absolute; left: -9999px;';
		}
		else
			$htmlOptions['style'] = 'position: absolute; left: -9999px;';

		if(isset($htmlOptions['checked']) && $htmlOptions['checked'] == 'checked')
			$class = "radio styler checked";
		else
			$class = "radio styler";

		if(isset($htmlOptions['disabled']) AND $htmlOptions['disabled'] == 'disabled')
			$class .= ' disabled';

		// add a hidden field so that if the radio button is not selected, it still submits a value
		return $hidden . self::activeInputField('radio', $model, $attribute, $htmlOptions) . self::openTag('span', array(
			'onclick' => '_radioBox(this); return false;', 'class' => $class,
			'style'   => 'display: inline-block')) . self::tag('span', array(), '', true) . self::closeTag('span');
	}

	public static function radioButtonList($name, $select, $data, $htmlOptions = array())
	{
		$template  = isset($htmlOptions['template']) ? $htmlOptions['template'] : '{input} {label}';
		$separator = isset($htmlOptions['separator']) ? $htmlOptions['separator'] : "<br/>\n";
		$container = isset($htmlOptions['container']) ? $htmlOptions['container'] : 'span';
		unset($htmlOptions['template'], $htmlOptions['separator'], $htmlOptions['container']);

		$labelOptions = isset($htmlOptions['labelOptions']) ? $htmlOptions['labelOptions'] : array();
		unset($htmlOptions['labelOptions']);

		if(isset($htmlOptions['empty'])) {
			if(!is_array($htmlOptions['empty']))
				$htmlOptions['empty'] = array('' => $htmlOptions['empty']);
			$data = array_merge($htmlOptions['empty'], $data);
			unset($htmlOptions['empty']);
		}

		$items  = array();
		$baseID = isset($htmlOptions['baseID']) ? $htmlOptions['baseID'] : self::getIdByName($name);
		unset($htmlOptions['baseID']);
		$id = 0;
		foreach($data as $value => $labelTitle) {
			$checked              = !strcmp($value, $select);
			$htmlOptions['value'] = $value;
			$htmlOptions['id']    = $baseID . '_' . $id++;
			$option               = self::radioButton($name, $checked, $htmlOptions);
			$beginLabel           = self::openTag('label', $labelOptions);
			$label                = self::label($labelTitle, $htmlOptions['id'], $labelOptions);
			$endLabel             = self::closeTag('label');
			$items[]              = strtr($template, array(
				'{input}'      => $option,
				'{beginLabel}' => $beginLabel,
				'{label}'      => $label,
				'{labelTitle}' => $labelTitle,
				'{endLabel}'   => $endLabel,
			));
		}
		if(empty($container))
			return implode($separator, $items);
		else
			return self::tag($container, array('id' => $baseID), implode($separator, $items));
	}

	public static function radioButton($name, $checked = false, $htmlOptions = array())
	{
		if($checked)
			$htmlOptions['checked'] = 'checked';
		else
			unset($htmlOptions['checked']);
		$value = isset($htmlOptions['value']) ? $htmlOptions['value'] : 1;
		self::clientChange('click', $htmlOptions);

		if(array_key_exists('uncheckValue', $htmlOptions)) {
			$uncheck = $htmlOptions['uncheckValue'];
			unset($htmlOptions['uncheckValue']);
		}
		else
			$uncheck = NULL;

		if($uncheck !== NULL) {
			// add a hidden field so that if the radio button is not selected, it still submits a value
			if(isset($htmlOptions['id']) && $htmlOptions['id'] !== false)
				$uncheckOptions = array('id' => self::ID_PREFIX . $htmlOptions['id']);
			else
				$uncheckOptions = array('id' => false);
			$hidden = self::hiddenField($name, $uncheck, $uncheckOptions);
		}
		else
			$hidden = '';

		if(array_key_exists('style', $htmlOptions)) {
			$htmlOptions['style'] = $htmlOptions['style'] . ' position: absolute; left: -9999px;';
		}
		else
			$htmlOptions['style'] = 'position: absolute; left: -9999px;';

		if($checked)
			$class = "radio styler checked";
		else
			$class = "radio styler";

		if(isset($htmlOptions['disabled']) AND $htmlOptions['disabled'] == 'disabled')
			$class .= ' disabled';

		// add a hidden field so that if the radio button is not selected, it still submits a value
		return $hidden . self::inputField('radio', $name, $value, $htmlOptions) . self::openTag('span', array(
			'onclick' => '_radioBox(this); return false;', 'class' => $class,
			'style'   => 'display: inline-block')) . self::tag('span', array(), '', true) . self::closeTag('span');
	}

	public static function activeCheckBox($model, $attribute, $htmlOptions = array())
	{
		self::resolveNameID($model, $attribute, $htmlOptions);
		if(!isset($htmlOptions['value']))
			$htmlOptions['value'] = 1;
		if(!isset($htmlOptions['checked']) && self::resolveValue($model, $attribute) == $htmlOptions['value'])
			$htmlOptions['checked'] = 'checked';
		self::clientChange('click', $htmlOptions);

		if(array_key_exists('uncheckValue', $htmlOptions)) {
			$uncheck = $htmlOptions['uncheckValue'];
			unset($htmlOptions['uncheckValue']);
		}
		else
			$uncheck = '0';

		$hiddenOptions = isset($htmlOptions['id']) ? array('id' => self::ID_PREFIX . $htmlOptions['id']) : array(
			'id' => false);
		$hidden        = $uncheck !== NULL ? self::hiddenField($htmlOptions['name'], $uncheck, $hiddenOptions) : '';

		if(array_key_exists('style', $htmlOptions)) {
			$htmlOptions['style'] = $htmlOptions['style'] . ' position: absolute; left: -9999px;';
		}
		else
			$htmlOptions['style'] = 'position: absolute; left: -9999px;';

		if(isset($htmlOptions['checked']) && $htmlOptions['checked'] == 'checked')
			$class = "checkbox styler checked";
		else
			$class = "checkbox styler";

		if(isset($htmlOptions['disabled']) AND $htmlOptions['disabled'] == 'disabled')
			$class .= ' disabled';

		return $hidden . self::activeInputField('checkbox', $model, $attribute, $htmlOptions) . self::openTag('span', array(
			'onclick' => '_checkBox(this); return false;', 'class' => $class,
			'style'   => 'display: inline-block')) . self::tag('span', array(), '', true) . self::closeTag('span');
	}

	public static function checkBox($name, $checked = false, $htmlOptions = array())
	{
		if($checked)
			$htmlOptions['checked'] = 'checked';
		else
			unset($htmlOptions['checked']);
		$value = isset($htmlOptions['value']) ? $htmlOptions['value'] : 1;
		self::clientChange('click', $htmlOptions);

		if(array_key_exists('uncheckValue', $htmlOptions)) {
			$uncheck = $htmlOptions['uncheckValue'];
			unset($htmlOptions['uncheckValue']);
		}
		else
			$uncheck = NULL;

		if($uncheck !== NULL) {
			// add a hidden field so that if the check box is not checked, it still submits a value
			if(isset($htmlOptions['id']) && $htmlOptions['id'] !== false)
				$uncheckOptions = array('id' => self::ID_PREFIX . $htmlOptions['id']);
			else
				$uncheckOptions = array('id' => false);
			$hidden = self::hiddenField($name, $uncheck, $uncheckOptions);
		}
		else
			$hidden = '';

		if(array_key_exists('style', $htmlOptions)) {
			$htmlOptions['style'] = $htmlOptions['style'] . ' position: absolute; left: -9999px;';
		}
		else
			$htmlOptions['style'] = 'position: absolute; left: -9999px;';

		if($checked)
			$class = "checkbox styler checked";
		else
			$class = "checkbox styler";

		if(isset($htmlOptions['disabled']) AND $htmlOptions['disabled'] == 'disabled')
			$class .= ' disabled';

		// add a hidden field so that if the check box is not checked, it still submits a value
		return $hidden . self::inputField('checkbox', $name, $value, $htmlOptions) . self::openTag('span', array(
			'onclick' => '_checkBox(this); return false;', 'class' => $class,
			'style'   => 'display: inline-block')) . self::tag('span', array(), '', true) . self::closeTag('span');
	}

	public static function error($model, $attribute, $htmlOptions = array())
	{
		self::resolveName($model, $attribute); // turn [a][b]attr into attr
		$error = $model->getError($attribute);
		if($error != '') {
			if(!isset($htmlOptions['class']))
				$htmlOptions['class'] = 'line_info alert ferrors';

			return self::tag('div', $htmlOptions, $error);
		}
		else
			return '';
	}

	public static function listOptionsStyler($selection, $listData, &$htmlOptions)
	{
		$raw     = isset($htmlOptions['encode']) && !$htmlOptions['encode'];
		$content = '';
		if(isset($htmlOptions['prompt'])) {
			$content .= '<option value="">' . strtr($htmlOptions['prompt'], array('<' => '&lt;',
																				  '>' => '&gt;')) . "</option>\n";
			unset($htmlOptions['prompt']);
		}
		if(isset($htmlOptions['empty'])) {
			if(!is_array($htmlOptions['empty']))
				$htmlOptions['empty'] = array('' => $htmlOptions['empty']);
			foreach($htmlOptions['empty'] as $value => $label)
				$content .= '<option value="' . self::encode($value) . '">' . strtr($label, array(
						'<' => '&lt;', '>' => '&gt;')) . "</option>\n";
			unset($htmlOptions['empty']);
		}

		if(isset($htmlOptions['options'])) {
			$options = $htmlOptions['options'];
			unset($htmlOptions['options']);
		}
		else
			$options = array();

		$key = isset($htmlOptions['key']) ? $htmlOptions['key'] : 'primaryKey';
		if(is_array($selection)) {
			foreach($selection as $i => $item) {
				if(is_object($item))
					$selection[$i] = $item->$key;
			}
		}
		elseif(is_object($selection))
			$selection = $selection->$key;

		foreach($listData as $key => $value) {
			if(is_array($value)) {
				$content .= '<optgroup label="' . ($raw ? $key : self::encode($key)) . "\">\n";
				$dummy = array('options' => $options);
				if(isset($htmlOptions['encode']))
					$dummy['encode'] = $htmlOptions['encode'];
				$content .= self::listOptions($selection, $value, $dummy);
				$content .= '</optgroup>' . "\n";
			}
			else {
				$attributes = array();

				if(!is_array($selection) && !strcmp($key, $selection) || is_array($selection) && in_array($key, $selection)) {
					if(isset($attributes['class']) AND trim($attributes['class']) != '')
						$attributes['class'] = 'selected sel ' . $attributes['class'];
					else
						$attributes['class'] = 'selected sel';
				}

				if(isset($options[$key]))
					$attributes = array_merge($attributes, $options[$key]);

				$content .= self::tag('li', $attributes, $raw ? (string)$value : self::encode((string)$value)) . "\n";
			}
		}

		unset($htmlOptions['key']);

		return $content;
	}

	public static function _substr($string)
	{
		return $string;
	}

	public static function tweet($text)
	{
		$text = parent::encode($text);

		$text = preg_replace("#([\n ])([a-z]+?)://([^, \n\r]+)#i", "\\1<a href=\"\\2://\\3\" target=\"_blank\">\\2://\\3</a>", $text);
		$text = preg_replace("#([\n ])www\.([a-z0-9\-]+)\.([a-z0-9\-.\~]+)((?:/[^, \n\r]*)?)#i", "\\1<a href=\"http://www.\\2.\\3\\4\" target=\"_blank\">www.\\2.\\3\\4</a>", $text);
		$text = preg_replace("#([\n ])([a-z0-9\-_.]+?)@([^, \n\r]+)#i", "\\1<a href=\"mailto:\\2@\\3\">\\2@\\3</a>", $text);

		$text = str_replace("\n", "<br>", $text);

		return $text;
	}

	public static function bbCode($data, $is_filter = true)
	{
		if($is_filter) {
			$data = parent::encode($data);
		}

		$data = preg_replace("#([\n ])([a-z]+?)://([^, \n\r]+)#i", "\\1<a href=\"\\2://\\3\" target=\"_blank\">\\2://\\3</a>", $data);
		$data = preg_replace("#([\n ])www\.([a-z0-9\-]+)\.([a-z0-9\-.\~]+)((?:/[^, \n\r]*)?)#i", "\\1<a href=\"http://www.\\2.\\3\\4\" target=\"_blank\">www.\\2.\\3\\4</a>", $data);
		$data = preg_replace("#([\n ])([a-z0-9\-_.]+?)@([^, \n\r]+)#i", "\\1<a href=\"mailto:\\2@\\3\">\\2@\\3</a>", $data);

		$find    = array('[b]', '[/b]', "\n");
		$replace = array('<b>', '</b>', '<br>');

		$data = str_replace($find, $replace, $data);

		return $data;
	}

	public static function showMoney($amount, $type)
	{
		return '<i class="it_rub"></i> ' . $amount . ' руб.';
	}

	public static function _getLang($lang = false, $does_matter = false)
	{
		$langArr = array('ru' => '_russain', 'en' => '_english');

		if($lang) {
			if(isset($langArr[$lang])) {
				return $langArr[$lang];
			}
			else {
				return '_unknown';
			}
		}
		else {
			$langsArr = array();
			if($does_matter)
				$langsArr['matter'] = Yii::t('main', '_does_not_matter');
			foreach($langArr as $_k => $_v)
				$langsArr[$_k] = Yii::t('main', $_v);

			return $langsArr;
		}
	}

	public static function _gTwLang($lang)
	{
		$langArr = array('ru' => '_russain', 'en' => '_english');

		if(isset($langArr[$lang])) {
			return $langArr[$lang];
		}
		else {
			return '_unknown';
		}
	}

	public static function _dateTransform($date, $format = '', $to)
	{
		if($format != "unix") {
			$date = strtotime($date);
		}

		switch($to) {
		case "days":
			$result = ceil((time() - $date) / 86400);
			break;
		}

		return $result;
	}

	public static function groupByKey($models, $valueField, $textField, $groupField)
	{
		$listData = array();

		foreach($models as $model) {
			$group = self::value($model, $groupField);
			$value = self::value($model, $valueField);
			$text  = self::value($model, $textField);

			if(!$group) {
				$_listData = array();

				foreach($models as $_model) {
					$_group = self::value($_model, $groupField);

					if($_group AND $_group == $value) {
						$_value             = self::value($_model, $valueField);
						$_text              = self::value($_model, $textField);
						$_listData[$_value] = $_text;
					}
				}

				$listData[$value][$text] = $_listData;
			}
		}

		return $listData;
	}

	public static function goupListOptions($selection, $listData, &$htmlOptions)
	{
		$content = '';

		if(isset($htmlOptions['empty'])) {
			if(!is_array($htmlOptions['empty']))
				$htmlOptions['empty'] = array('' => $htmlOptions['empty']);
			foreach($htmlOptions['empty'] as $value => $label)
				$content .= '<option value="' . self::encode($value) . '">' . strtr($label, array(
						'<' => '&lt;', '>' => '&gt;')) . "</option>";
			unset($htmlOptions['empty']);
		}

		if(isset($htmlOptions['options'])) {
			$options = $htmlOptions['options'];
			unset($htmlOptions['options']);
		}
		else
			$options = array();

		$key = isset($htmlOptions['key']) ? $htmlOptions['key'] : 'primaryKey';
		if(is_array($selection)) {
			foreach($selection as $i => $item) {
				if(is_object($item))
					$selection[$i] = $item->$key;
			}
		}
		elseif(is_object($selection))
			$selection = $selection->$key;

		foreach($listData as $_key => $_value) {
			if(is_array($_value)) {
				foreach($_value as $key => $value) {
					$attributes = array('value' => (string)$_key);
					if(!is_array($selection) && !strcmp($_key, $selection) || is_array($selection) && in_array($_key, $selection))
						$attributes['selected'] = 'selected';

					if(isset($htmlOptions['classes'])) {
						if(!is_array($htmlOptions['classes'])) {
							$mainClass  = $htmlOptions['classes'];
							$lowerClass = '';
						}
						else {
							$mainClass  = $htmlOptions['classes'][0];
							$lowerClass = $htmlOptions['classes'][1];
						}

						unset($htmlOptions['classes']);
					}

					if(isset($options[$_key]))
						$attributes = array_merge($attributes, $options[$_key]);

					if($mainClass) {
						$attributes['class'] = $mainClass;
					}

					$content .= self::tag('option', $attributes, Yii::t('twitterModule.accounts', (string)$key));

					if(count($value)) {
						$dummy = array('options' => $options);

						if(isset($htmlOptions['encode']))
							$dummy['encode'] = $htmlOptions['encode'];

						foreach($value as $k => $v) {
							$attributes = array('value' => (string)$k);
							if(!is_array($selection) && !strcmp($k, $selection) || is_array($selection) && in_array($k, $selection))
								$attributes['selected'] = 'selected';

							if($lowerClass) {
								$attributes['class'] = $lowerClass;
							}

							$content .= self::tag('option', $attributes, Yii::t('twitterModule.accounts', (string)$v));
						}
					}
				}
			}
		}

		unset($htmlOptions['key']);

		return $content;
	}

	public static function GroupdropDownList($name, $select, $data, $htmlOptions = array())
	{
		$htmlOptions['name'] = $name;

		if(!isset($htmlOptions['id']))
			$htmlOptions['id'] = self::getIdByName($name);
		elseif($htmlOptions['id'] === false)
			unset($htmlOptions['id']);

		self::clientChange('change', $htmlOptions);
		$options = "\n" . self::goupListOptions($select, $data, $htmlOptions);
		$hidden  = '';

		if(isset($htmlOptions['multiple'])) {
			if(substr($htmlOptions['name'], -2) !== '[]')
				$htmlOptions['name'] .= '[]';

			if(isset($htmlOptions['unselectValue'])) {
				$hiddenOptions = isset($htmlOptions['id']) ? array('id' => self::ID_PREFIX . $htmlOptions['id']) : array(
					'id' => false);
				$hidden        = self::hiddenField(substr($htmlOptions['name'], 0, -2), $htmlOptions['unselectValue'], $hiddenOptions);
				unset($htmlOptions['unselectValue']);
			}
		}

		// add a hidden field so that if the option is not selected, it still submits a value
		return $hidden . self::tag('select', $htmlOptions, $options);
	}

	public static function activeDropDownList($model, $attribute, $data, $htmlOptions = array())
	{
		self::resolveNameID($model, $attribute, $htmlOptions);
		$selection = self::resolveValue($model, $attribute);
		$options   = "\n" . self::listOptions($selection, $data, $htmlOptions);
		self::clientChange('change', $htmlOptions);

		if($model->hasErrors($attribute))
			self::addErrorCss($htmlOptions);

		$hidden = '';
		if(!empty($htmlOptions['multiple'])) {
			if(substr($htmlOptions['name'], -2) !== '[]')
				$htmlOptions['name'] .= '[]';

			if(isset($htmlOptions['unselectValue'])) {
				$hiddenOptions = isset($htmlOptions['id']) ? array('id' => self::ID_PREFIX . $htmlOptions['id']) : array('id' => false);
				$hidden        = self::hiddenField(substr($htmlOptions['name'], 0, -2), $htmlOptions['unselectValue'], $hiddenOptions);
				unset($htmlOptions['unselectValue']);
			}
		}

		return $hidden . self::tag('select', $htmlOptions, $options);
	}

	public static function activeId($model, $attribute)
	{
		return self::getIdByName(self::activeName($model, $attribute));
	}

	public static function resolveNameID($model, &$attribute, &$htmlOptions)
	{
		if(!isset($htmlOptions['name']))
			$htmlOptions['name'] = self::resolveName($model, $attribute);
		if(!isset($htmlOptions['id']))
			$htmlOptions['id'] = self::getIdByName($htmlOptions['name']);
		elseif($htmlOptions['id'] === false)
			unset($htmlOptions['id']);
	}

	public static function resolveName($model, &$attribute)
	{
		$modelName = self::modelName($model);

		if(($pos = strpos($attribute, '[')) !== false) {
			if($pos !== 0) // e.g. name[a][b]
				return $modelName . '[' . substr($attribute, 0, $pos) . ']' . substr($attribute, $pos);
			if(($pos = strrpos($attribute, ']')) !== false && $pos !== strlen($attribute) - 1) { // e.g. [a][b]name
				$sub       = substr($attribute, 0, $pos + 1);
				$attribute = substr($attribute, $pos + 1);

				return $modelName . $sub . '[' . $attribute . ']';
			}
			if(preg_match('/\](\w+\[.*)$/', $attribute, $matches)) {
				$name      = $modelName . '[' . str_replace(']', '][', trim(strtr($attribute, array('][' => ']', '[' => ']')), ']')) . ']';
				$attribute = $matches[1];

				return $name;
			}
		}

		return $modelName . '[' . $attribute . ']';
	}

	public static function activeTextField($model, $attribute, $htmlOptions = array())
	{
		self::resolveNameID($model, $attribute, $htmlOptions);
		self::clientChange('change', $htmlOptions);

		return self::activeInputField('text', $model, $attribute, $htmlOptions);
	}

	public static function modelName($model)
	{
		if(method_exists($model, 'formName')) {
			$className = $model->formName();
		}
		else {
			if(is_callable(self::$_modelNameConverter))
				return call_user_func(self::$_modelNameConverter, $model);

			$className = is_object($model) ? get_class($model) : (string)$model;
		}

		return trim(str_replace('\\', '_', $className), '_');
	}

}
