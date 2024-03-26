<?php


namespace Espo\Modules\Crm\Repositories;

use Espo\ORM\Entity;


class KnowledgeBaseArticle extends \Espo\Core\Repositories\Database
{
    protected function beforeSave(Entity $entity, array $options = [])
    {
        parent::beforeSave($entity, $options);

        $order = $entity->get('order');

        if (is_null($order)) {
            $order = $this->min('order');

            if (!$order) {
                $order = 9999;
            }

            $order--;

            $entity->set('order', $order);
        }
    }
}
