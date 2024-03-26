<?php


namespace Espo\Classes\FieldConverters;

use Espo\Core\Utils\Database\Orm\Defs\AttributeDefs;
use Espo\Core\Utils\Database\Orm\Defs\EntityDefs;
use Espo\Core\Utils\Database\Orm\FieldConverter;
use Espo\ORM\Defs\FieldDefs;
use Espo\ORM\Type\AttributeType;
use RuntimeException;

class RelationshipRole implements FieldConverter
{
    public function convert(FieldDefs $fieldDefs, string $entityType): EntityDefs
    {
        $name = $fieldDefs->getName();

        $attributeDefs = AttributeDefs::create($name)
            ->withType(AttributeType::VARCHAR)
            ->withNotStorable();

        $attributeDefs = $this->addWhere($attributeDefs, $fieldDefs, $entityType);

        return EntityDefs::create()
            ->withAttribute($attributeDefs);
    }

    private function addWhere(AttributeDefs $attributeDefs, FieldDefs $fieldDefs, string $entityType): AttributeDefs
    {
        $data = $fieldDefs->getParam('converterData');

        if (!is_array($data)) {
            throw new RuntimeException("No `converterData` in field defs.");
        }

        
        $column = $data['column'] ?? null;
        
        $link = $data['link'] ?? null;
        
        $relationName = $data['relationName'] ?? null;
        
        $nearKey = $data['nearKey'] ?? null;

        if (!$column || !$link || !$relationName || !$nearKey) {
            throw new RuntimeException("Bad `converterData`.");
        }

        $midTable = ucfirst($relationName);

        return $attributeDefs->withParamsMerged([
            'where' => [
                '=' => [
                    'whereClause' => [
                        'id=s' => [
                            'from' => $midTable,
                            'select' => [$nearKey],
                            'whereClause' => [
                                'deleted' => false,
                                $column => '{value}',
                            ],
                        ],
                    ],
                ],
                '<>' => [
                    'whereClause' => [
                        'id!=s' => [
                            'from' => $midTable,
                            'select' => [$nearKey],
                            'whereClause' => [
                                'deleted' => false,
                                $column => '{value}',
                            ],
                        ],
                    ],
                ],
                'IN' => [
                    'whereClause' => [
                        'id=s' => [
                            'from' => $midTable,
                            'select' => [$nearKey],
                            'whereClause' => [
                                'deleted' => false,
                                $column => '{value}',
                            ],
                        ],
                    ],
                ],
                'NOT IN' => [
                    'whereClause' => [
                        'id!=s' => [
                            'from' => $midTable,
                            'select' => [$nearKey],
                            'whereClause' => [
                                'deleted' => false,
                                $column => '{value}',
                            ],
                        ],
                    ],
                ],
                'LIKE' => [
                    'whereClause' => [
                        'id=s' => [
                            'from' => $midTable,
                            'select' => [$nearKey],
                            'whereClause' => [
                                'deleted' => false,
                                "$column*" => '{value}',
                            ],
                        ],
                    ],
                ],
                'NOT LIKE' => [
                    'whereClause' => [
                        'id!=s' => [
                            'from' => $midTable,
                            'select' => [$nearKey],
                            'whereClause' => [
                                'deleted' => false,
                                "$column*" => '{value}',
                            ],
                        ],
                    ],
                ],
                'IS NULL' => [
                    'whereClause' => [
                        'NOT' => [
                            'EXISTS' => [
                                'from' => $entityType,
                                'fromAlias' => 'sq',
                                'select' => ['id'],
                                'leftJoins' => [
                                    [
                                        $link,
                                        'm',
                                        null,
                                        ['onlyMiddle' => true]
                                    ]
                                ],
                                'whereClause' => [
                                    "m.$column!=" => null,
                                    'sq.id:' => lcfirst($entityType) . '.id',
                                ],
                            ],
                        ],
                    ],
                ],
                'IS NOT NULL' => [
                    'whereClause' => [
                        'EXISTS' => [
                            'from' => $entityType,
                            'fromAlias' => 'sq',
                            'select' => ['id'],
                            'leftJoins' => [
                                [
                                    $link,
                                    'm',
                                    null,
                                    ['onlyMiddle' => true]
                                ]
                            ],
                            'whereClause' => [
                                "m.$column!=" => null,
                                'sq.id:' => lcfirst($entityType) . '.id',
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }
}
