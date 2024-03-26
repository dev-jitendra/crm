<?php


namespace Espo\Core\Select\Text\FullTextSearch;

use Espo\Core\Utils\Config;
use Espo\Core\Select\Text\MetadataProvider;
use Espo\Core\Select\Text\FullTextSearch\DataComposer\Params;
use Espo\ORM\Query\Part\Expression\Util as ExpressionUtil;
use Espo\ORM\Query\Part\Expression;

class DefaultDataComposer implements DataComposer
{
    
    private array $functionMap = [
        Mode::BOOLEAN => 'MATCH_BOOLEAN',
        Mode::NATURAL_LANGUAGE => 'MATCH_NATURAL_LANGUAGE',
    ];

    public function __construct(
        private string $entityType,
        private Config $config,
        private MetadataProvider $metadataProvider
    ) {}

    public function compose(string $filter, Params $params): ?Data
    {
        if ($this->config->get('fullTextSearchDisabled')) {
            return null;
        }

        $columnList = $this->metadataProvider->getFullTextSearchColumnList($this->entityType) ?? [];

        if (!count($columnList)) {
            return null;
        }

        $fieldList = [];

        foreach ($this->getTextFilterFieldList() as $field) {
            if (str_contains($field, '.')) {
                continue;
            }

            if ($this->metadataProvider->isFieldNotStorable($this->entityType, $field)) {
                continue;
            }

            if (!$this->metadataProvider->isFullTextSearchSupportedForField($this->entityType, $field)) {
                continue;
            }

            $fieldList[] = $field;
        }

        if (!count($fieldList)) {
            return null;
        }

        $preparedFilter = $this->prepareFilter($filter, $params);

        $mode = Mode::BOOLEAN;

        if (
            mb_strpos($preparedFilter, ' ') === false &&
            mb_strpos($preparedFilter, '+') === false &&
            mb_strpos($preparedFilter, '-') === false &&
            mb_strpos($preparedFilter, '*') === false
        ) {
            $mode = Mode::NATURAL_LANGUAGE;
        }

        if ($mode === Mode::BOOLEAN) {
            $preparedFilter = str_replace('@', '*', $preparedFilter);
        }

        $argumentList = array_merge(
            array_map(fn ($item) => Expression::column($item), $columnList),
            [$preparedFilter]
        );

        $function = $this->functionMap[$mode];

        $expression = ExpressionUtil::composeFunction($function, ...$argumentList);

        return new Data(
            $expression,
            $fieldList,
            $columnList,
            $mode
        );
    }

    private function prepareFilter(string $filter, Params $params): string
    {
        $filter = str_replace('%', '*', $filter);
        $filter = str_replace(['(', ')'], '', $filter);
        $filter = str_replace('"*', '"', $filter);
        $filter = str_replace('*"', '"', $filter);

        while (str_contains($filter, '**')) {
            $filter = trim(
                str_replace('**', '*', $filter)
            );
        }

        while (mb_substr($filter, -2) === ' *') {
            $filter = trim(
                mb_substr($filter, 0, mb_strlen($filter) - 2)
            );
        }

        $filter = str_replace(['+-', '--', '-+', '++', '+*', '-*'], '', $filter);

        while (str_contains($filter, '+ ')) {
            $filter = str_replace('+ ', '', $filter);
        }

        while (str_contains($filter, '- ')) {
            $filter = str_replace('- ', '', $filter);
        }

        while (in_array(substr($filter, -1), ['-', '+'])) {
            $filter = substr($filter, 0, -1);
        }

        if ($filter === '*') {
            $filter = '';
        }

        return $filter;
    }

    
    private function getTextFilterFieldList(): array
    {
        return $this->metadataProvider->getTextFilterAttributeList($this->entityType) ?? ['name'];
    }
}
