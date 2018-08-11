<?php

class cookieTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var Cookie
	 */
	protected $cookie;

	protected function setUp()
	{
		$this->phoenix = new \PHPhoenix\Phoenix;
		$this->cookie = new \PHPhoenix\Cookie($this->phoenix);
		$this->cookie->set_cookie_data(array(
			'fairy' => 'Tinkerbell',
			'phoenix' => 'Trixie'
		));
	}

	public function testGet() {
		$this->assertEquals('Tinkerbell', $this->cookie->get('fairy', 'test'));
		$this->assertEquals('test', $this->cookie->get('fairy2', 'test'));
	}

	public function testSet() {
		$this->cookie->set('fairy', 'test2');
		$this->assertEquals('test2', $this->cookie->get('fairy', 'test'));
		$this->cookie->set('fairy2', 'test3');
		$this->assertEquals('test3', $this->cookie->get('fairy2', 'test'));
	}

	public function testRemove() {
		$this->cookie->remove('fairy');
		$this->assertEquals('test', $this->cookie->get('fairy', 'test'));
	}

	public function testUpdates() {
		$this->assertEquals(0, count($this->cookie->get_updates()));
		$this->cookie->set('fairy', 'test');
		$updates = $this->cookie->get_updates();
		$this->checkUpdate('fairy', 'test', null, null, null, null, null);
		
		$this->cookie->set('fairy', 'test', 4, '/', 'phphoenix.com', true, true);
		$updates = $this->cookie->get_updates();
		$this->checkUpdate('fairy', 'test', 4, '/', 'phphoenix.com', true, true);
		
		$this->cookie->remove('fairy');
		$this->checkUpdate('fairy', null, -24*3600*30, null, null, null, null);
	}
	
	public function testDefaults() {
		$this->phoenix->config->set('cookie.lifetime', 4);
		$this->phoenix->config->set('cookie.path', '/');
		$this->phoenix->config->set('cookie.domain', 'phphoenix.com');
		$this->phoenix->config->set('cookie.secure', true);
		$this->phoenix->config->set('cookie.http_only', true);
		$cookie = new \PHPhoenix\Cookie($this->phoenix);
		$cookie->set('fairy', 'test');
		$this->checkUpdate('fairy', 'test', 4, '/', 'phphoenix.com', true, true, $cookie->get_updates());
	}
	
	public function testSetData() {
		$this->cookie->set('fairy', 'test');
		$this->cookie->set_cookie_data(array(
			'fairy' => 'Blum'
		));
		$this->assertEquals(0, count($this->cookie-> get_updates()));
		$this->assertEquals('Blum', $this->cookie->get('fairy', 'test'));
		
	}
	
	protected function checkUpdate($key, $val, $lifetime, $path, $domain, $secure, $http_only, $updates = null) {
		if ($updates == null)
			$updates = $this->cookie->get_updates();
			
		$update = $updates[$key]; 
		$this->assertEquals($val, $this->phoenix-> arr($update, 'value', null));
		if($lifetime == null){
			$this->assertEquals(null, $this->phoenix->arr($update, 'expires', null));
		}else {
			$this->assertEquals(true, $this->phoenix->arr($update, 'expires', null) - time() - $lifetime < 1);
		}
		$this->assertEquals($path, $this->phoenix->arr($update, 'path', null));
		$this->assertEquals($domain, $this->phoenix->arr($update, 'domain', null));
		$this->assertEquals($secure, $this->phoenix->arr($update, 'secure', null));
		$this->assertEquals($http_only, $this->phoenix->arr($update, 'http_only', null));
	}

}
