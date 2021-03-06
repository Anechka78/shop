<!DOCTYPE HTML>
<html lang="ru">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scaleble=no, maximum-scale=1" />

    <link rel="stylesheet" type="text/css" href="/public/megamenu/css/style.css">
    <link rel="stylesheet" type="text/css" href="/public/megamenu/css/ionicons.min.css">
    <link rel="stylesheet" type="text/css" href="/public/css/productPage.min.css">

    <link rel="stylesheet" type="text/css" href="/public/css/slick.css"/>
    <link rel="stylesheet" type="text/css" href="/public/css/slick-theme.css"/>

    <link rel="stylesheet" type="text/css" href="/public/css/mainPage.min.css">
    <link rel="stylesheet" type="text/css" href="/public/font-awesome/css/font-awesome.min.css">

    <!--<script type="text/javascript" src="/public/js/jquery-1.11.0.min.js"></script>
    <script type="text/javascript" src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>-->
    <script type="text/javascript" src="/public/js/jquery.min.js"></script>
    <script type="text/javascript" src="/public/js/jquery.migrate.js"></script>
    <script type="text/javascript" src="/public/js/slick.min.js"></script>
    <script type="text/javascript" src="/public/js/main.js"></script>
    <script type="text/javascript" src="/public/js/productPage.js"></script>
    <script type="text/javascript" src="/public/megamenu/js/megamenu.js"></script>
    <script type="text/javascript" src="/public/js/imagezoom__j.js"></script>



    <?php \im\core\base\View::getMeta() ?>

</head>
<body>

<div class="page">
    <?php $this->getPart('layouts/headerTop');?>
    <?php $this->getPart('layouts/headerLogo');?>

    <!--<?php if(isset($_SESSION['error'])): ?>
        <div>
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <?php if(isset($_SESSION['success'])): ?>
        <div>
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>-->

    <?php //debug($_SESSION);?>

    <?= $content ?>
    <?= debug(\im\core\Db::$countSql)?>
    <?= debug(\im\core\Db::$queries)?>

    <div id="wrapper_cart">

    </div>


<?php $this->getPart('layouts/footer');?>
    <?php $curr = \im\core\App::$app->getProperty('currency'); //debug($curr);?>
    <script type="text/javascript">
        var path = '<?= PATH; ?>';
        var course = <?= $curr['value'];?>;

        <?php if($curr['symbol_left']): ?>
            var symLeft = "<?= $curr['symbol_left'] ?>";
        <?php else: ?>
        var symLeft = "";
        <?php endif; ?>

        <?php if($curr['symbol_right']): ?>
            var symRight = "<?= $curr['symbol_right'] ?>";
        <?php else: ?>
        var symRight = "";
        <?php endif; ?>
        </script>
