<?php

namespace ZBateson\MailMimeParser\Header;

abstract class HeaderConsts
{
    
    const RETURN_PATH = 'Return-Path';
    const RECEIVED = 'Received';
    const RESENT_DATE = 'Resent-Date';
    const RESENT_FROM = 'Resent-From';
    const RESENT_SENDER = 'Resent-Sender';
    const RESENT_TO = 'Resent-To';
    const RESENT_CC = 'Resent-Cc';
    const RESENT_BCC = 'Resent-Bcc';
    const RESENT_MSD_ID = 'Resent-Message-ID';
    const RESENT_MESSAGE_ID = self::RESENT_MSD_ID;
    const ORIG_DATE = 'Date';
    const DATE = self::ORIG_DATE;
    const FROM = 'From';
    const SENDER = 'Sender';
    const REPLY_TO = 'Reply-To';
    const TO = 'To';
    const CC = 'Cc';
    const BCC = 'Bcc';
    const MESSAGE_ID = 'Message-ID';
    const IN_REPLY_TO = 'In-Reply-To';
    const REFERENCES = 'References';
    const SUBJECT = 'Subject';
    const COMMENTS = 'Comments';
    const KEYWORDS = 'Keywords';
    
    const MIME_VERSION = 'MIME-Version'; 
    const CONTENT_TYPE = 'Content-Type'; 
    const AUTO_SUBMITTED = 'Auto-Submitted'; 
}
