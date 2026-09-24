<?php /** @var list<array> $faqs */ ?>
<div class="faq">
    <?php foreach ($faqs as $i => $faq): ?>
        <details<?= ($open ?? false) && $i === 0 ? ' open' : '' ?>>
            <summary><?= e($faq['question']) ?></summary>
            <div class="faq__answer"><?= $faq['answer'] /* sanitised on save by HtmlSanitizer */ ?></div>
        </details>
    <?php endforeach; ?>
</div>
