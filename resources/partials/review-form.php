<?php
/** @var list<array> $services */
use App\Services\FormGuard;

$errors = errors();
$field = static function (string $name) use ($errors): array {
    $has = isset($errors[$name]);
    return [
        $has ? ' field--error' : '',
        $has ? ' aria-invalid="true" aria-describedby="err-r-' . $name . '"' : '',
        $has ? '<p class="field__error" id="err-r-' . $name . '">' . e($errors[$name]) . '</p>' : '',
    ];
};
$rating = old('rating');
// The reviews page puts its own heading above the form; elsewhere the form carries one itself.
$heading = $heading ?? true;
?>
<form class="form form--card" id="review-form" method="post" action="/reviews/" novalidate data-track-form="review">
    <?php if ($heading): ?>
        <div>
            <h2>Leave a review</h2>
            <p class="muted">Tell other drivers how we helped. Reviews are checked before they are published.</p>
        </div>
    <?php endif; ?>
    <?php if (!empty($submitted)): ?>
        <div class="alert alert--success" role="status" data-conversion-event="review_submit">Thank you — your review has been received and will appear once it has been checked.</div>
    <?php endif; ?>
    <?php if (isset($errors['_form'])): ?>
        <div class="alert alert--error" role="alert"><?= e($errors['_form']) ?></div>
    <?php elseif ($errors !== []): ?>
        <div class="alert alert--error" role="alert">Please check the highlighted fields.</div>
    <?php endif; ?>
    <?= csrf_field() ?>
    <?= FormGuard::fields() ?>

    <?php [$c, $a, $m] = $field('rating'); ?>
    <fieldset class="field<?= $c ?>">
        <legend>Your rating <span class="req" aria-hidden="true">*</span></legend>
        <div class="rating-input">
            <?php for ($i = 5; $i >= 1; $i--): ?>
                <input type="radio" id="rv-rate-<?= $i ?>" name="rating" value="<?= $i ?>"<?= $rating === (string) $i ? ' checked' : '' ?>>
                <label for="rv-rate-<?= $i ?>"><?= icon('star') ?><span class="sr-only"><?= $i ?> star<?= $i > 1 ? 's' : '' ?></span></label>
            <?php endfor; ?>
        </div>
        <?= $m ?>
    </fieldset>

    <div class="form__row form__row--2">
        <?php [$c, $a, $m] = $field('name'); ?>
        <div class="field<?= $c ?>">
            <label for="rv-name">Your name <span class="req" aria-hidden="true">*</span></label>
            <input class="input" id="rv-name" name="name" type="text" autocomplete="name" required maxlength="100" value="<?= e(old('name')) ?>"<?= $a ?>>
            <span class="hint">Shown with your review. First name and initial is fine.</span>
            <?= $m ?>
        </div>
        <?php [$c, $a, $m] = $field('service_id'); ?>
        <div class="field<?= $c ?>">
            <label for="rv-service">Service you used</label>
            <select class="select" id="rv-service" name="service_id"<?= $a ?>>
                <option value="">Select</option>
                <?php foreach ($services as $s): ?>
                    <option value="<?= (int) $s['id'] ?>"<?= old('service_id') === (string) $s['id'] ? ' selected' : '' ?>><?= e($s['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <?= $m ?>
        </div>
    </div>

    <?php [$c, $a, $m] = $field('body'); ?>
    <div class="field<?= $c ?>">
        <label for="rv-body">Your review <span class="req" aria-hidden="true">*</span></label>
        <textarea class="textarea" id="rv-body" name="body" required minlength="20" maxlength="2000"<?= $a ?>><?= e(old('body')) ?></textarea>
        <?= $m ?>
    </div>

    <div class="form__row form__row--2">
        <?php [$c, $a, $m] = $field('area_text'); ?>
        <div class="field<?= $c ?>">
            <label for="rv-area">Area <span class="muted small">(optional)</span></label>
            <input class="input" id="rv-area" name="area_text" type="text" maxlength="120" placeholder="e.g. Dubai Marina" value="<?= e(old('area_text')) ?>"<?= $a ?>>
            <?= $m ?>
        </div>
        <?php [$c, $a, $m] = $field('email'); ?>
        <div class="field<?= $c ?>">
            <label for="rv-email">Email <span class="muted small">(optional, never shown)</span></label>
            <input class="input" id="rv-email" name="email" type="email" autocomplete="email" maxlength="191" value="<?= e(old('email')) ?>"<?= $a ?>>
            <?= $m ?>
        </div>
    </div>

    <button class="btn btn--call btn--lg btn--block" type="submit"><?= icon('star') ?>Submit review</button>
    <?php // Consent is given by sending the form; the wording has to stay in plain sight next to the button. ?>
    <p class="hint hint--consent">By sending this you agree that <?= e(business('name')) ?> may publish your review and
        the name you entered on this website. See our <a href="/privacy-policy/">privacy policy</a>.</p>
</form>
