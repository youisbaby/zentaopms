<?php
declare(strict_types=1);
class testcaseTao extends testcaseModel
{
    /**
     * Fetch scene name.
     *
     * @param  int       $sceneID
     * @access protected
     * @return void
     */
    protected function fetchSceneName(int $sceneID): string|null
    {
        return $this->dao->findByID((int)$sceneID - CHANGEVALUE)->from(TABLE_SCENE)->fetch('title');
    }

    /**
     * 插入用例的步骤。
     * Insert the steps of the case.
     *
     * @param  int       $caseID
     * @param  array     $steps
     * @param  array     $expects
     * @param  array     $stepTypes
     * @access protected
     * @return void
     */
    protected function insertSteps(int $caseID, array $steps, array $expects, array $stepTypes)
    {
        $preGrade     = 0;
        $parentStepID = $grandPaStepID = 0;
        foreach($steps as $stepKey => $stepDesc)
        {
            /* 跳过步骤描述为空的步骤。 */
            /* If step desc is empty, skip it. */
            if(empty($stepDesc)) continue;

            /* 计算步骤类型和层级。 */
            /* Set step type and step grade. */
            $stepType = $stepTypes[$stepKey];
            $grade    = substr_count((string)$stepKey, '.');

            /* 如果当前步骤层级为0，父ID和祖父ID清0。 */
            /* If step grade is zero, set parent step id and grand step id to zero. */
            if($grade == 0)
            {
                $parentStepID = $grandPaStepID = 0;
            }
            /* 如果前一个步骤的层级比当前步骤的层级大，将父ID设置为祖父ID，祖父ID清0。 */
            /* If previous step grade is greater than current step grade, set parent step id to grand step id, and set grand step id to zero. */
            elseif($preGrade > $grade)
            {
                $parentStepID  = $grandPaStepID;
                $grandPaStepID = 0;
            }

            /* 构建步骤数据，插入步骤。 */
            /* Build step data, and insert it. */
            $step = new stdClass();
            $step->type    = $stepType;
            $step->parent  = $parentStepID;
            $step->case    = $caseID;
            $step->version = 1;
            $step->desc    = rtrim(htmlSpecialString($stepDesc));
            $step->expect  = $stepType == 'group' ? '' : rtrim(htmlSpecialString(zget($expects, $stepKey, '')));

            $this->dao->insert(TABLE_CASESTEP)->data($step)
                ->autoCheck()
                ->exec();

            /* 如果步骤类型是group，将祖父ID设置为父ID，父ID设置为当前步骤ID。 */
            /* If step type is group, set grand step id to parent step id and set parent step id to current step id. */
            if($stepType == 'group')
            {
                $grandPaStepID = $parentStepID;
                $parentStepID  = $this->dao->lastInsertID();
            }

            $preGrade = $grade;
        }
    }

    /**
     * 获取用例步骤。
     * Get case steps.
     *
     * @param  int       $caseID
     * @param  int       $version
     * @access protected
     * @return void
     */
    protected function getSteps(int $caseID, int $version)
    {
        $caseSteps     = array();
        $steps         = $this->dao->select('*')->from(TABLE_CASESTEP)->where('`case`')->eq($caseID)->andWhere('version')->eq($version)->orderBy('id')->fetchAll('id');
        $preGrade      = 1;
        $parentSteps   = array();
        $key           = array(0, 0, 0);
        foreach($steps as $step)
        {
            $parentSteps[$step->id] = $step->parent;
            $grade = 1;
            if(isset($parentSteps[$step->parent])) $grade = isset($parentSteps[$parentSteps[$step->parent]]) ? 3 : 2;

            if($grade > $preGrade)
            {
                $key[$grade - 1] = 1;
            }
            else
            {
                if($grade < $preGrade)
                {
                    if($grade < 2) $key[1] = 0;
                    if($grade < 3) $key[2] = 0;
                }
                $key[$grade - 1] ++;
            }
            $name = implode('.', $key);
            $name = str_replace('.0', '', $name);

            $data = new stdclass();
            $data->name   = str_replace('.0', '', $name);
            $data->id     = $step->id;
            $data->step   = $step->desc;
            $data->desc   = $step->desc;
            $data->expect = $step->expect;
            $data->type   = $step->type;
            $data->parent = $step->parent;
            $data->grade  = $grade;

            $caseSteps[] = $data;

            $preGrade = $grade;
        }
        return $caseSteps;
    }
}
