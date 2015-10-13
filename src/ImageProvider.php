<?php
/**
 * @author Martin Mikšík
 */

namespace BowtieImages;


use Nette\Application\BadRequestException;
use Nette\Caching\Cache;
use Nette\Caching\Storages\FileStorage;
use Nette\Utils\Image;

class ImageProvider implements IImageProvider
{
	protected $namespace;
	protected $filename;
	protected $type; //TODO
	protected $width = '';
	protected $height = '';
	protected $flag = Image::FIT;


	protected $wwwDir;
	protected $prefix;
	protected $cacheInvalidationTime;

	protected $imageCache;

	function __construct($wwwDir, $prefix, $cacheInvalidationTime)
	{
		$this->wwwDir = $wwwDir;
		$this->prefix = $prefix;
		$this->cacheInvalidationTime = $cacheInvalidationTime;

		$storage = new FileStorage($this->wwwDir . '/../temp/cache');
		$this->imageCache = new ImageCache($cacheInvalidationTime, $this->prefix, $this->wwwDir, $storage);
	}


	public function provideImage($param)
	{
		//Check if all necessary variables exist and assign to class variables
		$this->assignVariables($param);

		//Contain origin path to image
		$pathOrigin = $this->wwwDir . '/' . $this->prefix . '/' . $this->namespace . '/' . $this->filename;

		//First check if exist in cache(it is more common to get resize then origin size)
		if($imageString = $this->imageCache->getFromCache($this->namespace, $this->width, $this->height, $this->flag, $this->filename)) {
			$image = Image::fromString($imageString, $this->type);
		//then look if height and width is null to provide origin
		}elseif($this->height == null && $this->width == null){
			$image = Image::fromFile($pathOrigin);
		//try get image, resize and save to cache
		}elseif(file_exists($pathOrigin)){
			$image = Image::fromFile($pathOrigin);
			$image = $this->resizeImage($image, $this->width, $this->height, $this->flag);

			if(!$this->imageCache->saveTocache($image, $this->namespace, $this->width, $this->height, $this->flag, $this->filename)){
				throw new BowtieImageException('There is something wrong :(');
			}

		//else image not exist throw exception
		}else{
			throw new BadRequestException('Required image not found.');
		}

		return $image;
	}

	private  function assignVariables($param)
	{
		if (!isset($param['namespace']))
			throw new BowtieImageException('namespace is not set');

		if (!isset($param['filename']))
			throw new BowtieImageException('filename is not set');

		if (!isset($param['type']))
			throw new BowtieImageException('type is not set');

		foreach ($param as $k => $v) {
			switch($k){
				case 'namespace':
				case 'filename':
				case 'type':
				case 'height':
				case 'width':
					$this->$k = $v;
					break;

				case 'flag':
					switch($v){
						case 'fit':
							$this->flag = Image::FIT;
							break;

						case 'fill':
							$this->flag = Image::FILL;
							break;

						case 'exact':
							$this->flag = Image::EXACT;
							break;

						case 'stretch':
							$this->flag = Image::STRETCH;
							break;
						//TODO: Exact require width and height
						//TODO: Schould throw exception when flag is not recognized!
					}
					break;
			}
		}

		$this->filename = $this->filename . $this->type;

	}

	private function resizeImage(Image $image, $width, $height = '', $flag = Image::FIT)
	{
		$image->resize($width, $height, $flag);
		return $image;
	}
}
