<?php
namespace modmore\Commerce_Donations;

use comDonation;
use comDonationCause;
use comDonationProduct;
use Commerce;
use modmore\Commerce\Admin\Configuration\About\ComposerPackages;
use modmore\Commerce\Admin\Sections\SimpleSection;
use modmore\Commerce\Dispatcher\EventDispatcher;
use modmore\Commerce\Events\Admin\GeneratorEvent;
use modmore\Commerce\Events\Admin\PageEvent;
use modmore\Commerce\Events\Admin\TopNavMenu;
use modmore\Commerce\Events\OrderState;
use modmore\Commerce\Modules\BaseModule;

require_once dirname(__DIR__) . '/vendor/autoload.php';

class Module extends BaseModule {

    public function getName(): string
    {
        $this->adapter->loadLexicon('commerce_donations:default');
        return $this->adapter->lexicon('commerce_donations');
    }

    public function getAuthor(): string
    {
        return 'modmore';
    }

    public function getDescription(): string
    {
        return $this->adapter->lexicon('commerce_donations.description');
    }

    public function initialize(EventDispatcher $dispatcher): void
    {
        // Load our lexicon
        $this->adapter->loadLexicon('commerce_donations:default');

        // Add the xPDO package, so Commerce can detect the derivative classes
        $path = dirname(__DIR__) . '/model/';
        $this->adapter->loadPackage('commerce_donations', $path);
        $this->adapter->loadClass(comDonationCause::class);
        $this->adapter->loadClass(comDonationProduct::class);

        // Add template path to twig
        $root = dirname(__DIR__);
        $this->commerce->view()->addTemplatesPath($root . '/templates/');

        // Add some custom pages
        $dispatcher->addListener(
            Commerce::EVENT_DASHBOARD_INIT_GENERATOR,
            function (GeneratorEvent $event) {
                $generator = $event->getGenerator();

                $generator->addPage('products/donations/causes', Admin\Causes::class);
                $generator->addPage('donations/cause/create', Admin\Cause\Create::class);
                $generator->addPage('donations/cause/update', Admin\Cause\Update::class);
                $generator->addPage('donations/cause/duplicate', Admin\Cause\Duplicate::class);
                $generator->addPage('donations/cause/delete', Admin\Cause\Delete::class);
                $generator->addPage('products/donations/cause/donations', Admin\Cause\Donations::class);
            }
        );

        // Add the causes menu to the products submenu
        $dispatcher->addListener(
            Commerce::EVENT_DASHBOARD_GET_MENU,
            function (TopNavMenu $event) {
                $items = $event->getItems();

                $items['products']['submenu'][] = [
                    'name' => $this->adapter->lexicon('commerce_donations.causes_menu'),
                    'key' => 'products/donations/causes',
                    'link' => $this->adapter->makeAdminUrl('products/donations/causes'),
                ];


                $event->setItems($items);
            }
        );

        // When processing the order, save the donation
        $dispatcher->addListener(
            Commerce::EVENT_STATE_CART_TO_PROCESSING,
            function(OrderState $event) {
                $order = $event->getOrder();
                $items = $order->getItems();

                foreach ($items as $item) {
                    if (!$item->getProperty('is_donation')) {
                        continue;
                    }

                    /** @var comDonationCause $cause */
                    $cause = $this->adapter->getObject(comDonationCause::class, [
                        'id' => (int)$item->getProperty('donation_cause')
                    ]);
                    if (!$cause) {
                        $order->log('Can\'t save donation, cause not found with ID ' . $item->getProperty('donation_cause'));
                        continue;
                    }

                    $donation = $this->adapter->newObject(comDonation::class);
                    $donation->fromArray([
                        'cause' => (int)$item->getProperty('donation_cause'),
                        'test' => $order->get('test'),
                        'order' => $order->get('id'),
                        'item' => $item->get('id'),
                        'user' => $order->getUser() ? $order->getUser()->get('id') : 0,

                        'donated_on' => time(),
                        'currency' => $order->get('currency'),
                        'amount' => $item->get('total'),
                        'amount_ex_tax' => $item->get('total_ex_tax'),

                        'donor_public' => $item->getProperty('donor_public', false),
                        'donor_name' => $item->getProperty('donor_name', ''),
                        'donor_note' => $item->getProperty('donor_note', ''),
                    ]);

                    if ($donation->save()) {
                        $order->log('Processed ' . $item->get('total_formatted') . ' donation for '. $cause->get('name'));
                        $cause->updateTotals();
                    }
                    else {
                        $order->log('Failed saving ' . $item->get('total_formatted') . ' donation for '. $cause->get('name'));
                    }
                }
            }
        );
    }

    public function getModuleConfiguration(\comModule $module): array
    {
        $fields = [];

        // A more detailed description to be shown in the module configuration. Note that the module description
        // ({@see self:getDescription}) is automatically shown as well.
//        $fields[] = new DescriptionField($this->commerce, [
//            'description' => $this->adapter->lexicon('commerce_donations.module_description'),
//        ]);

        return $fields;
    }

    public function addLibrariesToAbout(PageEvent $event): void
    {
        $lockFile = dirname(__DIR__, 2) . '/composer.lock';
        if (file_exists($lockFile)) {
            $section = new SimpleSection($this->commerce);
            $section->addWidget(new ComposerPackages($this->commerce, [
                'lockFile' => $lockFile,
                'heading' => $this->adapter->lexicon('commerce.about.open_source_libraries') . ' - ' . $this->adapter->lexicon('commerce_donations'),
                'introduction' => '', // Could add information about how libraries are used, if you'd like
            ]));

            $about = $event->getPage();
            $about->addSection($section);
        }
    }
}
