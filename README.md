# Visual Craft Utils
A collection of useful PHP classes

## Install
```sh
composer require visual-craft/utils
```

## Components

### StringInterpolator
```php
$interpolator = new \VisualCraft\Utils\StringInterpolator\StringInterpolator();
$interpolator->interpolate('Demonstration $var1 $var2. An${var3}er example \$var4', [
    'var1' => 'of',
    'var2' => 'interpolation',
    'var3' => 'oth',
]);
// Will return:
// "Demonstration of interpolation. Another example $var4"

$interpolator->getNames('Demonstration $var1 $var2. An${var3}er example \$var4');
// Will return:
// array (
//   0 => 'var1',
//   1 => 'var2',
//   2 => 'var3',
// )
```

### TextBlockManager

### CliArgsParser

Class used for parsing command line arguments coming from PHP $argv global variable

```php
$parser = new \VisualCraft\Utils\CliArgsParser\CliArgsParser();

// $self - script name
// $args - arguments
// $opts - options
list($self, $args, $opts) = $parser->parse($argv);
```

## Unit tests
```sh
composer install --dev
vendor/bin/kahlan
```

## License
MIT
