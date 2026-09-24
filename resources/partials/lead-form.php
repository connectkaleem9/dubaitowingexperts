<?php
/**
 * Lead / quote form. Posts to /contact/ and returns to $sourcePath on validation errors.
 * @var list<array> $services
 */
use App\Controllers\ContactController;
use App\Services\FormGuard;

$errors = errors();
$formType = $formType ?? 'contact';
$sourcePath = $sourcePath ?? current_path();
$heading = $heading ?? 'Request a call back';
$intro = $intro ?? 'Leave your details and location. We will call you back with a quote.';
$selectedService = old('service_id', (string) ($selectedService ?? ''));
$compact = $compact ?? false;
$field = static function (string $name) use ($errors): array {
    $has = isset($errors[$name]);
    return [
        $has ? ' field--error' : '',
        $has ? ' aria-invalid="true" aria-describedby="err-' . $name . '"' : '',
        $has ? '<p class="field__error" id="err-' . $name . '">' . e($errors[$name]) . '</p>' : '',
    ];
};
?>
<form class="form form--card" id="lead-form" method="post" action="/contact/" novalidate data-track-form="<?= e($formType) ?>">
    <div>
        <h2><?= e($heading) ?></h2>
        <p class="muted"><?= e($intro) ?></p>
    </div>
    <?php if (isset($errors['_form'])): ?>
        <div class="alert alert--error" role="alert"><?= e($errors['_form']) ?></div>
    <?php elseif ($errors !== []): ?>
        <div class="alert alert--error" role="alert">Please check the highlighted fields.</div>
    <?php endif; ?>
    <?= csrf_field() ?>
    <?= FormGuard::fields() ?>
    <input type="hidden" name="form_type" value="<?= e($formType) ?>">
    <input type="hidden" name="source_path" value="<?= e($sourcePath) ?>">

    <div class="form__row form__row--2">
        <?php [$c, $a, $m] = $field('name'); ?>
        <div class="field<?= $c ?>">
            <label for="lf-name">Your name <span class="req" aria-hidden="true">*</span></label>
            <input class="input" id="lf-name" name="name" type="text" autocomplete="name" required maxlength="100" value="<?= e(old('name')) ?>"<?= $a ?>>
            <?= $m ?>
        </div>
        <?php [$c, $a, $m] = $field('phone'); ?>
        <div class="field<?= $c ?>">
            <label for="lf-phone">Mobile number <span class="req" aria-hidden="true">*</span></label>
            <input class="input" id="lf-phone" name="phone" type="tel" autocomplete="tel" inputmode="tel" required maxlength="20" placeholder="05X XXX XXXX" value="<?= e(old('phone')) ?>"<?= $a ?>>
            <?= $m ?>
        </div>
    </div>

    <?php [$c, $a, $m] = $field('location'); ?>
    <div class="field<?= $c ?>">
        <label for="lf-location">Where is the vehicle? <span class="req" aria-hidden="true">*</span></label>
        <input class="input" id="lf-location" name="location" type="text" required maxlength="200" placeholder="Area, road or landmark" value="<?= e(old('location')) ?>"<?= $a ?>>
        <?= $m ?>
    </div>

    <div class="form__row form__row--2">
        <?php [$c, $a, $m] = $field('service_id'); ?>
        <div class="field<?= $c ?>">
            <label for="lf-service">Service needed</label>
            <select class="select" id="lf-service" name="service_id"<?= $a ?>>
                <option value="">Not sure / other</option>
                <?php foreach ($services as $s): ?>
                    <option value="<?= (int) $s['id'] ?>"<?= $selectedService === (string) $s['id'] ? ' selected' : '' ?>><?= e($s['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <?= $m ?>
        </div>
        <?php [$c, $a, $m] = $field('vehicle_type'); ?>
        <div class="field<?= $c ?>">
            <label for="lf-vehicle">Vehicle type</label>
            <select class="select" id="lf-vehicle" name="vehicle_type"<?= $a ?>>
                <option value="">Select</option>
                <?php foreach (ContactController::VEHICLE_TYPES as $t): ?>
                    <option<?= old('vehicle_type') === $t ? ' selected' : '' ?>><?= e($t) ?></option>
                <?php endforeach; ?>
            </select>
            <?= $m ?>
        </div>
    </div>

    <?php if (!$compact): ?>
        <?php [$c, $a, $m] = $field('message'); ?>
        <div class="field<?= $c ?>">
            <label for="lf-message">What happened? <span class="muted small">(optional)</span></label>
            <textarea class="textarea" id="lf-message" name="message" maxlength="2000" placeholder="E.g. car won't start in a basement car park, flat tyre on the highway, destination garage…"<?= $a ?>><?= e(old('message')) ?></textarea>
            <?= $m ?>
        </div>
    <?php endif; ?>

    <?php [$c, $a, $m] = $field('preferred_contact'); $pref = old('preferred_contact', 'phone'); ?>
    <fieldset class="field<?= $c ?>">
        <legend>How should we contact you?</legend>
        <div class="radio-row">
            <?php foreach (['phone' => 'Phone call', 'whatsapp' => 'WhatsApp', 'email' => 'Email'] as $val => $label): ?>
                <label class="radio-pill"><input type="radio" name="preferred_contact" value="<?= $val ?>"<?= $pref === $val ? ' checked' : '' ?>><span><?= $label ?></span></label>
            <?php endforeach; ?>
        </div>
        <?= $m ?>
    </fieldset>

    <?php [$c, $a, $m] = $field('email'); ?>
    <div class="field<?= $c ?>" data-email-field>
        <label for="lf-email">Email <span class="muted small">(needed if you choose email)</span></label>
        <input class="input" id="lf-email" name="email" type="email" autocomplete="email" maxlength="191" value="<?= e(old('email')) ?>"<?= $a ?>>
        <?= $m ?>
    </div>

    <button class="btn btn--primary btn--lg btn--block" type="submit"><?= $formType === 'quote' ? 'Get my quote' : 'Request recovery' ?></button>
    <p class="small muted">For urgent help, calling <a href="<?= e(tel_href()) ?>" data-track="phone_click" data-location="form"><?= e(business('phone_display')) ?></a> is fastest. By sending this form you agree to our <a href="/privacy-policy/">privacy policy</a>.</p>
</form>
