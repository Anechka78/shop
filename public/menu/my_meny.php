    <li>
        <a  class="" href="/category/<?= $category['alias'];?>"><?= $category['name']?></a>
        <!--<i class="triangle"></i>
        <i class="triangle_white"></i>-->
        <ul class="">
            <?php if(isset($category['children'])):?>
                <?= $this->getMenuHtml($category['children']);?>
            <?php endif; ?>
        </ul>
    </li>

