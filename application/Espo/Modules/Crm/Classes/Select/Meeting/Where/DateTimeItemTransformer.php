<?php


namespace Espo\Modules\Crm\Classes\Select\Meeting\Where;

use Espo\Core\Select\Where\DateTimeItemTransformer as DateTimeItemTransformerOriginal;
use Espo\Core\Select\Where\Item;


class DateTimeItemTransformer extends DateTimeItemTransformerOriginal
{
    public function transform(Item $item): Item
    {
        $type = $item->getType();
        $value = $item->getValue();
        $attribute = $item->getAttribute();

        $transformedItem = parent::transform($item);

        if (!in_array($attribute, ['dateStart', 'dateEnd'])) {
            return $transformedItem;
        }

        if (in_array($type, ['isNull', 'ever', 'isNotNull'])) {
            return $transformedItem;
        }

        $attributeDate = $attribute . 'Date';

        if (is_string($value)) {
            if (strlen($value) > 11) {
                return $transformedItem;
            }
        }
        else if (is_array($value)) {
            foreach ($value as $valueItem) {
                if (is_string($valueItem) && strlen($valueItem) > 11) {
                    return $transformedItem;
                }
            }
        }

        $datePartRaw = [
            'attribute' => $attributeDate,
            'type' => $type,
            'value' => $value,
        ];

        $raw = [
            'type' => 'or',
            'value' => [
                $datePartRaw,
                [
                    'type' => 'and',
                    'value' => [
                        $transformedItem->getRaw(),
                        [
                            'type' => 'isNull',
                            'attribute' => $attributeDate,
                        ]
                    ]

                ]
            ]
        ];

        return Item::fromRaw($raw);
    }
}
