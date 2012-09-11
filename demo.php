<?php
require_once('wp.xmlrpc.php');

$host     = 'hogehoge.net';
$user     = 'admin';
$password = 'mypassword';
$endpoint = 'http://hogehoge.net/xmlrpc.php';

$wp = new wpXMLRPC($endpoint, $host, $user, $password);

$data = array(
  'post_title' => 'タイトル',
  'post_content' => '内容です！',
  'post_thumbnail' => 'test.jpg',
  'terms_names' => array(
    'post_tag' => array('タグ１', 'タグ２')
  )
);

$wp->newPost($data)
?>