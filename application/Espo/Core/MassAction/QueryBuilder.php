<?php


namespace Espo\Core\MassAction;

use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Exceptions\Error;
use Espo\Core\Exceptions\Forbidden;
use Espo\ORM\Query\Select;
use Espo\Core\Select\SelectBuilderFactory;
use Espo\Entities\User;

class QueryBuilder
{
    public function __construct(private SelectBuilderFactory $selectBuilderFactory, private User $user)
    {}

    
    public function build(Params $params): Select
    {
        $builder = $this->selectBuilderFactory
            ->create()
            ->from($params->getEntityType())
            ->forUser($this->user)
            ->withStrictAccessControl();

        if ($params->hasIds()) {
            return $builder
                ->buildQueryBuilder()
                ->where([
                    'id' => $params->getIds(),
                ])
                ->build();
        }

        return $builder
            ->withSearchParams($params->getSearchParams())
            ->build();
    }
}
