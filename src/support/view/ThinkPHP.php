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

namespace support\view;

use think\Template;
use Webman\View;

/**
 * Class Blade
 * @package support\view
 */
class ThinkPHP implements View
{
    /**
     * @var array
     */
    protected static $vars = [];

    /**
     * Assign.
     * @param string|array $name
     * @param mixed $value
     */
    public static function assign($name, $value = null)
    {
        static::$vars = \array_merge(static::$vars, \is_array($name) ? $name : [$name => $value]);
    }

    /**
     * Render.
     * @param string $template
     * @param array $vars
     * @param string|null $app
     * @return false|string
     */
    public static function render(string $template, array $vars, string $app = null)
    {
        $request = \request();
        $plugin = $request->plugin ?? '';
        $app = $app === null ? $request->app : $app;
        $configPrefix = $plugin ? "plugin.$plugin." : '';
        $viewSuffix = \config("{$configPrefix}view.options.view_suffix", 'html');
        $baseView_path = $plugin ? \base_path() . "/plugin/$plugin/app" : \app_path();
        $viewPath = $app === '' ? "$baseView_path/view/" : "$baseView_path/$app/view/";
        $defaultOptions = [
            'view_path' => $viewPath,
            'cache_path' => \runtime_path() . '/views/',
            'view_suffix' => $viewSuffix
        ];
        $options = $defaultOptions + \config("{$configPrefix}view.options", []);
        $views = new Template($options);
        \ob_start();
        $vars = \array_merge(static::$vars, $vars);
        $views->fetch($template, $vars);
        $content = \ob_get_clean();
        static::$vars = [];
        return $content;
    }
}
