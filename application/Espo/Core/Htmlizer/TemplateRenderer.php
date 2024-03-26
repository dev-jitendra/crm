<?php


namespace Espo\Core\Htmlizer;

use Espo\Core\ApplicationState;
use Espo\ORM\Entity;
use Espo\Entities\User;

use stdClass;
use InvalidArgumentException;
use LogicException;

class TemplateRenderer
{
    
    private $data = null;
    private ?User $user = null;
    private ?Entity $entity = null;
    private bool $skipRelations = false;
    private bool $skipInlineAttachmentHandling = false;
    private bool $applyAcl = false;
    private bool $useUserTimezone = false;
    private HtmlizerFactory $htmlizerFactory;
    private ApplicationState $applicationState;
    private ?string $template = null;

    public function __construct(HtmlizerFactory $htmlizerFactory, ApplicationState $applicationState)
    {
        $this->htmlizerFactory = $htmlizerFactory;
        $this->applicationState = $applicationState;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function setEntity(Entity $entity): self
    {
        $this->entity = $entity;

        return $this;
    }

    
    public function setData($data): self
    {
        

        if (!is_array($data) && !$data instanceof stdClass) {
            throw new InvalidArgumentException();
        }

        if (is_object($data)) {
            $data = get_object_vars($data);
        }

        $this->data = $data;

        return $this;
    }

    public function setSkipRelations(bool $skipRelations = true): self
    {
        $this->skipRelations = $skipRelations;

        return $this;
    }

    public function setSkipInlineAttachmentHandling(bool $skipInlineAttachmentHandling = true): self
    {
        $this->skipInlineAttachmentHandling = $skipInlineAttachmentHandling;

        return $this;
    }
    public function setApplyAcl(bool $applyAcl = true): self
    {
        $this->applyAcl = $applyAcl;

        return $this;
    }

    public function setUseUserTimezone(bool $useUserTimezone = true): self
    {
        $this->useUserTimezone = $useUserTimezone;

        return $this;
    }

    public function setTemplate(string $template): self
    {
        $this->template = $template;

        return $this;
    }

    public function render(): string
    {
        if (!$this->template) {
            throw new LogicException("No template.");
        }

        return $this->renderTemplate($this->template);
    }

    public function renderTemplate(string $template): string
    {
        return $this->renderTemplateInternal($template, $this->createHtmlizer());
    }

    private function renderTemplateInternal(string $template, Htmlizer $htmlizer): string
    {
        return $htmlizer->render(
            $this->entity,
            $template,
            null,
            $this->data,
            $this->skipRelations,
            $this->skipInlineAttachmentHandling
        );
    }

    
    public function renderMultipleTemplates(string ...$templateList): array
    {
        $htmlizer = $this->createHtmlizer();

        $resultList = [];

        foreach ($templateList as $template) {
            $resultList[] = $this->renderTemplateInternal($template, $htmlizer);
        }

        return $resultList;
    }

    private function createHtmlizer(): Htmlizer
    {
        $user = $this->user ?? $this->applicationState->getUser();

        $params = new CreateForUserParams();

        $params->applyAcl = $this->applyAcl;
        $params->useUserTimezone = $this->useUserTimezone;

        return $this->htmlizerFactory->createForUser($user, $params);
    }
}
