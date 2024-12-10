<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/application/views/system/invoices/erp_invoice_print_cs.php',
    ])
    // uncomment to reach your current PHP version
    ->withPhpSets(true)
    ->withPreparedSets(deadCode: true);
