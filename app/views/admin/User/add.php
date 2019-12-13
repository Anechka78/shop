<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Добавление пользователя
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= ADMIN ?>"><i class="fa fa-dashboard"></i> Главная</a></li>
        <li><a href="<?= ADMIN ?>/user"> Список пользователей</a></li>
        <li class="active">Добавление пользователя</li>
    </ol>
</section>
<?php //debug($_SESSION['error']);?>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <form method="post" action="<?= ADMIN ?>/user/signup" role="form">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="login">Логин</label>
                            <input class="form-control" name="login" id="login" type="text" value="<?= isset($_SESSION['form_data']['login']) ? $_SESSION['form_data']['login'] : '' ?>">
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input class="form-control" name="pwd1" id="password" type="password"  required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input class="form-control" name="email" id="email" type="email" value="<?= isset($_SESSION['form_data']['email']) ? $_SESSION['form_data']['email'] : '' ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="name">Имя</label>
                            <input class="form-control" name="name" id="name" type="text" value="<?= isset($_SESSION['form_data']['name']) ? $_SESSION['form_data']['name'] : '' ?>">
                        </div>
                        <div class="form-group">
                            <label for="address">Адрес</label>
                            <input class="form-control" name="address" id="address" value="<?= isset($_SESSION['form_data']['address']) ? $_SESSION['form_data']['address'] : '' ?>">
                        </div>
                        <div class="form-group">
                            <label for="address">Телефон</label>
                            <input class="form-control" name="phone" id="phone" value="<?= isset($_SESSION['form_data']['phone']) ? $_SESSION['form_data']['phone'] : '' ?>">
                        </div>
                        <div class="form-group">
                            <label>Роль</label>
                            <select class="form-control" name="role">
                                <option value="user">Пользователь</option>
                                <option value="admin">Администратор</option>
                            </select>
                        </div>
                    </div>
                    <!-- /.box-body -->

                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary"">Добавить</button>
                    </div>
                </form>
                <?php if(isset($_SESSION['form_data'])) unset($_SESSION['form_data']); ?>
            </div>
        </div>
    </div>
</section>
<!-- /.content -->