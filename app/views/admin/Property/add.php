<section class="content-header">

    <ol class="breadcrumb">
        <li><a href="<?=ADMIN;?>"><i class="fa fa-dashboard"></i> Главная</a></li>
        <li> Добавление характеристики</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <h3> Список существующих характеристик</h3>

    <div class="box">
        <select name="property_name" id="property_name" class="form-control" style="width: 100%;" tabindex="-1" aria-hidden="true">
            <option class="property" value="0">Список характеристик</option>
            <?php foreach($properties as $k=>$v):?>
                <option id="<?= h($v['id']);?>" class="property_name" value="<?= h($v['name']);?>"><?= h($v['name']);?></option>
            <?php endforeach;?>
        </select>
    </div>

    <div>
        <p id="property_values" style="font-style: italic; color: #f91754; font-family:Tahoma;"></p>
    </div>

    <h3>
        Добавление характеристики
    </h3>
    <input class="form-control" type="text" name="title" id="new_property" placeholder="Введите название новой характеристики" value="" required>
    <button type="submit" id="btn_new_property" class="btn btn-success">Добавить характеристику</button>
</section>
<!-- /.content -->