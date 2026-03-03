<?php

namespace Modules\Demo\Logics;

use Modules\Base\Logic\BaseLogic;

/**
 * notes: 应用层(Http) - 业务逻辑类(Logic), 用于归纳特定应用端的业务,
 * - 如果是 通用抽象 或 偏向于数据对象的业务, 应归纳领域服务层(Srv), 避免版本迭代时发生"散弹式修改".
 * Class SampleLogic
 * @package Modules\Demo\Logics
 */
class EsSampleLogic extends BaseLogic
{

}
