<?php

namespace modmore\Commerce_Donations\Admin\Donation;

use modmore\Commerce\Admin\Widgets\Form\CheckboxField;
use modmore\Commerce\Admin\Widgets\Form\HiddenField;
use modmore\Commerce\Admin\Widgets\Form\NumberField;
use modmore\Commerce\Admin\Widgets\Form\SelectField;
use modmore\Commerce\Admin\Widgets\Form\TextareaField;
use modmore\Commerce\Admin\Widgets\Form\TextField;
use modmore\Commerce\Admin\Widgets\Form\Validation\Length;
use modmore\Commerce\Admin\Widgets\Form\Validation\Number;
use modmore\Commerce\Admin\Widgets\Form\Validation\Required;
use modmore\Commerce\Admin\Widgets\FormWidget;

/**
 * Class Form
 * @package modmore\Commerce\Admin\Donation
 *
 * @property \comDonation $record
 */
class Form extends FormWidget
{
    protected $classKey = \comDonation::class;
    public $key = 'donation-form';
    public $title = '';

    public function getFields(array $options = []): array
    {
        $fields = [];
        $fields[] = new HiddenField($this->commerce, [
            'name' => 'cause',
            'value' => $this->getOption('cause'),
            'validation' => [
                new Required(),
            ]
        ]);

        $fields[] = new TextField($this->commerce, [
            'name' => 'donor_name',
            'label' => $this->adapter->lexicon('commerce_donations.donor_name'),
            'validation' => [
                new Required(),
                new Length(1, 100),
            ]
        ]);

        $fields[] = new TextareaField($this->commerce, [
            'name' => 'donor_note',
            'label' => $this->adapter->lexicon('commerce_donations.donor_note'),
        ]);

        $fields[] = new CheckboxField($this->commerce, [
            'name' => 'donor_public',
            'label' => $this->adapter->lexicon('commerce_donations.donor_public'),
        ]);

        $fields[] = new NumberField($this->commerce, [
            'name' => 'amount',
            'label' => $this->adapter->lexicon('commerce.amount'),
            'validation' => [
                new Number(0),
                new Required(),
            ],
            'input_class' => 'commerce-field-currency',
            'default' => 0,
        ]);

        $fields[] = new SelectField($this->commerce, [
            'name' => 'currency',
            'label' => $this->adapter->lexicon('commerce.currency'),
            'options' => $this->getCurrencyOptions(),
            'validation' => [
                new Required()
            ]
        ]);

        $fields[] = new HiddenField($this->commerce, [
            'name' => 'user',
            'value' => $this->commerce->modx->user->get('id'),
        ]);

        $fields[] = new HiddenField($this->commerce, [
            'name' => 'test',
            'default' => $this->commerce->isTestMode(),
        ]);

        $fields[] = new HiddenField($this->commerce, [
            'name' => 'donated_on',
            'value' => time(),
        ]);

        $fields[] = new HiddenField($this->commerce, [
            'name' => 'amount_ex_tax',
            'default' => '',
        ]);

        return $fields;
    }

    /**
     * @return array
     */
    protected function getCurrencyOptions(): array
    {
        $c = $this->adapter->newQuery('comCurrency');
        $c->where([
            'active' => true
        ]);

        /** @var \comCurrency[] $currencies */
        $currencies = $this->adapter->getCollection('comCurrency', $c);

        $output = [];
        foreach ($currencies as $currency) {
            $output[] = [
                'value' => $currency->get('alpha_code'),
                'label' => $currency->get('name') . ' (' . $currency->get('alpha_code') . ')'
            ];
        }

        return $output;
    }

    public function getFormAction(array $options = []): string
    {
        if ($this->record->get('id') > 0) {
            return $this->adapter->makeAdminUrl('donations/donation/update', ['id' => $this->record->get('id')]);
        }
        return $this->adapter->makeAdminUrl('donations/donation/create');
    }

    public function handleSubmit(array $values)
    {
        if ($this->record->get('id') < 1) {
            $values = array_merge($values, [
                'user' => $this->commerce->modx->user->get('id'),
                'donated_on' => time(),
            ]);
        }

        $values = array_merge($values, [
            // todo: Unsure how to handle this currently. Perhaps just default tax group?
            'amount_ex_tax' => $values['amount'],
        ]);

        return parent::handleSubmit($values);
    }

    public function afterSave()
    {
        /** @var \comDonationCause $cause */
        $cause = $this->adapter->getObject(\comDonationCause::class, $this->record->get('cause'));
        $cause->updateTotals();
    }
}
