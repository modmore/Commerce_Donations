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

if (!$cause->get('active')) {
    $inactiveTpl = $scriptProperties['inactiveTpl'] ?? 'donations/cause/inactive.twig';
    return $view->render($inactiveTpl, [
        'cause' => $cause->toArray(),
    ]);
}

$activeTpl = $scriptProperties['activeTpl'] ?? 'donations/cause/active.twig';

if (isset($_POST['donate'], $_POST['cause']) && (int)$_POST['cause'] === $causeId) {

    $order = comOrder::loadUserOrder($commerce);

    $product = $cause->getProduct();
    /** @var comOrderItem $item */
    $item = $adapter->newObject(comOrderItem::class);
    $item->fromProduct($product);
    $item->set('is_manual_price', true);

    $price = $_POST['amount'] ?? 'custom';
    if (empty($price) || $price === 'custom') {
        $price = (int)$_POST['amount_custom'];
    }
    if ($price < 1) {
        return $view->render($activeTpl, [
            'cause' => $cause->toArray(),
            'errors' => [
                'Amount has to be at least 1.'
            ]
        ]);
    }

    $item->set('price', $price * 100);
    $item->set('link', $modx->makeUrl($modx->resource->get('id')));
    $item->setProperties([
        'is_donation' => true,
        'donation_cause' => $cause->get('id'),
        'donor_public' => (bool)($_POST['donor_public'] ?? false),
        'donor_name' => (string)($_POST['donor_name'] ?? ''),
        'donor_note' => (string)($_POST['donor_note'] ?? ''),
    ]);

    $order->addItem($item);

    $modx->sendRedirect($modx->makeUrl($modx->getOption('commerce.cart_resource')));
}


return $view->render($activeTpl, [
    'cause' => $cause->toArray(),
]);