<?php
/**
 * @author Martin Mikšík
 */

namespace BowtieImages;


use Nette\Caching\Cache;
use Nette\Caching\Storages\FileStorage;
use Nette\Utils\Image;

class ImageCache
{
	protected $namespace;
	protected $filename;
	protected $type; //TODO
	protected $width;
	protected $height;
	protected $flag = Image::FIT;

	protected $wwwDir;
	protected $prefix;
	protected $cacheInvalidationTime;

	/** @var Cache */
	protected $cache;

	function __construct($cacheInvalidationTime, $filename, $flag, $height, $namespace, $prefix, $type, $width, $wwwDir)
	{
		$this->cacheInvalidationTime = $cacheInvalidationTime;
		$this->filename = $filename;
		$this->flag = $flag;
		$this->height = $height;
		$this->namespace = $namespace;
		$this->prefix = $prefix;
		$this->type = $type;
		$this->width = $width;
		$this->wwwDir = $wwwDir;

		$storage = new FileStorage($this->wwwDir . '/../temp/cache');
		$this->cache = new Cache($storage, 'BowtieImage.Cache');
	}

	public function saveTocache(Image $image)
	{
		$this->cache->save($this->getCacheName(), $image->toString(), array(
			 Cache::EXPIRE => $this->cacheInvalidationTime,
			 Cache::SLIDING => TRUE,
		));

		return true;
	}

	public function getFromCache()
	{
		return $image = $this->cache->load($this->getCacheName());
	}


	public  function getCacheName()
	{
		return $this->namespace . "." . $this->width . "." . $this->height . "." . $this->flag . "." . $this->filename;
	}
}
