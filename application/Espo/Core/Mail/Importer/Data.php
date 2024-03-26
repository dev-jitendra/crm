<?php


namespace Espo\Core\Mail\Importer;

use Espo\Entities\EmailFilter;


class Data
{
    private ?string $assignedUserId = null;
    
    private array $teamIdList = [];
    
    private array $userIdList = [];
    
    private iterable $filterList = [];
    private bool $fetchOnlyHeader = false;
    
    private array $folderData = [];
    private ?string $groupEmailFolderId = null;

    public static function create(): self
    {
        return new self();
    }

    public function getAssignedUserId(): ?string
    {
        return $this->assignedUserId;
    }

    
    public function getTeamIdList(): array
    {
        return $this->teamIdList;
    }

    
    public function getUserIdList(): array
    {
        return $this->userIdList;
    }

    
    public function getFilterList(): iterable
    {
        return $this->filterList;
    }

    public function fetchOnlyHeader(): bool
    {
        return $this->fetchOnlyHeader;
    }

    
    public function getFolderData(): array
    {
        return $this->folderData;
    }

    public function getGroupEmailFolderId(): ?string
    {
        return $this->groupEmailFolderId;
    }

    public function withAssignedUserId(?string $assignedUserId): self
    {
        $obj = clone $this;

        $obj->assignedUserId = $assignedUserId;

        return $obj;
    }

    
    public function withTeamIdList(array $teamIdList): self
    {
        $obj = clone $this;

        $obj->teamIdList = $teamIdList;

        return $obj;
    }

    
    public function withUserIdList(array $userIdList): self
    {
        $obj = clone $this;
        $obj->userIdList = $userIdList;

        return $obj;
    }

    
    public function withFilterList(iterable $filterList): self
    {
        $obj = clone $this;
        $obj->filterList = $filterList;

        return $obj;
    }

    public function withFetchOnlyHeader(bool $fetchOnlyHeader = true): self
    {
        $obj = clone $this;
        $obj->fetchOnlyHeader = $fetchOnlyHeader;

        return $obj;
    }

    
    public function withFolderData(array $folderData): self
    {
        $obj = clone $this;
        $obj->folderData = $folderData;

        return $obj;
    }

    public function withGroupEmailFolderId(?string $groupEmailFolderId): self
    {
        $obj = clone $this;
        $obj->groupEmailFolderId = $groupEmailFolderId;

        return $obj;
    }
}
