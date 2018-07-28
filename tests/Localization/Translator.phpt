<?php

use Smartsupp\Localization;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

$translator = new Localization\Translator();
$translator->setTranslates([
	'foo' => 'Message no args',
	'bar' => 'Message with args {name} {value}',
	'params' => 'Message with args {name} {param}',
	'params_deep' => 'Message with args {name} {param_deep}',
	'params_deep_missing' => 'Message with args {name} {param_deep_missing}',
]);
$translator->setParameters([
	'param' => 'value',
	'param_deep' => 'deep_{param}',
	'param_deep_missing' => 'deep_{param_missing}',
]);

// missing translate (any text)
$text = $translator->translate('Any text');
Assert::equal('Any text', $text);

// missing translate (dot notation)
$text = $translator->translate('text.missing');
Assert::equal('|text.missing|', $text);

// only args
$text = $translator->translate('foo');
Assert::equal('Message no args', $text);

$text = $translator->translate('bar', ['name' => 'tester']);
Assert::equal('Message with args tester {value}', $text);

$text = $translator->translate('bar', ['name' => 'tester', 'value' => 1]);
Assert::equal('Message with args tester 1', $text);

// with params
$text = $translator->translate('params');
Assert::equal('Message with args {name} value', $text);

$text = $translator->translate('params_deep');
Assert::equal('Message with args {name} deep_value', $text);

$text = $translator->translate('params_deep_missing');
Assert::equal('Message with args {name} deep_{param_missing}', $text);

// with params and args
$text = $translator->translate('params', ['name' => 'tester']);
Assert::equal('Message with args tester value', $text);

$text = $translator->translate('params', ['name' => 'tester', 'param' => 'foo']);
Assert::equal('Message with args tester foo', $text);
