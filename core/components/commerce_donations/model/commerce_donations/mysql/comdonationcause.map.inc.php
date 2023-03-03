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

$xpdo_meta_map['comDonationCause']= array (
  'package' => 'commerce_donations',
  'version' => '1.1',
  'extends' => 'comSimpleObject',
  'table' => 'commerce_donation_cause',
  'tableMeta' => 
  array (
    'engine' => 'InnoDB',
  ),
  'fields' => 
  array (
    'product' => 0,
    'name' => '',
    'description' => '',
    'active' => 0,
    'goal' => 0,
    'goal_period' => 'total',
    'goal_by' => 0,
    'donated_total' => 0,
    'donated_total_perc' => 0.0,
    'donated_period' => 0,
    'donated_period_perc' => 0.0,
    'average_donation' => 0,
    'suggested_amounts' => '',
    'allow_arbitrary_amount' => 0,
    'removed' => 0,
    'removed_on' => 0,
    'removed_by' => 0,
  ),
  'fieldMeta' => 
  array (
    'product' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'description' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '190',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'active' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'boolean',
      'null' => false,
      'default' => 0,
    ),
    'goal' => 
    array (
      'formatter' => 'financial',
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'goal_period' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
      'phptype' => 'string',
      'null' => false,
      'default' => 'total',
    ),
    'goal_by' => 
    array (
      'formatter' => 'datetime',
      'dbtype' => 'int',
      'precision' => '20',
      'phptype' => 'int',
      'null' => false,
      'default' => 0,
    ),
    'donated_total' => 
    array (
      'formatter' => 'financial',
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'donated_total_perc' => 
    array (
      'formatter' => 'percentage',
      'dbtype' => 'decimal',
      'precision' => '20,4',
      'phptype' => 'float',
      'null' => false,
      'default' => 0.0,
    ),
    'donated_period' => 
    array (
      'formatter' => 'financial',
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'donated_period_perc' => 
    array (
      'formatter' => 'percentage',
      'dbtype' => 'decimal',
      'precision' => '20,4',
      'phptype' => 'float',
      'null' => false,
      'default' => 0.0,
    ),
    'average_donation' => 
    array (
      'formatter' => 'financial',
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'suggested_amounts' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'allow_arbitrary_amount' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'boolean',
      'null' => false,
      'default' => 0,
    ),
    'removed' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'boolean',
      'null' => false,
      'default' => 0,
    ),
    'removed_on' => 
    array (
      'formatter' => 'datetime',
      'dbtype' => 'int',
      'precision' => '20',
      'phptype' => 'int',
      'null' => false,
      'default' => 0,
    ),
    'removed_by' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'boolean',
      'null' => false,
      'default' => 0,
    ),
  ),
  'indexes' => 
  array (
    'product' => 
    array (
      'alias' => 'product',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'product' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'name' => 
    array (
      'alias' => 'name',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'name' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'active' => 
    array (
      'alias' => 'active',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'active' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'goal' => 
    array (
      'alias' => 'goal',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'goal' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'goal_by' => 
    array (
      'alias' => 'goal_by',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'goal_by' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'removed' => 
    array (
      'alias' => 'removed',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'removed' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
  'composites' => 
  array (
    'Donations' => 
    array (
      'class' => 'comDonation',
      'local' => 'id',
      'foreign' => 'cause',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
  'aggregates' => 
  array (
    'Product' => 
    array (
      'class' => 'comDonationProduct',
      'local' => 'product',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
