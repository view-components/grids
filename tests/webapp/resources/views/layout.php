<html>
<body>
<?= $this->renderMenu() ?>
<h1>Test App <?= $title?": <small>$title</small>":'' ?></h1>
<hr>
<?= $content ?>
<style>
    /* TABLES */
    td, th {
        border-bottom: 1px solid gray;
        border-right: 1px solid gray;
        padding: 7px;
    }

    tr {
        margin: 0 !important;
        padding: 0 !important;
    }

    table {
        border-spacing: 0;
        border-top: 1px solid gray;
        border-left: 1px solid gray;
    }

    /* PAGINATION */
    [data-control="pagination"] ul {
        padding:0;
        margin:5px;
    }
    [data-control="pagination"], [data-control="pagination"] li {
        display: inline-block;
    }
    [data-control="pagination"] li>a, [data-control="pagination"] li>span {
        text-decoration: none;
        border: 1px solid blue;
        border-radius: 5px;
        padding: 2px 8px 2px 8px;
        margin:2px;
    }
    [data-control="pagination"] li>span {
        border: 1px solid gray;
    }
</style>
<?php
$diff = round((microtime(true) - $_SERVER['start_time']), 4);
echo '<br>Generation Time: ' . $diff;
?>
</body>
</html>