<?php
require '../../connect.php';
require _DIR_('library/session/admin');
require _DIR_('library/shennboku.app/admin-config-website');
require _DIR_('library/layout/header.admin');
?>
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Web Config</h4>
      </div>
      <div class="card-body">
        <ul class="nav nav-tabs" role="tablist">
          <li class="nav-item">
            <a class="nav-link active" id="web-tab" data-toggle="tab" href="#web" aria-controls="web" role="tab" aria-selected="true">Website</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="mail-tab" data-toggle="tab" href="#mail" aria-controls="mail" role="tab" aria-selected="false">SMTP Mailer</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="hold-tab" data-toggle="tab" href="#hold" aria-controls="hold" role="tab" aria-selected="false">Hold Balance</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="media-tab" data-toggle="tab" href="#media" aria-controls="media" role="tab" aria-selected="false">Social Media</a>
          </li>
        </ul>
        <div class="tab-content">

          <div class="tab-pane active" id="web" aria-labelledby="web-tab" role="tabpanel">
            <form method="POST">
              <input type="hidden" id="csrf_token" name="csrf_token" value="<?= $csrf_string ?>">
              <div class="row">
                <div class="form-group col-12 col-md-7">
                  <label>Title</label>
                  <input type="text" class="form-control" name="title" value="<?= $_CONFIG['title'] ?>">
                </div>
                <div class="form-group col-12 col-md-5">
                  <label>Nav Title</label>
                  <input type="text" class="form-control" name="navbar" value="<?= $_CONFIG['navbar'] ?>">
                </div>
              </div>
              <div class="form-group">
                <label>Description</label>
                <textarea class="form-control" rows="5" name="description"><?= $_CONFIG['description'] ?></textarea>
                </div>
                <div class="form-group">
                  <label>Keyword</label>
                  <textarea class="form-control" rows="5" name="keyword"><?= $_CONFIG['keyword'] ?></textarea>
                  </div>
                  <div class="row">
                    <div class="form-group col-12 col-md-6">
                      <label>reCAPTCHA Site</label>
                      <input type="text" class="form-control" name="recsite" value="<?= $_CONFIG['reCAPTCHA']['site'] ?>">
                    </div>
                    <div class="form-group col-12 col-md-6">
                      <label>reCAPTCHA Secret</label>
                      <input type="text" class="form-control" name="recsecret" value="<?= $_CONFIG['reCAPTCHA']['secret'] ?>">
                    </div>
                  </div>
                  <div class="form-group">
                    <label>Banner Image</label>
                    <input type="text" class="form-control" name="banner" value="<?= $_CONFIG['banner'] ?>">
                    <small class="text-danger">*For og:image and twitter:image in metadata</small>
                  </div>
                  <div class="form-group">
                    <label>Icon / Logo</label>
                    <input type="text" class="form-control" name="icon" value="<?= $_CONFIG['icon'] ?>">
                  </div>
                  <div class="form-group text-right">
                    <button type="submit" name="reset" class="btn btn-danger waves-effect waves-light">Reset</button>
                    <button type="submit" name="save" class="btn btn-primary waves-effect waves-light">Submit</button>
                  </div>
                </form>
              </div>

              <div class="tab-pane" id="mail" aria-labelledby="mail-tab" role="tabpanel">
                <form method="POST">
                  <input type="hidden" id="csrf_token" name="csrf_token" value="<?= $csrf_string ?>">
                  <div class="form-group">
                    <label>Host</label>
                    <input type="text" class="form-control" name="mailer_host" value="<?= conf('mailer',1) ?>" placeholder="">
                  </div>
                  <div class="form-group">
                    <label>Username</label>
                    <input type="text" class="form-control" name="mailer_user" value="<?= conf('mailer',2) ?>" placeholder="">
                  </div>
                  <div class="form-group">
                    <label>Password</label>
                    <input type="text" class="form-control" name="mailer_pass" value="<?= conf('mailer',3) ?>" placeholder="">
                  </div>
                  <div class="form-group">
                    <label>Set Mail From</label>
                    <input type="text" class="form-control" name="mailer_from" value="<?= conf('mailer',4) ?>" placeholder="">
                  </div>
                  <div class="form-group text-right">
                    <button type="submit" name="reset" class="btn btn-danger waves-effect waves-light">Reset</button>
                    <button type="submit" name="save-smtp" class="btn btn-primary waves-effect waves-light">Submit</button>
                  </div>
                </form>
              </div>

              <div class="tab-pane" id="hold" aria-labelledby="hold-tab" role="tabpanel">
                <form method="POST">
                  <input type="hidden" id="csrf_token" name="csrf_token" value="<?= $csrf_string ?>">
                  <div class="form-group">
                    <label>Basic</label>
                    <input type="number" class="form-control" name="hold_basic" value="<?= conf('hold-balance',1) ?>">
                  </div>
                  <div class="form-group">
                    <label>Premium</label>
                    <input type="number" class="form-control" name="hold_premium" value="<?= conf('hold-balance',2) ?>">
                  </div>
                  <div class="form-group">
                    <label>Admin</label>
                    <input type="number" class="form-control" name="hold_admin" value="<?= conf('hold-balance',3) ?>">
                  </div>
                  <div class="form-group text-right">
                    <button type="submit" name="reset" class="btn btn-danger waves-effect waves-light">Reset</button>
                    <button type="submit" name="save-hold" class="btn btn-primary waves-effect waves-light">Submit</button>
                  </div>
                </form>
              </div>

              <div class="tab-pane" id="media" aria-labelledby="media-tab" role="tabpanel">
                <form method="POST">
                  <input type="hidden" id="csrf_token" name="csrf_token" value="<?= $csrf_string ?>">
                  <div class="form-group">
                    <label>Facebook</label>
                    <input type="text" class="form-control" name="social_fb" value="<?= conf('webscmd',1) ?>" placeholder="">
                  </div>
                  <div class="form-group">
                    <label>Instagram</label>
                    <input type="text" class="form-control" name="social_ig" value="<?= conf('webscmd',2) ?>" placeholder="">
                  </div>
                  <div class="form-group text-right">
                    <button type="submit" name="reset" class="btn btn-danger waves-effect waves-light">Reset</button>
                    <button type="submit" name="save-media" class="btn btn-primary waves-effect waves-light">Submit</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
<?php require _DIR_('library/layout/footer.user'); ?>