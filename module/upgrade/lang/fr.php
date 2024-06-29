<?php
/**
 * The upgrade module English file of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.cnezsoft.com)
 * @license     ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     upgrade
 * @version     $Id: en.php 5119 2013-07-12 08:06:42Z wyd621@gmail.com $
 * @link        https://www.zentao.net
 */
global $config;
$lang->upgrade->common          = 'Mise à jour';
$lang->upgrade->start           = 'Start';
$lang->upgrade->result          = 'Résultat';
$lang->upgrade->fail            = 'Echec';
$lang->upgrade->successTip      = 'Mise à jour effectuée';
$lang->upgrade->success         = 'Mise à jour effectuée';
$lang->upgrade->tohome          = 'Visitez ZenTao';
$lang->upgrade->license         = 'ZenTao est sous Z PUBLIC LICENSE(ZPL) 1.2.';
$lang->upgrade->warnning        = 'Attention!';
$lang->upgrade->checkExtension  = 'Vérifiez Extensions';
$lang->upgrade->consistency     = 'Vérifiez Consistence';
$lang->upgrade->warnningContent = <<<EOT
<p>The upgrade requires high database privileges, please use the root user.</p>
<p>Please backup your database before updating ZenTao!</p>
<pre class='bg-white space-y-2 p-3'>
1. Use phpMyAdmin to backup.
2. Use mysqlCommand to backup.
   $> mysqldump -u <span class='text-danger'>username</span> -p <span class='text-danger'>dbname</span> > <span class='text-danger'>filename</span>
   Change the red text into corresponding Username and Database name.
   e.g. mysqldump -u root -p zentao > zentao.bak
</pre>
EOT;

if($config->db->driver == 'dm')
{
    $lang->upgrade->warnningContent = <<<EOT
<p>The upgrade requires high database privileges, please use the root user.<br>
   Please backup your database before updating ZenTao!</p>
<pre>
1. It can be backed up by graphical client tools.
2. Use DIsql tool to back up data.
   $> BACKUP DATABASE BACKUPSET  <span class='text-danger'>'filename'</span>;
   After the statement is executed, a backup set directory named "filename" is generated in the default backup path.
   The default backup path is the path configured with BAK_PATH in dm.ini. If BAK_PATH is not configured, bak in SYSTEM_PATH is used by default.
   This is the simplest database backup statement,To set additional backup options, you need to understand the syntax of the online backup database.
</pre>
EOT;
}

$lang->upgrade->createFileWinCMD   = 'Ouvrez la fenêtre Ligne de commandes de windows et exécutez <strong style="color:#ed980f">echo > %s</strong>';
$lang->upgrade->createFileLinuxCMD = 'Executez la ligne de commande suivante: <strong style="color:#ed980f">touch %s</strong>';
$lang->upgrade->setStatusFile      = '<h4>Please complete the following actions</h4>
                                      <ul style="line-height:1.5;font-size:13px;">
                                      <li>%s</li>
                                      <li>Or delete "<strong style="color:#ed980f">%s</strong>" and create <strong style="color:#ed980f">ok.txt</strong> and leave it blank.</li>
                                      </ul>
                                      <p><strong style="color:red">I have read and done as instructed above. <a href="upgrade.php">Continue upgrading.</a></strong></p>';

$lang->upgrade->selectVersion = 'Version';
$lang->upgrade->continue      = 'Continuer';
$lang->upgrade->noteVersion   = "Sélectionnez une version compatible où vous pourriez perdre des données.";
$lang->upgrade->fromVersion   = 'De';
$lang->upgrade->toVersion     = 'à';
$lang->upgrade->confirm       = 'Confirmez SQL';
$lang->upgrade->sureExecute   = 'Executez';
$lang->upgrade->upgradingTips = 'La mise à jour est en cours, veuillez être patient. Ne pas actualiser la page ou éteindre votre ordinateur!';
$lang->upgrade->forbiddenExt  = 'Cette extension est incompatible avec la version. Elle a été désactivée :';
$lang->upgrade->updateFile    = "Le fichier information a besoin d'une mise à jour.";
$lang->upgrade->showSQLLog    = 'Your database is inconsistent with the standard and try fix it.';
$lang->upgrade->noticeErrSQL  = 'Votre base de donnée est inconsistente avec le standard et il y a eu un échec pour la corriger. Exécutez la commande SQL suivante et rafraichissez.';
$lang->upgrade->afterDeleted  = "Le fichier n'est pas supprimé. Recommencez après l'avoir supprimé.";
$lang->upgrade->afterExec     = 'Please modify the database manually according to the above error information, and refresh after the modification!';
$lang->upgrade->mergeProgram  = 'Data Merge';
$lang->upgrade->mergeTips     = 'Data Migration Tips';
$lang->upgrade->toPMS15Guide  = 'ZenTao open source version 15 upgrade';
$lang->upgrade->toPRO10Guide  = 'ZenTao profession version 10 upgrade';
$lang->upgrade->toBIZ5Guide   = 'ZenTao enterprise version 5 upgrade';
$lang->upgrade->toMAXGuide    = 'ZenTao ultimate version upgrade';

$lang->upgrade->line            = 'Product Line';
$lang->upgrade->allLines        = "All Product Lines";
$lang->upgrade->program         = 'Merge Project';
$lang->upgrade->existProgram    = 'Existing programs';
$lang->upgrade->existProject    = 'Existing projects';
$lang->upgrade->existLine       = 'Existing product lines';
$lang->upgrade->product         = $lang->productCommon;
$lang->upgrade->project         = 'Iteration';
$lang->upgrade->repo            = 'Repo';
$lang->upgrade->mergeRepo       = 'Merge Repo';
$lang->upgrade->setProgram      = 'Set the project to which the program belongs';
$lang->upgrade->setProject      = "Set the {$lang->executionCommon} to which the project belongs";
$lang->upgrade->dataMethod      = 'Data migration method';
$lang->upgrade->selectMergeMode = 'Please select the data merging method';
$lang->upgrade->mergeMode       = 'Data consolidation method : ';
$lang->upgrade->begin           = 'Begin Date';
$lang->upgrade->end             = 'End Date';
$lang->upgrade->unknownDate     = 'Unknown Date Project';
$lang->upgrade->selectProject   = 'The target project';
$lang->upgrade->programName     = 'Program Name';
$lang->upgrade->projectName     = 'Project Name';
$lang->upgrade->compatibleEXT   = 'Extension mechanism compatible';
$lang->upgrade->fileName        = 'File Name';
$lang->upgrade->next            = 'Next';
$lang->upgrade->back            = 'Back';

$lang->upgrade->newProgram        = 'Create';
$lang->upgrade->editedName        = 'New Name';
$lang->upgrade->projectEmpty      = 'Project must be not empty.';
$lang->upgrade->mergeSummary      = "Dear users, there are %s in your system waiting for Migration. By System Calculation, we recommend your migration plan as follows, you can also adjust according to your own situation:";
$lang->upgrade->productCount      = "%s {$lang->productCommon}";
$lang->upgrade->projectCount      = "%s {$lang->projectCommon}";
$lang->upgrade->mergeByProject    = "Currently, the following two data migration methods are available. If the historical projects are long term, we suggest upgrading the historical projects as projects.</br>If the historical projects are short cycle, we suggest that the historical projects be upgraded as iterations.";
$lang->upgrade->mergeRepoTips     = "Merge the selected version library under the selected product.";
$lang->upgrade->needBuild4Add     = 'Full text retrieval has been added in this upgrade. Need create index. Please go [Admin->System->BuildIndex] page to build index.';
$lang->upgrade->needChangeEngine  = 'The table engine needs to be replaced in this upgrade, Please go [Admin->System->TableEngine] page to replace engine.';
$lang->upgrade->errorEngineInnodb = 'Your MySQL does not support InnoDB data table engine. Please modify it to MyISAM and try again.';
$lang->upgrade->duplicateProject  = "Project name in the same program cannot be duplicate. Please adjust the duplicate names.";
$lang->upgrade->upgradeTips       = "Historically deleted data cannot be upgraded, and restoration is not supported after the upgrade. Please be aware.";
$lang->upgrade->moveEXTFileFail   = 'The migration file failed, please execute the above command and refresh!';
$lang->upgrade->deleteDirTip      = 'After the upgrade, the following folders will affect the use of system functions, please delete them.';
$lang->upgrade->errorNoProduct    = "Select the {$lang->productCommon} that you want to merge.";
$lang->upgrade->errorNoExecution  = "Select the {$lang->projectCommon} that you want to merge.";
$lang->upgrade->moveExtFileTip    = <<<EOT
<p>The new version will be compatible with the extension mechanism of the historical customization/plug-in. You need to migrate the customization/plug-in related files to extension/custom, otherwise the customization/plug-in function will not be available.</p>
<p>Please confirm whether the system has been customized/plug-in. If no customization/plug-in has been done, you can uncheck the following files; Whether you have done customization/plug-in, you can also keep the file checked.</p>
EOT;

$lang->upgrade->projectType['project']   = "Upgrade the historical {$lang->projectCommon} as a project";
$lang->upgrade->projectType['execution'] = "Upgrade the historical {$lang->projectCommon} as an execution";

$lang->upgrade->createProjectTip = <<<EOT
<p>After the upgrade, the existing {$lang->projectCommon} will be Project in the new version.</p>
<p>ZenTao will create an item in Execute with the same name of {$lang->projectCommon} according to the data in {$lang->projectCommon}, and move the tasks, stories, and bugs in {$lang->projectCommon} to it.</p>
EOT;

$lang->upgrade->createExecutionTip = <<<EOT
<p>ZenTao will upgrade existing {$lang->projectCommon} as execution.</p>
<p>After the upgrade, the data of existing {$lang->projectCommon} will be in a Project - Execute of the new version .</p>
EOT;

$lang->upgrade->mergeModes = array();
$lang->upgrade->mergeModes['project']   = 'Automatically merge data and upgrade historical projects as projects';
$lang->upgrade->mergeModes['execution'] = 'Automatically merge data and upgrade historical projects as executions';
$lang->upgrade->mergeModes['manually']  = 'Manually merge data';

$lang->upgrade->mergeProjectTip   = 'The historical project will be synchronized directly to the new version of the project. At the same time, the system will create an iteration with the same name as the project according to the historical project, and migrate the tasks, requirements, bugs and other data under the previous project to the iteration.';
$lang->upgrade->mergeExecutionTip = 'The system will automatically create projects by year, and merge the historical iteration data into the corresponding projects by year.';
$lang->upgrade->createProgramTip  = 'At the same time, the system will automatically create a default project set and place all projects under the default project set.';
$lang->upgrade->mergeManuallyTip  = 'You can manually select the data merging method.';

$lang->upgrade->defaultGroup = 'Default';

include dirname(__FILE__) . '/version.php';

$lang->upgrade->recoveryActions = new stdclass();
$lang->upgrade->recoveryActions->cancel = 'Cancel';
$lang->upgrade->recoveryActions->review = 'Review';

$lang->upgrade->remark     = 'Remark';
$lang->upgrade->remarkDesc = 'You can also switch the mode in the Admin-System-Mode page of the system.';

$lang->upgrade->upgradingTip = 'The system is being upgraded, please wait patiently...';

$lang->upgrade->addTraincoursePrivTips = "In order to facilitate everyone's learning of project management-related knowledge, we have made the courses and practical repositories of the academy accessible to all permission groups by default. This ensures that everyone can easily access the resources. However, if you do not require this feature, you can disable it in the backend feature switch.";

$lang->upgrade->storyStageList['']           = '';
$lang->upgrade->storyStageList['wait']       = 'Waiting';
$lang->upgrade->storyStageList['planned']    = 'Planned';
$lang->upgrade->storyStageList['projected']  = 'Projected';
$lang->upgrade->storyStageList['designing']  = 'Designing';
$lang->upgrade->storyStageList['designed']   = 'Designed';
$lang->upgrade->storyStageList['developing'] = 'Developing';
$lang->upgrade->storyStageList['developed']  = 'Developed';
$lang->upgrade->storyStageList['testing']    = 'Testing';
$lang->upgrade->storyStageList['tested']     = 'Tested';
$lang->upgrade->storyStageList['verified']   = 'Accepted';
$lang->upgrade->storyStageList['rejected']   = 'Verify Rejected';
$lang->upgrade->storyStageList['delivering'] = 'Delivering';
$lang->upgrade->storyStageList['delivered']  = 'Delivered';
$lang->upgrade->storyStageList['released']   = 'Released';
$lang->upgrade->storyStageList['closed']     = 'Closed';
