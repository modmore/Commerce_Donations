<?php
namespace modmore\Commerce_Donations;

use Commerce;
use modmore\Commerce\Admin\Configuration\About\ComposerPackages;
use modmore\Commerce\Admin\Sections\SimpleSection;
use modmore\Commerce\Dispatcher\EventDispatcher;
use modmore\Commerce\Events\Admin\GeneratorEvent;
use modmore\Commerce\Events\Admin\PageEvent;
use modmore\Commerce\Events\Admin\TopNavMenu;
use modmore\Commerce\Modules\BaseModule;
use modmore\Commerce_Donations\Admin\Causes;

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
        $this->adapter->loadClass(\comDonationProduct::class);

        // Add template path to twig
//        $root = dirname(__DIR__, 2);
//        $this->commerce->view()->addTemplatesPath($root . '/templates/');

        // Add some custom pages
        $dispatcher->addListener(
            Commerce::EVENT_DASHBOARD_INIT_GENERATOR,
            function (GeneratorEvent $event) {
                $generator = $event->getGenerator();

                $generator->addPage('products/donations/causes', Causes::class);
                $generator->addPage('donations/cause/create', Admin\Cause\Create::class);
                $generator->addPage('donations/cause/update', Admin\Cause\Update::class);
                $generator->addPage('donations/cause/duplicate', Admin\Cause\Duplicate::class);
                $generator->addPage('donations/cause/delete', Admin\Cause\Delete::class);
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
