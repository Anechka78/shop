<section class="content-header">

    <ol class="breadcrumb">
        <li><a href="<?=ADMIN;?>"><i class="fa fa-dashboard"></i> Главная</a></li>
        <li> Добавление значения для характеристики</li>
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
        <p style="font-style: italic;  font-family:Tahoma;">Выберите характеристику, чтобы увидеть ее существующие значения</p>
        <p id="property_values" style="font-style: italic; color: #f91754; font-family:Tahoma;"></p>
    </div>

    <h3>
        Добавление данных для характеристики
    </h3>
    <input class="form-control" type="text" name="property_name" id="new_property_name" placeholder="Наименование" value="" required>
    <div><p style="font-style: italic; font-family:Tahoma;">Введите название значения характеристики, например: "красный"</p></div>
    <input class="form-control" type="text" name="val" id="new_property_value" placeholder="Значение" value="">
    <div><p style="font-style: italic; font-family:Tahoma;">Введите значение, если оно есть. Например: "#FF0000" для отображения цвета</p></div>
    <button type="submit" id="btn_new_property_value" class="btn btn-success">Добавить</button>
</section>
<!-- /.content -->