<section class="content-header">
    <h1>
        Список категорий
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?=ADMIN;?>"><i class="fa fa-dashboard"></i> Главная</a></li>
        <li><a href="<?=ADMIN;?>/category">Список категорий</a></li>
        <li class="active">Добавление категории</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

    <div class="box">
        <form action="<?=ADMIN;?>/category/add" method="post">
            <!-- box-body -->
            <div class="box-body">
                <div class="form-group">
                    <label for="name">Наименование категории</label>
                    <input type="text" name="name" class="form-control" id="title" placeholder="Наименование категории" required>
                    <span class="glyphicon" aria-hidden="true"></span>
                </div>
                <div class="form-group">
                    <label for="title">Тайтл категории (для СЕО)</label>
                    <input type="text" name="title" class="form-control" id="title" placeholder="Наименование категории">
                    <span class="glyphicon" aria-hidden="true"></span>
                </div>
                <div class="form-group">
                    <label for="parent_id">Родительская категория</label>
                    <?php new \im\widgets\menu\Menu([
                        'tpl' => WWW . '/menu/select.php',
                        'container' => 'select',
                        'cache' => 0,
                        'cacheKey' => 'admin_select',
                        'class' => 'form-control',
                        'attrs' => [
                            'name' => 'parent_id',
                            'id' => 'parent_id',
                        ],
                        'prepend' => '<option value="0">Самостоятельная категория</option>',
                    ]) ?>
                </div>
                <div class="form-group">
                    <label for="description">Описание</label>
                    <input type="text" name="description" class="form-control" id="description" placeholder="Описание">
                    <span class="glyphicon" aria-hidden="true"></span>
                </div>
            </div>
            <div class="box-footer">
                <button type="submit" class="btn btn-success">Добавить</button>
            </div>
            <!-- /.box-body -->
        </form>
    </div>
    <!-- /.box -->
</section>
<!-- /.content -->