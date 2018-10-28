<?php

session_start();

ini_set('display_errors', 1);
define('MAX_FILE_SIZE', 1 * 1024 * 1024); // 1MB
define('THUMBNAIL_WIDTH', 400);
define('IMAGES_DIR', __DIR__ . '/images');
define('THUMBNAIL_DIR', __DIR__ . '/thumbs');

if (!function_exists('imagecreatetruecolor')) {
  echo 'GD not installed';
  exit;
}

function h($s) {
  return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

require_once 'ImageUploader.php';

$uploader = new \MyApp\ImageUploader();

if($_SERVER['REQUEST_METHOD'] === 'POST') {
  $uploader->upload();
}

list($success, $error) = $uploader->getResults();

$images = $uploader->getImages();

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>Image Uploader</title>
  <style>
    body {
      text-align: center;
      font-family: Arial, sans-serif;
    }
    ul {
      list-style: none;
      margin: 0;
      padding: 0;
    }
    li {
      margin-bottom: 5px;
    }
    input[type=file] {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      cursor: pointer;
      opacity: 0.0;
    }
    .img_upload_btn {
      position: relative;
      display: inline-block;
      width: 300px;
      padding: 7px;
      border-radius: 5px;
      margin: 10px auto 20px;
      color: #fff;
      box-shadow: 0 4px #0088cc;
      background: #00bbff;
    }
    .img_upload_btn:hover {
      opacity: 0.8;
    }
    .msg {
      margin: 0 auto 15px;
      width: 400px;
      font-weight: bold;
    }
    .msg.success {
      color: #4caf50;
    }
    .msg.error {
      color: #f44336;
    }
  </style>
</head>
<body>
  <div class="img_upload_btn">
    <form action="" method="post" enctype="multipart/form-data" id="my_form">
      Upload!
      <input type="hidden" name="MAX_FILE_SIZE" value="<?= h(MAX_FILE_SIZE); ?>">
      <input type="file" name="image" id="my_file">
      <!-- <input type="submit" value="upload"> -->
    </form>
  </div>

  <?php if (isset($success)) : ?>
    <div class="msg success"><?= h($success); ?></div>
  <?php endif; ?>
  <?php if (isset($error)) : ?>
    <div class="msg error"><?= h($error); ?></div>
  <?php endif; ?>

  <ul>
    <?php foreach($images as $image) : ?>
      <li>
        <a href="<?= h(basename(IMAGES_DIR) . '/' . basename($image)) ; ?>">
          <img src="<?= h($image); ?>">
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>

<script>
  $(function() {
    $('.msg').fadeOut(5000);
    $('#my_file').on('change', function() {
      $('#my_form').submit();
    });
  });

</script>
</body>
</html>
