<?php
/**
 * @author Martin Mikšík
 */

namespace BowtieImages;


use Nette\Application\BadRequestException;
use Nette\Caching\Cache;
use Nette\Caching\Storages;
use Nette\Utils\Image;

class ImageProvider
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

	function __construct($wwwDir, $prefix, $cacheInvalidationTime)
	{
		$this->wwwDir = $wwwDir;
		$this->prefix = $prefix;
		$this->cacheInvalidationTime = $cacheInvalidationTime;
	}


	public function provideImage($param)
	{
		//Check if all necessary variables exist and assign to class variables
		$this->assignVariables($param);
		$imageCache = new ImageCache($this->cacheInvalidationTime,
			                        $this->filename,
			                        $this->flag,
			                        $this->height,
			                        $this->namespace,
			                        $this->prefix,
			                        $this->type,
			                        $this->width,
			                        $this->wwwDir);


		//Contain origin path to image
		$pathOrigin = $this->wwwDir . '/' . $this->prefix . '/' . $this->namespace . '/' . $this->filename;

		//First check if exist in cache(it is more common to get resize then origin size)
		if($image = $imageCache->getFromCache()) {
			$image = Image::fromString($image, $this->type);
			$image->send();
			exit;
		//then look if height and width is null to provide origin
		}elseif($this->height == null && $this->width == null){
			$image = Image::fromFile($pathOrigin);
			$image->send();
			exit;
		//try get image, resize and save to cache
		}elseif(file_exists($pathOrigin)){
			$image = Image::fromFile($pathOrigin);
			$image = $this->resizeImage($image, $this->width, $this->height, $this->flag);

			if(!$imageCache->saveTocache($image)){
				throw new BowtieImageException('There is something wrong :(');
			}

			$image->send();
			exit;
		//else image not exist throw exception
		}else{
			throw new BadRequestException('Required image not found.');
		}

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
					$this->namespace = $v;
					break;

				case 'filename':
					$this->filename = $v;
					break;

				case 'type':
					$this->type = $v;
					break;

				case 'height':
					$this->height = $v;
					break;

				case 'width':
					$this->width = $v;
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