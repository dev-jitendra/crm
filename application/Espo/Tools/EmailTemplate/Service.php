<?php


namespace Espo\Tools\EmailTemplate;

use Espo\Core\Acl;
use Espo\Core\Exceptions\ForbiddenSilent;
use Espo\Core\Exceptions\NotFound;
use Espo\Entities\EmailTemplate;
use Espo\Entities\User;
use Espo\ORM\EntityManager;

class Service
{
    public function __construct(
        private Processor $processor,
        private User $user,
        private Acl $acl,
        private EntityManager $entityManager
    ) {}

    
    public function process(string $emailTemplateId, Data $data, ?Params $params = null): Result
    {
        
        $emailTemplate = $this->entityManager->getEntityById(EmailTemplate::ENTITY_TYPE, $emailTemplateId);

        if (!$emailTemplate) {
            throw new NotFound();
        }

        $params ??= Params::create()
            ->withApplyAcl(true)
            ->withCopyAttachments(true);

        if (
            $params->applyAcl() &&
            !$this->acl->checkEntityRead($emailTemplate)
        ) {
            throw new ForbiddenSilent();
        }

        if (!$data->getUser()) {
            $data = $data->withUser($this->user);
        }

        return $this->processor->process($emailTemplate, $params, $data);
    }
}
