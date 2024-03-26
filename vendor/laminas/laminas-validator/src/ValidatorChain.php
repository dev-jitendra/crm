<?php 

namespace Laminas\Validator;

use Countable;
use IteratorAggregate;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Stdlib\PriorityQueue;
use ReturnTypeWillChange;
use Traversable;

use function array_replace;
use function assert;
use function count;
use function rsort;

use const SORT_NUMERIC;


class ValidatorChain implements Countable, IteratorAggregate, ValidatorInterface
{
    
    public const DEFAULT_PRIORITY = 1;

    
    protected $plugins;

    
    protected $validators;

    
    protected $messages = [];

    
    public function __construct()
    {
        $this->validators = new PriorityQueue();
    }

    
    #[ReturnTypeWillChange]
    public function count()
    {
        return count($this->validators);
    }

    
    public function getPluginManager()
    {
        if (! $this->plugins) {
            $this->setPluginManager(new ValidatorPluginManager(new ServiceManager()));
        }
        return $this->plugins;
    }

    
    public function setPluginManager(ValidatorPluginManager $plugins)
    {
        $this->plugins = $plugins;
        return $this;
    }

    
    public function plugin($name, ?array $options = null)
    {
        $plugins = $this->getPluginManager();
        return $plugins->get($name, $options);
    }

    
    public function attach(
        ValidatorInterface $validator,
        $breakChainOnFailure = false,
        $priority = self::DEFAULT_PRIORITY
    ) {
        
        $this->validators->insert(
            [
                'instance'            => $validator,
                'breakChainOnFailure' => (bool) $breakChainOnFailure,
            ],
            $priority
        );

        return $this;
    }

    
    public function addValidator(
        ValidatorInterface $validator,
        $breakChainOnFailure = false,
        $priority = self::DEFAULT_PRIORITY
    ) {
        return $this->attach($validator, $breakChainOnFailure, $priority);
    }

    
    public function prependValidator(ValidatorInterface $validator, $breakChainOnFailure = false)
    {
        $priority = self::DEFAULT_PRIORITY;

        if (! $this->validators->isEmpty()) {
            $extractedNodes = $this->validators->toArray(PriorityQueue::EXTR_PRIORITY);
            rsort($extractedNodes, SORT_NUMERIC);
            $priority = $extractedNodes[0] + 1;
        }

        
        $this->validators->insert(
            [
                'instance'            => $validator,
                'breakChainOnFailure' => (bool) $breakChainOnFailure,
            ],
            $priority
        );
        return $this;
    }

    
    public function attachByName($name, $options = [], $breakChainOnFailure = false, $priority = self::DEFAULT_PRIORITY)
    {
        if (isset($options['break_chain_on_failure'])) {
            $breakChainOnFailure = (bool) $options['break_chain_on_failure'];
        }

        if (isset($options['breakchainonfailure'])) {
            $breakChainOnFailure = (bool) $options['breakchainonfailure'];
        }

        $this->attach($this->plugin($name, $options), $breakChainOnFailure, $priority);

        return $this;
    }

    
    public function addByName($name, $options = [], $breakChainOnFailure = false)
    {
        return $this->attachByName($name, $options, $breakChainOnFailure);
    }

    
    public function prependByName($name, $options = [], $breakChainOnFailure = false)
    {
        $validator = $this->plugin($name, $options);
        $this->prependValidator($validator, $breakChainOnFailure);
        return $this;
    }

    
    public function isValid($value, $context = null)
    {
        $this->messages = [];
        $result         = true;
        foreach ($this as $element) {
            $validator = $element['instance'];
            assert($validator instanceof ValidatorInterface);
            if ($validator->isValid($value, $context)) {
                continue;
            }
            $result         = false;
            $messages       = $validator->getMessages();
            $this->messages = array_replace($this->messages, $messages);
            if ($element['breakChainOnFailure']) {
                break;
            }
        }
        return $result;
    }

    
    public function merge(ValidatorChain $validatorChain)
    {
        foreach ($validatorChain->validators->toArray(PriorityQueue::EXTR_BOTH) as $item) {
            $this->attach($item['data']['instance'], $item['data']['breakChainOnFailure'], $item['priority']);
        }

        return $this;
    }

    
    public function getMessages()
    {
        return $this->messages;
    }

    
    public function getValidators()
    {
        return $this->validators->toArray(PriorityQueue::EXTR_DATA);
    }

    
    public function __invoke(mixed $value)
    {
        return $this->isValid($value);
    }

    
    public function __clone()
    {
        $this->validators = clone $this->validators;
    }

    
    public function __sleep()
    {
        return ['validators', 'messages'];
    }

    
    public function getIterator(): Traversable
    {
        return clone $this->validators;
    }
}
