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
 * Version package.
 *
 * @package   repository_pandavideo
 * @copyright 2025 Panda Video {@link https://pandavideo.com.br}
 * @author    2025 Eduardo Kraus {@link https://www.eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->version = 2025073000;
$plugin->requires = 2020061500;
$plugin->release = "1.0.3";
$plugin->component = "repository_pandavideo";
$plugin->maturity = MATURITY_STABLE;

$plugin->dependencies = [
    "mod_pandavideo" => 2025072000,
    "media_pandavideo" => 2025072000,
];
