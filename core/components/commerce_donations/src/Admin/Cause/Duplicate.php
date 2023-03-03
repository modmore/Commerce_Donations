<?php

namespace modmore\Commerce_Donations\Admin\Cause;

use comDonationCause;
use modmore\Commerce\Admin\Page;
use modmore\Commerce\Admin\Sections\SimpleSection;

class Duplicate extends Page
{
    public $key = 'duplicate-cause';
    public $title = 'commerce_donations.duplicate_cause';
    public static $permissions = ['commerce', 'commerce_products'];

    public function setUp()
    {
        $objectId = (int)$this->getOption('id', 0);
        $duplicate = $this->adapter->getObject(comDonationCause::class, [
            'id' => $objectId,
            'removed' => false
        ]);

        if ($duplicate instanceof \comDonationCause) {
            $new = $duplicate->duplicate();

            if (!$new) {
                return $this->returnError($this->adapter->lexicon('commerce.product.duplicate_failed'));
            }
            $section = new SimpleSection($this->commerce, [
                'title' => $this->title
            ]);

            $section->addWidget((new Form($this->commerce, [
                'isUpdate' => true,
                'id' => $new->get('id')
            ]))->setUp());
            $this->addSection($section);
            return $this;
        }
        return $this->returnError($this->adapter->lexicon('commerce.item_not_found'));
    }
}
