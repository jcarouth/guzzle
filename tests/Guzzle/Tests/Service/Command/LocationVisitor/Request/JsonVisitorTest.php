<?php

namespace Guzzle\Tests\Service\Command\LocationVisitor\Request;

use Guzzle\Service\Command\LocationVisitor\Request\JsonVisitor as Visitor;

/**
 * @covers Guzzle\Service\Command\LocationVisitor\Request\JsonVisitor
 */
class JsonVisitorTest extends AbstractVisitorTestCase
{
    public function testVisitsLocation()
    {
        $visitor = new Visitor();
        // Test after when no body query values were found
        $visitor->after($this->command, $this->request);

        $param = $this->getNestedCommand('json')->getParam('foo');
        $visitor->visit($this->command, $this->request, $param->setSentAs('test'), '123');
        $visitor->visit($this->command, $this->request, $param->setSentAs('test2'), 'abc');
        $visitor->after($this->command, $this->request);
        $this->assertEquals('{"test":"123","test2":"abc"}', (string) $this->request->getBody());
    }

    public function testAddsJsonHeader()
    {
        $visitor = new Visitor();
        $visitor->setContentTypeHeader('application/json-foo');
        $param = $this->getNestedCommand('json')->getParam('foo');
        $visitor->visit($this->command, $this->request, $param->setSentAs('test'), '123');
        $visitor->after($this->command, $this->request);
        $this->assertEquals('application/json-foo', (string) $this->request->getHeader('Content-Type'));
    }

    /**
     * @covers Guzzle\Service\Command\LocationVisitor\Request\JsonVisitor
     * @covers Guzzle\Service\Command\LocationVisitor\Request\AbstractRequestVisitor::resolveRecursively
     */
    public function testRecursivelyBuildsJsonBodies()
    {
        $command = $this->getCommand('json');
        $request = $command->prepare();
        $visitor = new Visitor();
        $param = $this->getNestedCommand('json')->getParam('foo');
        $visitor->visit($command, $request, $param->setSentAs('Foo'), $command['foo']);
        $visitor->after($command, $request);
        $this->assertEquals('{"Foo":{"test":{"baz":true,"Jenga_Yall!":"HELLO"},"bar":123}}', (string) $request->getBody());
    }
}
