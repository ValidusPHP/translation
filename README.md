# Validus Translation

Provides translations for zend expressive projects.

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/96be235a-c73f-4de0-80bf-881d79f2f04b/big.png)](https://insight.sensiolabs.com/projects/96be235a-c73f-4de0-80bf-881d79f2f04b)

---
[![Packagist](https://img.shields.io/packagist/dm/validus/translation.svg)](https://packagist.org/packages/validus/translation) [![GitHub license](https://img.shields.io/github/license/ValidusPHP/translation.svg)](https://github.com/ValidusPHP/translation/blob/master/LICENSE) [![Build Status](https://travis-ci.org/ValidusPHP/translation.svg?branch=master)](https://travis-ci.org/ValidusPHP/translation) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/ValidusPHP/translation/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/ValidusPHP/translation/?branch=master) [![Code Intelligence Status](https://scrutinizer-ci.com/g/ValidusPHP/translation/badges/code-intelligence.svg?b=master)](https://scrutinizer-ci.com/code-intelligence) [![Coverage Status](https://coveralls.io/repos/github/ValidusPHP/translation/badge.svg)](https://coveralls.io/github/ValidusPHP/translation)

---

Symfony Translation factories for PSR-11 with Zend configuration provider.

## Installation

The easiest way to install this package is through composer:
```bash
$ composer require validus/translation
```

## Configuration

A complete example configuration can be found in example/full-config.php. 
Please note that the values in there are the defaults, and don't have to be supplied when you are not changing them. Keep your own configuration as minimal as possible. A minimal configuration can be found in example/simple-config.php

If your application uses the zend-component-installer Composer plugin, your configuration is complete; the shipped Validus\Translation\ConfigProvider registers the translation service.

## Usage

Validus Translation provides middleware consuming PSR-7 HTTP message instances, via implementation of PSR-15 interfaces.

#### Adding the middleware to your application
you may pipe this middleware anywhere in your application. If you want to have it available anywhere, pipe it early in your application, prior to any routing. As an example, within Expressive, you could pipe it in the config/pipeline.php file:
```php
$app->pipe(Validus\Translation\Middleware\TranslatorMiddleware::class);
```
Within Expressive, you can do this when routing, in your config/routes.php file, or within a delegator factory:
```php
$app->post('/login', [
    Validus\Translation\Middleware\TranslatorMiddleware::class,
    User\Middleware\LoginMiddleware::class
]);
```
#### Accessing the translator 
if you have added the middleware to your application, you can access the translator via the container :
```php
use Symfony\Contracts\Translation\TranslatorInterface;

$translator = $container->get(TranslatorInterface::class);
```

