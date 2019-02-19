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

<section class="mainPage__center">
    <div class="wrapper">
        <h1>Результаты поиска по запросу: "<?=h($query);?>"</h1>
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
                <span style="color: #E82B50; margin-top: 30px;"><?= 'Поиск по запросу не дал результатов. Попробуйте изменить запрос.'?></span>
            <?php endif; ?>
        </div>
        <div style="clear: both;"></div>
    </div>

