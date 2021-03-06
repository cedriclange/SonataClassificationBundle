<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ClassificationBundle\Tests\Entity;

use Sonata\ClassificationBundle\Entity\ContextManager;
use Sonata\ClassificationBundle\Tests\Helpers\PHPUnit_Framework_TestCase;
use Sonata\CoreBundle\Test\EntityManagerMockFactory;

class ContextManagerTest extends PHPUnit_Framework_TestCase
{
    public function testGetPager()
    {
        $self = $this;
        $this
            ->getContextManager(function ($qb) use ($self) {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(array()));
                $qb->expects($self->never())->method('andWhere');
                $qb->expects($self->once())->method('setParameters')->with(array());
            })
            ->getPager(array(), 1);
    }

    public function testGetPagerWithEnabledContexts()
    {
        $self = $this;
        $this
            ->getContextManager(function ($qb) use ($self) {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(array()));
                $qb->expects($self->once())->method('andWhere')->with($self->equalTo('c.enabled = :enabled'));
                $qb->expects($self->once())->method('setParameters')->with(array('enabled' => true));
            })
            ->getPager(array(
                'enabled' => true,
            ), 1);
    }

    public function testGetPagerWithDisabledContexts()
    {
        $self = $this;
        $this
            ->getContextManager(function ($qb) use ($self) {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(array()));
                $qb->expects($self->once())->method('andWhere')->with($self->equalTo('c.enabled = :enabled'));
                $qb->expects($self->once())->method('setParameters')->with(array('enabled' => false));
            })
            ->getPager(array(
                'enabled' => false,
            ), 1);
    }

    protected function getContextManager($qbCallback)
    {
        $em = EntityManagerMockFactory::create($this, $qbCallback, array());

        $registry = $this->getMockForAbstractClass('Doctrine\Common\Persistence\ManagerRegistry');
        $registry->expects($this->any())->method('getManagerForClass')->will($this->returnValue($em));

        return new ContextManager('Sonata\PageBundle\Entity\BaseContext', $registry);
    }
}
