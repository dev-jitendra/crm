<?php


namespace Espo\Core\Mail\Account;

use Espo\Core\Field\Date;
use Espo\Core\Field\Link;
use Espo\Core\Field\LinkMultiple;
use Espo\Core\Mail\Exceptions\NoSmtp;
use Espo\Core\Mail\SmtpParams;
use Espo\Core\Utils\Config;
use Espo\Entities\Email;
use Espo\Entities\Settings;

class SystemSettingsAccount implements Account
{
    public function __construct(private Config $config)
    {}

    public function updateFetchData(FetchData $fetchData): void {}

    public function relateEmail(Email $email): void {}

    public function getPortionLimit(): int
    {
        return 0;
    }

    public function isAvailableForFetching(): bool
    {
        return false;
    }

    public function getEmailAddress(): ?string
    {
        return $this->config->get('outboundEmailFromAddress');
    }

    public function getAssignedUser(): ?Link
    {
        return null;
    }

    public function getUser(): ?Link
    {
        return null;
    }

    public function getUsers(): LinkMultiple
    {
        return LinkMultiple::create();
    }

    public function getTeams(): LinkMultiple
    {
        return LinkMultiple::create();
    }

    public function keepFetchedEmailsUnread(): bool
    {
        return false;
    }

    public function getFetchData(): FetchData
    {
        return FetchData::fromRaw((object) []);
    }

    public function getFetchSince(): ?Date
    {
        return null;
    }

    public function getEmailFolder(): ?Link
    {
        return null;
    }

    public function getGroupEmailFolder(): ?Link
    {
        return null;
    }

    public function getMonitoredFolderList(): array
    {
        return [];
    }

    public function getId(): ?string
    {
        return null;
    }

    public function getEntityType(): string
    {
        return Settings::ENTITY_TYPE;
    }

    public function getHost(): ?string
    {
        return null;
    }

    public function getPort(): ?int
    {
        return null;
    }

    public function getUsername(): ?string
    {
        return null;
    }

    public function getPassword(): ?string
    {
        return null;
    }

    public function getSecurity(): ?string
    {
        return null;
    }

    
    public function getImapHandlerClassName(): ?string
    {
        return null;
    }

    public function getSentFolder(): ?string
    {
        return null;
    }

    public function isAvailableForSending(): bool
    {
        return (bool) $this->config->get('smtpServer');
    }

    public function storeSentEmails(): bool
    {
        return false;
    }

    
    public function getSmtpParams(): ?SmtpParams
    {
        $host = $this->config->get('smtpServer');
        $port = $this->config->get('smtpPort');

        if (!$host) {
            throw new NoSmtp("No system SMTP settings.");
        }

        if (!$port) {
            throw new NoSmtp("No system SMTP port.");
        }

        $params = SmtpParams::create($host, $port)
            ->withSecurity($this->config->get('smtpSecurity'))
            ->withAuth($this->config->get('smtpAuth'));

        if ($params->useAuth()) {
            $password = $this->config->get('smtpPassword');

            $params = $params
                ->withUsername($this->config->get('smtpUsername'))
                ->withPassword($password)
                ->withAuthMechanism($this->config->get('smtpAuthMechanism') ?? 'login');
        }

        return $params;
    }

    public function getImapParams(): ?ImapParams
    {
        return null;
    }
}
