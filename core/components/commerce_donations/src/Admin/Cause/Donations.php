<?php

namespace modmore\Commerce_Donations\Admin\Cause;

use comDonationCause;
use modmore\Commerce\Admin\Page;
use modmore\Commerce\Admin\Sections\SimpleSection;

class Donations extends Page
{
    public $key = 'products/donations/cause/donations';
    public $title = 'commerce_donations';
    public static $permissions = ['commerce', 'commerce_products'];

    public function setUp()
    {
        $objectId = (int)$this->getOption('cause', 0);
        $exists = $this->adapter->getCount(comDonationCause::class, [
            'id' => $objectId, 'removed' => false
        ]);

        if ($exists) {
            $section = new SimpleSection($this->commerce, [
                'title' => $this->getTitle()
            ]);
            $section->addWidget(new DonationsGrid($this->commerce, [
                'cause' => $objectId,
            ]));
            $this->addSection($section);

            return $this;
        }

        return $this->returnError($this->adapter->lexicon('commerce.item_not_found'));
    }
}
