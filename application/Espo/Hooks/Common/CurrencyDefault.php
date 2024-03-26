<?php


namespace Espo\Hooks\Common;

use Espo\ORM\Entity;

use Espo\Core\Utils\{
    Config,
    FieldUtil,
};

class CurrencyDefault
{
    public static int $order = 200;

    private Config $config;

    private FieldUtil $fieldUtil;

    public function __construct(Config $config, FieldUtil $fieldUtil)
    {
        $this->config = $config;
        $this->fieldUtil = $fieldUtil;
    }

    public function beforeSave(Entity $entity): void
    {
        $fieldList = $this->fieldUtil->getFieldByTypeList($entity->getEntityType(), 'currency');

        $defaultCurrency = $this->config->get('defaultCurrency');

        foreach ($fieldList as $field) {
            $currencyAttribute = $field . 'Currency';

            if ($entity->isNew()) {
                if ($entity->get($field) && !$entity->get($currencyAttribute)) {
                    $entity->set($currencyAttribute, $defaultCurrency);
                }

                continue;
            }

            if (
                $entity->isAttributeChanged($field) && $entity->has($currencyAttribute) &&
                !$entity->get($currencyAttribute)
            ) {
                $entity->set($currencyAttribute, $defaultCurrency);
            }
        }
    }
}
