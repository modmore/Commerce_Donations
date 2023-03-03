<?php

namespace modmore\Commerce_Donations\Admin;

use modmore\Commerce\Admin\Page;
use modmore\Commerce\Admin\Sections\SimpleSection;

class Causes extends Page
{
    public $key = 'products/donations/causes';
    public $title = 'commerce_donations.causes_menu';
    public static $permissions = ['commerce', 'commerce_products'];

    public function setUp()
    {
        $section = new SimpleSection($this->commerce, [
            'title' => $this->getTitle()
        ]);
        $section->addWidget(new CausesGrid($this->commerce));
        $this->addSection($section);
        return $this;
    }
}
