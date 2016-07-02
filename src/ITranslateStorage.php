<?php

namespace Smartsupp\Localization;

interface ITranslateStorage
{

	/**
	 * Get translates for lang
	 * @param string $section
	 * @param string $lang
	 * @return array
	 */
	function getTranslates($section, $lang);


	/**
	 * Get last change
	 * @param string $section
	 * @param string $lang
	 * @return int
	 */
	function getLastChange($section, $lang);
}
