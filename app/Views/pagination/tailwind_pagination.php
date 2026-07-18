<?php if ($pager): ?>
<nav class="flex items-center justify-between mt-4" aria-label="Pagination">
    <p class="text-sm text-gray-600">
        Showing <?= $pager->getCurrentPage() ? 'page ' . $pager->getCurrentPage() . ' of ' . $pager->getPageCount() : '' ?>
    </p>
    <ul class="flex items-center gap-1">
        <?php if ($pager->hasPreviousPage()): ?>
            <li>
                <a href="<?= $pager->getPreviousPage() ?>" class="px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">&laquo; Previous</a>
            </li>
        <?php endif ?>

        <?php foreach ($pager->links() as $link): ?>
            <li>
                <?php if ($link['active']): ?>
                    <span class="px-3 py-1.5 text-sm font-medium text-white bg-indigo-600 border border-indigo-600 rounded-lg"><?= $link['title'] ?></span>
                <?php else: ?>
                    <a href="<?= $link['uri'] ?>" class="px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"><?= $link['title'] ?></a>
                <?php endif ?>
            </li>
        <?php endforeach ?>

        <?php if ($pager->hasNextPage()): ?>
            <li>
                <a href="<?= $pager->getNextPage() ?>" class="px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">Next &raquo;</a>
            </li>
        <?php endif ?>
    </ul>
</nav>
<?php endif ?>
