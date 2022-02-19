<?php
require_once 'app/require.php';

session_start();

if (isset($_SESSION["username"]) && !isset($_GET["f"])) {
    header("location: dashboard");
}
?>

<html>
<?php
// USERS COUNT
$users = "SELECT * FROM users";
$result = mysqli_query($db, $users);
$user_count = mysqli_num_rows($result);

// UPLOAD COUNT
$uploads = "SELECT * FROM uploads";
$result = mysqli_query($db, $uploads);
$upload_count = mysqli_num_rows($result);

// BANNED COUNT
$banned = "SELECT * FROM users WHERE banned='true'";
$result = mysqli_query($db, $banned);
$banned_count = mysqli_num_rows($result);

if (isset($_GET["invite"])) {
    $invitecode = $_GET["invite"];
    $query = "SELECT * FROM `invites` WHERE `inviteCode`='$invitecode'";
    $result = mysqli_query($db, $query);
    $row = mysqli_fetch_assoc($result);
    if (mysqli_num_rows($result) > 0) {
        $_SESSION["inviteCode"] = $invitecode;
        $giftAuthor = $row["inviteAuthor"];
        echo "<head>
        <meta name='theme-color' content='#6605ed'>
        <meta name='og:site_name' content='https://dev.clynt.de/'>
        <meta property='og:title' content='C-Cloud | Invite Code' />
        <meta property=og:url content='https://dev.clynt.de/invite/$invitecode' />
        <meta property='og:type' content='website' />
        <meta property='og:description' content='$giftAuthor invited you to C-Cloud!'/>
        <meta content='https://dev.clynt.de/images/logo.png' property='og:image'>
        </head>";
    } else {
        echo "<head>
        <meta name='theme-color' content='#ff0303'>
        <meta name='og:site_name' content='https://dev.clynt.de/'>
        <meta property='og:title' content='C-Cloud | Invite Code' />
        <meta property=og:url content='https://dev.clynt.de/invite/$invitecode' />
        <meta property='og:type' content='website' />
        <meta property='og:description' content='Invite code not found!'/>
        <meta content='https://dev.clynt.de/images/logo.png' property='og:image'>
        </head>";
    }
}

$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != "off" ? "https" : "http" . "://";
function human_filesize($bytes, $decimals)
{
    $size = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f ", $bytes / pow(1024, $factor)) . @$size[$factor];
}

if (isset($_GET["f"])) {
    $string = $_GET["f"];
    if (strlen($string) > 20) {
      $string = urlencode($string);
      $sql = "SELECT * FROM `uploads` WHERE `hash_filename`='$string'";
      $result = mysqli_query($db, $sql);
      $upload = mysqli_fetch_assoc($result);
      $filename = $upload["filename"];
      $type = strrchr($filename, '.');
      $type = str_replace(".", "", $type);
      $title = $upload['embed_title'];
      $description = $upload['embed_desc'];
      $author = $upload['embed_author'];
      $color = $upload['embed_color'];
      $username = $upload['username'];
      $self_destruct_upload = $upload['self_destruct_upload'];
      $uploaded_at = $upload['uploaded_at'];
      $role = $upload['role'];
      $delete_secret = $upload['delete_secret'];
      $original_filename = $upload['original_filename'];
      $show_filesize = 0;
      $sql534534534 = "SELECT * FROM `users` WHERE username='" . $username . "';";
      $result3123121 = mysqli_query($db, $sql534534534);
      $upload432423423 = mysqli_fetch_assoc($result3123121);
      $uuid = $upload432423423["uuid"];
      $files = scandir('uploads/' . $uuid . '/' . $username);
      $sql213 = "SELECT * FROM `users` WHERE username='" . $username . "';";
      $views = $upload['views'];
      $result123 = mysqli_query($db, $sql213);
      $result1234 = mysqli_fetch_assoc($result123);
      $banned = $result1234["banned"];
      $upload_background = $result1234["upload_background"];
      $upload_background_toggle = $result1234["upload_background_toggle"];
      $useridentification = $result1234["uuid"];
      header("Location: https://dev.clynt.de/$filename");
      exit;
    } else {
      $type = strrchr($string, '.');
      $type = str_replace(".", "", $type);
      $sql = "SELECT * FROM `uploads` WHERE `filename`='" . $string . "';";
      $result = mysqli_query($db, $sql);
      $upload = mysqli_fetch_assoc($result);
      $filename = $upload["filename"];
      $title = $upload['embed_title'];
      $description = $upload['embed_desc'];
      $author = $upload['embed_author'];
      $color = $upload['embed_color'];
      $username = $upload['username'];
      $self_destruct_upload = $upload['self_destruct_upload'];
      $uploaded_at = $upload['uploaded_at'];
      $role = $upload['role'];
      $delete_secret = $upload['delete_secret'];
      $original_filename = $upload['original_filename'];
      $show_filesize = 0;
      $sql534534534 = "SELECT * FROM `users` WHERE username='" . $username . "';";
      $result3123121 = mysqli_query($db, $sql534534534);
      $upload432423423 = mysqli_fetch_assoc($result3123121);
      $uuid = $upload432423423["uuid"];
      $files = scandir('uploads/' . $uuid . '/' . $username);
      $sql213 = "SELECT * FROM `users` WHERE username='" . $username . "';";
      $views = $upload['views'];
      $result123 = mysqli_query($db, $sql213);
      $result1234 = mysqli_fetch_assoc($result123);
      $banned = $result1234["banned"];
      $upload_background = $result1234["upload_background"];
      $upload_background_toggle = $result1234["upload_background_toggle"];
      $useridentification = $result1234["uuid"];
    }
    if ($self_destruct_upload == "true" && $views >= 2) {
      if (strpos($user_agent, "Discordbot")) {
        die("fuck off discord");
      } else {
        unlink("uploads/$uuid/$username/" . $filename);
        $query = "SELECT * FROM users WHERE username='$username'";
        $result = mysqli_query($db, $query);
        if (mysqli_num_rows($result) > 0) {
          while ($row = mysqli_fetch_assoc($result)) {
            $uploads = "" . $row["uploads"] . "" - 1;
          }
        } else {
          echo "0 results";
        }
        $query = "UPDATE users SET uploads=$uploads WHERE username='" . $username . "';";
        $result = mysqli_query($db, $query);
        $query = "DELETE FROM `uploads` WHERE `delete_secret`='" . $delete_secret . "';";
        $result = mysqli_query($db, $query);
        die();
      }
    }
    foreach ($files as $file) {
      if ($file == $_GET["f"]) {
        $filesize = human_filesize(filesize('uploads/' . $uuid . '/' . $username . "/" . $upload["filename"]), 2);
  
?>
      <head>
        <link rel="icon" type="image/png" href="assets/images/icons/favicon.ico" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="apple-mobile-web-app-capable" content="yes" />

        <meta name="apple-mobile-web-app-status-bar-style" content="black" />
        <link rel="apple-touch-icon" href="https://dev.clynt.de/images/logo.png" />

        <link rel="apple-touch-startup-image" href="https://dev.clynt.de/images/logo.png" />
        <link rel="stylesheet" type="text/css" href="assets/fonts/font-awesome-4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" type="text/css" href="assets/fonts/iconic/css/material-design-iconic-font.min.css">
        <link rel="stylesheet" type="text/css" href="/css/util.css">
        <link rel="stylesheet" type="text/css" href="/css/main.css">
        <script src="assets/js/main.js"></script>
        <title><?php echo $_GET["f"]; ?></title>
        <meta property="og:type" content="website">
        <meta name='og:site_name' content='<?php echo $author; ?>'>
        <meta name='twitter:title' content='<?php echo $title; ?>'>
        <?php if ($type == "png" || $type == "gif" || $type == "jpeg" || $type == "jpg") : ?>
          <meta name='twitter:card' content='summary_large_image'>
          <meta name="twitter:description" property="og:description" itemprop="description" content="<?php echo $description; ?>">
          <meta name='twitter:image' content='<?php echo "/uploads/$useridentification/$username/" . $filename; ?>'>
        <?php
        elseif ($type == "mp4" || $type == "webm") : ?>
          <meta name='twitter:card' content='player'>
          <meta name="twitter:description" content="<?php echo $description; ?>">
          <meta name='twitter:player' content='<?php echo "https://dev.clynt.de/uploads/$useridentification/$username/" . $_GET["f"]; ?>'>
          <meta name='twitter:player:width' content='1920'>
          <meta name='twitter:player:height' content='1080'>
        <?php
        elseif ($type == "zip") : ?>
          <meta name='twitter:card' content='summary_large_image'>
          <meta name="twitter:description" property="og:description" itemprop="description" content="<?php echo $description; ?>">
          <meta name='twitter:title' content='<?php echo $title; ?>'>
          <meta name='twitter:image' content='https://2.bp.blogspot.com/-y7kOjodOxZ4/WwN4y6vH1KI/AAAAAAAABc0/svTSsHj8DIgIcg0Iz3FZrxlTB_WI5tnBACLcBGAs/s1600/Filetype%2B-%2BZIP.png'>
        <?php
        elseif ($type == "rar") : ?>
          <meta name='twitter:card' content='summary_large_image'>
          <meta name="twitter:description" property="og:description" itemprop="description" content="<?php echo $description; ?>">
          <meta name='twitter:title' content='<?php echo $title; ?>'>
          <meta name='twitter:image' content='https://cdn.iconscout.com/icon/free/png-512/winrar-3-569260.png'>
        <?php
        elseif ($type == "torrent") : ?>
          <meta name='twitter:card' content='summary_large_image'>
          <meta name="twitter:description" property="og:description" itemprop="description" content="<?php echo $description; ?>">
          <meta name='twitter:title' content='<?php echo $title; ?>'>
          <meta name='twitter:image' content='https://storage.googleapis.com/multi-static-content/previews/artage-io-thumb-85273b178575b7f4a314356a42d61a1f.png'>
        <?php
        elseif ($type == "exe") : ?>
          <meta name='twitter:card' content='summary_large_image'>
          <meta name="twitter:description" property="og:description" itemprop="description" content="<?php echo $description; ?>">
          <meta name='twitter:title' content='<?php echo $title; ?>'>
          <meta name='twitter:image' content='https://aux2.iconspalace.com/uploads/448592549.png'>
        <?php
        elseif ($type == "wav") : ?>
          <meta name='twitter:card' content='summary_large_image'>
          <meta name="twitter:description" property="og:description" itemprop="description" content="<?php echo $description; ?>">
          <meta name='twitter:title' content='<?php echo $title; ?>'>
          <meta name='twitter:image' content='https://icons-for-free.com/iconfiles/png/512/file+format+wav+icon-1320184498602933828.png'>
        <?php
        elseif ($type == "mp3") : ?>
          <meta name='twitter:card' content='summary_large_image'>
          <meta name="twitter:description" property="og:description" itemprop="description" content="<?php echo $description; ?>">
          <meta name='twitter:title' content='<?php echo $title; ?>'>
          <meta name='twitter:image' content='https://cdn.discordapp.com/attachments/811038560240795678/833604882702663720/HF_9d-G7DKJl1cVG5DZM-ZOV6KlrJzMhIWC7R9-wwRtyQdlcrOm0zhyXFmRHm0PUKayJbzKXNCo1NyQvV2nXyOLTBKbzsXAnJ_B4.png'>
        <?php
        elseif ($type == "js") : ?>
          <meta name='twitter:card' content='summary_large_image'>
          <meta name="twitter:description" property="og:description" itemprop="description" content="<?php echo $description; ?>">
          <meta name='twitter:title' content='<?php echo $title; ?>'>
          <meta name='twitter:image' content='https://raw.githubusercontent.com/voodootikigod/logo.js/master/js.png'>
        <?php
        elseif ($type == "py") : ?>
          <meta name='twitter:card' content='summary_large_image'>
          <meta name="twitter:description" property="og:description" itemprop="description" content="<?php echo $description; ?>">
          <meta name='twitter:title' content='<?php echo $title; ?>'>
          <meta name='twitter:image' content='https://stickler.de/images/2000px-Python-logo-notext.png'>
        <?php
        elseif ($type == "css") : ?>
          <meta name='twitter:card' content='summary_large_image'>
          <meta name="twitter:description" property="og:description" itemprop="description" content="<?php echo $description; ?>">
          <meta name='twitter:title' content='<?php echo $title; ?>'>
          <meta name='twitter:image' content='https://cdn.iconscout.com/icon/free/png-256/css-118-569410.png'>
        <?php
        elseif ($type == "html") : ?>
          <meta name='twitter:card' content='summary_large_image'>
          <meta name="twitter:description" property="og:description" itemprop="description" content="<?php echo $description; ?>">
          <meta name='twitter:title' content='<?php echo $title; ?>'>
          <meta name='twitter:image' content='https://images.vexels.com/media/users/3/166383/isolated/preview/6024bc5746d7436c727825dc4fc23c22-html-programmiersprachen-symbol-by-vexels.png'>
        <?php
        elseif ($type == "cs") : ?>
          <meta name='twitter:card' content='summary_large_image'>
          <meta name="twitter:description" property="og:description" itemprop="description" content="<?php echo $description; ?>">
          <meta name='twitter:title' content='<?php echo $title; ?>'>
          <meta name='twitter:image' content='https://sharobella.at/images/icons/15935290281.png'>

        <?php
        else : ?>
          <meta name='twitter:card' content='summary_large_image'>
          <meta name="twitter:description" property="og:description" itemprop="description" content="<?php echo $description; ?>">
          <meta name='twitter:title' content='<?php echo $title; ?>'>
        <?php
        endif; ?>
        <meta name='theme-color' content='<?php echo $color; ?>'>
        <meta name='og:description' content='<?php echo $description; ?>'>
      </head>

      <body>
        <div class="loader-wrapper">
          <span class="loader"><span class="loader-inner"></span></span>
        </div>

        <div id='body_div'>
          <style>
            .hljs {
              display: block;
              overflow-x: auto;
              padding: .5em;
              background: #131313;
              border-radius: 15px;
              text-align: left;
              color: #fff;
            }

            .g-recaptcha {
              padding: 12px 30%;
            }
            .btn32143:hover {
              background-color: grey;
            }
          </style>
          <?php
          if ($upload_background_toggle == "true") {
            echo "<div class='bg-image'></div><div class='container-login100' style='background: 0;z-index: 2;position: absolute;  top: 0%;
            left: 0%;'>";
          } else {
            echo "<div class='container-login100'>";
          }
          ?>
          <div class="wrap-login100" style="filter: opacity(0.98); box-shadow: 0 1px 100px rgb(0 0 0 / 30%), 0 15px 12px rgb(0 0 0 / 22%);">
            <span class="login100-form-title p-b-26">
              <div class="card">
                <?php
                if ($self_destruct_upload == "true") {
                  echo "
                  <div class='card' <div class='card-body'><h3 class='card-text' style='color: red;'>This upload self destructs if you leave this site</h3></div><br>
                  <hr class='rounded'>
                  <br>";
                }
                if ($banned == "true") {
                  echo "<div class='flex'>
                     <p class='flex-child-small' style='color: white'>This user is banned from C-CLoud.</p>
                  </div>";
                  die();
                } else if ($banned == "false") {
                  echo "<div class='flex'>
                     <p class='flex-child-small' style='color: white'><strong>Uploader:</strong><br>$username ($role)</p>
                     <p class='flex-child-small' style='color: white'><strong>File Name:</strong><br>" . $_GET['f'] . "</p>
                  </div>
                  <div class='flex'>
                        <p class='flex-child-small'><a style='color: white;'><strong>File Size:</strong><br>$filesize</a></p>
                        <p class='flex-child-small' style='color: white'><strong>Original File Name:</strong><br>" . $original_filename . "</p>
                  </div>
                  <div class='flex'>
                        <p class='flex-child-small' ><a style='color: white;'><strong>Uploaded at:</strong><br>$uploaded_at</a></p>
                        <p class='flex-child-small' ><a style='color: white;'><strong>Views:</strong><br>$views</a></p>
                  </div>
                  
                  <br><hr class='rounded'><br>
                  ";
                  if ($type == 'png' || $type == 'gif' || $type == 'jpeg' || $type == 'jpg') {
                    $path = 'uploads/' . $uuid . '/' . $username . "/" . $_GET['f'];
                    echo "<div class='img-container'>
                        <a href='https://dev.clynt.de/" . 'uploads/' . $uuid . '/' . $username . "/" . $_GET['f'] . "'> <img src='https://dev.clynt.de/" . 'uploads/' . $uuid . '/' . $username . "/" . $_GET['f'] . "'></img></a>
                     </div>";
                  } else if ($type == "mp4") {
                    echo "<style>
                        .wrap-login100 {
                           width: auto;
                           background: #1b1b1b;
                           border-radius: 20px;
                           overflow: hidden;
                           padding: 33px 33px 33px 33px;
                           box-shadow: 0 5px 10px 0px rgb(0 0 0 / 10%);
                           -moz-box-shadow: 0 5px 10px 0px rgba(0, 0, 0, 0.1);
                           -webkit-box-shadow: 0 5px 10px 0px rgb(0 0 0 / 10%);
                           -o-box-shadow: 0 5px 10px 0px rgba(0, 0, 0, 0.1);
                           -ms-box-shadow: 0 5px 10px 0px rgba(0, 0, 0, 0.1);
                        }
                     </style>
                  <div class='video-container'>
                     <video width='640' height='480' controls>
                        <source src='/uploads/$uuid/$username/" . $_GET['f'] . "' type='video/mp4'>
                     </video>
                  </div>";
                  }
                }

                ?>
                <?php if ($type == 'png' || $type == 'gif' || $type == 'jpeg' || $type == 'jpg') : ?>
                  <span>

                  <?php
                elseif ($type == "mov" || $type == "MOV" && $banned == "false") : ?>
                    <style>
                      .wrap-login100 {
                        width: auto;
                        background: #1b1b1b;
                        border-radius: 20px;
                        overflow: hidden;
                        padding: 33px 33px 33px 33px;
                        box-shadow: 0 1px 100px rgb(0 0 0 / 30%), 0 15px 12px rgb(0 0 0 / 22%);
                      }
                    </style>
                    <div class="video-container">
                      <video width="640" height="480" controls>
                        <source src="<?php echo '/' . 'uploads/' . $uuid . '/' . $username . '/' . $_GET['f'] ?>" type="video/mov">
                      </video>
                    </div>

                  <?php
                elseif ($type == "webm" && $banned == "false") : ?>
                    <style>
                      .wrap-login100 {
                        width: auto;
                        background: #1b1b1b;
                        border-radius: 20px;
                        overflow: hidden;
                        padding: 33px 33px 33px 33px;
                        box-shadow: 0 1px 100px rgb(0 0 0 / 30%), 0 15px 12px rgb(0 0 0 / 22%);
                      }
                    </style>
                    <div class="video-container">
                      <video width="640" height="480" controls>
                        <source src="<?php echo "/" . 'uploads/' . $uuid . '/' . $username . "/" . $_GET['f'] ?>" type="video/webm">
                      </video>
                    </div>
                  <?php
                elseif ($type == "mp3" && $banned == "false") : ?>
                    <style>
                      .wrap-login100 {
                        width: auto;
                        background: #1b1b1b;
                        border-radius: 20px;
                        overflow: hidden;
                        padding: 33px 33px 33px 33px;
                        box-shadow: 0 1px 100px rgb(0 0 0 / 30%), 0 15px 12px rgb(0 0 0 / 22%);
                      }
                    </style>
                    <div class="audio-container">
                      <br>
                      <audio controls>
                        <source src="<?php echo "/" . 'uploads/' . $uuid . '/' . $username . "/" . $_GET['f'] ?>" type="audio/mp3">
                      </audio>
                    </div>
                  <?php
                elseif ($type == "ogg" && $banned == "false") : ?>
                    <style>
                      .wrap-login100 {
                        width: auto;
                        background: #1b1b1b;
                        border-radius: 20px;
                        overflow: hidden;
                        padding: 33px 33px 33px 33px;
                        box-shadow: 0 1px 100px rgb(0 0 0 / 30%), 0 15px 12px rgb(0 0 0 / 22%);
                      }
                    </style>
                    <div class="audio-container">
                      <br>
                      <audio controls>
                        <source src="<?php echo "/" . 'uploads/' . $uuid . '/' . $username . "/" . $_GET['f'] ?>" type="audio/ogg">
                      </audio>
                    </div>
                  <?php
                elseif ($type == "wav" && $banned == "false") : ?>
                    <style>
                      .wrap-login100 {
                        width: auto;
                        background: #1b1b1b;
                        border-radius: 20px;
                        overflow: hidden;
                        padding: 33px 33px 33px 33px;
                        box-shadow: 0 1px 100px rgb(0 0 0 / 30%), 0 15px 12px rgb(0 0 0 / 22%);
                      }
                    </style>
                    <div class="audio-container">
                      <br>
                      <audio controls>
                        <source src="<?php echo "/" . 'uploads/' . $uuid . '/' . $username . "/" . $_GET['f'] ?>" type="audio/wav">
                      </audio>
                    </div>
                  <?php
                endif; ?>
                  </span>
                  <!-- Block parent element -->
              </div>
              <br>
              <a class='btn32143' href='/<?php echo 'uploads/' . $uuid . '/' . $username . "/" . $_GET['f'] ?>' download><strong>Download</strong></a>
          </div>
          </span>
        </div>
        </div>

        </div>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/tilt.js/1.2.1/tilt.jquery.min.js"></script>
        <script src="https://unpkg.com/tilt.js@1.2.1/dest/tilt.jquery.min.js"></script>
      </body>
  <?php
    }
  }
} else { ?>

  <head>
    <?php
    if (!isset($_GET["invite"])) {
      echo "<meta name='theme-color' content='#6605ed'>
            <meta name='og:site_name' content='https://dev.clynt.de/'>
            <meta property='og:title' content='C-Cloud' />
            <meta property='og:url' content='https://dev.clynt.de/' />
            <meta property='og:type' content='website' />
            <meta property='og:description' content='A Free File Uploader for all of your Files.' />
            <meta property='og:locale' content='en_GB' />
            <meta content='https://dev.clynt.de/images/logo.png' property='og:image'>";
    }
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>

      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
      <meta name="description" content="">
      <meta name="author" content="">
      <meta name='theme-color' content='#6605ed'>
      <link rel="icon" type="image/png" href="https://dev.clynt.de/assets/images/icons/favicon.ico">
      <meta name='og:site_name' content='https://dev.clynt.de/'>
      <meta property='og:title' content='C-Cloud' />
      <meta property='og:url' content='https://dev.clynt.de/' />
      <meta property='og:type' content='website' />
      <meta property='og:description' content='A Free File Uploader for all of your Files.' />
      <meta property='og:locale' content='en_GB' />
      <meta content='https://dev.clynt.de/images/logo.png' property='og:image'>
      <link href="https://fonts.googleapis.com/css?family=Raleway:100,300,400,500,700,900" rel="stylesheet">
      <script async src="https://arc.io/widget.min.js#3uop4387"></script>
      <!-- Additional CSS Files -->
      <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">

      <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.css">

      <link rel="stylesheet" href="assets/css/newdesign.css">

    </head>

  <body>
    <!-- <div id="preloader">
      <div class="jumper">
        <div></div>
        <div></div>
        <div></div>
      </div>
    </div> -->
    <div class="welcome-area" id="welcome">
      <div class="header-text">
        <div class="container">
          <div class="row">
            <div class="offset-xl-3 col-xl-6 offset-lg-2 col-lg-8 col-md-12 col-sm-12">
              <h1><strong>C-Cloud</strong></h1>
              <p>The best, free to use File Hosting service out there. With it's customizablity you can change how your Expirience is like, with no limitations at all.</p>
              <a href="#" data-toggle="modal" data-target="#loginModal" id="loginOpen" class="main-button-slider">Login</a>
              <a href="#" data-toggle="modal" data-target="#registerModal" id="registerOpen" class="main-button-slider">Register</a>
              <a href="https://discord.gg/5uBDggq75b" style="background-color: #7289DA;" class="main-button-slider-discord">Discord</a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <svg id="waves" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 1440 140" style="transform: rotate(180deg);">
      <defs>
        <filter id="shadow">
          <feDropShadow dx="0" dy="-2" flood-color="black" flood-opacity="0.5" stdDeviation="6"></feDropShadow>
        </filter>
      </defs>
      <path d="M685.6,52.8C418.7,2.9,170.2,23.9,0,44v96h1440V44C1252.7,66.2,1010,113.4,685.6,52.8z"></path>
    </svg>
    <section class="section home-feature">
      <div class="container">
        <div class="row">
          <div class="offset-lg-3 col-lg-6">
            <div class="info" style="text-align: center; color: #fff; padding-top: 20px; padding-bottom: 20px;">
                <h1 style="text-align: center; color: #fff; padding-top: 20px; padding-bottom: 20px;">Statistics</h1>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-12">
            <div class="row">
              <div class="col-lg-4 col-md-6 col-sm-6 col-12" data-scroll-reveal="enter bottom move 50px over 0.6s after 0.2s">
                <div class="features-small-item">
                  <div class="icon">
                    <img style="height: 40px;margin-top: 28%;" src="http://simpleicon.com/wp-content/uploads/users.png" alt="">
                  </div>
                  <h5 class="features-title">Users</h5>
                  <p><?php echo $user_count ?></p>
                </div>
              </div>
              <div class="col-lg-4 col-md-6 col-sm-6 col-12" data-scroll-reveal="enter bottom move 50px over 0.6s after 0.4s">
                <div class="features-small-item">
                  <div class="icon">
                    <img style="height: 40px;margin-top: 28%;" src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/6b/OOjs_UI_icon_upload.svg/1024px-OOjs_UI_icon_upload.svg.png" alt="">
                  </div>
                  <h5 class="features-title">Uploads</h5>
                  <p><?php echo $upload_count ?></p>
                </div>
              </div>
              <div class="col-lg-4 col-md-6 col-sm-6 col-12" data-scroll-reveal="enter bottom move 50px over 0.6s after 0.6s">
                <div class="features-small-item">
                  <div class="icon">
                    <img style="height: 40px;margin-top: 28%;" src="http://cdn.onlinewebfonts.com/svg/img_154127.png" alt="">
                  </div>
                  <h5 class="features-title">Banned Users</h5>
                  <p><?php echo $banned_count ?></p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <footer>
      <div class="container">
        <div class="row">
          <div class="col-lg-12">
            <p class="copyright">Copyright &copy; 2022 C-Cloud</p>
          </div>
        </div>
      </div>
      <?php include('errors.php'); ?>
      <div class="modal fade" id="registerModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header border-bottom-0">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="form-title text-center">
                <img src="https://dev.clynt.de/assets/images/icons/favicon.ico" style="height: 128px; margin-bottom: 40px; border: 1px solid #3e3e3e; border-radius: 15px;">
                <h4 style="color: #fff;">Register</h4>
              </div>
              <div class="d-flex flex-column text-center">
                <form method="post" action="register" autocomplete="off">
                  <div class="form-group">
                    <input autocomplete="false" type="username" class="form-control" name="username" id="username" placeholder="Enter Username">
                  </div>
                  <div class="form-group">
                    <input autocomplete="off" type="email" class="form-control" name="email" id="email" placeholder="Enter Email">
                  </div>
                  <div class="form-group">
                    <input autocomplete="off" type="password" class="form-control" id="password_1" name="password_1" placeholder="Enter Password">
                  </div>
                  <div class="form-group">
                    <input autocomplete="off" type="password" class="form-control" id="password_2" name="password_2" placeholder="Enter Password">
                  </div>
                  <div class="form-group">
                    <input type="invite" class="form-control" id="key" name="key" placeholder="Enter Invite Code" value="<?php echo $_SESSION["inviteCode"]; ?>">
                  </div>
                  <button type="submit" name="reg_user" class="main-button-slider">Register</button>
                </form>
              </div>
            </div>
          </div>
        </div>
    </footer>


    <div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header border-bottom-0">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-title text-center">
              <img src="c-cloud.rocks/images/icons/favicon.ico" style="height: 128px; margin-bottom: 40px; border: 1px solid #3e3e3e; border-radius: 15px;">
              <h4 style="color: #fff;">Login</h4>
            </div>
            <div class="d-flex flex-column text-center">
              <form method="post" action="login">
                <div class="form-group">
                  <input type="username" class="form-control" name="username" id="username" placeholder="Enter Username">
                </div>
                <div class="form-group">
                  <input type="password" class="form-control" id="password" name="password" placeholder="Enter Password">
                  <p style="color: grey; text-align: right;">
                    Forgot your Password? <a href="reset" style="color: white; text-align: right;">Reset</a>
                  </p>
                </div>
                <button type="submit" name="login_user" class="main-button-slider">Login</button>
              </form>
            </div>
          </div>
        </div>
      </div>
      <script src="assets/js/jquery-2.1.0.min.js"></script>

      <script src="assets/js/popper.js"></script>
      <script src="assets/js/bootstrap.min.js"></script>

      <script src="assets/js/scrollreveal.min.js"></script>
      <script src="assets/js/waypoints.min.js"></script>
      <script src="assets/js/jquery.counterup.min.js"></script>
      <script src="assets/js/imgfix.min.js"></script>
      <script src="assets/js/custom.js"></script>
      <style>
        @media (min-width: 576px) {
            .modal-dialog 
            .modal-content {
            padding: 1rem;
            max-width: 400px;
            }
        }

        .modal-backdrop.show {
          opacity: .7;
        }

        .text-info {
          color: #ffffff !important;
        }

        .form-control {
          display: block;
          width: 100%;
          height: calc(1.5em + .75rem + 2px);
          padding: .375rem .75rem;
          font-size: 1rem;
          font-weight: 400;
          line-height: 1.5;
          color: #ffffff;
          background-color: #131313;
          background-clip: padding-box;
          border: 1px solid #444444;
          border-radius: 10px;
          transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
        }

        .form-control:hover {
          display: block;
          width: 100%;
          height: calc(1.5em + .75rem + 2px);
          padding: .375rem .75rem;
          font-size: 1rem;
          font-weight: 400;
          line-height: 1.5;
          color: #ffffff;
          background-color: #131313;
          background-clip: padding-box;
          border: 1px solid #444444;
          border-radius: 10px;
          transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
        }

        .modal-content {
          position: relative;
          display: -ms-flexbox;
          display: flex;
          -ms-flex-direction: column;
          flex-direction: column;
          width: 100%;
          pointer-events: auto;
          background-color: #1b1b1b;
          background-clip: padding-box;
          border: 2px solid rgb(255 255 255 / 20%);
          border-radius: 10px;
          outline: 0;
        }

        .close {
          float: right;
          font-size: 1.5rem;
          font-weight: 700;
          line-height: 1;
          color: #fff;
          text-shadow: 0 1px 0 #fff;
          opacity: .5;
        }

        .modal-header
        .close {
            margin-top: -1.5rem;
        }

        .form-title {
          margin: -2rem 0rem 2rem;
        }

        .btn-round {
          border-radius: 3rem;
        }

        .delimiter {
          padding: 1rem;
        }

        .social-buttons
        .btn {
            margin: 0 0.5rem 1rem;
          }

        .signup-section {
          padding: 0.3rem 0rem;
        }

        img {
          max-width: 100%;
          max-height: 100%;
          border-radius: 10px;
        }

        a.main-button-slider-discord {
          -webkit-transition: all 0.3s ease 0s;
          -moz-transition: all 0.3s ease 0s;
          -o-transition: all 0.3s ease 0s;
          transition: all 0.3s ease 0s;
          color: #fff;
          margin-right: 5px;
          font-size: 1.25rem;
          border-radius: 7px;
          background-color: #1b1b1b;
          padding: .5rem 1rem;
          border: 1px solid #2f2f2f;
          color: #fff;
        }

        button.main-button-slider {
          -webkit-transition: all 0.3s ease 0s;
          -moz-transition: all 0.3s ease 0s;
          -o-transition: all 0.3s ease 0s;
          transition: all 0.3s ease 0s;
          color: #fff;
          margin-right: 5px;
          font-size: 1.25rem;
          border-radius: 7px;
          background-color: #2f2f2f;
          padding: .5rem 1rem;
          border: 1px solid #737373;
          color: #fff;
        }

        button.main-button-slider:hover {
          padding: .5rem 1rem;
          line-height: 1.25;
          color: #131313;
          background-color: white;
          border: 1px solid #1b1b1b;
          transition: 0.3;
        }

        a.main-button-slider:hover {
          padding: .5rem 1rem;
          line-height: 1.25;
          color: #131313;
          background-color: white;
          border: 1px solid #1b1b1b;
          transition: 0.3;
        }

        @media (min-width: 1200px) {
          .offset-xl-3 {
            margin-left: 10%;
          }
        }

        svg#waves path {
          fill: #131313;
        }

        .feature-box {
          display: block;
          background: #0e0e0e;
          padding: 20px;
          border-radius: 20px;
          border: 3px solid #1b1b1b;
          box-shadow: 0 2px 48px 0 rgb(0 0 0 / 8%);
          margin-bottom: 30px;
          position: relative;
          -webkit-transition: all 0.3s ease 0s;
          -moz-transition: all 0.3s ease 0s;
          -o-transition: all 0.3s ease 0s;
          transition: all 0.3s ease 0s;
          text-align: center;
        }
      </style>
  </body>
<?php
}
?>
</html>