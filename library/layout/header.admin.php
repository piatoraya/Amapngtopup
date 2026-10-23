<!DOCTYPE html>
<?php $InAdminPage = true; if(!isset($sess_username)) die; ?>
<html class="loading" lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
        <meta content="<?= $_CONFIG['description']; ?>" name="description">
        <meta content="<?= $_CONFIG['keyword']; ?>" name="keyword">
        <meta name="author" content="">
        
        <meta name="robots" content="<?= $_META['robots'] ?>">
        <meta name="revisit-after" content="<?= $_META['revisit'] ?>">
        <meta name="msvalidate.01" content="<?= $_META['bing_site'] ?>">
        <meta name="google-site-verification" content="<?= $_META['google_site'] ?>">
        
        <meta name="Geo.Placename" content="<?= $_META['geo_placename'] ?>">
        <meta name="Geo.Country" content="<?= $_META['geo_country'] ?>">
        
        <meta property="og:title" content="<?= $_CONFIG['title'] ?>">
        <meta property="og:type" content="website">
        <meta property="og:url" content="<?= base_url() ?>">
        <meta property="og:image" content="<?= $_CONFIG['banner'] ?>">
        <meta property="og:image:type" content="image/png">
        <meta property="og:description" content="<?= $_CONFIG['description'] ?>">
        <meta property="og:site_name" content="<?= $_CONFIG['title'] ?>">
        <meta property="og:locale" content="id_ID">
        <meta property="fb:pages" content="291448691554058">
        <meta property="fb:admins" content="100022982940138">
        <meta property="twitter:card" content="Summary">
        <meta property="twitter:url" content="<?= base_url() ?>">
        <meta property="twitter:title" content="<?= $_CONFIG['title'] ?>">
        <meta property="twitter:image" content="<?= $_CONFIG['banner'] ?>">
        <meta property="twitter:creator" content="">
        
        <meta name="apple-mobile-web-app-title" content="<?= $_CONFIG['title']; ?>">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-touch-fullscreen" content="yes">

        <title><?= $_CONFIG['title']; ?></title>
        <link rel="apple-touch-icon" href="<?= $_CONFIG['icon']; ?>">
        <link rel="shortcut icon" type="image/x-icon" href="<?= $_CONFIG['icon']; ?>">
        <link href="<?= assets('fonts/cssed62.css?family=Montserrat:300,400,500,600') ?>" rel="stylesheet">
        
        <!-- Custom Meta -->
        <meta name="DeviceType" content="<?= $device ?>">
        <meta name="UserAgent" content="<?= $user_agent ?>">
        <meta name="IpAddress" content="<?= client_ip() ?>">
        <meta name="Rating" content="General">

        <!-- BEGIN: Vendor CSS-->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/4.7.95/css/materialdesignicons.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css">
        <link rel="stylesheet" type="text/css" href="<?= assets('vendors/css/vendors.css') ?>">
        <link rel="stylesheet" type="text/css" href="<?= assets('vendors/materializeicon/material-icons.css') ?>">
        <link rel="stylesheet" type="text/css" href="<?= assets('vendors/css/charts/apexcharts.css') ?>">
        <link rel="stylesheet" type="text/css" href="<?= assets('vendors/css/extensions/tether-theme-arrows.css') ?>">
        <link rel="stylesheet" type="text/css" href="<?= assets('vendors/css/extensions/tether.min.css') ?>">
        <link rel="stylesheet" type="text/css" href="<?= assets('vendors/css/extensions/shepherd-theme-default.css') ?>">
        <!-- END: Vendor CSS-->

        <!-- BEGIN: Theme CSS-->
        <link rel="stylesheet" type="text/css" href="<?= assets('css/bootstrap.min.css') ?>">
        <link rel="stylesheet" type="text/css" href="<?= assets('css/bootstrap-extended.min.css') ?>">
        <link rel="stylesheet" type="text/css" href="<?= assets('css/colors.min.css') ?>">
        <link rel="stylesheet" type="text/css" href="<?= assets('css/components.min.css') ?>">
        <link rel="stylesheet" type="text/css" href="<?= assets('css/themes/dark-layout.min.css') ?>">
        <link rel="stylesheet" type="text/css" href="<?= assets('css/themes/semi-dark-layout.min.css') ?>">

        <!-- BEGIN: Page CSS-->
        <link rel="stylesheet" type="text/css" href="<?= assets('css/core/menu/menu-types/vertical-menu.min.css') ?>">
        <link rel="stylesheet" type="text/css" href="<?= assets('css/core/colors/palette-gradient.min.css') ?>">
        <link rel="stylesheet" type="text/css" href="<?= assets('css/pages/dashboard-analytics.min.css') ?>">
        <link rel="stylesheet" type="text/css" href="<?= assets('css/pages/card-analytics.min.css') ?>">
        <link rel="stylesheet" type="text/css" href="<?= assets('css/pages/authentication.css') ?>">
        <link rel="stylesheet" type="text/css" href="<?= assets('css/pages/app-ecommerce-shop.min.css') ?>">
        <link rel="stylesheet" type="text/css" href="<?= assets('css/pages/faq.min.css') ?>">
        <link rel="stylesheet" type="text/css" href="<?= assets('vendors/css/documentation.css') ?>">
        <link rel="stylesheet" type="text/css" href="<?= assets('vendors/css/ui/prism.min.css') ?>">
        <link rel="stylesheet" type="text/css" href="<?= assets('vendors/css/ui/prism-treeview.css') ?>">
        <!-- END: Page CSS-->

        <!-- third party css -->
        <link href="<?= assets('libs/datatables/dataTables.bootstrap4.css') ?>" rel="stylesheet" type="text/css">
        <link href="<?= assets('libs/datatables/responsive.bootstrap4.css') ?>" rel="stylesheet" type="text/css">
        <link href="<?= assets('libs/datatables/buttons.bootstrap4.css') ?>" rel="stylesheet" type="text/css">
        <link href="<?= assets('libs/datatables/select.bootstrap4.css') ?>" rel="stylesheet" type="text/css">
        <link href="<?= assets('libs/magnific-popup/magnific-popup.css') ?>" rel="stylesheet" type="text/css" />
        <link href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.min.css" rel="stylesheet" type="text/css">
        <!-- third party css end -->
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

        <!-- BEGIN: Custom CSS & JS -->
        <link rel="stylesheet" type="text/css" href="<?= assets('css/style.css') ?>">
        <style type="text/css">
<?php require _DIR_('library/custom.min'); ?>
        </style>
<?php require _DIR_('library/custom.js'); ?>
        <!-- END: Custom CSS & JS -->

    </head>
    <body class="vertical-layout vertical-menu-modern 2-columns navbar-floating footer-static" data-open="click" id="Click-GPLV3" data-menu="vertical-menu-modern" data-col="content-right-sidebar">
        <nav class="header-navbar navbar-expand-lg navbar navbar-with-menu floating-nav navbar-light navbar-shadow">
            <div class="navbar-wrapper">
                <div class="navbar-container content">
                    <div class="navbar-collapse" id="navbar-mobile">
                        <div class="mr-auto float-left bookmark-wrapper d-flex align-items-center">
                            <ul class="nav navbar-nav">
                                <li class="nav-item mobile-menu d-xl-none mr-auto"><a class="nav-link nav-menu-main menu-toggle hidden-xs" href="#"><i class="ficon feather icon-menu"></i></a></li>
                            </ul>
                        </div>
                        <ul class="nav navbar-nav float-right">
                            <li class="nav-item d-none d-lg-block"><a class="nav-link nav-link-expand"><i class="ficon feather icon-maximize"></i></a></li>
                            <li class="dropdown dropdown-notification nav-item">
                                <a class="nav-link nav-link-label" href="#" data-toggle="dropdown">
                                    <i class="ficon feather icon-bell"></i>
                                    <!--<span class="badge badge-pill badge-primary badge-up">5</span>-->
                                </a>
                                <ul class="dropdown-menu dropdown-menu-media dropdown-menu-right">
                                    <li class="dropdown-menu-header">
                                        <div class="dropdown-header m-0 p-2">
                                            <h3 class="white">Notifications</h3><span class="notification-title">App Notifications</span>
                                        </div>
                                    </li>
                                    <li class="scrollable-container media-list">
                                        <?php
                                        $LogSearch = $call->query("SELECT * FROM logs WHERE user = '$sess_username' ORDER BY id DESC LIMIT 5");
                                        while($RowLog = $LogSearch->fetch_assoc()) {
                                            $RowLogIcon = 'feather icon-hash';
                                            if($RowLog['type'] == 'register') $RowLogIcon = 'feather icon-user-check';
                                            if($RowLog['type'] == 'login') $RowLogIcon = 'feather icon-log-in';
                                            if($RowLog['type'] == 'topup') $RowLogIcon = 'feather icon-credit-card';
                                            if($RowLog['type'] == 'order') $RowLogIcon = 'feather icon-shopping-cart';
                                            if($RowLog['type'] == 'logout') $RowLogIcon = 'feather icon-log-out';
                                        ?>
                                        <a class="d-flex justify-content-between" href="javascript:void(0)">
                                            <div class="media d-flex align-items-start">
                                                <div class="media-left"><i class="<?= $RowLogIcon.' font-medium-5 '.$RowLog['color'] ?>"></i></div>
                                                <div class="media-body">
                                                    <h6 class="<?= $RowLog['color'] ?> media-heading"><?= $RowLog['text'] ?></h6>
                                                    <small class="notification-text"><?= $RowLog['ip'].' - '.$RowLog['loc'] ?></small>
                                                </div>
                                                <small>
                                                    <time class="media-meta" datetime="<?= str_replace(' ','T',$RowLog['date']).'+07:00' ?>">
                                                        <?= timeAgo($RowLog['date']) ?>
                                                    </time>
                                                </small>
                                            </div>
                                        </a>
                                        <?php } ?>
                                    </li>
                                    <li class="dropdown-menu-footer"><a class="dropdown-item p-1 text-center" href="<?= base_url('account/activity') ?>">Read all notifications</a></li>
                                </ul>
                            </li>
                            <li class="dropdown dropdown-user nav-item">
                                <a class="dropdown-toggle nav-link dropdown-user-link" href="#" data-toggle="dropdown">
                                    <div class="user-nav d-sm-flex d-none">
                                        <span class="user-name text-bold-600"><?php//= $data_user['username'] ?></span>
                                        <span class="user-status"><?php//= 'Active' ?></span>
                                    </div>
                                    <span><img class="round" src="<?= $avatar ?>" alt="avatar" height="40" width="40"></span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item" href="<?= base_url('account/profile') ?>"><i class="feather icon-user"></i> Edit Profile</a>
                                    <a class="dropdown-item" href="<?= base_url('account/notification') ?>"><i class="feather icon-bell"></i> Notifications</a>
                                    <a class="dropdown-item" href="<?= base_url('account/signin-access') ?>"><i class="feather icon-layers"></i> Sign Access</a>
                                    <a class="dropdown-item" href="<?= base_url('account/mutation') ?>"><i class="feather icon-mail"></i> Mutations</a>
                                    <a class="dropdown-item" href="<?= base_url('account/activity') ?>"><i class="feather icon-check-square"></i> Log</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="<?= base_url('auth/logout') ?>"><i class="feather icon-power"></i> Logout</a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
        <div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
            <div class="navbar-header">
                <ul class="nav navbar-nav flex-row">
                    <li class="nav-item mr-auto"><a class="navbar-brand" href="<?= base_url() ?>">
                            <div class=""></div>
                            <h2 class="brand-text mb-0"><?= $_CONFIG['navbar']; ?></h2>
                        </a></li>
                    <li class="nav-item nav-toggle"><a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse"><i class="feather icon-x d-block d-xl-none font-medium-4 primary toggle-icon"></i><i class="toggle-icon feather icon-disc font-medium-4 d-none d-xl-block collapse-toggle-icon primary" data-ticon="icon-disc"></i></a></li>
                </ul>
            </div>
            <div class="shadow-bottom"></div>
            <div class="main-menu-content">
                <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
                    <?php require 'sidebar.admin.php'; ?>
                </ul>
            </div>
        </div>
        <div class="app-content content">
            <div class="content-overlay"></div>
            <div class="header-navbar-shadow"></div>
            <div class="content-wrapper">
                <div class="content-header row">
                </div>
                <div class="content-body">
                    <section id="dashboard-analytics">
<?php require _DIR_('library/session/result'); ?>
