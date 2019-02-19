<section class="content-header">
    <h1>
        Список категорий
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?=ADMIN;?>"><i class="fa fa-dashboard"></i> Главная</a></li>
        <li> Список категорий</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

    <div class="box">
        <div class="box-body">
            <?php new \im\widgets\menu\Menu([
                'tpl' => WWW . '/menu/category_admin.php',
                'container' => 'div',
                'table' => 'categories',
                'cache' => 0,
                'cacheKey' => 'admin_cat',
                'class' => 'list-group list-group-root well',
            ])?>
        </div>
        <!-- /.box-body -->
    </div>
    <!-- /.box -->




</section>
<!-- /.content -->