<?php

namespace modmore\Commerce_Donations\Admin\Donation;

use modmore\Commerce\Admin\Widgets\DeleteFormWidget;

/**
 * Class Form
 * @package modmore\Commerce\Admin\Donation
 *
 * @property \comDonation $record
 */
class DeleteForm extends DeleteFormWidget
{
    public function afterDelete()
    {
        /** @var \comDonationCause $cause */
        $cause = $this->adapter->getObject(\comDonationCause::class, $this->record->get('cause'));
        $cause->updateTotals();
    }
}