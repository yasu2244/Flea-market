<?php
namespace Tests;

use Laravel\Dusk\TestCase as BaseTestCase;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Chrome\ChromeOptions;

abstract class DuskTestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Dusk 用の環境ファイルを明示
     *
     * @var string
     */
    protected $environmentFile = '.env.dusk.local';

    /**
     * テスト開始前に必ず ChromeDriver を起動する
     *
     * @beforeClass
     */
    public static function prepare(): void
    {
        if (! static::runningInSail()) {
            // 第1引数：引数配列、第2引数：ドライババイナリのフルパス、第3：ポート
            static::startChromeDriver(
                ['--port=9515'],
                env('DUSK_DRIVER_PATH', '/usr/bin/chromedriver'),
                9515
            );
        }
    }

    /**
     * RemoteWebDriver の生成
     */
    protected function driver(): RemoteWebDriver
    {
        $options = (new ChromeOptions)->addArguments([
            '--window-size=1920,1080',
            '--disable-gpu',
            '--no-sandbox',
            '--disable-dev-shm-usage',
            '--headless=new',
            '--user-data-dir=/tmp/dusk_profile_' . uniqid(),
        ]);

        return RemoteWebDriver::create(
            env('DUSK_DRIVER_URL', 'http://localhost:9515'),
            DesiredCapabilities::chrome()->setCapability(
                ChromeOptions::CAPABILITY, $options
            )
        );
    }
}
