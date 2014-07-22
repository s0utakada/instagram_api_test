<?php

    require_once('config.php');

    @session_start();

    if(empty($_GET['code'])) {

        $params = [
            'client_id'     => CLIENT_ID,
            'redirect_uri'  => SITE_URL.'redirect.php',
            'scope'         => 'basic',
            'response_type' => 'code'
        ];
        $url    = 'https://api.instagram.com/oauth/authorize/?'.http_build_query($params);

        header('Location: '.$url);
        exit;

    } else {

        $params = [
            'client_id'     => CLIENT_ID,
            'client_secret' => CLIENT_SECRET,
            'code'          => $_GET['code'],
            'redirect_uri'  => SITE_URL.'redirect.php',
            'grant_type'    => 'authorization_code'
        ];
        $url    = 'https://api.instagram.com/oauth/access_token';

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $res = curl_exec($curl);
        curl_close($curl);
        $json = json_decode($res);
        
        try {

            $dbh = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);

        } catch(PDOException $e) {

            echo $e->getMessage();
            exit;

        }

        $stmt = $dbh->prepare("select * from users where instagram_user_id=:user_id limit 1");
        $stmt->execute([':user_id' => $json->user->id]);
        $user = $stmt->fetch();

        if(empty($user)) {

            $stmt = $dbh->prepare("
                insert into users (instagram_user_id, instagram_user_name, instagram_profile_picture, instagram_access_token, created, modified) value (
                    :user_id, :user_name, :profile_picture, :access_token, now(), now());
            ");

            $params = [
                ':user_id'         => $json->user->id,
                ':user_name'       => $json->user->username,
                ':profile_picture' => $json->user->profile_picture,
                ':access_token'    => $json->access_token
            ];
            $stmt->execute($params);

            $stmt = $dbh->prepare("select * from users where id=:last_insert_id limit 1");
            $stmt->execute([":last_insert_id" => $dbh->lastInsertId()]);
            $user = $stmt->fetch();

        }

        if(!empty($user)) {

            session_regenerate_id(TRUE);
            $_SESSION['user'] = $user;
        }

        header('Location: '.SITE_URL);

    }