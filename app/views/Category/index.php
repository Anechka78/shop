<nav>
    <div class="wrapper">
        <div class="menu-container">
            <div class="menu">
                <?php new \im\widgets\menu\Menu([
                    'tpl' => WWW . '/menu/my_meny.php',
                    'table' => 'categories',
                    'cache' => 60,
                ]); ?>
            </div>
        </div>
    </div>
    <div class="line"></div>
</nav>
<div class="wrapper">
    <div class="breadcrumbs-main">
        <ol class="breadcrumb">
            <?= $breadcrumbs; ?>
        </ol>
    </div>
</div>
<div class="line"></div>
<section class="section__category">
    <div class="wrapper">
        <h1>Товары категории "<?= $category['name'] ?>"</h1> <!--Получаем имя главной категории, в которую мы попали-->
    </div>
</section>
<section class="mainPage__center">
    <div class="wrapper">
        <div class="wrap">
            <?php if(!empty ($products)):?>
                <?php $curr = \im\core\App::$app->getProperty('currency'); //debug($curr); ?>
                <?php foreach($products as $item):?>
                    <div class="item">
                        <div class="item__title">
                            <a href="/product/<?= $item['alias'] ?>/"><?= $item['name'] ?></a>
                        </div>
                        <div class="item__img">
                            <a href="/product/<?= $item['alias'] ?>/">
                                <img src="/images/products/<?= $item['image'] ?>"/>
                            </a>
                        </div>

                        <div class="item__price">
                            <?php if($item['old_price']): ?>
                                <span id="old-price" style="font-size: 14px; font-weight: 500; color: #2D2D2D; impotant!"><del><?=$curr['symbol_left'];?><span id="old-for-price"><?=$item['old_price'] * $curr['value'];?></span> <?=$curr['symbol_right'];?></del></span>
                            <?php endif; ?>
                            <?= $curr['symbol_left']?><?= $item['price'] * $curr['value'] ?> <?= $curr['symbol_right']?></div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <span style="color: #E82B50; margin-top: 30px;"><?= 'В этой категории товаров пока нет'?></span>
            <?php endif; ?>
        </div><!--wrap-->
        <div style="clear: both;"></div>
        <div class="pagination">
            <?php if($pagination->countPages > 1): ?>
                <p>Товаров: <?= count($products);?> из <?= $total;?></p>
                <?= $pagination; ?>
            <?php endif; ?>
        </div>
    </div><!--wrapper-->
</section>