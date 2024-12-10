<?php

namespace Bow\Gravatar;

use Bow\Gravatar\InvalidEmailException;
use Bow\Validation\Validator;

class Gravatar
{
    /**
     * Gravatar base url
     *
     * @var string
     */
    private $publicBaseUrl = 'https://www.gravatar.com/avatar/';

    /**
     * Gravatar secure base url
     *
     * @var string
     */
    private $secureBaseUrl = 'https://secure.gravatar.com/avatar/';

    /**
     * Email address to check
     *
     * @var string
     */
    private $email;

    /**
     * @var array
     */
    private $config;

    /**
     * @var string
     */
    private $config_name;

    /**
     * @var string|false
     */
    private $fallback = false;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->config_name = 'default';
    }

    /**
     * Override the default image fallback set in the config.
     * Can either be a public URL to an image or a valid themed image.
     * For more info, visit http://en.gravatar.com/site/implement/images/#default-image
     *
     * @param string $fallback
     * @return $this
     */
    public function fallback(string $fallback)
    {
        // Gravatar changed mm to mp.
        // This way we make sure everything keeps working
        if ($fallback === 'mm') {
            $fallback = 'mp';
        }

        if (
            filter_var($fallback, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED)
            || in_array($fallback, ['mp', 'identicon', 'monsterid', 'wavatar', 'retro', 'robohash', 'blank'])
        ) {
            $this->fallback = $fallback;
        } else {
            $this->fallback = false;
        }

        return $this;
    }

    /**
     * Check if Gravatar has an avatar for the given email address
     *
     * @param string $email
     * @return bool
     * @throws InvalidEmailException
     */
    public function exists(string $email)
    {
        $this->checkEmail($email);

        $this->email = $email;

        $this->setConfig(['fallback' => 404]);

        $headers = @get_headers($this->buildUrl());

        return (bool) strpos($headers[0], '200');
    }

    /**
     * Get the gravatar url
     *
     * @param string $email
     * @param string|array|null $config
     * @return string
     * @throws InvalidEmailException
     */
    public function get(string $email, string $config = 'default')
    {
        $this->checkEmail($email);

        $this->setConfig($config);

        $this->email = $email;

        return $this->buildUrl();
    }

    /**
     * Helper function for setting the config based on either:
     * 1. The name of a config group
     * 2. A custom array
     * 3. The default group in the config
     *
     * @param string|array|null $group
     * @return $this
     */
    private function setConfig(string|array|null $config = null)
    {
        if (is_string($config) && $config != 'default') {
            $this->config_name = $config;
        } else {
            $this->config[$this->config_name] = array_merge($this->config[$this->config_name], $config);
        }

        return $this;
    }

    /**
     * Helper function to retrieve config settings.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    protected function getConfigByKey(string $key, ?string $default = null)
    {
        $config = $this->config[$this->config_name];

        return array_key_exists($key, $config) ? $config[$key] : $default;
    }

    /**
     * Helper function to md5 hash the email address
     *
     * @return string
     */
    private function hashEmail()
    {
        return md5(strtolower(trim($this->email)));
    }

    /**
     * @return string
     */
    private function getExtension()
    {
        $extension = $this->getConfigByKey('forceExtension');

        return $extension ? '.' . $extension : '';
    }

    /**
     * @return string
     */
    private function buildUrl()
    {
        $url  = $this->getConfigByKey('secure') === true ? $this->secureBaseUrl : $this->publicBaseUrl;
        $url .= $this->hashEmail();
        $url .= $this->getExtension();
        $url .= $this->getUrlParameters();

        return $url;
    }

    /**
     * @return string
     */
    private function getUrlParameters()
    {
        $build = array();

        foreach (get_class_methods($this) as $method) {
            if (substr($method, -strlen('Parameter')) !== 'Parameter') {
                continue;
            }

            if ($called = call_user_func([$this, $method])) {
                $build = array_replace($build, $called);
            }
        }

        return '?' . http_build_query($build);
    }

    /**
     * Check if the provided email address is valid
     *
     * @param string $email
     * @throws InvalidEmailException
     */
    private function checkEmail(string $email)
    {
        $validator = Validator::make(['email' => $email], ['email' => 'required|email']);

        if ($validator->fails()) {
            throw new InvalidEmailException('Please specify a valid email address');
        }
    }
}
