<?php

declare(strict_types=1);

/**
 * Your own number, the meter under it and how far you are from the discount.
 *
 * The distance to the threshold is always in the sentence, also when you are
 * above it: the app never lets you forget the number can fall again.
 *
 * @var int $score      The participant's score right now.
 * @var int $activeCount How many contacts still count as active.
 */

$gap = REWARD_THRESHOLD - $score;

?>
<section class="score-block" aria-label="Jouw draagvlak">
    <div class="score-block__top">
        <span class="score-block__label">Jouw draagvlak</span>
        <span class="score-block__value numeric"><?= e($score) ?></span>
    </div>

    <div class="meter">
        <span class="meter__fill" style="width: <?= e($score) ?>%"></span>
        <i class="meter__mark" style="left: <?= e(REWARD_THRESHOLD) ?>%"></i>
    </div>

    <p class="score-block__note">
        <?php if (hasReward($score)): ?>
            Je korting van <strong>5 euro</strong> is actief zolang je op
            <?= e(REWARD_THRESHOLD) ?> of hoger blijft.
        <?php else: ?>
            Nog <strong><?= e($gap) ?> <?= $gap === 1 ? 'punt' : 'punten' ?></strong> tot
            <?= e(REWARD_THRESHOLD) ?>. Vanaf <?= e(REWARD_THRESHOLD) ?> krijg je 5 euro
            korting op je zorgpremie.
        <?php endif; ?>
    </p>

    <p class="score-block__meta">
        Actieve contacten: <span class="numeric"><?= e($activeCount) ?></span> van minimaal
        <span class="numeric"><?= e(MIN_ACTIVE_CONTACTS) ?></span>.
    </p>
</section>
