<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0+
 */

namespace HeimrichHannot\SyndicationBundle;

use HeimrichHannot\SyndicationBundle\DependencyInjection\HeimrichHannotContaoShareExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class HeimrichHannotContaoShareBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new HeimrichHannotContaoShareExtension();
    }
}
