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

## Configuration

In configuration you can select Doctrine backend. Default settings are:

``` yml
# app/config/config.yml
pecserke_twig_doctrine_loader:
    backend: orm    # valid options are: orm, mongodb
    manager_name: default   # doctrine object manager to use with templates
    template_class: 'Pecserke\Bundle\TwigDoctrineLoaderBundle\Model\Template'   # template model class, must be subclass of this default class
    cache_prefix: ~ # used as a part of cache key
```

## Creating templates

In order to be able to load templates form database you have to create some first:

``` php
use Pecserke\Bundle\TwigDoctrineLoaderBundle\Model\Template;

$template = new Template();
$template->setName('demo_template.html.twig');
$template->setSource('Hi {{ name }}. This is simple demo Twig template.');

$manager->persist($template);
$manager->flush();

// ...
```

## Using templates

Now that you have created some templates, the next step is to use them.
You can get most out of this bundle, if you use it with
[Symfony Standard Edition](https://github.com/symfony/symfony-standard):

``` php
// src/Acme/Bundle/DemoBundle/Controller/DemoController.php

namespace Acme\Bundle\DemoBundle\Controller\DemoController;

import Symfony\FrameworkBundle\Controller\Controller;

class DemoController extends Controller
{
    public function demoAction()
    {
        return $this->render('demo_template.html.twig', array('name' => 'Tomas'));
    }
}
```

You can also use this bundle without full Symfony:

``` php
$twig = $container->get('twig');
$content = $twig->render('demo_template.html.twig', array('name' => 'Tomas'));
```
