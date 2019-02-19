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
                    <th>Покупатель</th>
                    <th>Статус</th>
                    <th>Сумма</th>
                    <th>Валюта</th>
                    <th>Дата создания</th>
                    <th>Дата изменения</th>
                    <th>Редактирование</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($orders as $order): ?>
                <tr>
                    <td><?= $order['id']?></td>
                    <td><?= $order['user_name']?></td>
                    <td><?= $order['status'] ? 'Завершен' : 'Новый';?></td>
                    <td><?= $order['sum']?></td>
                    <td><?= $order['currency']?></td>
                    <td><?= $order['date_created']?></td>
                    <td><?= $order['date_modification']?></td>
                    <td><a href="<?= ADMIN;?>/order/view?id=<?= $order['id']?>"><i class="fa fa-fw fa-eye"></i></a></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
                <tfoot>
                <tr>
                    <th>ID заказа</th>
                    <th>Покупатель</th>
                    <th>Статус</th>
                    <th>Сумма</th>
                    <th>Валюта</th>
                    <th>Дата создания</th>
                    <th>Дата изменения</th>
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
