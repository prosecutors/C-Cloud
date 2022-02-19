<?php
require_once '../app/require.php';

session_start();

if (!isset($_SESSION['username'])) {
    $_SESSION['msg'] = "You must log in first";
    header('location: ../');
}

function unset_cookie($cookie_name)
{
    if (isset($_COOKIE[$cookie_name])) {
        unset($_COOKIE[$cookie_name]);
        setcookie($cookie_name, null, -1);
    } else {
        return false;
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    unset($_SESSION['username']);
    unset_cookie('MHILLS_COOKIE');
    header('location: ../');
}

$username = $_SESSION['username'];


// FUNCTIONS
function generateRandomInt($length)
{
    $characters = '0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function generateRandomString($length)
{
    $characters = 'ABCDEFGHIJKLMOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function human_filesize($bytes, $decimals)
{
    $size = array(
        'B',
        'KB',
        'MB',
        'GB',
        'TB',
        'PB',
        'EB',
        'ZB',
        'YB'
    );
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f ", $bytes / pow(1024, $factor)) . @$size[$factor];
}

function delete_files($target)
{
    if (is_dir($target)) {
        $files = glob($target . '*', GLOB_MARK);

        foreach ($files as $file) {
            delete_files($file);
        }

        rmdir($target);
    } elseif (is_file($target)) {
        unlink($target);
    }
}


// SQL QUERYES ETC

$query = "SELECT * FROM users WHERE username='$username'";
$result = mysqli_query($db, $query);
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $usernamedb = "" . $row["username"] . "";
        $email = "" . $row["email"] . "";
        $role = "" . $row["role"] . "";
        $uid = "" . $row["id"] . "";
        $_SESSION['userid'] = $row["id"];
        $secret = "" . $row["secret"] . "";
        $reg_date = "" . $row["reg_date"] . "";
        $secret = "" . $row["secret"] . "";
        $uploads = "" . $row["uploads"] . "";
        $uuid = "" . $row["uuid"] . "";
        $twofa_status = "" . $row["use_2fa"] . "";
        $discord_avatar = "" . $row["discord_avatar"] . "";
        $discord_username = "" . $row["discord_username"] . "";
        $inviter = "" . $row["inviter"] . "";
        $last_uploaded = "" . $row["last_uploaded"] . "";
        $banned = "" . $row["banned"] . "";
    }
} else {
    echo " ";
}


// USER COUNT
$query = "SELECT * FROM users";
$result = mysqli_query($db, $query);
$usercount = mysqli_num_rows($result);

// UPLOAD COUNT
$query = "SELECT * FROM uploads";
$result = mysqli_query($db, $query);
$uploadcount = mysqli_num_rows($result);

// INVITES COUNT
$query = "SELECT * FROM `invites` WHERE `inviteAuthor`='$username'";
$result = mysqli_query($db, $query);
if (mysqli_num_rows($result)) {
    $invites = mysqli_num_rows($result);
} else {
    $invites = "0";
}

// INVITED USERS
$query = "SELECT * FROM `users` WHERE `inviter`='$username'";
$result = mysqli_query($db, $query);
if (mysqli_num_rows($result)) {
    $invitedusers = mysqli_num_rows($result);
} else {
    $invitedusers = "0";
}


if (isset($_GET['getNewSecret'])) {
    $newSecret = generateRandomInt(16);
    $username = $_SESSION['username'];
    $query = "SELECT * FROM users WHERE username='$username'";
    $result = mysqli_query($db, $query);
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $secret = "" . $row["secret"] . "";
        }
    } else {
        echo "0 results";
    }
    $query1 = "UPDATE users SET secret='$newSecret' WHERE secret='$secret'";
    $result1 = mysqli_query($db, $query1);
    if (mysqli_num_rows($result1) > 0) {
    } else {
        echo "0 results";
    }
    header("Refresh:0");
}


$query = "SELECT * FROM toggles";
$result = mysqli_query($db, $query);
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $announcement = "" . $row["announcement"] . "";
    }
} else {
    echo "0 results";
}


if (isset($_GET["wipeImages"])) {
    $sql = "DELETE FROM `uploads` WHERE `username`='$username'";
    $sql1 = "UPDATE `users` SET `uploads`='0' WHERE `username`='$username'";
    mysqli_query($db, $sql);
    mysqli_query($db, $sql1);
    sleep(2);
    delete_files("../uploads/$uuid");
    header("Refresh:0");
}
if (isset($_GET["downloadUploads"])) {
    $zip = new ZipArchive;
    if ($zip->open($username . '_uploads.zip', ZipArchive::CREATE) === TRUE) {
        if ($handle = opendir("../uploads/$uuid/$username/")) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != ".." && !is_dir("../uploads/$uuid/$username/" . $entry)) {
                    $zip->addFile("../uploads/$uuid/$username/" . $entry);
                }
            }
            closedir($handle);
        }
        $zip->close();
    }
    $file = $username . '_uploads.zip';

    header("Content-Description: File Transfer");
    header("Content-Type: application/octet-stream");
    header("Content-Disposition: attachment; filename=\"" . basename($file) . "\"");
    readfile($file);
    unlink('../home/' . $username . '_uploads.zip');
}

$sql = "SELECT *FROM `users` WHERE `username`='$username'";
$result = mysqli_query($db, $sql);
$row = mysqli_fetch_assoc($result);
$discord_avatar = $row["discord_avatar"];
$uuid = $row["uuid"];
$average_color = $row["social_banner_color"];


$query21 = "SELECT * FROM `invites` WHERE `inviteAuthor`=" . '"' . $username . '";';
$results21 = mysqli_query($db, $query21);
$rows21 = mysqli_num_rows($results21);

$query22 = "SELECT * FROM `users` WHERE `inviter`=" . '"' . $username . '";';
$results22 = mysqli_query($db, $query22);
$rows22 = mysqli_num_rows($results22);


if ($banned == "false") {
    function GetDirectorySize($path)
    {
        $bytestotal = 0;
        $path = realpath($path);
        if ($path !== false && $path != '' && file_exists($path)) {
            foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)) as $object) {
                $bytestotal += $object->getSize();
            }
        }
        return $bytestotal;
    }
    $totalfillessize = human_filesize(GetDirectorySize("../uploads/$uuid/$username"), 2);
} else {
    $totalfillessize = "Files locked";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../assets/images/icons/favicon.ico" />
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/bootstrap.css">
    <script async src="https://arc.io/widget.min.js#3uop4387"></script>

    <link rel="stylesheet" href="../assets/css/app.css">
    <link rel="shortcut icon" href="../assets/images/favicon.svg" type="image/x-icon">
</head>

<body>
    <div id="app">
        <div id="sidebar" class="active">
            <div class="sidebar-wrapper active">
                <div class="sidebar-header">
                    <div class="d-flex justify-content-between">
                        <div class="logo">
                            <a style="color: #ffffff;"><img style="height: 75px;" src="../assets/images/logo.png" alt="Logo" srcset="">C-Cloud</a>
                        </div>
                        <div class="toggler">
                            <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                        </div>
                    </div>
                </div>
                <div class="sidebar-menu">
                    <ul class="menu">
                        <li class="sidebar-title">Menu</li>

                        <li class="sidebar-item active">
                            <a href="#" class='sidebar-link'>
                                <i class="bi bi-grid-fill"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>

                        <li class="sidebar-item">
                            <a href="user" class='sidebar-link'>
                                <svg class="bi" width="1em" height="1em" fill="currentColor">
                                    <use xlink:href="https://upload.wikimedia.org/wikipedia/commons/thumb/9/98/OOjs_UI_icon_userAvatar.svg/1200px-OOjs_UI_icon_userAvatar.svg.png"></use>
                                </svg>
                                <span>User</span>
                            </a>
                        </li>

                        <li class="sidebar-item">
                            <a href="invites" class='sidebar-link'>
                                <svg class="bi" width="1em" height="1em" fill="currentColor">
                                    <use xlink:href="http://cdn.onlinewebfonts.com/svg/img_258567.png"></use>
                                </svg>
                                <span>Invites</span>
                            </a>
                        </li>

                        <li class="sidebar-item  has-sub">
                            <a href="#" class='sidebar-link'>
                                <svg class="bi" width="1em" height="1em" fill="currentColor">
                                    <use xlink:href="https://upload.wikimedia.org/wikipedia/commons/d/dc/Settings-icon-symbol-vector.png"></use>
                                </svg>
                                <span>Settings</span>
                            </a>
                            <ul class="submenu ">
                                <li class="submenu-item">
                                    <a href="embed-settings">Embed Settings</a>
                                </li>
                                <li class="submenu-item ">
                                    <a href="domain-settings">Domain Settings</a>
                                </li>
                                <li class="submenu-item ">
                                    <a href="upload-preferences">Upload Preferences</a>
                                </li>
                            </ul>
                        </li>

                        <li class="sidebar-item">
                            <a href="rules" class='sidebar-link'>
                                <svg class="bi" width="1em" height="1em" fill="currentColor">
                                    <use xlink:href="https://icon-library.com/images/rules-icon-png/rules-icon-png-9.jpg"></use>
                                </svg>
                                <span>Rules</span>
                            </a>
                        </li>

                        <li class="sidebar-item">
                            <a href="scoreboard" class='sidebar-link'>
                                <svg class="bi" width="1em" height="1em" fill="currentColor">
                                    <use xlink:href="https://cdn-icons-png.flaticon.com/512/79/79638.png"></use>
                                </svg>
                                <span>Scoreboard</span>
                            </a>
                        </li>

                        <li class="sidebar-item  ">
                            <a href="paste" class='sidebar-link'>
                                <svg class="bi" width="1em" height="1em" fill="currentColor">
                                    <use xlink:href="https://cdn-icons-png.flaticon.com/512/748/748035.png"></use>
                                </svg>
                                <span>Paste</span>
                            </a>
                        </li>

                        <li class="sidebar-item  ">
                            <a href="gallery/" class='sidebar-link'>
                                <svg class="bi" width="1em" height="1em" fill="currentColor">
                                    <use xlink:href="http://cdn.onlinewebfonts.com/svg/img_562621.png"></use>
                                </svg>
                                <span>Gallery</span>
                            </a>
                        </li>


                        <li class="sidebar-item  ">
                            <a href="upload-file" class='sidebar-link'>
                                <svg class="bi" width="1em" height="1em" fill="currentColor">
                                    <use xlink:href="http://cdn.onlinewebfonts.com/svg/img_150954.png"></use>
                                </svg>
                                <span>Upload</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="index.php?logout=%271%27" class='sidebar-link'>
                                <svg class="bi" width="1em" height="1em" fill="currentColor">
                                    <use xlink:href="https://upload.wikimedia.org/wikipedia/commons/thumb/8/8a/OOjs_UI_icon_logOut-ltr.svg/1200px-OOjs_UI_icon_logOut-ltr.svg.png"></use>
                                </svg>
                                <span>Logout</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <button class="sidebar-toggler btn x"><i data-feather="x"></i></button>
            </div>
        </div>
        <div id="main">
            <header class="mb-3">
                <a href="#" class="burger-btn d-block d-xl-none">
                    <i class="bi bi-justify fs-3"></i>
                </a>
            </header>

            <div class="page-heading">
                <h3 style="text-align: center;">Welcome, <?php echo $username ?></h3>
            </div>
            <div class="page-content">
                <section class="row">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">MOTD</h4>
                                </div>
                                <div class="card-body px-3 py-4-5">
                                    <a style="color: #ccc">Todays MOTD is: Welcome.</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-9">
                        <div class="row">
                            <div class="col-6 col-lg-3 col-md-6">
                                <div class="card">
                                    <div class="card-body px-3 py-4-5">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="stats-icon purple">
                                                    <img style="width: 30px;" src="https://pmls-print.de/wp-content/uploads/2020/06/PMLS_Nav_Icon_Upload.png">
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <h6 class="text-muted font-semibold">Uploads</h6>
                                                <h6 class="font-extrabold mb-0"><?php echo $row["uploads"] ?></h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-lg-3 col-md-6">
                                <div class="card">
                                    <div class="card-body px-3 py-4-5">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="stats-icon blue">
                                                    <img style="width: 30px;" src="../assets/images/invite-icon1.png">
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <h6 class="text-muted font-semibold">Invites</h6>
                                                <h6 class="font-extrabold mb-0"><?php echo $rows21 ?></h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-lg-3 col-md-6">
                                <div class="card">
                                    <div class="card-body px-3 py-4-5">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="stats-icon green">
                                                    <img style="width: 30px;" src="../assets/images/invite-icon1.png">
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <h6 class="text-muted font-semibold">Invited Users</h6>
                                                <h6 class="font-extrabold mb-0"><?php echo $rows22 ?></h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-lg-3 col-md-6">
                                <div class="card">
                                    <div class="card-body px-3 py-4-5">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="stats-icon red">
                                                    <img style="width: 30px;" src="../assets/images/invite-icon1.png">
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <h6 class="text-muted font-semibold">Size of Files</h6>
                                                <h6 class="font-extrabold mb-0"><?php echo $totalfillessize ?></h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Quick actions</h4>
                                    </div><br>
                                    <div class="card-body">
                                        <input type="button" class="btn btn-lg btn-dark" name="button1" onclick="generateConfig()" value="Download Config" />
                                        <a href="embed-settings" class="btn btn-lg btn-dark">Change Embed Settings</a>
                                        <a href="upload-preferences" class="btn btn-lg btn-dark">Upload Preferences</a>
                                        <a href="?getNewSecret=<?php echo $row["secret"] ?>" class="btn btn-lg btn-dark">Reset Secret</a>
                                        <a href="?wipeImages" class="btn btn-lg btn-dark">Wipe Uploads</a>
                                        <a href="?downloadUploads" class="btn btn-lg btn-dark">Download Uploads</a>
                                        <?php
                                        if ($row["role"] == "Owner" || $row["role"] == "Admin" || $row["role"] == "Manager") {
                                            echo "<a href='admin/' class='btn btn-lg btn-dark'>Admin Panel</a>";
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-3">
                        <div class="card">
                            <div class="card-body py-4 px-5" style=" background-image: url(<?php echo $row["social_banner"] ?>); background-size: cover; background-repeat: no-repeat; background-position-x: center; background-position-y: center; border-radius: .7rem; ">
                                <div class="d-flex align-items-center" style="position: relative;align-items: normal;">
                                    <div class="avatar avatar-xl">
                                        <img style="    box-shadow: 0 5px 10px 0px rgb(0 0 0 / 10%); -moz-box-shadow: 0 5px 10px 0px rgba(0, 0, 0, 0.1); -webkit-box-shadow: 0 5px 10px 0px rgb(0 0 0 / 10%); -o-box-shadow: 0 5px 10px 0px rgba(0, 0, 0, 0.1); -ms-box-shadow: 0 5px 10px 0px rgba(0, 0, 0, 0.1);" src="<?php echo $row["discord_avatar"] ?>" alt="Face 1">
                                    </div>
                                    <div class="ms-3 name">
                                        <h5 class="font-bold" style="text-shadow: -1px -1px 5px black, 1px -1px 5px black, -1px 1px 5px black, 1px 1px 5px black;"><?php echo $username ?> <span class="badge bg-primary"><?php echo $row["role"] ?></span></h5>
                                        <h6 class="text-muted mb-0" style="text-shadow: -1px -1px 5px black, 1px -1px 5px black, -1px 1px 5px black, 1px 1px 5px black;">@<?php echo $row["discord_username"] ?></h6>
                                        <h6 class="text-muted mb-0" style="text-shadow: -1px -1px 5px black, 1px -1px 5px black, -1px 1px 5px black, 1px 1px 5px black;">Followers: <a style="color: white;"><?php echo $row["social_follower"] ?></a></h6>
                                        <h6 class="text-muted mb-0" style="text-shadow: -1px -1px 5px black, 1px -1px 5px black, -1px 1px 5px black, 1px 1px 5px black;">Secret: <a id="blurtext" style="color: white;"><?php echo $row["secret"] ?></a></h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                <h4>Top Users (Uploads)</h4>
                            </div>
                            <div class="card-content pb-4">
                                <?php
                                $query1 = "SELECT * FROM `users` ORDER BY `uploads` DESC LIMIT 3";
                                $result1 = mysqli_query($db, $query1);
                                if (mysqli_num_rows($result1)) {
                                } else {
                                    echo "0 results";
                                }
                                $number = 0;
                                while ($row = mysqli_fetch_assoc($result1)) {
                                    $number++;
                                    echo "<div class='recent-message d-flex px-4 py-3'>
                                            <div class='avatar avatar-lg'>
                                        <img src='" . $row["discord_avatar"] . "'>
                                    </div>
                                    <div class='name ms-4'>
                                        <h5 class='mb-1'><a style='text-decoration: none' href='https://dev.clynt.de/profile/" . $row["id"] . "'>" . $row["username"] . "</a></h5>
                                        <h6 class='text-muted mb-0'>@" . $row["discord_username"] . "</h6>
                                        <h6 class='text-muted mb-0'>Uploads: " . $row["uploads"] . "</h6>
                                    </div>
                                    </div>";
                                }

                                ?>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <footer>
                <div class="footer clearfix mb-0 text-muted">
                    <div class="float-start">
                        <p>2022 &copy; C-Cloud</p>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script src="../assets/js/bootstrap.bundle.min.js"></script>

    <script src="../assets/js/pages/dashboard.js"></script>

    <script src="../assets/js/main.js"></script>
</body>
<style>
    .btn.btn-dark {
        color: #fff;
        margin: 5px;
    }

    .sidebar-wrapper {
        background-color: #1b1b1b;
        bottom: 0;
        height: 100vh;
        overflow-y: auto;
        position: fixed;
        top: 0;
        transition: left .5s ease-out;
        width: 300px;
        z-index: 10;
    }

    body {
        -webkit-text-size-adjust: 100%;
        -webkit-tap-highlight-color: rgba(0, 0, 0, 0);
        background-color: #131313;
        color: #607080;
        font-family: Nunito;
        font-size: 1rem;
        font-weight: 400;
        line-height: 1.5;
        margin: 0;
    }

    .sidebar-wrapper .menu .sidebar-link {
        align-items: center;
        border-radius: .5rem;
        color: #ccc;
        display: block;
        display: flex;
        font-size: 1rem;
        background: #1b1b1b;
        padding: .7rem 1rem;
        text-decoration: none;
        transition: all .5s;
    }

    .sidebar-wrapper::after .menu::after .sidebar-link::after {
        align-items: center;
        border-radius: .5rem;
        color: #ccc;
        display: block;
        display: flex;
        font-size: 1rem;
        background: #1b1b1b;
        padding: .7rem 1rem;
        text-decoration: none;
        transition: all .5s;
    }

    .sidebar-wrapper .menu .sidebar-link:hover {
        background-color: #151515
    }

    .sidebar-wrapper .menu .submenu .submenu-item a {
        color: #ccc;
        background: #171717;
        display: block;
        font-size: .85rem;
        border-radius: 10px;
        margin: 4px;
        font-weight: 600;
        letter-spacing: .5px;
        padding: .7rem 2rem;
        text-decoration: none;
        transition: all .3s;
    }

    .card {
        word-wrap: break-word;
        background-clip: border-box;
        background-color: #1b1b1b;
        border: 1px solid rgba(0, 0, 0, .125);
        border-radius: .7rem;
        display: flex;
        flex-direction: column;
        min-width: 0;
        position: relative;
    }

    .sidebar-wrapper .menu .sidebar-title {
        color: #ffffff;
        font-size: 1rem;
        font-weight: 600;
        list-style: none;
        margin: 1.5rem 0 1rem;
        padding: 0 1rem;
    }

    .card-header {
        background-color: #101010;
        border-bottom: 1px solid rgba(0, 0, 0, .125);
        margin-bottom: 0;
        padding: 1.5rem;
    }

    .h1,
    .h2,
    .h3,
    .h4,
    .h5,
    .h6,
    h1,
    h2,
    h3,
    h4,
    h5,
    h6 {
        color: #ffffff;
        font-weight: 700;
        line-height: 1.2;
        margin-bottom: .5rem;
        margin-top: 0;
    }

    .text-muted {
        color: #ccc;
    }

    .sidebar-wrapper .menu .sidebar-link i,
    .sidebar-wrapper .menu .sidebar-link svg {
        color: #5a5a5a;
    }

    .sidebar-wrapper .menu .sidebar-item.active .sidebar-link {
        background-color: #131313;
    }

    a {
        color: #ffffff;
        text-decoration: underline;
    }

    .avatar.avatar-xl .avatar-content,
    .avatar.avatar-xl img {
        font-size: 1.4rem;
        height: 60px;
        width: 60px;
        top: 0;
        right: 0;
    }

    #blurtext {
        filter: blur(3px);
    }

    #blurtext:hover {
        filter: blur(0px);
        transition: .5s;
    }

    .align-items-center {
        align-items: normal
    }

    /*.card-header{
    background-color: #<?php echo $average_color ?>; 
}*/
</style>
<script>
    function download(filename, text) {
        var element = document.createElement('a');
        element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(text));
        element.setAttribute('download', filename);

        element.style.display = 'none';
        document.body.appendChild(element);

        element.click();

        document.body.removeChild(element);
    }

    // Start file download.
    function generateConfig() {
        var text = `{
  "Version": "13.2.1",
  "Name": "Marc Hills Host - <?php echo $_SESSION['username']; ?>",
  "DestinationType": "ImageUploader, FileUploader",
  "RequestMethod": "POST",
  "RequestURL": "https://dev.clynt.de/api/upload",
  "Parameters": {
    "secret": "<?php echo $secret ?>",
    "use_sharex": "true"
  },
  "Body": "MultipartFormData",
  "FileFormName": "file"
}`;

        var filename = "dev.sxcu";
        setTimeout(() => {
            download(filename, text);
        }, 1000)
    }
</script>

</html>