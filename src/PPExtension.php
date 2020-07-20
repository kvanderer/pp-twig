<?php

namespace Dalee\PPTwig;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Twig\TwigFilter;

/**
 * Class PPExtension.
 *
 * @package Dalee\PPTwig
 */
class PPExtension extends AbstractExtension
{

	/**
	 * @inheritDoc
	 */
	public function getFunctions()
	{
		return [
			new TwigFunction('create_path', 'createPathByParentId'),
			new TwigFunction('parse_float', 'parseFloat'),
			new TwigFunction('parse_bool', 'parseBool'),
			new \Twig\TwigFunction('template_exists', function(Environment $env, string $path) {
				return $env->getLoader()->exists($path);
			}, ['needs_environment' => true]),
		];
	}


	/**
	 * @inheritDoc
	 */
	public function getFilters()
	{
		return [
			new TwigFilter('date_format', [Filters::class, 'dateFormat']),
			new TwigFilter('date_to_time', [Filters::class, 'dateToTime']),
			new TwigFilter('asset_updated', [Filters::class, 'assetUpdated']),
		];
	}

}
