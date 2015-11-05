<?php /** @var  */ ?>
<small style="white-space: nowrap">
    <a
        title="Sort ascending"
        <?php if($order === 'asc'): ?>
            style="text-decoration: none; color: green;"
        <?php else: ?>
            style="text-decoration: none; color:dodgerblue;"
            href="<?= $links['asc'] ?>"
        <?php endif ?>
        >
        &#x25B2;
    </a>
    <a
        title="Sort descending"
        <?php if($order === 'desc'): ?>
            style="text-decoration: none; color: green;"
        <?php else: ?>
            style="text-decoration: none; color:dodgerblue;"
            href="<?= $links['desc'] ?>"
        <?php endif ?>
        >
        &#x25BC;
    </a>
</small>