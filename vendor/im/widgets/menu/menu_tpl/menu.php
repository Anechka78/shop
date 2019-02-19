<ul class="nav"><?php foreach($tree as $category): ?>
    <li>
        <a  class=" mark" href="<?= $category['id'];?>"><?= $category['name']?></a>
        <i class="triangle"></i>
        <i class="triangle_white"></i>
        <ul class="sub-nav"  style="height: 0px;" >
            <?php if(isset($category['children'])):?>
                <table>
                    <?php foreach($category['children'] as $itemChild): ?>
                    <tr><td><a class="" href="<?= $itemChild['id']?>" data-count="1"><?= $itemChild['name']?></a></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>
        </ul>
    </li>
    <?php endforeach; ?>
</ul>