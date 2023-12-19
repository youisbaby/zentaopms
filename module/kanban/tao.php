<?php
declare(strict_types=1);
/**
 * The tao file of kanban module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2021 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.cnezsoft.com)
 * @license     ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Sun Guangming <sunguangming@easycorp.ltd>
 * @package     kanban
 * @version     $Id: model.php 5118 2021-10-22 10:18:41Z $
 * @link        https://www.zentao.net
 */
class kanbanTao extends kanbanModel
{
    /*
     * 创建看板。
     * Create a kanban.
     *
     * @param  object $kanban
     * @access public
     * @return int
     */
    protected function createKanban($kanban)
    {
        $this->dao->insert(TABLE_KANBAN)->data($kanban)
             ->autoCheck()
             ->batchCheck($this->config->kanban->create->requiredFields, 'notempty')
             ->checkIF(!$kanban->fluidBoard, 'colWidth', 'ge', $this->config->minColWidth)
             ->batchCheckIF($kanban->fluidBoard, 'minColWidth', 'ge', $this->config->minColWidth)
             ->checkIF($kanban->fluidBoard && $kanban->minColWidth >= $this->config->minColWidth, 'maxColWidth', 'gt', $kanban->minColWidth)
             ->check('name', 'unique', "space = {$kanban->space}")
             ->exec();
    }

    /**
     * 构造看板区域的数据结构。
     * Update a kanban.
     *
     * @param  array  $regionData
     * @param  array  $groups
     * @param  array  $laneGroup
     * @param  array  $columnGroup
     * @param  array  $cardGroup
     * @access public
     * @return array
     */
    protected function buildRegionData(array $regionData, array $groups, array $laneGroup, array $columnGroup, array $cardGroup): array
    {
        $laneCount  = 0;
        $groupData  = array();
        foreach($groups as $group)
        {
            $lanes = zget($laneGroup, $group->id, array());
            if(!$lanes) continue;

            $cols  = zget($columnGroup, $group->id, array());
            $items = zget($cardGroup, $group->id, array());

            /* 计算各个列上的卡片数量。 */
            $columnCount = array();
            $parentCols  = array();
            foreach($cols as $col) $parentCols[$col['id']] = $col['parent'];
            foreach($items as $colGroup)
            {
                foreach($colGroup as $colID => $cards)
                {
                    if(!isset($columnCount[$colID])) $columnCount[$colID] = 0;
                    $columnCount[$colID] += count($cards);

                    if(isset($parentCols[$colID]) && $parentCols[$colID] > 0)
                    {
                        if(!isset($columnCount[$parentCols[$colID]])) $columnCount[$parentCols[$colID]] = 0;
                        $columnCount[$parentCols[$colID]] += count($cards);
                    }
                }
            }

            foreach($cols as $colIndex => $col) $cols[$colIndex]['cards'] = isset($columnCount[$col['id']]) ? $columnCount[$col['id']] : 0;

            $laneCount += count($lanes);

            $groupData['id']            = $group->id;
            $groupData['key']           = "group{$group->id}";
            $groupData['data']['lanes'] = $lanes;
            $groupData['data']['cols']  = $cols;
            $groupData['data']['items'] = $items;

            $regionData['items'][] = $groupData;
        }
        $regionData['laneCount'] = $laneCount;

        return $regionData;
    }

    /**
     * 更新看板区域的排序。
     * Update sort of kanban region.
     *
     * @param  array  $regionIdList
     * @access public
     * @return void
     */
    protected function updateRegionSort(array $regionIdList)
    {
        $order = 1;
        foreach($regionIdList as $regionID)
        {
            $this->dao->update(TABLE_KANBANREGION)->set('`order`')->eq($order)->where('id')->eq($regionID)->exec();
            $order++;
        }
    }

    /**
     * 更新看板泳道的排序。
     * Update sort of kanban lane.
     *
     * @param  int    $regionID
     * @param  array  $lanes
     * @access public
     * @return void
     */
    protected function updateLaneSort(int $regionID, array $lanes)
    {
        $order = 1;
        foreach($lanes as $laneID)
        {
            $this->dao->update(TABLE_KANBANLANE)->set('`order`')->eq($order)->where('id')->eq($laneID)->andWhere('region')->eq($regionID)->exec();
            $order++;
        }
    }

    /**
     * 更新看板列的排序。
     * Update sort of kanban lane.
     *
     * @param  int    $regionID
     * @param  array  $columns
     * @access public
     * @return void
     */
    protected function updateColumnSort(int $regionID, array $columns)
    {
        $order = 1;
        foreach($columns as $columnID)
        {
            $this->dao->update(TABLE_KANBANCOLUMN)->set('`order`')->eq($order)->where('id')->eq($columnID)->andWhere('region')->eq($regionID)->exec();
            $order ++;
        }
    }

    /**
     * 为看板子列添加看板单元格。
     * Add kanban cell for child column.
     *
     * @param  int    $columnID
     * @param  int    $childColumnID
     * @param  int    $i
     * @access public
     * @return void
     */
    protected function addChildColumnCell(int $columnID, int $childColumnID, int $i = 0)
    {
        $cellList = $this->dao->select('*')->from(TABLE_KANBANCELL)->where('`column`')->eq($columnID)->fetchAll();
        foreach($cellList as $cell)
        {
            $newCell = new stdclass();
            $newCell->kanban = $cell->kanban;
            $newCell->lane   = $cell->lane;
            $newCell->column = $childColumnID;
            $newCell->type   = 'common';
            $newCell->cards  = $i == 0 ? $cell->cards : '';

            $this->dao->insert(TABLE_KANBANCELL)->data($newCell)->exec();
            $this->dao->update(TABLE_KANBANCELL)->set('cards')->eq('')->where('id')->eq($cell->id)->exec();
        }
    }

    /**
     * 更新看板列的父列。
     * Update parent column of kanban column.
     *
     * @param  object $column
     * @access public
     * @return void
     */
    protected function updateColumnParent(object $column)
    {
        $children = $this->dao->select('count(*) as count')->from(TABLE_KANBANCOLUMN)
            ->where('parent')->eq($column->parent)
            ->andWhere('id')->ne($column->id)
            ->andWhere('deleted')->eq('0')
            ->andWhere('archived')->eq('0')
            ->fetch('count');

        if(!$children) $this->dao->update(TABLE_KANBANCOLUMN)->set('parent')->eq(0)->where('id')->eq($column->parent)->exec();
    }

    /**
     * 转入卡片时，更新卡片的指派人。
     * Update assignedTo of card when move in.
     *
     * @param  int    $cardID
     * @param  string $oldAssignedToList
     * @param  array  $users
     * @access public
     * @return void
     */
    protected function updateCardAssignedTo($cardID, $oldAssignedToList, $users)
    {
        $assignedToList = explode(',', $oldAssignedToList);
        foreach($assignedToList as $index => $account)
        {
            if(!isset($users[$account])) unset($assignedToList[$index]);
        }

        $assignedToList = implode(',', $assignedToList);
        $assignedToList = trim($assignedToList, ',');

        if($oldAssignedToList != $assignedToList)
        {
            $this->dao->update(TABLE_KANBANCARD)->set('assignedTo')->eq($assignedToList)->where('id')->eq($cardID)->exec();
        }
    }

    /**
     * 获取可转入卡片的产品。
     * Get products can be imported.
     *
     * @param  string $objectType
     * @access public
     * @return array
     */
    protected function getCanImportProducts($objectType = 'productplan'): array
    {
        $productPairs = $this->loadModel('product')->getPairs('', 0, '', 'all');

        $excludeProducts = array();
        if($objectType == 'productPlan')
        {
            $excludeProducts = $this->dao->select('t1.product')->from(TABLE_PROJECTPRODUCT)->alias('t1')
                ->leftJoin(TABLE_PROJECT)->alias('t2')->on('t1.project = t2.id')
                ->where('t2.type')->eq('project')
                ->andWhere('t2.model')->ne('scrum')
                ->andWhere('t2.hasProduct')->eq('0')
                ->fetchPairs();
        }
        elseif($objectType == 'release')
        {
            $excludeProducts = $this->dao->select('t1.product')->from(TABLE_PROJECTPRODUCT)->alias('t1')
                ->leftJoin(TABLE_PROJECT)->alias('t2')->on('t1.project = t2.id')
                ->where('t2.type')->eq('project')
                ->andWhere('t2.model')->eq('kanban')
                ->andWhere('t2.hasProduct')->eq('0')
                ->fetchPairs();
        }

        if(!empty($excludeProducts))
        {
            foreach($productPairs as $id)
            {
                if(isset($excludeProducts[$id])) unset($productPairs[$id]);
            }
        }

        return array($this->lang->kanban->allProducts) + $productPairs;
    }

    /**
     * 构造导入卡片的数据结构。
     * Build import card data structure.
     *
     * @param  object $objectType
     * @param  object $object
     * @param  string $fromType
     * @param  array  $creators
     * @access public
     * @return object
     */
    protected function buildObjectCard(object $objectCard, object $object, string $fromType, array $creators): object
    {
        if($fromType == 'productplan' or $fromType == 'release')
        {
            $objectCard->createdBy = zget($creators, $object->id, '');
            $objectCard->delay     = helper::today() > $objectCard->end ? true : false;
        }

        if($fromType =='execution')
        {
            if($object->status != 'done' and $object->status != 'closed' and $object->status != 'suspended')
            {
                $delay = helper::diffDate(helper::today(), $object->end);
                if($delay > 0) $objectCard->delay = $delay;
            }
            $objectCard->execType = $object->type;
            $objectCard->progress = $object->progress;

            $parentExecutions  = $this->dao->select('id,name')->from(TABLE_EXECUTION)->where('id')->in(trim($object->path, ','))->andWhere('type')->in('stage,kanban,sprint')->orderBy('grade')->fetchPairs();
            $objectCard->title = implode('/', $parentExecutions);

            $children             = $this->dao->select('count(1) as children')->from(TABLE_EXECUTION)->where('parent')->eq($object->id)->andWhere('type')->in('stage,kanban,sprint')->andWhere('deleted')->eq(0)->fetch('children');
            $objectCard->children = !empty($children) ? $children : 0;
        }

        $objectCard->desc         = strip_tags(htmlspecialchars_decode($object->desc));
        $objectCard->objectStatus = $objectCard->status;
        $objectCard->status       = $objectCard->progress == 100 ? 'done' : 'doing';

        return $objectCard;
    }

    /**
     * 获取执行下看板的来源看板Cell。
     * Get source kanban cell of execution.
     *
     * @param  int    $cardID
     * @param  int    $executionID
     * @param  int    $fromColID
     * @param  int    $fromLaneID
     * @param  string $groupBy
     * @param  string $browseType
     * @access public
     * @return object
     */
    protected function getExecutionFromCell(int $cardID, int $executionID, int $fromColID, int $fromLaneID, string $groupBy, string $browseType): object
    {
        return $this->dao->select('id, cards, lane')->from(TABLE_KANBANCELL)
                    ->where('kanban')->eq($executionID)
                    ->andWhere('`column`')->eq($fromColID)
                    ->beginIF(!$groupBy or $groupBy == 'default')->andWhere('lane')->eq($fromLaneID)->fi()
                    ->beginIF($groupBy and $groupBy != 'default')
                    ->andWhere('type')->eq($browseType)
                    ->andWhere('cards')->like("%,$cardID,%")
                    ->fi()
                    ->fetch();
    }

    /**
     * 获取执行下看板的目标看板Cell。
     * Get target kanban cell of execution.
     *
     * @param  int    $executionID
     * @param  int    $toColID
     * @param  int    $toLaneID
     * @access public
     * @return object
     */
    protected function getExecutionToCell(int $executionID, int $toColID, int $toLaneID): object
    {
        return $this->dao->select('id, cards')->from(TABLE_KANBANCELL)
                    ->where('kanban')->eq($executionID)
                    ->andWhere('lane')->eq($toLaneID)
                    ->andWhere('`column`')->eq($toColID)
                    ->fetch();
    }

    /**
     * 更新执行下看板的卡片。
     * Update card of execution.
     *
     * @param  int    $executionID
     * @param  int    $colID
     * @param  int    $laneID
     * @param  string $cards
     * @access public
     * @return void
     */
    protected function updateExecutionCell(int $executionID, int $colID, int $laneID, string $cards)
    {
        $this->dao->update(TABLE_KANBANCELL)->set('cards')->eq($cards)
              ->where('kanban')->eq($executionID)
              ->andWhere('lane')->eq($laneID)
              ->andWhere('`column`')->eq($colID)
              ->exec();
    }

    /**
     * 获取产品计划看板的分支
     * Get branches for product plan kanban.
     *
     * @param  object $product
     * @param  string $branchID
     * @access public
     * @return array
     */
    protected function getBranchesForPlanKanban(object $product, string $branchID): array
    {
        $this->loadModel('branch');

        $branches = array();
        if($product->type == 'normal')
        {
            $branches = array('all' => $this->lang->productplan->allAB);
        }
        elseif($branchID == 'all')
        {
            $branches = $this->branch->getPairs($product->id, 'active');
        }
        elseif($branchID == BRANCH_MAIN)
        {
            $branches = array(BRANCH_MAIN => $this->lang->branch->main);
        }
        elseif($branchID)
        {
            foreach(explode(',', $branchID) as $id)
            {
                $branchName = $this->branch->getById($id);
                $branches[$id] = $branchName;
            }
        }

        return $branches;
    }

    /**
     * 获取专业研发看板分组视图下的看板数据。
     * Get kanban data for group view of RD kanban.
     *
     * @param  object $execution
     * @param  string $browseType
     * @param  string $groupBy
     * @param  string $searchValue
     * @param  string $orderBy
     * @access public
     * @return array
     */
    protected function getRDKanbanByGroup(object $execution, string $browseType, string $orderBy, int $regionID, string $groupBy, string $searchValue): array
    {
        $regionData = array();
        $heading          = new stdclass();
        $heading->title   = $execution->name;
        $heading->actions = $this->getRDRegionActions($execution->id, $regionID);

        $regionData['key']               = "region{$execution->id}";
        $regionData['id']                = $execution->id;
        $regionData['heading']           = $heading;
        $regionData['toggleFromHeading'] = true;
        $regionData['items']             = $this->getKanban4Group($execution->id, $browseType, $groupBy, $searchValue, $orderBy);

        $kanbanList[] = $regionData;
        return $kanbanList;
    }

    /**
     * 获取专业研发看板分组视图下的看板数据。
     * Get kanban data for group view of RD kanban.
     *
     * @param  array  $regionData
     * @param  array  $groups
     * @param  array  $laneGroup
     * @param  array  $columnGroup
     * @param  array  $cardGroup
     * @param  string $searchValue
     * @access public
     * @return array
     */
    protected function buildRDRegionData(array $regionData, array $groups, array $laneGroup, array $columnGroup, array $cardGroup, string $searchValue = '')
    {
        $laneCount = 0;
        $groupData = array();
        foreach($groups as $group)
        {
            $lanes = zget($laneGroup, $group->id, array());
            if(!$lanes) continue;

            $cols  = zget($columnGroup, $group->id, array());
            $items = zget($cardGroup, $group->id, array());

            if($searchValue != '' and empty($items)) continue;

            /* 计算各个列上的卡片数量。 */
            $columnCount = array();
            $parentCols  = array();
            foreach($cols as $col) $parentCols[$col['id']] = $col['parent'];
            foreach($items as $colGroup)
            {
                foreach($colGroup as $colID => $cards)
                {
                    if(!isset($columnCount[$colID])) $columnCount[$colID] = 0;
                    $columnCount[$colID] += count($cards);

                    if(isset($parentCols[$colID]) && $parentCols[$colID] > 0)
                    {
                        if(!isset($columnCount[$parentCols[$colID]])) $columnCount[$parentCols[$colID]] = 0;
                        $columnCount[$parentCols[$colID]] += count($cards);
                    }
                }
            }

            foreach($cols as $colIndex => $col) $cols[$colIndex]['cards'] = isset($columnCount[$col['id']]) ? $columnCount[$col['id']] : 0;

            $lanes = array_values($lanes);
            $laneCount += count($lanes);

            $groupData['id']            = $group->id;
            $groupData['key']           = "group{$group->id}";
            $groupData['data']['lanes'] = $lanes;
            $groupData['data']['cols']  = $cols;
            $groupData['data']['items'] = $items;

            $regionData['items'][] = $groupData;
        }

        $regionData['laneCount'] = $laneCount;

        return $regionData;
    }

    /**
     * 初始化卡片数据。
     * Init card data.
     *
     * @param  object $card
     * @param  object $cell
     * @param  int    $order
     * @param  array  $avatarPairs
     * @param  array  $users
     * @access public
     * @return array
     */
    protected function initCardItem(object $card, object $cell, int $order, array $avatarPairs, array $users): array
    {
        $item = array();
        $item['column']       = $cell->column;
        $item['lane']         = $cell->lane;
        $item['title']        = !empty($card->title) ? htmlspecialchars_decode($card->title) : htmlspecialchars_decode($card->name);
        $item['id']           = $card->id;
        $item['name']         = $card->id;
        $item['pri']          = $card->pri;
        $item['color']        = $card->color;
        $item['assignedTo']   = $card->assignedTo;
        $item['progress']     = !empty($card->progress) ? $card->progress : 0;
        $item['group']        = !empty($card->group) ? $card->group : '';
        $item['region']       = !empty($card->region) ? $card->region : '';
        $item['begin']        = !empty($card->begin) ? $card->begin : '';
        $item['end']          = !empty($card->end) ? $card->end  : '';
        $item['fromID']       = !empty($card->fromID) ? $card->fromID : 0;
        $item['fromType']     = !empty($card->fromType) ? $card->fromType : '';
        $item['desc']         = !empty($card->desc) ? $card->desc : '';
        $item['delay']        = !empty($card->delay) ? $card->delay : 0;
        $item['status']       = !empty($card->status) ? $card->status : '';
        $item['objectStatus'] = !empty($card->objectStatus) ? $card->objectStatus : '';
        $item['deleted']      = !empty($card->deleted) ? $card->deleted : 0;
        $item['date']         = !empty($card->date) ? $card->date : '';
        $item['estimate']     = !empty($card->estimate) ? $card->estimate : '';
        $item['deadline']     = !empty($card->deadline) ? $card->deadline : '';
        $item['severity']     = !empty($card->severity) ? $card->severity : '';
        $item['cardType']     = $cell->type;
        $item['avatarList']   = array();
        $item['realnames']    = '';
        $item['order']        = $order;

        if($card->assignedTo)
        {
            $assignedToList = explode(',', $card->assignedTo);
            foreach($assignedToList as $account)
            {
                if(!$account) continue;

                $userAvatar = zget($avatarPairs, $account, '');
                $userAvatar = $userAvatar ? "<img src='$userAvatar'/>" : strtoupper(mb_substr($account, 0, 1, 'utf-8'));
                $item['avatarList'][]  = $userAvatar;
                $item['realnames']    .= zget($users, $account, '') . ' ';
            }
        }

        if($cell->type == 'task')
        {
            $item['left']       = $card->left;
            $item['estStarted'] = $card->estStarted;
            $item['mode']       = $card->mode;
        }

        return $item;
    }

    /**
     * 处理看板中多人任务的指派人。
     * Process assignedTo of multi tasks.
     *
     * @param  array  $cardList
     * @access public
     * @return array
     */
    protected function appendTeamMember(array $cardList): array
    {
        $multiTasks = array();
        foreach($cardList as $id => $task)
        {
            if($task->mode == 'multi') $multiTasks[$id] = $task;
        }

        $taskTeams = $this->dao->select('t1.account,t1.task,t2.realname')->from(TABLE_TASKTEAM)->alias('t1')
            ->leftJoin(TABLE_USER)->alias('t2')->on('t1.account = t2.account')
            ->where('t1.task')->in(array_keys($multiTasks))
            ->orderBy('t1.order')
            ->fetchGroup('task', 'account');
        foreach($multiTasks as $taskID => $task)
        {
            $teamPairs = array();
            foreach($taskTeams[$taskID] as $account => $team) $teamPairs[$account] = $team->realname;
            $task->teamMember  = $teamPairs;
            $cardList[$taskID] = $task;
        }

        return $cardList;
    }

    /**
     * 构造看板的分组视图。
     * Build group view of kanban.
     *
     * @param  array $lanes
     * @param  array $columns
     * @param  array $cardGroup
     * @param  string $searchValue
     * @param  string $groupBy
     * @param  string $browseType
     * @param  array $menus
     * @access public
     * @return array
     */
    protected function buildGroupKanban(array $lanes, array $columns, array $cardGroup, string $searchValue, string $groupBy, string $browseType, array $menus): array
    {
        $columnList  = array();
        $laneList    = array();
        $cardList    = array();
        $avatarPairs = $this->loadModel('user')->getAvatarPairs();
        $users       = $this->loadModel('user')->getPairs('noletter');
        foreach($lanes as $laneID => $lane)
        {
            $laneData = array();
            $laneData['id']     = $groupBy . $laneID;
            $laneData['type']   = $browseType;
            $laneData['name']   = $laneData['id'];
            $laneData['region'] = $lane->execution;
            $laneData['title']  = (($groupBy == 'pri' or $groupBy == 'severity') and $laneID) ? $this->lang->$browseType->$groupBy . ':' . $lane->name : $lane->name;
            $laneData['color']  = $lane->color;
            $laneData['order']  = $lane->order;

            if(empty($laneID) and !in_array($groupBy, array('module', 'story', 'pri', 'severity'))) $laneID = '';

            /* Construct kanban column data. */
            foreach($columns as $column)
            {
                $cardIdList = array_unique(array_filter(explode(',', $column->cards)));
                $columnData = $this->buildGroupColumn($columnList, $column, $laneData, $browseType);

                list($cardCount, $cardData) = $this->buildGroupCard($cardGroup, $cardIdList, $column, (string)$laneID, $groupBy, $browseType, $searchValue, $avatarPairs, $users, $menus);

                $columnData['cards'] += $cardCount;
                $cardList[$laneData['id']][$column->column] = $cardData;
                $columnList[$column->column] = $columnData;
            }
            $laneList[] = $laneData;
        }

        foreach($columnList as $column)
        {
            if(isset($column['parentName']) && isset($columnList[$column['parentName']])) $columnList[$column['parentName']]['cards'] += $column['cards'];
        }

        return array($laneList, $columnList, $cardList);
    }

    /**
     * 构造看板分组视图的列。
     * Build column of group view.
     *
     * @param  array  $columnsL
     * @param  object $column
     * @param  array  $laneData
     * @param  string $browseType
     * @access public
     * @return array
     */
    protected function buildGroupColumn(array $columnList, object $column, array $laneData, string $browseType): array
    {
        if(!isset($columnList[$column->column]))
        {
            $columnData = array();
            $columnData['id']         = $column->column;
            $columnData['type']       = $column->columnType;
            $columnData['name']       = $column->column;
            $columnData['title']      = $column->columnName;
            $columnData['color']      = $column->color;
            $columnData['limit']      = $column->limit;
            $columnData['region']     = $laneData['region'];
            $columnData['laneName']   = $column->lane;
            $columnData['group']      = $browseType;
            $columnData['cards']      = 0;
            $columnData['actionList'] = array('setColumn', 'setWIP');
            if($column->parent > 0) $columnData['parentName'] = $column->parent;
        }
        else
        {
            $columnData = $columnList[$column->column];
        }

        return $columnData;
    }

    /**
     * 构造看板分组视图的卡片。
     * Build card of group view.
     *
     * @param  array  $cardGroup
     * @param  array  $cardIdList
     * @param  object $column
     * @param  string $laneID
     * @param  string $groupBy
     * @param  string $browseType
     * @param  string $searchValue
     * @param  array  $avatarPairs
     * @param  array  $users
     * @param  array  $menus
     * @access public
     * @return array
     */
    protected function buildGroupCard(array $cardGroup, array $cardIdList, object $column, string $laneID, string $groupBy, string $browseType, string $searchValue, array $avatarPairs, array $users, array $menus): array
    {
        $cardCount = 0;
        $cardList  = array();
        $objects   = zget($cardGroup, $column->columnType, array());
        foreach($cardIdList as $cardID)
        {
            $object = zget($objects, $cardID, array());

            if(empty($object)) continue;
            if(in_array($groupBy, array('module', 'story', 'pri', 'severity')) and $object->$groupBy != $laneID) continue;
            if(in_array($groupBy, array('type', 'category', 'source')) and $object->$groupBy != $laneID) continue;
            if($groupBy == 'assignedTo')
            {
                $laneID = (string)$laneID;
                if(empty($object->$groupBy)) $object->$groupBy = '';
                if(empty($object->teamMember) and $object->$groupBy != $laneID) continue;
                if(!empty($object->teamMember) and !in_array($laneID, array_keys($object->teamMember), true)) continue;
            }

            $cardData = $this->buildExecutionCard($object, $column, $browseType, $searchValue, $menus);
            if(empty($cardData)) continue;

            $cardData['cardType'] = $browseType;
            if($groupBy == 'assignedTo' && $object->$groupBy !== $laneID) $cardData['assignedTo'] = $laneID;
            if($cardData['assignedTo'])
            {
                $userAvatar = zget($avatarPairs, $cardData['assignedTo'], '');
                $userAvatar = $userAvatar ? "<img src='$userAvatar'/>" : strtoupper(mb_substr($cardData['assignedTo'], 0, 1, 'utf-8'));
                $cardData['avatarList'][] = $userAvatar;
                $cardData['realnames']    = zget($users, $cardData['assignedTo'], '');
            }
            $cardList[] = $cardData;
            $cardCount ++;
        }

        return array($cardCount, $cardList);
    }
}
