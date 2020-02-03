<section class="content-header">
    <h1>
        Список заказов
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?=ADMIN;?>"><i class="fa fa-dashboard"></i> Главная</a></li>
        <li> Список заказов</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

    <div class="box">
        <div class="box-header">
            <h3 class="box-title">Таблица заказов пользователей</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <table id="" class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>ID заказа</th>
                    <th>Дата создания</th>
                    <th>Покупатель</th>
                    <th>Статус</th>
                    <th>Сумма</th>
                    <th>Оплата</th>
                    <th>Информация для доставки</th>
                    <th>Прим. от магазина</th>
                    <th>Редактирование</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($orders as $order): ?>
                <tr data_order="<?= $order['id']?>" data_user="<?= $order['user_id']?>">
                    <td><?= $order['id']?></td>
                    <td><?= $order['date_created']?></td>
                    <td><?= $order['user_id']?></td>
                    <td><?php if($order['status'] == '0'): ?>
                        <?= 'Заказ принят';?>
                        <?php elseif($order['status'] == '1'): ?>
                        <?= 'Заказ подтвержден';?>
                        <?php elseif($order['status'] == '2'): ?>
                        <?= 'Заказ на сборке';?>
                        <?php elseif($order['status'] == '3'): ?>
                        <?= 'Заказ в пути';?>
                        <?php elseif($order['status'] == '4'): ?>
                        <?= 'Заказ доставлен';?>
                        <?php elseif($order['status'] == '5'): ?>
                        <?= 'Заказ завершен';?>
                        <?php endif; ?>
                    </td>
                    <td><?= $order['sum']?></td>
                    <td><?= $order['date_payment']?></td>
                    <td>
                        <?php foreach($order['shipping_info'] as $name=>$value): ?>
                            <?= $name?>: <?= $value?><br>
                        <?php endforeach; ?>


                    </td>
                    <td><?= $order['note']?></td>
                    <td><a href="<?= ADMIN;?>/order/edit?id=<?= $order['id']?>"><i class="fa fa-fw fa-eye"></i></a></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
                <tfoot>
                <tr>
                    <th>ID заказа</th>
                    <th>Дата создания</th>
                    <th>Покупатель</th>
                    <th>Статус</th>
                    <th>Сумма</th>
                    <th>Оплата</th>
                    <th>Информация для доставки</th>
                    <th>Прим. от магазина</th>
                    <th>Редактирование</th>
                </tr>
                </tfoot>
            </table>
        </div>
        <div class="text-center">
            <p>Показано <?=count($orders);?> заказа(ов) из <?= $count;?></p>
            <?php if($pagination->countPages > 1):?>
                <?= $pagination; ?>
            <?php endif; ?>
        </div>
        <!-- /.box-body -->
    </div>
    <!-- /.box -->




</section>
<!-- /.content -->
