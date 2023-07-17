<?php
/*
author:海月(SeaMonn)
*/
class GetSqlFunc
{
    private $con;
    private $stmt;
    private $clause;
    private $RequestLocking = false;
    private $Mode = null;
    private $Module = array();
    private $requires = array();
    private $Savingopt = true;
    private $StopReason = array();
    private array $SupportedFunc = array('select', 'update', 'insert', 'delete');
    private $table;
    private $FuncEssential = array();
    function __construct($value)
    {
        $this->con = $value;
        $this->BoostingModule('stmt');
        $this->Module['stmt'] = new StmtInit();
        $tmp = $this->Module['stmt']->StmtInit($this->con);
        if ($tmp['statu']) {
            $this->stmt = $tmp['callback'];
        } else {
            return $tmp;
        }
        $this->FuncEssential['clause'] = false;
    }
    public function OperateInitializing($mode, $args)
    {
        if (!count($args) === 1) {
            return array('statu' => false, 'reason' => '数据表格式不符合规范');
        } else {
            $this->FuncEssential['table'] = $args[0];
            return array('statu' => true, 'reason' => null);
        }
    }
    public function ModeChecking($mode)
    {
        $statu = false;
        foreach ($this->SupportedFunc as $tmp) {
            if ($tmp == $mode) {
                $statu = true;
            }
        }
        if ($statu) {
            return true;
        } else {
            return false;
        }
    }
    public function EssentialPushing($mode, $data)
    {
        if (!is_array($data)) {
            $this->RequestLocking = true;
            array_push($this->StopReason, '参数必须提交为数组');
        }
        if ($mode == 'key') {
            $this->FuncEssential['key'] = $data;
        } else {
            if ($mode == 'value') {
                $this->FuncEssential['value'] = $data;
            } else {
                if ($mode == 'clause') {
                    $this->FuncEssential['clause'] = $this->GenerateClause($data);
                } else {
                    if ($mode == 'bind') {
                        $this->FuncEssential['bind'] = $data;
                    }
                }
            }
        }
        return $this;
    }
    function __call($function, $args)
    {
        $this->Mode = $function;
        $this->FuncEssential = array();
        if ($this->ModeChecking($function)) {
            $this->OperateInitializing($function, $args);
        } else {
            $this->RequestLocking = true;
            array_push($this->StopReason, '模式不存在');
        }
        return $this;
    }
    private function SubmitRequest()
    {
        if (isset($this->Mode)) {
            if ($this->RequestLocking) {
                return array('statu' => false, 'reason' => $this->StopReason);
            } else {
                $callback = $this->Module[$this->Mode]->Do($this->FuncEssential);
                //var_dump($this->Module);
                if ($this->Savingopt) {
                    unset($this->Module[$this->Mode]);
                }
                $this->Mode = '';
                return $callback;
            }
        } else {
            return array('statu' => false, 'reason' => '模式未设置');
        }
    }
    public function BoostingModule($module)
    {
        if (in_array($module, $this->requires))
        return;
        switch ($module) {
            case 'stmt':
                require 'StmtInit.php';
                break;
            case 'select':
                require 'Select.php';
                break;
            case 'update':
                require 'Update.php';
                break;
            case 'insert':
                require 'Insert.php';
                break;
            case 'delete':
                require 'Delete.php';
                break;
        }
        array_push($this->requires,$module);
    }
    public function key($data)
    {
        return $this->EssentialPushing('key', $data);
    }
    public function GenerateClause($data)
    {
        if ($this->RequestLocking) {
            return false;
        }
        $clause = '';
        foreach ($data as $tmp) {
            if ($tmp !== end($data)) {
                $clause .= $tmp . '=?, ';
            } else {
                $clause .= $tmp . '=? ';
            }
        }
        return $clause;
    }
    public function clause($clause)
    {
        return $this->EssentialPushing('clause', $clause);
    }
    public function bind($data)
    {
        return $this->EssentialPushing('bind', $data);
    }
    public function value($data)
    {
        return $this->EssentialPushing('value', $data);
    }
    public function Savingopt($boolen)
    {
        $this->Savingopt = $boolen;
    }
    public function run()
    {
        switch ($this->Mode) {
            case 'select':
                $tmp = $this->BoostingModule('select');
                $this->Module[$this->Mode] = new Select($this->stmt);
                return $this->SubmitRequest();
                break;
            case 'insert':
                $tmp = $this->BoostingModule('insert');
                $this->Module[$this->Mode] = new Insert($this->stmt);
                return $this->SubmitRequest();
                break;
            case 'update':
                $tmp = $this->BoostingModule('update');
                $this->Module[$this->Mode] = new Update($this->stmt);
                $this->FuncEssential['con'] = $this->con;
                return $this->SubmitRequest();
                break;
            case 'delete':
                $tmp = $this->BoostingModule('delete');
                $this->Module[$this->Mode] = new Delete($this->stmt);
                $this->FuncEssential['con'] = $this->con;
                return $this->SubmitRequest();
                break;
            default:
                return $this->SubmitRequest();
        }
    }
}
