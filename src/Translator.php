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
	 * @param  mixed $arg argument (first of arguments)
	 * @return string
	 */
	public function translate($key, $arg = null)
	{
		if (isset($this->dictionary[$key])) {
			$message = $this->dictionary[$key];
		} elseif (preg_match('/^(\w+\.)+\w+$/', $key)) {
			$message = '|' . $key . '|';
		} else {
			$message = $key;
		}

		if ($arg !== null) {
			if (is_array($arg)) {
				foreach ($arg as $name => $value) {
					$message = str_replace('{' . $name . '}', $value, $message);
				}
			} else {
				$args = func_get_args();
				array_shift($args);
				$message = vsprintf($message, $args);
			}
		}

		return preg_replace_callback('/\{([^}]+)\}/', function ($matches) {
			if (isset($this->parameters[$matches[1]])) {
				return $this->parameters[$matches[1]];
			} else {
				return $matches[1];
			}
		}, $message);
	}

}
