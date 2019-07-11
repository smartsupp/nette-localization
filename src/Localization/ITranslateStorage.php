<?php declare(strict_types = 1);

namespace Smartsupp\Localization;

interface ITranslateStorage
{

	public function getTranslates(string $section, string $lang): array;


	public function getLastChange(string $section, string $lang): int;

}
