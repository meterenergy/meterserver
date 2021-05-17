<?php

class Model extends Database
{
    protected $model;
    protected $table;
    private $where_clause;
    private $where_clause_value;

    private function restruct($data)
    {
        $model = new Model();
        $prop = $this->Create($data);
        foreach ($prop as $key => $value) {
            $model->{$key} = $value;
        }
        return $model;
    }

    public function Create($data)
    {
        foreach ($this as $key => $value) {
            if (array_key_exists($key, $data)) {
                $this->{$key} = $data[$key];
            }
        }
        return $this->model;
    }

    public function where($where_clause, $value)
    {
        $this->where_clause = $where_clause;
        $this->where_clause_value = $value;
        return $this;
    }

    public function find($id)
    {
        $this->id = $id;
        $this->get();
        return $this;
    }

    protected function insert($data)
    {
        $con = $this->connection();
        $cols = "";
        $rows = "";

        foreach ($data as $col => $row) {
            $cols .= "`" . $col . "`" . ",";
            $rows .= "'" . $row . "'" . ",";
        }
        $cols = substr($cols, 0, -1);
        $rows = substr($rows, 0, -1);

        $sql = "INSERT INTO `{$this->table}` ($cols) VALUES ($rows)";
        //echo $sql;
        $sqlQuery = mysqli_query($this->connection(), $sql);
        //echo mysqli_error($con);

        return $sqlQuery;
    }

    public function update()
    {
        $updateQuery = "";

        foreach ($this as $col => $row) {
            if ($col != 'model' && $col != 'table' && $col != 'where_clause' && $col != 'where_clause_value') {
                $updateQuery .= "`" . $col . "` = '" . $row . "',";
            }
        }
        $updateQuery = substr($updateQuery, 0, -1);

        $sql = "UPDATE `{$this->table}` SET $updateQuery WHERE `id` = '{$this->id}'";
        $sqlQuery = mysqli_query($this->connection(), $sql);

        return $sqlQuery;
    }

    public function get()
    {
        try {
            $table = array();
            if ($this->id) {
                $sql = "SELECT * FROM `{$this->table}` WHERE `id` = '{$this->id}'";
                $sqlQuery = mysqli_query($this->connection(), $sql);
                if ($sqlQuery && mysqli_num_rows($sqlQuery)) {
                    while ($row = mysqli_fetch_assoc($sqlQuery)) {
                        array_push($table, $this->restruct($row));
                    }
                }

                return isset($table[0]) && $table[0];
            }
            elseif ($this->where_clause) {
                $sql = "SELECT * FROM `{$this->table}` WHERE `{$this->where_clause}` = '{$this->where_clause_value}'";
            }
            else {
                $sql = "SELECT * FROM `{$this->table}`";
            }

            $sqlQuery = mysqli_query($this->connection(), $sql);
            if ($sqlQuery && mysqli_num_rows($sqlQuery)) {
                // while ($row = mysqli_fetch_object($sqlQuery)) {
                //     array_push($table, $row);
                // }
                while ($row = mysqli_fetch_assoc($sqlQuery)) {
                    array_push($table, $this->restruct($row));
                }
            }

            return $table;
        } catch (\Throwable $th) {
            return;
        }
    }
}

?>