<section class="content-header">
    <h1>
        Редактирование профиля пользователя
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?=ADMIN;?>"><i class="fa fa-dashboard"></i> Главная</a></li>
        <li><a href="<?=ADMIN;?>/user">Список пользователей</a></li>
        <li> Редактирование профиля</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

    <div class="box">
        <div class="box-header">
            <h3 class="box-title">Редактирование профиля</h3>
        </div>
        <!-- /.box-header -->
        <form action="<?=ADMIN;?>/user/edit" method="post">
            <div class="box-body">
                <div class="form-group">
                    <label for="login">Логин</label>
                    <input type="text" class="form-control" name="login" id="login" value="<?=h($user['login']);?>">
                </div>
                <div class="form-group">
                    <label for="password">Пароль</label>
                    <input type="password" class="form-control" name="pwd1" id="pwd1" placeholder="Введите пароль, если хотите его изменить">
                </div>
                <div class="form-group">
                    <label for="name">ФИО</label>
                    <input type="text" class="form-control" name="name" id="name" value="<?=h($user['name']);?>">
                </div>
                <div class="form-group">
                    <label for="phone">Телефон</label>
                    <input type="text" class="form-control" name="phone" id="phone" value="<?=h($user['phone']);?>">
                </div>
                <div class="form-group has-feedback">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" name="email" id="email" value="<?=h($user['email']);?>" required>
                </div>
                <div class="form-group">
                    <label for="address">Адрес</label>
                    <input type="text" class="form-control" name="address" id="address" value="<?=h($user['address']);?>">
                </div>
                <div class="form-group">
                    <label>Роль</label>
                    <select name="role" id="role" class="form-control">
                        <option value="user"<?php if($user['role'] == 'user') echo ' selected'; ?>>Пользователь</option>
                        <option value="admin"<?php if($user['role'] == 'admin') echo ' selected'; ?>>Администратор</option>
                    </select>
                </div>
            </div>
            <div class="box-footer">
                <input type="hidden" name="id" value="<?=$user['id'];?>">
                <button type="submit" class="btn btn-primary">Сохранить</button>
            </div>
        </form>
        <!-- /.box-body -->
    </div>
    <!-- /.box -->

</section>
<!-- /.content -->