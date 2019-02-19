<section class="" xmlns="http://www.w3.org/1999/html">
    <div class="wrapper wrapper_cart">
        <h1>Ваш заказ</h1>
        <?php //debug($_SESSION); ?>
        <?php if(! $itemsArr): ?>
            В корзине пусто.
        <?php else: ?>

        <div id="cartForm">
            <div class="cart-wrap">
                <div class="cart-product">
                    <?php foreach($itemsArr as $id => $item):?>
                        <?php //debug($item); ?>
                    <div class="cart-product__item" data-id="<?= $id; ?>" data-productId="<?= $item['product']['id']; ?>" id="itemInfo_<?= $id; ?>">
                        <div class="cart-product__name" data-id="<?= $id; ?>">
                            <a href="/product/<?= $item['product']['alias']; ?>"><?= $item['product']['name']; ?></a>
                            <a class="cart-toggle itemToDel" id="itemToDel_<?= $id; ?>" href="#" data-proc="delete" alt="Удалить из корзины">Удалить</a>

                        </div>

                        <div class="cart-product__info">
                            <div class="cart-product__img" data-image="<?= $item['product']['image']; ?>"><img src="/images/products/<?= $item['product']['image']; ?>" style="width: 50px; margin-right: 10px;" />
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
                            <input name="itemCnt_<?= $id; ?>" class="itemCnt" id="itemCnt_<?= $id; ?>" type="text" value="<?= $item['qty'] ?>" data-count="<?= $item['count'] ?>" data-weight="<?= $item['weight'] ?>">
                            <span class="plus btn" data-proc="plus" id="plus_<?= $id; ?>">+</span>

                            <span class="itemPrice" id="itemPrice_<?= $id; ?>" value="<?= $item['price'];?>" style="display: none;"><?= $currency['symbol_left'];?><?= $item['price'];?><?= $currency['symbol_right'];?></span>
                            <span class="product-summa">
                                <span class="product-summ" id="itemRealPrice_<?= $id; ?>"><?= $currency['symbol_left'];?><span><?= $item['qty'] * $item['price'] * $currency['value'];?></span><?= $currency['symbol_right'];?></span>

                            </span>
                        </div>

                    </div>

                </div>
                <?php endforeach; ?>
            </div><!--cart-wrap-->

            <div class="cart-checkout">
                <input class="clear_cart" type="button" onclick="clearCart();" value="Очистить корзину"/>
                <div style="clear: both;"></div>
                <div class="cart-checkout__summ">
                    <span class="summ-text">К оплате:</span>
                        <?php //debug($currency);?>
                        <span class="product-summa">
                            <span class="summ-count"><?= $currency['symbol_left'] . $sum*$currency['value'] . $currency['symbol_right'];?></span>

                        </span>
                </div>
                <input class="cart-checkout__order" type="button" value="Оформить заказ"/>
            </div>
            <div class="user-order" style="display: none;">
                <?php if(isset($_SESSION['user'])):?>
                    <span class="user_order_info">ФИО:</span><input class="users_info" id="user_order_name" type="text" name= "name" value="<?= $_SESSION['user']['name'];?>" required/><br>
                    <span class="user_order_info">Адрес:</span><input class="users_info" id="user_order_adress" type="text" name= "adress" value="<?= $_SESSION['user']['address'];?>" required/><br>
                    <span class="user_order_info">E-mail:</span><input class="users_info" id="user_order_email" type="text" name= "email" value="<?= $_SESSION['user']['email'];?>" readonly/><br>
                    <span class="user_order_info">Телефон:</span><input class="users_info" id="user_order_phone" type="text" name= "phone" value="<?= $_SESSION['user']['phone'];?>" required/><br>
                    <span class="user_order_info">Прим:</span><textarea rows="3" cols="30" name="note" id="user_order_prim" value=""/></textarea><br>
                <?php else: ?>
                    <input id="user_login" type="button" value="Авторизоваться"/>
                    <input id="user_signup" type="button" value="Зарегистрироваться"/>
                    <br>
                    <span class="order_without_login">Чтобы оформить заказ без регистрации, заполните указанную ниже форму</span>

                    <span class="user_order_info">ФИО:</span><input class="users_info" id="user_order_name" type="text" value="" name= "name" required/><br>
                    <span class="user_order_info">Адрес:</span><input class="users_info" id="user_order_adress" type="text" value="" name= "adress" required/><br>
                    <span class="user_order_info">E-mail:</span><input class="users_info" id="user_order_email" type="text" value="" name= "email" required/><br>
                    <span class="user_order_info">Телефон:</span><input class="users_info" id="user_order_phone" type="text" value="" name= "phone" required/><br>
                    <span class="user_order_info">Прим:</span><textarea rows="3" cols="30" name="note" id="user_order_prim" value=""/></textarea><br>
                <?php endif;?>
                <input type="button" value= "Отправить" onclick="saveOrder();">
            </div>

            <div style="clear: both;"></div>

    </div><!---->
    </div>

    <?php endif; ?>
</section>