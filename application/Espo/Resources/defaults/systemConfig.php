<?php


return [
    'defaultPermissions' => [
        'dir' => '0755',
        'file' => '0644',
        'user' => '',
        'group' => '',
    ],
    'crud' => [
        'get' => 'read',
        'post' => 'create',
        'put' => 'update',
        'patch' => 'patch',
        'delete' => 'delete',
    ],
    'systemUserAttributes' => [
        'lastName' => 'System',
    ],
    'systemItems' => [
        'systemItems',
        'adminItems',
        'superAdminItems',
        'configPath',
        'cachePath',
        'database',
        'crud',
        'logger',
        'isInstalled',
        'systemUser',
        'defaultPermissions',
        'passwordSalt',
        'cryptKey',
        'apiSecretKeys',
        'hashSecretKey',
        'restrictedMode',
        'instanceId',
        'adminUpgradeDisabled',
        'userLimit',
        'portalUserLimit',
        'stylesheet',
        'userItems',
        'globalItems',
        'internalSmtpServer',
        'internalSmtpPort',
        'internalSmtpAuth',
        'internalSmtpUsername',
        'internalSmtpPassword',
        'internalSmtpSecurity',
        'internalOutboundEmailFromAddress',
        'requiredPhpVersion',
        'requiredMysqlVersion',
        'requiredPostgresqlVersion',
        'recommendedMysqlParams',
        'requiredPhpLibs',
        'recommendedPhpLibs',
        'recommendedPhpParams',
        'requiredMariadbVersion',
        'recommendedMariadbParams',
        'phpExecutablePath',
        'webSocketDebugMode',
        'webSocketSslCertificateFile',
        'webSocketSslCertificateLocalPrivateKey',
        'webSocketSslCertificatePassphrase',
        'webSocketSslAllowSelfSigned',
        'webSocketUseSecureServer',
        'webSocketPort',
        'webSocketZeroMQSubscriberDsn',
        'webSocketZeroMQSubmissionDsn',
        'webSocketMessager',
        'actualDatabaseType',
        'actualDatabaseVersion',
        'clientSecurityHeadersDisabled',
        'clientCspDisabled',
        'clientCspScriptSourceList',
        'authTokenSecretDisabled',
        'authLogDisabled',
        'authApiUserLogDisabled',
        'authFailedAttemptsPeriod',
        'authMaxFailedAttemptNumber',
        'ipAddressServerParam',
        'jobNoTableLocking',
        'passwordRecoveryRequestLifetime',
        'passwordChangeRequestNewUserLifetime',
        'passwordChangeRequestExistingUserLifetime',
        'passwordRecoveryInternalIntervalPeriod',
    ],
    'adminItems' => [
        'devMode',
        'smtpServer',
        'smtpPort',
        'smtpAuth',
        'smtpSecurity',
        'smtpUsername',
        'smtpPassword',
        'jobMaxPortion',
        'jobPeriod',
        'jobRerunAttemptNumber',
        'jobRunInParallel',
        'jobPoolConcurrencyNumber',
        'jobPeriodForActiveProcess',
        'cronMinInterval',
        'daemonInterval',
        'daemonProcessTimeout',
        'daemonMaxProcessNumber',
        'authenticationMethod',
        'adminPanelIframeHeight',
        'adminPanelIframeUrl',
        'adminPanelIframeDisabled',
        'ldapHost',
        'ldapPort',
        'ldapSecurity',
        'ldapAuth',
        'ldapUsername',
        'ldapPassword',
        'ldapBindRequiresDn',
        'ldapBaseDn',
        'ldapUserLoginFilter',
        'ldapAccountCanonicalForm',
        'ldapAccountDomainName',
        'ldapAccountDomainNameShort',
        'ldapAccountFilterFormat',
        'ldapTryUsernameSplit',
        'ldapOptReferrals',
        'ldapPortalUserLdapAuth',
        'ldapCreateEspoUser',
        'ldapAccountDomainName',
        'ldapAccountDomainNameShort',
        'ldapUserNameAttribute',
        'ldapUserFirstNameAttribute',
        'ldapUserLastNameAttribute',
        'ldapUserTitleAttribute',
        'ldapUserEmailAddressAttribute',
        'ldapUserPhoneNumberAttribute',
        'ldapUserObjectClass',
        'maxEmailAccountCount',
        'massEmailMaxPerHourCount',
        'massEmailMaxPerBatchCount',
        'massEmailSiteUrl',
        'personalEmailMaxPortionSize',
        'inboundEmailMaxPortionSize',
        'authTokenLifetime',
        'authTokenMaxIdleTime',
        'ldapUserDefaultTeamId',
        'ldapUserDefaultTeamName',
        'ldapUserTeamsIds',
        'ldapUserTeamsNames',
        'ldapPortalUserPortalsIds',
        'ldapPortalUserPortalsNames',
        'ldapPortalUserRolesIds',
        'ldapPortalUserRolesNames',
        'cleanupJobPeriod',
        'emailAutoReplySuppressPeriod',
        'emailAutoReplyLimit',
        'cleanupActionHistoryPeriod',
        'adminNotifications',
        'adminNotificationsNewVersion',
        'adminNotificationsCronIsNotConfigured',
        'adminNotificationsNewExtensionVersion',
        'leadCaptureAllowOrigin',
        'cronDisabled',
        'defaultPortalId',
        'cleanupDeletedRecords',
        'cleanupSubscribers',
        'cleanupSubscribersPeriod',
        'authTokenPreventConcurrent',
        'emailParser',
        'passwordRecoveryDisabled',
        'passwordRecoveryNoExposure',
        'passwordRecoveryForAdminDisabled',
        'passwordRecoveryForInternalUsersDisabled',
        'passwordRecoveryRequestDelay',
        'thumbImageCacheDisabled',
        'emailReminderPortionSize',
        'outboundSmsFromNumber',
        'currencyNoJoinMode',
        'authAnotherUserDisabled',
        'emailAddressEntityLookupDefaultOrder',
        'phoneNumberEntityLookupDefaultOrder',
        'latestVersion',
    ],
    'superAdminItems' => [
        'jobMaxPortion',
        'jobPeriod',
        'jobRerunAttemptNumber',
        'jobRunInParallel',
        'jobPoolConcurrencyNumber',
        'jobPeriodForActiveProcess',
        'cronMinInterval',
        'daemonInterval',
        'daemonProcessTimeout',
        'daemonMaxProcessNumber',
        'adminPanelIframeUrl',
        'adminPanelIframeDisabled',
        'adminPanelIframeHeight',
        'cronDisabled',
        'maintenanceMode',
        'siteUrl',
        'useWebSocket',
        'webSocketUrl',
    ],
    'userItems' => [],
    'globalItems' => [
        'cacheTimestamp',
        'appTimestamp',
        'language',
        'isDeveloperMode',
        'theme',
        'dateFormat',
        'timeFormat',
        'timeZone',
        'decimalMark',
        'weekStart',
        'thousandSeparator',
        'companyLogoId',
        'applicationName',
        'jsLibs',
        'maintenanceMode',
        'siteUrl',
        'useCache',
        'useCacheInDeveloperMode',
        'isDeveloperMode',
        'useWebSocket',
        'webSocketUrl',
        'aclAllowDeleteCreated',
    ],
    'isInstalled' => false,
    'requiredPhpVersion' => '8.1.0',
    'requiredPhpLibs' => [
        'json',
        'openssl',
        'mbstring',
        'zip',
        'gd',
        'iconv'
    ],
    'recommendedPhpLibs' => [
        'curl',
        'xml',
        'xmlwriter',
        'exif',
    ],
    'recommendedPhpParams' => [
        'max_execution_time' => 180,
        'max_input_time' => 180,
        'memory_limit' => '256M',
        'post_max_size' => '20M',
        'upload_max_filesize' => '20M',
    ],
    'requiredMysqlVersion' => '5.7.0',
    'recommendedMysqlParams' => [],
    'requiredMariadbVersion' => '10.2.2',
    'requiredPostgresqlVersion' => '15.0',
    'recommendedMariadbParams' => [],
    
    'jobPeriod' => 7800,
    
    'jobPeriodForActiveProcess' => 36000,
    
    'jobRerunAttemptNumber' => 1,
    
    'cronMinInterval' => 2,
];
