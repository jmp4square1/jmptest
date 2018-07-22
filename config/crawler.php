<?php


/*
 * Somes standard crawler configurations
 */

return [
    'timeOutSeconds' => 5,
    'appliances' => [
        'base_targets_url' => [
            'https://www.appliancesdelivered.ie/small-appliances' ,
            'https://www.appliancesdelivered.ie/dishwashers',
            /*
            'https://www.appliancesdelivered.ie/garden-diy', // Uncomment if you want more products :)
            'https://www.appliancesdelivered.ie/floorcare' // Uncomment if you want more products :)
             */
        ],
        'url_x_hit' => 5
    ]
    
];