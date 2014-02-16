<?php

namespace common\components;

class Redis extends \yii\base\Component
{
	/*
	 * Настройки сервера
	 *
	 * @var array
	 */
	public $server = [
		'host' => '127.0.0.1',
		'port' => 6379
	];
	/*
	 * @var boolean
	 */
	protected $is_connected = false;
	protected $redis;

	public function init()
	{
		parent::init();

		if($this->is_connected === false) {
			$this->redis = new \Redis();
			$this->redis->connect($this->server['host'], $this->server['port']);

			$this->is_connected = true;
		}
	}

	public function set($key, $value)
	{
		$this->redis->set($key, $value);
		return $this;
	}

	public function get($key)
	{
		return $this->redis->get($key);
	}

	public function getMultiple($keys)
	{
		return $this->redis->mGet($keys);
	}

	public function redisSize()
	{
		return $this->redis->dbSize();
	}

	public function exists($key)
	{
		return $this->redis->exists($key);
	}

	public function delete($key)
	{
		$this->redis->delete($key);
	}

	public function zAdd($key, $h, $value)
	{
		$this->redis->zAdd($key, $h, $value);

		return $this;
	}

	public function zSize($key)
	{
		return $this->redis->zSize($key);
	}

	public function zRange($k, $h, $v)
	{
		return $this->redis->zRange($k, $h, $v);
	}

	public function hMset($k, $v)
	{
		$this->redis->hMset($k, $v);

		return $this;
	}

	public function hLen($key)
	{
		return $this->redis->hLen($key);
	}

	public function hSet($k, $h, $v)
	{
		$this->redis->hSet($k, $h, $v);
		return $this;
	}

	public function hGetAll($key)
	{
		return $this->redis->hGetAll($key);
	}

	public function hGet($key, $hkey)
	{
		return $this->redis->hGet($key, $hkey);
	}

	public function hKeys($key)
	{
		return $this->redis->hKeys($key);
	}

	public function hmGet($key, $fields)
	{
		return $this->redis->hmGet($key, $fields);
	}

	public function hExists($k, $v)
	{
		return $this->redis->hExists($k, $v);
	}

	public function lPush($k, $v)
	{
		$this->redis->lPush($k, $v);
		return $this;
	}

	public function lRange($k, $s, $e)
	{
		return $this->redis->lRange($k, $s, $e);
	}

	public function lRem($k, $h, $v)
	{
		return $this->redis->lRem($k, $h, $v);
	}

	public function rPush($k, $v)
	{
		return $this->redis->rPush($k, $v);
	}

	public function expire($k, $time)
	{
		return $this->redis->expire($k, $time);
	}

	public function hDel($key, $hash)
	{
		return $this->redis->hDel($key, $hash);
	}

	/*
	 * Удаление по шаблону (временное решение)
	 */
	public function hDelete($key, $patter)
	{
		$keys = $this->hKeys($key);

		if($keys !== array()) {
			foreach($keys as $e) {
				if(substr($e, 0, strlen($patter)) === $patter)
					$this->hDel($key, $e);
			}
		}

		return $keys;
	}

	/*
	 * Получаем ключи по шаблону
	 */
	public function keys($pattern)
	{
		return $this->redis->keys($pattern);
	}

	/*
	 * Очищаем базу
	 */
	public function flushDB()
	{
		return $this->redis->flushDB();
	}
}