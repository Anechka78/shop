<section class="" xmlns="http://www.w3.org/1999/html">
    <div class="wrapper wrapper_cart">
        <?php if(!empty($_SESSION['cart'])): ?>
        <?php //debug($_SESSION['cart']); die(); ?>
        <form action="/cart/order/" method="POST" id="cartForm">
            <div class="cart-wrap">
                <div class="cart-product">
                    <?php foreach($_SESSION['cart']['products'] as $id => $item):?>
                    <div class="cart-product__item" data-id="<?= $id; ?>" id="itemInfo_<?= $id; ?>">
                        <div class="cart-product__name" data-id="<?= $id; ?>">
                            <a href="/product/<?= $item['product']['alias']; ?>"><?= $item['product']['name']; ?></a>
                            <a class="cart-toggle itemToDel" id="itemToDel_<?= $id; ?>" href="#" data-proc="delete" alt="Удалить из корзины">Удалить</a>

                        </div>

                        <div class="cart-product__info">
                            <div class="class="cart-product__img"><img src="/images/products/<?= $item['product']['image']; ?>" style="width: 50px; margin-right: 10px;" />
                        </div>
                        <div class="cart-product__count">
                            <?php if(isset($item['product']['prDepArr'])):?>
                                <span><?= $item['product']['prDepArr']['p_name'].': ';?> </span><span class="cart-product__size"><?= $item['product']['prDepArr']['pv_name'];?></span>
                                <span><?= $item['product']['prDepArr']['ch_name'].': ';?>  </span><span class="cart-product__color"><?= $item['product']['prDepArr']['ch_val'];?></span><br>
                            <?php endif;?>

                            <?php if(isset($item['product']['prValArr'])):?>
                                <?php foreach($item['product']['prValArr'] as $key => $mod):?>
                                    <span><?= $mod['p_name'].': ';?> </span><span class="cart-product__size"><?= $mod['pv_name'];?></span><br>
                                <?php endforeach;?>
                            <?php endif;?>

                            <span class="product-count">Количество:</span>
                            <span class="minus btn" data-proc="minus" id="minus_<?= $id; ?>">-</span>
                            <input name="itemCnt_<?= $id; ?>" class="itemCnt" id="itemCnt_<?= $id; ?>" type="text" value="<?= $item['qty'] ?>" data-count="<?= $item['qty'] ?>">
                            <span class="plus btn" data-proc="plus" id="plus_<?= $id; ?>">+</span>

                            <span class="itemPrice" id="itemPrice_<?= $id; ?>" value="<?= $item['price'];?>" style="display: none;"><?= $_SESSION['cart.currency']['symbol_left'];?><?= $item['price'];?><?= $_SESSION['cart.currency']['symbol_right'];?></span>
                            <span class="product-summa">
                                <span class="product-summ" id="itemRealPrice_<?= $id; ?>"><?= $_SESSION['cart.currency']['symbol_left'];?><span><?= $item['qty'] * $item['price'];?></span><?= $_SESSION['cart.currency']['symbol_right'];?></span>

                            </span>
                        </div>

                    </div>

                </div>
                <?php endforeach; ?>
            </div>

            <div class="cart-checkout">
                <input class="clear_cart" type="button" onclick="clearCart();" value="Очистить корзину"/>
                <div style="clear: both;"></div>
                <div class="cart-checkout__summ">
                    <span class="summ-text">К оплате:</span>
                    <?php //debug($currency);?>
                    <span class="product-summa">
                            <span class="summ-count"><?= $_SESSION['cart.currency']['symbol_left'] . $_SESSION['cart']['totalsum'] . $_SESSION['cart.currency']['symbol_right'];?></span>

                        </span>
                </div>
                <input class="cart-checkout__order" type="button" value="Оформить заказ"/>
            </div>
            <div style="clear: both;"></div>
    </div><!--cart-wrap-->
    </form>

        <?php else: ?>
            <h3>Корзина пуста</h3>
        <?php endif; ?>
    </div>
</section>