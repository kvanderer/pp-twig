<?php

namespace Dalee\PPTwig;

use App\Extension\PPExtension;
use Twig\Environment;
use Twig\Extension\ExtensionInterface;
use Twig\Loader\FilesystemLoader;
use Twig\Extension\DebugExtension;
use PP\Lib\Html\Layout\LayoutInterface;
use Twig\TwigFunction;

/**
 * Class TwigLayout.
 *
 * @package Dalee\PPTwig
 */
class TwigLayout implements LayoutInterface
{

	/** @var Environment */
	protected $twig;

	/** @var \PXUserHTMLLang */
	protected $lang;

	/** @var array */
	protected $vars = [];

	/** @var string */
	protected $indexTemplate = 'index.twig';

	/**
	 * TwigLayout constructor.
	 *
	 * @param array $settings
	 */
	public function __construct(array $settings = [])
	{
		$settings = array_merge([
			'cache_path' => CACHE_PATH . '/twig_templates_c',
			'template_dir' => [BASEPATH . '/local/templates/'],
			'strict_variables' => true,
		], $settings);
		$loader = new FilesystemLoader(array_merge($settings['template_dir'], [dirname(__FILE__) . '/templates/']));
		MakeDirIfNotExists($settings['cache_path']);
		$this->twig = new Environment($loader, [
			'cache' => $settings['cache_path'],
			'strict_variables' => $settings['strict_variables'],
			'debug' => false,
		]);
		$this->addExtension(new PPExtension());
		$this->twig->addFunction(new TwigFunction('lang', function (string $key) {
			if (!$this->lang) {
				return $key;
			}

			return $this->lang->_get($key);
		}));
	}

	/**
	 * @param string $name
	 * @param mixed $value
	 * @return LayoutInterface
	 */
	public function assign(string $name, $value): LayoutInterface
	{
		$this->vars[$name] = $value;

		return $this;
	}

	/**
	 * @return string|null
	 * @throws \Twig\Error\LoaderError
	 * @throws \Twig\Error\RuntimeError
	 * @throws \Twig\Error\SyntaxError
	 */
	public function display(): ?string
	{
		return $this->twig->render($this->indexTemplate, $this->vars);
	}

	/**
	 * @param \PXUserHTMLLang $lang
	 * @return LayoutInterface
	 */
	function setLang(\PXUserHTMLLang $lang): LayoutInterface
	{
		$this->lang = $lang;

		return $this;
	}

	/**
	 * @return \PXUserHTMLLang
	 */
	function getLang(): \PXUserHTMLLang
	{
		return $this->lang;
	}

	/**
	 * @param string $langCode
	 * @return LayoutInterface
	 */
	function setLangCode(string $langCode = 'rus'): LayoutInterface
	{
		$this->lang->setLang($langCode);

		return $this;
	}

	/**
	 * @param bool $value
	 * @return LayoutInterface
	 */
	function setDebug(bool $value): LayoutInterface
	{
		if (!$value) {
			$this->twig->disableDebug();
			$this->twig->disableAutoReload();

			return $this;
		}

		$this->twig->enableDebug();
		$this->twig->enableAutoReload();

		if (!$this->twig->hasExtension(DebugExtension::class)) {
			$this->twig->addExtension(new DebugExtension());
		}

		return $this;
	}

	/**
	 * @param ExtensionInterface $extension
	 * @return TwigLayout
	 */
	public function addExtension(ExtensionInterface $extension): TwigLayout
	{
		$this->twig->addExtension($extension);

		return $this;
	}

}
