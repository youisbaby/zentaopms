<?php
declare(strict_types=1);
/**
 * The ajaxgetdropmenu view file of dimension module of ZenTaoPMS.
 *
 * @copyright   Copyright 2023 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @author      Sun Guangming<sunguangming@easycorp.ltd>
 * @package     project
 * @version     $Id
 * @link        https://www.zentao.net
 */
namespace zin;

/**
 * 定义最终的 JSON 数据。
 * Define the final json data.
 */
$json = array();
$json['data']       = $items;
$json['searchHint'] = $lang->searchAB;
$json['link']       = array('dimension' => $link);
$json['labelMap']   = array('dimension' => $lang->dimension->common);
$json['expandName'] = 'closed';
$json['itemType']   = 'dimension';

/**
 * 渲染 JSON 字符串并发送到客户端。
 * Render json data to string and send to client.
 */
renderJson($json);
