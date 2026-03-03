<?php

namespace Extend\Artisanx;

/**
 * notes:
 * @author 陈鸿扬 | @date 2020/12/10 13:49
 * Class ThinkEx
 * @package Extend\thinkex
 */
class Artisanx
{
    protected $r = "~r";
    protected $n = "~n";
    protected $t = "~t";
    protected $s = "~s";
    protected $opt;
    protected $moduleDomeName;
    protected $childDemoName;

    public function __construct($option)
    {
        $this->moduleDomeName = "Demo";
        $this->childDemoName  = ucwords("Sample");
        $this->opt            = $option;
    }

    function DB($childName, $type = null)
    {
        $opt = $this->opt;

        $DB        = new PdoDB($opt);
        $childName = $this->childNameFilter($childName);

        switch ($type) {
            default:
                $res = $DB->query($type);
                break;
            case "getTableFields":
                $res = $DB->getTableFields($childName);
                break;
        }
        return $res;
    }


    function console($msg, $color = null)
    {
        switch ($color) {
            default:
                $first = "\033[0m";
                break;
            case "red":
                $first = "\033[31m";
                break;
            case "lemon":
                $first = "\033[32m";
                break;
            case "yellow":
                $first = "\033[33m";
                break;
            case "blue":
                $first = "\033[34m";
                break;
            case "purple":
                $first = "\033[35m";
                break;
            case "green":
                $first = "\033[36m";
                break;
        }
        flush();
        print($first . $msg . "\n\033[0m");
    }

    function readyToReplace(&$fileContent)
    {
        $r           = $this->r;
        $n           = $this->n;
        $t           = $this->t;
        $s           = $this->s;
        $fileContent = preg_replace("/\r+/is", $r, $fileContent);
        $fileContent = preg_replace("/\n+/is", $n, $fileContent);
        $fileContent = preg_replace("/\t+/is", $t, $fileContent);
        $fileContent = preg_replace("# #is", $s, $fileContent);
        return $fileContent;
    }

    function readyToContent(&$fileContent)
    {
        $r           = $this->r;
        $n           = $this->n;
        $t           = $this->t;
        $s           = $this->s;
        $fileContent = preg_replace("/(" . $r . ")/is", "\r", $fileContent);
        $fileContent = preg_replace("/(" . $n . ")/is", "\n", $fileContent);
        $fileContent = preg_replace("/(" . $t . ")/is", "\t", $fileContent);
        $fileContent = preg_replace("/(" . $s . ")/is", " ", $fileContent);
        return $fileContent;
    }

    function fileEditCopy($option, $callfunc)
    {
        $path    = $option['get_path'];
        $putPath = $option['put_path'];
        $content = file_get_contents($path);
        $this->readyToReplace($content);

        $moduleDomeName             = $this->moduleDomeName;
        $childDemoName              = $this->childDemoName;
        $option['module_demo_name'] = $moduleDomeName;
        $option['child_demo_name']  = $childDemoName;
        $content                    = $callfunc($option, $content);

        $this->readyToContent($content);
        file_put_contents($putPath, $content);
        return true;
    }

    function fileEditChange($option, $callfunc)
    {
        $demoPath   = $option['get_path'];
        $changePath = $putPath = $option['put_path'];

        //模板内容
        $demoContent = file_get_contents($demoPath);
        $this->readyToReplace($demoContent);

        //待修改内容
        $changeContent = file_get_contents($changePath);
        $this->readyToReplace($changeContent);

        $moduleDomeName             = $this->moduleDomeName;
        $childDemoName              = $this->childDemoName;
        $option['module_demo_name'] = $moduleDomeName;
        $option['child_demo_name']  = $childDemoName;

        //非空才执行 - 可通过闭包 控制是否写入
        $putContent = $callfunc($option, $demoContent, $changeContent);
        if (!empty($putContent)) {
            $this->readyToContent($putContent);
            file_put_contents($putPath, $putContent);
        }
        return true;
    }

    function makeDemoContent(&$content, $option, $opt, $dbOn = null, $dbOptStr = null)
    {
        //修改默认数据库连接配置,用于跨库查询
        if ($dbOptStr) {
            $this->changeDbOption($dbOptStr, $opt);
        }
        //#
        $ContentReplace = new ContentReplace($option, $opt);
        $ContentReplace->forNameHump($content); //替换指定文本
        $ContentReplace->forHiddenTag($content); //替换注释标签
        if ($dbOn) {
            $ContentReplace->forFields($content); //替換字段Array
        }
        $ContentReplace->forCodeBlockTag($content); //指定输出代码块
        return $content;
    }

    function changeDemoContent(&$content, &$changeContent, $option, $opt = null, $dbOn = null, $dbOptStr = null)
    {
        //修改默认数据库连接配置,用于跨库查询
        if ($dbOptStr) {
            $this->changeDbOption($dbOptStr, $opt);
        }
        //#
        $ContentReplace = new ContentReplace($option, $opt);
//        $ContentReplace->forNameHump($changeContent); //替换指定文本
//        $ContentReplace->forHiddenTag($changeContent); //替换注释标签
//        if ($dbOn) {
//            $ContentReplace->forFields($changeContent); //替換字段Array
//        }
        $ContentReplace->changeForCodeBlockTag($content, $changeContent); //替换指定输出代码块
        $ContentReplace->forCodeBlockTag($changeContent); //指定输出代码块
        return $changeContent;
    }


    function forValidate($childName)
    {
        $r = $this->r;
        $n = $this->n;
        $t = $this->t;
        $s = $this->s;

        $res         = $this->DB($childName, "getTableFields");
        $repValidate = '';
        $space       = $r . $n . $t . $t;
        if ($res) {
            foreach ($res as $k => $v) {
                switch ($v['native_type']) {
                    default :
                        break;
                    case "LONGLONG":
                    case "LONG":
                    case"TINY":
                        $v['native_type'] = 'integer';
                        break;
                    case "VAR_STRING":
                    case "BLOB":
                        $v['native_type'] = 'alphaDash';
                        break;
                    case "DATETIME":
                    case "TIMESTAMP":
                        $v['native_type'] = 'date';
                        break;
                    case "NEWDECIMAL":
                        $v['native_type'] = 'number';
                        break;
                };
                switch ($v['name']) {
                    default :
                        break;
                    case "status":
                        $v['native_type'] = 'integer|in:1,2';
                        break;
                    case "type":
                        $v['native_type'] = 'integer|in:1,2';
                        break;
                    case "phone":
                        $v['native_type'] = 'number|max:11';
                        break;
                    case "sex":
                        $v['native_type'] = 'integer|in:0,1,2';
                        break;
                    case "ip":
                        $v['native_type'] = 'ip';
                        break;
                };
                if ($k < count($res) - 1) {
                    $repValidate .= "\"" . $v['name'] . "\"=>\"" . $v['native_type'] . "\"," . $space;
                } else {
                    $repValidate .= "\"" . $v['name'] . "\"=>\"" . $v['native_type'] . "\"," . $space;
                }
            }
        }
        return $space . $repValidate;

    }

    function forFillable($childName)
    {
        $r           = $this->r;
        $n           = $this->n;
        $t           = $this->t;
        $s           = $this->s;
        $res         = $this->DB($childName, "getTableFields");
        $repFillable = '';
        $space       = $r . $n . $t . $t;
        if ($res) {
            foreach ($res as $k => $v) {
                switch ($v['name']) {
                    case "id":
                    case "created_at":
                    case"updated_at":
                    case"deleted_at":
                        break;
                    default:
                        if ($k < count($res) - 1) {
                            $repFillable .= "\"" . $v['name'] . "\"," . $space;
                        } else {
                            $repFillable .= "\"" . $v['name'] . "\"," . $space;
                        }
                        break;
                }
            }
        }
        return $space . $repFillable;
    }

    function forGuarded($childName)
    {
        $r           = $this->r;
        $n           = $this->n;
        $t           = $this->t;
        $s           = $this->s;
        $res         = $this->DB($childName, "getTableFields");
        $repFillable = '';
        $space       = $r . $n . $t . $t;
        if ($res) {
            foreach ($res as $k => $v) {
                switch ($v['name']) {
                    case "id":
                    case "create_time":
                    case"update_time":
                        if ($k < count($res) - 1) {
                            $repFillable .= "\"" . $v['name'] . "\"," . $space;
                        } else {
                            $repFillable .= "\"" . $v['name'] . "\"," . $space;
                        }
                        break;
                    default:
                        break;
                }
            }
        }
        return $space . $repFillable;
    }

    function forTypes($childName)
    {
        $r           = $this->r;
        $n           = $this->n;
        $t           = $this->t;
        $s           = $this->s;
        $res         = $this->DB($childName, "getTableFields");
        $repFillable = '';
        $space       = $r . $n . $t . $t;
        if ($res) {
            foreach ($res as $k => $v) {
                //var_dump( $v['native_type'] );
                switch ($v['native_type']) {
                    default :
                        break;
                    case "LONG":
                        $v['native_type'] = 'int';
                        break;
                    case "LONGLONG":
                        $v['native_type'] = 'bigint';
                        break;
                    case"TINY":
                        $v['native_type'] = 'tinyint';
                        break;
                    case "VAR_STRING":
                    case "BLOB":
                        $v['native_type'] = 'varchar';
                        break;
                    case "DATETIME":
                    case "TIMESTAMP":
                        $v['native_type'] = 'datetime';
                        break;
                    case "NEWDECIMAL":
                        $v['native_type'] = 'numeric';
                        break;
                };
                switch ($v['name']) {
                    default :
                        break;
                    case "status":
                        $v['native_type'] = 'tinyint';
                        break;
                    case "type":
                        $v['native_type'] = 'tinyint';
                        break;
                };
                if ($k < count($res) - 1) {
                    $repFillable .= "\"" . $v['name'] . "\"=>\"" . $v['native_type'] . "\"," . $space;
                } else {
                    $repFillable .= "\"" . $v['name'] . "\"=>\"" . $v['native_type'] . "\"," . $space;
                }
            }
        }
        return $space . $repFillable;
    }

    function forRules($childName)
    {
        $r           = $this->r;
        $n           = $this->n;
        $t           = $this->t;
        $s           = $this->s;
        $res         = $this->DB($childName, "getTableFields");
        $repFillable = '';
        $space       = $r . $n . $t . $t;
        if ($res) {
            foreach ($res as $k => $v) {
                var_dump($v['native_type']);
                switch ($v['native_type']) {
                    default :
                        break;
                    case "LONGLONG":
                    case "LONG":
                    case"TINY":
                        $v['native_type'] = 'integer';
                        break;
                    case "VAR_STRING":
                    case "BLOB":
                        $v['native_type'] = 'string';
                        break;
                    case "DATETIME":
                    case "TIMESTAMP":
                        $v['native_type'] = 'date';
                        break;
                    case "NEWDECIMAL":
                        $v['native_type'] = 'numeric';
                        break;
                };
                switch ($v['name']) {
                    default :
                        break;
                    case "status":
                        $v['native_type'] = 'integer|in:1,2';
                        break;
                    case "type":
                        $v['native_type'] = 'integer|in:1,2';
                        break;
                };
                if ($k < count($res) - 1) {
                    $repFillable .= "\"" . $v['name'] . "\"=>\"" . $v['native_type'] . "\"," . $space;
                } else {
                    $repFillable .= "\"" . $v['name'] . "\"=>\"" . $v['native_type'] . "\"," . $space;
                }
            }
        }
        return $space . $repFillable;
    }

    function forData($childName)
    {
        $r           = $this->r;
        $n           = $this->n;
        $t           = $this->t;
        $s           = $this->s;
        $res         = $this->DB($childName, "getTableFields");
        $repFillable = '';
        $space       = $r . $n . $t . $t . $t;
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
                if ($k < count($res) - 1) {
                    $repFillable .= "\"" . $v['name'] . "\"=>" . $v['native_type'] . "\$result->" . $v['name'] . "," . $space;
                } else {
                    $repFillable .= "\"" . $v['name'] . "\"=>" . $v['native_type'] . "\$result->" . $v['name'] . "," . $space;
                }
            }
        }
        return $space . $repFillable;
    }

//#########################################################

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
        return $newChildName;
    }

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


    function setDocFile($moduleName, $childName, $dbOn = null)
    {
        $moduleDomeName = $this->moduleDomeName;
        $childDemoName  = $this->childDemoName;
        $root           = $this->opt['root_path'];
        $option         = [
            "get_path"    => $root . "\\" . $moduleDomeName . "\\" . "doc.md",
            "module_name" => $moduleName,
            "child_name"  => $childName,
            "put_path"    => $root . "\\" . $moduleName . "\\" . "doc.md",
        ];
        $isExistFile    = file_exists($option['put_path']);
        if ($isExistFile) {
            $msg = "Exception : " . $moduleName . " | SetDocFile | is exists !";
            $this->console($msg, "red");
        } else {
            $this->fileEditCopy($option, function ($option, $content) {
                //替换指定文本
                //$ContentReplace = new ContentReplace($option); $ContentReplace ->forNameLower($content);
                return $content;
            });
            $msg = "created: " . $option["put_path"];
            $this->console($msg, "green");
            return true;
        }
        return false;
    }

    function setModuleProviderFile($moduleName, $childName)
    {
        $moduleDomeName = $this->moduleDomeName;
        $opt            = $this->opt;
        $root           = $this->opt['root_path'];
        $option         = [
            "get_path"    => $root . "\\" . $moduleDomeName . "\\Providers\\" . "ModuleServiceProvider.php",
            "module_name" => $moduleName,
            "child_name"  => $childName,
            "put_path"    => $root . "\\" . $moduleName . "\\Providers\\" . "ModuleServiceProvider.php",
        ];
        $isExistFile    = file_exists($option['put_path']);
        if ($isExistFile) {
            $msg = "Exception : " . $moduleName . " | SetModuleServiceProvider | is exists !";
            $this->console($msg, "red");
        } else {
            $this->fileEditCopy($option, function ($option, $content) use ($opt) {
                //替换指定文本
                $ContentReplace = new ContentReplace($option, $opt);
                $ContentReplace->forNameHump($content);
                //替换注释标签
                $ContentReplace->forHiddenTag($content);
                return $content;
            });
            $msg = "created: " . $option["put_path"];
            $this->console($msg, "green");
            return true;
        }
        return false;
    }

    function setRouteProviderFile($moduleName, $childName)
    {
        $moduleDomeName = $this->moduleDomeName;
        $opt            = $this->opt;
        $root           = $this->opt['root_path'];
        $option         = [
            "get_path"    => $root . "\\" . $moduleDomeName . "\\Providers\\" . "RouteServiceProvider.php",
            "module_name" => $moduleName,
            "child_name"  => $childName,
            "put_path"    => $root . "\\" . $moduleName . "\\Providers\\" . "RouteServiceProvider.php",
        ];
        $isExistFile    = file_exists($option['put_path']);
        if ($isExistFile) {
            $msg = "Exception : " . $moduleName . " | SetModuleServiceProvider | is exists !";
            $this->console($msg, "red");
        } else {
            $this->fileEditCopy($option, function ($option, $content) use ($opt) {
                //替换指定文本
                $ContentReplace = new ContentReplace($option, $opt);
                $ContentReplace->forNameHump($content);
                //替换注释标签
                $ContentReplace->forHiddenTag($content);
                return $content;
            });
            $msg = "created: " . $option["put_path"];
            $this->console($msg, "green");
            return true;
        }
        return false;
    }

    function setModuleJsonFile($moduleName, $childName)
    {
        $moduleDomeName = $this->moduleDomeName;
        $opt            = $this->opt;
        $root           = $this->opt['root_path'];
        $option         = [
            "get_path"    => $root . "\\" . $moduleDomeName . "\\" . "module.json",
            "module_name" => $moduleName,
            "child_name"  => $childName,
            "put_path"    => $root . "\\" . $moduleName . "\\" . "module.json",
        ];
        $isExistFile    = file_exists($option['put_path']);
        if ($isExistFile) {
            $msg = "Exception : " . $moduleName . " | SetModuleServiceProvider | is exists !";
            $this->console($msg, "red");
        } else {
            $this->fileEditCopy($option, function ($option, $content) use ($opt) {
                //替换指定文本
                $ContentReplace = new ContentReplace($option, $opt);
                $ContentReplace->forNameHump($content);
                //替换注释标签
                $ContentReplace->forHiddenTag($content);
                return $content;
            });
            $msg = "created: " . $option["put_path"];
            $this->console($msg, "green");
            return true;
        }
        return false;
    }

    function setDatabaseSeederFile($moduleName, $childName, $dbOn = null)
    {
        $moduleDomeName = $this->moduleDomeName;
        $childDemoName  = $this->childDemoName;
        $opt            = $this->opt;
        $root           = $this->opt['root_path'];
        $option         = [
            "get_path"    => $root . "\\" . $moduleDomeName . "\\Database\\Seeders\\" . $moduleDomeName . "DatabaseSeeder.php",
            "module_name" => $moduleName,
            "child_name"  => $childName,
            "put_path"    => $root . "\\" . $moduleName . "\\Database\\Seeders\\" . $moduleName . "DatabaseSeeder.php",
        ];

        $isExistFile = file_exists($option['put_path']);
        if ($isExistFile) {
            $msg = "Exception : " . $moduleName . " | SetDatabaseSeederFile | is exists !";
            $this->console($msg, "red");
        } else {
            $this->fileEditCopy($option, function ($option, $content) use ($opt, $dbOn) {
                //替换指定文本
                $ContentReplace = new ContentReplace($option, $opt);
                $ContentReplace->forNameNormal($content);
                //替换注释标签
                $ContentReplace->forHiddenTag($content);
                return $content;
            });
            $msg = "created: " . $option["put_path"];
            $this->console($msg, "green");
            return true;
        }
        return false;
    }

    function setControllerFile($moduleName, $childName, $dbOn = null)
    {
        $moduleDomeName = $this->moduleDomeName;
        $childDemoName  = $this->childDemoName;
        $opt            = $this->opt;
        $root           = $this->opt['root_path'];
        $option         = [
            "get_path"    => $root . "\\" . $moduleDomeName . "\\Http\\Controllers\\" . ucwords($childDemoName) . "Controller.php",
            "module_name" => $moduleName,
            "child_name"  => $childName,
            "put_path"    => $root . "\\" . $moduleName . "\\Http\\Controllers\\" . ucwords($childName) . "Controller.php",
        ];
        $isExistFile    = file_exists($option['put_path']);
        if ($isExistFile) {
            $this->fileEditChange($option, function ($option, $demoContent, $changeContent) use ($opt, $dbOn) {
                $this->changeDemoContent($demoContent, $changeContent, $option, $opt, $dbOn);
                return $changeContent;
            });
            $msg = "changed : Module " . $moduleName . ' | SetControllerFile | OK';
            $this->console($msg, "green");
            //$msg="Exception : ".$moduleName." | SetControllerFile | is exists !"; $this->console($msg,"red");
        } else {
            $this->fileEditCopy($option, function ($option, $content) use ($opt, $dbOn) {
                $this->makeDemoContent($content, $option, $opt, $dbOn);
                return $content;
            });
            $msg = "created: " . $option["put_path"];
            $this->console($msg, "green");
            return true;
        }
        return false;
    }

    function setCmdFile($moduleName, $childName, $dbOn = null)
    {
        $moduleDomeName = $this->moduleDomeName;
        $childDemoName  = $this->childDemoName;
        $opt            = $this->opt;
        $root           = $this->opt['root_path'];
        $option         = [
            "get_path"    => $root . "\\" . $moduleDomeName . "\\Consoles\\" . ucwords($childDemoName) . "Cmd.php",
            "module_name" => $moduleName,
            "child_name"  => $childName,
            "put_path"    => $root . "\\" . $moduleName . "\\Consoles\\" . ucwords($childName) . "Cmd.php",
        ];

        $isExistFile = file_exists($option['put_path']);
        if ($isExistFile) {
            $this->fileEditChange($option, function ($option, $demoContent, $changeContent) use ($opt, $dbOn) {
                $this->changeDemoContent($demoContent, $changeContent, $option, $opt, $dbOn);
                return $changeContent;
            });
            $msg = "changed : Module " . $moduleName . ' | SetCmdFile | OK';
            $this->console($msg, "green");
        } else {
            $this->fileEditCopy($option, function ($option, $content) use ($opt, $dbOn) {
                $this->makeDemoContent($content, $option, $opt, $dbOn);
                return $content;
            });
            $msg = "created: " . $option["put_path"];
            $this->console($msg, "green");
            return true;
        }

        return false;
    }

    function setJobFile($moduleName, $childName, $dbOn = null)
    {
        $moduleDomeName = $this->moduleDomeName;
        $childDemoName  = $this->childDemoName;
        $opt            = $this->opt;
        $root           = $this->opt['root_path'];
        $option         = [
            "get_path"    => $root . "\\" . $moduleDomeName . "\\Jobs\\" . ucwords($childDemoName) . "Job.php",
            "module_name" => $moduleName,
            "child_name"  => $childName,
            "put_path"    => $root . "\\" . $moduleName . "\\Jobs\\" . ucwords($childName) . "Job.php",
        ];

        $isExistFile = file_exists($option['put_path']);
        if ($isExistFile) {
            $msg = "Exception : " . $moduleName . " | SetJobFile | is exists !";
            $this->console($msg, "red");
        } else {
            $this->fileEditCopy($option, function ($option, $content) use ($opt, $dbOn) {
                //替换指定文本
                $ContentReplace = new ContentReplace($option, $opt);
                $ContentReplace->forNameNormal($content);
                //替换注释标签
                $ContentReplace->forHiddenTag($content);
                return $content;
            });
            $msg = "created: " . $option["put_path"];
            $this->console($msg, "green");
            return true;
        }
        return false;
    }

    function setRequestFile($moduleName, $childName, $dbOn = null, $dbOptStr = null)
    {
        $moduleDomeName = $this->moduleDomeName;
        $childDemoName  = $this->childDemoName;
        $opt            = $this->opt;
        $root           = $this->opt['root_path'];
        $option         = [
            "get_path"    => $root . "\\" . $moduleDomeName . "\\Http\\Requests\\" . ucwords($childDemoName) . "Request.php",
            "module_name" => $moduleName,
            "child_name"  => $childName,
            "put_path"    => $root . "\\" . $moduleName . "\\Http\\Requests\\" . ucwords($childName) . "Request.php"
        ];
        $isExistFile    = file_exists($option['put_path']);
        if ($isExistFile) {
            $msg = "Exception : " . $moduleName . " | SetRequestFile | is exists !";
            $this->console($msg, "red");
        } else {
            $this->fileEditCopy($option, function ($option, $content) use ($opt, $dbOn, $dbOptStr) {
                //修改默认数据库连接配置,用于跨库查询
                if ($dbOptStr) {
                    $this->changeDbOption($dbOptStr, $opt);
                }
                //#
                $ContentReplace = new ContentReplace($option, $opt);
                //替换指定文本
                $ContentReplace->forNameNormal($content);
                if ($dbOn) {
                    //替换标签区域
                    $ContentReplace->forRules($content);
                    $ContentReplace->forMessages($content);
                }
                return $content;
            });
            $msg = "created: " . $option["put_path"];
            $this->console($msg, "green");
            return true;
        }
        return false;
    }

    function setModelFile($moduleName, $childName, $dbOn = null, $dbOptStr = null)
    {
        $moduleDomeName = $this->moduleDomeName;
        $childDemoName  = $this->childDemoName;
        $opt            = $this->opt;
        $root           = $this->opt['root_path'];
        $option         = [
            "get_path"    => $root . "\\" . $moduleDomeName . "\\Models\\" . ucwords($childDemoName) . "Model.php",
            "module_name" => $moduleName,
            "child_name"  => $childName,
            "put_path"    => $root . "\\" . $moduleName . "\\Models\\" . ucwords($childName) . "Model.php"
        ];
        $isExistFile    = file_exists($option['put_path']);
        if ($isExistFile) {
            $msg = "Exception : " . $moduleName . " | SetModelFile | is exists !";
            $this->console($msg, "red");
        } else {
            $this->fileEditCopy($option, function ($option, $content) use ($opt, $dbOn, $dbOptStr) {
                //修改默认数据库连接配置,用于跨库查询
                if ($dbOptStr) {
                    $this->changeDbOption($dbOptStr, $opt);
                }
                //#
                $ContentReplace = new ContentReplace($option, $opt);
                //替换指定文本
                $ContentReplace->forNameNormal($content);
                //替换注释标签
                $ContentReplace->forHiddenTag($content);
                if ($dbOn) {
                    //替换标签区域
                    $ContentReplace->forFillAble($content);
                    $ContentReplace->forGuarded($content);
                    $ContentReplace->forCasts($content);
                }
                return $content;
            });
            $msg = "created: " . $option["put_path"];
            $this->console($msg, "green");
            return true;
        }
        return false;
    }

    function setRepositoryFile($moduleName, $childName, $dbOn = null, $dbOptStr = null)
    {
        $moduleDomeName = $this->moduleDomeName;
        $childDemoName  = $this->childDemoName;
        $opt            = $this->opt;
        $root           = $this->opt['root_path'];
        $option         = [
            "get_path"    => $root . "\\" . $moduleDomeName . "\\Reposit\\" . ucwords($childDemoName) . "Repo.php",
            "module_name" => $moduleName,
            "child_name"  => $childName,
            "put_path"    => $root . "\\" . $moduleName . "\\Reposit\\" . ucwords($childName) . "Repo.php",
        ];
        $isExistFile    = file_exists($option['put_path']);
        if ($isExistFile) {
            $this->fileEditChange($option, function ($option, $demoContent, $changeContent) use ($opt, $dbOn, $dbOptStr) {
                $this->changeDemoContent($demoContent, $changeContent, $option, $opt, $dbOn, $dbOptStr);
                return $changeContent;
            });
            $msg = "changed : Module " . $moduleName . ' | SetRepositoryFile | OK';
            $this->console($msg, "green");
            //$msg="Exception : ".$moduleName." | SetRepositoryFile | is exists !"; $this->console($msg,"red");
        } else {
            $this->fileEditCopy($option, function ($option, $content) use ($opt, $dbOn, $dbOptStr) {
                $this->makeDemoContent($content, $option, $opt, $dbOn, $dbOptStr);
                return $content;
            });
            $msg = "created: " . $option["put_path"];
            $this->console($msg, "green");
            return true;
        }
        return false;
    }

    function setTransformerFile($moduleName, $childName, $dbOn = null, $dbOptStr = null)
    {
        $moduleDomeName = $this->moduleDomeName;
        $childDemoName  = $this->childDemoName;
        $opt            = $this->opt;
        $root           = $this->opt['root_path'];
        $option         = [
            "get_path"    => $root . "\\" . $moduleDomeName . "\\Http\\Trans\\" . ucwords($childDemoName) . "Trans.php",
            "module_name" => $moduleName,
            "child_name"  => $childName,
            "put_path"    => $root . "\\" . $moduleName . "\\Http\\Trans\\" . ucwords($childName) . "Trans.php",
        ];
        $isExistFile    = file_exists($option['put_path']);
        if ($isExistFile) {
            $msg = "Exception : " . $moduleName . " | SetTransformerFile | is exists !";
            $this->console($msg, "red");
        } else {
            $this->fileEditCopy($option, function ($option, $content) use ($opt, $dbOn, $dbOptStr) {
                //修改默认数据库连接配置,用于跨库查询
                if ($dbOptStr) {
                    $this->changeDbOption($dbOptStr, $opt);
                }
                //#
                //替换指定文本
                $ContentReplace = new ContentReplace($option, $opt);
                $ContentReplace->forNameLower($content);
                //替换注释标签
                $ContentReplace->forHiddenTag($content);
                if ($dbOn) {
                    //替换标签区域
                    $ContentReplace->forData($content);
                }
                return $content;
            });
            $msg = "created: " . $option["put_path"];
            $this->console($msg, "green");
            return true;
        }
        return false;
    }

    function setServiceFile($moduleName, $childName, $dbOn = null, $dbOptStr = null)
    {
        $moduleDomeName = $this->moduleDomeName;
        $childDemoName  = $this->childDemoName;
        $opt            = $this->opt;
        $root           = $this->opt['root_path'];
        $option         = [
            "get_path"    => $root . "\\" . $moduleDomeName . "\\Srv\\" . ucwords($childDemoName) . "Srv.php",
            "module_name" => $moduleName,
            "child_name"  => $childName,
            "put_path"    => $root . "\\" . $moduleName . "\\Srv\\" . ucwords($childName) . "Srv.php",
        ];
        $isExistFile    = file_exists($option['put_path']);
        if ($isExistFile) {
            $this->fileEditChange($option, function ($option, $demoContent, $changeContent) use ($opt, $dbOn, $dbOptStr) {
                $this->changeDemoContent($demoContent, $changeContent, $option, $opt, $dbOn, $dbOptStr);
                return $changeContent;
            });
            $msg = "changed : Module " . $moduleName . ' | SetServiceFile | OK';
            $this->console($msg, "green");
            //$msg="Exception : ".$moduleName." | SetServiceFile | is exists !"; $this->console($msg,"red");
        } else {
            $this->fileEditCopy($option, function ($option, $content) use ($opt, $dbOn, $dbOptStr) {
                $this->makeDemoContent($content, $option, $opt, $dbOn, $dbOptStr);
                return $content;
            });
            $msg = "created: " . $option["put_path"];
            $this->console($msg, "green");
            return true;
        }
        return false;
    }

    function setLogicFile($moduleName, $childName, $dbOn = null, $dbOptStr = null)
    {
        $moduleDomeName = $this->moduleDomeName;
        $childDemoName  = $this->childDemoName;
        $opt            = $this->opt;
        $appPath        = $this->opt['root_path'];
        $option         = [
            "get_path"    => $appPath . "\\" . $moduleDomeName . "\\Http\\Logics\\" . ucwords($childDemoName) . "Logic.php",
            "module_name" => $moduleName,
            "child_name"  => $childName,
            "put_path"    => $appPath . "\\" . $moduleName . "\\Http\\Logics\\" . ucwords($childName) . "Logic.php",
        ];
        $isExistFile    = file_exists($option['put_path']);
        if ($isExistFile) {
            $this->fileEditChange($option, function ($option, $demoContent, $changeContent) use ($opt, $dbOn, $dbOptStr) {
                $this->changeDemoContent($demoContent, $changeContent, $option, $opt, $dbOn, $dbOptStr);
                return $changeContent;
            });
            $msg = "changed : Module " . $moduleName . ' | SetLogicFile | OK';
            $this->console($msg, "green");
            //$msg="Exception : ".$moduleName." | SetLogicFile | is exists !"; $this->console($msg,"red");
        } else {
            $this->fileEditCopy($option, function ($option, $content) use ($opt, $dbOn, $dbOptStr) {
                $this->makeDemoContent($content, $option, $opt, $dbOn, $dbOptStr);
                return $content;
            });
            $msg = "created: " . $option["put_path"];
            $this->console($msg, "green");
            return true;
        }
        return false;
    }

    function setEnumFile($moduleName, $childName, $dbOn = null)
    {
        $moduleDomeName = $this->moduleDomeName;
        $childDemoName  = $this->childDemoName;
        $root           = $this->opt['root_path'];
        $option         = [
            "get_path"    => $root . "\\" . $moduleDomeName . "\\Enums\\" . ucwords($childDemoName) . "Enum.php",
            "module_name" => $moduleName,
            "child_name"  => $childName,
            "put_path"    => $root . "\\" . $moduleName . "\\Enums\\" . ucwords($childName) . "Enum.php",
        ];
        $isExistFile    = file_exists($option['put_path']);
        if ($isExistFile) {
            $msg = "Exception : " . $moduleName . " | SetStatusFile | is exists !";
            $this->console($msg, "red");
        } else {
            $this->fileEditCopy($option, function ($option, $content) {
                //替换指定文本
                $ContentReplace = new ContentReplace($option);
                $ContentReplace->forNameLower($content);
                return $content;
            });
            $msg = "created: " . $option["put_path"];
            $this->console($msg, "green");
            return true;
        }
        return false;
    }

    function setErrorCodeFile($moduleName, $childName, $dbOn = null)
    {
        $moduleDomeName = $this->moduleDomeName;
        $childDemoName  = $this->childDemoName;
        $root           = $this->opt['root_path'];
        $option         = [
            "get_path"    => $root . "\\" . $moduleDomeName . "\\Errors\\" . ucwords($moduleDomeName) . "RootError.php",
            "module_name" => $moduleName,
            "child_name"  => $childName,
            "put_path"    => $root . "\\" . $moduleName . "\\Errors\\" . ucwords($moduleName) . "RootError.php",
        ];
        $isExistFile    = file_exists($option['put_path']);
        if ($isExistFile) {
            $msg = "Exception : " . $moduleName . " | SetErrorCodeFile | is exists !";
            $this->console($msg, "red");
        } else {
            $this->fileEditCopy($option, function ($option, $content) {
                //替换指定文本
                $ContentReplace = new ContentReplace($option);
                $ContentReplace->forNameLower($content);
                return $content;
            });
            $msg = "created: " . $option["put_path"];
            $this->console($msg, "green");
            return true;
        }
        return false;
    }

    function setApiRoute($moduleName, $childName, $dbOn = null)
    {
        $moduleDomeName = $this->moduleDomeName;
        $childDemoName  = $this->childDemoName;
        $root           = $this->opt['root_path'];

        //配置路径
        $option = [
            "get_path"    => $root . "\\" . $moduleDomeName . "\\Http\\api.php",
            "module_name" => $moduleName,
            "child_name"  => $childName,
            "put_path"    => $root . "\\" . $moduleName . "\\Http\\api.php",
        ];

        $isExistFile = file_exists($option['put_path']);
        if ($isExistFile) {
            $msg = "Exception : " . $moduleName . " | SetRouteFile | route is exists !";
            $this->console($msg, "red");
        } else {
            $this->fileEditCopy($option, function ($option, $content) {
                //替换指定文本
                $ContentReplace = new ContentReplace($option);
                $ContentReplace->forRouteName($content);
                return $content;
            });
            $msg = "created: " . $option["put_path"];
            $this->console($msg, "green");
            return true;
        }
        return false;
    }

    function changeLogicFile($moduleName, $childName, $dbOptStr = null)
    {
        $moduleDomeName = $this->moduleDomeName;
        $childDemoName  = $this->childDemoName;
        $opt            = $this->opt;
        $appPath        = $this->opt['root_path'];
        $option         = [
            "get_path"    => $appPath . "\\" . $moduleDomeName . "\\Http\\Logics\\" . ucwords($childDemoName) . "Logic.php",
            "module_name" => $moduleName,
            "child_name"  => $childName,
            "put_path"    => $appPath . "\\" . $moduleName . "\\Http\\Logics\\" . ucwords($childName) . "Logic.php",
        ];

        $this->fileEditChange($option, function ($option, $demoContent, $changeContent) use ($opt, $dbOptStr) {
            //修改默认数据库连接配置,用于跨库查询
            if ($dbOptStr) {
                $this->changeDbOption($dbOptStr, $opt);
            }
            //#
            $ContentReplace = new ContentReplace($option, $opt);
//            $ContentReplace->forNameHump($changeContent); //替换指定文本
//            $ContentReplace->forHiddenTag($changeContent); //替换注释标签
            $ContentReplace->forFields($changeContent); //替換字段Array
            $ContentReplace->changeForCodeBlockTag($content, $changeContent); //替换指定输出代码块
            $ContentReplace->forCodeBlockTag($changeContent); //指定输出代码块
            return $changeContent;
        });

        $msg = "changed: " . $option["put_path"];
        $this->console($msg, "green");
        return true;
    }

    function changeModelFields($moduleName, $childName, $dbOptStr = null)
    {
        $moduleDomeName = $this->moduleDomeName;
        $childDemoName  = $this->childDemoName;
        $opt            = $this->opt;
        $root           = $this->opt['root_path'];
        $option         = [
            "get_path"    => $root . "\\" . $moduleDomeName . "\\Models\\" . ucwords($childDemoName) . "Model.php",
            "module_name" => $moduleName,
            "child_name"  => $childName,
            "put_path"    => $root . "\\" . $moduleName . "\\Models\\" . ucwords($childName) . "Model.php",
        ];
        $this->fileEditChange($option, function ($option, $demoContent, $changeContent) use ($opt, $dbOptStr) {
            //修改默认数据库连接配置,用于跨库查询
            if ($dbOptStr) {
                $this->changeDbOption($dbOptStr, $opt);
            }
            //#
            //替换指定文本
            $ContentReplace = new ContentReplace($option, $opt);
            $ContentReplace->forNameNormal($changeContent);
            //替换标签区域
            $ContentReplace->forFillAble($changeContent);
            $ContentReplace->forGuarded($changeContent);
            $ContentReplace->forCasts($changeContent);
            return $changeContent;
        });

        $msg = "changed: " . $option["put_path"];
        $this->console($msg, "green");
        return true;
    }

    function changeRepositoryFields($moduleName, $childName, $dbOptStr = null)
    {
        $moduleDomeName = $this->moduleDomeName;
        $childDemoName  = $this->childDemoName;
        $opt            = $this->opt;
        $root           = $this->opt['root_path'];
        $option         = [
            "get_path"    => $root . "\\" . $moduleDomeName . "\\Repositories\\" . ucwords($childDemoName) . "Repository.php",
            "module_name" => $moduleName,
            "child_name"  => $childName,
            "put_path"    => $root . "\\" . $moduleName . "\\Repositories\\" . ucwords($childName) . "Repository.php",
        ];

        $this->fileEditChange($option, function ($option, $demoContent, $changeContent) use ($opt, $dbOptStr) {
            //修改默认数据库连接配置,用于跨库查询
            if ($dbOptStr) {
                $this->changeDbOption($dbOptStr, $opt);
            }
            //#
            //替换指定文本
            $ContentReplace = new ContentReplace($option, $opt);
            $ContentReplace->forNameNormal($changeContent);
            //替换标签区域
            $ContentReplace->forFillAble($changeContent);
            $ContentReplace->forGuarded($changeContent);
            $ContentReplace->forCasts($changeContent);
            return $changeContent;
        });

        $msg = "changed: " . $option["put_path"];
        $this->console($msg, "green");
        return true;
    }

    function changeRequestFields($moduleName, $childName, $dbOptStr = null)
    {
        $moduleDomeName = $this->moduleDomeName;
        $childDemoName  = $this->childDemoName;
        $opt            = $this->opt;
        $root           = $this->opt['root_path'];
        $option         = [
            "get_path"    => $root . "\\" . $moduleDomeName . "\\Http\\Requests\\" . ucwords($childDemoName) . "Request.php",
            "module_name" => $moduleName,
            "child_name"  => $childName,
            "put_path"    => $root . "\\" . $moduleName . "\\Http\\Requests\\" . ucwords($childName) . "Request.php",
        ];

        $this->fileEditChange($option, function ($option, $demoContent, $changeContent) use ($opt, $dbOptStr) {
            //修改默认数据库连接配置,用于跨库查询
            if ($dbOptStr) {
                $this->changeDbOption($dbOptStr, $opt);
            }
            //替换指定文本
            $ContentReplace = new ContentReplace($option, $opt);
            $ContentReplace->forNameLower($changeContent);
            //替换标签区域文本
            $ContentReplace->forRules($changeContent);
            $ContentReplace->forMessages($changeContent);
            return $changeContent;
        });

        $msg = "changed: " . $option["put_path"];
        $this->console($msg, "green");
        return true;
    }

    function changeTransformerFields($moduleName, $childName, $dbOptStr = null)
    {
        $moduleDomeName = $this->moduleDomeName;
        $childDemoName  = $this->childDemoName;
        $opt            = $this->opt;
        $root           = $this->opt['root_path'];
        $option         = [
            "get_path"    => $root . "\\" . $moduleDomeName . "\\Http\\Trans\\" . ucwords($childDemoName) . "Trans.php",
            "module_name" => $moduleName,
            "child_name"  => $childName,
            "put_path"    => $root . "\\" . $moduleName . "\\Http\\Trans\\" . ucwords($childName) . "Trans.php",
        ];

        $this->fileEditChange($option, function ($option, $demoContent, $changeContent) use ($opt, $dbOptStr) {
            //修改默认数据库连接配置,用于跨库查询
            if ($dbOptStr) {
                $this->changeDbOption($dbOptStr, $opt);
            }
            //#
            //替换指定文本
            $ContentReplace = new ContentReplace($option, $opt);
            $ContentReplace->forNameLower($changeContent);
            //替换标签区域文本
            $ContentReplace->forData($changeContent);
            return $changeContent;
        });

        $msg = "changed: " . $option["put_path"];
        $this->console($msg, "green");
        return true;
    }

    function makeFolderByArr($moduleName, $folders = [])
    {
        if (empty($folders)) {
            $childPathArr = ['Config', 'Consoles', 'EDocs', 'Enums', 'Errors', 'Jobs', 'Models',
                'Http\Controllers', 'Http\Requests', 'Http\Logics', 'Http\Trans',
                'Providers', 'Reposit', 'Srv'];
        } else {
            $childPathArr = $folders;
        }
        $root       = $this->opt['root_path'];
        $modulePath = $root . "\\" . $moduleName;
        foreach ($childPathArr as $k => $v) {
            $childPath   = $modulePath . "\\" . $v;
            $isExistFile = file_exists($childPath);
            if ($isExistFile) {
                $msg = "Exception : Module " . $moduleName . " | MakeFolder | " . $v . " is exists !";
                $this->console($msg, "red");
            } else {
                $res = mkdir(iconv("UTF-8", "GBK", $childPath), 0755, true);
                if ($res) {
                    $msg = "created : " . $childPath;
                    $this->console($msg, "green");
                }
            }
        }
        return true;
    }

    function makeTestFolder($moduleName, $message = null)
    {
        $childPathArr = ['Controllers', 'doc'];
        $modulePath   = $this->opt['test_path'] . "\\" . $moduleName;
        foreach ($childPathArr as $k => $v) {
            $childPath   = $modulePath . "\\" . $v;
            $isExistFile = file_exists($childPath);
            if ($isExistFile) {
                if ($message) {
                    $msg = "Exception : TestModule " . $moduleName . " | MakeFolder | " . $v . " is exists !";
                    $this->console($msg, "red");
                }
            } else {
                $res = mkdir(iconv("UTF-8", "GBK", $childPath), 0755, true);
                if ($res) {
                    if ($message) {
                        $msg = "created : TestModule " . $moduleName . " | MakeFolder | make " . $v . " folder OK";
                        $this->console($msg, "yellow");
                    }
                }
            }
        }
    }

    function setTestFile($moduleName, $childName, $dbOn = null, $dbOptStr = null)
    {
        $moduleDomeName = $this->moduleDomeName;
        $childDemoName  = $this->childDemoName;
        $opt            = $this->opt;
        $root           = $this->opt['test_path'];
        $option         = [
            "get_path"    => $root . "\\" . $moduleDomeName . "\\Controllers\\" . ucwords($childDemoName) . "Test.php",
            "module_name" => $moduleName,
            "child_name"  => $childName,
            "put_path"    => $root . "\\" . $moduleName . "\\Controllers\\" . ucwords($childName) . "Test.php"
        ];

        $isExistFile = file_exists($option['put_path']);
        if ($isExistFile) {
            $msg = "Exception : " . $moduleName . " | SetTestFile | is exists !";
            $this->console($msg, "red");
        } else {
            $this->fileEditCopy($option, function ($option, $content) use ($opt, $dbOn, $dbOptStr) {
                //修改默认数据库连接配置,用于跨库查询
                if ($dbOptStr) {
                    $this->changeDbOption($dbOptStr, $opt);
                }
                //#
                $ContentReplace = new ContentReplace($option, $opt);
                //替换指定文本
                $ContentReplace->forNameHumpAndLower($content);
                //替换注释标签
                $ContentReplace->forHiddenTag($content);
                if ($dbOn) {
                    //替换标签区域
                    $ContentReplace->forTestInData($content);
                    $ContentReplace->forTestUpData($content);
                }
                return $content;
            });
            $msg = "created : TestModule " . $moduleName . ' | SetTestFile | OK';
            $this->console($msg, "yellow");
            return true;
        }
        return false;
    }

    //修改默认数据库连接,用于跨库查询
    function changeDbOption($dbOption, &$option = null)
    {
        $optStr = 'database.connections.' . $dbOption;
        $mysql  = config($optStr);
        if (!$mysql) {
            $msg = "Exception : ChangeDbOption | is fail !";
            $this->console($msg, "red");
            die;
        }
        $option['host']     = $mysql["host"];
        $option['port']     = $mysql["port"];
        $option['database'] = $mysql["database"];
        $option['prefix']   = $mysql["prefix"];
        $option['username'] = $mysql["username"];
        $option['password'] = $mysql["password"];
        return $option;
    }

    function setCodeBlockCurr($codeBlockStr)
    {
        $codeBlockArr = explode(',', $codeBlockStr);
        //获取交集
        $codeBlockOpt                 = $this->opt['code_block'];
        $this->opt['code_block_curr'] = array_intersect($codeBlockOpt, $codeBlockArr);
    }

}
