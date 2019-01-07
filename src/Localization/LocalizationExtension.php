<?php

namespace Smartsupp\Localization;

use Nette\DI\CompilerExtension;
use Nette\Utils\Validators;

class LocalizationExtension extends CompilerExtension
{

	public $defaults = [
		'tempDir' => '%tempDir%/cache/Localization',
		'translatesDir' => null,
		'debugMode' => '%debugMode%',
		'sections' => [],
		'alias' => [],
		'parameters' => [],
		'filters' => [],
	];


	public function loadConfiguration()
	{
		$container = $this->getContainerBuilder();
		$config = $this->validateConfig($this->defaults);

		Validators::assertField($config, 'translatesDir', 'string');

		$container->addDefinition($this->prefix('translatesStorage'))
			->setFactory(DirectoryStorage::class)
			->setArguments([$config['translatesDir']]);

		$container->addDefinition($this->prefix('translatesLoader'))
			->setFactory(TranslatesLoader::class)
			->addSetup('$debugMode', [$config['debugMode']])
			->addSetup('setTempDir', [$config['tempDir']]);

		$translatorFactory = $container->addDefinition($this->prefix('translatorFactory'))
			->setFactory(TranslatorFactory::class)
			->addSetup('$defaultSections', [$config['sections']])
			->addSetup('setParameters', [$config['parameters']]);

		if (count($config['alias'])) {
			foreach ($config['alias'] as $from => $to) {
				$translatorFactory->addSetup('setAlias', [$from, $to]);
			}
		}

		if (count($config['filters'])) {
			foreach ($config['filters'] as $filter) {
				$translatorFactory->addSetup('addFilter', [$filter]);
			}
		}
	}

}
