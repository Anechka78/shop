<?php $parent_id=\im\core\App::$app->getProperty('parent_id');?>
<option value="<?=$id;?>"<?php if($id == $parent_id) echo ' selected'; ?><?php if(isset($_GET['id']) && $id == $_GET['id']) echo ' disabled'; ?>><?=$tab . $category['name'];?></option>
<?php if(isset($category['children'])): ?>
    <?= $this->getMenuHtml($category['children'], '&nbsp;' . $tab. '-') ?>
<?php endif; ?>


