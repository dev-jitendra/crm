<?php


namespace Espo\Core\Console\Commands;

use Espo\Core\Console\Command;
use Espo\Core\Console\Command\Params;
use Espo\Core\Console\IO;
use Espo\Core\InjectableFactory;
use Espo\Core\Utils\File\Manager as FileManager;
use Espo\Core\Utils\Util;

class AppInfo implements Command
{
    public function __construct(private InjectableFactory $injectableFactory, private FileManager $fileManager)
    {}

    public function run(Params $params, IO $io): void
    {
        
        $fileList = $this->fileManager->getFileList('application/Espo/Classes/AppInfo');

        $typeList = array_map(
            function ($item): string {
                return lcfirst(substr($item, 0, -4));
            },
            $fileList
        );

        foreach ($typeList as $type) {
            if ($params->hasFlag(Util::camelCaseToHyphen($type))) {
                $this->processType($io, $type, $params);

                return;
            }
        }

        if (count($params->getFlagList()) === 0) {
            $io->writeLine("");
            $io->writeLine("Available flags:");
            $io->writeLine("");

            foreach ($typeList as $type) {
                $io->writeLine(' --' . Util::camelCaseToHyphen($type));
            }

            $io->writeLine("");

            return;
        }

        $io->writeLine("Not supported flag specified.");
    }

    protected function processType(IO $io, string $type, Params $params): void
    {
        
        $className = 'Espo\\Classes\\AppInfo\\' . ucfirst($type);

        $obj = $this->injectableFactory->create($className);

        
        assert(method_exists($obj, 'process'));

        $result = $obj->process($params);

        $io->writeLine('');
        $io->write($result);
        $io->writeLine("");
    }
}
