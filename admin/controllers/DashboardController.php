<?php

namespace Pandao\Admin\Controllers;

use Pandao\Common\Utils\DbUtils;

class DashboardController extends Controller
{
    /**
     * Display the dashboard page.
     *
     */
    public function index()
    {
        $modulesData = [];

        foreach ($this->adminContext->modules as $module) {
            $rights = $module->permissions;

            if ($module->dashboard && !in_array("no_access", $rights) && !empty($rights)) {
                $query = "SELECT count(id) AS num";
                if ($module->dates) {
                    $query .= ", MAX(add_date) AS last_add_date, MAX(edit_date) AS last_edit_date";
                }
                $query .= " FROM solutionsCMS_" . $module->name . " WHERE 1";
                if ($module->multi) {
                    $query .= " AND lang = " . PMS_DEFAULT_LANG;
                }

                if (!in_array($_SESSION['user']['type'], ["administrator", "manager", "editor"]) 
                    && DbUtils::dbColumnExists($this->pms_db, "solutionsCMS_" . $module->name, "users")) {
                    $query .= " AND users REGEXP '(^|,)" . $_SESSION['user']['id'] . "(,|$)'";
                }

                $result = @$this->pms_db->query($query);
                if ($result !== false && $this->pms_db->last_row_count() > 0) {
                    $row = $result->fetch();
                    $module->count = $row['num'];
                    if ($module->dates) {
                        $last_add_date = (!is_null($row['last_add_date'])) ? $row['last_add_date'] : 0;
                        $last_edit_date = (!is_null($row['last_edit_date'])) ? $row['last_edit_date'] : 0;

                        $last_date = max($last_edit_date, $last_add_date);
                        $last_date = ($last_date == 0) ? "" : date("Y-m-d g:ia", $last_date);
                        $module->last_date = $last_date;
                    }
                }

                $modulesData[] = $module;
            }
        }

        $this->viewData['modulesData'] = $modulesData;
        $this->render('dashboard', 'system');
    }
}
