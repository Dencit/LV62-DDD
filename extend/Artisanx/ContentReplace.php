<?php

namespace Extend\Artisanx;

/**
 * notes:
 * @author 陈鸿扬 | @date 2020/12/10 13:49
 * Class ContentReplace
 * @package Extend\thinkex
 */
class ContentReplace
{
    protected $r = "~r";
    protected $n = "~n";
    protected $t = "~t";
    protected $s = "~s";
    protected $opt;
    public $moduleName;
    public $childName;
    public $moduleDemoName;
    public $childDemoName;

    public function __construct($pathOpt, $opt = null)
    {
        $this->opt = $opt;

        $this->moduleName     = $pathOpt['module_name'];
        $this->childName      = $pathOpt['child_name'];
        $this->moduleDemoName = $pathOpt['module_demo_name'];
        $this->childDemoName  = $pathOpt['child_demo_name'];
    }


    public function forNameNormal(&$content)
    {
        $moduleName     = $this->moduleName;
        $childName      = $this->childName;
        $moduleDemoName = $this->moduleDemoName;
        $childDemoName  = $this->childDemoName;

        $content = str_replace($moduleDemoName, $moduleName, $content);
        //$content = str_replace(strtolower($moduleDemoName) , strtolower($moduleName) , $content);
        $content = str_replace(($moduleDemoName), ($moduleName), $content);

        $content = str_replace($childDemoName, $childName, $content);
        $content = str_replace(strtolower($childDemoName), strtolower($this->childNameFilter($childName)), $content);

        //对小写方法名 做驼峰转换
        $content = str_replace(strtolower('->' . $this->childNameFilter($childName)), ('->' . $this->childNameFirstFilter($childName)), $content);

        return $content;
    }

    public function forNameHump(&$content)
    {
        $moduleName     = $this->moduleName;
        $childName      = $this->childName;
        $moduleDemoName = $this->moduleDemoName;
        $childDemoName  = $this->childDemoName;

        $content = str_replace($moduleDemoName, $moduleName, $content);
        $content = str_replace(strtolower($moduleDemoName), strtolower($moduleName), $content);
        $content = str_replace(($moduleDemoName), ($moduleName), $content);

        $content = str_replace($childDemoName, $childName, $content);
        $content = str_replace(strtolower($childDemoName), ($this->childNameFirstFilter($childName)), $content);
    }

    public function forClassName(&$content)
    {
        $moduleName     = $this->moduleName;
        $childName      = $this->childName;
        $moduleDemoName = $this->moduleDemoName;
        $childDemoName  = $this->childDemoName;

        $content = str_replace($moduleDemoName, $moduleName, $content);
        //$content = str_replace(strtolower($moduleDemoName) , strtolower($moduleName) , $content);
        $content = str_replace(($moduleDemoName), ($moduleName), $content);

        $content = str_replace($childDemoName, $childName, $content);
        $content = str_replace(strtolower($childDemoName), ($this->childNameFirstFilter($childName)), $content);
    }

    public function forRouteName(&$content)
    {
        $moduleName     = $this->moduleName;
        $childName      = $this->childName;
        $moduleDemoName = $this->moduleDemoName;
        $childDemoName  = $this->childDemoName;

        //替换模块字符
        $content = str_replace($moduleDemoName, $moduleName, $content);
        //替换模块小写字符
        $content = str_replace(strtolower($moduleDemoName), strtolower($moduleName), $content);

        //替换模板字符
        $content = str_replace($childDemoName, $childName, $content);

        //过滤以上替换结果中,模块和路由名重复部分.
        $childNameRep = str_replace(strtolower($moduleName), '', strtolower($childName));
        if (empty($childNameRep)) {
            $content = str_replace('/' . strtolower($childDemoName), '', $content);
        } else {
            $content = str_replace('/' . strtolower($childDemoName), '/' . $childNameRep, $content);
        }
        //替换模板小写字符 - @开头的字符 转 驼峰
        $content = str_replace("@" . strtolower($childDemoName), "@" . $this->childNameFirstFilter($childName), $content);
    }

    public function forNameLower(&$content)
    {
        $moduleName     = $this->moduleName;
        $childName      = $this->childName;
        $moduleDemoName = $this->moduleDemoName;
        $childDemoName  = $this->childDemoName;

        $content = str_replace($moduleDemoName, $moduleName, $content);
        //$content = str_replace(strtolower($moduleDemoName) , strtolower($moduleName) , $content);
        $content = str_replace(($moduleDemoName), ($moduleName), $content);

        $content = str_replace($childDemoName, $childName, $content);
        $content = str_replace(strtolower($childDemoName), strtolower($childName), $content);

        return $content;
    }

    public function forNameHumpAndLower(&$content)
    {
        $moduleName     = $this->moduleName;
        $childName      = $this->childName;
        $moduleDemoName = $this->moduleDemoName;
        $childDemoName  = $this->childDemoName;

        $content = str_replace($moduleDemoName, $moduleName, $content);
        $content = str_replace(strtolower($moduleDemoName), strtolower($moduleName), $content);
        $content = str_replace(($moduleDemoName), ($moduleName), $content);

        //过滤模块和子模块名重复部分
        $childNameRep = str_replace(strtolower($moduleName), '', strtolower($childName));
        $content      = str_replace('/' . strtolower($childDemoName), '/' . $childNameRep, $content);

        $content = str_replace($childDemoName, $childName, $content);
        $content = str_replace(strtolower($childDemoName), strtolower($this->childNameFilter($childName)), $content);

        //$content = str_replace( ($this->childNameFirstFilter($childName).'s'), strtolower($this->childNameFilter($childName).'s') ,$content);
    }

    public function forHiddenTag(&$content)
    {
        $r     = $this->r;
        $n     = $this->n;
        $t     = $this->t;
        $s     = $this->s;
        $space = $r . $n . $s . $s . $s . $s;
        $start = '';
        $end   = '';
        if (!$this->opt['debug']) {
            $start   = "/*";
            $end     = "*/";
            $content = str_replace("//{@hidden", $start, $content);
            $content = str_replace("//@hidden}", $end, $content);
        } else {
            $content = str_replace($space . "//{@hidden", $start, $content);
            $content = str_replace($space . "//@hidden}", $end, $content);
            $content = str_replace($r . $n . "//{@hidden", $start, $content);
            $content = str_replace($r . $n . "//@hidden}", $end, $content);
        }
    }

    public function forCodeBlockTag(&$content)
    {
        $r = $this->r;
        $n = $this->n;
        $t = $this->t;
        $s = $this->s;
        //指定保留的代码块
        $codeBlockCurr = $this->opt['code_block_curr'];
        foreach ($codeBlockCurr as $ind => $val) {
            //清除掉 "模板" 注释
            $content = preg_replace("/模板/i", '', $content);
            //#
            $content = preg_replace("/(\\/\\/{@block_" . $val . "})/", '', $content);
            $content = preg_replace("/(\\/\\/{@block_" . $val . "\\/})/", '', $content);
        }
        //清除未指定保留的代码块
        $codeBlock = $this->opt['code_block'];
        foreach ($codeBlock as $ind => $val) {
            $content = preg_replace("/(\\/\\/{@block_" . $val . "}).*(\\/\\/{@block_" . $val . "\\/})/", '$1' . $r . $n . '$2', $content);
        }
    }

    public function changeForCodeBlockTag(&$content, &$changeContent)
    {
        //指定保留的代码块
        $codeBlockCurr = $this->opt['code_block_curr'];
        foreach ($codeBlockCurr as $ind => $val) {
            preg_match("/\\/\\/{@block_" . $val . "}(.*)\\/\\/{@block_" . $val . "\\//", $content, $m);
            if (isset($m[1])) {
                //清除掉 "模板" 注释
                $m[1] = preg_replace("/模板/i", '', $m[1]);
                //#
                $changeContent = preg_replace("/(\\/\\/{@block_" . $val . "}).*(\\/\\/{@block_" . $val . "\\/})/", "$1" . $m[1] . "$2", $changeContent);
            }
        }
    }

    public function forRules(&$content)
    {
        $r           = $this->r;
        $n           = $this->n;
        $t           = $this->t;
        $s           = $this->s;
        $childName   = ($this->childName);
        $repValidate = '';
        $space       = $r . $n . $t . $t;
        $res         = $this->DB($childName, "showFullColumns");
        if ($res) {
            foreach ($res as $k => $v) {

                $field   = $this->fieldFilter($v['Field']);
                $typeArr = explode(' ', $this->typeFilter($v['Type']));
                //var_dump( $field ); var_dump( $typeArr );//

                $ruleStr = '';
                switch ($typeArr[0]) {
                    default :
                        break;
                    case "int":
                        $ruleStr = 'integer|gt:0' . '|between:0,' . $typeArr[1];
                        break;
                    case "decimal":
                        $ruleStr = 'numeric|gt:0' . '|between:0,' . $typeArr[1];
                        break;
                    case "string":
                        $ruleStr = 'string' . '|between:0,' . $typeArr[1];
                        break;
                    case "text":
                        $ruleStr = 'string';
                        break;
                    case "date_time":
                        $ruleStr = 'date';
                        break;
                };
                switch ($field) {
                    default :
                        break;
                    case "status":
                        $ruleStr = 'integer|in:1,2';
                        break;
                    case "type":
                        $ruleStr = 'integer|in:1,2';
                        break;
                    case "date":
                        $ruleStr = 'date_format:Y-m-d';
                        break;
                    case "phone":
                        $ruleStr = 'number|max:11';
                        break;
                    case "sex":
                        $ruleStr = 'integer|in:0,1,2';
                        break;
                    case "ip":
                        $ruleStr = 'ip';
                        break;
                };

                $repValidate .= "\"" . $field . "\"=>\"" . $ruleStr . "\"," . $space;
            }

            $repInfo = $space . $repValidate;
            $content = preg_replace("/(\\/\\/@rules).*(\\/\\/@rules)/is", "$1" . $repInfo . "$2", $content);
        }

        return $content;
    }

    public function forMessages(&$content)
    {
        $r           = $this->r;
        $n           = $this->n;
        $t           = $this->t;
        $s           = $this->s;
        $childName   = ($this->childName);
        $repValidate = '';
        $space       = $r . $n . $t . $t;
        $res         = $this->DB($childName, "showFullColumns");
        if ($res) {

            foreach ($res as $k => $v) {

                $field    = $this->fieldFilter($v['Field']);
                $typeArr  = explode(' ', $this->typeFilter($v['Type']));
                $nullAble = $this->nullFilter($v['Null']);
                $comment  = $this->commentFilter($v['Comment'], $v['Key']);
                $comment  = explode(':', $comment)[0] ?? explode(' ', $comment)[0];
                //var_dump( $field ); var_dump( $typeArr ); var_dump( $nullAble ); var_dump( $comment );//

                switch ($typeArr[0]) {
                    default :
                        break;
                    case "int":
                        $repValidate .= "'" . $field . ".integer'=>'" . $comment . " 必须是整数'," . $space;
                        $repValidate .= "'" . $field . ".gt'=>'" . $comment . " 必须大于0'," . $space;
                        $repValidate .= "'" . $field . ".gte'=>'" . $comment . " 必须大于等于0'," . $space;
                        $repValidate .= "'" . $field . ".max'=>'" . $comment . " 超出最大值'," . $space;
                        $repValidate .= "'" . $field . ".min'=>'" . $comment . " 超出最小值'," . $space;
                        $repValidate .= "'" . $field . ".in'=>'" . $comment . " 数值超出许可范围'," . $space;
                        break;
                    case "decimal":
                        $repValidate .= "'" . $field . ".numeric'=>'" . $comment . " 必须是数字或小数'," . $space;
                        $repValidate .= "'" . $field . ".gt'=>'" . $comment . " 必须大于0'," . $space;
                        $repValidate .= "'" . $field . ".gte'=>'" . $comment . " 必须大于等于0'," . $space;
                        $repValidate .= "'" . $field . ".max'=>'" . $comment . " 超出最大值'," . $space;
                        $repValidate .= "'" . $field . ".min'=>'" . $comment . " 低于最小值'," . $space;
                        $repValidate .= "'" . $field . ".in'=>'" . $comment . " 数值超出许可范围'," . $space;
                        break;
                    case "string":
                        $repValidate .= "'" . $field . ".string'=>'" . $comment . " 包含非法字符-只能是字符串'," . $space;
                        $repValidate .= "'" . $field . ".alpha'=>'" . $comment . " 包含非法字符-只能是/字母'," . $space;
                        $repValidate .= "'" . $field . ".alpha_num'=>'" . $comment . " 包含非法字符-只能是/字母/数字'," . $space;
                        $repValidate .= "'" . $field . ".alpha_dash'=>'" . $comment . " 包含非法字符'," . $space;
                        break;
                    case "text":
                        $repValidate .= "'" . $field . ".string'=>'" . $comment . " 包含非法字符-只能是字符串'," . $space;
                        $repValidate .= "'" . $field . ".alpha'=>'" . $comment . " 包含非法字符-只能是/字母'," . $space;
                        $repValidate .= "'" . $field . ".alpha_num'=>'" . $comment . " 包含非法字符-只能是/字母/数字'," . $space;
                        $repValidate .= "'" . $field . ".alpha_dash'=>'" . $comment . " 包含非法字符'," . $space;
                        break;
                    case "date_time":
                        $repValidate .= "'" . $field . ".date'=>'" . $comment . " 日期时间格式有误'," . $space;
                        $repValidate .= "'" . $field . ".date_format'=>'" . $comment . " 自定义日期格式有误'," . $space;
                        break;
                };

                //处理 不能null的字段
                if ($nullAble == 'no') {
                    $repValidate .= "'" . $field . ".required'=>'" . $comment . " 不能为空'," . $space;
                }

                //处理 没有长度限制的字段
                switch ($typeArr[0]) {
                    default :
                        $repValidate .= "'" . $field . ".between'=>'" . $comment . " 最大长度是 " . $typeArr[1] . "'," . $space;
                        break;
                    case "longtext":
                        $repValidate .= "'" . $field . ".between'=>'" . $comment . " 超出最大长度 是4294967295'," . $space;
                        break;
                    case "text":
                        $repValidate .= "'" . $field . ".between'=>'" . $comment . " 超出最大长度 是65536'," . $space;
                        break;
                    case "date_time":
                        break;
                }

                $repValidate .= $space;
            }

            $repInfo = $space . $repValidate;
            $content = preg_replace("/(\\/\\/@messages).*(\\/\\/@messages)/is", "$1" . $repInfo . "$2", $content);
        }
        return $content;
    }

    public function forFields(&$content)
    {
        $r         = $this->r;
        $n         = $this->n;
        $t         = $this->t;
        $s         = $this->s;
        $childName = ($this->childName);
        $repInfo   = '';
        $space     = $r . $n . $t . $t;

        $res = $this->DB($childName, "showFullColumns");
        if ($res) {
            //
            $fieldStr    = implode('","', array_column($res, 'Field'));
            $fieldArrStr = '$fields = ["' . $fieldStr . '"];';
            //
            $repInfo = $space . '    ' . $fieldArrStr . $space . '    ';
            $content = preg_replace("/(\\/\\/{@field_collect).*(\\/\\/@field_collect})/is", "$1" . $repInfo . "$2", $content);
            $content = preg_replace("/(\\/\\/{@field_detail).*(\\/\\/@field_detail})/is", "$1" . $repInfo . "$2", $content);
        }

        return $content;
    }

    public function forFillAble(&$content)
    {
        $r         = $this->r;
        $n         = $this->n;
        $t         = $this->t;
        $s         = $this->s;
        $childName = ($this->childName);
        $res       = $this->DB($childName, "getTableFields");
        $repInfo   = '';
        $space     = $r . $n . $t . $t;
        if ($res) {
            foreach ($res as $k => $v) {
                switch ($v['name']) {
                    case "id":
                    case "created_at":
                    case"updated_at":
                    case"deleted_at":
                    case "create_time":
                    case"update_time":
                    case"delete_time":
                    case"is_delete":
                        break;
                    default:
                        if ($k < count($res) - 1) {
                            $repInfo .= "\"" . $v['name'] . "\"," . $space;
                        } else {
                            $repInfo .= "\"" . $v['name'] . "\"," . $space;
                        }
                        break;
                }
            }
            $repInfo = $space . $repInfo;
            $content = preg_replace("/(\\/\\/@fillAble).*(\\/\\/@fillAble)/is", "$1" . $repInfo . "$2", $content);
        }
        return $content;
    }

    public function forGuarded(&$content)
    {
        $r         = $this->r;
        $n         = $this->n;
        $t         = $this->t;
        $s         = $this->s;
        $childName = ($this->childName);
        $res       = $this->DB($childName, "getTableFields");
        $repInfo   = '';
        $space     = $r . $n . $t . $t;
        if ($res) {
            foreach ($res as $k => $v) {
                switch ($v['name']) {
                    case "id":
                    case "created_at":
                    case "create_time":
                        if ($k < count($res) - 1) {
                            $repInfo .= "\"" . $v['name'] . "\"," . $space;
                        } else {
                            $repInfo .= "\"" . $v['name'] . "\"," . $space;
                        }
                        break;
                    default:
                        break;
                }
            }
            $repInfo = $space . $repInfo;
            $content = preg_replace("/(\\/\\/@guarded).*(\\/\\/@guarded)/is", "$1" . $repInfo . "$2", $content);
        }
        return $content;
    }

    public function forCasts(&$content)
    {
        $r         = $this->r;
        $n         = $this->n;
        $t         = $this->t;
        $s         = $this->s;
        $childName = ($this->childName);
        $res       = $this->DB($childName, "getTableFields");
        $repInfo   = '';
        $space     = $r . $n . $t . $t;
        if ($res) {
            foreach ($res as $k => $v) {
                //var_dump( $v['native_type'] );
                switch ($v['native_type']) {
                    default :
                        break;
                    case "LONG":
                        $v['native_type'] = 'integer';
                        break;
                    case "LONGLONG":
                        $v['native_type'] = 'integer';
                        break;
                    case"TINY":
                        $v['native_type'] = 'integer';
                        break;
                    case "VAR_STRING":
                        $v['native_type'] = 'string';
                        break;
                    case "BLOB":
                        $v['native_type'] = 'boolean';
                        break;
                    case "TEXT":
                        $v['native_type'] = 'string';
                        break;
                    case "DATETIME":
                    case "TIMESTAMP":
                        $v['native_type'] = 'datetime';
                        break;
                    case "NEWDECIMAL":
                        $v['native_type'] = 'float';
                        break;
                };
                switch ($v['name']) {
                    default :
                        break;
                    case "status":
                        $v['native_type'] = 'integer';
                        break;
                    case "type":
                        $v['native_type'] = 'integer';
                        break;
                };
                if ($k < count($res) - 1) {
                    $repInfo .= "\"" . $v['name'] . "\"=>\"" . $v['native_type'] . "\"," . $space;
                } else {
                    $repInfo .= "\"" . $v['name'] . "\"=>\"" . $v['native_type'] . "\"," . $space;
                }
            }

            $repInfo = $space . $repInfo;
            $content = preg_replace("/(\\/\\/@casts).*(\\/\\/@casts)/is", "$1" . $repInfo . "$2", $content);
        }

        return $content;
    }

    public function forData(&$content)
    {
        $r         = $this->r;
        $n         = $this->n;
        $t         = $this->t;
        $s         = $this->s;
        $childName = ($this->childName);
        $res       = $this->DB($childName, "getTableFields");
        $repInfo   = '';
        $space     = $r . $n . $t . $t . $t;
        if ($res) {
            foreach ($res as $k => $v) {
                //var_dump( $v['native_type'] );
                switch ($v['native_type']) {
                    default :
                        $v['native_type'] = '(string)';
                        break;
                    case "LONGLONG":
                    case "LONG":
                    case"TINY":
                        $v['native_type'] = '(int)';
                        break;
                    case "VAR_STRING":
                        $v['native_type'] = '(string)';
                        break;
                    case "DATETIME":
                    case "TIMESTAMP":
                        $v['native_type'] = '(string)';
                        break;
                    case "NEWDECIMAL":
                        $v['native_type'] = '(float)';
                        break;
                };

                switch ($v['name']) {
                    default:
                        $repInfo .= "\"" . $v['name'] . "\"=>" . $v['native_type'] . "\$result->" . $v['name'] . "," . $space;
                        break;
                    case "deleted_at":
                        $repInfo .= "//" . "\"" . $v['name'] . "\"=>" . $v['native_type'] . "\$result->" . $v['name'] . "," . $space;
                        break;
                    case "delete_time":
                        $repInfo .= "//" . "\"" . $v['name'] . "\"=>" . $v['native_type'] . "\$result->" . $v['name'] . "," . $space;
                        break;
                }

            }

            $repInfo = $space . $repInfo;
            $content = preg_replace("/(\\/\\/@data).*(\\/\\/@data)/is", "$1" . $repInfo . "$2", $content);
        }

        return $content;
    }

    public function forTestInData(&$content)
    {
        $r         = $this->r;
        $n         = $this->n;
        $t         = $this->t;
        $s         = $this->s;
        $childName = ($this->childName);
        $res       = $this->DB($childName, "getTableFields");
        $repInfo   = '';
        $space     = $r . $n . $t . $t . $t;
        if ($res) {
            foreach ($res as $k => $v) {
                //var_dump( $v['native_type'] );
                switch ($v['native_type']) {
                    default :
                        $v['native_type'] = "'" . $v['name'] . "'";
                        break;
                    case "LONGLONG":
                    case "LONG":
                    case"TINY":
                        $v['native_type'] = 0;
                        break;
                    case "VAR_STRING":
                        $v['native_type'] = "'" . $v['name'] . "'";
                        break;
                    case "DATETIME":
                        $v['native_type'] = "'" . date('Y-m-d H:i:s', time()) . "'";
                        break;
                    case "TIMESTAMP":
                        $v['native_type'] = time();
                        break;
                    case "NEWDECIMAL":
                        $v['native_type'] = 0.0;
                        break;
                };

                switch ($v['name']) {
                    default:
                        $repInfo .= "\"" . $v['name'] . "\"=>" . $v['native_type'] . "," . $space;
                        break;
                    case "mobile":
                        $repInfo .= "\"" . $v['name'] . "\"=> '18500000000' ," . $space;
                        break;
                    case "photo":
                        $repInfo .= "\"" . $v['name'] . "\"=> '/none'," . $space;
                        break;
                    case "update_time":
                    case "delete_time":
                        break;
                }

            }

            $repInfo = $space . trim($repInfo, ',');
            $content = preg_replace("/(\\/\\/@in_data).*(\\/\\/@in_data)/is", "$1" . $repInfo . "$2", $content);
        }

        return $content;
    }

    public function forTestUpData(&$content)
    {
        $r         = $this->r;
        $n         = $this->n;
        $t         = $this->t;
        $s         = $this->s;
        $childName = ($this->childName);
        $res       = $this->DB($childName, "getTableFields");
        $repInfo   = '';
        $space     = $r . $n . $t . $t . $t;
        if ($res) {
            foreach ($res as $k => $v) {
                //var_dump( $v['native_type'] );
                switch ($v['native_type']) {
                    default :
                        $v['native_type'] = "'" . $v['name'] . "'";
                        break;
                    case "LONGLONG":
                    case "LONG":
                    case"TINY":
                        $v['native_type'] = 0;
                        break;
                    case "VAR_STRING":
                        $v['native_type'] = "'" . $v['name'] . "'";
                        break;
                    case "DATETIME":
                        $v['native_type'] = "'" . date('Y-m-d H:i:s', time()) . "'";
                        break;
                    case "TIMESTAMP":
                        $v['native_type'] = time();
                        break;
                    case "NEWDECIMAL":
                        $v['native_type'] = 0.0;
                        break;
                };

                switch ($v['name']) {
                    default:
                        $repInfo .= "\"" . $v['name'] . "\"=>" . $v['native_type'] . "," . $space;
                        break;
                    case "mobile":
                        $repInfo .= "\"" . $v['name'] . "\"=> '18500000000' ," . $space;
                        break;
                    case "photo":
                        $repInfo .= "\"" . $v['name'] . "\"=> '/none'," . $space;
                        break;
                    case "update_time":
                    case "delete_time":
                        break;
                }

            }

            $repInfo = $space . trim($repInfo, ',');
            $content = preg_replace("/(\\/\\/@up_data).*(\\/\\/@up_data)/is", "$1" . $repInfo . "$2", $content);
        }

        return $content;
    }

    public function forTestInList(&$content)
    {
        $r         = $this->r;
        $n         = $this->n;
        $t         = $this->t;
        $s         = $this->s;
        $childName = ($this->childName);
        $res       = $this->DB($childName, "getTableFields");
        $repInfo   = '';
        $space     = $r . $n . $t . $t . $t;
        if ($res) {
            foreach ($res as $k => $v) {
                //var_dump( $v['native_type'] );
                switch ($v['native_type']) {
                    default :
                        $v['native_type'] = "'" . $v['name'] . "'";
                        break;
                    case "LONGLONG":
                    case "LONG":
                    case"TINY":
                        $v['native_type'] = 0;
                        break;
                    case "VAR_STRING":
                        $v['native_type'] = "'" . $v['name'] . "'";
                        break;
                    case "DATETIME":
                        $v['native_type'] = "'" . date('Y-m-d H:i:s', time()) . "'";
                        break;
                    case "TIMESTAMP":
                        $v['native_type'] = time();
                        break;
                    case "NEWDECIMAL":
                        $v['native_type'] = 0.0;
                        break;
                };

                switch ($v['name']) {
                    default:
                        $repInfo .= "\"" . $v['name'] . "\"=>" . $v['native_type'] . ",";
                        break;
                    case "mobile":
                        $repInfo .= "\"" . $v['name'] . "\"=> '18500000000' ,";
                        break;
                    case "photo":
                        $repInfo .= "\"" . $v['name'] . "\"=> '/none',";
                        break;
                    case "create_time":
                        $repInfo .= "\"" . $v['name'] . "\"=> \$createTime,";
                        break;
                    case "update_time":
                    case "delete_time":
                        break;
                }

            }

            $repInfo = $space . '[' . trim($repInfo, ',') . ']' . $space;
            $content = preg_replace("/(\\/\\/@in_list).*(\\/\\/@in_list)/is", "$1" . $repInfo . "$2", $content);
        }

        return $content;
    }

    public function fieldFilter($field)
    {
        return $field;
    }

    public function typeFilter($type)
    {
        preg_match('/^([A-Z a-z]+)\\W([0-9]+|\\d+\\W+\\d+)\\.*/is', $type, $mType);
        preg_match('/^([A-Z a-z]+)$/is', $type, $nType);

        if (!empty($mType[1])) {
            switch ($mType[1]) {
                default :
                    return $type;
                    break;
                case "int":
                case "bigint":
                case"tinyint":
                    return "int " . $mType[2];
                    break;
                case "char":
                case "varchar":
                    return "string " . $mType[2];
                    break;
                case "text":
                case "longtext":
                    return "text " . $mType[2];
                    break;
                case "decimal":
                    return "decimal " . $mType[2];
                    break;
            }
        }

        if (!empty($nType[1])) {
            switch ($nType[1]) {
                default :
                    return $type;
                    break;
                case "datetime":
                    return "date_time";
                    break;
                case "timestamp":
                    return "date_time";
                    break;
            }
        }

        return $type;
    }

    public function nullFilter($nullStr)
    {
        switch ($nullStr) {
            default :
                return '';
                break;
            case "NO":
                return "yes";
                break;
            case "YES":
                return "no";
                break;
        }
    }

    public function defaultFilter($default, $key = null)
    {
        if ($default == null) {
            $default = "";
        }
        if ($key == "PRI") {
            $default = "auto_increment";
        }

        return $default;
    }

    public function commentFilter($comment, $key = null)
    {
        if (empty($comment) && $key == "PRI") {
            $comment = "主键";
        }

        return $comment;
    }

    function DB($childName, $type = null)
    {
        $opt = $this->opt;

        if ($opt) {
            $DB        = new PdoDB($opt);
            $childName = $this->childNameFilter($childName);

            switch ($type) {
                default:
                    $res = $DB->query($type);
                    break;
                case "getTableFields":
                    $res = $DB->getTableFields($childName);
                    break;
                case "showFullColumns":
                    $res = $DB->showFullColumns($childName);
                    break;
                case "showTableStatus":
                    $res = $DB->showTableStatus($childName);
                    break;
            }
            return $res;
        }

        return null;
    }

    //“_”拆分$childName
    function childNameFilter($childName)
    {
        $newChildName = '';
        for ($i = 1; $i < 10; $i++) {
            preg_match("/([A-Z]{1}[a-z 0-9]+){" . $i . "}/", $childName, $m);
            if (!empty($m[1])) {
                $newChildName .= $m[1] . '_';
            } else {
                $newChildName = preg_replace("/_$/", '', $newChildName);
                break;
            }
        }
        return strtolower($newChildName);
    }

    //首字母大写 字符 转 驼峰
    function childNameFirstFilter($childName)
    {
        $newChildName = '';
        $first        = 0;
        for ($i = 1; $i < 10; $i++) {
            preg_match("/([A-Z]{1}[a-z 0-9]+){" . $i . "}/", $childName, $m);
            if (!empty($m[1])) {
                $first++;
                if ($first < 2) {
                    $newChildName .= strtolower($m[1]);
                } else {
                    $newChildName .= $m[1];
                }
            }
        }
        return $newChildName;
    }

    //首字母大写 字符 转 破折号连接字符
    function childNameDashFilter($childName)
    {
        $newChildName = '';
        $first        = 0;
        $leng         = strlen($childName);
        for ($i = 1; $i <= $leng; $i++) {
            preg_match("/([A-Z]{1}[a-z 0-9]+){" . $i . "}/", $childName, $m);
            if (!empty($m[1])) {
                $first++;
                if ($first < 2) {
                    $newChildName = strtolower($m[1]);
                } else {
                    $newChildName .= '-' . strtolower($m[1]);
                }
            }
        }
        return $newChildName;
    }

}
