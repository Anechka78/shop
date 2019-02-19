<section class="content-header">
    <h1>
        Список пользователей
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?=ADMIN;?>"><i class="fa fa-dashboard"></i> Главная</a></li>
        <li> Список пользователей</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

    <div class="box">
        <div class="box-header">
            <h3 class="box-title">Список пользователей</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <table id="" class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Логин</th>
                    <th>Email</th>
                    <th>ФИО</th>
                    <th>Телефон</th>
                    <th>Адрес</th>
                    <th>Роль</th>
                    <th>Действия</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($users as $user): ?>
                    <tr>
                        <td><?= $user['id']?></td>
                        <td><?= $user['login']?></td>
                        <td><?= $user['email'];?></td>
                        <td><?= $user['name']?></td>
                        <td><?= $user['phone']?></td>
                        <td><?= $user['address']?></td>
                        <td><?= $user['role']?></td>
                        <td><a href="<?=ADMIN;?>/user/edit?id=<?=$user['id'];?>"><i class="fa fa-fw fa-eye"></i></a>&nbsp;&nbsp;&nbsp;<a class="delete" href="<?=ADMIN;?>/user/delete?id=<?=$user['id'];?>"><i class="fa fa-fw fa-close text-danger"></i></a></td>
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
            <p>Показано <?=count($users);?> пользователей из <?= $count;?></p>
            <?php if($pagination->countPages > 1):?>
                <?= $pagination; ?>
            <?php endif; ?>
        </div>
        <!-- /.box-body -->
    </div>
    <!-- /.box -->

</section>
<!-- /.content -->

