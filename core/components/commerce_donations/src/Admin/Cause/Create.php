<?php

namespace modmore\Commerce_Donations\Admin\Cause;

use modmore\Commerce\Admin\Sections\SimpleSection;
use modmore\Commerce\Admin\Page;

class Create extends Page
{
    public $key = 'product/donations/cause/create';
    public $title = 'commerce_donations.add_cause';
    public static $permissions = ['commerce', 'commerce_products'];

    public function setUp()
    {
        $section = new SimpleSection($this->commerce, [
            'title' => $this->title
        ]);
        $section->addWidget((new Form($this->commerce, ['id' => 0]))->setUp());
        $this->addSection($section);
        return $this;
    }
}
