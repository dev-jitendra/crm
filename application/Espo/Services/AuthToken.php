<?php


namespace Espo\Services;

use stdClass;


class AuthToken extends Record
{
    protected $actionHistoryDisabled = true;

    public function filterUpdateInput(stdClass $data): void
    {
        parent::filterUpdateInput($data);

        $dataArray = get_object_vars($data);

        foreach (array_keys($dataArray) as $attribute) {
            if ($attribute !== 'isActive') {
                unset($data->$attribute);

                continue;
            }
        }

        if ($data->isActive ?? false) {
            unset($data->isActive);
        }
    }
}
