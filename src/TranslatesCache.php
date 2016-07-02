<?php

namespace Smartsupp\Localization;

use Nette\Utils\SafeStream;

/**
 * TranslatesCache
 */
class TranslatesCache
{
	/** @var string */
	private $tempDir;

	/** @var string */
	private $filename;


	/**
	 * Set temp dir
	 * @param string $tempDir
	 */
	public function setTempDir($tempDir)
	{
		$this->tempDir = $tempDir;
	}


	/**
	 * Set filename of cached file
	 * @param string $filename
	 */
	public function setFilename($filename)
	{
		$this->filename = $filename;
	}


	/**
	 * Check if cache file exists
	 * @return bool
	 */
	public function isCached()
	{
		return is_file($this->getCacheFile());
	}


	/**
	 * Get cache file path
	 * @return string
	 */
	public function getCacheFile()
	{
		return $this->tempDir . '/' . $this->filename;
	}


	/**
	 * Load cached translates
	 * @return array
	 */
	public function load()
	{
		if ($this->isCached()) {
			return include $this->getCacheFile();
		} else {
			return null;
		}
	}


	/**
	 * Store translates
	 * @param string $translates
	 */
	public function save($translates)
	{
		if (preg_match('/^([^\-]+\-[^\-]+\-).*$/', $this->filename, $matches)) {
			foreach (scandir($this->tempDir) as $f) { // clean old
				if ($f[0] != '.') {
					if (strpos($f, $matches[1]) === 0) {
						unlink($this->tempDir . '/' . $f);
					}
				}
			}
		}
		SafeStream::register();
		file_put_contents('nette.safe://' . $this->getCacheFile(), $translates);
	}

}
