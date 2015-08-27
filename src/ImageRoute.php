<?php
/**
 * @author Martin Mikšík
 */

namespace BowtieImages;

use Nette\Application\Routers\Route;
use Nette\Http\IRequest;

class ImageRoute extends Route
{

	protected $imageProvider;

	public function __construct($mask, array $defaults = [], ImageProvider $imageProvider)
	{
		$this->imageProvider = $imageProvider;

		$defaults['presenter'] = 'Nette:Micro';

		$defaults['callback'] = function($presenter){
			$parameters = $presenter->getRequest()->getParameters();
			$this->imageProvider->provideImage($parameters);
		};

		parent::__construct($mask, $defaults);
	}
}
