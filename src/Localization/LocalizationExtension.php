<?php declare(strict_types = 1);

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


	public function loadConfiguration(): void
	{
		$container = $this->getContainerBuilder();
		$config = $this->getConfig($this->defaults);

		Validators::assertField($config, 'translatesDir', 'string');

		$container->addDefinition($this->prefix('translatesStorage'))
			->setFactory(DirectoryStorage::class, [$config['translatesDir']]);

		$container->addDefinition($this->prefix('translatesLoader'))
			->setFactory(TranslatesLoader::class, [$config['debugMode']]);

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
