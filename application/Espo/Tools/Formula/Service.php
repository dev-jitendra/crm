<?php


namespace Espo\Tools\Formula;

use Espo\Core\Formula\Parser;
use Espo\Core\Formula\Exceptions\SyntaxError;
use Espo\Core\Formula\Exceptions\Error;
use Espo\Core\Formula\Manager;
use Espo\Core\Field\LinkParent;

use Espo\Core\Exceptions\NotFoundSilent;

use Espo\ORM\EntityManager;
use Espo\ORM\Entity;

class Service
{
    private Parser $parser;

    private Manager $manager;

    private EntityManager $entityManager;

    public function __construct(Parser $parser, Manager $manager, EntityManager $entityManager)
    {
        $this->parser = $parser;
        $this->manager = $manager;
        $this->entityManager = $entityManager;
    }

    public function checkSyntax(string $expression): SyntaxCheckResult
    {
        try {
            $this->parser->parse($expression);

            $result = SyntaxCheckResult::createSuccess();
        }
        catch (SyntaxError $e) {
            return SyntaxCheckResult::createError($e);
        }

        return $result;
    }

    public function run(string $expression, ?LinkParent $targetLink = null): RunResult
    {
        $syntaxCheckResult = $this->checkSyntax($expression);

        if (!$syntaxCheckResult->isSuccess()) {
            
            $exception = $syntaxCheckResult->getException();

            return RunResult::createSyntaxError($exception);
        }

        $target = null;

        if ($targetLink) {
            $target = $this->entityManager->getEntityById($targetLink->getEntityType(), $targetLink->getId());

            if (!$target) {
                throw new NotFoundSilent("Target entity not found.");
            }
        }

        $variables = (object) [];

        try {
            $this->manager->run($expression, $target, $variables);
        }
        catch (Error $e) {
            $output = $variables->__output ?? null;

            return RunResult::createError($e, $output);
        }

        if ($target && $this->isEntityChanged($target)) {
            $this->entityManager->saveEntity($target);
        }

        $output = $variables->__output ?? null;

        return RunResult::createSuccess($output);
    }

    private function isEntityChanged(Entity $entity): bool
    {
        foreach ($entity->getAttributeList() as $attribute) {
            if ($entity->isAttributeChanged($attribute)) {
                return true;
            }
        }

        return false;
    }
}
