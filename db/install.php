<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Installation file for the pandavideo repository.
 *
 * @package   repository_pandavideo
 * @copyright 2025 Panda Video {@link https://pandavideo.com.br}
 * @author    2025 Eduardo Kraus {@link https://www.eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Repository pandavideo install.
 *
 * @return bool Return true.
 * @throws dml_exception
 */
function xmldb_repository_pandavideo_install() {
    global $DB;

    $repository = (object)[
        "type" => "pandavideo",
        "visible" => 1,
        "sortorder" => 2,
    ];
    $repository->id = $DB->insert_record("repository", $repository);

    $repositoryinstances = (object)[
        "name" => get_string("pluginname", "repository_pandavideo"),
        "typeid" => $repository->id,
        "userid" => 0,
        "contextid" => 1,
        "username" => null,
        "password" => null,
        "timecreated" => time(),
        "timemodified" => time(),
        "readonly" => 0,
    ];
    $repositoryinstances->id = $DB->insert_record("repository_instances", $repositoryinstances);

    $pandatoken = get_config("pandavideo", "panda_token");
    if (isset($pandatoken[20])) {
        set_config("panda_token", $pandatoken, "repository_pandavideo");
    } else {
        $pandatoken = get_config("supervideo", "panda");
        if (isset($pandatoken[20])) {
            set_config("panda_token", $pandatoken, "repository_pandavideo");
        }
    }

    return true;
}
