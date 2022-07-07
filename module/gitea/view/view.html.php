<?php
/**
 * The view file of GitLab module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      dave.li  <lichengjun@cnezsoft.com>
 * @package     GitLab
 * @version     $Id: view.html.php 4728 2013-05-03 06:14:34Z david18810279601@gmail.com $
 * @link        http://www.zentao.net
 * */
?>
<?php include '../../common/view/header.html.php';?>
<style>.action-cell {margin-bottom: 10px;}</style>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <div class="page-title">
      <span class="label label-id"><?php echo $gitea->id?></span>
      <span class="text" title="<?php echo $gitea->name;?>" style='color: #3c4354'><?php echo $gitea->name;?></span>
      <?php if($gitea->deleted):?>
      <span class='label label-danger'><?php echo $lang->gitea->deleted;?></span>
      <?php endif; ?>
    </div>
  </div>
</div>
<div id="mainContent" class="main-row">
  <div class="main-col col-8">
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->gitea->url;?></div>
        <div class="detail-content article-content"><?php echo html::a($gitea->url, $gitea->url, '_target');?></div>
      </div>
    </div>
    <div class='cell action-cell'><?php include '../../common/view/action.html.php';?></div>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
