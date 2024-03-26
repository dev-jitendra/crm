<?php


return [
    'EmailTemplate' => [
        [
            'name' => 'Case-to-Email auto-reply',
            'subject' => 'Case has been created',
            'body' => '<p>{Person.name},</p><p>Case \'{Case.name}\' has been created with number '.
                '{Case.number} and assigned to {User.name}.</p>',
            'isHtml ' => '1',
        ]
    ],
    'ScheduledJob' => [
        [
            'name' => 'Check Group Email Accounts',
            'job' => 'CheckInboundEmails',
            'status' => 'Active',
            'scheduling' => '*/2 * * * *',
        ],
        [
            'name' => 'Check Personal Email Accounts',
            'job' => 'CheckEmailAccounts',
            'status' => 'Active',
            'scheduling' => '*/1 * * * *',
        ],
        [
            'name' => 'Send Email Reminders',
            'job' => 'SendEmailReminders',
            'status' => 'Active',
            'scheduling' => '*/2 * * * *',
        ],
        [
            'name' => 'Send Email Notifications',
            'job' => 'SendEmailNotifications',
            'status' => 'Active',
            'scheduling' => '*/2 * * * *',
        ],
        [
            'name' => 'Clean-up',
            'job' => 'Cleanup',
            'status' => 'Active',
            'scheduling' => '1 1 * * 0',
        ],
        [
            'name' => 'Send Mass Emails',
            'job' => 'ProcessMassEmail',
            'status' => 'Active',
            'scheduling' => '10,30,50 * * * *',
        ],
        [
            'name' => 'Auth Token Control',
            'job' => 'AuthTokenControl',
            'status' => 'Active',
            'scheduling' => '*/6 * * * *',
        ],
        [
            'name' => 'Control Knowledge Base Article Status',
            'job' => 'ControlKnowledgeBaseArticleStatus',
            'status' => 'Active',
            'scheduling' => '10 1 * * *',
        ],
        [
            'name' => 'Process Webhook Queue',
            'job' => 'ProcessWebhookQueue',
            'status' => 'Active',
            'scheduling' => '*/5 * * * *',
        ],
    ],
];
