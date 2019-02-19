<nav xmlns="http://www.w3.org/1999/html">
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
    <div class="breadcrumbs-main">
        <ol class="breadcrumb">
            <?= $breadcrumbs; ?>
        </ol>
    </div>
</div>
<div class="line"></div>
<?php //debug($_SESSION); ?>

<section class="sectionProduct">

    <div class="wrapper">
        <div class="product-info">
            <div class="product-images">
                <div style="width: 550px; text-align: center;">
                    <?php if(count($gallery) > 1): ?>
                    <div class="slider-for" style="width: 100%;">
                        <?php foreach($gallery as $item): ?>
                        <div><img style="height: 400px; margin: 0 auto;" src="/images/products/<?=$item['image'];?>" <!--data-imagezoom="true" class="img-responsive"--> ></div>
                        <?php endforeach; ?>
                    </div>
                    <br>
                    <div class="slider-nav" style="padding-bottom: 40px; width: 100%;">
                        <?php foreach($gallery as $item): ?>
                        <div><img style="height: 100px; margin: 0 auto; padding: 0 2px;" src="/images/products/<?=$item['image'];?>"></div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                            <img style="margin: 0 auto; padding: 0 2px;" src="/images/products/<?=$product['image'];?>" alt="" <!--data-imagezoom="true" class="img-responsive" --> >
                    <?php endif; ?>
                </div>
            </div><!--product-images-->
            <div class="product-characters">
                <?php $curr = \im\core\App::$app->getProperty('currency'); //debug($curr); ?>
                <h1><?= $product['name']; ?></h1> <!--Получаем имя товара-->
                <!--Получаем цену товара-->
                <div class="product-price">
                    <!--Выводим старую цену товара, если она есть-->
                    <?php if($product['old_price']): ?>
                        <span id="old-price"><del><?=$curr['symbol_left'];?><span id="old-for-price"><?=$product['old_price'] * $curr['value'];?></span> <?=$curr['symbol_right'];?></del></span>
                    <?php endif; ?>
                    <!--Выводим основную цену товара-->
                    <span>&nbsp;<?= $curr['symbol_left']; ?><span id="base-price"><?= $product['price']* $curr['value']; ?></span> <?= $curr['symbol_right']; ?></span>
                </div>
                <!--Получаем данные о товаре из таблицы products-->
                <div class="info-characters">
                    <div class="field">
                        <label for="vendor">Категория:</label>
                        <span id="vendor"><a href="/category/<?= $product['cat_alias']; ?>"><?= $product['cat_name']; ?></a></span>
                    </div>
                    <div style="clear: both;"></div>
                    <div class="field">
                        <label for="vendor">Производитель:</label>
                        <span id="vendor"><a href="#"><?= $product['vn_name']; ?></a></span>
                    </div>
                    <div style="clear: both;"></div>
                    <!--Блок с характеристиками товара из таблицы product_properties_values-->
                    <?php if(!empty($product['propertiesArr'])): ?><?php //debug($product['propertiesArr'])?>
                    <div class="field properties" id="properties">
                        <?php foreach($product['propertiesArr'] as $property_name => $valArr): ?>
                            <div class="product_properties">
                            <label class="property_name" data-name='<?= json_encode($property_name)?>' data-mods='<?= json_encode($valArr); ?>' for="property_values"><?= $property_name. ':'; ?></label>
                            <span class="property_values">
                                <?php if(count($valArr) > 1): ?>
                                    <select>
                                       <!-- <option value="0">Выберите значение</option>-->
                                        <?php foreach($valArr as $id => $prop): ?>
                                        <option style="background-color: <?php if($property_name == 'Цвет'):?><?= $prop['pv_value'];?><?php endif; ?>" data-id='<?= $id; ?>' data-p_name='<?= $property_name; ?>' data-info='<?= json_encode($prop); ?>' value="<?= $prop['pv_name']; ?>"><?= $prop['pv_name']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php else: ?>
                                    <?php foreach($valArr as $id => $prop): ?>
                                        <span data-id='<?= $id; ?>' data-p_name='<?= $property_name; ?>' data-info='<?= json_encode($prop); ?>'><?= $prop['pv_name']; ?></span>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </span><br>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div style="clear: both;"></div>
                    <?php endif; ?>
                    <span style="display: none;" class="properties_data_arr" ></span>
                </div>
                <!--Блок с зависимыми (родитель-ребенок) характеристиками товара из таблицы properties_dependences -->
                <?php if(!empty($product['parentArr'])): ?>
                <div class="product-size-colors">

                    <div class="sizes">
                        <label for="size"><?= $parent_name.':'; ?></label>
                        <?php foreach($product['parentArr'] as $parent_value => $modsArr): ?>
                        <?php $str_key=$modsArr; ?>
                        <span id="size" data-size="<?= $parent_value; ?>" data-color='<?= json_encode($str_key); ?>' title="Размер: <?= $parent_value; ?>"><?= $parent_value; ?></span>
                        <?php endforeach; ?>
                    </div>
                    <div class="colors">
                        <label for="color"><?= $child_name.':'; ?></label>
                        <?php foreach($product['childsArr'] as $child_val => $chModsArr): ?>

                        <span id="color" style=" background-color: <?= current(array_keys($chModsArr)); ?>" title="<?= $child_val; ?>" data-color="<?= $child_val; ?>" data-size='<?= json_encode(current($chModsArr)); ?>'></span>
                        <?php endforeach; ?>

                    </div>
                </div><!--product-size-colors-->
                <?php endif; ?>

                <a id="" href="#" onclick="" data-id="<?= $product['id']; ?>" alt="Добавить в корзину" class="btn btn__cart">Добавить в корзину</a>



            </div><!--product-characters-->
            <div style="clear: both;"></div>

        </div><!--product-info-->
    </div>
    <div class="wrapper">
        <div class="tab-wrap">
            <ul class="nav-tab-list tabs">
                <li class="nav-tab-list__item active">
                    <a href="#tab_1" class="nav-tab-list__link"><h2>Описание</h2></a>
                </li>
                <li class="nav-tab-list__item">
                    <a href="#tab_2" class="nav-tab-list__link"><h2>Оплата и доставка</h2></a>
                </li>
            </ul>
            <div class="line"></div>
            <div class="box-tab-cont">
                <div class="tab-cont" id="tab_1"><?= $product['description']; ?></div>
                <div class="tab-cont hide" id="tab_2">.Текст про доставку и оплату товаров.</div>
            </div>
        </div><!--tab-wrap-->
     </div>
    <!--related-products-->
    <br>
    <div class="wrapper">
        <h2>Рекомендуемые товары</h2>
        <div class="line"></div>
        <div class="wrap">
            <?php if(!empty($product['related'])):?>
                <?php foreach($product['related'] as $item):?>
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
                                <span id="old-price"><del><?=$curr['symbol_left'];?><?=$item['old_price'] * $curr['value'];?><?=$curr['symbol_right'];?></del></span>
                            <?php endif; ?>
                            <?= $curr['symbol_left']?>&nbsp;<?= $item['price'] * $curr['value'] ?> <?= $curr['symbol_right']?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div><!--wrap-->
        <div style="clear: both;"></div>
    </div><!--wrapper-->
    <br>
    <div class="wrapper">
        <h2>Последние просмотренные товары</h2>
        <div class="line"></div>
        <div class="wrap">
            <?php if($recentlyViewed):?>
                <?php foreach($recentlyViewed as $item):?>
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
                                <span id="old-price"><del><?=$curr['symbol_left'];?><?=$item['old_price'] * $curr['value'];?><?=$curr['symbol_right'];?></del></span>
                            <?php endif; ?>
                            <?= $curr['symbol_left']?>&nbsp;<?= $item['price'] * $curr['value'] ?> <?= $curr['symbol_right']?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div><!--wrap-->
        <div style="clear: both;"></div>
    </div><!--wrapper-->
</section>
