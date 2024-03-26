<?php declare(strict_types=1);



namespace Monolog\Handler;

use Monolog\Level;


class SendGridHandler extends MailHandler
{
    
    protected string $apiUser;

    
    protected string $apiKey;

    
    protected string $from;

    
    protected array $to;

    
    protected string $subject;

    
    public function __construct(string $apiUser, string $apiKey, string $from, string|array $to, string $subject, int|string|Level $level = Level::Error, bool $bubble = true)
    {
        if (!extension_loaded('curl')) {
            throw new MissingExtensionException('The curl extension is needed to use the SendGridHandler');
        }

        parent::__construct($level, $bubble);
        $this->apiUser = $apiUser;
        $this->apiKey = $apiKey;
        $this->from = $from;
        $this->to = (array) $to;
        $this->subject = $subject;
    }

    
    protected function send(string $content, array $records): void
    {
        $message = [];
        $message['api_user'] = $this->apiUser;
        $message['api_key'] = $this->apiKey;
        $message['from'] = $this->from;
        foreach ($this->to as $recipient) {
            $message['to[]'] = $recipient;
        }
        $message['subject'] = $this->subject;
        $message['date'] = date('r');

        if ($this->isHtmlBody($content)) {
            $message['html'] = $content;
        } else {
            $message['text'] = $content;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https:
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($message));
        Curl\Util::execute($ch, 2);
    }
}
