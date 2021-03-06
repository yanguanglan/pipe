<?php

namespace Pipe\Util;

class ProcessorRegistry
{
    protected $processors = array();

    function prepend($mimeType, $processor)
    {
        if (!class_exists($processor)) {
            throw new \InvalidArgumentException("Class $processor is not defined");
        }

        if (empty($this->processors[$mimeType])) {
            $this->processors[$mimeType] = array();
        }

        array_unshift($this->processors[$mimeType], $processor);
        return $this;
    }

    function register($mimeType, $processor)
    {
        if (!class_exists($processor) and !is_callable($processor)) {
            throw new \InvalidArgumentException("Processor must be either a factory callback or a class name");
        }

        if (empty($this->processors[$mimeType])) {
            $this->processors[$mimeType] = array();
        }

        $this->processors[$mimeType][] = $processor;
        return $this;
    }

    function clear()
    {
        $this->processors = array();
    }

    function get($mimeType)
    {
        if (empty($this->processors[$mimeType])) {
            return array();
        }
        return $this->processors[$mimeType];
    }
}
