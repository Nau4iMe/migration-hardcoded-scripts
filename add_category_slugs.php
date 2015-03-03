<?php

function slug($title, $separator = '-')
{
    $title = preg_replace('![^'.preg_quote($separator).'\pL\pN\s]+!u', '', mb_strtolower($title, 'utf-8'));
    $flip = $separator == '-' ? '_' : '-';
    $title = preg_replace('!['.preg_quote($flip).']+!u', $separator, $title);
    $title = preg_replace('!['.preg_quote($separator).'\s]+!u', $separator, $title);

    return trim($title, $separator);
}

function dd($something)
{
    echo '<pre>';
    var_dump($something);
    echo '</pre>';
}

$pdo = new PDO('mysql:host=127.0.0.1;dbname=nau4i', 'root', '', array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
));

$sql = 'SELECT t0.title title, t0.slug, t0.id,
    (SELECT GROUP_CONCAT(t2.slug ORDER BY t2._lft SEPARATOR "/")
     FROM categories t2 WHERE t2._lft<t0._lft AND t2._rgt>t0._rgt) ancestors
    FROM categories t0
    GROUP BY t0.title;';

$query = $pdo->query($sql);
foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
    // dd($row);
    // dd(slug($row['title']));
    $ancestors = str_replace('root', '', $row['ancestors']);
    $meow = $ancestors.'/'.$row['slug'];
    // $pdo->query("UPDATE `categories` SET `path` = '".  preg_replace('~^/~', '', $meow) ."' WHERE id = '".$row['id']."'");

}
echo 'meow';


