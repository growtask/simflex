<?php

/** @var \Simflex\Core\Extensions\Form\ModuleForm $this */
?>
<?= \Simflex\Core\Extensions\Alert\Alert::getHTML() ?>
<?php
if (!$this->submitted() || $this->submitted() && $this->errors) : ?>
    <?= $this->params['text_before'] ?? '' ?>

    <form id="<?= $this->name; ?>-form" class="plug-form" method="<?= $this->method ?>" action="<?= $this->action ?>">
        <table>
            <?php
            foreach ($this->fields as $field) : ?>
                <tr>
                    <td><label class="plug-form-label"><?php
                            echo $field->getLabel(), $field->vRequired ? ' <span>*</span>' : ''; ?></label></td>
                    <td>
                        <?php
                        echo $field->html();
                        echo '<div class="plug-form-error">';
                        if (isset($this->errors[$field->getName()])) {
                            foreach ($this->errors[$field->getName()] as $err) {
                                echo '— &nbsp;', $err, '<br />';
                            }
                        }
                        echo '</div>';
                        if ($field->comment) {
                            echo '<div class="plug-form-comment">', $field->comment, '</div>';
                        }
                        ?>
                    </td>
                </tr>
            <?php
            endforeach; ?>
        </table>
        <hr/>
        <div class="buttons">
            <button class="btn" name="<?= $this->name ?>[submit]"><?= $this->submitButtonText ?></button>
        </div>
    </form>

<?php
endif ?>

