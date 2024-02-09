<div class="data2 <?=$portletClass ?? ''?>">
    <div class="data2__head">
        <h4 class="data2__title"><?php echo $group['label'] ?></h4>
    </div>
    <div class="data2__content">
            <?php foreach ($group['fields'] as $field): ?>
                <?php include 'form.field.tpl' ?>
            <?php endforeach ?>
    </div>
</div>