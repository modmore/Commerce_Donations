<?php
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
class comDonationProduct extends comProduct
{
    public static $visibleType = false;
    private ?comDonationCause $cause = null;

    /**
     * @return comDonationCause|false|null
     */
    public function getTarget()
    {
        if ($this->cause) {
            return $this->cause;
        }

        $this->cause = $this->adapter->getObject(comDonationCause::class, [
            'id' => $this->get('target'),
        ]);

        return $this->cause;
    }
}
