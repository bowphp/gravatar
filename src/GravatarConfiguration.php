<?php

namespace Bow\Gravatar;

use Bow\Configuration\Configuration;
use Bow\Configuration\Loader as ConfigurationLoader;

class GravatarConfiguration extends Configuration
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function create(ConfigurationLoader $config): void
    {
        $gravatar = (array) $config['gravatar'];

        $config_path = __DIR__ . '/../config/gravatar.php';

        $gravatar = array_merge(
            require $config_path,
            $gravatar
        );

        $config['gravatar'] = $gravatar;

        $this->container->factory('gravatar', function () use ($config) {
            return new Gravatar($config['gravatar']);
        });
    }

    public function run(): void
    {
        $this->container->make('gravatar');
    }
}
