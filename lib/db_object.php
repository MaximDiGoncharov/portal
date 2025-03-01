<?php


class db_object {

    private string $table;
    private array $all;
    private array $main;
    private array $int_convert;
    private string $index;
    function __construct(string $table,
            array $all,
            array $main = null, array $int_convert = []) {
        $this->table = $table;
        $this->all = $all;
        if (is_null($main)) {
            $main = &$all;
        }
        $this->main = $main;
        $this->index = $this->table . '_id';
        $this->int_convert = $int_convert;
    }

    public function _add($param) {
        return addGenerator($this->table, $param, $this->all, $this->main, false, $this->int_convert);
    }

    public function _up($param, $cond = '') {
        return updateGenerator($this->table, $param, $this->all, $this->index, false, $cond, $this->int_convert);
    }

    public function _del($id) {
        return delGenerator($this->table, $id, $this->index);
    }

    //_get for old vers
    public function _get($id) {
        return getGenerator($this->table, $id, $this->index);
    }

    public function _list() {
        return listGenerator($this->table);
    }

    public function _cnt() {
        return countGenerator($this->table);
    }

    public function _admin_router($act = null, $input_id = null, $post = null) {
        if (is_null($act)) {
            $act = filter_input(INPUT_GET, 'act');
        }

        if (is_null($input_id)) {
            $input_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        }

        if (is_null($post)) {
            $post = filter_input_array(INPUT_POST);
        }


        if (sizeof($post)) {
            if ($act == 'edit') {
                $fname = 'Change';

                if (!method_exists($x, $fname)) {
                    $fname = '_up';
                }

                $this->fname($post);
            }

            if ($act == 'add') {
                $fname = 'Add';

                if (!method_exists($x, $fname)) {
                    $fname = '_add';
                }

                $input_id = $this->fname($post);
                if ($input_id) {
                    $act = 'edit';
                }
            }
        }

        if ($act == 'edit') {
            $fname = 'Get';

            if (!method_exists($x, $fname)) {
                $fname = '_get';
            }

            $this->cur = $this->fname($input_id);
        } else {
            if ($act == 'del') {
                $fname = 'Del';

                if (!method_exists($x, $fname)) {
                    $fname = '_del';
                }

                $this->fname($input_id);
            }
        }

        return in_array($act, ['add', 'edit']) ? 'edit' : 'tpl';
    }

}
