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
 * Moodle links filter phpunit tests
 *
 * @package    filter_moodlelinks
 * @category   test
 * @copyright  2012 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/filter/moodlelinks/filter.php'); // Include the code to test

/**
 * Moodle links filter testcase
 */
class filter_moodlelinks_testcase extends basic_testcase {

    /**
     * Test some simple replaces, some case-sensitive, others no...
     */
    function test_filter_simple() {
        // Some simple words, originally replaced with str_[i]replace(), now
        // processed by the better filter_phrases() stuff. Results are 99% the
        // original ones but now we avoid replacing into tags and links.
        $texts = array(
            'AMoodle downloadZ' => 'A<a title="Auto-link" href="http://download.moodle.org/">Moodle download</a>Z',
            'AMOODLE downloadZ' => 'AMOODLE downloadZ', // Not replaced, case-sensitive search
            'Adownload MoodleZ' => 'A<a title="Auto-link" href="http://download.moodle.org/">download Moodle</a>Z',
            'Adownload MOODLEZ' => 'Adownload MOODLEZ', // Not replaced, case-sensitive search
            'Adownload pageZ' => 'A<a title="Auto-link" href="http://download.moodle.org/">download page</a>Z',
            'Adownload PAGEZ' => 'Adownload PAGEZ', // Not replaced, case-sensitive search
            'A Using Moodle,' => 'A Using Moodle,',
            'A Using MoodleZ' => 'A Using MoodleZ', // Not replaced, full-match search
            'A Using MOODLE' => 'A Using MOODLE', // Not replaced, case-sensitive search
            'A MOODLE roadmap.' => 'A <a title="Auto-link" href="http://docs.moodle.org/dev/Roadmap">Moodle Roadmap</a>.',
            'A MOODLE roadmapZ' => 'A MOODLE roadmapZ', // Not replaced, full-match search
            'AAMOODLE roadmap' => 'AAMOODLE roadmap', // Not replaced, full-match search
            'A MOODLE themes.' => 'A <a title="Auto-link" href="https://moodle.org/themes">Moodle Themes</a>.',
            'A MOODLE partners,' => 'A <a title="Auto-link" href="http://moodle.com/partners">Moodle Partners</a>,',
            'A MOODLE partner:' => 'A <a title="Auto-link" href="http://moodle.com/partners">Moodle Partner</a>:',
            'A MOODLE trackeR:' => 'A <a title="Auto-link" href="https://tracker.moodle.org">Moodle Tracker</a>:',
            'A MOODLE jobs/' => 'A <a title="Auto-link" href="https://moodle.org/jobs">Moodle jobs</a>/',
            '.MOODLE books' => '.<a title="Auto-link" href="https://moodle.org/books">Moodle books</a>',
            ',MoocH' => ',<a title="MoodleNet - Connecting and empowering educators worldwide" href="https://moodle.net">MoocH</a>',
            'MoOdLe.NeT' => '<a title="MoodleNet - Connecting and empowering educators worldwide" href="https://moodle.net">MoOdLe.NeT</a>',
            'MoodleNET' => '<a title="MoodleNet - Connecting and empowering educators worldwide" href="https://moodle.net">MoodleNET</a>',
            ' planet MOODLE' => ' <a title="Auto-link" href="http://planet.moodle.org">Planet Moodle</a>',
            ': MOODLE plugins' => ': <a title="Auto-link" href="https://moodle.org/plugins">Moodle plugins</a>',
            '[plugins DIRECTORY)' => '[<a title="Auto-link" href="https://moodle.org/plugins">Plugins directory</a>)',
            'see moodlecloud for details' => 'see <a title="Auto-link" href="https://moodle.com/cloud/">MoodleCloud</a> for details',
            'The moodle users association.' => 'The <a title="Auto-link" href="https://moodleassociation.org/">Moodle Users Association</a>.',
            ',MUA site.' => ',<a title="Auto-link" href="https://moodleassociation.org/">MUA</a> site.',
            'East Midlands Universities Association (EMUA)' => 'East Midlands Universities Association (EMUA)',
            // Verify MDLSITE-1632 (replacements into tags and links) is fixed.
            '<a title="to Moodle Tracker" href="">MDLSITE-111</a>' => '<a title="to Moodle Tracker" href="">MDLSITE-111</a>',
            '<a title="Auto-link" href="">to Moodle Tracker</a>' => '<a title="Auto-link" href="">to Moodle Tracker</a>'
        );

        $filter = new testable_filter_moodlelinks();

        foreach ($texts as $text => $expected) {
            $msg = "Testing text '$text':";
            $result = $filter->filter($text);

            $this->assertEquals($expected, $result, $msg);
        }
    }

    /**
     * Test some complexer links to the Moodle Tracker (including Tim's ones @ MDLSITE-647)
     */
    function test_filter_tracker() {
        $texts = array(
            // Not replaced cases by Tim's regexp
            'MDL-123Z' => 'MDL-123Z',
            '<a href="http://tracker.moodle.org/browse/CONTRIB-1234567890">CONTRIB-1234567890</a>' => '<a href="http://tracker.moodle.org/browse/CONTRIB-1234567890">CONTRIB-1234567890</a>',
            "<a href  =    'http://tracker.moodle.org/browse/CONTRIB-1234567890'>CONTRIB-1234567890</a>" => "<a href  =    'http://tracker.moodle.org/browse/CONTRIB-1234567890'>CONTRIB-1234567890</a>",
            '<a href="http://www.google.com.au/search?q=MDL-123">Google search</a>' => '<a href="http://www.google.com.au/search?q=MDL-123">Google search</a>',
            '<a href = "http://www.google.com.au/search?q=MDL-123"><br />Google search</a>' => '<a href = "http://www.google.com.au/search?q=MDL-123"><br />Google search</a>',
            '<a href="http://www.google.com.au/">go to Google and search for MDL-123</a>' => '<a href="http://www.google.com.au/">go to Google and search for MDL-123</a>',
            '<a href="http://www.google.com.au/">search for MDL-123 on Google</a>' => '<a href="http://www.google.com.au/">search for MDL-123 on Google</a>',
            '<a href="http://example.com/a/very/very/long/url/containing/MDL-123"><br />MDL-123</a>' => '<a href="http://example.com/a/very/very/long/url/containing/MDL-123"><br />MDL-123</a>',

            // A known limit of the regexp, we have to live with it (unless somebody fixes it without breaking the rest)
            'search Google for <a href="http://www.google.com.au/"><b>MDLSITE-0</b></a>' => 'search Google for <a href="http://www.google.com.au/"><b><a title="Auto-link to Moodle Tracker" href="https://tracker.moodle.org/browse/MDLSITE-0">MDLSITE-0</a></b></a>',

            // Replaced cases by Tim's regexp
            'MDL-123' => '<a title="Auto-link to Moodle Tracker" href="https://tracker.moodle.org/browse/MDL-123">MDL-123</a>',
            '<b>MDL-123</b>' => '<b><a title="Auto-link to Moodle Tracker" href="https://tracker.moodle.org/browse/MDL-123">MDL-123</a></b>',
            'See MDL-1 for details!' => 'See <a title="Auto-link to Moodle Tracker" href="https://tracker.moodle.org/browse/MDL-1">MDL-1</a> for details!',
            'http://www.google.com.au/search?q=MDL-1' => 'http://www.google.com.au/search?q=<a title="Auto-link to Moodle Tracker" href="https://tracker.moodle.org/browse/MDL-1">MDL-1</a>',
            '<a href="http://example.com">Link</a>http://www.google.com.au/search?q=MDL-123' => '<a href="http://example.com">Link</a>http://www.google.com.au/search?q=<a title="Auto-link to Moodle Tracker" href="https://tracker.moodle.org/browse/MDL-123">MDL-123</a>',
            'search for MDL-123 on <a href="http://www.google.com.au/">Google</a>' => 'search for <a title="Auto-link to Moodle Tracker" href="https://tracker.moodle.org/browse/MDL-123">MDL-123</a> on <a href="http://www.google.com.au/">Google</a>',
            '<br /> This should be working - MDL-123. Please vote for it if you\'d like... <br />' => '<br /> This should be working - <a title="Auto-link to Moodle Tracker" href="https://tracker.moodle.org/browse/MDL-123">MDL-123</a>. Please vote for it if you\'d like... <br />',
            "The bug 'MDL-123' is > (more serious than)" => "The bug '<a title=\"Auto-link to Moodle Tracker\" href=\"https://tracker.moodle.org/browse/MDL-123\">MDL-123</a>' is > (more serious than)",

            // The texts like 'bug #123' or 'bug 123' should not be processed (MDLSITE-4019).
            'Bug 123X' => 'Bug 123X',
            'Bug #123X' => 'Bug #123X',
            '<a   href="http://www.google.com.au/">Look for Bug 123</a>' => '<a   href="http://www.google.com.au/">Look for Bug 123</a>',
            'Bug 123' => 'Bug 123',
            'Bug #123' => 'Bug #123',
            'bUg 123' => 'bUg 123',
            '<b>Bug 123</b>' => '<b>Bug 123</b>',
            'http://www.google.com.au/search?q=Bug 123' => 'http://www.google.com.au/search?q=Bug 123',

            // Links to other projects (CONTRIB, MDLSITE, MDLQA, MDLTEST, MOBILE)
            'CONTRIB-1234567890' => '<a title="Auto-link to Moodle Tracker" href="https://tracker.moodle.org/browse/CONTRIB-1234567890">CONTRIB-1234567890</a>',
            'MDLSITE-0' => '<a title="Auto-link to Moodle Tracker" href="https://tracker.moodle.org/browse/MDLSITE-0">MDLSITE-0</a>',
            'MDLQA-0' => '<a title="Auto-link to Moodle Tracker" href="https://tracker.moodle.org/browse/MDLQA-0">MDLQA-0</a>',
            'MDLTEST-0' => '<a title="Auto-link to Moodle Tracker" href="https://tracker.moodle.org/browse/MDLTEST-0">MDLTEST-0</a>',
            'MOBILE-1234567890' => '<a title="Auto-link to Moodle Tracker" href="https://tracker.moodle.org/browse/MOBILE-1234567890">MOBILE-1234567890</a>',
        );

        $filter = new testable_filter_moodlelinks();

        foreach ($texts as $text => $expected) {
            $msg = "Testing text: ". str_replace('%', '%%', $text) . ": %s"; // Escape original '%' so sprintf() wont get confused
            $result = $filter->filter($text);

            $this->assertEquals($expected, $result, $msg);
        }
    }
}

/**
 * Subclass of filter_moodlelinks, for easier testing.
 */
class testable_filter_moodlelinks extends filter_moodlelinks {
    public function __construct() {
        $this->context = context_system::instance();
    }
}
