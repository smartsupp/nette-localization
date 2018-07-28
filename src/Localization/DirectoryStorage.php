<?php

namespace Smartsupp\Localization;

class DirectoryStorage implements ITranslateStorage
{

	private $dir;


	public function __construct($dir)
	{
		$this->dir = $dir;
	}


	/**
	 * Get translates for lang
	 * @param string $section
	 * @param string $lang
	 * @return array
	 */
	public function getTranslates($section, $lang)
	{
		$path = $this->dir . '/' . $section . '/' . $lang . '.json';
		if (is_file($path)) {
			$translates = [];
			$data = json_decode(file_get_contents($path), true);
			$data = array_filter($data, function ($value) {
				return $value !== '';
			});
			$this->expandKeys($translates, $data);
			return $translates;
		} else {
			return [];
		}
	}


	/**
	 * Get last change
	 * @param string $section
	 * @param string $lang
	 * @return int
	 */
	public function getLastChange($section, $lang)
	{
		$path = $this->dir . '/' . $section . '/' . $lang . '.json';
		if (is_file($path)) {
			return filemtime($path);
		} else {
			return 0;
		}
	}


	/**
	 * Expand structured translates
	 * @param array $translates
	 * @param array $data
	 * @param string $prefix
	 */
	private function expandKeys(array &$translates, array $data, $prefix = null)
	{
		foreach ($data as $key => $value) {
			if (is_array($value)) {
				$this->expandKeys($translates, $value, $prefix ? "$prefix.$key" : $key);
			} else {
				$translates[$prefix ? "$prefix.$key" : $key] = $value;
			}
		}
	}

}
