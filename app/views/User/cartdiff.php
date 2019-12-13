<?php if(!empty($_SESSION['user']['cart_diff'])):?>
    <div>
        <span>С прошлого посещения у Вас остались товары в Корзине. Добавить их в текущий заказ или удалить? Вид во views/user/cartdiff</span>
    </div>
    <div class="cart-wrap">
        <div class="cart-product">
            <?php foreach($_SESSION['user']['cart_diff'] as $id => $item):?>
                <?php //debug($_SESSION); ?>
                <div class="cart-product__item" data-id="<?= $id; ?>" data-productId="<?= $item['id']; ?>" >
                    <div class="cart-product__name" data-id="<?= $id; ?>">
                        <a href="/product/<?= $item['alias']; ?>"><?= $item['name']; ?></a>
                    </div>

                    <div class="cart-product__info">
                        <div class="cart-product__img" data-image="<?= $item['image']; ?>"><img src="/images/products/<?= $item['image']; ?>" style="width: 50px; margin-right: 10px;" />
                        </div>
                        <div class="cart-product__count">
                            <?php if(isset($item['prDepArr'])):?>
                                <span><?= $item['prDepArr']['p_name'].': ';?> </span><span class="cart-product__size"><?= $item['prDepArr']['pv_name'];?></span>
                                <span><?= $item['prDepArr']['ch_name'].': ';?>  </span><span class="cart-product__color"><?= $item['prDepArr']['ch_val'];?></span>
                            <?php endif;?>

                            <?php if(isset($item['prValArr'])):?>
                                <?php foreach($item['prValArr'] as $key => $mod):?>
                                    <span><?= $mod['p_name'].': ';?> </span><span class="cart-product__size"><?= $mod['pv_name'];?></span>
                                <?php endforeach;?>
                            <?php endif;?>

                            <span class="product-count">Количество: <?= $item['qty'] ?> шт. Х <?= $item['itemPrice']*$_SESSION['cart.currency']['value'];?><?= $_SESSION['cart.currency']['symbol_left'];?><?= $_SESSION['cart.currency']['symbol_right'];?> = <?= $_SESSION['cart.currency']['symbol_left'];?><?= $item['qty'] * $item['itemPrice'] * $_SESSION['cart.currency']['value'];?><?= $_SESSION['cart.currency']['symbol_right'];?></span>
                            <span class="itemPrice" value="<?= $item['itemPrice'];?>" style="display: none;"><?= $_SESSION['cart.currency']['symbol_left'];?><?= $item['price'];?><?= $_SESSION['cart.currency']['symbol_right'];?></span>
                        </div>
                    </div>

                </div>
            <?php endforeach; ?>
        </div><!--cart-product-->

        <div class="cart-checkout">
            <input id="clear_from_cart" style="background-color: #317EF7;" type="button" value="Удалить из заказа"/>
            <input id="add_in_cart" type="button" value="Добавить в заказ"/>
        </div>
        <div style="clear: both;"></div>
    </div><!--cart-wrap-->
<?php else: ?>
    <h3>Корзин для сравнения нет Вид во views/user/cartdiff</h3>
<?php endif; ?>

