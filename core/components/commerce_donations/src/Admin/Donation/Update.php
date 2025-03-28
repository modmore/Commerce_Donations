<?php

namespace modmore\Commerce_Donations\Admin\Donation;

use modmore\Commerce\Admin\Sections\SimpleSection;
use modmore\Commerce\Admin\Page;

class Update extends Page
{
    public $key = 'donations/donation/update';
    public $title = 'commerce_donations.edit_donation';
    public static $permissions = ['commerce', 'commerce_products'];

    public function setUp()
    {
        $objectId = (int)$this->getOption('id', 0);
        $exists = $this->adapter->getCount(\comDonation::class, [
            'id' => $objectId,
        ]);

        if ($exists) {
            $section = new SimpleSection($this->commerce, [
                'title' => $this->title
            ]);
            $section->addWidget((new Form($this->commerce, [
                'id' => $objectId
            ]))->setUp());
            $this->addSection($section);
            return $this;
        }

        return $this->returnError($this->adapter->lexicon('commerce.item_not_found'));
    }
}
