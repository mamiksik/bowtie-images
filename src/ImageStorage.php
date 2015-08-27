<?php
/**
 * @author Martin Mikšík
 */

namespace BowtieImages;

use Nette\Http\FileUpload;
use Nette\Object;
use Nette\Utils\Image;

class ImageStorage extends Object
{
	protected $wwwDir;
	protected $dataPrefix;

	function __construct($dataPrefix, $wwwDir)
	{
		$this->dataPrefix = $dataPrefix;
		$this->wwwDir = $wwwDir;
	}


	public function changeImage($namespac, Image $image, $oldImageName)
	{
		$image = $this->savaOriginImage($namespac, $image);
		$this->removeImage($namespac, $oldImageName);
		return $image;
	}

	public function savaOriginImage($namespace, FileUpload $file)
	{
		if(!$file->isOk() || !$file->isImage()){
			throw new ImageStorageException("invalid image");
		}

		$i = 1;
		$name = $file->getSanitizedName();
		$path = $this->getPath($namespace, $name);

		while(file_exists($path)) {
			$name = $i . '-' . $file->getSanitizedName();
			$path = $this->getPath($namespace, $name);
			$i++;
		}

		$file->move($path);
		return $name;
	}

	public function removeImage($namespace, $name)
	{
		if(file_exists($path = $this->getPath($namespace, $name))){
			@unlink($path);
		}
	}

	private function getPath($namespace, $name)
	{
		return $this->wwwDir . "/" . $this->dataPrefix . "/" . $namespace . "/" . $name;
	}
}
