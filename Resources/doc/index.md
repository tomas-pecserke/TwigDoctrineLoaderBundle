Getting Started With PecserkeTwigDoctrineLoaderBundle
=====================================================

[Twig](http://twig.sensiolabs.org/) is a powerful templating engine.
This bundle allows you to load these templates from database using [Doctrine](http://www.doctrine-project.org/).

## Prerequisites

This version of the bundle requires [TwigBundle 2.2+](https://packagist.org/packages/symfony/twig-bundle)
and [Composer](http://getcomposer.org/).

## Installation

Add PecserkeTwigDoctrineLoaderBundle in your composer.json:

``` js
{
    "require": {
        "pecserke/twig-doctrine-loader-bundle": "1.0@dev"
    }
}
```

Now tell composer to download the bundle by running the command:

``` bash
$ php composer.phar update pecserke/twig-doctrine-loader-bundle
```

Composer will install the bundle into your project's `vendor/pecserke` directory.

Enable the bundle in the kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Pecserke\Bundle\TwigDoctrineLoaderBundle\PecserkeTwigDoctrineLoaderBundle(),
    );
}
```
