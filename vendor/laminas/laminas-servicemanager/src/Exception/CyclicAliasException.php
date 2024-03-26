<?php

declare(strict_types=1);

namespace Laminas\ServiceManager\Exception;

use function array_filter;
use function array_keys;
use function array_map;
use function array_values;
use function implode;
use function reset;
use function serialize;
use function sort;
use function sprintf;

class CyclicAliasException extends InvalidArgumentException
{
    
    public static function fromCyclicAlias(string $alias, array $aliases): self
    {
        $cycle  = $alias;
        $cursor = $alias;
        while (isset($aliases[$cursor]) && $aliases[$cursor] !== $alias) {
            $cursor = $aliases[$cursor];
            $cycle .= ' -> ' . $cursor;
        }
        $cycle .= ' -> ' . $alias . "\n";

        return new self(sprintf(
            "A cycle was detected within the aliases definitions:\n%s",
            $cycle
        ));
    }

    
    public static function fromAliasesMap(array $aliases)
    {
        $detectedCycles = array_filter(array_map(
            static fn($alias): ?array => self::getCycleFor($aliases, $alias),
            array_keys($aliases)
        ));

        if (! $detectedCycles) {
            return new self(sprintf(
                "A cycle was detected within the following aliases map:\n\n%s",
                self::printReferencesMap($aliases)
            ));
        }

        return new self(sprintf(
            "Cycles were detected within the provided aliases:\n\n%s\n\n"
            . "The cycle was detected in the following alias map:\n\n%s",
            self::printCycles(self::deDuplicateDetectedCycles($detectedCycles)),
            self::printReferencesMap($aliases)
        ));
    }

    
    private static function getCycleFor(array $aliases, $alias)
    {
        $cycleCandidate = [];
        $targetName     = $alias;

        while (isset($aliases[$targetName])) {
            if (isset($cycleCandidate[$targetName])) {
                return $cycleCandidate;
            }

            $cycleCandidate[$targetName] = true;
            $targetName                  = $aliases[$targetName];
        }

        return null;
    }

    
    private static function printReferencesMap(array $aliases)
    {
        $map = [];

        foreach ($aliases as $alias => $reference) {
            $map[] = '"' . $alias . '" => "' . $reference . '"';
        }

        return "[\n" . implode("\n", $map) . "\n]";
    }

    
    private static function printCycles(array $detectedCycles)
    {
        return "[\n" . implode("\n", array_map([self::class, 'printCycle'], $detectedCycles)) . "\n]";
    }

    
    private static function printCycle(array $detectedCycle)
    {
        $fullCycle   = array_keys($detectedCycle);
        $fullCycle[] = reset($fullCycle);

        return implode(
            ' => ',
            array_map(
                static fn($cycle): string => '"' . $cycle . '"',
                $fullCycle
            )
        );
    }

    
    private static function deDuplicateDetectedCycles(array $detectedCycles)
    {
        $detectedCyclesByHash = [];

        foreach ($detectedCycles as $detectedCycle) {
            $cycleAliases = array_keys($detectedCycle);

            sort($cycleAliases);

            $hash = serialize($cycleAliases);

            $detectedCyclesByHash[$hash] ??= $detectedCycle;
        }

        return array_values($detectedCyclesByHash);
    }
}
