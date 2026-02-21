<?php

namespace iEducar\Packages\PreMatricula\Services\Concerns;

abstract class Finder
{
    protected $finders;

    public function find($data)
    {
        foreach ($this->finders as $finder) {
            if ($entity = $finder->find($data)) {
                return $entity;
            }
        }
    }
}
