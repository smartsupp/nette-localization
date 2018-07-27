<?php

namespace Smartsupp\Localization;

use Nette\DI\CompilerExtension;

class LocalizationExtension extends CompilerExtension
{

	public $defaults = [
		'translatesDir' => null,
		'debugMode' => null,
		'sections' => null,
		'alias' => null,
		'parameters' => [],
	];

	private $tempDir;


	public function __construct($tempDir = null)
	{
		$this->tempDir = $tempDir;
	}


	public function loadConfiguration()
	{
		$container = $this->getContainerBuilder();
		$config = $this->getConfig($this->defaults);
		$debugMode = $config['debugMode'] !== null ? $config['debugMode'] : $container->parameters['debugMode'];
		$tempDir = $this->tempDir ? $this->tempDir : $container->parameters['tempDir'];

		$container->addDefinition($this->prefix('translatesStorage'))
			->setClass('Smartsupp\Localization\DirectoryStorage')
			->setArguments([$config['translatesDir']]);

		$container->addDefinition($this->prefix('translatesLoader'))
			->setClass('Smartsupp\Localization\TranslatesLoader')
			->addSetup('$debugMode', [$debugMode])
			->addSetup('setTempDir', [$tempDir . '/cache/_Wimo.Localization']);

		$translatorFactory = $container->addDefinition($this->prefix('translatorFactory'))
			->setClass('Smartsupp\Localization\TranslatorFactory')
			->addSetup('$debugMode', [$debugMode])
			->addSetup('setParameters', [$config['parameters']]);

		if ($config['sections']) {
			$translatorFactory->addSetup('$defaultSections', [$config['sections']]);
		}
		if ($config['alias']) {
			foreach ($config['alias'] as $from => $to) {
				$translatorFactory->addSetup('setAlias', [$from, $to]);
			}
		}
	}

}
