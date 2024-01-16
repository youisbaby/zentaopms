<?php
namespace zin;
global $lang;

$fields = defineFieldList('program.edit', 'program');

$fields->field('begin')
    ->control('inputGroup')
    ->required()
    ->label($lang->project->dateRange)
    ->itemBegin('begin')->require()->type('datePicker')->value(data('program.begin'))->placeholder($lang->project->begin)->itemEnd()
    ->itemBegin()->type('addon')->label($lang->project->to)->text($lang->colon)->itemEnd()
    ->itemBegin('end')->require()->type('datePicker')->value(data('program.end'))->placeholder($lang->project->end)->itemEnd();

$fields->field('budget')
    ->checkbox(array('name' => 'future', 'text' => $lang->project->future, 'checked' => data('program.budget') == 0 ? true : false))
    ->itemBegin('budget')->control('input')->value(data('program.budget'))->disabled(data('program.budget') == 0 ? true : false)->itemEnd()
    ->item(field('budgetUnit')->required()->disabled(data('program.parent') ? true : false)->control('picker')->name('budgetUnit')->items(data('budgetUnitList'))->value(data('program.parent') ? data('parentProgram.budgetUnit') : $config->project->defaultCurrency))
    ->itemBegin('syncPRJUnit')->className('hidden')->value('false')->itemEnd()
    ->itemBegin('exchangeRate')->className('hidden')->value('')->itemEnd();

$fields->field('acl')
    ->items(data('program.parent') ? $lang->program->subAclList : $lang->program->aclList);

$fields->field('whitelist')
    ->hidden(data('program.acl') == 'open');
