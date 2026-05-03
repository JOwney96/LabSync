<?php

namespace Tests;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Illuminate\Support\Collection;
use Laravel\Dusk\TestCase as BaseTestCase;
use PHPUnit\Framework\Attributes\BeforeClass;
use Symfony\Component\Process\Process;

abstract class DuskTestCase extends BaseTestCase
{
    protected static ?Process $server = null;

    /**
     * Prepare for Dusk test execution.
     */
    #[BeforeClass]
    public static function prepare(): void
    {
        if (! static::runningInSail()) {
            static::startChromeDriver(['--port=9515']);
        }

        static::startApplicationServer();
    }

    /**
     * Create the RemoteWebDriver instance.
     */
    protected function driver(): RemoteWebDriver
    {
        $options = (new ChromeOptions)->addArguments(collect([
            $this->shouldStartMaximized() ? '--start-maximized' : '--window-size=1920,1080',
            '--disable-search-engine-choice-screen',
            '--disable-smooth-scrolling',
        ])->unless($this->hasHeadlessDisabled(), function (Collection $items) {
            return $items->merge([
                '--disable-gpu',
                '--headless=new',
            ]);
        })->all());

        return RemoteWebDriver::create(
            $_ENV['DUSK_DRIVER_URL'] ?? env('DUSK_DRIVER_URL') ?? 'http://localhost:9515',
            DesiredCapabilities::chrome()->setCapability(
                ChromeOptions::CAPABILITY, $options
            )
        );
    }

    protected function tearDown(): void
    {
        static::closeAll();

        parent::tearDown();
    }

    protected static function startApplicationServer(): void
    {
        if (static::$server?->isRunning()) {
            return;
        }

        $serverUrl = $_ENV['APP_URL'] ?? getenv('APP_URL') ?: 'http://127.0.0.1:8000';
        $host = parse_url($serverUrl, PHP_URL_HOST) ?: '127.0.0.1';
        $port = (string) (parse_url($serverUrl, PHP_URL_PORT) ?: 8000);

        static::$server = new Process(
            [PHP_BINARY, 'artisan', 'serve', '--host='.$host, '--port='.$port],
            dirname(__DIR__),
            ['APP_URL' => 'http://'.$host.':'.$port]
        );

        static::$server->setTimeout(null);
        static::$server->start();

        retry(20, function () use ($host, $port) {
            $connection = @fsockopen($host, (int) $port);

            if ($connection === false) {
                throw new \RuntimeException('The application server is not ready yet.');
            }

            fclose($connection);
        }, 250);

        static::afterClass(function () {
            if (static::$server?->isRunning()) {
                static::$server->stop();
            }

            static::$server = null;
        });
    }
}
