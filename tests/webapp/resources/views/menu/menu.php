<?php
use ViewComponents\TestingHelpers\Application\Http\EasyRouting;
$actions = EasyRouting::getUris(get_class($this));
?>
<style>
    <?= include ($resourcesDir . '/css/menu.css') ?>
</style>
<div style="float: right;">
    <ul class="menu">
        <?php foreach ($actions as $action): ?>
            <li>
                <a href='<?= $action ?>'>
                    <?= substr($action, 1) ?>
                </a>
            </li>
        <?php endforeach ?>
    </ul>
</div>
<div style="clear: both"></div>