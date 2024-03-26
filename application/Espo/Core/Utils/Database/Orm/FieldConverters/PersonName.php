<?php


namespace Espo\Core\Utils\Database\Orm\FieldConverters;

use Espo\Core\Utils\Config;
use Espo\Core\Utils\Database\Orm\Defs\AttributeDefs;
use Espo\Core\Utils\Database\Orm\Defs\EntityDefs;
use Espo\Core\Utils\Database\Orm\FieldConverter;
use Espo\ORM\Defs\FieldDefs;
use Espo\ORM\Type\AttributeType;

class PersonName implements FieldConverter
{
    private const FORMAT_LAST_FIRST = 'lastFirst';
    private const FORMAT_LAST_FIRST_MIDDLE = 'lastFirstMiddle';
    private const FORMAT_FIRST_MIDDLE_LAST = 'firstMiddleLast';

    public function __construct(private Config $config) {}

    public function convert(FieldDefs $fieldDefs, string $entityType): EntityDefs
    {
        $format = $this->config->get('personNameFormat');

        $name = $fieldDefs->getName();
        $firstName = 'first' . ucfirst($name);
        $lastName = 'last' . ucfirst($name);
        $middleName = 'middle' . ucfirst($name);

        $subList = match ($format) {
            self::FORMAT_LAST_FIRST => [$lastName, ' ', $firstName],
            self::FORMAT_LAST_FIRST_MIDDLE => [$lastName, ' ', $firstName, ' ', $middleName],
            self::FORMAT_FIRST_MIDDLE_LAST => [$firstName, ' ', $middleName, ' ', $lastName],
            default => [$firstName, ' ', $lastName],
        };

        if (
            $format === self::FORMAT_LAST_FIRST_MIDDLE ||
            $format === self::FORMAT_LAST_FIRST
        ) {
            $orderBy1Field = $lastName;
            $orderBy2Field = $firstName;
        } else {
            $orderBy1Field = $firstName;
            $orderBy2Field = $lastName;
        }

        $fullList = [];
        $whereItems = [];

        foreach ($subList as $subFieldName) {
            $fieldNameTrimmed = trim($subFieldName);

            if (empty($fieldNameTrimmed)) {
                $fullList[] = "'" . $subFieldName . "'";

                continue;
            }

            $fullList[] = $fieldNameTrimmed;
            $whereItems[] = $fieldNameTrimmed;
        }

        $whereItems[] = "CONCAT:({$firstName}, ' ', {$lastName})";
        $whereItems[] = "CONCAT:({$lastName}, ' ', {$firstName})";

        if ($format === 'firstMiddleLast') {
            $whereItems[] = "CONCAT:({$firstName}, ' ', {$middleName}, ' ', {$lastName})";
        } else
            if ($format === 'lastFirstMiddle') {
                $whereItems[] = "CONCAT:({$lastName}, ' ', {$firstName}, ' ', {$middleName})";
            }

        $selectExpression = $this->getSelect($fullList);
        $selectForeignExpression = $this->getSelect($fullList, '{alias}');

        if (
            $format === self::FORMAT_FIRST_MIDDLE_LAST ||
            $format === self::FORMAT_LAST_FIRST_MIDDLE
        ) {
            $selectExpression = "REPLACE:({$selectExpression}, '  ', ' ')";
            $selectForeignExpression = "REPLACE:({$selectForeignExpression}, '  ', ' ')";
        }

        $attributeDefs = AttributeDefs::create($name)
            ->withType(AttributeType::VARCHAR)
            ->withNotStorable()
            ->withParamsMerged([
                'select' => [
                    'select' => $selectExpression,
                ],
                'selectForeign' => [
                    'select' => $selectForeignExpression,
                ],
                'where' => [
                    'LIKE' => [
                        'whereClause' => [
                            'OR' => array_fill_keys(
                                array_map(fn ($item) => $item . '*', $whereItems),
                                '{value}'
                            ),
                        ],
                    ],
                    'NOT LIKE' => [
                        'whereClause' => [
                            'AND' => array_fill_keys(
                                array_map(fn ($item) => $item . '!*', $whereItems),
                                '{value}'
                            ),
                        ],
                    ],
                    '=' => [
                        'whereClause' => [
                            'OR' => array_fill_keys($whereItems, '{value}'),
                        ],
                    ],
                ],
                'order' => [
                    'order' => [
                        [$orderBy1Field, '{direction}'],
                        [$orderBy2Field, '{direction}'],
                    ],
                ],
            ]);

        $dependeeAttributeList = $fieldDefs->getParam('dependeeAttributeList');

        if ($dependeeAttributeList) {
            $attributeDefs = $attributeDefs->withParam('dependeeAttributeList', $dependeeAttributeList);
        }

        return EntityDefs::create()
            ->withAttribute($attributeDefs);
    }

    
    private function getSelect(array $fullList, ?string $alias = null): string
    {
        foreach ($fullList as &$item) {
            $rowItem = trim($item, " '");

            if (empty($rowItem)) {
                continue;
            }

            if ($alias) {
                $item = $alias . '.' . $item;
            }

            $item = "IFNULL:({$item}, '')";
        }

        return "NULLIF:(TRIM:(CONCAT:(" . implode(", ", $fullList) . ")), '')";
    }
}
