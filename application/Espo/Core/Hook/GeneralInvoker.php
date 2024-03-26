<?php


namespace Espo\Core\Hook;

use Espo\Core\Hook\Hook\AfterMassRelate;
use Espo\Core\Hook\Hook\AfterRelate;
use Espo\Core\Hook\Hook\AfterRemove;
use Espo\Core\Hook\Hook\AfterSave;
use Espo\Core\Hook\Hook\AfterUnrelate;
use Espo\Core\Hook\Hook\BeforeRemove;
use Espo\Core\Hook\Hook\BeforeSave;
use Espo\ORM\Entity;
use Espo\ORM\Query\Select;
use Espo\ORM\Repository\Option\MassRelateOptions;
use Espo\ORM\Repository\Option\RelateOptions;
use Espo\ORM\Repository\Option\RemoveOptions;
use Espo\ORM\Repository\Option\SaveOptions;
use Espo\ORM\Repository\Option\UnrelateOptions;
use LogicException;


class GeneralInvoker
{
    private const HOOK_BEFORE_SAVE = 'beforeSave';
    private const HOOK_AFTER_SAVE = 'afterSave';
    private const HOOK_BEFORE_REMOVE = 'beforeRemove';
    private const HOOK_AFTER_REMOVE = 'afterRemove';
    private const HOOK_AFTER_RELATE = 'afterRelate';
    private const HOOK_AFTER_UNRELATE = 'afterUnrelate';
    private const HOOK_AFTER_MASS_RELATE = 'afterMassRelate';

    
    public function invoke(
        object $hook,
        string $name,
        mixed $subject,
        array $options,
        array $hookData
    ): void {

        if ($name === self::HOOK_BEFORE_SAVE && $hook instanceof BeforeSave) {
            if (!$subject instanceof Entity) {
                throw new LogicException();
            }

            $hook->beforeSave($subject, SaveOptions::fromAssoc($options));

            return;
        }

        if ($name === self::HOOK_AFTER_SAVE && $hook instanceof AfterSave) {
            if (!$subject instanceof Entity) {
                throw new LogicException();
            }

            $hook->afterSave($subject, SaveOptions::fromAssoc($options));

            return;
        }

        if ($name === self::HOOK_BEFORE_REMOVE &&  $hook instanceof BeforeRemove) {
            if (!$subject instanceof Entity) {
                throw new LogicException();
            }

            $hook->beforeRemove($subject, RemoveOptions::fromAssoc($options));

            return;
        }

        if ($name === self::HOOK_AFTER_REMOVE && $hook instanceof AfterRemove) {
            if (!$subject instanceof Entity) {
                throw new LogicException();
            }

            $hook->afterRemove($subject, RemoveOptions::fromAssoc($options));

            return;
        }

        if ($name === self::HOOK_AFTER_RELATE && $hook instanceof AfterRelate) {
            $relationName = $hookData['relationName'] ?? null;
            $relatedEntity = $hookData['foreignEntity'] ?? null;
            $columnData = $hookData['relationData'] ?? [];

            if (
                !$subject instanceof Entity ||
                !is_string($relationName) ||
                !$relatedEntity instanceof Entity
            ) {
                throw new LogicException();
            }

            $hook->afterRelate(
                $subject,
                $relationName,
                $relatedEntity,
                $columnData,
                RelateOptions::fromAssoc($options)
            );

            return;
        }

        if ($name === self::HOOK_AFTER_UNRELATE && $hook instanceof AfterUnrelate) {
            $relationName = $hookData['relationName'] ?? null;
            $relatedEntity = $hookData['foreignEntity'] ?? null;

            if (
                !$subject instanceof Entity ||
                !is_string($relationName) ||
                !$relatedEntity instanceof Entity
            ) {
                throw new LogicException();
            }

            $hook->afterUnrelate(
                $subject,
                $relationName,
                $relatedEntity,
                UnrelateOptions::fromAssoc($options)
            );

            return;
        }

        if ($name === self::HOOK_AFTER_MASS_RELATE && $hook instanceof AfterMassRelate) {
            $relationName = $hookData['relationName'] ?? null;
            $query = $hookData['query'] ?? null;
            $columnData = $hookData['relationData'] ?? []; 

            if (
                !$subject instanceof Entity ||
                !is_string($relationName) ||
                !$query instanceof Select
            ) {
                throw new LogicException();
            }

            $hook->afterMassRelate(
                $subject,
                $relationName,
                $query,
                $columnData,
                MassRelateOptions::fromAssoc($options)
            );

            return;
        }

        $hook->$name($subject, $options, $hookData);
    }
}
