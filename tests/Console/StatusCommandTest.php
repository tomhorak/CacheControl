<?php
namespace Crunch\CacheControl\Console;

use Crunch\CacheControl\Connector;
use Crunch\CacheControl\Console\Helper\CacheControlHelper;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Phake;
use Phake_IMock as Mock;

class StatusCommandTest extends TestCase
{
    /** @var InputInterface|Mock */
    private $input;
    /** @var OutputInterface|Mock */
    private $output;
    /** @var Connector|Mock */
    private $connector;
    /** @var CacheControlHelper|Mock */
    private $helper;

    /** @var StatusCommand|Mock */
    private $subject;

    protected function setUp()
    {
        parent::setUp();
        $this->input = Phake::mock(InputInterface::class);
        $this->output = Phake::mock(OutputInterface::class);
        $this->connector = Phake::mock(Connector::class);
        $this->helper = Phake::mock(CacheControlHelper::class);

        $this->subject = Phake::partialMock(StatusCommand::class);

        Phake::when($this->subject)->createConnectorInstance(Phake::anyParameters())->thenReturn($this->connector);
        Phake::when($this->subject)->getHelper('cache-control')->thenReturn($this->helper);
    }

    public function testStatusViaHostAndPort()
    {
        Phake::when($this->connector)->fetchStatus()->thenReturn([]);
        Phake::when($this->input)->getOption('host')->thenReturn('localhost:9000');

        $this->subject->run($this->input, $this->output);

        Phake::verify($this->connector, Phake::times(1))->fetchStatus();
        Phake::verify($this->subject)->createConnectorInstance('localhost', '9000');
    }

    public function testStatusViaSocket()
    {
        Phake::when($this->connector)->fetchStatus()->thenReturn([]);
        Phake::when($this->input)->getOption('host')->thenReturn('unix:///foo/bar');

        $this->subject->run($this->input, $this->output);

        Phake::verify($this->connector, Phake::times(1))->fetchStatus();
        Phake::verify($this->subject)->createConnectorInstance('unix:///foo/bar', null);
    }

    public function testStatusViaSocketPathOnly()
    {
        Phake::when($this->connector)->fetchStatus()->thenReturn([]);
        Phake::when($this->input)->getOption('host')->thenReturn('/foo/bar');

        $this->subject->run($this->input, $this->output);

        Phake::verify($this->connector, Phake::times(1))->fetchStatus();
        Phake::verify($this->subject)->createConnectorInstance('unix:///foo/bar', null);
    }
}
