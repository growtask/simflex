<?php
$colRowActionsWidth = 14 + 32 * $this->rowActionsCnt;
?>


<div class="content">
<div class="content__inner">
    <div class="content__head">
        <div class="content__head-wrap">
            <div class="content__head-bg"></div>
            <div class="content__head-left">
                <h4 class="content__title"><?php echo $this->title ?></h4>
                <button type="button" class="content__head-toggle BtnIconPrimarySm">
                    <svg class="notification__close-icon" fill="#ffffff" viewBox="0 0 24 24">
                        <use xlink:href="<?=asset('img/icons/svg-defs.svg')?>#arrow"></use>
                    </svg>
                </button>
            </div>
            <div class="content__head-right">
                <div class="content__head-btns">
                    <?php $this->showActions() ?>
                </div>
            </div>
        </div>
    </div>
    <div class="content__body">
        <?php \Simflex\Admin\Plugins\Alert\Alert::output() ?>
        <div class="tables">
            <form class="table-top-form" >
                <table class="table">
                    <thead class="table__head">
                        <tr class="table__head-row">
                            <th class="table-data-select table__head-col table__head-col-first">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M5 12L10 17L19 8" stroke="#0D0D0D" stroke-width="2" stroke-linecap="round"
                                          stroke-linejoin="round" />
                                </svg>
                                <div class="form-control">
                                    <input type="checkbox" class="form-control__checkbox" />
                                </div>
                            </th>
                            <?php foreach ($this->fields as $f):?>
                                <?php if ($f->isVisible): ?>
                            <th class="table-data-<?=$f->name?> table__head-col">
                                <div class="form-control table__head-sort">
                                    <div class="form-control__sort">
                                        <span class="form-control__sort-title"><?=$f->label?></span>
                                        <div class="form-control__sort-btns">
                                            <div class="form-control__sort-up <?=($f->name == $this->order && !$this->desc) ? 'form-control__sort-up--active' : ''?>">
                                                <a class="form-control__sort-link" href="?o=<?=$f->name?>"></a>
                                            </div>
                                            <div class="form-control__sort-down <?=($f->name == $this->order && $this->desc) ? 'form-control__sort-down--active' : ''?>" >
                                                <a class="form-control__sort-link" href="?o=<?=$f->name?>"></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php $this->filterField($f)?>
                            </th>
                            <?php endif; ?>
                            <?php endforeach; ?>
                            <th class="table-data-controls table__head-col">
                                <div class="form-control">
                                    <div class="form-control__sort">
                                        <span class="form-control__sort-title">Действия</span>

                                    </div>
                                </div>
                                <button class="btn-reset">
                                    <div class="BtnSecondaryMonoSm">Сброс</div>
                                </button>
                            </th>
                        </tr>
                    </thead>
                </table>
            </form>
            <div class="table__wrapper" data-simplebar>
                <table class="table">
                    <tbody class="table__body">
                        <?php
                        $i = 0;
                        foreach ($rows as $row): ?>
                        <tr class="table__row table__body-row">
                            <td class="table-data-select">
                                <div class="form-control">
                                    <input type="checkbox" name="row[]" value="<?=$row[$this->pk->name]?>" id="" class="form-control__checkbox" />
                                </div>
                            </td>
                            <?php foreach ($this->fields as $field): ?>
                                <?php if ($field->isVisible): ?>
                                    <td  data-field="<?=$field->name?>" data-pk="<?=$row[$this->pk->name]?>" class="table-data-<?=$field->name?> <?=$field->fk?'table-data-id':''?>">
                                            <?php $field->pkValue = $row[$this->pk->name]; ?>
                                        <?=$this->showCell($field, $row)?>
                                    </td>
                                <?php endif ?>
                            <?php endforeach ?>
                            <td class="table-data-controls">
                                                                    <?php $this->rowActions($row, $i)?>
                            </td>
                        </tr>
                        <?php
                            $i++;
                        endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php echo $pagecontrol->content(); ?>
    </div>
</div>

<script>
    const nd =  document.createElement('style');
    nd.innerHTML = `
    <?php foreach ($this->fields as $f): ?>
    <?php if ($f->isVisible && $f->width > 1): ?>
    .table-data-<?=$f->name?> {
        width: <?=(double)$f->width/1920.0*100?>vw !important;
    }

    @media screen and (max-width: 1401px) {
        .table-data-<?=$f->name?> {
            width: <?=(double)$f->width/1366.0*100?>vw !important;
        }
    }

    @media screen and (max-width: 991px) {
        .table-data-<?=$f->name?> {
            width: <?=(double)$f->width/768.0*100?>vw !important;
            <?php if ($f->widthMob == 0): ?>
                display: none;
            <?php endif; ?>
        }
    }

    <?php endif; ?>

    <?php if ($f->isVisible && ($f->widthMob > 1 || $f->widthMob == 0)): ?>
    @media screen and (max-width: 576px) {
        .table-data-<?=$f->name?> {
        <?php if ($f->widthMob > 1): ?>
            width: <?=(double)$f->widthMob/375.0*100?>vw !important;
        <?php elseif ($f->widthMob == 0): ?>
            display: none;
        <?php endif; ?>
        }
    }
    <?php endif; ?>

    <?php endforeach; ?>
   `;
   document.head.append(nd);
</script>

</div>