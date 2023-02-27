<?php
namespace modmore\Commerce_Donations;

use modmore\Commerce\Admin\Configuration\About\ComposerPackages;
use modmore\Commerce\Admin\Sections\SimpleSection;
use modmore\Commerce\Dispatcher\EventDispatcher;
use modmore\Commerce\Events\Admin\PageEvent;
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
//        $root = dirname(__DIR__, 2);
//        $path = $root . '/model/';
//        $this->adapter->loadPackage('commerce_donations', $path);

        // Add template path to twig
//        $root = dirname(__DIR__, 2);
//        $this->commerce->view()->addTemplatesPath($root . '/templates/');

        // Add composer libraries to the about section (v0.12+)
        $dispatcher->addListener(\Commerce::EVENT_DASHBOARD_LOAD_ABOUT, [$this, 'addLibrariesToAbout']);
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
