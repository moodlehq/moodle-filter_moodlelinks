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

    // All the phrases to convert to links.
    // Format: phrase => array(<a> tag, casesensitive, fullmatch, forcephrase).
    protected $links = array(
        // Some are case-sensitive, non full-match
        'Moodle download' => array('<a title="Auto-link" href="http://download.moodle.org/">', true, false),
        'download Moodle' => array('<a title="Auto-link" href="http://download.moodle.org/">', true, false),
        'download page' => array('<a title="Auto-link" href="http://download.moodle.org/">', true, false),

        // With this being full-match
        //'Using Moodle' => array('<a title="Auto-link" href="https://moodle.org/course/view.php?id=5">', true, true),

        // The rest are case-insensitive and full-match (and using forced phrase)
        'moodle roadmap' => array('<a title="Auto-link" href="http://docs.moodle.org/dev/Roadmap">', false, true, 'Moodle Roadmap'),
        'moodle themes' => array('<a title="Auto-link" href="https://moodle.org/themes">', false, true, 'Moodle Themes'),
        'moodle partners' => array('<a title="Auto-link" href="http://moodle.com/partners">', false, true, 'Moodle Partners'),
        'moodle partner' => array('<a title="Auto-link" href="http://moodle.com/partners">', false, true, 'Moodle Partner'),
        'moodle tracker' => array('<a title="Auto-link" href="https://tracker.moodle.org">', false, true, 'Moodle Tracker'),
        'moodle jobs' => array('<a title="Auto-link" href="https://moodle.org/jobs">', false, true, 'Moodle jobs'),
        'moodle books' => array('<a title="Auto-link" href="https://moodle.org/books">', false, true, 'Moodle books'),
        'mooch' => array('<a title="MoodleNet - Connecting and empowering educators worldwide" href="https://moodle.net">', false, true),
        'moodle.net' => array('<a title="MoodleNet - Connecting and empowering educators worldwide" href="https://moodle.net">', false, true),
        'moodlenet' => array('<a title="MoodleNet - Connecting and empowering educators worldwide" href="https://moodle.net">', false, true),
        'planet moodle' => array('<a title="Auto-link" href="http://planet.moodle.org">', false, true, 'Planet Moodle'),
        'moodle plugins' => array('<a title="Auto-link" href="https://moodle.org/plugins">', false, true, 'Moodle plugins'),
        'plugins directory' => array('<a title="Auto-link" href="https://moodle.org/plugins">', false, true, 'Plugins directory'),
        'moodlecloud' => array('<a title="Auto-link" href="https://moodle.com/cloud/">', false, true, 'MoodleCloud'),
        'Moodle Users Association' => array('<a title="Auto-link" href="https://moodleassociation.org/">', false, true, 'Moodle Users Association'),

        // Some case-sensitive abbrevs, full matched.
        'MUA' => array('<a title="Auto-link" href="https://moodleassociation.org/">', true, true),
    );

    public function filter($text, array $options = array()) {

        // Trivial-cache - keyed on $cachedcontextid.
        static $cachedcontextid;
        static $linklist;

        // Initialise/invalidate our trivial cache if dealing with a different context.
        if (!isset($cachedcontextid) || $cachedcontextid !== $this->context->id) {
            $cachedcontextid = $this->context->id;
            $linklist = array();
        }

        // Define the links if needed (may be cached).
        if (empty($linklist)) {
            foreach ($this->links as $search => $replace) {
                if (!isset($replace[0])) {
                    continue; // Skip if the target a tag is not specified.
                }
                $atagbegin = $replace[0];
                $atagend = '</a>';
                $casesensitive = isset($replace[1]) ? $replace[1] : false;
                $fullmatch = isset($replace[2]) ? $replace[2] : true;
                $forcephrase = isset($replace[3]) ? $replace[3] : null;
                $linklist[] = new filterobject($search, $atagbegin, $atagend, $casesensitive, $fullmatch, $forcephrase);
            }
            // Remove dupes, just in case.
            $linklist = filter_remove_duplicates($linklist);
        }

        // Let's filter all the filter objects.
        $text = filter_phrases($text, $linklist);

        // Some legacy links to the cvs repository.
        // TODO: Take them out once the cvs repo is down.
        $text = preg_replace("|cvs:/([[:alnum:]\./_-]*)([[:alnum:]/])|i",
                "<a title=\"Auto-link to Moodle CVS repository\" href=\"http://cvs.moodle.org/\\1\\2\">cvs:/$1$2</a>",
                $text);

        // TODO: Add links to the git repository

        // Tim's spiffy new regexp, see test.php in this directory
        $regexp = '#' .
                  '(?:MDL|MOBILE|MDLSITE|CONTRIB|MDLQA|MDLTEST)-\d+' . // The basic pattern we are trying to match (\d is any digit).
                  '\b' . // At the end of a word, That is, we don't want to match MDL-123xyz, but we don't care if we are followed by a space, punctionation or ...
                  '(?![^\'"<>]*[\'"]\s*(?:\w+=[\'"][^\'"]*[\'"])*\\\?>)' . // Try to avoid matching if we are inside a HTML attribute. relies on having moderately well-formed HTML.
                  '(?![^<]*</a>)' . // Try to avoid matching inside another link. Can be fooled by HTML like: <a href="..."><b>MDL-123</b></a>.
                  '#';
        $text = preg_replace($regexp,
                '<a title="Auto-link to Moodle Tracker" href="https://tracker.moodle.org/browse/$0">$0</a>',
                $text);

        return $text;
    }
}
