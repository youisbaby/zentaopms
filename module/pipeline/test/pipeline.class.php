<?php
class pipelineTest
{
    /**
     * __construct
     *
     * @access public
     * @return void
     */
    public function __construct(string $account = 'admin')
    {
        su($account);

        global $tester, $app;
        $this->objectModel = $tester->loadModel('pipeline');

        $app->rawModule = 'pipeline';
        $app->rawMethod = 'index';
        $app->setModuleName('pipeline');
        $app->setMethodName('index');
    }


    /**
     * 根据id获取一条服务器记录。
     * Get a pipeline by id.
     *
     * @param  int          $id
     * @access public
     * @return object|false
     */
    public function getByIDTest(int $id): object|false
    {
        $pipeline = $this->objectModel->getByID($id);

        if(dao::isError()) return dao::getError();
        return $pipeline;
    }

    /**
     * 获取服务器列表。
     * Get pipeline list.
     *
     * @param  string $type       jenkins|gitlab
     * @param  string $orderBy
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return array
     */
    public function getListTest(string $type = 'jenkins', string $orderBy = 'id_desc', int $recPerPage = 20, int $pageID = 1): array
    {
        $this->objectModel->app->loadClass('pager', true);

        $pager        = new pager(0, $recPerPage, $pageID);
        $pipelineList = $this->objectModel->getList($type, $orderBy, $pager);

        if(dao::isError()) return dao::getError();
        return $pipelineList;
    }

    /**
     * 创建服务器。
     * Create a server.
     *
     * @param  string       $type
     * @param  array        $object
     * @access public
     * @return array|object
     */
    public function createTest(string $type, object $object)
    {
        $object->type = $type;

        $pipelineID = $this->objectModel->create($object);

        if(dao::isError()) return dao::getError();
        return $this->objectModel->getByID($pipelineID);
    }

    /**
     * Update a pipeline.
     *
     * @param  int    $id
     * @access public
     * @return bool
     */
    public function updateTest($id)
    {
        $objects = $this->objectModel->update($id);

        if(dao::isError()) return dao::getError();

        $objects = $this->objectModel->getByID($id);

        return $objects;
    }

    /**
     * Delete one record.
     *
     * @param  string $id     the id to be deleted
     * @param  string $object the action object
     * @access public
     * @return int
     */
    public function deleteTest($id, $object = 'gitlab')
    {
        $objects = $this->objectModel->deleteByObject($id, $object);

        if(dao::isError()) return dao::getError();

        $objects = $this->objectModel->getById($id);

        return $objects;
    }

    /**
     * 根据名称及类型获取一条流水线记录。
     * Get a pipeline by name and type.
     *
     * @param  string $name
     * @param  string $type
     * @access public
     * @return object|false|array
     */
    public function getByNameAndTypeTest(string $name, string $type): object|false|array
    {
        $pipeline = $this->objectModel->getByNameAndType($name, $type);

        if(dao::isError()) return dao::getError();
        return $pipeline;
    }

    /**
     * 根据url获取渠成创建的代码库。
     * Get a pipeline by url which created by quickon.
     *
     * @param  string $url
     * @access public
     * @return object|false|array
     */
    public function getByUrlTest(string $url): object|false|array
    {
        $pipeline = $this->objectModel->getByUrl($url);

        if(dao::isError()) return dao::getError();
        return $pipeline;
    }

    /**
     * 获取服务器列表。
     * Get pipeline pairs.
     *
     * @param  string $type
     * @access public
     * @return array
     */
    public function getPairsTest(string $type): array
    {
        $pipelinePairs = $this->objectModel->getPairs($type);

        if(dao::isError())  return dao::getError();
        return $pipelinePairs;
    }
}
