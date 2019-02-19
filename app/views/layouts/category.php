<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scaleble=no, maximum-scale=1" />

    <link rel="stylesheet" type="text/css" href="/public/css/mainPage.min.css">
    <link rel="stylesheet" type="text/css" href="/public/css/categoryPage.min.css">
    <link rel="stylesheet" type="text/css" href="/public/font-awesome/css/font-awesome.min.css">
    <!--<link rel="stylesheet" type="text/css" href="/public/css/cartPage.min.css">
    <link rel="stylesheet" type="text/css" href="/public/css/slider.min.css">
    <link rel="stylesheet" type="text/css" href="/public/css/orderPage.min.css">
    <link rel="stylesheet" type="text/css" href="/public/css/productPage.min.css">
    <link rel="stylesheet" type="text/css" href="/public/css/userPage.min.css">-->

    <script type="text/javascript" src="/public/js/jquery.min.js"></script>
    <script type="text/javascript" src="/public/js/jquery.migrate.js"></script>
    <script type="text/javascript" src="/public/js/mainPage.js"></script>
    <script type="text/javascript" src="/public/js/functions.js"></script>
    <script type="text/javascript" src="/public/js/categoryPage.js"></script>
    <!--<script type="text/javascript" src="/public/js/orderPage.js"></script>
    <script type="text/javascript" src="/public/js/userPage.js"></script>
    <script type="text/javascript" src="/public/js/cartPage.js"></script>
    <script type="text/javascript" src="/public/js/productPage.js"></script>
    <script type="text/javascript" src="/public/js/slick.min.js"></script>-->

    <?php \im\core\base\View::getMeta() ?>

</head>
<body>
<body>
<div class="page">
    <?php $this->getPart('layouts/headerTop');?>
    <?php $this->getPart('layouts/headerLogo');?>

    <?php if(isset($_SESSION['error'])): ?>
        <div>
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <?php if(isset($_SESSION['success'])): ?>
        <div>
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php //debug($_SESSION);?>

    <?= $content ?>
    <?= debug(\im\core\Db::$countSql)?>
    <?= debug(\im\core\Db::$queries)?>



<?php $this->getPart('layouts/footer');?>