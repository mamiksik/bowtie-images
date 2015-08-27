<?php
/**
 * @author Martin Mikšík
 */

namespace BowtieImages;


use Nette\DI\CompilerExtension;
use Nette;
use Nette\Application;

class ImageExtension extends CompilerExtension
{
	public $defaults = array(
		 'wwwDir' => '%wwwDir%',
		 'urlPrefix' => 'images',
		 'dataPrefix' => 'data',
		 'cacheInvalidationTime' => '7 days'
	);

	public function loadConfiguration()
	{
		$container = $this->getContainerBuilder();
		$config = $this->getConfig($this->defaults);

		if($config['urlPrefix'] === $config['dataPrefix']){
			throw new BowtieImageException("urlPrefix and dataPrefix can't be same. Change one of them in config file.");
		}

		$provider = $container->addDefinition($this->prefix('provider'))
			 ->setClass('BowtieImages\ImageProvider', [$config['wwwDir'], $config['dataPrefix'], $config['cacheInvalidationTime']]);

		$container->addDefinition($this->prefix('imageStorage'))
			 ->setClass('BowtieImages\ImageStorage', [$config['dataPrefix'], $config['wwwDir']]);

		//url mask
		$routerMask = $config['urlPrefix'] . '/<namespace>[/<width>x[<height>]][/<flag>]/<filename><type .png|.gif|.jpg|.jpeg>';

		$router = $container->addDefinition($this->prefix('router'))
			 ->setClass('Nette\Application\Routers\RouteList')
			 ->addTag($this->prefix('routeList'))
			 ->setAutowired(FALSE);

		$route = $container->addDefinition($this->prefix('route1'))
			 ->setClass('BowtieImages\ImageRoute', [
				  $routerMask, [],
				  $this->prefix('@provider')
			 ])
			 ->addTag($this->prefix('route'))
			 ->setAutowired(FALSE);

		$router->addSetup('$service[] = ?', [
			 $this->prefix('@route1'),
		]);


	}

	public function beforeCompile()
	{
		$container = $this->getContainerBuilder();
		$router = $container->getByType('Nette\Application\IRouter');
		if ($router) {
			if (!$router instanceof \Nette\DI\ServiceDefinition) {
				$router = $container->getDefinition($router);
			}
		} else {
			$router = $container->getDefinition('router');
		}

		$router->addSetup('BowtieImages\ImageExtension::prepend', [
			 '@self',
			 $this->prefix('@router'),
		]);
	}

	public static function prepend(Application\Routers\RouteList $router, Application\IRouter $route)
	{
		$router[] = $route;

		$lastKey = count($router) - 1;
		foreach ($router as $i => $r) {
			if ($i === $lastKey) {
				break;
			}
			$router[$i + 1] = $r;
		}

		$router[0] = $route;
	}

} 