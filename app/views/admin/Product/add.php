<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Новый товар
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?=ADMIN;?>"><i class="fa fa-dashboard"></i> Главная</a></li>
        <li><a href="<?=ADMIN;?>/product">Список товаров</a></li>
        <li class="active">Новый товар</li>
    </ol>
</section>
<?php unset($_SESSION['single']); unset($_SESSION['multi']); unset($_SESSION['pd']); unset($_SESSION['pv']);?>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <form action="<?=ADMIN;?>/product/add" method="post" data-toggle="validator">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="name">Наименование товара</label>
                            <input type="text" name="name" class="form-control" id="name" placeholder="Наименование товара" value="<?php isset($_SESSION['form_data']['name']) ? h($_SESSION['form_data']['name']) : null; ?>" required>
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
                                <option value="0">Выберите производителя</option>
                                <?php foreach($vendors as $k=>$v):?>
                                <option value="<?= $v['id']?>"><?= $v['name']?></option>
                                <?php endforeach;?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="title">Тайтл (для СЕО)</label>
                            <input type="text" name="title" class="form-control" id="title" placeholder="Заголовок в браузере" value="<?php isset($_SESSION['form_data']['title']) ? h($_SESSION['form_data']['title']) : null; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="description">Краткое описание (meta-description)</label>
                            <input type="text" name="meta_desc" class="form-control" id="meta_desc" placeholder="Краткое описание" value="<?php isset($_SESSION['form_data']['meta_desc']) ? h($_SESSION['form_data']['meta_desc']) : null; ?>">
                        </div>

                        <div class="form-group has-feedback">
                            <label for="content">Описание</label>
                            <textarea name="description" id="editor1" cols="80" rows="10"><?php isset($_SESSION['form_data']['description']) ? $_SESSION['form_data']['description'] : null; ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="price">Цена</label>
                            <input type="text" name="price" class="form-control" id="price" placeholder="Цена (допускаются цифры и десятичная точка)" pattern="^[0-9.]{1,}$" value="<?php isset($_SESSION['form_data']['price']) ? h($_SESSION['form_data']['price']) : null; ?>" required data-error="Допускаются цифры и десятичная точка">
                            <div class="help-block with-errors"></div>
                        </div>

                        <div class="form-group">
                            <label for="old_price">Старая цена</label>
                            <input type="text" name="old_price" class="form-control" id="old_price" placeholder="Старая цена (допускаются цифры и десятичная точка)" pattern="^[0-9.]{1,}$" value="<?php isset($_SESSION['form_data']['old_price']) ? h($_SESSION['form_data']['old_price']) : null; ?>" data-error="Допускаются цифры и десятичная точка">
                        </div>
                        <div class="form-group">
                            <label for="price">Количество</label>
                            <input type="text" name="count" class="form-control" id="count" placeholder="Количество (допускаются цифры)" pattern="^[0-9.]{1,}$" value="<?php isset($_SESSION['form_data']['count']) ? h($_SESSION['form_data']['count']) : null; ?>" data-error="Допускаются цифры">
                        </div>

                        <div class="form-group">
                            <label for="old_price">Вес</label>
                            <input type="text" name="weight" class="form-control" id="weight" placeholder="Вес товара (допускаются цифры и десятичная точка)" pattern="^[0-9.]{1,}$" value="<?php isset($_SESSION['form_data']['weight']) ? h($_SESSION['form_data']['weight']) : null; ?>" data-error="Допускаются цифры и десятичная точка">

                        </div>

                        <div class="form-group">
                            <label>
                                <input type="checkbox" name="status" checked> Статус
                            </label>
                        </div>

                        <div class="form-group">
                            <label>
                                <input type="checkbox" name="hit"> Хит
                            </label>
                        </div>
                        <div class="form-group">
                            <label for="related">Связанные товары</label>
                            <select name="related[]" class="form-control related select2" id="related" multiple>
                            </select>
                        </div>

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

                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-6 properties_dependences">
                                    <div class="form-group">
                                        <label for="parent_name">Зависимые характеристики (типа размер-цвет)</label>
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
                                    <button type="button" class="btn btn-block btn-info" onclick="addPropertyDep();">Добавить</button>
                                </div>
                                <!-- /.col -->
                                <div class="col-md-6 properties_values">
                                    <div class="form-group">
                                        <label>Обычные характеристики</label>
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
                                    <button type="button" class="btn btn-block btn-info" onclick="addPropertyVal();">Добавить</button>
                                </div>
                                <!-- /.col -->
                            </div>
                            <!-- /.row -->
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-6" id="properties_dependences_values">
                                </div>
                                <div class="col-md-6" id="properties_values_values">
                                </div>
                                <!-- /.col -->
                            </div>
                            <!-- /.row -->
                        </div>


<?php debug($_SESSION);?>
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-success">Добавить</button>
                    </div>
                </form>
                <?php if(isset($_SESSION['form_data'])) unset($_SESSION['form_data']); ?>
            </div>
        </div>
    </div>

</section>
<!-- /.content -->