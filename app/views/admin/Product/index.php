<section class="content-header">
    <h1>
        Список товаров
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?=ADMIN;?>"><i class="fa fa-dashboard"></i> Главная</a></li>
        <li> Список товаров</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

    <div class="box">
        <div class="box-header">
            <h3 class="box-title">Таблица товаров</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <table id="" class="table table-bordered table-striped">
                <thead>
                <tr class="sort">
                    <th><img class="sort-top"  data-val="id" data-dir="ASC" src="/images/sort_top.png" >ID товара<img  data-val="id" data-dir="DESC" src="/images/sort_bottom.png" img class="sort-bottom"</th>
                    <th><img class="sort-top"  data-val="category" data-dir="ASC" src="/images/sort_top.png" >Категория<img  data-val="category" data-dir="DESC" src="/images/sort_bottom.png" img class="sort-bottom"</th>
                    <th><img class="sort-top"  data-val="vendor" data-dir="ASC" src="/images/sort_top.png" >Производитель<img  data-val="vendor" data-dir="DESC" src="/images/sort_bottom.png" img class="sort-bottom"</th>
                    <th><img class="sort-top"  data-val="name" data-dir="ASC" src="/images/sort_top.png" >Наименование<img  data-val="name" data-dir="DESC" src="/images/sort_bottom.png" img class="sort-bottom"</th>
                    <th><img class="sort-top"  data-val="price" data-dir="ASC" src="/images/sort_top.png" >Цена<img  data-val="price" data-dir="DESC" src="/images/sort_bottom.png" img class="sort-bottom"</th>
                    <th><img class="sort-top"  data-val="hit" data-dir="ASC" src="/images/sort_top.png" >Хит<img  data-val="hit" data-dir="DESC" src="/images/sort_bottom.png" img class="sort-bottom"</th>
                    <th><img class="sort-top"  data-val="status" data-dir="ASC" src="/images/sort_top.png" >Статус<img  data-val="status" data-dir="DESC" src="/images/sort_bottom.png" img class="sort-bottom"</th>
                    <th>Действия</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($products as $product): ?>
                    <tr>
                        <td><?= $product['id']?></td>
                        <td><?= $product['category']?></td>
                        <td><?= $product['vendor_name'];?></td>
                        <td><a href="/product/<?=$product['alias'];?>">
                                <?= $product['name']?>
                            </a>
                        </td>
                        <td><?= $product['price']?></td>
                        <td><?= $product['hit']?></td>
                        <td><?= $product['status'] ? 'В наличии': 'Нет на складе';?></td>
                        <td><a href="<?=ADMIN;?>/product/edit?id=<?=$product['id'];?>"><i class="fa fa-fw fa-eye"></i></a>&nbsp;&nbsp;&nbsp;<a class="delete" href="<?=ADMIN;?>/product/delete?id=<?=$product['id'];?>"><i class="fa fa-fw fa-close text-danger"></i></a></td>
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
            <p>Показано <?=count($products);?> товара(ов) из <?= $count;?></p>
            <?php if($pagination->countPages > 1):?>
                <?= $pagination; ?>
            <?php endif; ?>
        </div>
        <!-- /.box-body -->
    </div>
    <!-- /.box -->




</section>
<!-- /.content -->
