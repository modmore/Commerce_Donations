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

$xpdo_meta_map['comDonationProduct']= array (
  'package' => 'commerce_donations',
  'version' => '1.1',
  'extends' => 'comProduct',
  'tableMeta' => 
  array (
    'engine' => 'InnoDB',
  ),
  'fields' => 
  array (
  ),
  'fieldMeta' => 
  array (
  ),
  'aggregates' => 
  array (
    'Cause' => 
    array (
      'class' => 'comDonationCause',
      'local' => 'target',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
