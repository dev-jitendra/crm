<?php


namespace Espo\Core\Utils\Database\Orm\FieldConverters;

use Espo\Core\Utils\Database\Orm\Defs\AttributeDefs;
use Espo\Core\Utils\Database\Orm\Defs\EntityDefs;
use Espo\Core\Utils\Database\Orm\Defs\RelationDefs;
use Espo\Core\Utils\Database\Orm\FieldConverter;
use Espo\Entities\PhoneNumber;
use Espo\ORM\Defs\FieldDefs;
use Espo\ORM\Type\AttributeType;
use Espo\ORM\Type\RelationType;


class Phone implements FieldConverter
{
    private const COLUMN_ENTITY_TYPE_LENGTH = 100;

    public function convert(FieldDefs $fieldDefs, string $entityType): EntityDefs
    {
        $name = $fieldDefs->getName();

        $foreignJoinAlias = "$name$entityType{alias}Foreign";
        $foreignJoinMiddleAlias = "$name$entityType{alias}ForeignMiddle";

        $emailAddressDefs = AttributeDefs
            ::create($name)
            ->withType(AttributeType::VARCHAR)
            ->withParamsMerged(
                $this->getPhoneNumberParams($entityType, $foreignJoinAlias, $foreignJoinMiddleAlias)
            );

        $dataDefs = AttributeDefs
            ::create($name . 'Data')
            ->withType(AttributeType::JSON_ARRAY)
            ->withNotStorable()
            ->withParamsMerged([
                'notExportable' => true,
                'isPhoneNumberData' => true,
                'field' => $name,
            ]);

        $isOptedOutDefs = AttributeDefs
            ::create($name . 'IsOptedOut')
            ->withType(AttributeType::BOOL)
            ->withNotStorable()
            ->withParamsMerged(
                $this->getIsOptedOutParams($foreignJoinAlias, $foreignJoinMiddleAlias)
            );

        $isInvalidDefs = AttributeDefs
            ::create($name . 'IsInvalid')
            ->withType(AttributeType::BOOL)
            ->withNotStorable()
            ->withParamsMerged(
                $this->getIsInvalidParams($foreignJoinAlias, $foreignJoinMiddleAlias)
            );

        $numericAttribute = AttributeDefs
            ::create($name . 'Numeric')
            ->withType(AttributeType::VARCHAR)
            ->withNotStorable()
            ->withParamsMerged(
                $this->getNumericParams($entityType)
            );

        $relationDefs = RelationDefs
            ::create('phoneNumbers')
            ->withType(RelationType::MANY_MANY)
            ->withForeignEntityType(PhoneNumber::ENTITY_TYPE)
            ->withRelationshipName('entityPhoneNumber')
            ->withMidKeys('entityId', 'phoneNumberId')
            ->withConditions(['entityType' => $entityType])
            ->withAdditionalColumn(
                AttributeDefs
                    ::create('entityType')
                    ->withType(AttributeType::VARCHAR)
                    ->withLength(self::COLUMN_ENTITY_TYPE_LENGTH)
            )
            ->withAdditionalColumn(
                AttributeDefs
                    ::create('primary')
                    ->withType(AttributeType::BOOL)
                    ->withDefault(false)
            );

        return EntityDefs::create()
            ->withAttribute($emailAddressDefs)
            ->withAttribute($dataDefs)
            ->withAttribute($isOptedOutDefs)
            ->withAttribute($isInvalidDefs)
            ->withAttribute($numericAttribute)
            ->withRelation($relationDefs);
    }

    
    private function getPhoneNumberParams(
        string $entityType,
        string $foreignJoinAlias,
        string $foreignJoinMiddleAlias,
    ): array {

        return [
            'select' => [
                "select" => "phoneNumbers.name",
                'leftJoins' => [['phoneNumbers', 'phoneNumbers', ['primary' => true]]],
            ],
            'selectForeign' => [
                "select" => "$foreignJoinAlias.name",
                'leftJoins' => [
                    [
                        'EntityPhoneNumber',
                        $foreignJoinMiddleAlias,
                        [
                            "$foreignJoinMiddleAlias.entityId:" => "{alias}.id",
                            "$foreignJoinMiddleAlias.primary" => true,
                            "$foreignJoinMiddleAlias.deleted" => false,
                        ]
                    ],
                    [
                        PhoneNumber::ENTITY_TYPE,
                        $foreignJoinAlias,
                        [
                            "$foreignJoinAlias.id:" => "$foreignJoinMiddleAlias.phoneNumberId",
                            "$foreignJoinAlias.deleted" => false,
                        ]
                    ]
                ],
            ],
            'fieldType' => 'phone',
            'where' => [
                'LIKE' => [
                    'whereClause' => [
                        'id=s' => [
                            'from' => 'EntityPhoneNumber',
                            'select' => ['entityId'],
                            'joins' => [
                                [
                                    'phoneNumber',
                                    'phoneNumber',
                                    [
                                        'phoneNumber.id:' => 'phoneNumberId',
                                        'phoneNumber.deleted' => false,
                                    ],
                                ],
                            ],
                            'whereClause' => [
                                'deleted' => false,
                                'entityType' => $entityType,
                                'phoneNumber.name*' => '{value}',
                            ],
                        ],
                    ],
                ],
                'NOT LIKE' => [
                    'whereClause' => [
                        'id!=s' => [
                            'from' => 'EntityPhoneNumber',
                            'select' => ['entityId'],
                            'joins' => [
                                [
                                    'phoneNumber',
                                    'phoneNumber',
                                    [
                                        'phoneNumber.id:' => 'phoneNumberId',
                                        'phoneNumber.deleted' => false,
                                    ],
                                ],
                            ],
                            'whereClause' => [
                                'deleted' => false,
                                'entityType' => $entityType,
                                'phoneNumber.name*' => '{value}',
                            ],
                        ],
                    ],
                ],
                '=' => [
                    'leftJoins' => [['phoneNumbers', 'phoneNumbersMultiple']],
                    'whereClause' => [
                        'phoneNumbersMultiple.name=' => '{value}',
                    ],
                    'distinct' => true,
                ],
                '<>' => [
                    'leftJoins' => [['phoneNumbers', 'phoneNumbersMultiple']],
                    'whereClause' => [
                        'phoneNumbersMultiple.name!=' => '{value}',
                    ],
                    'distinct' => true,
                ],
                'IN' => [
                    'leftJoins' => [['phoneNumbers', 'phoneNumbersMultiple']],
                    'whereClause' => [
                        'phoneNumbersMultiple.name=' => '{value}',
                    ],
                    'distinct' => true,
                ],
                'NOT IN' => [
                    'leftJoins' => [['phoneNumbers', 'phoneNumbersMultiple']],
                    'whereClause' => [
                        'phoneNumbersMultiple.name!=' => '{value}',
                    ],
                    'distinct' => true,
                ],
                'IS NULL' => [
                    'leftJoins' => [['phoneNumbers', 'phoneNumbersMultiple']],
                    'whereClause' => [
                        'phoneNumbersMultiple.name=' => null,
                    ],
                    'distinct' => true,
                ],
                'IS NOT NULL' => [
                    'leftJoins' => [['phoneNumbers', 'phoneNumbersMultiple']],
                    'whereClause' => [
                        'phoneNumbersMultiple.name!=' => null,
                    ],
                    'distinct' => true,
                ],
            ],
            'order' => [
                'order' => [
                    ['phoneNumbers.name', '{direction}'],
                ],
                'leftJoins' => [['phoneNumbers', 'phoneNumbers', ['primary' => true]]],
                'additionalSelect' => ['phoneNumbers.name'],
            ],
        ];
    }

    
    private function getIsOptedOutParams(string $foreignJoinAlias, string $foreignJoinMiddleAlias): array
    {
        return [
            'select' => [
                'select' => 'phoneNumbers.optOut',
                'leftJoins' => [['phoneNumbers', 'phoneNumbers', ['primary' => true]]],
            ],
            'selectForeign' => [
                'select' => "$foreignJoinAlias.optOut",
                'leftJoins' => [
                    [
                        'EntityPhoneNumber',
                        $foreignJoinMiddleAlias,
                        [
                            "$foreignJoinMiddleAlias.entityId:" => "{alias}.id",
                            "$foreignJoinMiddleAlias.primary" => true,
                            "$foreignJoinMiddleAlias.deleted" => false,
                        ]
                    ],
                    [
                        PhoneNumber::ENTITY_TYPE,
                        $foreignJoinAlias,
                        [
                            "$foreignJoinAlias.id:" => "$foreignJoinMiddleAlias.phoneNumberId",
                            "$foreignJoinAlias.deleted" => false,
                        ]
                    ]
                ],
            ],
            'where' => [
                '= TRUE' => [
                    'whereClause' => [
                        ['phoneNumbers.optOut=' => true],
                        ['phoneNumbers.optOut!=' => null],
                    ],
                    'leftJoins' => [['phoneNumbers', 'phoneNumbers', ['primary' => true]]],
                ],
                '= FALSE' => [
                    'whereClause' => [
                        'OR' => [
                            ['phoneNumbers.optOut=' => false],
                            ['phoneNumbers.optOut=' => null],
                        ]
                    ],
                    'leftJoins' => [['phoneNumbers', 'phoneNumbers', ['primary' => true]]],
                ]
            ],
            'order' => [
                'order' => [
                    ['phoneNumbers.optOut', '{direction}'],
                ],
                'leftJoins' => [['phoneNumbers', 'phoneNumbers', ['primary' => true]]],
                'additionalSelect' => ['phoneNumbers.optOut'],
            ],
        ];
    }

    
    private function getIsInvalidParams(string $foreignJoinAlias, string $foreignJoinMiddleAlias): array
    {
        return [
            'select' => [
                'select' => 'phoneNumbers.invalid',
                'leftJoins' => [['phoneNumbers', 'phoneNumbers', ['primary' => true]]],
            ],
            'selectForeign' => [
                'select' => "$foreignJoinAlias.invalid",
                'leftJoins' => [
                    [
                        'EntityPhoneNumber',
                        $foreignJoinMiddleAlias,
                        [
                            "$foreignJoinMiddleAlias.entityId:" => "{alias}.id",
                            "$foreignJoinMiddleAlias.primary" => true,
                            "$foreignJoinMiddleAlias.deleted" => false,
                        ]
                    ],
                    [
                        PhoneNumber::ENTITY_TYPE,
                        $foreignJoinAlias,
                        [
                            "$foreignJoinAlias.id:" => "$foreignJoinMiddleAlias.phoneNumberId",
                            "$foreignJoinAlias.deleted" => false,
                        ]
                    ]
                ],
            ],
            'where' => [
                '= TRUE' => [
                    'whereClause' => [
                        ['phoneNumbers.invalid=' => true],
                        ['phoneNumbers.invalid!=' => null],
                    ],
                    'leftJoins' => [['phoneNumbers', 'phoneNumbers', ['primary' => true]]],
                ],
                '= FALSE' => [
                    'whereClause' => [
                        'OR' => [
                            ['phoneNumbers.invalid=' => false],
                            ['phoneNumbers.invalid=' => null],
                        ]
                    ],
                    'leftJoins' => [['phoneNumbers', 'phoneNumbers', ['primary' => true]]],
                ]
            ],
            'order' => [
                'order' => [
                    ['phoneNumbers.invalid', '{direction}'],
                ],
                'leftJoins' => [['phoneNumbers', 'phoneNumbers', ['primary' => true]]],
                'additionalSelect' => ['phoneNumbers.invalid'],
            ],
        ];
    }

    
    private function getNumericParams(string $entityType): array
    {
        return [
            'notExportable' => true,
            'where' => [
                'LIKE' => [
                    'whereClause' => [
                        'id=s' => [
                            'from' => 'EntityPhoneNumber',
                            'select' => ['entityId'],
                            'joins' => [
                                [
                                    'phoneNumber',
                                    'phoneNumber',
                                    [
                                        'phoneNumber.id:' => 'phoneNumberId',
                                        'phoneNumber.deleted' => false,
                                    ],
                                ],
                            ],
                            'whereClause' => [
                                'deleted' => false,
                                'entityType' => $entityType,
                                'phoneNumber.numeric*' => '{value}',
                            ],
                        ],
                    ],
                ],
                'NOT LIKE' => [
                    'whereClause' => [
                        'id!=s' => [
                            'from' => 'EntityPhoneNumber',
                            'select' => ['entityId'],
                            'joins' => [
                                [
                                    'phoneNumber',
                                    'phoneNumber',
                                    [
                                        'phoneNumber.id:' => 'phoneNumberId',
                                        'phoneNumber.deleted' => false,
                                    ],
                                ]
                            ],
                            'whereClause' => [
                                'deleted' => false,
                                'entityType' => $entityType,
                                'phoneNumber.numeric*' => '{value}',
                            ],
                        ],
                    ],
                ],
                '=' => [
                    'leftJoins' => [['phoneNumbers', 'phoneNumbersMultiple']],
                    'whereClause' => [
                        'phoneNumbersMultiple.numeric=' => '{value}',
                    ],
                    'distinct' => true
                ],
                '<>' => [
                    'leftJoins' => [['phoneNumbers', 'phoneNumbersMultiple']],
                    'whereClause' => [
                        'phoneNumbersMultiple.numeric!=' => '{value}',
                    ],
                    'distinct' => true
                ],
                'IN' => [
                    'leftJoins' => [['phoneNumbers', 'phoneNumbersMultiple']],
                    'whereClause' => [
                        'phoneNumbersMultiple.numeric=' => '{value}',
                    ],
                    'distinct' => true
                ],
                'NOT IN' => [
                    'leftJoins' => [['phoneNumbers', 'phoneNumbersMultiple']],
                    'whereClause' => [
                        'phoneNumbersMultiple.numeric!=' => '{value}',
                    ],
                    'distinct' => true
                ],
                'IS NULL' => [
                    'leftJoins' => [['phoneNumbers', 'phoneNumbersMultiple']],
                    'whereClause' => [
                        'phoneNumbersMultiple.numeric=' => null,
                    ],
                    'distinct' => true
                ],
                'IS NOT NULL' => [
                    'leftJoins' => [['phoneNumbers', 'phoneNumbersMultiple']],
                    'whereClause' => [
                        'phoneNumbersMultiple.numeric!=' => null,
                    ],
                    'distinct' => true
                ],
            ],
        ];
    }
}
