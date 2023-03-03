<?php

namespace modmore\Commerce_Donations\Admin\Cause;

use comDonationCause;
use modmore\Commerce\Admin\Sections\SimpleSection;
use modmore\Commerce\Admin\Page;

class Update extends Page
{
    public $key = 'donations/cause/update';
    public $title = 'commerce_donations.edit_cause';
    public static $permissions = ['commerce', 'commerce_products'];

    public function setUp()
    {
        $objectId = (int)$this->getOption('id', 0);
        $exists = $this->adapter->getCount(comDonationCause::class, [
            'id' => $objectId, 'removed' => false
        ]);

        if ($exists) {
            $section = new SimpleSection($this->commerce, [
                'title' => $this->title
            ]);
            $section->addWidget((new Form($this->commerce, [
                'isUpdate' => true,
                'id' => $objectId
            ]))->setUp());
            $this->addSection($section);
            return $this;
        }

        return $this->returnError($this->adapter->lexicon('commerce.item_not_found'));
    }
}
