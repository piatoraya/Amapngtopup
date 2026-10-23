<?php 
require '../connect.php';
require _DIR_('library/session/auth');
require _DIR_('library/shennboku.app/auth-login');
require _DIR_('library/layout/header.auth');
?>
<section class="row flexbox-container">
    <div class="col-xl-12 col-12 d-flex justify-content-center">
        <div class="card bg-authentication rounded-0 mb-0">
            <div class="row m-0">
                <div class="col-lg-12 col-12 p-0">
                    <div class="card rounded-0 mb-0 px-2">
                        <div class="card-header pb-1">
                            <div class="card-title">
                                <a href="<?= visited() ?>">
                                    <h4 class="mb-0"><i class="feather icon-log-in"></i> Login</h4>
                                </a>
                            </div>
                        </div>
                        <p class="px-2">Welcome back, please login to your account.</p>
                        <?php require _DIR_('library/session/result'); ?>
                        <div class="card-content">
                            <div class="card-body pt-1">
                                <form method="POST">
                                    <input type="hidden" id="csrf_token" name="csrf_token" value="<?= $csrf_string ?>">
                                    <fieldset class="form-label-group form-group position-relative has-icon-left">
                                        <input type="text" class="form-control" name="user" id="user-name" placeholder="Username/Email/Phone(62)">
                                        <div class="form-control-position">
                                            <i class="feather icon-user"></i>
                                        </div>
                                        <label for="user-name">Username/Email/Phone(62)<font color="red">*</font></label>
                                    </fieldset>

                                    <fieldset class="form-label-group position-relative has-icon-left">
                                        <input type="password" class="form-control" name="pass" id="user-password" placeholder="Password">
                                        <div class="form-control-position">
                                            <i class="feather icon-lock"></i>
                                        </div>
                                        <label for="user-password">Password <font color="red">*</font></label>
                                    </fieldset>
                                    <div class="form-group d-flex justify-content-between align-items-center">
                                        <div class="text-left">
                                            <fieldset class="checkbox">
                                                <div class="vs-checkbox-con vs-checkbox-primary">
                                                    <input type="checkbox" name="remember" value="true">
                                                    <span class="vs-checkbox">
                                                        <span class="vs-checkbox--check">
                                                            <i class="vs-icon feather icon-check"></i>
                                                        </span>
                                                    </span>
                                                    <span class="">Remember me</span>
                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="text-right"><a href="<?= base_url('auth/forgot') ?>" class="card-link">Forgot Password?</a></div>
                                    </div>
                                    <a href="<?= base_url('auth/register') ?>" class="btn btn-outline-primary float-left btn-inline">Register</a>
                                    <button name="login" type="submit" class="btn btn-relief-primary float-right btn-inline">Login</button>
                                </form>
                            </div>
                        </div>
                        <div class="login-footer">
                            <div class="divider">
                                <div class="divider-text">VISIT US</div>
                            </div>
                            <div class="footer-btn d-inline text-center">
                                <a href="<?= base_url(); ?>page/product" class="btn btn-outline-primary btn-block btn-inline">PRICE LIST</a>
                                <!--<a href="" target="" class="btn btn-instagram"><span class="fab fa-instagram"></span></a>-->
                                <!--<a href="" target="" class="btn btn-facebook"><span class="fab fa-facebook"></span></a>-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php require _DIR_('library/layout/footer.auth'); ?>