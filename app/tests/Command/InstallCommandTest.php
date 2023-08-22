<?php

namespace App\Tests\Command;

use App\Command\App\InstallCommand;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class InstallCommandTest extends TestCase
{

    private InstallCommand $installCommand;

    private ReflectionMethod $executeMethod;

    protected function setUp(): void
    {
        $this->installCommand = new InstallCommand();
        $this->executeMethod = new ReflectionMethod($this->installCommand, 'execute');
        $this->executeMethod->setAccessible(true);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(Command::class, $this->installCommand);
    }

    public function testMethodsExistence()
    {
        $this->assertTrue(method_exists($this->installCommand, 'configure'));
        $this->assertTrue(method_exists($this->installCommand, 'execute'));
        $this->assertTrue(method_exists($this->installCommand, 'getCommandHelp'));
    }

    public function testPropertiesExistence()
    {
        $alias = &$this->installCommand;
        $this->assertEquals('siotest:install:app', $alias->getName());
        $this->assertEquals('Install DB, make migrations, load fixtures', $alias->getDescription());
    }

    public function testMethodExecuteSuccess()
    {
        $result = $this->checkMethodExecuteByStatusCode(0);
        $this->assertEquals(0, $result, 'Check result: Command::SUCCESS');
    }

    public function testMethodExecuteFailure()
    {
        $result = $this->checkMethodExecuteByStatusCode(1);
        $this->assertEquals(1, $result, 'Check result: Command::FAILURE');
    }

    private function checkMethodExecuteByStatusCode(int $statusCode)
    {
        $mockCommand = $this->getMockBuilder(Command::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mockCommand->expects($this->any())
            ->method('run')
            ->with(
                $this->isInstanceOf(InputInterface::class),
                $this->isInstanceOf(OutputInterface::class)
            )
            ->willReturn($statusCode);

        $mockApplication = $this->getMockBuilder(Application::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mockApplication->expects($this->any())
            ->method('find')
            ->withConsecutive(
                ['doctrine:database:create'],
                ['doctrine:migrations:migrate'],
                ['doctrine:fixtures:load']
            )
            ->willReturn($mockCommand);

        $this->installCommand->setApplication($mockApplication);

        return $this->executeMethod->invoke(
            $this->installCommand,
            $this->createMock(InputInterface::class),
            $this->createMock(OutputInterface::class)
        );
    }
}