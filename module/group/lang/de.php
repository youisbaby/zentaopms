<?php
/**
 * The group module English file of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.cnezsoft.com)
 * @license     ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     group
 * @version     $Id: en.php 4719 2013-05-03 02:20:28Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
$lang->group->common             = 'Berechtigungen';
$lang->group->browse             = 'Gruppen Rechte';
$lang->group->create             = 'Gruppe hinzufügen';
$lang->group->edit               = 'Bearbeiten';
$lang->group->copy               = 'Kopieren';
$lang->group->delete             = 'Löschen';
$lang->group->manageView         = 'Anzeigen';
$lang->group->managePriv         = 'Mehrfachzuordnung';
$lang->group->managePrivByGroup  = 'Rechte';
$lang->group->managePrivByModule = 'Modul Rechte';
$lang->group->byModuleTips       = '<span class="tips">(SHIFT/STRG für Multi-Select)</span>';
$lang->group->allTips            = 'After checking this option, the administrator can manage all objects in the system, including objects created later.';
$lang->group->manageMember       = 'Mitglieder';
$lang->group->manageProjectAdmin = 'Manage Program Admins';
$lang->group->permissionedit     = 'Permission Edit';
$lang->group->confirmDelete      = "Do you want to delete '%s'?";
$lang->group->successSaved       = 'Gespeichert!';
$lang->group->errorNotSaved      = 'Fehlgeschlagen. Bitte aktion und Gruppe wählen.';
$lang->group->viewList           = 'Anzeige ist zulässig.';
$lang->group->object             = 'Manage Object';
$lang->group->manageProgram      = 'Manage Program';
$lang->group->manageProject      = 'Manage Project';
$lang->group->manageExecution    = 'Manage ' . $lang->execution->common;
$lang->group->manageProduct      = 'Manage ' . $lang->productCommon;
$lang->group->programList        = 'Access Program';
$lang->group->productList        = 'Produkte sind zugänglich.';
$lang->group->projectList        = 'Projekte sind zugänglich.';
$lang->group->executionList      = "Access {$lang->execution->common}";
$lang->group->dynamic            = 'Access Dynamics';
$lang->group->noticeVisit        = 'Leer bedeutet Zugriff verweigert.';
$lang->group->noneProgram        = "No Program";
$lang->group->noneProduct        = "No {$lang->productCommon}";
$lang->group->noneExecution      = "No {$lang->execution->common}";
$lang->group->project            = 'Project';
$lang->group->group              = 'Group';
$lang->group->more               = 'More';
$lang->group->allCheck           = 'All';
$lang->group->noGroup            = 'No group';
$lang->group->repeat             = "『%s』『%s』exists.Please adjust it and try again.";
$lang->group->noneProject        = 'No Project';
$lang->group->addPriv            = 'Add Priv';
$lang->group->add                = 'Add';
$lang->group->batchSetDependency = 'Batch Set Dependency';
$lang->group->managePrivPackage  = 'Manage Priv Package';
$lang->group->createPrivPackage  = 'Create Priv Package';
$lang->group->editPrivPackage    = 'Edit Priv Package';
$lang->group->deletePrivPackage  = 'Delete Priv Package';

$lang->group->id       = 'ID';
$lang->group->name     = 'Name';
$lang->group->desc     = 'Beschreibung';
$lang->group->role     = 'Rolle';
$lang->group->acl      = 'Rechte';
$lang->group->users    = 'Benutzer';
$lang->group->module   = 'Module';
$lang->group->method   = 'Methoden';
$lang->group->priv     = 'Gruppe';
$lang->group->option   = 'Option';
$lang->group->inside   = 'Gruppenbenutzer';
$lang->group->outside  = 'Andere Benutzer';
$lang->group->limited  = 'Limited Users';
$lang->group->other    = 'Andere';
$lang->group->all      = 'Alle';
$lang->group->config   = 'Config';

if(!isset($lang->privPackage)) $lang->privPackage = new stdclass();
$lang->privPackage->id     = 'ID';
$lang->privPackage->name   = 'Priv Package Name';
$lang->privPackage->module = 'Module';
$lang->privPackage->desc   = 'Priv Package Desc';

$lang->group->copyOptions['copyPriv'] = 'Rechte kopieren';
$lang->group->copyOptions['copyUser'] = 'Benutzer kopieren';

$lang->group->versions['']           = 'Verlauf';
$lang->group->versions['16_5_beta1'] = 'ZenTao16.5.beta1';
$lang->group->versions['16_4']       = 'ZenTao16.4';
$lang->group->versions['16_3']       = 'ZenTao16.3';
$lang->group->versions['16_2']       = 'ZenTao16.2';
$lang->group->versions['16_1']       = 'ZenTao16.1';
$lang->group->versions['16_0']       = 'ZenTao16.0';
$lang->group->versions['16_0_beta1'] = 'ZenTao16.0.beta1';
$lang->group->versions['15_8']       = 'ZenTao15.8';
$lang->group->versions['15_7']       = 'ZenTao15.7';
$lang->group->versions['15_0_rc1']   = 'ZenTao15.0.rc1';
$lang->group->versions['12_5']       = 'ZenTao12.5';
$lang->group->versions['12_3']       = 'ZenTao12.3';
$lang->group->versions['11_6_2']     = 'ZenTao11.6.2';
$lang->group->versions['10_6']       = 'ZenTao10.6';
$lang->group->versions['10_1']       = 'ZenTao10.1';
$lang->group->versions['10_0_alpha'] = 'ZenTao10.0.alpha';
$lang->group->versions['9_8']        = 'ZenTao9.8';
$lang->group->versions['9_6']        = 'ZenTao9.6';
$lang->group->versions['9_5']        = 'ZenTao9.5';
$lang->group->versions['9_2']        = 'ZenTao9.2';
$lang->group->versions['9_1']        = 'ZenTao9.1';
$lang->group->versions['9_0']        = 'ZenTao9.0';
$lang->group->versions['8_4']        = 'ZenTao8.4';
$lang->group->versions['8_3']        = 'ZenTao8.3';
$lang->group->versions['8_2_beta']   = 'ZenTao8.2.beta';
$lang->group->versions['8_0_1']      = 'ZenTao8.0.1';
$lang->group->versions['8_0']        = 'ZenTao8.0';
$lang->group->versions['7_4_beta']   = 'ZenTao7.4.beta';
$lang->group->versions['7_3']        = 'ZenTao7.3';
$lang->group->versions['7_2']        = 'ZenTao7.2';
$lang->group->versions['7_1']        = 'ZenTao7.1';
$lang->group->versions['6_4']        = 'ZenTao6.4';
$lang->group->versions['6_3']        = 'ZenTao6.3';
$lang->group->versions['6_2']        = 'ZenTao6.2';
$lang->group->versions['6_1']        = 'ZenTao6.1';
$lang->group->versions['5_3']        = 'ZenTao5.3';
$lang->group->versions['5_1']        = 'ZenTao5.1';
$lang->group->versions['5_0_beta2']  = 'ZenTao5.0.beta2';
$lang->group->versions['5_0_beta1']  = 'ZenTao5.0.beta1';
$lang->group->versions['4_3_beta']   = 'ZenTao4.3.beta';
$lang->group->versions['4_2_beta']   = 'ZenTao4.2.beta';
$lang->group->versions['4_1']        = 'ZenTao4.1';
$lang->group->versions['4_0_1']      = 'ZenTao4.0.1';
$lang->group->versions['4_0']        = 'ZenTao4.0';
$lang->group->versions['4_0_beta2']  = 'ZenTao4.0.beta2';
$lang->group->versions['4_0_beta1']  = 'ZenTao4.0.beta1';
$lang->group->versions['3_3']        = 'ZenTao3.3';
$lang->group->versions['3_2_1']      = 'ZenTao3.2.1';
$lang->group->versions['3_2']        = 'ZenTao3.2';
$lang->group->versions['3_1']        = 'ZenTao3.1';
$lang->group->versions['3_0_beta2']  = 'ZenTao3.0.beta2';
$lang->group->versions['3_0_beta1']  = 'ZenTao3.0.beta1';
$lang->group->versions['2_4']        = 'ZenTao2.4';
$lang->group->versions['2_3']        = 'ZenTao2.3';
$lang->group->versions['2_2']        = 'ZenTao2.2';
$lang->group->versions['2_1']        = 'ZenTao2.1';
$lang->group->versions['2_0']        = 'ZenTao2.0';
$lang->group->versions['1_5']        = 'ZenTao1.5';
$lang->group->versions['1_4']        = 'ZenTao1.4';
$lang->group->versions['1_3']        = 'ZenTao1.3';
$lang->group->versions['1_2']        = 'ZenTao1.2';
$lang->group->versions['1_1']        = 'ZenTao1.1';
$lang->group->versions['1_0_1']      = 'ZenTao1.0.1';

include (dirname(__FILE__) . '/resource.php');
