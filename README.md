# bitfinex-api-php

## Features

* Official implementation
* REST v2 API

## Installation
```bash
  composer require bitfinex-api-php
```

## Usage

```php
require __DIR__ . '/vendor/autoload.php';

use BFX\RESTv2;

$restV2 = new RESTv2([
  'apiKey' => '...',
  'apiSecret' => '...',
  'transform' => true
]);

try {
  $res = $restV2->userInfo();
  print_r($res);
} catch (\Throwable $ex) {
  var_dump($ex->getMessage());
}
```

## Testing
```bash
composer run-script test
```

## Docs

Documentation at [https://docs.bitfinex.com/v2/reference](https://docs.bitfinex.com/v2/reference)

