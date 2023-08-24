<?php

namespace Boil;

use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

class Application extends Container implements \Illuminate\Contracts\Foundation\Application
{
    protected ?string $bootstrapPath = null;

    protected $appPath;

    protected $langPath;

    protected $configPath;

    protected $publicPath;

    protected $storagePath;

    protected $databasePath;

    protected $bootingCallbacks = [];

    protected $bootedCallbacks = [];

    /**
     * The array of terminating callbacks.
     *
     * @var callable[]
     */
    protected $terminatingCallbacks = [];

    /**
     * All the registered service providers.
     *
     * @var \Illuminate\Support\ServiceProvider[]
     */
    protected $serviceProviders = [];

    /**
     * The names of the loaded service providers.
     *
     * @var array
     */
    protected $loadedProviders = [];

    protected $booted = false;

    protected string $locale = 'en';

    public function __construct(protected string $basePath)
    {
        if ($basePath) {
            $this->setBasePath($basePath);
        }

        $this->registerBaseBindings();
        $this->registerBaseServiceProviders();
        $this->registerCoreContainerAliases();

        static::setInstance($this);
    }

    public function baseInstallationPath(string $path = ''): string
    {
        if (defined('ABSPATH')) {
            return $this->joinPaths(ABSPATH, $path);
        }

        throw new \Exception('Absolute path to the WordPress installation directory not defined.');
    }

    public function version(): string
    {
        return '0.0.1';
    }

    public function basePath($path = ''): string
    {
        return $this->joinPaths($this->basePath, $path);
    }

    public function joinPaths($basePath, $path = ''): string
    {
        return $basePath.($path != '' ? DIRECTORY_SEPARATOR.ltrim($path, DIRECTORY_SEPARATOR) : '');
    }

    public function bootstrapPath($path = ''): string
    {
        return $this->joinPaths($this->bootstrapPath, $path);
    }

    public function configPath($path = ''): string
    {
        return $this->joinPaths($this->configPath ?: $this->basePath('config'), $path);
    }

    public function databasePath($path = ''): string
    {
        return $this->joinPaths($this->databasePath ?: $this->basePath('database'), $path);
    }

    public function langPath($path = ''): string
    {
        return $this->joinPaths($this->langPath, $path);
    }

    public function publicPath($path = ''): string
    {
        return $this->joinPaths($this->publicPath ?: $this->basePath('public'), $path);
    }

    public function resourcePath($path = ''): string
    {
        return $this->joinPaths($this->basePath('resources'), $path);
    }

    public function storagePath($path = ''): string
    {
        return $this->joinPaths($this->storagePath ?: $this->basePath('storage'), $path);
    }

    public function environment(...$environments): string
    {
        // return WP_DEBUG ? 'dev' : 'production';

        return $this['env'];
    }

    public function runningInConsole(): bool
    {
        return false;
    }

    public function runningUnitTests(): bool
    {
        return $this->bound('env') && $this['env'] === 'testing';
    }

    public function hasDebugModeEnabled(): bool
    {
        if (defined('WP_DEBUG')) {
            return WP_DEBUG;
        }

        return false;
    }

    /** @return bool */
    public function maintenanceMode()
    {
        /**
         * Get an instance of the maintenance mode manager implementation.
         *
         * @return \Illuminate\Contracts\Foundation\MaintenanceMode
         */
        // Todo: public function maintenanceMode();
        return $this->isDownForMaintenance();
    }

    public function isDownForMaintenance(): bool
    {
        return file_exists($this->baseInstallationPath('.maintenance'));
    }

    public function registerConfiguredProviders(): void
    {
        Collection::make($this->make('config')->get('app.providers'))
            ->each(fn (string $provider) => $this->register($provider));
    }

    public function register($provider, $force = false)
    {
        if (($registered = $this->getProvider($provider)) && ! $force) {
            return $registered;
        }

        // If the given "provider" is a string, we will resolve it, passing in the
        // application instance automatically for the developer. This is simply
        // a more convenient way of specifying your service provider classes.
        if (is_string($provider)) {
            $provider = $this->resolveProvider($provider);
        }

        $provider->register();

        // If there are bindings / singletons set as properties on the provider we
        // will spin through them and register them with the application, which
        // serves as a convenience layer while registering a lot of bindings.
        if (property_exists($provider, 'bindings')) {
            foreach ($provider->bindings as $key => $value) {
                $this->bind($key, $value);
            }
        }

        if (property_exists($provider, 'singletons')) {
            foreach ($provider->singletons as $key => $value) {
                $key = is_int($key) ? $value : $key;

                $this->singleton($key, $value);
            }
        }

        $this->markAsRegistered($provider);

        // If the application has already booted, we will call this boot method on
        // the provider class so it has an opportunity to do its boot logic and
        // will be ready for any usage by this developer's application logic.
        if ($this->isBooted()) {
            $this->bootProvider($provider);
        }

        return $provider;
    }

    protected function getProvider($provider)
    {
        return array_values($this->getProviders($provider))[0] ?? null;
    }

    public function getProviders($provider): array
    {
        $name = is_string($provider) ? $provider : get_class($provider);

        return Arr::where($this->serviceProviders, function ($value) use ($name) {
            return $value instanceof $name;
        });
    }

    public function registerDeferredProvider($provider, $service = null): void
    {
        // Once the provider that provides the deferred service has been registered we
        // will remove it from our local list of the deferred services with related
        // providers so that this container does not try to resolve it out again.
        if ($service) {
            unset($this->deferredServices[$service]);
        }

        $this->register($instance = new $provider($this));

        if (! $this->isBooted()) {
            $this->booting(function () use ($instance) {
                $this->bootProvider($instance);
            });
        }
    }

    public function resolveProvider($provider)
    {
        return new $provider($this);
    }

    public function boot(): void
    {
        if ($this->isBooted()) {
            return;
        }

        // Once the application has booted we will also fire some "booted" callbacks
        // for any listeners that need to do work after this initial booting gets
        // finished. This is useful when ordering the boot-up processes we run.
        $this->fireAppCallbacks($this->bootingCallbacks);

        array_walk($this->serviceProviders, function ($p) {
            $this->bootProvider($p);
        });

        $this->booted = true;

        $this->fireAppCallbacks($this->bootedCallbacks);
    }

    protected function fireAppCallbacks(array &$callbacks): void
    {
        $index = 0;

        while ($index < count($callbacks)) {
            $callbacks[$index]($this);

            $index++;
        }
    }

    public function booting($callback)
    {
        $this->bootingCallbacks[] = $callback;
    }

    public function booted($callback): void
    {
        $this->bootedCallbacks[] = $callback;

        if ($this->isBooted()) {
            $callback($this);
        }
    }

    public function bootstrapWith(array $bootstrappers): void
    {
        foreach ($bootstrappers as $bootstrapper) {
            $this->make($bootstrapper)->bootstrap($this);
        }
    }

    /** @return string */
    public function getLocale()
    {
        return $this->locale;
    }

    public function getNamespace()
    {
        return '';
    }

    public function hasBeenBootstrapped(): bool
    {
        // TODO: Implement hasBeenBootstrapped() method.
        return false;
    }

    public function getCachedConfigPath()
    {
        return $this->basePath('bootstrap/cache/config.php');
    }

    public function detectEnvironment(callable $callback)
    {
        return $this['env'] = $callback();
    }

    public function loadDeferredProviders()
    {
        // TODO: Implement loadDeferredProviders() method.
    }

    /**
     * @param string $locale
     * @return void
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    public function shouldSkipMiddleware(): bool
    {
        return true;
    }

    public function terminating($callback)
    {
        $this->terminatingCallbacks[] = $callback;

        return $this;
    }

    public function terminate()
    {
        $index = 0;

        while ($index < count($this->terminatingCallbacks)) {
            $this->call($this->terminatingCallbacks[$index]);

            $index++;
        }
    }

    protected function markAsRegistered(ServiceProvider $provider): void
    {
        $this->serviceProviders[] = $provider;

        $this->loadedProviders[get_class($provider)] = true;
    }

    public function isBooted(): bool
    {
        return $this->booted;
    }

    protected function bootProvider(ServiceProvider $provider): void
    {
        $provider->callBootingCallbacks();

        if (method_exists($provider, 'boot')) {
            $this->call([$provider, 'boot']);
        }

        $provider->callBootedCallbacks();
    }

    public function setBasePath(string $basePath): static
    {
        $this->basePath = rtrim($basePath, '\/');

        $this->bindPathsInContainer();

        return $this;
    }

    protected function bindPathsInContainer(): void
    {
        $this->instance('path', $this->path());
        $this->instance('path.base', $this->basePath());
        //        $this->instance('path.lang', $this->langPath());
        $this->instance('path.config', $this->configPath());
        $this->instance('path.public', $this->publicPath());
        $this->instance('path.storage', $this->storagePath());
        //        $this->instance('path.database', $this->databasePath());
        $this->instance('path.resources', $this->resourcePath());
        $this->instance('path.bootstrap', $this->bootstrapPath());
    }

    public function path($path = '')
    {
        $appPath = $this->appPath ?: $this->basePath.DIRECTORY_SEPARATOR.'app';

        return $appPath.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    protected function registerBaseBindings()
    {
        static::setInstance($this);

        $this->instance('app', $this);

        $this->instance(Container::class, $this);
    }

    protected function registerBaseServiceProviders()
    {
        // $this->register(new EventServiceProvider($this));
        // $this->register(new LogServiceProvider($this));
        // $this->register(new RoutingServiceProvider($this));
    }

    public function registerCoreContainerAliases()
    {
        foreach ([
            'app' => [self::class, \Illuminate\Contracts\Container\Container::class, \Illuminate\Contracts\Foundation\Application::class, \Psr\Container\ContainerInterface::class],
            //                     'auth' => [\Illuminate\Auth\AuthManager::class, \Illuminate\Contracts\Auth\Factory::class],
            //                     'auth.driver' => [\Illuminate\Contracts\Auth\Guard::class],
            'blade.compiler' => [\Illuminate\View\Compilers\BladeCompiler::class],
            // 'cache' => [\Illuminate\Cache\CacheManager::class, \Illuminate\Contracts\Cache\Factory::class],
            //                     'cache.store' => [\Illuminate\Cache\Repository::class, \Illuminate\Contracts\Cache\Repository::class, \Psr\SimpleCache\CacheInterface::class],
            //                     'cache.psr6' => [\Symfony\Component\Cache\Adapter\Psr16Adapter::class, \Symfony\Component\Cache\Adapter\AdapterInterface::class, \Psr\Cache\CacheItemPoolInterface::class],
            'config' => [\Illuminate\Config\Repository::class, \Illuminate\Contracts\Config\Repository::class],
            //                     'cookie' => [\Illuminate\Cookie\CookieJar::class, \Illuminate\Contracts\Cookie\Factory::class, \Illuminate\Contracts\Cookie\QueueingFactory::class],
            //                     'db' => [\Illuminate\Database\DatabaseManager::class, \Illuminate\Database\ConnectionResolverInterface::class],
            //                     'db.connection' => [\Illuminate\Database\Connection::class, \Illuminate\Database\ConnectionInterface::class],
            //                     'encrypter' => [\Illuminate\Encryption\Encrypter::class, \Illuminate\Contracts\Encryption\Encrypter::class, \Illuminate\Contracts\Encryption\StringEncrypter::class],
            'events' => [\Illuminate\Events\Dispatcher::class, \Illuminate\Contracts\Events\Dispatcher::class],
            'files' => [\Illuminate\Filesystem\Filesystem::class],
            'filesystem' => [\Illuminate\Filesystem\FilesystemManager::class, \Illuminate\Contracts\Filesystem\Factory::class],
            'filesystem.disk' => [\Illuminate\Contracts\Filesystem\Filesystem::class],
            'filesystem.cloud' => [\Illuminate\Contracts\Filesystem\Cloud::class],
            //                     'hash' => [\Illuminate\Hashing\HashManager::class],
            //                     'hash.driver' => [\Illuminate\Contracts\Hashing\Hasher::class],
            //                     'translator' => [\Illuminate\Translation\Translator::class, \Illuminate\Contracts\Translation\Translator::class],
            //                     'log' => [\Illuminate\Log\LogManager::class, \Psr\Log\LoggerInterface::class],
            //                     'mail.manager' => [\Illuminate\Mail\MailManager::class, \Illuminate\Contracts\Mail\Factory::class],
            //                     'mailer' => [\Illuminate\Mail\Mailer::class, \Illuminate\Contracts\Mail\Mailer::class, \Illuminate\Contracts\Mail\MailQueue::class],
            //                     'auth.password' => [\Illuminate\Auth\Passwords\PasswordBrokerManager::class, \Illuminate\Contracts\Auth\PasswordBrokerFactory::class],
            //                     'auth.password.broker' => [\Illuminate\Auth\Passwords\PasswordBroker::class, \Illuminate\Contracts\Auth\PasswordBroker::class],
            //                     'queue' => [\Illuminate\Queue\QueueManager::class, \Illuminate\Contracts\Queue\Factory::class, \Illuminate\Contracts\Queue\Monitor::class],
            //                     'queue.connection' => [\Illuminate\Contracts\Queue\Queue::class],
            //                     'queue.failer' => [\Illuminate\Queue\Failed\FailedJobProviderInterface::class],
            //                     'redirect' => [\Illuminate\Routing\Redirector::class],
            //                     'redis' => [\Illuminate\Redis\RedisManager::class, \Illuminate\Contracts\Redis\Factory::class],
            //                     'redis.connection' => [\Illuminate\Redis\Connections\Connection::class, \Illuminate\Contracts\Redis\Connection::class],
            //                     'request' => [\Illuminate\Http\Request::class, \Symfony\Component\HttpFoundation\Request::class],
            //                     'router' => [\Illuminate\Routing\Router::class, \Illuminate\Contracts\Routing\Registrar::class, \Illuminate\Contracts\Routing\BindingRegistrar::class],
            //                     'session' => [\Illuminate\Session\SessionManager::class],
            //                     'session.store' => [\Illuminate\Session\Store::class, \Illuminate\Contracts\Session\Session::class],
            //                     'url' => [\Illuminate\Routing\UrlGenerator::class, \Illuminate\Contracts\Routing\UrlGenerator::class],
            //                     'validator' => [\Illuminate\Validation\Factory::class, \Illuminate\Contracts\Validation\Factory::class],
            'view' => [\Illuminate\View\Factory::class, \Illuminate\Contracts\View\Factory::class],
        ] as $key => $aliases) {
            foreach ($aliases as $alias) {
                $this->alias($key, $alias);
            }
        }

        // Scan directory for files
        //        $config = (new Collection(scandir($this->configPath())))
        //            ->filter(fn ($file) => str_ends_with($file, '.php'))
        //            ->mapWithKeys(fn ($file) => [str_replace('.php', '', $file) => include $this->configPath($file)]);
        //
        //        $this->singleton('config', fn () => new Repository($config->toArray()));
    }
}
