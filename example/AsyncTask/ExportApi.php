<?php
/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/3/21
 * Time: 15:59
 */

namespace Bijou\Example\AsyncTask;


use Bijou\Interfaces\ExportApiInterface;

class ExportApi implements ExportApiInterface
{

    /**
     * @param array $apis
     * @return mixed
     */
    public function export(Array $apis)
    {
        $html = '';
        foreach ($apis as $k => $v) {
            $api = $v['api'];
            $hash = strtr($api, '/{}', '___');
            $desc = '&#12288;<span>' . (isset($v['description']) ? $v['description'] : '') . '</span>';

            $method = '<h5><i>Request Method:</i></h5>';
            $method .= '<div class="parameters">' . $v['method'] . '</div>';

            if (!empty($v['param'])) {
                $inputs = '<h5><i>Input Parameters:</i></h5>';
                $inputs .= '<div class="parameters">';
                foreach ($v['param'] as $p) {
                    $p += array('', '', '', '');
                    $inputs .= "{$p[0]} <b>" . str_replace('$', '', $p[1]) . "</b> {$p[2]}<br>";
                }
                $inputs .= '</div>';
            } else {
                $inputs = '<h5><i>No Parameter</i></h5>';
            }
            if (!empty($v['return'])) {
                $outputs = '<h5><i>Output Parameters:</i></h5>';
                $outputs .= '<div class="parameters">';
                $outputs .= $v['return'];
                $outputs .= '</div>';
            } else {
                $outputs = '';
            }
            $html .= "
              <li id='{$api}'>
                <a href='javascript:void(0);' onclick='toggle(\"toggle_{$hash}\");'>{$api} {$desc}</a>
                <div id='toggle_{$hash}' class='desc'>                  
                  {$method}
                  {$inputs}
                  {$outputs}
                </div>
              </li>";
        }
        file_put_contents(__DIR__ . '/../apiHelper.html', "
          <html>
          <head>
          <title> Helper </title>
          <meta http-equiv='content-type' content='text/html;charset=utf-8'>
          <style type='text/css'>
            BODY { color: #000000; background-color: white; font-family: Verdana; margin:0px;}        
            A:link { color: #336699; font-weight: bold; text-decoration: none; }
            A:visited { color: #6699cc; font-weight: bold; text-decoration: none; }
            A:active { color: #336699; font-weight: bold; text-decoration: underline; }
            A:hover { color: #cc3300; font-weight: bold; text-decoration: none; }
            P { color: #000000; margin-top: 0px; margin-bottom: 12px; font-family: Verdana; }
            pre { background-color: #efefdc; padding: 5px; font-family: Courier New; font-size: 11px; border: 1px #f0f0e0 solid; margin:0; }
            ul { margin-top: 10px; margin-left: 20px; }
            li { margin-top: 10px; color: #000; list-style-type:circle;}
            .heading1 { color: #ffffff; font-family: Tahoma; font-size: 26px; background-color: #003366; padding:10px 0px 8px 20px;}
            #content {font-size: .80em; padding:8px 16px;}
            .intro { margin-left: 10px; }
            h5 {margin:5px 0px 2px 0px;font-size:12px;}
            h5 i {color:#888;}
            h5 p {color:#666;}
            a span {color:#777;}
            pre { background-color:#efefdc; color:#555; padding: 5px; font-family: Courier New; font-size: 11px; border: 1px #999 dashed; margin:0; }
            pre b {color:#992222;}
            .parameters {color:#666;font-size:11px;padding:5px;line-height:150%;border:1px #999 solid;background-color: #f2f2f2;}
            .desc {display:none;color:#333;}
          </style>
          <script>
            function toggle(div) {
              var d = document.getElementById(div);
              if(d.style.display=='block')
                d.style.display = 'none';
              else
                d.style.display = 'block';
            }
          </script>
          </head>
          <body>
          <p class='heading1'> Helper </p>
          <div id='content'>
          <p class='intro'></p>
          <ul>" . $html .
            "</ul>
          </div>
          </body>
          </html>
          ");
    }

}