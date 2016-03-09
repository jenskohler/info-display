<?php
/* (c) 2014 Thomas Smits */
namespace HSMA\InfoDisplay\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Class InfoDisplayExtension
 * @package HSMA\InfoDisplay\DependencyInjection
 *
 * Helper class to load bundle configuration. This avoid changes to the central
 * config.yml file.
 *
 * Although the official Symfony documentation does not mention the need for this
 * file, the local configuration is ignored by the framework unless loaded
 * explicitly by this class.
 */
class InfoDisplayExtension extends Extension {

    public function load(array $configs, ContainerBuilder $container) {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
    }
}
