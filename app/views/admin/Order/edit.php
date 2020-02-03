<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Просмотр и редактирование заказа <span style="color: #E60415"><?= h($order_info['id']); ?> </span> от <?= h($order_info['date_created']); ?>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?=ADMIN;?>"><i class="fa fa-dashboard"></i> Главная</a></li>
        <li><a href="<?=ADMIN;?>/orders">Список заказов</a></li>
        <li class="active">Просмотр и редактирование заказа</li>
    </ol>
</section>
<?php //debug($order_info['shipping_info']);//unset($_SESSION['single']); unset($_SESSION['multi']); unset($_SESSION['pd']); unset($_SESSION['pv']);?>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-6">
            <!-- general form elements -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Данные для доставки</h3>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <form role="form" id="shipping_info">
                    <div class="box-body">

                        <div class="form-group">
                            <label for="name">Ф.И.О. получателя</label>
                            <input type="text" class="form-control" id="name" placeholder="Ф.И.О. получателя" value="<?= h($order_info['shipping_info']['name']); ?>">
                        </div>
                        <div class="form-group">
                            <label for="email">Email клиента</label>
                            <input type="email" class="form-control" id="email" placeholder="Email клиента" value="<?= h($order_info['shipping_info']['email']); ?>">
                        </div>
                        <div class="form-group">
                            <label for="adress">Адрес получателя</label>
                            <input type="text" class="form-control" id="adress" placeholder="Адрес получателя" value="<?= h($order_info['shipping_info']['adress']); ?>">
                        </div>
                        <div class="form-group">
                            <label for="phone">Телефон получателя</label>
                            <input type="text" class="form-control" id="phone" placeholder="Телефон получателя" value="<?= h($order_info['shipping_info']['phone']); ?>">
                        </div>
                        <div class="form-group">
                            <label for="note">Примечание к заказу</label>
                            <input type="text" class="form-control" id="note" placeholder="Пожелания клиента" value="<?= h($order_info['shipping_info']['note']); ?>">
                        </div>


                    </div>
                    <!-- /.box-body -->

                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary" id="change_shipping-info">Изменить</button>
                    </div>
                </form>
            </div>
            <!-- /.box -->


    </div>
        <div class="col-md-6">
            <!-- general form elements -->
            <div class="box box-primary"  style="background-color: #ffb6c1;">
                <div class="box-header with-border">
                    <h3 class="box-title">Данные о клиенте</h3>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <form role="form">
                    <div class="box-body">

                        <div class="form-group">
                            <label for="user_name">Ф.И.О. клиента</label>
                            <input type="text" class="form-control" id="user_name"  value="<?= h($user_info['name']); ?>">
                        </div>
                        <div class="form-group">
                            <label for="user_email">Email клиента</label>
                            <input type="email" class="form-control" id="user_email"  value="<?= h($user_info['email']); ?>">
                        </div>
                        <div class="form-group">
                            <label for="user_adress">Адрес клиента</label>
                            <input type="text" class="form-control" id="user_adress"  value="<?= h($user_info['address']); ?>">
                        </div>
                        <div class="form-group">
                            <label for="user_phone">Телефон клиента</label>
                            <input type="text" class="form-control" id="user_phone"  value="<?= h($user_info['phone']); ?>">
                        </div>
                    </div>
                    <!-- /.box-body -->

                </form>
            </div>
            <!-- /.box -->
        </div>

        <table id="" class="table table-bordered table-striped">
            <thead>
            <tr>
                <th>ID в заказе</th>
                <th>ID товара//<br>Составной ID</th>
                <th>Наименование товара</th>
                <th>Характеристики</th>
                <th>К-во х Цена <br>Базовая валюта</th>
                <th>К-во х Цена <br>Валюта клиента</th>

            </tr>
            </thead>
            <tbody>
            <?php foreach($order_items as $order): ?>
                <tr>
                    <td><?= $order['id']?></td>
                    <td><?= $order['product_id']?>//<br><?= $order['multiple_id']?></td>
                    <td><a href="/product/<?= $order['product_info']['alias'] ?>/"><?= $order['product_info']['name'] ?></a><br>
                        <a href="/product/<?= $order['product_info']['alias'] ?>/">
                            <img style="width: 75px;" src="/images/products/prev/<?= $order['product_info']['image'] ?>"/>
                        </a></td>
                    <td>
                        <?php if(isset($order['product_info']['prDepArr'])):?>
                            <div><?= $order['product_info']['prDepArr']['p_name'] ?>: <?= $order['product_info']['prDepArr']['pv_name'] ?> </div>
                            <div><?= $order['product_info']['prDepArr']['ch_name'] ?>: <?= $order['product_info']['prDepArr']['ch_val'] ?></div>
                        <?php endif; ?>
                        <?php if(isset($order['product_info']['prValArr'])):?>
                            <?php foreach($order['product_info']['prValArr'] as $num=>$char):?>
                                <div><?= $char['p_name'] ?>: <?= $char['pv_name'] ?> </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </td>
                    <td><?= $order['qty']?>шт. x <?= $order['price'].' лв.'?></td>
                    <td><?= $order['qty']?>шт. x <?= $order['price']*$order_info['currency']['value'].' '.$order_info['currency']['code']?></td>

                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th>ИТОГО сумма:</th>
                <th><?= $order_info['sum'].' лв.'?></th>
                <th><?= $order_info['sum']*$order_info['currency']['value'].' '.$order_info['currency']['code']?></th>
            </tr>
            </tfoot>
        </table>
</section>
<!-- /.content -->