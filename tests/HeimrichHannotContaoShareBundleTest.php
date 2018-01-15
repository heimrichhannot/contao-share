<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0+
 */

namespace HeimrichHannot\ShareBundle\Test;

use HeimrichHannot\ShareBundle\DependencyInjection\HeimrichHannotContaoShareExtension;
use HeimrichHannot\ShareBundle\HeimrichHannotContaoShareBundle;
use PHPUnit\Framework\TestCase;

class HeimrichHannotContaoShareBundleTest extends TestCase
{
    public function testCanBeInstantiated()
    {
        $bundle = new HeimrichHannotContaoShareBundle();
        $this->assertInstanceOf(HeimrichHannotContaoShareBundle::class, $bundle);
    }

    public function testGetContainerExtension()
    {
        $bundle = new HeimrichHannotContaoShareBundle();
        $this->assertInstanceOf(HeimrichHannotContaoShareExtension::class, $bundle->getContainerExtension());
    }
}
