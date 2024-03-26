<?php


namespace Espo\Core\Mail\Account;

use Espo\Core\Field\Date;
use Espo\Core\Field\Link;
use Espo\Core\Field\LinkMultiple;
use Espo\Core\Mail\SmtpParams;
use Espo\Entities\Email;

interface Account
{
    
    public function updateFetchData(FetchData $fetchData): void;

    
    public function relateEmail(Email $email): void;

    
    public function getPortionLimit(): int;

    
    public function isAvailableForFetching(): bool;

    
    public function getEmailAddress(): ?string;

    
    public function getAssignedUser(): ?Link;

    
    public function getUser(): ?Link;

    
    public function getUsers(): LinkMultiple;

    
    public function getTeams(): LinkMultiple;

    
    public function keepFetchedEmailsUnread(): bool;

    
    public function getFetchData(): FetchData;

    
    public function getFetchSince(): ?Date;

    
    public function getEmailFolder(): ?Link;

    
    public function getGroupEmailFolder(): ?Link;

    
    public function getMonitoredFolderList(): array;

    
    public function getId(): ?string;

    
    public function getEntityType(): string;

    
    public function getImapParams(): ?ImapParams;

    
    public function getImapHandlerClassName(): ?string;

    
    public function getSentFolder(): ?string;

    
    public function isAvailableForSending(): bool;

    
    public function getSmtpParams(): ?SmtpParams;

    
    public function storeSentEmails(): bool;
}
