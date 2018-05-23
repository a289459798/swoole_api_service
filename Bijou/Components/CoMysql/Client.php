<?php

namespace Bijou\Components\CoMysql;

use Bijou\App;
use Bijou\Core\Exception\DbException;

/**
 * @property $app
 * @property $connected
 * @property $lastQuery
 * @property $transBegined
 * @property $tranEnable
 */
class Client
{
    /**
     * @var App
     */
    protected $app;
    /**
     * @var bool
     */
    protected $connected = false;
    /**
     * @var \mysqli
     */
    protected $resLink = null;
    /**
     * @var \mysqli_result|bool
     */
    protected $resResult = null;
    /**
     * @var bool
     */
    protected $tranEnable = false;
    /**
     * @var bool
     */
    protected $transBegined = false;
    /**
     * @var bool
     */
    protected $persistent = false;
    /**
     * @var bool
     */
    protected $queryError = false;
    /**
     * @var string
     */
    protected $errorMsg = '';
    /**
     * @var string
     */
    protected $lastQuery = '';

    /**
     * @return \mysqli
     */
    public function connect()
    {
        $host = 'localhost';
        $port = 3306;
        $user = '';
        $pass = '';
        $name = '';
        $characterSet = 'UTF8MB4';
        $timeout = 10;
        $persistent = false;
        $this->init($characterSet, $timeout);
        $port = (int)$port;
        $this->persistent = (boolean)$persistent;
        if (!\mysqli_real_connect($this->resLink, $this->escapeStr($this->persistent ? "p:{$host}" : $host), $this->escapeStr($user), $this->escapeStr($pass), $this->escapeStr($name), $port)) {
            $this->connected = false;
        } else
            $this->connected = true;
        return $this->resLink;
    }

    /**
     * @return mixed
     */
    public function isConnected()
    {
        return $this->connected;
    }

    /**
     * @param string $characterSet
     * @param int $timeout
     */
    final private function init($characterSet = 'UTF8MB4', $timeout = 1000)
    {
        $this->resLink = \mysqli_init();
        $this->option(MYSQLI_OPT_CONNECT_TIMEOUT, $timeout);
        $this->option(MYSQLI_OPT_INT_AND_FLOAT_NATIVE, 1);
        $this->option(MYSQLI_INIT_COMMAND, "SET NAMES '{$characterSet}';");
    }

    /**
     * @param $Option
     * @param $Data
     * @return bool
     */
    final private function option($Option, $Data)
    {
        return \mysqli_options($this->resLink, $Option, $Data);
    }

    /**
     * @param $name
     * @return bool
     */
    public function selectDB($name)
    {
        if (!$this->connected) return false;
        return \mysqli_select_db($this->resLink, $this->escapeStr($name));
    }

    /**
     * @return int
     */
    final public function connectErrno()
    {
        return \mysqli_connect_errno();
    }

    /**
     * @return string
     */
    final public function connectError()
    {
        return \mysqli_connect_error();
    }

    /**
     * @return bool|int
     */
    final public function getErrno()
    {
        if (!$this->connected) return false;
        return \mysqli_errno($this->resLink);
    }

    /**
     * @return bool|string
     */
    final public function getError()
    {
        if (!$this->connected) return false;
        return \mysqli_error($this->resLink);
    }

    /**
     * @param $queryStr
     * @return bool|null
     */
    final public function query($queryStr)
    {
        if (!$this->connected || !$queryStr) return false;
        $this->resResult = false;
        if ($this->tranQuery($queryStr, false))
            return $this->realQuery($queryStr);
        else {
            $this->resResult = false;
            return $this->resResult;
        }
    }

    /**
     * @param $queryStr
     * @param bool $multi
     * @return bool
     */
    final private function tranQuery($queryStr, $multi = false)
    {
        if (!empty($queryStr) && $this->tranEnable === true && $this->transBegined === false) {
            $querys = ($multi === true) ? $this->querySplit($queryStr) : array($queryStr);
            foreach ($querys as $query)
                if ((strtoupper(substr($query, 0, 6)) !== 'SELECT' && strtoupper(substr($query, 0, 3)) !== 'SET' && strtoupper(substr($query, 0, 5)) !== 'FLUSH')
                    || strtoupper(substr($query, -10)) === 'FOR UPDATE') {
                    $this->transBegined = $this->realQuery("begin");
                    $this->queryError = !$this->transBegined;
                    return $this->transBegined;
                }
            return true;
        } else
            return true;
    }

    /**
     * @param $queryStr
     * @return bool|\mysqli_result|null
     * @throws DbException
     */
    final private function realQuery($queryStr)
    {
        if (!$this->connected) return false;
        $this->lastQuery = $queryStr;
        $this->resResult = \mysqli_query($this->resLink, $queryStr);

        if ($this->resResult === false) {
            $this->queryError = true;
            $this->errorMsg = \mysqli_error($this->resLink);
            throw new DbException($queryStr);
        } else {
            return $this->resResult;
        }
    }

    /**
     * @param null $resResult
     * @return array|null
     */
    final public function fetchRow($resResult = null)
    {
        return $this->isResResult($resResult) ? \mysqli_fetch_row($resResult) : null;
    }

    /**
     * @param null $resResult
     * @return array|null
     */
    final public function fetchAssoc($resResult = null)
    {
        return $this->isResResult($resResult) ? \mysqli_fetch_assoc($resResult) : null;
    }

    /**
     * @param int $Cols
     * @param int $Rows
     * @param null $resResult
     * @return bool
     */
    final public function fetchCell($Cols = 0, $Rows = 0, $resResult = null)
    {
        $resResult = $this->getResResult($resResult);
        if ($this->isResResult($resResult)) {
            \mysqli_data_seek($resResult, $Rows);
            $Result = \mysqli_fetch_row($resResult);
            return $Result[$Cols];
        } else return false;
    }

    /**
     * @return bool
     */
    final public function transBegin()
    {
        $this->tranEnable = true;
        return true;
    }

    /**
     * @return bool
     */
    final public function tranEnd()
    {
        $this->tranEnable = false;
        return true;
    }

    /**
     * @return mixed
     */
    final public function isTransOn()
    {
        return $this->transBegined;
    }

    /**
     * @return bool
     */
    final public function transRollback()
    {
        if (!$this->connected)
            return false;
        $this->transBegined = false;
        return $this->query("rollback");
    }

    /**
     * @return bool
     */
    final public function transCommit()
    {
        if (!$this->connected)
            return false;
        if ($this->transBegined) {
            $this->transBegined = false;
            return $this->query("commit");
        } else
            return true;
    }

    /**
     * @return int|string
     */
    final public function getInsertId()
    {
        return \mysqli_insert_id($this->resLink);
    }

    /**
     * @return bool|int
     */
    final public function getAffectedRow()
    {
        if (!$this->connected) return false;
        return \mysqli_affected_rows($this->resLink);
    }

    /**
     * @param null $resResult
     * @return bool|int
     */
    final public function numRows($resResult = null)
    {
        return $this->isResResult($resResult) ? \mysqli_num_rows($resResult) : false;
    }

    /**
     * @param null $resResult
     * @return bool|int
     */
    final public function numFields($resResult = null)
    {
        return $this->isResResult($resResult) ? \mysqli_num_fields($resResult) : false;
    }


    /**
     * @param null $resResult
     * @return bool
     */
    final public function free($resResult = null)
    {
        if (!$this->connected) return false;
        $resResult = $this->getResResult($resResult);
        if ($this->isResResult($resResult)) {
            \mysqli_free_result($resResult);
            if ($resResult === $this->resResult) $this->resResult = null;
            return true;
        } else return false;
    }

    /**
     * @return bool
     */
    final public function close()
    {
        if (!$this->connected) return false;
        if ($this->isResLink($this->resLink)) {
            if (!$this->persistent) {
                $ThreadId = sprintf("%u", \mysqli_thread_id($this->resLink));
                if ($ThreadId <= PHP_INT_MAX) {
                    \mysqli_kill($this->resLink, $ThreadId);
                }
            }
            \mysqli_close($this->resLink);
            $this->resLink = null;
            $this->connected = false;
            return true;
        }
        return false;
    }

    /**
     * @param $resResult
     * @return \mysqli_result|null
     */
    final private function getResResult($resResult)
    {
        return $resResult instanceof \mysqli_result ? $resResult : $this->resResult;
    }

    /**
     * @param $resResult
     * @return bool
     */
    final  private function isResResult($resResult)
    {
        return $resResult instanceof \mysqli_result;
    }

    /**
     * @param $resLink
     * @return bool
     */
    final  private function isResLink($resLink)
    {
        return $resLink instanceof \mysqli;
    }


    /**
     * @param $queryStr
     * @return mixed|string
     */
    final private function escapeStr($queryStr)
    {
        if (!$this->connected) return str_replace(array("'", '`'), array("\\'", '\\`'), $queryStr);
        return \mysqli_real_escape_string($this->resLink, $queryStr);
    }

    /**
     * @param $queryStr
     * @return array|mixed
     */
    final private function querySplit($queryStr)
    {
        $pattern = '%\s*((?:\'[^\'\\\\]*(?:\\\\.[^\'\\\\]*)*\'|"[^"\\\\]*(?:\\\\.[^"\\\\]*)*"|/*[^*]*\*+([^*/][^*]*\*+)*/|\#.*|--.*|[^"\';#])+(?:;|$))%x';
        $matches = array();
        if (preg_match_all($pattern, $queryStr, $matches)) return $matches[1];
        return array();
    }
}
