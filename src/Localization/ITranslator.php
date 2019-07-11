<?php declare(strict_types = 1);

namespace Smartsupp\Localization;

interface ITranslator extends \Nette\Localization\ITranslator
{

	public function setTranslates(array $dictionary): void;


	public function getTranslates(): array;


	public function setParameters(array $parameters): void;


	public function getParameters(): array;

}
