<?php

namespace iEducar\Packages\PreMatricula\Support\Database;

use Exception;

/**
 * @codeCoverageIgnore
 */
trait MigrateDefiner
{
    use MigrationUtils;
    use OwnDatabase;

    /**
     * Run the migrations.
     *
     * @return void
     *
     * @throws Exception
     */
    public function up()
    {
        $this->useOwnDatabase()
            ? $this->createTable()
            : $this->createThirdPartyView();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     *
     * @throws Exception
     */
    public function down()
    {
        $this->useOwnDatabase()
            ? $this->dropTable()
            : $this->dropThirdPartyView();
    }

    /**
     * Create view in third party database.
     *
     * @return void
     *
     * @throws Exception
     */
    public function createThirdPartyView()
    {
        $this->checkViewData();
        $this->dropView($this->viewName);
        $this->executeSqlFile($this->viewFile);
    }

    /**
     * Drop view in third party database.
     *
     * @return void
     *
     * @throws Exception
     */
    public function dropThirdPartyView()
    {
        $this->checkViewData();
        $this->dropView($this->viewName);
    }

    /**
     * @throws Exception
     */
    private function checkViewData()
    {
        if (empty($this->viewName)) {
            throw new Exception('View name not defined.');
        }

        if (empty($this->viewFile)) {
            throw new Exception('View filename not defined.');
        }
    }

    abstract public function createTable();

    abstract public function dropTable();
}
