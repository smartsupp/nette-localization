<?php declare(strict_types = 1);

namespace Smartsupp\Localization;

use Nette\Utils\AssertionException;
use Nette\Utils\Validators;

class TranslatorFactory
{

	/** @var array */
	public $alias = [];

	/** @var string Used as defaults */
	public $defaultLang = 'en';

	/** @var array Global sections, added to each translator */
	public $defaultSections = [];

	/** @var array */
	private $parameters = [];

	/** @var array */
	private $filters = [];

	/** @var TranslatesLoader */
	private $loader;


	public function __construct(TranslatesLoader $loader)
	{
		$this->loader = $loader;
	}


	/**
	 * Add language alias. If requested "$from" language, returned is translator with "$to" language.
	 */
	public function setAlias(string $from, string $to): void
	{
		$this->alias[$from] = $to;
	}


	public function setParameters(array $parameters): void
	{
		$this->parameters = array_merge($this->parameters, $parameters);
	}


	public function addFilter(callable $filter): void
	{
		if (!Validators::isCallable($filter)) {
			throw new AssertionException("Filter is not callable");
		}
		$this->filters[] = $filter;
	}


	public function create(string $lang, array $sections = []): Translator
	{
		$translator = new Translator();
		$translator->setParameters(['lang' => $lang]);
		$translator->setParameters($this->parameters);
		foreach ($this->filters as $filter) {
			$translator->addFilter($filter);
		}

		if (isset($this->alias[$lang])) {
			$lang = $this->alias[$lang];
		}

		$translates = [];
		foreach ($this->defaultSections as $section) {
			$translates += $this->loader->loadTranslates($section, $lang, $this->defaultLang);
		}
		foreach ($sections as $section) {
			$translates += $this->loader->loadTranslates($section, $lang, $this->defaultLang);
		}
		$translator->setTranslates($translates);

		return $translator;
	}

}
