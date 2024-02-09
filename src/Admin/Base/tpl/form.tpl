<?php

$showRightCol = (bool)count($this->params['right']);
ob_start();
$this->portlets('right');
$rightPortletsHTML = ob_get_clean();
$showRightCol |= (bool)$rightPortletsHTML;
$artificial = false;
$artificialGroups = [];
?>

<form class="layout__content" method="post" action="?action=save" enctype="multipart/form-data">
    <div class="content">
        <div class="content__inner">
            <input id="sf-form-submit" type="hidden" name="submit_save" value=""/>
            <input type="hidden" name="request_uri" value="<?= $_SERVER['REQUEST_URI'] ?>"/>
            <input type="hidden" id="info-table" value="<?= $this->table ?>"/>
            <input type="hidden" id="info-key-name" value="<?= @$this->pk->name ?>"/>
            <input type="hidden" id="info-key-value" value="<?= (int)@$row[$this->pk->name] ?>"/>
            <input type="hidden" id="group-ids" name="group_ids" value="<?= @$ids ?>"/>

            <?php
            foreach ($this->fields as $field) {
                if ($field->hidden) {
                    echo $field->inputHidden($row[$field->name]);
                }
            }
            ?>

            <div class="content__head">
                <div class="content__head-bg"></div>
                <div class="content__head-wrap">
                    <div class="content__head-left">
                        <h4 class="content__title"><?= $title ?></h4>
                        <button type="button" class="content__head-toggle BtnIconPrimarySm">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                      clip-rule="evenodd"
                                      d="M10.7071 5.29289C11.0976 5.68342 11.0976 6.31658 10.7071 6.70711L6.41421 11H20C20.5523 11 21 11.4477 21 12C21 12.5523 20.5523 13 20 13H6.41421L10.7071 17.2929C11.0976 17.6834 11.0976 18.3166 10.7071 18.7071C10.3166 19.0976 9.68342 19.0976 9.29289 18.7071L3.29289 12.7071C2.90237 12.3166 2.90237 11.6834 3.29289 11.2929L9.29289 5.29289C9.68342 4.90237 10.3166 4.90237 10.7071 5.29289Z"
                                      fill="white"/>
                            </svg>
                        </button>
                    </div>
                    <div class="content__head-right">
                        <div class="content__head-btns content__head-btns--hidden-desktop">
                            <button name="submit_apply" type="submit" class="content__btn-save BtnPrimarySm BtnIconLeft">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path d="M5 12L10 17L19 8"
                                          stroke="#ffffff"
                                          stroke-width="2"
                                          stroke-linecap="round"
                                          stroke-linejoin="round"/>
                                </svg>
                                Сохранить
                            </button>
                            <button name="submit_save" type="submit" class="content__btn-save-exit BtnSecondaryMonoSm BtnIconLeft">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path d="M5 12L10 17L19 8" stroke="#ffffff" stroke-width="2"
                                          stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>

                                Сохранить и выйти
                            </button>
                            <a href="./" class="content__btn-cancel BtnOutlineMonoSm BtnIconLeftOutline">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                          d="M5.29289 5.29289C5.68342 4.90237 6.31658 4.90237 6.70711 5.29289L12 10.5858L17.2929 5.29289C17.6834 4.90237 18.3166 4.90237 18.7071 5.29289C19.0976 5.68342 19.0976 6.31658 18.7071 6.70711L13.4142 12L18.7071 17.2929C19.0976 17.6834 19.0976 18.3166 18.7071 18.7071C18.3166 19.0976 17.6834 19.0976 17.2929 18.7071L12 13.4142L6.70711 18.7071C6.31658 19.0976 5.68342 19.0976 5.29289 18.7071C4.90237 18.3166 4.90237 17.6834 5.29289 17.2929L10.5858 12L5.29289 6.70711C4.90237 6.31658 4.90237 5.68342 5.29289 5.29289Z"
                                          fill="#0D0D0D"/>
                                </svg>
                                Отмена
                            </a>
                            <!--                    <div class="form-control form-control--sm">-->
                            <!--                        <div class="form-control__dropdown">-->
                            <!--                            <div class="form-control__dropdown-top">-->
                            <!--                                <div class="form-control__dropdown-current">Название шаблона</div>-->
                            <!--                                <button class="form-control__dropdown-toggle">-->
                            <!--                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"-->
                            <!--                                         xmlns="http://www.w3.org/2000/svg">-->
                            <!--                                        <path fill-rule="evenodd" clip-rule="evenodd"-->
                            <!--                                              d="M8.29289 10.2929C8.68342 9.90237 9.31658 9.90237 9.70711 10.2929L12 12.5858L14.2929 10.2929C14.6834 9.90237 15.3166 9.90237 15.7071 10.2929C16.0976 10.6834 16.0976 11.3166 15.7071 11.7071L12.7071 14.7071C12.3166 15.0976 11.6834 15.0976 11.2929 14.7071L8.29289 11.7071C7.90237 11.3166 7.90237 10.6834 8.29289 10.2929Z"-->
                            <!--                                              fill="#0D0D0D" />-->
                            <!--                                    </svg>-->
                            <!--                                </button>-->
                            <!--                            </div>-->
                            <!--                            <div class="form-control__dropdown-list">-->
                            <!--                                <div class="form-control__dropdown-item">Шаблон 1</div>-->
                            <!--                                <div class="form-control__dropdown-item">Шаблон 2</div>-->
                            <!--                                <div class="form-control__dropdown-item">Шаблон 3</div>-->
                            <!--                                <div class="form-control__dropdown-item">Шаблон 4</div>-->
                            <!--                                <div class="form-control__dropdown-item">Шаблон 5</div>-->
                            <!--                            </div>-->
                            <!--                        </div>-->
                            <!--                    </div>-->
                        </div>
                    </div>
                </div>
            </div>

            <div class="content__body" data-simplebar>
                <?php \Simflex\Admin\Plugins\Alert\Alert::output(); ?>
                <?php if (count($this->errors)): ?>
                    <div class="content__notification notification notification--active notification--error">
                        <div class="notification__inner">
                            <div class="notification__top">
                                <div class="notification__title">При сохранении возникли ошибки</div>
                                <button type="button" class="notification__close">
                                    <svg class="notification__close-icon" width="24" height="24"
                                         viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                              d="M5.29289 5.29289C5.68342 4.90237 6.31658 4.90237 6.70711 5.29289L12 10.5858L17.2929 5.29289C17.6834 4.90237 18.3166 4.90237 18.7071 5.29289C19.0976 5.68342 19.0976 6.31658 18.7071 6.70711L13.4142 12L18.7071 17.2929C19.0976 17.6834 19.0976 18.3166 18.7071 18.7071C18.3166 19.0976 17.6834 19.0976 17.2929 18.7071L12 13.4142L6.70711 18.7071C6.31658 19.0976 5.68342 19.0976 5.29289 18.7071C4.90237 18.3166 4.90237 17.6834 5.29289 17.2929L10.5858 12L5.29289 6.70711C4.90237 6.31658 4.90237 5.68342 5.29289 5.29289Z"
                                              fill="#0D0D0D"/>
                                    </svg>
                                </button>
                            </div>
                            <div class="notification__text">
                                <?php echo '<ul>';
                                foreach ($this->errors as $error) {
                                    if (is_array($error)) {
                                        foreach ($error as $e) {
                                            echo '<li>', $e, '</li>';
                                        }
                                    } else {
                                        echo '<li>', $error, '</li>';
                                    }
                                }
                                echo '</ul>'; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php foreach ($this->fields as $field): ?>
                <?php $showRightCol = ($field->params['pos'] == 'right') || $showRightCol;
                $artificial = ($field->params['pos'] == 'right') || $artificial;
                if ($field->params['pos'] == 'right' && !$field->hidden) {
                    $artificialGroups[$field->params['pos_group']][] = $field;
                }
                ?>
                    <?php if (!$field->hidden && $field->params['pos'] != 'right'): ?>

                        <div for="" class="data-point <?=$isGroup?'data-point--copy':''?>">
                            <span class="data-point__text"><?= $field->label ?></span>
                            <?php if ($isGroup): ?>
                            <input type="checkbox" name="set[<?= $field->name ?>]" id="" class="form-control__checkbox">
                        <?php endif; ?>
                            <?= $this->formFieldInput($field, $row); ?>
                        </div>
                    <?php endif ?>
                <?php endforeach ?>

                <div id="params-left" class="data-point">
                    <?php if (count($this->params['left'])) : ?>
                        <?php
                        $hasWithoutGroup = false;
                        foreach ($this->params['left'] as $param) {
                            if (isset($param['field'])) {
                                $hasWithoutGroup = true;
                                break;
                            }
                        }
                        ?>
                        <?php
                        $paramGroups = [];
                        foreach ($this->params['left'] as $param) {
                            if (!isset($param['field'])) continue;
                            $paramGroups[$param['group_name'] ?: 'Параметры'][] = $param;
                        }

                        ?>
                        <?php if ($hasWithoutGroup && $paramGroups): ?>
                            <?php foreach ($paramGroups as $groupName => $groupParams): ?>
                                <div class="data2">
                                    <div class="data2__head">
                                        <h4 class="data2__title"><?php echo $groupName ?></h4>
                                    </div>
                                    <div class="data2__content">
                                        <?php foreach ($groupParams as $param): ?>
                                            <?php if (isset($param['field'])): ?>
                                                <?php $field = $param['field'] ?>
                                                <?php \Simflex\Admin\Fields\Field::setFieldValue($field, $group, $params, $row) ?>
                                                <?php include 'form.field.tpl' ?>
                                            <?php endif ?>
                                        <?php endforeach ?>
                                    </div>
                                </div>
                            <?php endforeach ?>
                        <?php endif ?>
                        <?php foreach ($this->params['left'] as $group): ?>
                            <?php if (count($group['fields'])): ?>
                                <div class="data2">
                                    <div class="data2__head">
                                        <h4 class="data2__title"><?php echo $group['label'] ?></h4>
                                    </div>
                                    <div class="data2__content">
                                        <?php foreach ($group['fields'] as $field): ?>
                                            <?php \Simflex\Admin\Fields\Field::setFieldValue($field, $group, $params, $row) ?>
                                            <?php include 'form.field.tpl' ?>
                                        <?php endforeach ?>
                                    </div>
                                </div>
                            <?php endif ?>
                        <?php endforeach ?>
                    <?php endif; ?>

                </div>

                <?= $this->extraLeft ?>
                <?php $this->portlets('left') ?>
            </div>
            <div class="content__btns">
                <button name="submit_apply" type="submit" class="content__btn-save BtnPrimarySm BtnIconLeft">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M5 12L10 17L19 8"
                              stroke="#ffffff"
                              stroke-width="2"
                              stroke-linecap="round"
                              stroke-linejoin="round"/>
                    </svg>
                    Сохранить
                </button>
                <button name="submit_save" type="submit" class="content__btn-save-exit BtnSecondaryMonoSm BtnIconLeft">
                    <svg fill="none" stroke="#ffffff" viewBox="0 0 24 24">
                        <use xlink:href="<?= asset('img/icons/svg-defs.svg') ?>#good"></use>
                    </svg>
                    Сохранить и выйти
                </button>
                <a href="./" class="content__btn-cancel BtnOutlineMonoSm BtnIconLeftOutline">
                    <svg stroke="none" fill="#343434" viewBox="0 0 24 24">
                        <use xlink:href="<?= asset('img/icons/svg-defs.svg') ?>#close"></use>
                    </svg>
                    Отмена
                </a>
                <!--        <div class="form-control form-control--sm">-->
                <!--            <div class="form-control__dropdown">-->
                <!--                <div class="form-control__dropdown-top">-->
                <!--                    <div class="form-control__dropdown-current">Название шаблона</div>-->
                <!--                    <button class="form-control__dropdown-toggle">-->
                <!--                        <svg stroke="none" fill="#0D0D0D" viewBox="0 0 24 24">-->
                <!--                            <use xlink:href="-->
                <?php //=asset('img/icons/svg-defs.svg')?><!--#chevron-mini"></use>-->
                <!--                        </svg>-->
                <!--                    </button>-->
                <!--                </div>-->
                <!--                <div class="form-control__dropdown-list">-->
                <!--                    <div class="form-control__dropdown-item">Шаблон 1</div>-->
                <!--                    <div class="form-control__dropdown-item">Шаблон 2</div>-->
                <!--                    <div class="form-control__dropdown-item">Шаблон 3</div>-->
                <!--                    <div class="form-control__dropdown-item">Шаблон 4</div>-->
                <!--                    <div class="form-control__dropdown-item">Шаблон 5</div>-->
                <!--                </div>-->
                <!--            </div>-->
                <!--        </div>-->
            </div>
        </div>

<!--        <div aria-hidden="true" role="basic" tabindex="-1" id="modal-ajax" class="modal fade">-->
<!--            <div class="modal-dialog">-->
<!--                <div class="modal-content">-->
<!--                    <div style="text-align: center; padding: 50px 0">-->
<!--                        <img class="loading" alt="" src="/admin/theme/img/ajax-modal-loading.gif">-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->

    </div>
    <?php if ($showRightCol || $this->extraRight): ?>
        <div class="content content--right">
            <div class="content__inner">
                <div class="content__body" data-simplebar>
                    <?php if ($artificial): ?>
                    <?php foreach ($artificialGroups as $g=>$fs): ?>
                        <div class="data3">
                            <div class="data3__head">
                                <div class="data3__title"><?=$g?:'Параметры'?></div>
                            </div>
                            <div class="data3__content">
                                <?php foreach ($fs as $field): ?>

                                        <div for="" class="data-point <?=$isGroup?'data-point--copy':''?>">
                                            <span class="data-point__text"><?= $field->label ?></span>
                                            <?php if ($isGroup): ?>
                                                <input type="checkbox" name="set[<?= $field->name ?>]" id="" class="form-control__checkbox">
                                            <?php endif; ?>
                                            <?= $this->formFieldInput($field, $row); ?>
                                        </div>
                                <?php endforeach ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                    <?php if (count($this->params['right'])): ?>
                        <?php
                        $group = null;
                        $hasWithoutGroup = false;
                        foreach ($this->params['right'] as $param) {
                            if (isset($param['field'])) {
                                $hasWithoutGroup = true;
                                break;
                            }
                        }
                        ?>
                        <?php if ($hasWithoutGroup): ?>
                            <div class="data3">
                                <div class="data3__head">
                                    <div class="data3__title">Параметры</div>
                                </div>
                                <div class="data3__content">
                                    <?php foreach ($this->params['right'] as $param): ?>
                                        <?php if (isset($param['field'])): ?>
                                            <?php $field = $param['field'] ?>
                                            <?php @\Simflex\Admin\Fields\Field::setFieldValue($field, $group, $params, $row) ?>
                                            <?php include 'form.field.tpl' ?>
                                        <?php endif ?>
                                    <?php endforeach ?>
                                </div>
                            </div>
                        <?php endif ?>
                        <?php foreach ($this->params['right'] as $group): ?>
                            <?php if (count($group['fields'])): ?>
                                <div class="data3">
                                    <div class="data3__head">
                                        <div class="data3__title"><?= $group['label'] ?></div>
                                    </div>
                                    <div class="data3__content">
                                        <?php foreach ($group['fields'] as $field): ?>
                                            <?php \Simflex\Admin\Fields\Field::setFieldValue($field, $group, $params, $row) ?>
                                            <?php include 'form.field.tpl' ?>
                                        <?php endforeach ?>
                                    </div>
                                </div>
                            <?php endif ?>
                        <?php endforeach ?>
                        <div class="ajax-params">
                        </div>
                    <?php endif ?>
                    <?= $rightPortletsHTML ?>
                    <?= $this->extraRight ?>
                </div>
            </div>
        </div>
    <?php endif ?>
</form>
