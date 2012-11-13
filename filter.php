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
 * Moodle links filter for moodle.org
 *
 * @package    filter_moodlelinks
 * @copyright  2011 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class filter_moodlelinks extends moodle_text_filter {

    public function filter($text, array $options = array()) {


        $text = str_replace('Moodle download', '<a title="Auto-link" href="http://download.moodle.org/">Moodle download</a>', $text);

        $text = str_replace('download Moodle', '<a title="Auto-link" href="http://download.moodle.org/">download Moodle</a>', $text);
        $text = str_replace('download page', '<a title="Auto-link" href="http://download.moodle.org/">download page</a>', $text);

        $text = str_ireplace(' Moodle roadmap', ' <a title="Auto-link" href="http://docs.moodle.org/dev/Roadmap">Moodle Roadmap</a>', $text);
        $text = str_ireplace(' Moodle Themes', ' <a title="Auto-link" href="http://moodle.org/themes">Moodle Themes</a>', $text);


        $text = str_replace(' Using Moodle', ' <a title="Auto-link" href="http://moodle.org/course/view.php?id=5">Using Moodle</a>', $text);
        $text = str_ireplace(' moodle partners', ' <a title="Auto-link" href="http://moodle.com/">Moodle Partners</a>', $text);
        $text = str_ireplace(' moodle partner', ' <a title="Auto-link" href="http://moodle.com/">Moodle Partner</a>', $text);
        $text = str_ireplace(' moodle tracker', ' <a title="Auto-link" href="http://tracker.moodle.org/">Moodle Tracker</a>', $text);
        $text = str_ireplace(' Moodle jobs', ' <a title="Auto-link" href="http://moodle.org/jobs">Moodle jobs</a>', $text);
        $text = str_ireplace(' Moodle books', ' <a title="Auto-link" href="http://moodle.org/books">Moodle books</a>', $text);
        $text = str_ireplace(' MOOCH', ' <a title="Moodle.org Open Community Hub" href="http://hub.moodle.org/">MOOCH</a>', $text);
        $text = str_ireplace(' Planet Moodle',  ' <a title="Auto-link" href="http://planet.moodle.org/">Planet Moodle</a>', $text);
        $text = str_ireplace(' Moodle plugins', ' <a title="Auto-link" href="http://moodle.org/plugins/">Moodle plugins</a>', $text);
        $text = str_ireplace(' Plugins directory', ' <a title="Auto-link" href="http://moodle.org/plugins/">Plugins directory</a>', $text);

        $text = preg_replace("|cvs:/([[:alnum:]\./_-]*)([[:alnum:]/])|i",
                "<a title=\"Auto-link to Moodle CVS repository\" href=\"http://cvs.moodle.org/\\1\\2\">cvs:/$1$2</a>",
                $text);

        // Tim's spiffy new regexp, see test.php in this directory
        $regexp = '#' .
                  '(?:MDL|MDLSITE|CONTRIB)-\d+' . // The basic pattern we are trying to match (\d is any digit).
                  '\b' . // At the end of a word, That is, we don't want to match MDL-123xyz, but we don't care if we are followed by a space, punctionation or ...
                  '(?![^\'"<>]*[\'"]\s*(?:\w+=[\'"][^\'"]*[\'"])*\\\?>)' . // Try to avoid matching if we are inside a HTML attribute. relies on having moderately well-formed HTML.
                  '(?![^<]*</a>)' . // Try to avoid matching inside another link. Can be fooled by HTML like: <a href="..."><b>MDL-123</b></a>.
                  '#';
        $text = preg_replace($regexp,
                '<a title="Auto-link to Moodle Tracker" href="http://tracker.moodle.org/browse/$0">$0</a>',
                $text);

        // New regexp from Matteo Scaramuccia, based on Tim's one above (MDLSITE-1146) for better handling "Bug XXXX" matches.
        $regexp = '$' .
                  'bug #?(\d+)' . // The basic pattern we are trying to match (\d is any digit).
                  '\b' . // At the end of a word, That is, we don't want to match MDL-123xyz, but we don't care if we are followed by a space, punctionation or ...
                  '(?![^\'"<>]*[\'"]\s*(?:\w+=[\'"][^\'"]*[\'"])*\\\?>)' . // Try to avoid matching if we are inside a HTML attribute. relies on having moderately well-formed HTML.
                  '(?![^<]*</a>)' . // Try to avoid matching inside another link. Can be fooled by HTML like: <a href="..."><b>Bug #123</b></a>.
                  '$i';
        $text = preg_replace($regexp,
                '<a title="Auto-link to Moodle Tracker" href="http://tracker.moodle.org/browse/MDL-$1">$0</a>',
                $text);

        return $text;
    }
}
