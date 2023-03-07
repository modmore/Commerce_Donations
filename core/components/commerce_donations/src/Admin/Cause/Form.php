<?php

namespace modmore\Commerce_Donations\Admin\Cause;

use comDonationCause;
use modmore\Commerce\Admin\Widgets\Form\ClassField;
use modmore\Commerce\Admin\Widgets\Form\DateTimeField;
use modmore\Commerce\Admin\Widgets\Form\NumberField;
use modmore\Commerce\Admin\Widgets\Form\SelectField;
use modmore\Commerce\Admin\Widgets\Form\Tab;
use modmore\Commerce\Admin\Widgets\Form\TextField;
use modmore\Commerce\Admin\Widgets\Form\Validation\Length;
use modmore\Commerce\Admin\Widgets\Form\Validation\Number;
use modmore\Commerce\Admin\Widgets\Form\Validation\Required;
use modmore\Commerce\Admin\Widgets\FormWidget;

/**
 * Class Form
 * @package modmore\Commerce\Admin\Configuration\PaymentMethods
 *
 * @property comDonationCause $record
 */
class Form extends FormWidget
{
    protected $classKey = comDonationCause::class;
    public $key = 'donation-cause-form';
    public $title = '';

    public function getFields(array $options = [])
    {
        $fields = [];

        $fields[] = new ClassField($this->commerce, [
            'name' => 'class_key',
            'label' => $this->adapter->lexicon('commerce.class_key'),
            'description' => $this->adapter->lexicon('commerce.class_key.description'),
            'parentClass' => comDonationCause::class,
            'validation' => [
                new Required(),
                new Length(3, 100),
            ]
        ]);


        return array_merge($fields, $this->record->getModelFields());
    }

    public function getFormAction(array $options = [])
    {
        if ($this->record->get('id') > 0) {
            return $this->adapter->makeAdminUrl('donations/cause/update', ['id' => $this->record->get('id')]);
        }
        return $this->adapter->makeAdminUrl('donations/cause/create');
    }

    public function afterSave()
    {
        // Update totals after saving as a useful check (i.e. switching test/live mode)
        $this->record->updateTotals();
    }
}
