<?php

namespace Smartsupp\Localization;

use Nette\DI\CompilerExtension;

class LocalizationExtension extends CompilerExtension
{

	public $defaults = array(
		'translatesDir' => null,
		'debugMode' => null,
		'alias' => null,
		'sections' => null
	);


	public function loadConfiguration()
	{
		$container = $this->getContainerBuilder();
		$config = $this->getConfig($this->defaults);

		$debugMode = $config['debugMode'] === null ? $container->parameters['debugMode'] : $config['debugMode'];

		$container->addDefinition($this->prefix('translatesStorage'))
			->setClass('Smartsupp\Localization\DirectoryStorage')
			->setArguments(array($config['translatesDir']));

		$container->addDefinition($this->prefix('translatesLoader'))
			->setClass('Smartsupp\Localization\TranslatesLoader')
			->addSetup('$debugMode', array($debugMode))
			->addSetup('setTempDir', array('%tempDir%/cache/_Wimo.Localization'));

		$translatorFactory = $container->addDefinition($this->prefix('translatorFactory'))
			->setClass('Smartsupp\Localization\TranslatorFactory')
			->addSetup('$debugMode', array($debugMode));

		if ($config['alias']) {
			foreach ($config['alias'] as $from => $to) {
				$translatorFactory->addSetup('setAlias', array($from, $to));
			}
		}

		if ($config['sections']) {
			$translatorFactory->addSetup('$defaultSections', array($config['sections']));
		}
	}

}
