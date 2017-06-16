<?php
/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/3/21
 * Time: 14:39
 */

namespace Bijou\Example\AsyncTask;


use Bijou\Interfaces\AsyncTaskInterface;

class ExportApiTask implements AsyncTaskInterface
{

    private $route;
    private $exportApi;

    public function __construct(ExportApi $exportApi, Array $route)
    {
        $this->route = $route;
        $this->exportApi = $exportApi;
    }

    /**
     * 异步执行
     * @param $from_id
     * @return mixed
     */
    public function doInBackground($from_id)
    {
        $apis = [];
        foreach ($this->route as $group => $route) {
            if (!is_int($group)) {

                foreach ($route as $r) {
                    $api = $this->format($r, $group);
                }
            } else {
                $api = $this->format($route);
            }

            if (isset($api['ignore'])) {
                continue;
            }
            $apis[] = $api;
        }

        $this->exportApi->export($apis);
    }

    private function format(Array $route, $pre = '')
    {
        list($class, $method) = $route[2];
        $requestMethod = $route[0];
        $api = $pre . $route[1];

        $reflectionMethod = new \ReflectionMethod($class, $method);
        $doc = $this->parserComments($reflectionMethod->getDocComment());
        return $doc + [
            'api' => $api,
            'method' => $requestMethod,
        ];
    }

    /**
     * 解析doc
     * @param bool $comments
     * @return array
     */
    private function parserComments($comments = false)
    {
        $return = array();
        if ($comments) {
            preg_match_all("/@ *(\w+) *([^\r\n]*?)[\r\n]/isu", $comments, $matches);
            if (isset($matches[1]) && isset($matches[2])) {
                foreach ($matches[1] as $i => $key) {
                    switch ($key) {
                        case 'param':
                            $return[$key][] = preg_split("/\s+/", trim($matches[2][$i]), 3);
                            break;
                        default:
                            $return[$key] = trim($matches[2][$i]);
                            break;
                    }
                }
            }
            preg_match("|/\*\* *\r?\n?([^\r\n]+?)[\r\n]|isu", $comments, $match);
            if (isset($match[1])) {
                $return['description'] = trim($match[1], "* \r\n");
            } else {
                $return['description'] = '';
            }
        }
        return $return;
    }


    /**
     * 任务完成后回调
     * @return mixed
     */
    public function onFinish()
    {
        unset($this->route);
    }
}