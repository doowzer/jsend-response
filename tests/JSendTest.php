<?php

namespace Tests;

use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class JSendTest extends TestCase
{
    /** @var \JSend\JSend $jsend */
    private $jsend;


    public function setUp()
    {
        $this->jsend = new \JSend\JSend();
    }

    public function testFail()
    {
        $result = $this->jsend->fail('Mock message');
        $this->assertEquals('{"status":"fail","data":"Mock message"}', (string) $result);
    }

    public function testError()
    {
        $result = $this->jsend->error('Mock message')->setCode(1);
        $this->assertEquals('{"status":"error","message":"Mock message","code":1}', (string) $result);
    }

    public function testSuccess()
    {
        $result = $this->jsend->success(['Mock message']);
        $this->assertEquals('{"status":"success","data":["Mock message"]}', (string) $result);
    }

    public function testErrorException()
    {
        $mockClass= new class('Mock message', 1) extends \Exception
        {
        };

        $result = json_decode($this->jsend->errorException($mockClass), 1);

        $this->assertArrayHasKey('status', $result);
        $this->assertArrayHasKey('message', $result);
        $this->assertArrayHasKey('code', $result);
        $this->assertArrayHasKey('data', $result);

        $this->assertEquals('error', $result['status']);
        $this->assertEquals('Mock message', $result['message']);
        $this->assertEquals(1, $result['code']);

        $this->assertArrayHasKey('file', $result['data']);
        $this->assertArrayHasKey('line', $result['data']);
        $this->assertArrayHasKey('trace', $result['data']);
    }

    public function testGetJson()
    {
        $result = json_decode(
            $this->jsend->error('Mock message')->setCode(1)->setData(['Mock data'])->getJson(),
            1
        );

        $this->assertArrayHasKey('status', $result);
        $this->assertArrayHasKey('message', $result);
        $this->assertArrayHasKey('code', $result);
        $this->assertArrayHasKey('data', $result);

        $this->assertEquals('error', $result['status']);
        $this->assertEquals('Mock message', $result['message']);
        $this->assertEquals(1, $result['code']);

        $this->assertTrue(is_array($result['data']));
        $this->assertEquals(['Mock data'], $result['data']);
    }

    public function testSetData()
    {
        $result = $this->jsend->success()->addData(['value-1']);
        $this->assertEquals('{"status":"success","data":["value-1"]}', (string) $result);
    }

    public function testAddData()
    {
        $result = $this->jsend->success(['value-1'])->addData(['value-2']);
        $this->assertEquals('{"status":"success","data":["value-1","value-2"]}', (string) $result);
    }

    /**
     * @expectedException \JSend\JSendException
     * @expectedExceptionMessage parameter must be an instance of \Throwable
     */
    public function testErrorExceptionNoThrowable()
    {
        $this->jsend->errorException(
            new class()
            {
            }
        );
    }

    /**
     * @expectedException \JSend\JSendException
     * @expectedExceptionMessage Message is required
     */
    public function testErrorNoMessage()
    {
        $this->jsend->error(null);
    }

    /**
     * @expectedException \JSend\JSendException
     * @expectedExceptionMessage Code must be numeric
     */
    public function testSetCodeNoneNumeric()
    {
        $this->jsend->error('Mock message')->setCode('A');
    }

    /**
     * @expectedException \JSend\JSendException
     * @expectedExceptionMessage Code can only be set when an error response is created
     */
    public function testSetCodeInvalidMessage()
    {
        $this->jsend->success()->setCode(0);
    }

    /**
     * @expectedException \JSend\JSendException
     * @expectedExceptionMessage Response must contain an status
     */
    public function testCheckResponseSetData()
    {
        $this->jsend->setData(['Mock message']);
    }

    /**
     * @expectedException \JSend\JSendException
     * @expectedExceptionMessage Response must contain an status
     */
    public function testCheckResponseSetCode()
    {
        $this->jsend->setCode(1);
    }
}
