<?php

$pdo = new PDO('mysql:host=127.0.0.1;dbname=nau4i', 'root', '', array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
            )
        );

$query = $pdo->query("SELECT id, created_by, introtext, fullcontent, created_at, updated_at
    FROM content
    WHERE introtext
    LIKE '%flowplayer%' OR fullcontent LIKE '%flowplayer%'");

echo '<pre>';
foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $content) {

    preg_match_all('/{flowplayer}(.+){\/flowplayer}/', $content['introtext'], $m);

    if (count($m[1])) {
        foreach ($m[1] as $v) {
            $pdo->query("INSERT INTO videos(user_id, name, legacy, created_at, updated_at)
                VALUES('".$content['created_by']."','".$v."','1','".$content['created_at']."', '".$content['updated_at']."')");
        }
    }

    preg_match_all('/{flowplayer}(.+){\/flowplayer}/', $content['fullcontent'], $m);

    if (count($m[1])) {
        foreach ($m[1] as $v) {
            $pdo->query("INSERT INTO videos(user_id, name, legacy, created_at, updated_at)
                VALUES('".$content['created_by']."','".$v."','1','".$content['created_at']."', '".$content['updated_at']."')");
        }
    }

    // echo ($content['introtext']) . '<br>';
    // echo ($content['fullcontent']) . '<br>';
}
