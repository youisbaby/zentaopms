#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/ui.php';
class testcase extends tester
{
    public function createTestCase($project = array(), $testcase = array())
    {
        $this->login();
        $form = $this->initForm('testcase', 'create',$project, 'appIframe-qa');
        if(isset($testcase['caseName']))   $form->dom->title->setValue($testcase['caseName']);
        if(isset($testcase['type']))       $form->dom->type->picker($testcase['type']);
        if(isset($testcase['stage']))      $form->dom->{'stage[]'}->multiPicker($testcase['stage']);
        if(isset($testcase['pri']))        $form->dom->pri->picker($testcase['pri']);
        if(isset($testcase['prediction'])) $form->dom->prediction->setValue($testcase['prediction']);
        if(isset($testcase['steps']))
        {
            $parentGroup = 0;
            foreach($testcase['steps'] as $parentSteps => $parentExpects)
            {
                $parentGroup++;
                if(!is_array($parentExpects))
                {
                    $form->dom->{"steps[$parentGroup]"}->scrollToElement();
                    $form->dom->{"steps[$parentGroup]"}->setValue($parentSteps);
                    $form->dom->{"expects[$parentGroup]"}->setValue($parentExpects);
                }
                else
                {
                    $group = 0;
                    $subButton = "//textarea[@name = 'steps[$parentGroup]']/../..//button[@data-action='sub']/i";
                    $this->page->scrollToElement($subButton);
                    $this->page->click($subButton);
                    foreach($parentExpects as $steps => $expects)
                    {
                        $group++;
                        if(!is_array($expects))
                        {
                            $form->dom->{"steps[$parentGroup.$group]"}->scrollToElement();
                            $form->dom->{"steps[$parentGroup.$group]"}->setValue($steps);
                            $form->dom->{"expects[$parentGroup.$group]"}->setValue($expects);
                        }
                    }
                }
            }
        }
        $form->dom->btn($this->lang->save)->click();
        $this->webdriver->wait(1);

        $caseLists = $form->dom->caseName->getElementList($form->dom->page->xpath['caseNameList']);
        $caseList  = array_map(function($element){return $element->getText();}, $caseLists->element);
        if(in_array($testcase['caseName'], $caseList)) return $this->success('创建测试用例成功');
        return $this->failed('创建测试用例失败');
    }
}
