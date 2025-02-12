<?php
namespace Slim\Tests\Views;

use PHPUnit\Framework\TestCase;
use Slim\Views\Smarty;

class SmartyTest extends TestCase
{
    /**
     * @var Smarty
     */
    protected $view;

    public function setUp(): void
    {
        $mockRouter = $this->getMockBuilder('Slim\Router')
            ->disableOriginalConstructor()
            ->getMock();

        $this->view = new Smarty(dirname(__FILE__) . '/templates', [
            'compileDir' => dirname(__FILE__) . '/templates_c',
            'pluginsDir' => './test'
        ]);

        $this->view->addSlimPlugins($mockRouter, 'base_url_test');
    }

    /**
     * @covers \Slim\Views\Smarty::fetch
     */
    public function testFetch()
    {
        $output = $this->view->fetch('hello.tpl', [
            'name' => 'Matheus'
        ]);

        $this->assertEquals("<p>Hello, my name is Matheus.</p>\n", $output);
    }

    /**
     * @covers \Slim\Views\Smarty::render
     */
    public function testRender()
    {
        $mockBody = $this->getMockBuilder('Psr\Http\Message\StreamInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $mockResponse = $this->getMockBuilder('Psr\Http\Message\ResponseInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $mockBody->expects($this->once())
            ->method('write')
            ->with("<p>Hello, my name is Matheus.</p>\n")
            ->willReturn(34);

        $mockResponse->expects($this->once())
            ->method('getBody')
            ->willReturn($mockBody);

        $response = $this->view->render($mockResponse, 'hello.tpl', [
            'name' => 'Matheus'
        ]);
        $this->assertInstanceOf('Psr\Http\Message\ResponseInterface', $response);
    }

    /**
     * @covers \Slim\Views\Smarty::fetch
     */
    public function testPlugin()
    {
        $output = $this->view->fetch('plugin.tpl');

        $this->assertEquals("<p>Plugin return: base_url_test.</p>\n", $output);
    }

    /**
     * @covers \Slim\Views\Smarty::getSmarty
     */
    public function testPluginDirs()
    {
        $this->assertGreaterThanOrEqual(2, count($this->view->getSmarty()->getPluginsDir()));
    }
}
