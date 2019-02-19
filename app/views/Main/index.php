<div>
    <nav>
        <div class="wrapper">
            <div class="menu-container">
                <div class="menu">
                    <?php new \im\widgets\menu\Menu([
                        'tpl' => WWW . '/menu/my_meny.php',
                        'table' => 'categories',
                        'cache' => 3600,
                    ]); ?>
                </div>
            </div>
        </div>
        <div class="line"></div>
    </nav>

    <div class="wrapper">

        <?php /*new \im\widgets\menu\Menu([
            'tpl' => WWW . '/menu/select.php',
            'table' => 'categories',
            'cache' => 60,
        ]); */?>
    </div>

    <?php $this->getPart('Main/slider');?>

    <section class="mainPage__center">
        <div class="wrapper">
            <h1>Последние добавленные товары</h1>
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
                            <img src="/images/products/prev/<?= $item['image'] ?>"/>
                        </a>
                    </div>
                    <div class="item__price"><?= $curr['symbol_left']?><?= $item['price'] * $curr['value'] ?> <?= $curr['symbol_right']?></div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div style="clear: both;"></div>
        </div>
        <div class="wrapper">
            <h1>Популярные товары</h1>
            <div class="wrap">
                <?php if(!empty ($rsHits)):?>
                    <?php foreach($rsHits as $item):?>
                        <div class="item">
                            <div class="item__title">
                                <a href="/product/<?= $item['alias'] ?>/"><?= $item['name'] ?></a>
                            </div>
                            <div class="item__img">
                                <a href="/product/<?= $item['alias'] ?>/">
                                    <img src="/images/products/<?= $item['image'] ?>"/>
                                </a>
                            </div>
                            <div class="item__price"><?= $curr['symbol_left']?>&nbsp;<?= $item['price'] * $curr['value'] ?> <?= $curr['symbol_right']?></div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div style="clear: both;"></div>
        </div>
    </section>

    <!--<?php if(!empty ($posts)):?>
        <?php foreach($posts as $post):?>
            <h2><?= $post['title'] ?></h2>
            <div><?= $post['text'] ?></div>
        <?php endforeach; ?>
        <div>
            <p>Статей: <?= count($posts);?> из <?= $total;?></p>
            <?php if($pagination->countPages > 1): ?>
                <?= $pagination; ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>-->
</div>
