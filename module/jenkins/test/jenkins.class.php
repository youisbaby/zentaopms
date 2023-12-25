<?php
class jenkinsTest
{
    public $tester;

    public function __construct()
    {
        global $tester;
        $this->tester   = $tester;
        $this->jenkins = $this->tester->loadModel('jenkins');
    }

    /**
     * 测试获取流水线列表。
     * Test get jenkins tasks.
     *
     * @param  int    $jenkinsID
     * @param  int    $depth
     * @access public
     * @return array
     */
    public function getTasks(int $jenkinsID, int $depth = 0)
    {
        return $this->jenkins->getTasks($jenkinsID, $depth);
    }

    /**
     * 测试获取 Jenkins 流水线。
     * Test get jobs by jenkins .
     *
     * @param  int    $jenkinsID
     * @access public
     * @return string
     */
    public function getJobPairsTest(int $jenkinsID): string
    {
        $jobs = $this->jenkins->getJobPairs($jenkinsID);
        $return = '';
        foreach($jobs as $jobID => $job) $return .= "{$jobID}:{$job},";
        return trim($return, ',');
    }

    /**
     * 测试获取jenkins api 密码串。
     * Test get jenkins api userpwd string.
     *
     * @param  int    $jenkinsID
     * @access public
     * @return array
     */
    public function getApiUserPWDTest(int $jenkinsID)
    {
        global $tester;
        $jenkins = $tester->dao->select('*')->from(TABLE_PIPELINE)->where('id')->eq($jenkinsID)->fetch();
        return $this->jenkins->getApiUserPWD($jenkins);
    }
}
