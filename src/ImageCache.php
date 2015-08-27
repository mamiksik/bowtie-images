<?php
/**
 * @author Martin Mikšík
 */

namespace BowtieImages;


use Nette\Caching\Cache;
use Nette\Caching\Storages\FileStorage;
use Nette\Utils\Image;

class ImageCache extends \Nette\Object
{
	protected $wwwDir;
	protected $prefix;
	protected $cacheInvalidationTime;

	/** @var Cache */
	protected $cache;

	function __construct($cacheInvalidationTime, $prefix, $wwwDir, $storage)
	{
		$this->prefix = $prefix;
		$this->wwwDir = $wwwDir;
		$this->cacheInvalidationTime = $cacheInvalidationTime;
		$this->cache = new Cache($storage, 'BowtieImage.Cache');
	}

	public function saveTocache(Image $image, $namespace, $width, $height, $flag, $filename)
	{
		$this->cache->save($this->getCacheName($namespace, $width, $height, $flag, $filename), $image->toString(), array(
			 Cache::EXPIRE => $this->cacheInvalidationTime,
			 Cache::SLIDING => TRUE,
			 Cache::FILES => $this->wwwDir . '/' . $this->prefix . '/' . $namespace . '/' . $filename
		));

		return true;
	}

	public function getFromCache($namespace, $width, $height, $flag, $filename)
	{
		return $image = $this->cache->load($this->getCacheName($namespace, $width, $height, $flag, $filename));
	}


	public  function getCacheName($namespace, $width, $height, $flag = Image::FIT, $filename)
	{
		return $namespace . "." . $width . "." . $height . "." . $flag . "." . $filename;
	}
}

