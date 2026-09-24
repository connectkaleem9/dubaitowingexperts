<?php
/*
 * Thin navy strip above the header (from the approved design).
 * Only claims we can stand behind: 24/7 is confirmed, Dubai-wide coverage is confirmed, and
 * "price agreed before dispatch" is our stated process. No response-time claim (CLAUDE.md §2).
 */
?>
<div class="topbar">
    <div class="container topbar__inner">
        <span class="topbar__item"><?= icon('clock') ?>24/7 Emergency Recovery Service</span>
        <span class="topbar__item"><?= icon('pin') ?>Covering All Areas of Dubai</span>
        <span class="topbar__item"><?= icon('tag') ?>Price Agreed Before Dispatch</span>
        <span class="topbar__item topbar__spacer"><?= icon('pin') ?><?= e(business('city')) ?> – UAE</span>
    </div>
</div>
