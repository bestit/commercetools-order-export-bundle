# ct-order-export-bundle
Symfony 3 Bundle to export commercetools orders based on templates and a virtual file system.

## Installation

An example implementation can be found in [bestit/commercetools-order-export](https://github.com/bestit/commercetools-order-export).

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require bestit/ct-order-export-bundle
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...

            new \Oneup\FlysystemBundle\OneupFlysystemBundle(),
            new \BestIt\CtOrderExportBundle\BestItCtOrderExportBundle()
        );

        // ...
    }

    // ...
}
```

### Step 3: Configuration of the bundle

```yaml
best_it_ct_order_export:
    commercetools_client:  # Required
        id:                   ~ # Required
        secret:               ~ # Required
        project:              ~ # Required
        scope:                ~ # Required

    # Please provide the service id for your flysystem file system.
    filesystem:           ~ # Required
    
    # Please provide the service id for your logging service.
    logger:               logger
    orders:

        # Should we use a paginated list of orders (or is the result list changing by "itself")?
        with_pagination:      true

        # Add where clauses for orders: https://dev.commercetools.com/http-api-projects-orders.html#query-orders
        default_where:        []
        
        # Which template is used for the export of a single order?
        file_template:        detail.xml.twig

        # Provide an order field name or a format string for the date function enclosed with {{ and }}.
        name_scheme:          'order_{{id}}_{{YmdHis}}.xml'
```

## Use the bundle

### Shell-Command

The bundle provides you with a shell command:

```
$ php bin/console order-export:export-orders [-v|-vv|-vv] [-q]
```

### Events

This bundle provides two events at this moment:

1. **order_export.preOrderExport**: This event is triggered with an 
**BestIt\CtOrderExportBundle\Event\PrepareOrderExportEvent**-Instance. Use the API for the exportData to get more 
exportable data.
2. **order_export.postOrderExport**: The event instance of type **BestIt\CtOrderExportBundle\Event\FinishOrderExportEvent** 
allows you to manpulate an order after the export.  

## Further Todos
* Unittesting
* Added a list template
* Make heavier use of events
