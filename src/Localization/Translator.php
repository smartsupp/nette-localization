<?php

namespace Smartsupp\Localization;

class Translator implements ITranslator
{

	/** @var boolean */
	public $debugMode = false;

	/** @var array  translation table */
	private $dictionary = [];

	/** @var array */
	private $parameters = [];


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

		return $this->replaceParameters($message);
	}


	private function replaceParameters($string)
	{
		if (strpos($string, '{') === false) {
			return $string;
		}
		return preg_replace_callback('/\{([^}]+)\}/', function ($matches) {
			if (!isset($this->parameters[$matches[1]])) {
				return $matches[0];
			} else {
				// TODO: prevent cyclic replacing
				return $this->replaceParameters($this->parameters[$matches[1]]);
			}
		}, $string);
	}

}
