<?php
$lang->zahost->id             = 'ID';
$lang->zahost->common         = '宿主机';
$lang->zahost->browse         = '宿主机列表';
$lang->zahost->create         = '添加宿主机';
$lang->zahost->view           = '宿主机详情';
$lang->zahost->initTitle      = '初始化宿主机';
$lang->zahost->edit           = '编辑';
$lang->zahost->editAction     = '编辑宿主机';
$lang->zahost->delete         = '删除';
$lang->zahost->cancel         = "取消下载";
$lang->zahost->deleteAction   = '删除宿主机';
$lang->zahost->byQuery        = '搜索';
$lang->zahost->all            = '全部主机';
$lang->zahost->browseNode     = '执行节点列表';
$lang->zahost->deleted        = "已删除";
$lang->zahost->copy           = '复制';
$lang->zahost->copied         = '复制成功';

$lang->zahost->name        = '名称';
$lang->zahost->IP          = 'IP/域名';
$lang->zahost->extranet    = 'IP/域名';
$lang->zahost->memory      = '内存';
$lang->zahost->cpuCores    = 'CPU';
$lang->zahost->diskSize    = '硬盘容量';
$lang->zahost->desc        = '描述';
$lang->zahost->type        = '类型';
$lang->zahost->status      = '状态';

$lang->zahost->createdBy    = '由谁创建';
$lang->zahost->createdDate  = '创建时间';
$lang->zahost->editedBy     = '由谁修改';
$lang->zahost->editedDate   = '最后修改时间';
$lang->zahost->registerDate = '最后注册时间';

$lang->zahost->memorySize = $lang->zahost->memory;
$lang->zahost->cpuCoreNum = $lang->zahost->cpuCores;
$lang->zahost->os         = '操作系统';
$lang->zahost->imageName  = '镜像文件';

$lang->zahost->createZanode        = '创建执行节点';
$lang->zahost->initNotice          = '保存成功，请您初始化宿主机或返回列表。';
$lang->zahost->createZanodeNotice  = '初始化成功，您现在可以创建执行节点了。';
$lang->zahost->downloadImageNotice = '初始化成功，请下载镜像用于创建执行节点。';
$lang->zahost->undeletedNotice     = "宿主机下存在执行节点无法删除。";
$lang->zahost->uninitNotice        = '请先初始化宿主机';
$lang->zahost->netError            = '无法连接到宿主机，请检查网络后重试。';

$lang->zahost->init = new stdclass;
$lang->zahost->init->statusTitle = "服务状态";
$lang->zahost->init->checkStatus = "检测服务状态";
$lang->zahost->init->not_install = "未安装";
$lang->zahost->init->not_available = "已安装，未启动";
$lang->zahost->init->ready = "已就绪";
$lang->zahost->init->next = "下一步";

$lang->zahost->init->initFailNotice    = "服务未就绪，在宿主机上执行安装服务命令或<a href='https://github.com/easysoft/zenagent/' target='_blank'>查看帮助</a>.";
$lang->zahost->init->initSuccessNotice = "服务已就绪，您可以在%s后%s。";

$lang->zahost->init->serviceStatus = [
    "kvm"        => 'not_install',
    "nginx"      => 'not_install',
    "novnc"      => 'not_install',
    "websockify" => 'not_install',
];
$lang->zahost->init->title          = "初始化宿主机";
$lang->zahost->init->descTitle      = "请根据引导完成宿主机上的初始化: ";
$lang->zahost->init->initDesc       = "- 在宿主机上执行命令：%s %s <br>- 点击检测服务状态。";
$lang->zahost->init->statusTitle    = "服务状态";

$lang->zahost->image = new stdclass;
$lang->zahost->image->browseImage   = '镜像列表';
$lang->zahost->image->createImage   = '创建镜像';
$lang->zahost->image->choseImage    = '选择镜像';
$lang->zahost->image->downloadImage = '下载镜像';
$lang->zahost->image->startDowload  = '开始下载';

$lang->zahost->image->common     = '镜像';
$lang->zahost->image->name       = '名称';
$lang->zahost->image->desc       = '描述';
$lang->zahost->image->path       = '文件路径';
$lang->zahost->image->memory     = $lang->zahost->memory;
$lang->zahost->image->disk       = $lang->zahost->diskSize;
$lang->zahost->image->os         = $lang->zahost->os;
$lang->zahost->image->imageName  = $lang->zahost->imageName;
$lang->zahost->image->progress   = '下载进度';

$lang->zahost->image->statusList['notDownloaded'] = '可下载';
$lang->zahost->image->statusList['created']       = '下载中';
$lang->zahost->image->statusList['canceled']      = '可下载';
$lang->zahost->image->statusList['inprogress']    = '下载中';
$lang->zahost->image->statusList['completed']     = '可使用';
$lang->zahost->image->statusList['failed']        = '下载失败';

$lang->zahost->image->imageEmpty           = '无镜像';
$lang->zahost->image->downloadImageFail    = '创建下载镜像任务失败';
$lang->zahost->image->downloadImageSuccess = '创建下载镜像任务成功';
$lang->zahost->image->cancelDownloadFail    = '取消下载镜像任务失败';
$lang->zahost->image->cancelDownloadSuccess = '取消下载镜像任务成功';

$lang->zahost->empty         = '暂时没有宿主机';

$lang->zahost->statusList['wait']    = '待初始化';
$lang->zahost->statusList['ready']   = '在线';
$lang->zahost->statusList['online']  = '在线';
$lang->zahost->statusList['offline'] = '离线';
$lang->zahost->statusList['busy']    = '繁忙';

$lang->zahost->vsoft = '虚拟化软件';
$lang->zahost->softwareList['kvm'] = 'KVM';

$lang->zahost->unitList['GB'] = 'GB';
$lang->zahost->unitList['TB'] = 'TB';

$lang->zahost->zaHostType                 = '主机类型';
$lang->zahost->zaHostTypeList['physical'] = '实体主机';

$lang->zahost->confirmDelete           = '是否删除该宿主机记录？';
$lang->zahost->cancelDelete            = '是否取消该下载任务？';

$lang->zahost->notice = new stdclass();
$lang->zahost->notice->ip              = '『%s』格式不正确！';
$lang->zahost->notice->registerCommand = '宿主机注册命令：./zagent-host -t host -s http://%s:%s -i %s -p 8086 -secret %s';
$lang->zahost->notice->loading         = '加载中...';
$lang->zahost->notice->noImage         = '无可用的镜像文件';

$lang->zahost->tips = '宿主机包括实体主机、k8s集群、云服务器以及云容器实例，主要用于创建虚拟机或容器实例。宿主机推荐安装的操作系统为ubuntu或CentOS的LTS版本。';
