<?php

namespace Application;

use Abramenko\RestApi\Application\Exceptions\ApplicationException;
use Abramenko\RestApi\Application\Exceptions\ApplicationThrowable;
use PHPUnit\Framework\TestCase;
use \Abramenko\RestApi\Application\Application;

class ApplicationInputTest extends Application
{
    protected function fileGetContents($input = false): string
    {
        $input = '{
            "name": "a_abramenko/rest-api",
            "type": "project",
            "license": "MIT",
            "keywords": ["Rest API", "Routing", "App Pattern"]}';
        return parent::fileGetContents($input);
    }
}

class ApplicationTest extends TestCase
{
    public function testCanCreateAnApplication(): void
    {
        $app = new Application();

        $this->assertEquals(
            "Abramenko\RestApi\Application\Application",
            $app::class
        );
    }

    public function testRunMethodIsWork(): void
    {
        $app = new Application();
        $app->run();
        $this->expectOutputString("");
    }

    /**
     * @throws ApplicationThrowable
     * @throws ApplicationException
     */
    public function testParseRequestToServer(): void
    {
        $_REQUEST['name'] = 'Alexey';
        $_REQUEST['last-name'] = 'Abramenko';
        $_REQUEST['users'] = [
            "Alexey Ivanov", "Sergey Petrov"
        ];
        $app = new ApplicationInputTest();
        $app->run();
        $this->assertEquals(
            [
                'name' => 'a_abramenko/rest-api',
                'last-name' => 'Abramenko',
                'users' => [
                    "Alexey Ivanov", "Sergey Petrov"
                ],
                'type' => 'project',
                'license' => 'MIT',
                'keywords' => ["Rest API", "Routing", "App Pattern"]
            ],
            $app->getRequestVariables()
        );
    }

    public function testDoesTheDefaultRouteWork(): void
    {
        $app = new Application();
        $app->setDefaultRoute(function ($params) {
            applicationDefaultRoute($params);
        });
        $app->run();
        $this->expectOutputString("Default Route is Work");
    }
}

function applicationDefaultRoute($variables): void
{
    echo "Default Route is Work";
}
