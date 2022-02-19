<?php
error_reporting(E_ERROR);

session_start();

function generateRandomInt($length)
{
    $characters = '0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0;$i < $length;$i++)
    {
        $randomString .= $characters[rand(0, $charactersLength - 1) ];
    }
    return $randomString;
}
$tag = generateRandomInt(4);

$username = "";
$email = "";
$errors = array();
$succeded = array();
$db = mysqli_connect('localhost', 's4585_test', 'p8r9#V7y', 's4585_test');
function uuid()
{
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand(0, 0xffff) , mt_rand(0, 0xffff) , mt_rand(0, 0xffff) , mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000, mt_rand(0, 0xffff) , mt_rand(0, 0xffff) , mt_rand(0, 0xffff));
}

$uuid = uuid();

if(isset($_POST['reg_user'])) {


    $username = mysqli_real_escape_string($db, $_POST['username']);
    $email = mysqli_real_escape_string($db, $_POST['email']);
    define("EMAIL", mysqli_real_escape_string($db, $_POST['email']));
    $password_1 = mysqli_real_escape_string($db, $_POST['password_1']);
    $password_2 = mysqli_real_escape_string($db, $_POST['password_2']);
    $key = mysqli_real_escape_string($db, $_POST['key']);

    if (empty($username))
    {
        array_push($errors, "Username is required");
    }
    if (empty($email))
    {
        array_push($errors, "Email is required");
    }
    if (empty($password_1))
    {
        array_push($errors, "Password is required");
    }
    if (empty($key))
    {
        array_push($errors, "A Key is required");
    }
    if ($password_1 != $password_2)
    {
        array_push($errors, "The two passwords do not match");
    }

    $user_check_query = "SELECT * FROM users WHERE username='$username' OR email='$email' LIMIT 1";
    $result = mysqli_query($db, $user_check_query);
    $user = mysqli_fetch_assoc($result);

    if ($user)
    {
        if ($user['username'] == $username)
        {
            array_push($errors, "Username already exists.");
        }
        else if ($user['email'] == $email)
        {
            array_push($errors, "Email already exists.");
        }
        else
        {
            array_push($errors, "Already registered.");
        }

    }
    else
    {

    }

    $query12345 = "SELECT * FROM users WHERE invite='$key'";
    $exquery = mysqli_query($db, $query12345);

    if (mysqli_num_rows($exquery) > 0)
    {

        array_push($errors, "Invite is already assigned to another Account.");

    }
    else
    {
        $regQuery = "SELECT * FROM `invites` WHERE `inviteCode`='$key';";
        $regReq = mysqli_query($db, $regQuery);
        $regResult = mysqli_fetch_assoc($regReq);
        $inviter = $regResult['inviteAuthor'];
        if ($regResult['inviteCode'] == $key)
        {
            $delquery = "DELETE FROM `invites` WHERE `inviteCode` = '$key';";
            mysqli_query($db, $delquery);
            function generateRandomString($length = 16)
            {
                $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $charactersLength = strlen($characters);
                $randomString = '';
                for ($i = 0;$i < $length;$i++)
                {
                    $randomString .= $characters[rand(0, $charactersLength - 1) ];
                }
                return $randomString;
            }
            $ranPass = generateRandomInt(16);
            date_default_timezone_set('Europe/Amsterdam');
            $date = date("F d, Y h:i:s A");

            if (count($errors) == 0)
            {
                if (!file_exists('uploads/' . $uuid))
                {
                    mkdir('uploads/' . $uuid, 0777, true);
                }

                $password = password_hash($password_1, PASSWORD_DEFAULT);
                $embed_colour = "#6605ed";
                $embed_desc = "test1";
                $embed_title = "test2";
                $query = "INSERT INTO `users`(`id`, `uuid`, `username`, `email`, `password`, `banned`, `invite`, `secret`, `embedcolor`, `embedauthor`, `embedtitle`, `embeddesc`, `role`, `reg_date`, `use_embed`, `use_customdomain`, `use_2fa`, `self_destruct_upload`, `filename_type`, `url_type`, `uploads`, `upload_domain`, `discord_username`, `discord_id`, `discord_nitro`, `discord_avatar`, `inviter`, `last_uploaded`, `upload_limit`, `upload_size_limit`, `profile_description`, `profile_privacy`, `upload_background`, `upload_background_toggle`, `social_follower`, `social_banner`, `social_banner_filename`, `social_banner_color`, `social_currency`) VALUES (NULL, '$uuid', '$username', '$email', '$password', 'false','$key', '$ranPass', '#6605ed', 'C-Cloud | File Host', '%filename (%filesize)', 'Uploaded by %username at %date', 'User', '$date', 'true', 'false', 'false', 'false', 'short', 'short', 0, 'c-cloud.rocks', 'user#0000', '000000000000000000', 'No Nitro', 'https://cdn.discordapp.com/avatars/483330377214066707/2f85384205ece254104f0c6cf014bbe4.png?size=2048', '$inviter', 'Could not find Date', '500 MB', '32 MB', 'No description set.', 'true', '', 'false', 0, 'https://c-cloud.rocks/uploads/U2hB6rSs.png', 'U2hB6rSs.png', '#000000', 0);";
                mysqli_query($db, $query);
                $_SESSION['username'] = $username;
                $_SESSION['key'] = $key;
                $ip = $_SERVER['REMOTE_ADDR'];
                $_SESSION['success'] = "You are now logged in";

            }
            else
            {
                array_push($errors, "Something went wrong.");
            }
        }
        else
        {
            array_push($errors, "Invite is not valid.");
        }
    }
}

if (isset($_POST['login_user'])) {

    $username = mysqli_real_escape_string($db, $_POST['username']);
    $password = mysqli_real_escape_string($db, $_POST['password']);
    if (empty($username))
    {
        array_push($errors, "Username is required");
    }
    if (empty($password))
    {
        array_push($errors, "Password is required");
    }

    if (count($errors) == 0) {

        $query = "SELECT * FROM users WHERE username='$username'";
        $results = mysqli_query($db, $query);
        if (mysqli_num_rows($results) == 1) {
            $row = mysqli_fetch_assoc($results);
            if (password_verify($password, $row['password'])) {
                $_SESSION['username'] = $username;
                $_SESSION['key'] = $row['invite'];
                $_SESSION['success'] = "You are now logged in";
                mysqli_query($db, $query);
            } else {
                array_push($errors, "Wrong username/password combination");
            }
        }

    }

}

?>
