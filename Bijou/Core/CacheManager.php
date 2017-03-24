<?php
/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/3/23
 * Time: 14:58
 */

namespace Bijou\Core;

define("BIJOU_CACHE_FILE", 1);

class CacheManager
{
    private $cacheDir;
    private $expire;
    private $mode;

    public function __construct($cacheDir, $expire = 3600, $mode = BIJOU_CACHE_FILE)
    {
        $this->cacheDir = rtrim($cacheDir, '/');
        $this->expire = $expire;
        $this->mode = $mode;


    }

    /**
     * 读取缓存
     * @param $api
     * @param $expire
     * @return bool
     */
    public function readCache($api, $expire)
    {
        $cacheFile = join("/", $this->getCacheFile($api));
        if (file_exists($cacheFile)) {
            $expire = true === $expire ? $this->expire : $expire;
            $mtime = filemtime($cacheFile);
            if (time() < ($mtime + $expire)) {
                $data = file_get_contents($cacheFile);
                return $this->unpack($data);
            }
        }
        return false;
    }

    /**
     * @param $api
     * @param $data
     */
    public function writeCache($api, $data)
    {
        list($dir, $file) = $this->getCacheFile($api);
        if (!is_dir($dir)) {
            mkdir($dir, 0644, true);
        }
        \Swoole\Async::writeFile($dir . '/' . $file, $this->pack($data));
    }

    private function pack($data)
    {
        if (function_exists('\Swoole\Serialize::pack')) {
            return \Swoole\Serialize::pack($data);
        } else if (function_exists('\swSerialize::pack')) {
            return \swSerialize::pack($data);
        }
        return serialize($data);
    }

    private function unpack($data)
    {
        if (function_exists('\Swoole\Serialize::unpack')) {
            return \Swoole\Serialize::unpack($data);
        } else if (function_exists('\swSerialize::pack')) {
            return \swSerialize::unpack($data);
        }
        return unserialize($data);
    }

    private function getCacheFile($api)
    {
        $md5 = md5($api);
        return [$this->cacheDir . '/' . substr($md5, 0, 8) . '/' . substr($md5, 8, 8), substr($md5, 16)];
    }
}