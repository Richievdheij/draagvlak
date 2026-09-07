<?php

declare(strict_types=1);

/**
 * One-off messages, printed once and then gone.
 *
 * This is where the app says what a choice cost, always afterwards. Included by
 * the layout, so a screen only has to call flash() before its redirect. Takes
 * no variables.
 */

$flashes = takeFlashes();

if ($flashes === []) {
    return;
}

?>
<div class="notices" role="status" aria-live="polite">
    <?php foreach ($flashes as $flashMessage): ?>
        <p class="notice notice--<?= e($flashMessage['tone']) ?>">
            <?php if (($flashMessage['title'] ?? null) !== null): ?>
                <strong><?= e($flashMessage['title']) ?></strong>
            <?php endif; ?>
            <?= e($flashMessage['message']) ?>
        </p>
    <?php endforeach; ?>
</div>
