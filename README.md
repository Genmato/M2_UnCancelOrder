Genmato Un-Cancel Order extension for Magento2
====

This extension will give you the option to process a canceled order by setting it back to processing and update the inventory.

Installation
====

This package is registered on [Packagist](https://packagist.org/packages/genmato/uncancelorder) for easy installation. In your Magento installation root run:

`composer require genmato/uncancelorder`

This will install the latest version in your Magento installation, when completed run:

```
php bin/magento module:enable Genmato_UnCancelOrder

php bin/magento setup:upgrade

php bin/magento cache:clean
```

This will enable the extension within your installation.

Upgrades
====

When there is a updated version available, simply run (in your Magento installation root) to download and install the updated version:

`composer update`

