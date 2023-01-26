<?php
/**
 * This file is part of webman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link      http://www.workerman.net/
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

namespace Webman;


class Middleware
{

    /**
     * @var array
     */
    protected static $instances = [];

    /**
     * @param array $allMiddlewares
     * @param string $plugin
     * @return void
     */
    public static function load($allMiddlewares, string $plugin = '')
    {
        if (!\is_array($allMiddlewares)) {
            return;
        }
        foreach ($allMiddlewares as $appName => $middlewares) {
            if (!\is_array($middlewares)) {
                throw new \RuntimeException('Bad middleware config');
            }
            foreach ($middlewares as $className) {
                if (\method_exists($className, 'process')) {
                    static::$instances[$plugin][$appName][] = [$className, 'process'];
                } else {
                    // @todo Log
                    echo "middleware $className::process not exsits\n";
                }
            }
        }
    }

    /**
     * @param string $plugin
     * @param string $appName
     * @param bool $withGlobal_middleware
     * @return array|mixed
     */
    public static function getMiddleware(string $plugin, string $appName, bool $withGlobal_middleware = true)
    {
        $globalMiddleware = $withGlobal_middleware && isset(static::$instances[$plugin]['']) ? static::$instances[$plugin][''] : [];
        if ($appName === '') {
            return \array_reverse($globalMiddleware);
        }
        $appMiddleware = static::$instances[$plugin][$appName] ?? [];
        return \array_reverse(\array_merge($globalMiddleware, $appMiddleware));
    }

    /**
     * @deprecated
     * @return void
     */
    public static function container($_)
    {

    }
}
