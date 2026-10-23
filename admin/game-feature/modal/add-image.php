<?php
require '../../../connect.php';
require _DIR_('library/session/session');

$code = isset($_GET['s']) ? filter($_GET['s']) : '';
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    if(!isset($_SESSION['user'])) exit("No direct script access allowed!");
    if($data_user['level'] !== 'Admin') exit("No direct script access allowed!");
    if(!isset($code) || !$code) exit("No direct script access allowed!");
    $search = $call->query("SELECT * FROM con_game WHERE code = '$code'");
    if($search->num_rows == 0) exit("No data found from the Code!");
    $data = $search->fetch_assoc();
?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.min.css" rel="stylesheet" type="text/css">
<form method="POST" enctype="multipart/form-data">
    <input type="hidden" id="csrf_token" name="csrf_token" value="<?= $csrf_string ?>">
    <input type="hidden" name="web_code" value="<?= $data['code'] ?>">
    <div class="form-group">
        <input type="file"
            name="content"            
            data-max-file-size="3M"
            data-errors-position="outside"
            data-allowed-file-extensions="jpg jpeg webp png"
        class="dropify">
    </div>
    <div class="row">
        <div class="form-group col-6">
            <button type="button" class="btn btn-danger btn-block" data-dismiss="modal"> BACK </button>
        </div>
        <div class="form-group col-6">
            <button type="submit" name="add_image" class="btn btn-primary btn-block"> ADD </button>
        </div>
    </div>
</form>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/js/dropify.min.js"></script>
<script type="text/javascript">
    $('.dropify').dropify({
        error: {
            'fileSize': 'The file size is too big ({{ value }} max).',            
        },
        tpl: {
            wrap:            '<div class="dropify-wrapper"></div>',
            loader:          '<div class="dropify-loader"></div>',
            message:         '<div class="dropify-message"><i class="feather icon-upload-cloud" style="font-size:48px" /> <p>{{ default }}</p></div>',
            preview:         '<div class="dropify-preview"><span class="dropify-render"></span><div class="dropify-infos"><div class="dropify-infos-inner"><p class="dropify-infos-message">{{ replace }}</p></div></div></div>',
            filename:        '<p class="dropify-filename"><span class="file-icon"></span> <span class="dropify-filename-inner"></span></p>',
            clearButton:     '<button type="button" class="dropify-clear">{{ remove }}</button>',
            errorLine:       '<p class="dropify-error">{{ error }}</p>',
            errorsContainer: '<div class="dropify-errors-container"><ul style="font-size:82%;"></ul></div>'
        }
    });
</script>
<?php
} else {
    exit("No direct script access allowed!");
}
?>