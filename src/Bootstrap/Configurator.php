<?php
namespace Asdf\Bootstrap;

use Asdf\Application\Plugin\PluginManager;
use Nette\Application\Application;
use Nette\Application\IResponse;
use Nette\Application\Request;
use Nette\Application\UI\Presenter;
use Nette\Caching\Storages\FileStorage;
use Nette\DI\Config\Helpers;
use Nette\DI\Config\Loader;
use Nette\DI\Container;
use Nette\Loaders\RobotLoader;
use Tracy\Debugger;

class Configurator
{
    /**
     * @var string
     */
    private $configDir;

    /**
     * @var string
     */
    private $localNeonPath;

    /**
     * @var string
     */
    private $tempDir;

    /**
     * @var string
     */
    private $logsDir;

    /**
     * @var string
     */
    private $baseDir;

    /**
     * @param string $baseDir
     */
    public function __construct(string $baseDir)
    {
        $this->configDir = $baseDir . '/app/config';
        $this->localNeonPath = $this->configDir . '/config.local.neon';

        require_once $baseDir . '/vendor/autoload.php';
        $this->baseDir = $baseDir;
    }

    /**
     * @return string
     * @throws \Exception
     */
    private function getEnvironment(): string
    {
        if (!isset($_SERVER['PHP_ENV'])) {
            throw new \Exception('environment not found in $_SERVER[\'PHP_ENV\']');
        }

        return $_SERVER['PHP_ENV'];
    }

    /**
     * @return Container
     */
    public function createContainer(): Container
    {
        $environment = $this->getEnvironment();
        $configuratorParams = $this->getConfiguratorParams($environment);
        $this->setPhpParams($configuratorParams);
        $this->setConfiguratorPaths($configuratorParams);
        $this->initDebug($configuratorParams);
        $this->initRobotLoader($configuratorParams);

        $container = $this->createNetteContainer($configuratorParams);

        $this->registerPlugins($container);

        return $container;
    }

    /**
     * @param string $environment
     * @return array
     */
    private function getConfiguratorParams(string $environment): array
    {
        $configs = [
            $this->configDir . '/configuratorParams/default.neon',
            $this->configDir . '/configuratorParams/' . $environment . '.neon',
            $this->localNeonPath,
        ];

        $params = [];
        foreach ($configs as $path) {
            $configParams = (new Loader())->load($path);
            $params = Helpers::merge($configParams, $params);
        }

        return $params;
    }

    /**
     * @param array $configuratorParams
     */
    private function setConfiguratorPaths(array $configuratorParams)
    {
        $this->tempDir = $this->baseDir . $configuratorParams['tempDir'];
        $this->logsDir = $this->baseDir . $configuratorParams['logDir'];
    }

    /**
     * @param array $configuratorParams
     * @return Container
     */
    private function createNetteContainer(array $configuratorParams): Container
    {
        $configurator = new \Nette\Configurator();
        $configurator->setTempDirectory($this->tempDir);
        foreach ($configuratorParams['configs'] as $path) {
            $configurator->addConfig($this->baseDir . '/' . $path);
        }
        $configurator->addConfig($this->localNeonPath);

        $configurator->addParameters(
            [
                'BASE_DIR' => $this->baseDir,
            ]
        );
        $configurator->setDebugMode($configuratorParams['debugMode']);

        return $configurator->createContainer();
    }

    /**
     * @param array $configuratorParams
     */
    private function initDebug(array $configuratorParams)
    {
        $debuggerParams = $configuratorParams['debugger'];

        Debugger::$strictMode = (bool)$debuggerParams['strictMode'];
        Debugger::$maxDepth = (int)$debuggerParams['maxDepth'];

        $debuggerEnabled = $this->getDebuggerEnabled($debuggerParams);

        Debugger::enable(
            $debuggerEnabled === false,
            $this->logsDir,
            $debuggerParams['email']
        );
    }

    /**
     * @param array $configuratorParams
     */
    private function initRobotLoader(array $configuratorParams)
    {
        $robotLoaderParams = $configuratorParams['robotLoader'];
        if ((bool)$robotLoaderParams['enabled']) {
            $robotLoader = new RobotLoader();
            $robotLoader->setCacheStorage(new FileStorage($this->tempDir));

            foreach ($robotLoaderParams['directories'] as $directory) {
                $robotLoader->addDirectory($this->baseDir . '/' . $directory);
            }
            $robotLoader->autoRebuild = (bool)$robotLoaderParams['autoRebuild'];
            $robotLoader->register();
        }
    }

    /**
     * @param array $configuratorParams
     */
    private function setPhpParams(array $configuratorParams)
    {
        error_reporting($configuratorParams['error_reporting']);
        ini_set('display_errors', $configuratorParams['ini_set_display_errors']);
        date_default_timezone_set($configuratorParams['date_default_timezone_set']);
    }

    /**
     * Possible to add white list of IPs, that can send tracy-debug cookie
     * to turn-on the debug mode
     *
     * @param array $params
     * @return bool
     */
    private function getDebuggerEnabled(array $params): bool
    {
        $enable = $params['enabled'];
        if (is_array($enable)) {
            $debuggerEnabled = $this->detectDebugMode($params);
        } elseif (is_bool($enable)) {
            $debuggerEnabled = $enable;
        } else {
            $debuggerEnabled = false;
        }

        return $debuggerEnabled;
    }

    /**
     * @param array $params
     * @return bool
     */
    private function detectDebugMode(array $params)
    {
        $address = isset($_SERVER['HTTP_X_FORWARDED_FOR'])
            ? $_SERVER['HTTP_X_FORWARDED_FOR']
            : php_uname('n');

        $cookieSecret = $params['cookieSecret'];
        $secret = isset($_COOKIE[$cookieSecret]) && is_string($_COOKIE[$cookieSecret])
            ? $_COOKIE[$cookieSecret]
            : null;

        return in_array("$secret@$address", $params['enabled'], true);
    }

    /**
     * @param Container $container
     */
    private function registerPlugins(Container $container)
    {
        $application = $container->getByType(Application::class);
        $pluginManager = $container->getByType(PluginManager::class);

        $application->onStartup[] = function (Application $sender) use ($pluginManager) {
            $pluginManager->onStartup($sender);
        };
        $application->onRequest[] = function (Application $sender, Request $request) use ($pluginManager) {
            $pluginManager->onRequest($sender, $request);
        };
        $application->onPresenter[] = function (Application $sender, Presenter $presenter) use ($pluginManager) {
            $pluginManager->onPresenterCreate($sender, $presenter);
        };
        $application->onResponse[] = function (Application $application, IResponse $response) use ($pluginManager) {
            $pluginManager->onResponse($application, $response);
        };
        $application->onShutdown[] = function (Application $application, \Throwable $exception = null) use (
            $pluginManager
        ) {
            $pluginManager->onShutdown($application, $exception);
        };
    }
}
