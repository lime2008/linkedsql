<?php
/*
author:海月(SeaMonn)
*/
namespace GetSqlFunc;
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
    function __construct()
    {
        $this->FuncEssential['clause'] = false;
    }
    public function Connect_Loader($con){
        $this->con = $con;
        $this->stmt = mysqli_stmt_init($this->con);
    }
    public function OperateInitializing($mode, $args)
    {
        if (!count($args) === 1) {
            return array('status' => false, 'reason' => '数据表格式不符合规范');
        } else {
            $this->FuncEssential['table'] = $args[0];
            return array('status' => true, 'reason' => null);
        }
    }
    public function ModeChecking($mode)
    {
        $status = false;
        foreach ($this->SupportedFunc as $tmp) {
            if ($tmp == $mode) {
                $status = true;
            }
        }
        if ($status) {
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
                return array('status' => false, 'reason' => $this->StopReason);
            } else {
                $this->Module[$this->Mode]->Stmt_Loader($this->stmt);
                $callback = $this->Module[$this->Mode]->Do($this->FuncEssential);
                //var_dump($this->Module);
                if ($this->Savingopt) {
                    unset($this->Module[$this->Mode]);
                }
                $this->Mode = '';
                return $callback;
            }
        } else {
            return array('status' => false, 'reason' => '模式未设置');
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
                $clause .= $tmp . '=? AND ';
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
                $this->Module[$this->Mode] = new Select\Select($this->stmt);
                return $this->SubmitRequest();
                break;
            case 'insert':
                $tmp = $this->BoostingModule('insert');
                $this->Module[$this->Mode] = new Insert\Insert($this->stmt);
                return $this->SubmitRequest();
                break;
            case 'update':
                $tmp = $this->BoostingModule('update');
                $this->Module[$this->Mode] = new Update\Update($this->stmt);
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
