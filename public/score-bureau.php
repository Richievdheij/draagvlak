<?php

declare(strict_types=1);

use Draagvlak\Features\Score\Pages\ScoreBureauPage;

/**
 * The score bureau: paid maintenance of your number.
 *
 * The URL, and nothing else. What it does is in src/Features/Score/Pages/ScoreBureauPage.php
 * and what it looks like is in views/features/score/score-bureau.php.
 */

$app = require __DIR__ . '/../bootstrap.php';

(new ScoreBureauPage($app))->handle();
