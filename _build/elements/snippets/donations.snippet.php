<?php
/**
 * @var modX $modx
 * @var array $scriptProperties
 */

// Instantiate the Commerce class
$path = $modx->getOption('commerce.core_path', null, MODX_CORE_PATH . 'components/commerce/') . 'model/commerce/';
$params = ['mode' => $modx->getOption('commerce.mode')];
/** @var Commerce|null $commerce */
$commerce = $modx->getService('commerce', 'Commerce', $path, $params);
if (!($commerce instanceof Commerce)) {
    return '<p class="error">Oops! It is not possible to view your cart currently. We\'re sorry for the inconvenience. Please try again later.</p>';
}

if ($commerce->isDisabled()) {
    return $commerce->adapter->lexicon('commerce.mode.disabled.message');
}
$adapter = $commerce->adapter;
$view = $commerce->view();

if (!class_exists(comDonationCause::class)) {
    return 'Donations module disabled.';
}

$causeId = (int)($scriptProperties['cause'] ?? 0);

/** @var comDonationCause $cause */
$cause = $modx->getObject(comDonationCause::class, [
    'id' => $causeId,
    'removed' => false,
]);
if (!$cause) {
    return '<p class="error">Cause not found.</p>';
}

$donations = [];

$c = $modx->newQuery(comDonation::class);
$c->where([
    'cause' => $cause->get('id'),
    'donor_public' => true,
]);

$sortby = $scriptProperties['sortby'] ?? 'donated_on';
$sortdir = $scriptProperties['sortdir'] ?? 'DESC';
$c->sortby($sortby, $sortdir);

/** @var comDonation $donation */
foreach ($modx->getIterator(comDonation::class, $c) as $donation) {
    $a = $donation->toArray();

    $donations[] = $a;
}


$tpl = $scriptProperties['tpl'] ?? 'donations/cause/donations.twig';
return $view->render($tpl, [
    'cause' => $cause->toArray(),
    'donations' => $donations,
]);