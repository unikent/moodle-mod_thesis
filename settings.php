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
 * Local stuff for Mod Thesis
 *
 * @package    mod_thesis
 * @copyright  2017 University of Kent <is-lrd@kent.ac.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) {

    $settings = new admin_settingpage('mod_thesis', get_string('modulename', 'mod_thesis'));

    $settings->add(new admin_setting_configtext(
            'thesis_kar_username',
            get_string('kar:username', 'mod_thesis'),
            '',
            ''
    ));

    $settings->add(new admin_setting_configtext(
            'thesis_kar_password',
            get_string('kar:password', 'mod_thesis'),
            '',
            ''
    ));

    $settings->add(new admin_setting_configtext(
            'thesis_kar_server',
            get_string('kar:server', 'mod_thesis'),
            'Please include the https:// in the server url',
            ''
    ));

}

