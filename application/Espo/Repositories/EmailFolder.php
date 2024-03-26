<?php


namespace Espo\Repositories;

use Espo\ORM\Entity;


class EmailFolder extends \Espo\Core\Repositories\Database
{
    protected function beforeSave(Entity $entity, array $options = [])
    {
        parent::beforeSave($entity, $options);

        $order = $entity->get('order');

        if (is_null($order)) {
            $order = $this->max('order');

            if (!$order) {
                $order = 0;
            }

            $order++;

            $entity->set('order', $order);
        }
    }
}
