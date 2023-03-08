<?php

namespace modmore\Commerce_Donations\Admin\Cause;

use comDonation;
use modmore\Commerce\Admin\Util\Column;
use modmore\Commerce\Admin\Widgets\GridWidget;

class DonationsGrid extends GridWidget
{
    public $key = 'donation-causes-grid';
    public $title = '';
    public $defaultSort = 'donated_on';
    public $defaultSortDir = 'DESC';

    public function getItems(array $options = array()): array
    {
        $items = [];

        $c = $this->adapter->newQuery('comDonation');
        $c->where([
            'cause' => (int)$this->getOption('cause'),
            'test' => $this->commerce->isTestMode(),
        ]);
        $c->select($this->adapter->getSelectColumns('comDonation', 'comDonation'));

        if (array_key_exists('search_by_name', $options) && !empty($options['search_by_name'])) {
            $options['search_by_name'] = trim($options['search_by_name']);
            foreach (array_filter(explode(' ', $options['search_by_name'])) as $searchByNamePart) {
                $c->where([
                    'donor_name:LIKE' => "%$searchByNamePart%"
                ]);
            }
        }

        $sortby = array_key_exists('sortby', $options) && !empty($options['sortby']) ? $this->adapter->escape($options['sortby']) : $this->defaultSort;
        $sortdir = array_key_exists('sortdir', $options) && strtoupper($options['sortdir']) === 'DESC' ? 'DESC' : 'ASC';
        $c->sortby($sortby, $sortdir);

        $count = $this->adapter->getCount('comDonation', $c);
        $this->setTotalCount($count);
        $c->prepare();
        $this->adapter->log(1, $c->toSQL());

        $c->limit($options['limit'], $options['start']);
        /** @var comDonation[] $collection */
        $collection = $this->adapter->getCollection('comDonation', $c);

        foreach ($collection as $cause) {
            $items[] = $this->prepareItem($cause);
        }

        return $items;
    }

    public function getColumns(array $options = array()): array
    {
        return [
            new Column('donated_on', $this->adapter->lexicon('commerce.received_on'), true),
            new Column('amount', $this->adapter->lexicon('commerce.amount'), true, true),
            new Column('donor_name', $this->adapter->lexicon('commerce.name'), true),
            new Column('order', $this->adapter->lexicon('commerce.order'), false, true),
        ];
    }

    public function prepareItem(comDonation $cause): array
    {
        $item = $cause->toArray();

        $item['donated_on'] = $item['donated_on_formatted'];
        $item['amount'] = $item['amount_formatted'];
        if ($item['amount_ex_tax_formatted'] !== $item['amount_formatted']) {
            $item['amount'] .= ' <span style="color: #777; margin-left: 1em;">' . $item['amount_ex_tax_formatted'] . ' ex taxes</span>';
        }
        $item['name'] = !empty($item['name']) ? $item['name'] : 'anonymous';

        if ($order = $this->adapter->getObject(\comOrder::class, [
            'id' => $item['order'],
            'test' => $this->commerce->isTestMode(),
        ])) {
            $orderUrl = $this->adapter->makeAdminUrl('order', ['order' => $order->get('id')]);
            $item['order'] = '<a href="' . $orderUrl . '" class="commerce-ajax-modal">' . $order->get('reference') . '</a>';
        }

        return $item;
    }


    public function getTopToolbar(array $options = array()): array
    {
        $toolbar = [];

        $toolbar[] = [
            'name' => 'search_by_name',
            'title' => $this->adapter->lexicon('commerce.search_by_name'),
            'type' => 'textfield',
            'value' => array_key_exists('search_by_name', $options) ? (string)$options['search_by_name'] : '',
            'position' => 'top',
        ];

        $toolbar[] = [
            'name' => 'limit',
            'title' => $this->adapter->lexicon('commerce.limit'),
            'type' => 'textfield',
            'value' => ((int)$options['limit'] === 10) ? '' : (int)$options['limit'],
            'position' => 'bottom',
        ];
        return $toolbar;
    }
}
