<?php

/*
 * (c) Tomas Pecserke <tomas@pecserke.eu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pecserke\Bundle\TwigDoctrineLoaderBundle\Tests\Model;

use Pecserke\Bundle\TwigDoctrineLoaderBundle\Model\Template;

class TemplateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Template
     */
    private $template;

    protected function setUp()
    {
        $this->template = new Template();
    }

    public function testGetSetName()
    {
        $name = 'some_name';
        $this->template->setName($name);
        $this->assertEquals($name, $this->template->getName());
    }

    public function testGetSetSource()
    {
        $source = 'template source';
        $this->template->setSource($source);
        $this->assertEquals($source, $this->template->getSource());
    }

    public function testOnModification()
    {
        $before = time();
        $this->template->onModified();
        $after = time();
        $lastModifiedTime = $this->template->getModifiedAt()->getTimestamp();
        $this->assertTrue($before <= $lastModifiedTime && $lastModifiedTime <= $after);
    }
}
