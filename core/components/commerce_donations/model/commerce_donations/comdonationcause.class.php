<?php

use modmore\Commerce\Admin\Widgets\Form\CheckboxField;
use modmore\Commerce\Admin\Widgets\Form\DateTimeField;
use modmore\Commerce\Admin\Widgets\Form\ImageField;
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

    public static $translatableFields = ['name', 'description', 'cart_description'];
    private ?comDonationProduct $product = null;

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
            'description' => $this->adapter->lexicon('commerce_donations.description_desc'),
            'translations' => $this->getTranslations('description'),
        ]);

        $fields[] = new TextareaField($this->commerce, [
            'name' => 'cart_description',
            'label' => $this->adapter->lexicon('commerce_donations.cart_description'),
            'description' => $this->adapter->lexicon('commerce_donations.cart_description_desc'),
            'translations' => $this->getTranslations('cart_description'),
            'validation' => [
                new Length(0, 190),
            ]
        ]);

        $fields[] = new ImageField($this->commerce, [
            'name' => 'image',
            'label' => $this->adapter->lexicon('commerce.image'),
            'description' => $this->adapter->lexicon('commerce_donations.image_desc'),
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
            'default' => 0,
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

    public function getProduct(): comDonationProduct
    {
        if ($this->product) {
            return $this->product;
        }

        if ($this->get('product') > 0) {
            $this->product = $this->adapter->getObject(comDonationProduct::class, [
                'id' => $this->get('product'),
                'target' => $this->get('id'),
            ]);
            $this->product->fromArray([
                'name' => $this->get('name'),
                'description' => $this->get('cart_description'),
                'image' => $this->get('image'),
                'tax_group' => $this->adapter->getOption('commerce_donations.tax_group', null, 1),
                'delivery_type' => $this->adapter->getOption('commerce_donations.delivery_type', null, 1),
                'stock_infinite' => true,
            ]);
            $this->product->save();
        }

        if (!$this->product) {
            $this->product = $this->adapter->newObject(comDonationProduct::class);
            $this->product->fromArray([
                'sku' => 'DONATIONS-' . $this->get('id'),
                'name' => $this->get('name'),
                'description' => $this->get('cart_description'),
                'image' => $this->get('image'),
                'target' => $this->get('id'),

                'tax_group' => $this->adapter->getOption('commerce_donations.tax_group', null, 1),
                'delivery_type' => $this->adapter->getOption('commerce_donations.delivery_type', null, 1),
                'stock_infinite' => true,
            ]);
            $this->product->save();
            $this->set('product', $this->product->get('id'));
            $this->save();
        }

        return $this->product;
    }

    public function duplicate(array $options = [])
    {
        // @todo duplicate product
        return parent::duplicate($options); // TODO: Change the autogenerated stub
    }

    public function updateTotals(): void
    {
        $c = $this->adapter->newQuery(comDonation::class);
        $c->select([
            'SUM(amount) as total_amt'
        ]);
        $c->where([
            'test' => $this->commerce->isTestMode(),
            'cause' => $this->get('id'),
        ]);
        $c->prepare();
        if ($stmt = $this->xpdo->query($c->toSQL())) {
            $this->set('donated_total', $stmt->fetchColumn());
            if ($this->get('goal') > 0) {
                $this->set('donated_total_perc', ($this->get('donated_total') / $this->get('goal')) * 100);
            }
        }

        $c = $this->adapter->newQuery(comDonation::class);
        $c->select([
            'AVG(amount) as avg_amt'
        ]);
        $c->where([
            'test' => $this->commerce->isTestMode(),
            'cause' => $this->get('id'),
        ]);
        $c->prepare();
        if ($stmt = $this->xpdo->query($c->toSQL())) {
            $this->set('average_donation', $stmt->fetchColumn());
        }

        if ($this->get('goal_period') === 'total') {
            $this->set('donated_period', $this->get('donated_total'));
            $this->set('donated_period_perc', $this->get('donated_total_perc'));
        }
        else {
            $c = $this->adapter->newQuery(comDonation::class);
            $c->select([
                'SUM(amount) as total_amt'
            ]);
            $c->where([
                'test' => $this->commerce->isTestMode(),
                'cause' => $this->get('id'),
                'donated_on:>=' => strtotime('-1 ' . $this->get('goal_period')),
            ]);
            $c->prepare();
            if ($stmt = $this->xpdo->query($c->toSQL())) {
                $this->set('donated_period', $stmt->fetchColumn());
                if ($this->get('goal') > 0) {
                    $this->set('donated_period_perc', ($this->get('donated_period') / $this->get('goal')) * 100);
                }
            }
        }

        $this->save();
    }
}
