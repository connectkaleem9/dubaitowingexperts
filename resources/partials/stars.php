<?php $rating = max(0, min(5, (int) $rating)); ?>
<span class="stars" role="img" aria-label="Rated <?= $rating ?> out of 5">
    <?php for ($i = 1; $i <= 5; $i++): ?>
        <?= icon('star', 'icon' . ($i > $rating ? ' off' : '')) ?>
    <?php endfor; ?>
</span>
