<?php

declare(strict_types=1);

use Draagvlak\Features\Auth\Account;
use Draagvlak\Features\Score\ScoreRules;

/**
 * Your own number, the meter under it and how far you are from the discount.
 *
 * The distance to the threshold is always in the sentence, also when you are
 * above it: the app never lets you forget the number can fall again.
 *
 * @var Account $account     The visitor.
 * @var int     $activeCount How many contacts still count as active.
 */

$gap = $account->pointsToReward();

?>
<section class="score-block" aria-label="Jouw draagvlak">
    <div class="score-block__top">
        <span class="score-block__label">Jouw draagvlak</span>
        <span class="score-block__value numeric"><?= $this->e($account->score) ?></span>
    </div>

    <div class="meter">
        <span class="meter__fill" style="width: <?= $this->e($account->score) ?>%"></span>
        <i class="meter__mark" style="left: <?= $this->e(ScoreRules::REWARD_THRESHOLD) ?>%"></i>
    </div>

    <p class="score-block__note">
        <?php if ($account->hasReward()): ?>
            Je korting van <strong>5 euro</strong> is actief zolang je op
            <?= $this->e(ScoreRules::REWARD_THRESHOLD) ?> of hoger blijft.
        <?php else: ?>
            Nog <strong><?= $this->e($gap) ?> <?= $gap === 1 ? 'punt' : 'punten' ?></strong> tot
            <?= $this->e(ScoreRules::REWARD_THRESHOLD) ?>. Vanaf
            <?= $this->e(ScoreRules::REWARD_THRESHOLD) ?> krijg je 5 euro korting op je zorgpremie.
        <?php endif; ?>
    </p>

    <p class="score-block__meta">
        Actieve contacten: <span class="numeric"><?= $this->e($activeCount) ?></span> van minimaal
        <span class="numeric"><?= $this->e(ScoreRules::MIN_ACTIVE_CONTACTS) ?></span>.
    </p>
</section>
