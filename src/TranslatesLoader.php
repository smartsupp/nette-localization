<?php

namespace Smartsupp\Localization;

class TranslatesLoader
{

	/** @var bool */
	public $debugMode = false;

	/** @var string */
	private $tempDir;

	/** @var ITranslateStorage */
	private $storage;


	public function __construct(ITranslateStorage $storage)
	{
		$this->storage = $storage;
	}


	/**
	 * Set temp dir for cache
	 * @param string $tempDir Musi byt zapisovatelny
	 */
	public function setTempDir($tempDir)
	{
		$this->tempDir = $tempDir;
	}


	/**
	 * Returns translates storage
	 * @return ITranslateStorage
	 */
	public function getStorage()
	{
		return $this->storage;
	}


	/**
	 * Get translates from cache
	 * @param string $section
	 * @param string $lang
	 * @param string $defaultLang
	 * @return array
	 */
	public function loadTranslates($section, $lang, $defaultLang = null)
	{
		$tempDir = $this->tempDir;
		if (!is_dir($tempDir)) {
			mkdir($tempDir);
		}

		$cachedFile = $tempDir . '/' . $lang . '-' . $section . '.php';
		if (!$this->debugMode && is_file($cachedFile)) {
			$translates = include $cachedFile;
		} else {
			// create cache
			$cache = new TranslatesCache();
			$cache->setTempDir($tempDir);
			$cache->setFilename($lang . '-' . $section . ($this->debugMode ? '-' . md5($this->getLastChange($section, $lang, $defaultLang)) : '') . '.php');
			// build or load cached file
			$translates = $cache->load();
			if (!$translates) {
				$cache->save($this->compile($this->getTranslates($section, $lang, $defaultLang)));
				$translates = $cache->load();
			}
		}

		if (is_array($translates)) {
			return $translates;
		} else {
			return [];
		}
	}


	/**
	 * Format array into php
	 * @param array $translates
	 * @return string
	 */
	private function compile(array $translates)
	{
		$string = "<?php\nreturn array(\n";
		foreach ($translates as $from => $to) {
			$string .= "'$from' => \"" . str_replace('"', '\'', $to) . "\",\n";
		}
		$string .= ");";
		return $string;
	}


	private function getTranslates($section, $lang, $defaultLang = null)
	{
		$translates = $this->storage->getTranslates($section, $lang);
		if ($defaultLang && $defaultLang != $lang) {
			$translates = array_merge($this->storage->getTranslates($section, $defaultLang), $translates);
		}
		ksort($translates);
		return $translates;
	}


	private function getLastChange($section, $lang, $defaultLang = null)
	{
		if ($defaultLang) {
			return max($this->storage->getLastChange($section, $lang), $this->storage->getLastChange($section, $defaultLang));
		} else {
			return $this->storage->getLastChange($section, $lang);
		}
	}

}
