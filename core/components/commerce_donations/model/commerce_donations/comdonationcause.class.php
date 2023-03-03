<?php

use modmore\Commerce\Admin\Widgets\Form\CheckboxField;
use modmore\Commerce\Admin\Widgets\Form\DateTimeField;
use modmore\Commerce\Admin\Widgets\Form\NumberField;
use modmore\Commerce\Admin\Widgets\Form\SelectField;
use modmore\Commerce\Admin\Widgets\Form\TextareaField;
use modmore\Commerce\Admin\Widgets\Form\TextField;
use modmore\Commerce\Admin\Widgets\Form\Validation\Length;
use modmore\Commerce\Admin\Widgets\Form\Validation\Number;
use modmore\Commerce\Admin\Widgets\Form\Validation\Required;
use modmore\Commerce\Traits\SoftDelete;

/**
 * Donations for Commerce.
 *
 * Copyright 2023 by modmore <support@modmore.com>
 *
 * This file is meant to be used with Commerce by modmore. A valid Commerce license is required.
 *
 * @package commerce_donations
 * @license See core/components/commerce_donations/docs/license.txt
 */
class comDonationCause extends comSimpleObject
{
    use SoftDelete;

    public static $translatableFields = ['name', 'description'];

    public function getModelFields(): array
    {
        $fields = [];


        $fields[] = new TextField($this->commerce, [
            'name' => 'name',
            'label' => $this->adapter->lexicon('commerce.name'),
            'translations' => $this->getTranslations('name'),
            'validation' => [
                new Required(),
                new Length(1, 100),
            ]
        ]);


        $fields[] = new TextareaField($this->commerce, [
            'name' => 'description',
            'label' => $this->adapter->lexicon('commerce.description'),
            'translations' => $this->getTranslations('description'),
            'validation' => [
                new Length(0, 190),
            ]
        ]);

        $fields[] = new CheckboxField($this->commerce, [
            'name' => 'active',
            'label' => $this->adapter->lexicon('commerce.active'),
        ]);

        $fields[] = new NumberField($this->commerce, [
            'name' => 'goal',
            'label' => $this->adapter->lexicon('commerce_donations.goal'),
            'validation' => [
                new Number(0),
            ],
            'input_class' => 'commerce-field-currency',
        ]);

        $fields[] = new SelectField($this->commerce, [
            'name' => 'goal_period',
            'label' => $this->adapter->lexicon('commerce_donations.goal_period'),
            'description' => $this->adapter->lexicon('commerce_donations.goal_period_desc'),
            'options' => [
                [
                    'label' => $this->adapter->lexicon('commerce_donations.goal_period.total'),
                    'value' => 'total',
                ],
                [
                    'label' => $this->adapter->lexicon('commerce_donations.goal_period.month'),
                    'value' => 'month',
                ],
                [
                    'label' => $this->adapter->lexicon('commerce_donations.goal_period.year'),
                    'value' => 'year',
                ],
            ]
        ]);

        $fields[] = new DateTimeField($this->commerce, [
            'name' => 'goal_by',
            'label' => $this->adapter->lexicon('commerce_donations.goal_by'),
            'description' => $this->adapter->lexicon('commerce_donations.goal_by_desc'),
            'validation' => [

            ]
        ]);

        $fields[] = new TextField($this->commerce, [
            'name' => 'suggested_amounts',
            'label' => $this->adapter->lexicon('commerce_donations.suggested_amounts'),
            'description' => $this->adapter->lexicon('commerce_donations.suggested_amounts_desc'),
            'validation' => [

            ]
        ]);
        $fields[] = new CheckboxField($this->commerce, [
            'name' => 'allow_arbitrary_amount',
            'label' => $this->adapter->lexicon('commerce_donations.allow_arbitrary_amount'),
        ]);

        return $fields;
    }

}
