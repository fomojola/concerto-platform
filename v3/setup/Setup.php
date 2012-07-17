<?php

/*
  Concerto Platform - Online Adaptive Testing Platform
  Copyright (C) 2011-2012, The Psychometrics Centre, Cambridge University

  This program is free software; you can redistribute it and/or
  modify it under the terms of the GNU General Public License
  as published by the Free Software Foundation; version 2
  of the License, and not any of the later versions.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

class Setup {

    public static function get_db_update_steps_count() {
        return self::update_db(true);
    }

    public static function update_db_validate_column_names() {
        return self::update_db(false, false, false, true);
    }

    public static function create_db() {
        return self::update_db(false, false, false, false, true);
    }

    public static function update_db_repopulate_TestTemplate() {
        return self::update_db(false, false, true, true);
    }

    public static function update_db_recalculate_hash() {
        return self::update_db(false, true, true, true);
    }

    public static function php_version_check() {
        $v = phpversion();
        $nums = explode(".", $v);
        if ($nums[0] < 5)
            return json_encode(array("result" => 1, "param" => $v));
        if ($nums[0] == 5 && $nums[1] < 3)
            return json_encode(array("result" => 1, "param" => $v));
        if ($nums[0] == 5 && $nums[1] >= 3)
            return json_encode(array("result" => 0, "param" => $v));
        if ($nums[0] > 5)
            return json_encode(array("result" => 0, "param" => $v));
    }

    public static function php_safe_mode_check() {
        return json_encode(array("result" => !ini_get("safe_mode") ? 0 : 1, "param" => ini_get("safe_mode")));
    }

    public static function php_magic_quotes_check() {
        return json_encode(array("result" => !ini_get("magic_quotes_gpc") ? 0 : 1, "param" => ini_get("magic_quotes_gpc")));
    }

    public static function php_short_open_tag_check() {
        return json_encode(array("result" => ini_get("short_open_tag") ? 0 : 1, "param" => ini_get("magic_quotes_gpc")));
    }

    public static function php_exe_path_check() {
        require '../Ini.php';
        $ini = new Ini();
        return self::file_paths_check(Ini::$path_php_exe);
    }

    public static function R_exe_path_check() {
        require '../Ini.php';
        $ini = new Ini();
        return self::file_paths_check(Ini::$path_r_exe);
    }

    public static function file_paths_check($path) {
        if (file_exists($path) && is_file($path))
            return json_encode(array("result" => 0, "param" => $path));
        else
            return json_encode(array("result" => 1, "param" => $path));
    }

    public static function directory_paths_check($path) {
        if (file_exists($path) && is_dir($path))
            return true;
        else
            return false;
    }

    public static function directory_writable_check($path) {
        if (self::directory_paths_check($path) && is_writable($path))
            return true;
        else
            return false;
    }

    public static function media_directory_writable_check() {
        require '../Ini.php';
        $ini = new Ini();
        if (self::directory_writable_check(Ini::$path_internal_media))
            return json_encode(array("result" => 0, "param" => Ini::$path_internal_media));
        else
            return json_encode(array("result" => 1, "param" => Ini::$path_internal_media));
    }

    public static function socks_directory_writable_check() {
        require '../Ini.php';
        $ini = new Ini();
        if (self::directory_writable_check(Ini::$path_unix_sock_dir))
            return json_encode(array("result" => 0, "param" => Ini::$path_unix_sock_dir));
        else
            return json_encode(array("result" => 1, "param" => Ini::$path_unix_sock_dir));
    }

    public static function temp_directory_writable_check() {
        require '../Ini.php';
        $ini = new Ini();
        if (self::directory_writable_check(Ini::$path_temp))
            return json_encode(array("result" => 0, "param" => Ini::$path_temp));
        else
            return json_encode(array("result" => 1, "param" => Ini::$path_temp));
    }

    public static function files_directory_writable_check() {
        require '../Ini.php';
        $ini = new Ini();
        $path = Ini::$path_internal . "cms/js/lib/fileupload/php/files";
        if (self::directory_writable_check($path))
            return json_encode(array("result" => 0, "param" => $path));
        else
            return json_encode(array("result" => 1, "param" => $path));
    }

    public static function cache_directory_writable_check() {
        require '../Ini.php';
        $ini = new Ini();
        $path = Ini::$path_internal . "cms/lib/ckeditor/plugins/pgrfilemanager/PGRThumb/cache";
        if (self::directory_writable_check($path))
            return json_encode(array("result" => 0, "param" => $path));
        else
            return json_encode(array("result" => 1, "param" => $path));
    }

    public static function rscript_check() {
        require '../Ini.php';
        $ini = new Ini();
        $array = array();
        $return = 0;
        exec('"' . Ini::$path_r_script . '" -e 1+1', $array, $return);
        return json_encode(array("result" => $return, "param" => Ini::$path_r_script));
    }

    public static function r_version_check() {
        $version = Setup::get_r_version();
        $elems = explode(".", $version);
        if ($elems[0] > 2)
            return json_encode(array("result" => 0, "param" => $version));
        if ($elems[0] == 2) {
            if ($elems[1] >= 12)
                return json_encode(array("result" => 0, "param" => $version));
        }
        return json_encode(array("result" => 1, "param" => $version));
    }

    public static function get_r_version() {
        require '../Ini.php';
        $ini = new Ini();
        $output = array();
        $return = 0;
        exec('"' . Ini::$path_r_script . '" -e version', $output, $return);
        $version = str_replace(" ", "", str_replace("major", "", $output[6])) . "." . str_replace(" ", "", str_replace("minor", "", $output[7]));
        return $version;
    }

    public static function mysql_connection_check() {
        include'../SETTINGS.php';
        if (@mysql_connect($db_host . ":" . $db_port, $db_user, $db_password))
            return json_encode(array("result" => 0, "param" => "Host: <b>$db_host</b>, Port: <b>$db_port</b>, Login: <b>$db_user</b>"));
        else
            return json_encode(array("result" => 1, "param" => "Host: <b>$db_host</b>, Port: <b>$db_port</b>, Login: <b>$db_user</b>"));;
    }

    public static function mysql_select_db_check() {
        include'../SETTINGS.php';
        Setup::mysql_connection_check();
        if (@mysql_select_db($db_name))
            return json_encode(array("result" => 0, "param" => $db_name));
        else
            return json_encode(array("result" => 1, "param" => $db_name));
    }

    public static function r_package_check($package) {
        $array = array();
        $return = 0;
        exec('"' . Ini::$path_r_script . '" -e "library(' . $package . ')"', $array, $return);
        return ($return == 0);
    }

    public static function catR_r_package_check() {
        require '../Ini.php';
        $ini = new Ini();
        $array = array();
        $return = 0;
        exec('"' . Ini::$path_r_script . '" -e "library(catR)"', $array, $return);
        return json_encode(array("result" => $return, "param" => "catR"));
    }

    public static function RMySQL_r_package_check() {
        require '../Ini.php';
        $ini = new Ini();
        $array = array();
        $return = 0;
        exec('"' . Ini::$path_r_script . '" -e "library(RMySQL)"', $array, $return);
        return json_encode(array("result" => $return, "param" => "RMySQL"));
    }

    public static function session_r_package_check() {
        require '../Ini.php';
        $ini = new Ini();
        $array = array();
        $return = 0;
        exec('"' . Ini::$path_r_script . '" -e "library(session)"', $array, $return);
        return json_encode(array("result" => $return, "param" => "session"));
    }

    public static function create_db_structure($simulate = false) {
        foreach (Ini::get_system_tables() as $table) {
            $sql = sprintf("SHOW TABLES LIKE '%s'", $table);
            $z = mysql_query($sql);
            if (mysql_num_rows($z) == 0) {
                if ($simulate) {
                    return true;
                } else {
                    if (!$table::create_db())
                        return json_encode(array("result" => 1, "param" => $table));
                }
            }
        }
        if ($simulate) {
            return false;
        }
        return json_encode(array("result" => 0));
    }

    public static function reset_db() {
        CustomSection::create_db(true);
        CustomSectionVariable::create_db(true);
        DS_Module::create_db(true);
        DS_Right::create_db(true);
        DS_Sharing::create_db(true);
        DS_TableColumnType::create_db(true);
        DS_TestSectionType::create_db(true);
        DS_UserInstitutionType::create_db(true);
        Setting::create_db(true);
        Table::create_db(true);
        TableColumn::create_db(true);
        Template::create_db(true);
        Test::create_db(true);
        TestSection::create_db(true);
        TestSectionValue::create_db(true);
        TestSession::create_db(true);
        TestSessionReturn::create_db(true);
        TestTemplate::create_db(true);
        TestVariable::create_db(true);
        User::create_db(true);
        UserGroup::create_db(true);
        UserType::create_db(true);
        UserTypeRight::create_db(true);
    }

    public static function update_db($simulate = false, $only_recalculate_hash = false, $only_repopulate_TestTemplate = false, $only_validate_column_names = false, $only_create_db = false) {
        require '../Ini.php';
        $ini = new Ini();

        if ($only_create_db) {
            return self::create_db_structure();
        }

        if ($only_recalculate_hash) {
            OModule::calculate_all_xml_hashes();
            return json_encode(array("result" => 0));
        }

        if ($only_repopulate_TestTemplate) {
            TestTemplate::repopulate_table();
            return json_encode(array("result" => 0));
        }

        if ($only_validate_column_names) {
            $result = TableColumn::validate_columns_name();
            return json_encode(array("result" => $result ? 0 : 1));
        }

        $versions_to_update = array();

        $previous_version = Setting::get_setting("version");

        $recalculate_hash = false;
        $repopulate_TestTemplate = false;
        $validate_column_names = false;

        if (Ini::does_patch_apply("3.3.0", $previous_version)) {
            if ($simulate) {
                array_push($versions_to_update, "3.3.0");
            } else {

                ///COMPATIBILITY FIX FOR V3.0.0 START
                $sql = "SHOW COLUMNS FROM `User` WHERE `Field`='last_activity'";
                $z = mysql_query($sql);
                if (mysql_num_rows($z) > 0) {
                    $sql = "ALTER TABLE `User` CHANGE `last_activity` `last_login` timestamp NOT NULL default '0000-00-00 00:00:00';";
                    if (!mysql_query($sql))
                        return json_encode(array("result" => 1, "param" => $sql));
                }

                //DS_TableColumnType - numeric split to integer and float
                $id_4_found = false;
                $sql = "SELECT * FROM `DS_TableColumnType`";
                $z = mysql_query($sql);
                while ($r = mysql_fetch_array($z)) {
                    switch ($r['id']) {
                        case 2: {
                                if ($r['name'] != "integer") {
                                    $sql2 = "UPDATE `DS_TableColumnType` SET `name`='integer', value='integer' WHERE `id`=2";
                                    if (!mysql_query($sql2))
                                        return json_encode(array("result" => 1, "param" => $sql2));
                                }
                                break;
                            }
                        case 3: {
                                if ($r['name'] != "float") {
                                    $sql2 = "UPDATE `DS_TableColumnType` SET `name`='float', value='float' WHERE `id`=3";
                                    if (!mysql_query($sql2))
                                        return json_encode(array("result" => 1, "param" => $sql2));
                                }
                                break;
                            }
                        case 4: {
                                $id_4_found = true;
                                if ($r['name'] != "HTML") {
                                    $sql2 = "UPDATE `DS_TableColumnType` SET `name`='HTML', value='HTML' WHERE `id`=4";
                                    if (!mysql_query($sql2))
                                        return json_encode(array("result" => 1, "param" => $sql2));
                                }
                                break;
                            }
                    }
                }
                if (!$id_4_found) {
                    $sql2 = "INSERT INTO `DS_TableColumnType` SET `id`=4, `name`='HTML', value='HTML', `position`=4";
                    if (!mysql_query($sql2))
                        return json_encode(array("result" => 1, "param" => $sql2));
                }

                //TableColumn - change numeric and float MySQL type
                $sql = sprintf("SELECT * FROM `TableColumn`");
                $z = mysql_query($sql);
                while ($r = mysql_fetch_array($z)) {
                    $table = Table::from_mysql_id($r['Table_id']);
                    if ($table == null)
                        continue;
                    if (!$table->has_table()) {
                        $table->mysql_delete();
                        continue;
                    }
                    $table_name = $table->get_table_name();
                    $type = "TEXT NOT NULL";
                    switch ($r['TableColumnType_id']) {
                        case 2: {
                                $type = "BIGINT NOT NULL";
                                break;
                            }
                        case 3: {
                                $type = "DOUBLE NOT NULL";
                                break;
                            }
                    }
                    $old_name = $r['name'];
                    $new_name = Table::format_column_name($old_name);

                    if ($r['TableColumnType_id'] == 3) {
                        $sql2 = sprintf("UPDATE `TableColumn` SET `TableColumnType_id`='%d' WHERE `id`='%d'", 4, $r['id']);
                        if (!mysql_query($sql2)) {
                            return json_encode(array("result" => 1, "param" => $sql2));
                        }
                    }

                    if ($old_name != $new_name) {
                        $sql2 = sprintf("ALTER TABLE `%s` CHANGE `%s` `%s` %s;", $table_name, $old_name, $new_name, $type);
                        $i = 1;
                        while (!mysql_query($sql2)) {
                            $new_name = "col" . $i;
                            $sql2 = sprintf("ALTER TABLE `%s` CHANGE `%s` `%s` %s;", $table_name, $old_name, $new_name, $type);
                            $i++;
                        }

                        $sql2 = sprintf("UPDATE `TableColumn` SET `name`='%s' WHERE `id`='%d'", $new_name, $r['id']);
                        if (!mysql_query($sql2)) {
                            return json_encode(array("result" => 1, "param" => $sql2));
                        }
                    }
                }
                Setting::set_setting("version", "3.3.0");
                return json_encode(array("result" => 0, "param" => "3.3.0"));
            }
        }

        if (Ini::does_patch_apply("3.4.0", $previous_version)) {
            if ($simulate) {
                array_push($versions_to_update, "3.4.0");
            } else {

                //Test - add session_count field
                $sql = "SHOW COLUMNS FROM `Test` WHERE `Field`='session_count'";
                $z = mysql_query($sql);
                if (mysql_num_rows($z) == 0) {
                    $sql = "ALTER TABLE `Test` ADD `session_count` bigint(20) NOT NULL;";
                    if (!mysql_query($sql))
                        return json_encode(array("result" => 1, "param" => $sql));
                }

                //TestSectionValue - indexes changes
                $sql = sprintf("SELECT `TestSection`.`id`, `TestSection`.`TestSectionType_id` FROM `TestSection` WHERE `TestSectionType_id` IN (%d,%d,%d)", DS_TestSectionType::LOAD_HTML_TEMPLATE, DS_TestSectionType::SET_VARIABLE, DS_TestSectionType::CUSTOM);
                $z = mysql_query($sql);
                while ($r = mysql_fetch_array($z)) {
                    set_time_limit(0);
                    switch ($r[1]) {
                        case DS_TestSectionType::LOAD_HTML_TEMPLATE: {
                                $params_count = 0;
                                $returns_count = 0;
                                $sql2 = sprintf("SELECT `index`,`value` FROM `%s` WHERE `TestSection_id`=%d AND (`index`=1 OR `index`=2) ", TestSectionValue::get_mysql_table(), $r[0]);
                                $z2 = mysql_query($sql2);
                                while ($r2 = mysql_fetch_array($z2)) {
                                    if ($r2['index'] == 1)
                                        $params_count = $r2['value'];
                                    if ($r2['index'] == 2)
                                        $returns_count = $r2['value'];
                                }

                                $delete_index = 3 + $params_count + 1;

                                for ($i = 0; $i < $returns_count; $i++) {
                                    $sql2 = sprintf("DELETE FROM `%s` WHERE `TestSection_id`=%d AND `index` IN (%d,%d)", TestSectionValue::get_mysql_table(), $r[0], $delete_index, $delete_index + 1);
                                    if (!mysql_query($sql2))
                                        return json_encode(array("result" => 1, "param" => $sql2));

                                    $sql2 = sprintf("UPDATE `%s` SET `index`=`index`-2 WHERE `TestSection_id`=%d AND `index`>%d", TestSectionValue::get_mysql_table(), $r[0], $delete_index);
                                    if (!mysql_query($sql2))
                                        return json_encode(array("result" => 1, "param" => $sql2));

                                    $delete_index++;
                                }
                                break;
                            }
                        case DS_TestSectionType::SET_VARIABLE: {
                                $sql2 = sprintf("DELETE FROM `%s` WHERE `TestSection_id`=%d AND `index` IN (4,5)", TestSectionValue::get_mysql_table(), $r[0]);
                                if (!mysql_query($sql2))
                                    return json_encode(array("result" => 1, "param" => $sql2));

                                $sql2 = sprintf("UPDATE `%s` SET `index`=`index`-2 WHERE `TestSection_id`=%d AND `index`>%d", TestSectionValue::get_mysql_table(), $r[0], 5);
                                if (!mysql_query($sql2))
                                    return json_encode(array("result" => 1, "param" => $sql2));
                                break;
                            }
                        case DS_TestSectionType::CUSTOM: {
                                $params_count = 0;
                                $returns_count = 0;
                                $csid = 0;

                                $sql2 = sprintf("SELECT `value` FROM `%s` WHERE `TestSection_id`=%d AND `index`=0 ", TestSectionValue::get_mysql_table(), $r[0]);
                                $z2 = mysql_query($sql2);
                                $r2 = mysql_fetch_array($z2);
                                $csid = $r2['value'];

                                $sql2 = sprintf("SELECT * FROM `%s` WHERE `CustomSection_id`=%d AND `type`=0", CustomSectionVariable::get_mysql_table(), $csid);
                                $params_count = mysql_num_rows(mysql_query($sql2));

                                $sql2 = sprintf("SELECT * FROM `%s` WHERE `CustomSection_id`=%d AND `type`=1", CustomSectionVariable::get_mysql_table(), $csid);
                                $returns_count = mysql_num_rows(mysql_query($sql2));

                                $delete_index = 1 + $params_count + 1;

                                for ($i = 0; $i < $returns_count; $i++) {
                                    $sql2 = sprintf("DELETE FROM `%s` WHERE `TestSection_id`=%d AND `index` IN (%d,%d)", TestSectionValue::get_mysql_table(), $r[0], $delete_index, $delete_index + 1);
                                    if (!mysql_query($sql2))
                                        return json_encode(array("result" => 1, "param" => $sql2));

                                    $sql2 = sprintf("UPDATE `%s` SET `index`=`index`-2 WHERE `TestSection_id`=%d AND `index`>%d", TestSectionValue::get_mysql_table(), $r[0], $delete_index);
                                    if (!mysql_query($sql2))
                                        return json_encode(array("result" => 1, "param" => $sql2));

                                    $delete_index++;
                                }
                                break;
                            }
                    }
                }

                //TestSession - added new fields
                $sql = "SHOW COLUMNS FROM `TestSession` WHERE `Field`='status'";
                $z = mysql_query($sql);
                if (mysql_num_rows($z) == 0) {
                    $sql = "ALTER TABLE `TestSession` ADD `status` tinyint(4) NOT NULL default '0';";
                    if (!mysql_query($sql))
                        return json_encode(array("result" => 1, "param" => $sql));
                }

                $sql = "SHOW COLUMNS FROM `TestSession` WHERE `Field`='time_limit'";
                $z = mysql_query($sql);
                if (mysql_num_rows($z) == 0) {
                    $sql = "ALTER TABLE `TestSession` ADD `time_limit` int(11) NOT NULL default '0';";
                    if (!mysql_query($sql))
                        return json_encode(array("result" => 1, "param" => $sql));
                }

                $sql = "SHOW COLUMNS FROM `TestSession` WHERE `Field`='HTML'";
                $z = mysql_query($sql);
                if (mysql_num_rows($z) == 0) {
                    $sql = "ALTER TABLE `TestSession` ADD `HTML` text NOT NULL default '';";
                    if (!mysql_query($sql))
                        return json_encode(array("result" => 1, "param" => $sql));
                }

                $sql = "SHOW COLUMNS FROM `TestSession` WHERE `Field`='Template_id'";
                $z = mysql_query($sql);
                if (mysql_num_rows($z) == 0) {
                    $sql = "ALTER TABLE `TestSession` ADD `Template_id` bigint(20) NOT NULL default '0';";
                    if (!mysql_query($sql))
                        return json_encode(array("result" => 1, "param" => $sql));
                }

                $sql = "SHOW COLUMNS FROM `TestSession` WHERE `Field`='time_tamper_prevention'";
                $z = mysql_query($sql);
                if (mysql_num_rows($z) == 0) {
                    $sql = "ALTER TABLE  `TestSession` ADD  `time_tamper_prevention` INT NOT NULL;";
                    if (!mysql_query($sql))
                        return json_encode(array("result" => 1, "param" => $sql));
                }

                $sql = "SHOW COLUMNS FROM `TestSession` WHERE `Field`='hash'";
                $z = mysql_query($sql);
                if (mysql_num_rows($z) == 0) {
                    $sql = "ALTER TABLE `TestSession` ADD `hash` text NOT NULL default '';";
                    if (!mysql_query($sql))
                        return json_encode(array("result" => 1, "param" => $sql));
                }

                $sql = "SHOW COLUMNS FROM `TestSession` WHERE `Field`='r_type'";
                $z = mysql_query($sql);
                if (mysql_num_rows($z) == 0) {
                    $sql = "ALTER TABLE  `TestSession` ADD  `r_type` TINYINT( 1 ) NOT NULL;";
                    if (!mysql_query($sql))
                        return json_encode(array("result" => 1, "param" => $sql));
                }

                Setting::set_setting("version", "3.4.0");
                return json_encode(array("result" => 0, "param" => "3.4.0"));
            }
        }

        if (Ini::does_patch_apply("3.4.1", $previous_version)) {
            if ($simulate) {
                array_push($versions_to_update, "3.4.1");
            } else {

                //TestSession - added Template_TestSection_id field
                $sql = "SHOW COLUMNS FROM `TestSession` WHERE `Field`='Template_TestSection_id'";
                $z = mysql_query($sql);
                if (mysql_num_rows($z) == 0) {
                    $sql = "ALTER TABLE `TestSession` ADD `Template_TestSection_id` bigint(20) NOT NULL default '0';";
                    if (!mysql_query($sql))
                        return json_encode(array("result" => 1, "param" => $sql));
                }

                Setting::set_setting("version", "3.4.1");
                return json_encode(array("result" => 0, "param" => "3.4.1"));
            }
        }

        if (Ini::does_patch_apply("3.4.3", $previous_version)) {
            if ($simulate) {
                array_push($versions_to_update, "3.4.3");
            } else {

                //TestSection - added end field
                $sql = "SHOW COLUMNS FROM `TestSection` WHERE `Field`='end'";
                $z = mysql_query($sql);
                if (mysql_num_rows($z) == 0) {
                    $sql = "ALTER TABLE `TestSection` ADD `end` tinyint(1) NOT NULL default '0';";
                    if (!mysql_query($sql))
                        return json_encode(array("result" => 1, "param" => $sql));
                }

                //TestSession - added new fields
                $sql = "SHOW COLUMNS FROM `TestSession` WHERE `Field`='debug'";
                $z = mysql_query($sql);
                if (mysql_num_rows($z) == 0) {
                    $sql = "ALTER TABLE `TestSession` ADD `debug` tinyint(1) NOT NULL default '0';";
                    if (!mysql_query($sql))
                        return json_encode(array("result" => 1, "param" => $sql));
                }

                $sql = "SHOW COLUMNS FROM `TestSession` WHERE `Field`='release'";
                $z = mysql_query($sql);
                if (mysql_num_rows($z) == 0) {
                    $sql = "ALTER TABLE `TestSession` ADD `release` tinyint(1) NOT NULL default '0';";
                    if (!mysql_query($sql))
                        return json_encode(array("result" => 1, "param" => $sql));
                }

                $sql = "SHOW COLUMNS FROM `TestSession` WHERE `Field`='serialized'";
                $z = mysql_query($sql);
                if (mysql_num_rows($z) == 0) {
                    $sql = "ALTER TABLE `TestSession` ADD `serialized` tinyint(1) NOT NULL default '0';";
                    if (!mysql_query($sql))
                        return json_encode(array("result" => 1, "param" => $sql));
                }

                Setting::set_setting("version", "3.4.3");
                return json_encode(array("result" => 0, "param" => "3.4.3"));
            }
        }

        if (Ini::does_patch_apply("3.5.0", $previous_version)) {
            if ($simulate) {
                array_push($versions_to_update, "3.5.0");
            } else {

                //Table - added description field
                $sql = "SHOW COLUMNS FROM `Table` WHERE `Field`='description'";
                $z = mysql_query($sql);
                if (mysql_num_rows($z) == 0) {
                    $sql = "ALTER TABLE `Table` ADD `description` text NOT NULL;";
                    if (!mysql_query($sql))
                        return json_encode(array("result" => 1, "param" => $sql));
                }

                //TableColumn - fix Timestamp fields names
                $sql = "SHOW COLUMNS FROM `TableColumn` WHERE `Field`='created' OR `Field`='udpated'";
                $z = mysql_query($sql);
                if (mysql_num_rows($z) == 2) {
                    $sql = "ALTER TABLE `TableColumn` CHANGE `created` `updated_temp` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;";
                    if (!mysql_query($sql)) {
                        return json_encode(array("result" => 1, "param" => $sql));
                    }
                    $sql = "ALTER TABLE `TableColumn` CHANGE `udpated` `created` TIMESTAMP NOT NULL DEFAULT  '0000-00-00 00:00:00';";
                    if (!mysql_query($sql)) {
                        return json_encode(array("result" => 1, "param" => $sql));
                    }
                    $sql = "ALTER TABLE `TableColumn` CHANGE `updated_temp` `updated` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;";
                    if (!mysql_query($sql)) {
                        return json_encode(array("result" => 1, "param" => $sql));
                    }
                }

                //Template - added description field
                $sql = "SHOW COLUMNS FROM `Template` WHERE `Field`='description'";
                $z = mysql_query($sql);
                if (mysql_num_rows($z) == 0) {
                    $sql = "ALTER TABLE `Template` ADD `description` text NOT NULL;";
                    if (!mysql_query($sql))
                        return json_encode(array("result" => 1, "param" => $sql));
                }

                //Test - added description field
                $sql = "SHOW COLUMNS FROM `Test` WHERE `Field`='description'";
                $z = mysql_query($sql);
                if (mysql_num_rows($z) == 0) {
                    $sql = "ALTER TABLE `Test` ADD `description` text NOT NULL;";
                    if (!mysql_query($sql))
                        return json_encode(array("result" => 1, "param" => $sql));
                }

                //TestSection - fix Timestamp fields
                $sql = "SHOW COLUMNS FROM `TestSection` WHERE `Field`='created' OR `Field`='updated'";
                $z = mysql_query($sql);
                if (mysql_num_rows($z) == 2) {
                    $sql = "ALTER TABLE `TestSection` CHANGE `created` `updated_temp` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;";
                    if (!mysql_query($sql)) {
                        return json_encode(array("result" => 1, "param" => $sql));
                    }
                    $sql = "ALTER TABLE `TestSection` CHANGE `updated` `created` TIMESTAMP NOT NULL DEFAULT  '0000-00-00 00:00:00';";
                    if (!mysql_query($sql)) {
                        return json_encode(array("result" => 1, "param" => $sql));
                    }
                    $sql = "ALTER TABLE `TestSection` CHANGE `updated_temp` `updated` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;";
                    if (!mysql_query($sql)) {
                        return json_encode(array("result" => 1, "param" => $sql));
                    }
                }

                //TestSectionValue - fix Timestamp fields
                $sql = "SHOW COLUMNS FROM `TestSectionValue` WHERE `Field`='created' OR `Field`='updated'";
                $z = mysql_query($sql);
                if (mysql_num_rows($z) == 2) {
                    $sql = "ALTER TABLE `TestSectionValue` CHANGE `created` `updated_temp` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;";
                    if (!mysql_query($sql)) {
                        return json_encode(array("result" => 1, "param" => $sql));
                    }
                    $sql = "ALTER TABLE `TestSectionValue` CHANGE `updated` `created` TIMESTAMP NOT NULL DEFAULT  '0000-00-00 00:00:00';";
                    if (!mysql_query($sql)) {
                        return json_encode(array("result" => 1, "param" => $sql));
                    }
                    $sql = "ALTER TABLE `TestSectionValue` CHANGE `updated_temp` `updated` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;";
                    if (!mysql_query($sql)) {
                        return json_encode(array("result" => 1, "param" => $sql));
                    }
                }

                Setting::set_setting("version", "3.5.0");
                return json_encode(array("result" => 0, "param" => "3.5.0"));
            }
        }

        if (Ini::does_patch_apply("3.5.2", $previous_version)) {
            if ($simulate) {
                array_push($versions_to_update, "3.5.2");
            } else {

                //TestSection - fixed name of r_type field
                $sql = "SHOW COLUMNS FROM `TestSession` WHERE `Field`='r_typ'";
                $z = mysql_query($sql);
                if (mysql_num_rows($z) > 0) {
                    $sql = "ALTER TABLE `TestSession` CHANGE `r_typ` `r_type` tinyint(1) NOT NULL default '0';";
                    if (!mysql_query($sql))
                        return json_encode(array("result" => 1, "param" => $sql));
                }

                Setting::set_setting("version", "3.5.2");
                return json_encode(array("result" => 0, "param" => "3.5.2"));
            }
        }

        if (Ini::does_patch_apply("3.6.0", $previous_version)) {
            if ($simulate) {
                array_push($versions_to_update, "3.6.0");
            } else {

                //DS_TestSectionType - added loop and test inclusion section
                $sql = "INSERT INTO `DS_TestSectionType` SET `id`=10, `name`='loop', `value`='10', `position`=10;";
                if (!mysql_query($sql))
                    return json_encode(array("result" => 1, "msg" => $sql));
                $sql = "INSERT INTO `DS_TestSectionType` SET `id`=11, `name`='test inclusion', `value`='11', `position`=11;";
                if (!mysql_query($sql))
                    return json_encode(array("result" => 1, "param" => $sql));

                //Template - added head field
                $sql = "SHOW COLUMNS FROM `Template` WHERE `Field`='head'";
                $z = mysql_query($sql);
                if (mysql_num_rows($z) == 0) {
                    $sql = "ALTER TABLE `Template` ADD `head` text NOT NULL;";
                    if (!mysql_query($sql))
                        return json_encode(array("result" => 1, "param" => $sql));
                }

                Setting::set_setting("version", "3.6.0");
                return json_encode(array("result" => 0, "param" => "3.6.0"));
            }
        }

        if (Ini::does_patch_apply("3.6.2", $previous_version)) {
            if ($simulate) {
                array_push($versions_to_update, "3.6.2");
            } else {

                //CustomSection - added xml_hash
                $sql = "SHOW COLUMNS FROM `CustomSection` WHERE `Field`='xml_hash'";
                $z = mysql_query($sql);
                if (mysql_num_rows($z) == 0) {
                    $sql = "ALTER TABLE `CustomSection` ADD `xml_hash` text NOT NULL;";
                    if (!mysql_query($sql))
                        return json_encode(array("result" => 1, "param" => $sql));
                }

                //Table - added xml_hash
                $sql = "SHOW COLUMNS FROM `Table` WHERE `Field`='xml_hash'";
                $z = mysql_query($sql);
                if (mysql_num_rows($z) == 0) {
                    $sql = "ALTER TABLE `Table` ADD `xml_hash` text NOT NULL;";
                    if (!mysql_query($sql))
                        return json_encode(array("result" => 1, "param" => $sql));
                }

                //Template - added xml_hash
                $sql = "SHOW COLUMNS FROM `Template` WHERE `Field`='xml_hash'";
                $z = mysql_query($sql);
                if (mysql_num_rows($z) == 0) {
                    $sql = "ALTER TABLE `Template` ADD `xml_hash` text NOT NULL;";
                    if (!mysql_query($sql))
                        return json_encode(array("result" => 1, "param" => $sql));
                }

                //Test - added xml_hash
                $sql = "SHOW COLUMNS FROM `Test` WHERE `Field`='xml_hash'";
                $z = mysql_query($sql);
                if (mysql_num_rows($z) == 0) {
                    $sql = "ALTER TABLE `Test` ADD `xml_hash` text NOT NULL;";
                    if (!mysql_query($sql))
                        return json_encode(array("result" => 1, "param" => $sql));
                }

                Setting::set_setting("version", "3.6.2");
                return json_encode(array("result" => 0, "param" => "3.6.2"));
            }
        }

        if (Ini::does_patch_apply("3.6.7", $previous_version)) {
            if ($simulate) {
                array_push($versions_to_update, "3.6.7");
            } else {

                //User - added new fields
                $sql = "SHOW COLUMNS FROM `User` WHERE `Field`='UserInstitutionType_id'";
                $z = mysql_query($sql);
                if (mysql_num_rows($z) == 0) {
                    $sql = "ALTER TABLE `User` ADD `UserInstitutionType_id` int(11) NOT NULL;";
                    if (!mysql_query($sql))
                        return json_encode(array("result" => 1, "param" => $sql));
                }

                $sql = "SHOW COLUMNS FROM `User` WHERE `Field`='institution_name'";
                $z = mysql_query($sql);
                if (mysql_num_rows($z) == 0) {
                    $sql = "ALTER TABLE `User` ADD `institution_name` text NOT NULL;";
                    if (!mysql_query($sql))
                        return json_encode(array("result" => 1, "param" => $sql));
                }

                //UserType - changed Sharing_id of standard user type to public
                $sql = "UPDATE `UserType` SET `Sharing_id`=3 WHERE `id`=4;";
                if (!mysql_query($sql))
                    return json_encode(array("result" => 1, "param" => $sql));

                Setting::set_setting("version", "3.6.7");
                return json_encode(array("result" => 0, "param" => "3.6.7"));
            }
        }

        if (Ini::does_patch_apply("3.6.8", $previous_version)) {
            if ($simulate) {
                array_push($versions_to_update, "3.6.8");
            } else {

                //TestSectionValue - indexes changes
                $sections = TestSection::from_property(array("TestSectionType_id" => DS_TestSectionType::LOAD_HTML_TEMPLATE));
                foreach ($sections as $section) {
                    $sql = sprintf("DELETE FROM `%s` WHERE `TestSection_id`=%d AND `index`>=3", TestSectionValue::get_mysql_table(), $section->id);
                    $vals = $section->get_values();
                    $template = Template::from_mysql_id($vals[0]);
                    if ($template == null)
                        continue;
                    $inserts = $template->get_inserts();
                    $returns = $template->get_outputs();
                    $j = 3;
                    $i = 3;
                    foreach ($inserts as $ins) {
                        $tsv = new TestSectionValue();
                        $tsv->TestSection_id = $section->id;
                        $tsv->index = $j;
                        $tsv->value = $ins;
                        $tsv->mysql_save();

                        $tsv = new TestSectionValue();
                        $tsv->TestSection_id = $section->id;
                        $tsv->index = $j + 1;
                        $tsv->value = (isset($vals[$i]) ? $vals[$i] : $ins);
                        $tsv->mysql_save();

                        $j = $j + 2;
                        $i++;
                    }

                    foreach ($returns as $ret) {
                        $tsv = new TestSectionValue();
                        $tsv->TestSection_id = $section->id;
                        $tsv->index = $j;
                        $tsv->value = $ret['name'];
                        $tsv->mysql_save();

                        $tsv = new TestSectionValue();
                        $tsv->TestSection_id = $section->id;
                        $tsv->index = $j + 1;
                        $tsv->value = (isset($vals[$i]) ? $vals[$i] : $ret['name']);
                        $tsv->mysql_save();

                        $j = $j + 2;
                        $i++;
                    }
                }
                Setting::set_setting("version", "3.6.8");
                return json_encode(array("result" => 0, "param" => "3.6.8"));
            }

            $validate_column_names = true;
            $recalculate_hash = true;
            $repopulate_TestTemplate = true;
        }

        if (Ini::does_patch_apply("3.6.10", $previous_version)) {
            if ($simulate) {
                array_push($versions_to_update, "3.6.10");
            } else {

                //User - change of password field
                $sql = "SHOW COLUMNS FROM `User` WHERE `Field`='md5_password'";
                $z = mysql_query($sql);
                if (mysql_num_rows($z) > 0) {
                    $sql = "ALTER TABLE `User` CHANGE `md5_password` `password` text NOT NULL default '';";
                    if (!mysql_query($sql))
                        return json_encode(array("result" => 1, "param" => $sql));
                }

                Setting::set_setting("version", "3.6.10");
                return json_encode(array("result" => 0, "param" => "3.6.10"));
            }
        }

        if (Ini::does_patch_apply("3.6.11", $previous_version)) {
            if ($simulate) {
                array_push($versions_to_update, "3.6.11");
            } else {

                //DS_TestSectionType - add new section type
                $sql = "
                INSERT INTO `DS_TestSectionType` (`id`, `name`, `value`, `position`) VALUES
                (12, 'lower level R code', '12', 12);
                ";
                if (!mysql_query($sql))
                    return json_encode(array("result" => 1, "param" => $sql));

                Setting::set_setting("version", "3.6.11");
                return json_encode(array("result" => 0, "param" => "3.6.11"));
            }
        }

        if ($simulate)
            return json_encode(array("versions" => $versions_to_update, "validate_column_names" => $validate_column_names, "repopulate_TestTemplate" => $repopulate_TestTemplate, "recalculate_hash" => $recalculate_hash, "create_db" => self::create_db_structure(true)));
        return json_encode(array("result" => 2));
    }

}

echo Setup::$_POST['check']();
?>