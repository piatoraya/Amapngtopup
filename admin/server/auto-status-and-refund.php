<?php
require '../../connect.php';
require _DIR_('library/session/admin');
require _DIR_('library/cron/_key');
require _DIR_('library/layout/header.admin');
?>
<div class="row">
    <div class="col-12 col-md-6">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title">Status Last Activity</h4>
                <hr>
                <div class="form-group">
                    <label style="font-weight:bold">
                        Game Feature
                        [<a href="<?= base_url('library/cron/status-game?key='.SHENN_CRON_KEY) ?>" target="_blank">CHECK</a>]
                    </label>
                    <input type="text" class="form-control" value="<?= format_date('en', conf('status-refund-access', 4)) ?>" disabled>
                </div>
                <hr>
                <div class="form-group">
                    <label style="font-weight:bold">
                        Pulsa PPOB
                        [<a href="<?= base_url('library/cron/status-ppob?key='.SHENN_CRON_KEY) ?>" target="_blank">CHECK</a>]
                    </label>
                    <input type="text" class="form-control" value="<?= format_date('en', conf('status-refund-access', 5)) ?>" disabled>
                </div>
                <hr>
                <div class="form-group">
                    <label style="font-weight:bold">
                        Social Media
                        [<a href="<?= base_url('library/cron/status-socmed?key='.SHENN_CRON_KEY) ?>" target="_blank">CHECK</a>]
                    </label>
                    <input type="text" class="form-control" value="<?= format_date('en', conf('status-refund-access', 6)) ?>" disabled>
                </div>
                <hr>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title">Refund Last Activity</h4>
                <hr>
                <div class="form-group">
                    <label style="font-weight:bold">
                        Game Feature
                        [<a href="<?= base_url('library/cron/refund-game?key='.SHENN_CRON_KEY) ?>" target="_blank">CHECK</a>]
                    </label>
                    <input type="text" class="form-control" value="<?= format_date('en', conf('status-refund-access', 1)) ?>" disabled>
                </div>
                <hr>
                <div class="form-group">
                    <label style="font-weight:bold">
                        Pulsa PPOB
                        [<a href="<?= base_url('library/cron/refund-ppob?key='.SHENN_CRON_KEY) ?>" target="_blank">CHECK</a>]
                    </label>
                    <input type="text" class="form-control" value="<?= format_date('en', conf('status-refund-access', 2)) ?>" disabled>
                </div>
                <hr>
                <div class="form-group">
                    <label style="font-weight:bold">
                        Social Media
                        [<a href="<?= base_url('library/cron/refund-socmed?key='.SHENN_CRON_KEY) ?>" target="_blank">CHECK</a>]
                    </label>
                    <input type="text" class="form-control" value="<?= format_date('en', conf('status-refund-access', 3)) ?>" disabled>
                </div>
                <hr>
            </div>
        </div>
    </div>
</div>
<?php require _DIR_('library/layout/footer.user'); ?>