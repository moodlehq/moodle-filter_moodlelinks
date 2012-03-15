<?php

defined('MOODLE_INTERNAL') || die();

//////////////////////////////////////////////////////////////
//  Various links for Moodle.org
//////////////////////////////////////////////////////////////

/// Changed to 2.0 moodle_text_filter by Eloy 20101102

class filter_moodlelinks extends moodle_text_filter {

    public function filter($text, array $options = array()) {


        $text = str_replace('modules download', '<a title="Auto-link" href="http://download.moodle.org/modules/">modules download</a>', $text);

        $text = str_replace('Moodle download', '<a title="Auto-link" href="http://download.moodle.org/">Moodle download</a>', $text);

        $text = str_replace('download Moodle', '<a title="Auto-link" href="http://download.moodle.org/">download Moodle</a>', $text);
        $text = str_replace('download page', '<a title="Auto-link" href="http://download.moodle.org/">download page</a>', $text);

        $text = str_ireplace(' Moodle roadmap', ' <a title="Auto-link" href="http://docs.moodle.org/dev/Roadmap">Moodle Roadmap</a>', $text);
        $text = str_ireplace(' Moodle Themes', ' <a title="Auto-link" href="http://moodle.org/themes">Moodle Themes</a>', $text);

        $text = str_ireplace(' modules and plugins', ' <a title="Auto-link" href="http://moodle.org/mod/data/view.php?id=6009">Modules and Plugins</a>', $text);

        $text = str_ireplace(' moodle modules', ' <a title="Auto-link" href="http://moodle.org/mod/data/view.php?id=6009">Moodle modules</a>', $text);

        $text = str_replace(' Using Moodle', ' <a title="Auto-link" href="http://moodle.org/course/view.php?id=5">Using Moodle</a>', $text);
        $text = str_ireplace(' moodle lounge', ' <a title="Auto-link" href="http://moodle.org/course/view.php?id=55">Moodle Lounge</a>', $text);
        $text = str_ireplace(' moodle partners', ' <a title="Auto-link" href="http://moodle.com/">Moodle Partners</a>', $text);
        $text = str_ireplace(' moodle partner', ' <a title="Auto-link" href="http://moodle.com/">Moodle Partner</a>', $text);
        $text = str_ireplace(' moodle tracker', ' <a title="Auto-link" href="http://tracker.moodle.org/">Moodle Tracker</a>', $text);
        $text = str_ireplace(' Moodle jobs', ' <a title="Auto-link" href="http://moodle.org/jobs">Moodle jobs</a>', $text);
        $text = str_ireplace(' Moodle books', ' <a title="Auto-link" href="http://moodle.org/books">Moodle books</a>', $text);
        $text = str_ireplace(' Plugins directory', ' <a title="Auto-link" href="http://moodle.org/plugins/">Plugins directory</a>', $text);
        $text = str_ireplace(' Planet Moodle',  ' <a title="Auto-link" href="http://planet.moodle.org/">Planet Moodle</a>', $text);
        $text = str_ireplace(' Moodle Docs', ' <a title="Auto-link" href="http://docs.moodle.org/">Moodle Docs</a>', $text);
        $text = str_ireplace(' MOOCH', ' <a title="Moodle.org Open Community Hub" href="http://hub.moodle.org/">MOOCH</a>', $text);

        $text = eregi_replace("cvs:/([[:alnum:]\./_-]*)([[:alnum:]/])",
                "<a title=\"Auto-link to Moodle CVS repository\" href=\"http://cvs.moodle.org/\\1\\2\">cvs:/\\1\\2</a>", 
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

        $text = eregi_replace("bug ([0-9]+)",
                "<a title=\"Auto-link to Moodle Tracker\" href=\"http://tracker.moodle.org/browse/MDL-\\1\">MDL-\\1</a>", 
                $text);

        $text = eregi_replace("bug #([0-9]+)",
                "<a title=\"Auto-link to Moodle Tracker\" href=\"http://tracker.moodle.org/browse/MDL-\\1\">MDL-\\1</a>", 
                $text);

        return $text;
    }
}
