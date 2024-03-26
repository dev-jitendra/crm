<?php



namespace Carbon\PHPStan;

use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\MethodsClassReflectionExtension;
use PHPStan\Reflection\Php\PhpMethodReflectionFactory;
use PHPStan\Type\TypehintHelper;


final class MacroExtension implements MethodsClassReflectionExtension
{
    
    protected $methodReflectionFactory;

    
    protected $scanner;

    
    public function __construct(PhpMethodReflectionFactory $methodReflectionFactory)
    {
        $this->scanner = new MacroScanner();
        $this->methodReflectionFactory = $methodReflectionFactory;
    }

    
    public function hasMethod(ClassReflection $classReflection, string $methodName): bool
    {
        return $this->scanner->hasMethod($classReflection->getName(), $methodName);
    }

    
    public function getMethod(ClassReflection $classReflection, string $methodName): MethodReflection
    {
        $builtinMacro = $this->scanner->getMethod($classReflection->getName(), $methodName);

        return $this->methodReflectionFactory->create(
            $classReflection,
            null,
            $builtinMacro,
            $classReflection->getActiveTemplateTypeMap(),
            [],
            TypehintHelper::decideTypeFromReflection($builtinMacro->getReturnType()),
            null,
            null,
            $builtinMacro->isDeprecated()->yes(),
            $builtinMacro->isInternal(),
            $builtinMacro->isFinal(),
            $builtinMacro->getDocComment()
        );
    }
}
