<?php

namespace Smartsupp\Localization;

class Translator implements ITranslator
{

	/** @var boolean */
	public $debugMode = false;

	/** @var array  translation table */
	private $dictionary = [];


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
		} else {
			$message = '|' . $key . '|';
		}
		if ($arg !== null) {
			if (is_array($arg)) {
				foreach ($arg as $name => $value) {
					$message = str_replace('{' . $name . '}', $value, $message);
				}
				return $message;
			} else {
				$args = func_get_args();
				array_shift($args);
				return vsprintf($message, $args);
			}
		} else {
			return $message;
		}
	}

}
