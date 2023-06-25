<?php
class Insert
{
    private $stmt;
    private $bind_mark;
    private $value;
    function __construct($value)
    {
        $this->stmt = $value;
    }
    public function Do($data)
    {
        try {
            if (isset($data['value'])) {
                $this->value = false;
            }
            $sql = $this->GenerateSql($data['key'], $data['value'], $data['table']);
            mysqli_stmt_prepare($this->stmt, $sql);
            if (!$data['value']) {
                try {
                    $res = mysqli_stmt_execute($this->stmt);
                } catch (Exception $e) {
                    $res = false;
                }
                goto common;
            }
            $this->stmt->bind_param($this->bind_mark, ...(array) $data['value']);
            $res = mysqli_stmt_execute($this->stmt);
        } catch (Exception $e) {
            $res = false;
        }
        common:
        if ($res && empty(mysqli_stmt_error($this->stmt))) {
            return array('statu' => true, 'reason' => null);
        } else {
            return array('statu' => false, 'reason' => '查询失败', 'err' => $e . ' ' . mysqli_stmt_error($this->stmt));
        }
    }
    private function GenerateSql($skey, $value, $table)
    {
        $key_data = '';
        foreach ($skey as $tmp) {
            if ($tmp !== end($skey)) {
                $key_data .= $tmp . ', ';
            } else {
                $key_data .= $tmp;
            }
        }
        if (!$value) {
            $sql = 'INSERT INTO ' . $table . ' ( ' . $key_data . ' ) ';
            return $sql;
        }
        $bind_data = '';
        $mark_data = '';
        $tmpnum = 0;
        $notend = true;
        $count = count($value) - 1;
        foreach ($value as $tmp) {
            if ($tmpnum == $count) {
                $notend = false;
            }
            if (is_int($tmp)) {
                $this->bind_mark .= 'i';
            } else {
                $this->bind_mark .= 's';
            }
            if ($notend) {
                $bind_data .= $tmp . ', ';
                $mark_data .= '? ,';
            } else {
                $bind_data .= $tmp;
                $mark_data .= '?';
            }
            $tmpnum++;
        }
        $sql = 'INSERT INTO ' . $table . ' ( ' . $key_data . ' ) VALUES ( ' . $mark_data . ' )';
        return $sql;
    }
}
