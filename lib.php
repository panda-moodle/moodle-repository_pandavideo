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
 * Lib class
 *
 * @package   repository_pandavideo
 * @copyright 2025 Panda Video {@link https://pandavideo.com.br}
 * @author    2025 Eduardo Kraus {@link https://www.eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_pandavideo\panda\repository as pandarepository;

defined('MOODLE_INTERNAL') || die();

require_once("{$CFG->dirroot}/repository/lib.php");

/**
 * Repository pandavideo class
 *
 * @package   repository_pandavideo
 * @copyright 2025 Eduardo Kraus  {@link http://pandavideo.com.br}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class repository_pandavideo extends repository {

    /**
     * Get file listing.
     *
     * @param string $encodedpath
     * @param string $page
     *
     * @return array
     */
    public function get_listing($encodedpath = "", $page = "") {
        return $this->search("", 0);
    }

    /**
     * Return search results.
     *
     * @param string $searchtext
     * @param int $page
     *
     * @return array|mixed
     */
    public function search($searchtext, $page = 0) {
        global $SESSION;
        $sessionkeyword = "pandavideo_" . $this->id;

        if ($page && !$searchtext && isset($SESSION->{$sessionkeyword})) {
            $searchtext = $SESSION->{$sessionkeyword};
        }

        $SESSION->{$sessionkeyword} = $searchtext;

        $ret = [
            "dynload" => true,
            "nologin" => true,
            "nosearch" => false,
            "norefresh" => false,
            "manage" => "https://dashboard.pandavideo.com.br/",
        ];

        $videos = $this->search_videos($searchtext, $page);
        $ret["list"] = $videos["list"];
        $ret["path"] = $videos["path"];

        return $ret;
    }


    /**
     * Private method to search remote videos
     *
     * @param string $searchtext
     * @param int $page
     * @return array
     * @throws Exception
     */
    private function search_videos($searchtext, $page, $pasta = -1) {
        global $OUTPUT;

        $acceptedtypes  = optional_param_array('accepted_types', '*', PARAM_TEXT);
        $mimetype = "video/mp4";
        $extension = "";
        if ($acceptedtypes[0] == ".panda") {
            $mimetype = "video/panda";
            $extension = ".panda";
        }

        $list = [];
        $folderid = optional_param("p", false, PARAM_TEXT);
        $folders = pandarepository::get_folders();
        $videos = pandarepository::get_videos($page, 100, $searchtext);

        foreach ($folders->folders as $folder) {
            if ($folder->parent_folder_id == $folderid) {
                $list[] = [
                    "title" => $folder->name,
                    "path" => $folder->id,
                    "thumbnail" => $OUTPUT->image_url("f/folder")->out(false),
                    "icon" => $OUTPUT->image_url("f/folder")->out(false),
                    "children" => [],
                ];
            }
        }
        foreach ($videos->videos as $video) {
            if ($video->folder_id == $folderid) {
                $list[] = [
                    "shorttitle" => $video->title,
                    "title" => "{$video->title}{$extension}",
                    "mimetype" => $mimetype,
                    "thumbnail_title" => "{$video->title}{$extension}",
                    "thumbnail" => $video->thumbnail,
                    "icon" => $video->thumbnail,
                    "datecreated" => strtotime($video->created_at),
                    "datemodified" => strtotime($video->updated_at),
                    "size" => $video->storage_size,
                    "dimensions" => "{$video->width}x{$video->height} - " . implode(", ", $video->playback),
                    "source" => "https://dashboard.pandavideo.com.br/#/videos/{$video->id}",
                    "license" => "Panda Video",
                    "author" => "Panda Video",
                ];
            }
        }

        return [
            "list" => $list,
            "pages" => $videos->pages,
            "path" => $this->get_folder_path($folders->folders, $folderid),
        ];
    }

    /**
     * get_folder_path
     *
     * @param array $folders
     * @param string $folderid
     * @return array
     */
    private function get_folder_path($folders, $folderid) {
        global $OUTPUT;

        $foldermap = [];
        foreach ($folders as $folder) {
            $foldermap[$folder->id] = $folder;
        }

        $path = [];
        while (isset($foldermap[$folderid])) {
            $folder = $foldermap[$folderid];

            $newpath = [
                "path" => $folder->id,
                "name" => $folder->name,
                "icon" => $OUTPUT->image_url("f/folder")->out(false),
            ];
            array_unshift($path, $newpath);
            $folderid = $folder->parent_folder_id;
        }

        $newpath = [
            "path" => "",
            "name" => get_string("root_folder", "repository_pandavideo"),
            "icon" => $OUTPUT->image_url("i/home")->out(false),
        ];
        array_unshift($path, $newpath);

        return $path;
    }

    /**
     * Youtube plugin doesn't support global search
     */
    public function global_search() {
        return false;
    }

    /**
     * get type option name function
     *
     * This function is for module settings.
     *
     * @return array
     */
    public static function get_type_option_names() {
        return array_merge(parent::get_type_option_names(), ["key"]);
    }

    /**
     * file types supported by pandavideo plugin
     *
     * @return array
     */
    public function supported_filetypes() {
        $mimetypes = get_mimetypes_array();
        if (!isset($mimetypes["panda"])) {
            core_filetypes::add_type("panda", "video/panda", "unknown");
        }
        return [
            "video",       // Videos.
            "video/panda", // Panda Video.
        ];
    }

    /**
     * pandavideo plugin only return external links
     *
     * @return int
     */
    public function supported_returntypes() {
        return FILE_EXTERNAL;
    }

    /**
     * Is this repository accessing private data?
     *
     * @return bool
     */
    public function contains_private_data() {
        return false;
    }

    /**
     * is_enable
     *
     * @return bool
     */
    public function is_enable() {
        $config = get_config("pandavideo");
        return isset($config->panda_token[20]);
    }
}
