<section class="content-header">
    <h1>
        Список характеристик
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?=ADMIN;?>"><i class="fa fa-dashboard"></i> Главная</a></li>
        <li> Список характеристик</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<div class="box">

    <!-- /.col -->

    <!-- /.row -->
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-body table-responsive no-padding">
                    <table class="table table-bordered table-striped" >
                        <tr>
                            <th>Название характеристики</th>
                            <th>Ее значения</th>
                        </tr>
                        <?php foreach($properties as $property): ?>
                        <tr>
                            <td><?= $property['name']?></td>
                            <td><button type="button" class="btn btn-success property" id="<?= $property['id']?>">Показать</button></td>
                        </tr>
                        <?php $str=''; ?>
                        <?php foreach($propertiesValues as $propertyValue): ?>
                            <?php if($property['id'] == $propertyValue['property_id']):?>
                                <?php $str .= (($str=='')?'':' | ').$propertyValue['name'];?>
                            <?php endif;?>
                        <?php endforeach; ?>
                        <tr>
                            <td colspan="2" id="td_<?= $property['id']?>" style="display: none;"><?= $str ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
    </div>


</section>
<!-- /.content -->