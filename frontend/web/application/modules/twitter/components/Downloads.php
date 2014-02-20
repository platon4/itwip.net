<?php

namespace twitter\components;

use Yii;

class Downloads
{
	protected $fileSize;
	protected $fileName;
	protected $tweets;

	public function setTweets($tweets)
	{
		if(is_array($tweets)) {
			$this->tweets = $tweets;
		}
		else {
			throw(new \Exception('Not tweets list set.'));
		}
	}

	public function setOutPutFile()
	{

	}

	public function outPutFile()
	{
		$this->setHeaders();

		if(is_array($this->tweets) && $this->tweets !== []) {
			foreach($this->tweets as $tweet) {
				echo $tweet['tweet'] . "\n";
			}
		}

		Yii::app()->end();
	}

	public function setFileName($name)
	{
		$this->fileName = $name;
	}

	public function fileName()
	{
		return $this->fileName;
	}

	protected function setHeaders()
	{
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private", false);
		header("Content-Type: plain/text");
		header('Content-Disposition: attachment; filename="' . $this->fileName() . '.txt";');
		header("Content-Transfer-Encoding: binary");

		header("Content-Length: " . $this->fileSize);
		header("Connection: close");

		@ini_set('max_execution_time', 0);
		@set_time_limit();
	}
}
