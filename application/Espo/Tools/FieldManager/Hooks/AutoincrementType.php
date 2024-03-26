<?php


namespace Espo\Tools\FieldManager\Hooks;

use Espo\Core\Di;
use Espo\Core\Exceptions\Error;

class AutoincrementType implements Di\MetadataAware
{
    use Di\MetadataSetter;

    
    public function beforeSave(string $scope, string $name, $defs, $options): void
    {
        if (!isset($options['isNew']) || !$options['isNew']) {
            return;
        }

        $fields = $this->metadata->get(['entityDefs', $scope, 'fields']);

        foreach ($fields as $fieldName => $fieldDefs) {
            if ($fieldDefs['type'] == 'autoincrement') {
                throw new Error('The entity can have only one Auto-increment field.');
            }
        }
    }
}
