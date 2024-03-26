<?php


namespace Espo\Tools\Layout;

use Espo\Core\Utils\Resource\FileReader\Params as FileReaderParams;
use RuntimeException;

class PortalLayoutProvider extends LayoutProvider
{
    public function get(string $scope, string $name): ?string
    {
        if (
            $this->sanitizeInput($scope) !== $scope ||
            $this->sanitizeInput($name) !== $name
        ) {
            throw new RuntimeException("Bad parameters.");
        }

        $path = 'layouts/' . $scope . '/portal/' . $name . '.json';

        $params = FileReaderParams::create()
            ->withScope($scope);

        if ($this->fileReader->exists($path, $params)) {
            return $this->fileReader->read($path, $params);
        }

        return parent::get($scope, $name);
    }
}
