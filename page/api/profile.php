<?php 
require '../../connect.php';
require _DIR_('library/session/session');
require _DIR_('library/layout/header.user');
?>
<div class="sidebar-right sidebar-sticky d-none d-md-block">
    <div class="sidebar">
        <div class="pt-2 pr-2">
            <div class="sidebar-content p-1 affix" data-spy="affix" data-offset-top="-77">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title">Profile</h6>
                    </div>
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            <nav id="sidebar-page-navigation">
                                <ul id="page-navigation-list" class="nav">
                                    <li class="nav-item"><a class="nav-link" href="#detail">Detail</a></li>
                                </ul>
                            </nav>
                        </div>
                        <h6 class="border-top-grey border-top-lighten-2 p-1 mt-1 mb-0">
                            <a class="nav-link display-block text-muted" href="#top">Back to top
                                <i class="float-right fa fa-arrow-circle-o-up font-medium-3"></i>
                            </a>
                        </h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="content-detached content-left">
    <div class="content-body">
        <div class="card">
            <div class="card-header">
                <h4 id="order" class="card-title">Detail</h4>
                <div class="heading-elements">
                    <ul class="list-inline mb-0">
                        <li><a data-action="collapse"><i class="feather icon-chevron-down"></i></a></li>
                        <li><a data-action="expand"><i class="feather icon-maximize"></i></a></li>
                    </ul>
                </div>
            </div>
            <div class="card-content collapse show" aria-expanded="true">
                <div class="card-body">
                    <div class="card-text">
                        <p>[<code>POST</code>] <?= base_url('api/profile') ?></p>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-borderless mb-0">
                            <thead>
                                <tr>
                                    <th>Parameter</th>
                                    <th>Type</th>
                                    <th>Value</th>
                                    <th>Req.</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>key</td>
                                    <td><code>string</code></td>
                                    <td>berisi apikey anda.</td>
                                    <td>Yes</td>
                                </tr>
                                <tr>
                                    <td>sign</td>
                                    <td><code>string</code></td>
                                    <td>berisi formula md5(API ID + API KEY).</td>
                                    <td>Yes</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <h4 class="mt-3">Example Response</h4>
                    <hr>
                    <pre>
                        <code class="language-json">
<?= json_encode([
    'result' => true,
    'data' => [
        'full_name' => isset($data_user) ? $data_user['name'] : 'your full name',
        'username' => isset($data_user) ? $data_user['username'] : 'some1d',
        'balance' => isset($data_user) ? (int)$data_user['balance'] : 0,
        'point' => isset($data_user) ? (int)$data_user['point'] : 0,
        'level' => isset($data_user) ? $data_user['level'] : 'Basic',
        'registered' => isset($data_user) ? $data_user['date'] : $dtme,
    ],
    'message' => 'Successfully got your account details.'
], JSON_PRETTY_PRINT) ?>
                        </code>
                    </pre>
                </div>
            </div>
        </div>
        <!--/ Melakukan Pemesanan Structure -->
    </div>
    <div class="mb-5"></div>
<?php require _DIR_('library/layout/footer.user'); ?>