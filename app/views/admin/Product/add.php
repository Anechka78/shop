<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 id="h1_add_new_prodact">
        Добавление товара <?echo isset($_SESSION['add_product'][$secret_key]['name']) ? h($_SESSION['add_product'][$secret_key]['name']) : null; ?>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?=ADMIN;?>"><i class="fa fa-dashboard"></i> Главная</a></li>
        <li><a href="<?=ADMIN;?>/product">Список товаров</a></li>
        <li class="active">Добавление товара</li>
    </ol>
</section>
<?php unset($_SESSION['single']); unset($_SESSION['multi']); unset($_SESSION['pd']); unset($_SESSION['pv']);?>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box">
<!--                <form action="--><?//=ADMIN;?><!--/product/add" method="post" class="formData">-->
                    <div class="box-body">
                        <ul class="nav nav-tabs">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#main_info">Основная информация</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#product_pd">Зависимые характеристики</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#product_pv">Простые характеристики</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#related">Сопутствующие товары</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#images">Фото</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane in active fade" id="main_info">
<!--                                <form action="--><?//=ADMIN;?><!--/product/addMainInfo" method="post" class="formData add_main_info">-->
                                    <form action="<?=ADMIN;?>/product/addMainInfo" method="post" class="formData add_main_info">
                                    <input id="secret_key" name="secret_key" value="<?= $secret_key; ?>" style="display: none;">
                                    <div class="form-group">
                                        <label for="name">Наименование товара</label>
                                        <input type="text" name="name" class="form-control" id="name" placeholder="Наименование товара" value="<?php echo isset($_SESSION['add_product'][$secret_key]['name']) ? h($_SESSION['add_product'][$secret_key]['name']) : null; ?>" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="category_id">Родительская категория</label>
                                        <?php new \im\widgets\menu\Menu([
                                            'tpl' => WWW . '/menu/select.php',
                                            'container' => 'select',
                                            'cache' => 0,
                                            'cacheKey' => 'admin_select',
                                            'class' => 'form-control',
                                            'attrs' => [
                                                'name' => 'category_id',
                                                'id' => 'category_id',
                                            ],
                                            'prepend' => '<option>Выберите категорию</option>',
                                        ]) ?>
                                    </div>

                                    <div class="form-group">
                                        <label for="vendor">Производитель</label>
                                        <select class="form-control" id="vendor" name="vendor">
                                            <option value="">Выберите производителя</option>
                                            <?php foreach($vendors as $k=>$v):?>
                                                <option value="<?= (isset($_SESSION['add_product'][$secret_key]['vendor']) &&($_SESSION['add_product'][$secret_key]['vendor'] == $v['id']))? $v['id']: $v['id']?>" <?php echo isset($_SESSION['add_product'][$secret_key]) ? ' selected' : null; ?>><?= $v['name']?></option>
                                            <?php endforeach;?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="title">Тайтл (для СЕО)</label>
                                        <input type="text" name="title" class="form-control" id="title" placeholder="Заголовок в браузере" value="<?php echo isset($_SESSION['add_product'][$secret_key]['title']) ? h($_SESSION['add_product'][$secret_key]['title']) : null; ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="description">Краткое описание (meta-description)</label>
                                        <input type="text" name="meta_desc" class="form-control" id="meta_desc" placeholder="Краткое описание" value="<?php echo isset($_SESSION['add_product'][$secret_key]['meta_desc']) ? h($_SESSION['add_product'][$secret_key]['meta_desc']) : null; ?>">
                                    </div>

                                    <div class="form-group has-feedback">
                                        <label for="content">Описание</label>
                                        <textarea name="description" id="editor1" cols="80" rows="10"><?php echo isset($_SESSION['add_product'][$secret_key]['description']) ? $_SESSION['add_product'][$secret_key]['description'] : null; ?></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label for="price">Цена</label>
                                        <input type="text" name="price" class="form-control" id="price" placeholder="Цена (допускаются цифры и десятичная точка)" pattern="^[0-9.]{1,}$" value="<?php echo isset($_SESSION['add_product'][$secret_key]['price']) ? h($_SESSION['add_product'][$secret_key]['price']) : null; ?>" required data-error="Допускаются цифры и десятичная точка">
                                        <div class="help-block with-errors"></div>
                                    </div>

                                    <div class="form-group">
                                        <label for="old_price">Старая цена</label>
                                        <input type="text" name="old_price" class="form-control" id="old_price" placeholder="Старая цена (допускаются цифры и десятичная точка)" pattern="^[0-9.]{1,}$" value="<?php echo isset($_SESSION['add_product'][$secret_key]['old_price']) ? h($_SESSION['add_product'][$secret_key]['old_price']) : null; ?>" data-error="Допускаются цифры и десятичная точка">
                                    </div>
                                    <div class="form-group">
                                        <label for="price">Количество</label>
                                        <input type="text" name="count" class="form-control" id="count" placeholder="Количество (допускаются цифры)" pattern="^[0-9.]{1,}$" value="<?php echo isset($_SESSION['add_product'][$secret_key]['count']) ? h($_SESSION['add_product'][$secret_key]['count']) : null; ?>" data-error="Допускаются цифры">
                                    </div>

                                    <div class="form-group">
                                        <label for="old_price">Вес</label>
                                        <input type="text" name="weight" class="form-control" id="weight" placeholder="Вес товара (допускаются цифры и десятичная точка)" pattern="^[0-9.]{1,}$" value="<?php echo isset($_SESSION['add_product'][$secret_key]['weight']) ? h($_SESSION['add_product'][$secret_key]['weight']) : null; ?>" data-error="Допускаются цифры и десятичная точка">

                                    </div>

                                    <div class="form-group">
                                        <label>
                                            <input type="checkbox" name="status" id="status"> Статус
                                        </label>
                                    </div>

                                    <div class="form-group">
                                        <label>
                                            <input type="checkbox" name="hit" id="hit"> Хит
                                        </label>
                                    </div>

                                    <div class="form-group">
                                        <label>
                                            <input type="checkbox" name="new" id="n_new"> Новинка
                                        </label>
                                    </div>

                                    <div class="form-group">
                                        <label>
                                            <input type="checkbox" name="sale" id="sale"> Распродажа
                                        </label>
                                    </div>
                                    <button type="submit" id="btn-add_main_info" data-update="0" class="btn btn-success">Добавить информацию о товаре</button>
                                </form>
                            </div>
                            <div class="tab-pane fade" id="product_pd">
                                <div class="box-body">
                                    <div class="properties_dependences">
                                        <div class="form-group">
                                            <h3 class="box-title">Зависимые характеристики (типа размер-цвет)</h3>
                                            <select id="parent_name" name="parent_name" class="form-control" style="width: 100%;" tabindex="-1" aria-hidden="true">
                                                <option class="parent_name" value="0">Выберите главную характеристику</option>
                                                <?php foreach($mods as $k=>$v):?>
                                                    <option class="parent_name" value="<?= $v['id']?>"><?= $v['name']?></option>
                                                <?php endforeach;?>
                                            </select>
                                            <select id="parent_names" name="parent_names" class="form-control" style="width: 100%;" tabindex="-1" aria-hidden="true">
                                                <option value="0">Выберите значение</option>
                                            </select><br>
                                            <select id="child_name" name="child_name" class="form-control" style="width: 100%;" tabindex="-1" aria-hidden="true">
                                                <option value="0">Выберите зависимую характеристику</option>
                                                <?php foreach($mods as $k=>$v):?>
                                                    <option value="<?= $v['id']?>"><?= $v['name']?></option>
                                                <?php endforeach;?>
                                            </select>
                                            <select id="child_names" name="child_names" class="form-control" style="width: 100%;" tabindex="-1" aria-hidden="true">
                                                <option value="0">Выберите значение</option>
                                            </select>
                                        </div>
                                        <!-- /.form-group -->
                                        <div class="form-group">
                                            <label for="pd_count">Количество на складе, шт. (если требуется)</label>
                                            <input type="text" name="pd_count" class="form-control" id="pd_count" placeholder="Кол-во товаров" value="">
                                        </div>
                                        <div class="form-group">
                                            <label for="pd_price">Цена товара (если требуется)</label>
                                            <input type="text" name="pd_price" class="form-control" id="pd_price" placeholder="Цена" value="">
                                        </div>
                                        <div class="form-group">
                                            <label for="pd_oldprice">Старая цена товара (если требуется)</label>
                                            <input type="text" name="pd_oldprice" class="form-control" id="pd_oldprice" placeholder="Старая цена" value="">
                                        </div>
                                        <div class="form-group">
                                            <label for="pd_weight">Вес в кг. 1 шт. товара (если требуется)</label>
                                            <input type="text" name="pd_weight" class="form-control" id="pd_weight" placeholder="Вес" value="">
                                        </div>
                                        <button type="button" class="btn btn-block btn-danger" onclick="addPropertyDep();">Добавить зависимую характеристику</button>
                                    </div>

                                    <div class="box-body" id="table-pd" style="display: none;">
                                        <label for="mods">Таблица зависимых характеристик</label>
                                        <table class="table table-bordered table-striped" id="mods">
                                            <thead>
                                            <tr>
                                                <th class="col-xs-1">ID</th>
                                                <th>Главная х-ка</th>
                                                <th>Значение</th>
                                                <th>Зависимая х-ка</th>
                                                <th>Значение</th>
                                                <th class="col-xs-1">Кол-во</th>
                                                <th class="col-xs-1">Цена</th>
                                                <th class="col-xs-1">Старая цена</th>
                                                <th class="col-xs-1">Вес</th>
                                                <th class="col-xs-1">Удалить</th>
                                            </tr>
                                            </thead>
                                            <tbody id="properties_dependences_values">
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- /.col -->

                                    <!-- /.col -->
                                </div>
                            </div>
                            <div class="tab-pane fade" id="product_pv">
                                <div class="properties_values">
                                    <div class="form-group">
                                        <h3 class="box-title">Обычные характеристики</h3>
                                        <select id="prop_name" name="prop_name" class="form-control" style="width: 100%;" tabindex="-1" aria-hidden="true">
                                            <option value="0">Выберите простую характеристику</option>
                                            <?php foreach($mods as $k=>$v):?>
                                                <option value="<?= $v['id']?>"><?= $v['name']?></option>
                                            <?php endforeach;?>
                                        </select><br>
                                        <select id="prop_value" name="prop_value" class="form-control" style="width: 100%;" tabindex="-1" aria-hidden="true">
                                            <option value="0">Выберите значение характеристики</option>
                                        </select>
                                    </div>
                                    <!-- /.form-group -->
                                    <div class="form-group pv_count">
                                        <label for="pv_count">Количество на складе, шт. (если требуется)</label>
                                        <input type="text" name="pv_count" class="form-control" id="pv_count" placeholder="Кол-во товаров" value="">
                                    </div>
                                    <div class="form-group pv_price">
                                        <label for="pv_price">Цена товара (если требуется)</label>
                                        <input type="text" name="pv_price" class="form-control" id="pv_price" placeholder="Цена" value="">
                                    </div>
                                    <div class="form-group pv_oldprice">
                                        <label for="pv_oldprice">Старая цена товара (если требуется)</label>
                                        <input type="text" name="pv_oldprice" class="form-control" id="pv_oldprice" placeholder="Старая цена" value="">
                                    </div>
                                    <div class="form-group pv_weight">
                                        <label for="pv_weight">Вес в кг. 1 шт. товара (если требуется)</label>
                                        <input type="text" name="pv_weight" class="form-control" id="pv_weight" placeholder="Вес" value="">
                                    </div>
                                    <button type="button" class="btn btn-block btn-warning" onclick="addPropertyVal();">Добавить обычную характеристику</button>
                                </div>
                                <div class="box-body" id="table-pv" style="display: none;">
                                    <label for="mods">Таблица обычных характеристик</label>
                                    <table class="table table-bordered table-striped" id="pv">
                                        <thead>
                                        <tr>
                                            <th class="col-xs-1">ID</th>
                                            <th>Характеристика</th>
                                            <th>Значение</th>
                                            <th class="col-xs-1">Кол-во</th>
                                            <th class="col-xs-1">Цена</th>
                                            <th class="col-xs-1">Старая цена</th>
                                            <th class="col-xs-1">Вес</th>
                                            <th class="col-xs-1">Удалить</th>
                                        </tr>
                                        </thead>
                                        <tbody id="properties_values_values">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="related" style="padding-top: 15px;">
                                <form action="<?=ADMIN;?>/product/addRelated" method="post" class="formData add_related" id="form-add_related">
                                    <input id="secret_key" name="secret_key" value="<?= $secret_key; ?>" style="display: none;">
                                    <input id="add-product_id" name="product_id" value="" style="display: none;">
                                    <div class="form-group">
                                        <label for="related">Связанные товары</label>
                                        <select name="related[]" style="width:300px;" class="form-control related select2" id="related" multiple>
                                        </select>
                                    </div>
                                    <button type="submit" id="btn-add_related" data-update="0" class="btn btn-success">Добавить связанные товары</button>
                                </form>
                            </div>
                            <div class="tab-pane fade" id="images">
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <div class="box box-success box-solid file-upload">
                                            <div class="box-header">
                                                <h3 class="box-title">Изображения товара (первое изображение - главное)</h3>
                                            </div>
                                            <div class="box-body">
                                                <div id="multi" class="btn btn-success" data-url="product/add-image" data-name="multi">Выбрать файл</div>
                                                <p><small>Вес картинки: до 1 Мб</small></p>
                                                <div class="multi"></div>
                                            </div>
                                            <div class="overlay">
                                                <i class="fa fa-refresh fa-spin"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-warning">Добавить и активировать товар</button>
                    </div>
<!--                </form>-->
                <?php if(isset($_SESSION['add_product'][$secret_key])) unset($_SESSION['add_product'][$secret_key]); ?>
            </div><!-- /.box -->
        </div>
    </div>

</section>
<!-- /.content -->
