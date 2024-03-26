<?php


namespace Espo\Core\Utils;


class SystemUser
{
    
    public const NAME = 'system';

    private const ID = 'system';
    private const UUID = 'ffffffff-ffff-ffff-ffff-ffffffffffff';

    private string $id;

    public function __construct(Metadata $metadata, Config $config)
    {
        $id = $config->get('systemUserId');

        if ($id) {
            $this->id = $id;

            return;
        }

        $isUuid = $metadata->get(['app', 'recordId', 'dbType']) === 'uuid';

        $this->id = $isUuid ?
            self::UUID :
            self::ID;
    }

    
    public function getId(): string
    {
        return $this->id;
    }
}
