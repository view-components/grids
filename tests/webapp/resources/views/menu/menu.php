<style>
    <?= include ($resourcesDir . '/css/menu.css') ?>
</style>
<div style="float: right;">
    <ul class="menu">
        <?php foreach ($this->getActions() as $action): ?>
            <li>
                <a href='/<?= $action->name ?>'>
                    <?= $action->name ?>
                </a>
            </li>
        <?php endforeach ?>
    </ul>
</div>
<div style="clear: both"></div>