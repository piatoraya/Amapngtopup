<?php
li('Back','feather icon-home warning','/');
li('Home','feather icon-umbrella warning','/admin/');
ul([
    'name' => 'Users',
    'icon' => 'feather icon-user info',
    'content' => [
        ['name' => 'Manage Users','page' => '/admin/users/manage'],
        ['name' => 'Locked Users','page' => '/admin/users/locked'],
        ['name' => 'Activity Logs','page' => '/admin/users/activity'],
        ['name' => 'Balance Mutation','page' => '/admin/users/mutation'],
        ['name' => 'Transfer History','page' => '/admin/users/transfer'],
    ]
]);
ulhead('');
li('Provider','feather icon-cpu danger','/admin/provider/');
ul([
    'name' => 'Pulsa PPOB',
    'icon' => 'feather icon-cloud primary',
    'content' => [
        ['name' => 'Transaction','page' => '/admin/pulsa-ppob/transaction'],
        ['name' => 'Category','page' => '/admin/pulsa-ppob/category'],
        ['name' => 'Service','page' => '/admin/pulsa-ppob/service'],
        ['name' => 'Report','page' => '/admin/pulsa-ppob/report'],
        ['name' => 'Point','page' => '/admin/pulsa-ppob/point'],
    ]
]);
ul([
    'name' => 'Social Media',
    'icon' => 'feather icon-cloud primary',
    'content' => [
        ['name' => 'Transaction','page' => '/admin/social-media/transaction'],
        ['name' => 'Category','page' => '/admin/social-media/category'],
        ['name' => 'Service','page' => '/admin/social-media/service'],
        ['name' => 'Report','page' => '/admin/social-media/report'],
        ['name' => 'Point','page' => '/admin/social-media/point'],
    ]
]);
ul([
    'name' => 'Game Feature',
    'icon' => 'feather icon-cloud primary',
    'content' => [
        ['name' => 'Configuration','page' => '/admin/game-feature/configuration'],
        ['name' => 'Transaction','page' => '/admin/game-feature/transaction'],
        ['name' => 'Category','page' => '/admin/game-feature/category'],
        ['name' => 'Service','page' => '/admin/game-feature/service'],
        ['name' => 'Report','page' => '/admin/game-feature/report'],
        ['name' => 'Point','page' => '/admin/game-feature/point'],
    ]
]);
ul([
    'name' => 'Reports',
    'icon' => 'feather icon-file-text primary',
    'content' => [
        ['name' => 'Sales','page' => '/admin/report/sales'],
        ['name' => 'Profit','page' => '/admin/report/profit'],
        ['name' => 'Deposit','page' => '/admin/report/deposit'],
        ['name' => 'Summary','page' => '/admin/report/summary'],
    ]
]);
ulhead('');
ul([
    'name' => 'Deposit',
    'icon' => 'feather icon-credit-card info',
    'content' => [
        ['name' => 'Manage','page' => '/admin/deposit/manage'],
        ['name' => 'Voucher','page' => '/admin/deposit/voucher'],
        ['name' => 'Method','page' => '/admin/deposit/method'],
        #['name' => 'Deposit REQ','page' => '/admin/deposit/deposit-req'],
        
    ]
]);
ul([
    'name' => 'Configuration',
    'icon' => 'feather icon-settings danger',
    'content' => [
        ['name' => 'Website','page' => '/admin/config/website'],
        ['name' => 'Firebase','page' => '/admin/config/firebase'],
        #['name' => 'SMTP Mailer','page' => '/admin/config/smtp'],
        #['name' => 'Social Media','page' => '/admin/config/social'],
        #['name' => 'Hold Balance','page' => '/admin/config/hold'],
        ['name' => 'Web Pages','page' => '/admin/config/pages'],
        ['name' => 'Benefit','page' => '/admin/config/benefit'],
        ['name' => 'Others','page' => '/admin/config/others'],
        ['name' => 'Broadcast','page' => '/admin/config/member-toast'],
    ]
]);
ul([
    'name' => 'Server',
    'icon' => 'feather icon-box primary',
    'content' => [
        ['name' => 'Status & Refund Act.','page' => '/admin/server/auto-status-and-refund'],
        ['name' => 'Error Log','page' => '/admin/server/error-log'],
        #['name' => 'Broadcast Email','page' => '/admin/server/broadcast-email'],
    ]
]);
ul([
    'name' => 'Others',
    'icon' => 'feather icon-list info',
    'content' => [
        ['name' => 'News & Info','page' => '/admin/others/news/'],
        ['name' => 'Index Banner','page' => '/admin/others/banner/'],
        ['name' => 'General Quest','page' => '/admin/others/faq/'],
        ['name' => 'Deposit Feature','page' => '/admin/others/deposit/'],
    ]
]);
//li('Transaction Notifier','feather icon-archive','/admin/addon/transaction-notifier');
?>