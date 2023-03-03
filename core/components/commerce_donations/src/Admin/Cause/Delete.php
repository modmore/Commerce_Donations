<?php

namespace modmore\Commerce_Donations\Admin\Cause;

use comDonationCause;
use modmore\Commerce\Admin\Sections\SimpleSection;
use modmore\Commerce\Admin\Page;
use modmore\Commerce\Admin\Widgets\DeleteFormWidget;

class Delete extends Page
{
    public $key = 'donations-cause-delete';
    public $title = 'commerce_donations.delete_cause';
    public static $permissions = ['commerce', 'commerce_products'];

    public function setUp()
    {
        $objectId = (int)$this->getOption('id', 0);
        $object = $this->adapter->getObject(comDonationCause::class, [
            'id' => $objectId,
            'removed' => false
        ]);

        if ($object) {
            $section = new SimpleSection($this->commerce, [
                'title' => $this->title
            ]);
            $widget = new DeleteFormWidget($this->commerce, [
                'title' => 'commerce_donations.delete_cause_named'
            ]);
            $widget->setRecord($object);
            $widget->setClassKey(comDonationCause::class);
            $widget->setFormAction($this->adapter->makeAdminUrl('donations/cause/delete', [
                'id' => $object->get('id')
            ]));
            $widget->setUp();
            $section->addWidget($widget);
            $this->addSection($section);
            return $this;
        }
        return $this->returnError($this->adapter->lexicon('commerce.item_not_found'));
    }
}
