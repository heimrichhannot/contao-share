<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0+
 */

namespace HeimrichHannot\SyndicationBundle\Components;

abstract class AbstractComponent
{
    public function getName()
    {
        return $this->getAlias();
    }

    abstract public function getAlias(): string;
}
