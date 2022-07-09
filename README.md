# InitPHP EventEmitter

This library has been designed to emit events in its simplest and simplest form.

## Requirements

- PHP 5.6 or higher

## Installation

```
composer require initphp/event-emitter
```

or **Manuel Installation :**

Download this repository. And include the `src/Init.php` file in your project.

## Usage

```php
require_once "vendor/autoload.php";
use InitPHP\EventEmitter\EventEmitter;

$events = new EventEmitter();

$events->on('hello', function ($name) {
    echo 'Hello ' . $name . '!' . PHP_EOL;
}, 99);

$events->on('hello', function ($name) {
    echo 'Hi ' . $name . '!' . PHP_EOL;
}, 10);

// Emit
$events->emit('hello', ['World']);
```

_Output :_

```
Hi World!
Hello World!
```

## Credits

- [Muhammet ŞAFAK](https://www.muhammetsafak.com.tr) <<info@muhammetsafak.com.tr>>

## License

Copyright &copy; 2022 [MIT License](./LICENSE)