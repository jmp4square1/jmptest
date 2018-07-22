<?php

/*
 * Global app configuration
 */

return [
    'cron_catalog_update' => '*/1 * * * *',
    'catalog_n_products_x_page' => '20',
    'fast_mode' => false,
    'redis_var_seeds' => 'SQUAREONE:TECHTEST:CRAWLER:SEEDS',
    'redis_channel_download_img' => 'SQUAREONE_GET_IMAGE'
];