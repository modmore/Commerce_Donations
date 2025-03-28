<?php

namespace modmore\Commerce_Donations\Admin\Donation;

use modmore\Commerce\Admin\Sections\SimpleSection;
use modmore\Commerce\Admin\Page;

class Create extends Page
{
    public $key = 'donations/donation/create';
    public $title = 'commerce_donations.add_donation';
    public static $permissions = ['commerce', 'commerce_products'];

    public function setUp()
    {
        $section = new SimpleSection($this->commerce, [
            'title' => $this->title
        ]);
        $section->addWidget((new Form($this->commerce, [
            'id' => 0,
            'cause' => $this->getOption('cause'),
        ]))->setUp());
        $this->addSection($section);
        return $this;
    }
}
