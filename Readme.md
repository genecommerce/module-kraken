# Kraken Image Optimisation

This module will optimise all images uploaded to products pages & CMS pages through Kraken, saving vital disk space and increasing page load speeds. 

You can optimise existing images using the `bin/magento gene:kraken:optimise [directory] [skipcache]` command. *Be sure to take a backup of the directory first as the module will overwrite the existing files.*
*Cache files are included by default.*

## Support
This module is provided as open source with no support provided.

## Requirements
1) A [Kraken](https://kraken.io/) account
3) Magento 2.1 and 2.2+ Commerce/OpenSource

## Setup Guide
1) Create an account on [Kraken](https://kraken.io/)
2) Retrieve your [API Credentials](https://kraken.io/account/api-credentials) from the Kraken web interface
3) Install this module using the command `composer require gene/module-kraken`
4) Run `bin/magento setup:upgrade`
5) In the Magento admin, navigate to Stores -> Settings -> Configuration -> GENE Commerce -> Kraken.
6) Enter your API Credentials from step 2 and select "Yes" for the "Enable Kraken Image Optimisation" option.
7) Save your configuration and flush the Magento config cache

## How does it work?
The core Magento class `Magento\Framework\File\Uploader` is overriden in order to send any uploaded images (in their temporary storage state) to Kraken for optimisation ([code reference](https://github.com/genecommerce/module-kraken/blob/develop/Model/Overrides/FrameworkUploader.php#L43)). This ensures any subsequent resizing by the Magento platform is done on the optimised image.

If Kraken does not return a valid response, [the error is logged](https://github.com/genecommerce/module-kraken/blob/develop/Model/Optimise.php#L129) to the core Magento log files. Equally if Kraken does not return a response within 30 seconds the optimisation will be skipped. In all instances, the module fails gracefully in order to ensure the image is still uploaded.

## Authors
This module was built by [GENE Commerce](http://www.gene.co.uk/).

- [Aidan Threadgold (Lead Developer)](https://twitter.com/AidanThreadgold)
