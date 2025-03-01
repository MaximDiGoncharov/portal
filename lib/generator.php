<?php

function addGeneratorInt($table, $param, $fields, $reqFields = null, $ret_rows = false) {
    $vals = '';
    $keys = '';
    $id = 0;

    if (is_null($reqFields))
        $reqFields = $fields;

    for ($i = 0; isset($fields[$i]); $i++) {
        if (isset($param[$fields[$i]])) {
            $vals .= (int) $param[$fields[$i]] . ',';
            $keys .= $fields[$i] . ',';
        } else {
            if (in_array($fields[$i], $reqFields)) {
                logger::add('Fill required fields. ' . $table);
                return $id;
            }
        }
    }

    if ($vals != '') {
        $vals = substr($vals, 0, -1);
        $keys = substr($keys, 0, -1);

        $query = 'INSERT INTO ' . $table . '(' . $keys . ') VALUES(' . $vals . ')';
        db($query);


        $id = $ret_rows ? affected_rows() : db_id();
    }

    return $id;
}

function addGenerator($table, $param, $fields, $reqFields = null, $no_html_trim = false) {
    $vals = '';
    $keys = '';
    $id = 0;

    for ($i = 0; isset($fields[$i]); $i++) {
        if (isset($param[$fields[$i]])) {

            if ($no_html_trim) {
                $param[$fields[$i]] = htmlspecialchars($param[$fields[$i]]);
            }
            $vals .= db_safe($param[$fields[$i]], DB_SAFE_QUOTAS_COMMA_END);

            $keys .= $fields[$i] . ',';
        } else {
            if (in_array($fields[$i], $reqFields)) {
                logger::add('Fill required fields. ' . $table);
                return $id;
            }
        }
    }

    if ($vals != '') {
        $vals = substr($vals, 0, -1);
        $keys = substr($keys, 0, -1);

        $query = 'INSERT INTO ' . $table . '(' . $keys . ') VALUES(' . $vals . ')';
        db($query);
        $id = db_id();
    }

    return $id;
}

function updateGenerator($table, $param, $fields, $idName = '', $no_html_trim = false) {

    if (!$idName)
        $idName = $table . 'ID';
    $param[$idName] = intval($param[$idName]);
    if ($param[$idName]) {

        $vals = '';
        for ($i = 0; isset($fields[$i]); $i++) {
            if (isset($param[$fields[$i]])) {

                if ($no_html_trim) {
                    $param[$fields[$i]] = htmlspecialchars($param[$fields[$i]]);
                }

                $vals .= $fields[$i] . '=' . db_safe($param[$fields[$i]], DB_SAFE_QUOTAS_COMMA_END);
            }
        }
        if ($vals != '') {
            $vals = substr($vals, 0, -1);
            $query = 'UPDATE ' . $table . ' SET ' . $vals . ' WHERE ' . $idName . '=' . $param[$idName] . ' LIMIT 1';

            db($query);
        }
    }

    return $param[$idName];
}

function delGenerator($table, $id, $field = '') {
    if ($id = intval($id)) {
        if (!$field) {
            $field = $table . 'ID';
        }

        $query = 'DELETE FROM `' . $table . '` WHERE ' . $field . '=' . $id . ' LIMIT 1';
        db($query);
    }
}

function getGenerator($table, $id, $field = '') {
    $id = intval($id);
    if ($id) {
        if (!$field) {
            $field = $table . 'ID';
        }

        $query = 'SELECT * FROM `' . $table . '` WHERE ' . $field . '=' . $id . ' LIMIT 1';

        $r = mysqli_fetch_assoc(db($query));
        return $r;
    }
}

function adminRouter($name, $act = null, $input_id = null) {
    if (is_null($act)) {
        $act = filter_input(INPUT_GET, 'act');
    }

    if (is_null($input_id)) {
        $input_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    }


    if (sizeof($_POST)) {
        if ($act == 'del') {
            if (function_exists($fname = ($name . 'Del')))
                $fname($input_id);
        }
        if ($act == 'edit') {
            if (function_exists($fname = ($name . 'Change')))
                $fname($_POST);
        }

        if ($act == 'add') {
            if (function_exists($fname = ($name . 'Add'))) {
                if ($input_id = $fname($_POST))
                    $act = 'edit';
            }
        }
    }

    if ($act == 'edit')
        if (function_exists($fname = ($name . 'Get'))) {
            $GLOBALS['currentUser'] = $fname($input_id);
        }
}

//function loadMyEnv

function generateParamFromfields($addName, $fields, $outerParam) {
    $param = array();
    for ($i = 0; isset($fields[$i]); $i++) {
        if ($outerParam[$addName . $fields[$i]]) {
            $param[$fields[$i]] = $outerParam[$addName . $fields[$i]];
        }
    }
    return $param;
}

function listGenerator($table, array $filters = null) {
    $sql = 'SELECT * FROM ' . $table;

    $r = mysqli_fetch_all(db($sql), MYSQLI_ASSOC);
    return $r;
}

function countGenerator($table, array $filters = null) {
    $sql = 'SELECT COUNT(*) FROM ' . $table;

    $r = mysqli_fetch_row(db($sql))[0];
    return $r;
}

function generateEnumValues($table, $field) {

    $r = db('SHOW COLUMNS FROM `' . $table . '` LIKE "' . $field . '"');

    $row = mysqli_fetch_assoc($r);

    if (substr($row['Type'], 0, 4) !== 'enum')
        return false;


    eval('$vlist = array' . substr($row['Type'], 4) . ';');

    return $vlist;
}
