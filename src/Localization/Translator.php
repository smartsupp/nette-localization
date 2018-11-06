<?php

namespace Smartsupp\Localization;

use Nette\Utils\Validators;

class Translator implements ITranslator
{

	/** @var array  translation table */
	private $dictionary = [];

	/** @var array */
	private $parameters = [];

	/** @var array */
	private $filters = [];


	/**
	 * Set dictionary
	 * @param array $dictionary
	 */
	public function setTranslates(array $dictionary)
	{
		$this->dictionary = $dictionary;
	}


	/**
	 * Returns translates
	 * @return string[]
	 */
	public function getTranslates()
	{
		return $this->dictionary;
	}


	/**
	 * @param array $parameters
	 */
	public function setParameters(array $parameters)
	{
		$this->parameters = array_merge($this->parameters, $parameters);
	}


	/**
	 * @return array
	 */
	public function getParameters()
	{
		return $this->parameters;
	}


	/**
	 * @param callable $filter
	 */
	public function addFilter($filter)
	{
		if (!Validators::isCallable($filter)) {
			throw new \Nette\Utils\AssertionException("Filter is not callable");
		}
		$this->filters[] = $filter;
	}


	/**
	 * Has message?
	 * @param string $key
	 * @return bool
	 */
	public function hasMessage($key)
	{
		return isset($this->dictionary[$key]);
	}


	/**
	 * Translates the given string. NEPODPORUJE PLURAL
	 * @param  string $key translation string
	 * @param  mixed $args argument (first of arguments)
	 * @return string
	 */
	public function translate($key, $args = null)
	{
		if (isset($this->dictionary[$key])) {
			$message = $this->dictionary[$key];
		} elseif (preg_match('/^(\w+\.)+\w+$/', $key)) {
			$message = '|' . $key . '|';
		} else {
			$message = $key;
		}

		if ($args !== null) {
			if (is_array($args)) {
				$message = preg_replace_callback('/\{([^}]+)\}/', function ($matches) use ($args) {
					return array_key_exists($matches[1], $args) ? $args[$matches[1]] : $matches[0];
				}, $message);
			} else {
				$args = func_get_args();
				array_shift($args);
				$message = vsprintf($message, $args);
			}
		}

		if (count($this->parameters) > 0) {
			$message = $this->applyParameters($message);
		}

		foreach ($this->filters as $filter) {
			$message = call_user_func_array($filter, [$message, $key]);
		}

		return $message;
	}


	private function applyParameters($string)
	{
		if (strpos($string, '{') === false) {
			return $string;
		}
		return preg_replace_callback('/\{([^}]+)\}/', function ($matches) {
			if (!isset($this->parameters[$matches[1]])) {
				return $matches[0];
			} else {
				return $this->applyParameters($this->parameters[$matches[1]]);
			}
		}, $string);
	}

}
