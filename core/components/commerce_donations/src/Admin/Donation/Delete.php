<?php

namespace modmore\Commerce_Donations\Admin\Donation;

use modmore\Commerce\Admin\Sections\SimpleSection;
use modmore\Commerce\Admin\Page;

class Delete extends Page
{
    public $key = 'donation-delete';
    public $title = 'commerce_donations.delete_donation';
    public static $permissions = ['commerce', 'commerce_products'];

    public function setUp()
    {
        $objectId = (int)$this->getOption('id', 0);
        $object = $this->adapter->getObject(\comDonation::class, [
            'id' => $objectId,
        ]);

        if ($object) {
            $section = new SimpleSection($this->commerce, [
                'title' => $this->title
            ]);
            $widget = new DeleteForm($this->commerce, [
                'title' => 'commerce_donations.delete_donation_named'
            ]);
            $widget->setRecord($object);
            $widget->setClassKey(\comDonation::class);
            $widget->setFormAction($this->adapter->makeAdminUrl('donations/donation/delete', [
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
