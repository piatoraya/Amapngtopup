                    </section>
                </div>
                <div class="mb-5"></div>
            </div>
        </div>
        <div class="mb-3 d-md-none">
            <div class="sidenav-overlay"></div>
            <div class="drag-target"></div>
        </div>
<?php
$page_load = explode(' ', microtime());
$page_load = $page_load[1] + $page_load[0];
$page_load_finish = $page_load;
$page_load_total = round(($page_load_finish - $page_load_start), 4);
?>
        <footer class="footer d-none d-md-block footer-static footer-light">
            <p class="clearfix blue-grey lighten-2 mb-0">
                <span class="float-md-left d-block d-md-inline-block mt-25">
                    2018 - <?= date('Y') ?> &copy; <a class="text-primary darken-2" href="https://lann.my.id" target="_blank"><b>Lann ID</b>,</a>All rights Reserved
                </span>
                <span class="float-md-right d-none d-md-block">Page Load: <?= $page_load_total ?>s.<i class="feather icon-heart pink"></i></span>
                <button class="btn btn-primary btn-icon scroll-top" type="button"><i class="feather icon-arrow-up"></i></button>
            </p>
        </footer>
        
        <?php if(!isset($InAdminPage) && isset($sess_username)) { ?>
        <div id="footer-bar" class="footer-bar-6 d-flex d-md-none" style="transform: translate(0px, 0px);">
            
            <a href="<?= base_url('account/mutation') ?>" class="<?= lifoot('/account/mutation') ?>">
                <i class="fas fa-file-invoice-dollar"></i><span>Mutation</span><em></em>
            </a>
            <a href="<?= base_url('order/history/pulsa-ppob') ?>" class="<?= lifoot('order/history/pulsa-ppob') ?>">
                <i class="fas fa-file-invoice-dollar"></i><span>History</span><em></em>
            </a>
           
            <!--<a href="<?= base_url() ?>" class=" <?= lifoot('/') ?>">-->
            <!--    <i class="fa fa-home"></i><span>Home</span><strong><u></u></strong><em></em>-->
            <!--</a>-->
            <a href="<?= base_url('') ?>" class="circle-nav <?= lifoot('/') ?>">
                <i class="fa fa-home"></i><span>Home</span><strong><u></u></strong><em></em>
            </a>
            
               <a href="javascript:;" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                <i class="fa fa-credit-card"></i><span>Deposit</span><em></em>
            <div class="dropdown-menu" style="background-color: rgba(255, 255, 255, 0.7);"><a href="javascript:;" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                    </a>
                        <a class="dropdown-item" href="<?= base_url('deposit/new') ?>">New Deposit</a>
                        <a class="dropdown-item" href="<?= base_url('deposit/history') ?>">History Deposit</a>
                        
            </div></a> 
           
            <a href="<?= base_url('account/profile') ?>" class="<?= lifoot('/account/profile') ?>">
                <i class="fa fa-address-book"></i><span>Me</span><em></em>
            </a>
        </div>
        <?php } ?>
        
        <!-- BEGIN: Vendor JS-->
        <script src="<?= assets('vendors/js/vendors.min.js') ?>"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.5.4/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
        <!-- BEGIN Vendor JS-->

        <!-- BEGIN: Page Vendor JS-->
        <script src="<?= assets('vendors/js/charts/apexcharts.min.js') ?>"></script>
        <script src="<?= assets('vendors/js/extensions/tether.min.js') ?>"></script>
        <?php if(isset($sess_username)) { ?>
        <script src="<?= assets('vendors/js/extensions/geoDevices.min.js') ?>"></script>
        <?php } ?>
        <!-- END: Page Vendor JS-->

        <!-- Plugins js-->
        <script src="<?= assets('libs/flatpickr/flatpickr.min.js') ?>"></script>

        <!-- third party js -->
        <script src="<?= assets('libs/datatables/jquery.dataTables.min.js') ?>"></script>
        <script src="<?= assets('libs/datatables/dataTables.bootstrap4.js') ?>"></script>
        <script src="<?= assets('libs/datatables/dataTables.responsive.min.js') ?>"></script>
        <script src="<?= assets('libs/datatables/responsive.bootstrap4.min.js') ?>"></script>
        <script src="<?= assets('libs/datatables/dataTables.buttons.min.js') ?>"></script>
        <script src="<?= assets('libs/datatables/buttons.bootstrap4.min.js') ?>"></script>
        <script src="<?= assets('libs/datatables/buttons.html5.min.js') ?>"></script>
        <script src="<?= assets('libs/datatables/dataTables.keyTable.min.js') ?>"></script>
        <script src="<?= assets('libs/datatables/dataTables.select.min.js') ?>"></script>
        <script src="<?= assets('libs/magnific-popup/jquery.magnific-popup.min.js') ?>"></script>
        <!-- third party js ends -->

        <!-- Dropify js -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/js/dropify.min.js"></script>
        <script type="text/javascript">$('.dropify').dropify();</script>
        <!-- Dropify js ends -->
        
        <!-- Clipboard js -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.4/clipboard.min.js"></script>
        <script type="text/javascript">var clipboard = new ClipboardJS('.copy');</script>
        <!-- Clipboard js ends -->

        <!-- BEGIN: Theme JS-->
        <script src="<?= assets('js/core/app-menu.min.js') ?>"></script>
        <script src="<?= assets('js/core/app.min.pretest.js') ?>"></script>
        <script src="<?= assets('js/scripts/components.min.js') ?>"></script>
        <script src="<?= assets('js/scripts/customizer.min.js') ?>"></script>
        <script src="<?= assets('js/scripts/footer.min.js') ?>"></script>
        <script src="<?= assets('js/pages/gallery.init.js') ?>"></script>
        <script src="<?= assets('js/scripts/modal/components-modal.min.js') ?>"></script>
        <script src="<?= assets('js/scripts/extensions/swiper.min.js') ?>"></script>
        <script src="<?= assets('js/main.js') ?>"></script>
        <script src="<?= assets('js/swiper.min.js') ?>"></script>
        <script src="<?= assets('js/scripts/show-password.min.js') ?>"></script>
        <!-- END: Theme JS-->
        
        <!-- BEGIN: Page JS-->
        <script src="<?= assets('js/scripts/pages/user-profile.min.js') ?>"></script>
        <script src="<?= assets('js/scripts/pages/app-ecommerce-shop.min.js') ?>"></script>
        <script src="<?= assets('js/scripts/pages/faq-kb.min.js') ?>"></script>
        <script src="<?= assets('js/scripts/documentation.js') ?>" type="text/javascript"></script>
        <script src="<?= assets('vendors/js/ui/prism.min.js') ?>" type="text/javascript"></script>
        <script src="<?= assets('vendors/js/ui/prism-treeview.js') ?>" type="text/javascript"></script>
        <script src="<?= assets('vendors/js/ui/affix.js') ?>" type="text/javascript"></script>
        <script src="<?= assets('js/scripts/cards/card-statistics.min.js') ?>"></script>
        <!-- END: Page JS--><?php if($_CONFIG['firebase'] == 'true') { ?>
        
        
        <!-- BEGIN: Firebase JS-->
        <script src="https://www.gstatic.com/firebasejs/<?= conf('firebase',7) ?>/firebase-app.js"></script>
        <script src="https://www.gstatic.com/firebasejs/<?= conf('firebase',7) ?>/firebase-messaging.js"></script>
        <script>
            firebase.initializeApp({'messagingSenderId': '<?= conf('firebase',4) ?>','apiKey': '<?= conf('firebase',1) ?>','projectId': '<?= conf('firebase',2) ?>','appId': '<?= conf('firebase',5) ?>'});
            const messaging = firebase.messaging();
            messaging.requestPermission().then(function () { return messaging.getToken() }).then(function(token) {
                $.post(base_url + "library/track-firebase",{token: token,validate: 'ShennEcosystem2021'});
            }).catch(function (err) { console.log("Unable to get permission to notify.", err); });
            messaging.onMessage(function (payload) {
                console.log("Shenn Ecosystem detected new notification.");
                const ShennNotificationData = JSON.parse(payload.data.notification);
                if(Notification.permission === "granted") {
                    var notification = new Notification(ShennNotificationData.title, {body: ShennNotificationData.body});
                    notification.onclick = function (ev) { ev.preventDefault(); notification.close(); }
                }
            });
        </script>
        <!-- END: Firebase JS-->
    <?php } ?>
    
    </body>
</html>