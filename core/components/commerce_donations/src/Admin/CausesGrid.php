<?php

namespace modmore\Commerce_Donations\Admin;

use comDonationCause;
use modmore\Commerce\Admin\Util\Action;
use modmore\Commerce\Admin\Util\Column;
use modmore\Commerce\Admin\Widgets\GridWidget;

class CausesGrid extends GridWidget
{
    public $key = 'donation-causes-grid';
    public $title = '';

    public function getItems(array $options = array())
    {
        $items = [];

        $c = $this->adapter->newQuery('comDonationCause');
        $c->where([
            'removed' => false,
        ]);
        $c->select($this->adapter->getSelectColumns('comDonationCause', 'comDonationCause'));

        if (array_key_exists('search_by_name', $options) && !empty($options['search_by_name'])) {
            $options['search_by_name'] = trim($options['search_by_name']);
            foreach (array_filter(explode(' ', $options['search_by_name'])) as $searchByNamePart) {
                $c->where([
                    'name:LIKE' => "%$searchByNamePart%"
                ]);
            }
        }

        $sortby = array_key_exists('sortby', $options) && !empty($options['sortby']) ? $this->adapter->escape($options['sortby']) : $this->defaultSort;
        $sortdir = array_key_exists('sortdir', $options) && strtoupper($options['sortdir']) === 'DESC' ? 'DESC' : 'ASC';
        $c->sortby($sortby, $sortdir);

        $count = $this->adapter->getCount('comDonationCause', $c);
        $this->setTotalCount($count);

        $c->limit($options['limit'], $options['start']);
        /** @var comDonationCause[] $collection */
        $collection = $this->adapter->getCollection('comDonationCause', $c);

        foreach ($collection as $cause) {
            $items[] = $this->prepareItem($cause);
        }

        return $items;
    }

    public function getColumns(array $options = array())
    {
        return [
            new Column('name', $this->adapter->lexicon('commerce.name'), true, true),
            new Column('goal', $this->adapter->lexicon('commerce_donations.goal'), true, true),
            new Column('donated_period_formatted', $this->adapter->lexicon('commerce_donations.donated_period'), true, true),
            new Column('donated_total_formatted', $this->adapter->lexicon('commerce_donations.donated_total'), true, true),
            new Column('average_donation_formatted', $this->adapter->lexicon('commerce_donations.average_donation'), true),
        ];
    }

    public function prepareItem(comDonationCause $cause)
    {
        $item = $cause->toArray();

        $editLink = $this->adapter->makeAdminUrl('donations/cause/update', [
            'id' => $cause->get('id')
        ]);
        $item['name'] = '<a href="' . $editLink . '" class="commerce-ajax-modal" style="font-weight: 600;"><nobr>' . $this->encode($cause->get('name')) . '</nobr></a>';
        $item['name'] .= ' <nobr style="color: #6a6a6a;">(#' . $item['id'] . ')</nobr>';

        $item['goal'] = $item['goal_formatted'];
        if ($item['goal_period'] !== 'total') {
            $item['goal'] .= ' ' . $this->adapter->lexicon('commerce_donations.goal_period.' . $item['goal_period']);
        }

        if ($item['goal_by'] > 0) {
            $item['goal'] .= '<br><span style="color:#6a6a6a;"><i class="icon icon-calendar"></i> ' . $item['goal_by_formatted'] . '</span>';
        }

        if ($cause->get('goal') > 0) {
            $item['donated_total_formatted'] .= ' <span style="color:#6a6a6a; padding-left: 1em;">' . $item['donated_total_perc_formatted'] . '</span>';
            $item['donated_period_formatted'] .= ' <span style="color:#6a6a6a; padding-left: 1em;">' . $item['donated_period_perc_formatted'] . '</span>';
        }

        $item['actions'] = [];

        $item['actions'][] = (new Action())
            ->setUrl($editLink)
            ->setTitle($this->adapter->lexicon('commerce_donations.edit_cause'))
            ->setIcon('icon-edit');

        $item['actions'][] = (new Action())
            ->setUrl($this->adapter->makeAdminUrl('donations/cause/duplicate', ['id' => $item['id'], 'class_key' => $cause->get('class_key')]))
            ->setTitle($this->adapter->lexicon('commerce_donations.duplicate_cause'))
            ->setIcon('icon-copy');

        $item['actions'][] = (new Action())
            ->setUrl($this->adapter->makeAdminUrl('donations/cause/delete', ['id' => $item['id']]))
            ->setTitle($this->adapter->lexicon('commerce_donations.delete_cause'))
            ->setIcon('icon-trash');

        return $item;
    }


    public function getTopToolbar(array $options = array())
    {
        $toolbar = [];

        $toolbar[] = [
            'name' => 'add-cause',
            'title' => $this->adapter->lexicon('commerce_donations.add_cause'),
            'type' => 'button',
            'link' => $this->adapter->makeAdminUrl('donations/cause/create'),
            'button_class' => 'commerce-ajax-modal',
            'icon_class' => 'plus',
            'modal_title' => $this->adapter->lexicon('commerce_donations.add_cause'),
            'position' => 'top',
        ];

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
