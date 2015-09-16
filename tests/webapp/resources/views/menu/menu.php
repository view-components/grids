<style>
    td, th {
        border-bottom: 1px solid gray;
        border-right: 1px solid gray;
        padding:7px;
    }
    tr {
        margin:0 !important;
        padding: 0 !important;
    }
    table {
        border-spacing: 0;
        border-top: 1px solid gray;
        border-left: 1px solid gray;
    }

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