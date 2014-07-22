<?php

    require_once('config.php');

    @session_start();

    function h($s) {

        return htmlspecialchars($s, ENT_QUOTES, "UTF-8");
    }

    if(empty($_SESSION['user'])) {
        header('Location: '.SITE_URL.'login.php');
        exit;
    }

    $url  = "https://api.instagram.com/v1/users/self/media/recent/?access_token=".$_SESSION['user']['instagram_access_token'];
    $json = file_get_contents($url);
    $json = json_decode($json);

?>

<!doctype html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>My Instagram Page</title>
</head>
<body>
<h1>My Instagram Page</h1>

<div><img src="<?php echo h($_SESSION['user']['instagram_profile_picture']); ?>"/></div>
<p><?php echo h($_SESSION['user']['instagram_user_name']); ?>としてログインしています <a href="logout.php">[logout]</a></p>

<?php foreach($json->data as $data) : ?>
    <div>
        <img src="<?php echo h($data->images->low_resolution->url); ?>" alt=""/>

        <?php

            for($i = 0; $i < $data->likes->count; $i++) {
                echo '<span style="color:red">❤</span>';
            }
        ?>

    </div>
<?php endforeach; ?>

</body>
</html>