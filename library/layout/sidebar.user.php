<?php
if(isset($sess_username)) {
if($data_user['level'] == 'Admin') li('Admin','feather icon-bookmark warning','/admin');
li('Home','feather icon-home primary','/');
if($data_user['level'] != 'Basic') ul([
    'name' => 'Premium',
    'icon' => 'feather icon-feather primary',
    'content' => [
        ['name' => 'Transfer Balance','page' => '/premium/transfer'],
        ['name' => 'Make a Voucher','page' => '/premium/voucher'],
        ['name' => 'My Downline','page' => '/premium/downline'],
    ]
]);
li('Benefit','feather icon-command','/page/benefit');

ulhead('Main Menu');
 if(conf('xtra-fitur',4) == 'true') ul([
    'name' => 'Hall of Fame',
    'icon' => 'feather icon-award success',
    'content' => [
        ['name' => 'Deposit','page' => '/page/monthly-rating/deposit'],
       ['name' => 'Pulsa & PPOB','page' => '/page/monthly-rating/pulsa-ppob'],
        (conf('xtra-fitur',3) == 'true') ? ['name' => 'Social Media','page' => '/page/monthly-rating/social-media'] : '',
       (conf('xtra-fitur',2) == 'true') ? ['name' => 'Game Feature','page' => '/page/monthly-rating/game-feature'] : '',
   ]
 ]);   
if(conf('xtra-fitur',2) <> 'true' && conf('xtra-fitur',3) <> 'true') {
       li('History','feather icon-shopping-bag','/order/history/pulsa-ppob');
} else {
    ul([
        'name' => 'History',
        'icon' => 'feather icon-shopping-bag info',
        'content' => [
            #['name' => 'Grafik ','page' => '/order/history/grafik'],
            ['name' => 'Pulsa & PPOB','page' => '/order/history/pulsa-ppob'],
            (conf('xtra-fitur',3) == 'true') ? ['name' => 'Social Media','page' => '/order/history/social-media'] : '',
            (conf('xtra-fitur',2) == 'true') ? ['name' => 'Game Feature','page' => '/order/history/game-feature'] : '',
        ]
    ]);
}
li('Product List','feather icon-tag primary','/page/product');
ul([
    'name' => 'Deposit',
    'icon' => 'feather icon-dollar-sign success',
    'content' => [
        ['name' => 'New','page' => '/deposit/new'],
        ['name' => 'Voucher','page' => '/deposit/voucher'],
        ['name' => 'History','page' => '/deposit/history'],
    ]
]);
ulhead('Help Center');
ul([
    'name' => 'Page',
    'icon' => 'feather icon-map info',
    'content' => [
        ['name' => 'Information Center','page' => '/page/information'],
        ['name' => 'Terms of Service','page' => '/page/terms-of-service'],
        ['name' => 'General Questions','page' => '/page/general-questions'],
        ['name' => 'Contact Admin','page' => '/page/contact'],
        ['name' => 'Delete Account','page' => '/page/delete'],
    ]
]);
if(conf('xtra-fitur',2) <> 'true' && conf('xtra-fitur',3) <> 'true') {
    li('Monitoring','feather icon-monitor','/page/monitoring/pulsa-ppob');
} else {
ul([
    'name' => 'Monitoring',
    'icon' => 'feather icon-monitor primary',
    'content' => [
        ['name' => 'Pulsa & PPOB','page' => '/page/monitoring/pulsa-ppob'],
        (conf('xtra-fitur',3) == 'true') ? ['name' => 'Social Media','page' => '/page/monitoring/social-media'] : '',
        (conf('xtra-fitur',2) == 'true') ? ['name' => 'Game Feature','page' => '/page/monitoring/game-feature'] : '',
    ]
]);
}
ul([
    'name' => 'API Documentation',
    'icon' => 'feather icon-terminal warning',
    'content' => [
        ['name' => 'Profile','page' => '/page/api/profile'],
        ['name' => 'Prepaid','page' => '/page/api/prepaid'],
        (conf('xtra-fitur',1) == 'true') ? ['name' => 'Postpaid','page' => '/page/api/postpaid'] : '',
        (conf('xtra-fitur',3) == 'true') ? ['name' => 'Social Media','page' => '/page/api/social-media'] : '',
        (conf('xtra-fitur',2) == 'true') ? ['name' => 'Game Feature','page' => '/page/api/game-feature'] : '',
    ]
]);
} else {
li('Home','feather icon-home','/');
ulhead('Help Center');
ul([
    'name' => 'Page',
    'icon' => 'feather icon-map',
    'content' => [
        ['name' => 'Terms of Service','page' => '/page/terms-of-service'],
        ['name' => 'General Questions','page' => '/page/general-questions'],
        ['name' => 'Contact Admin','page' => '/page/contact'],
        ['name' => 'Product List','page' => '/page/product'],        
    ]
]);
}
?>