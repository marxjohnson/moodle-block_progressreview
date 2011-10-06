<?php

class block_progressreview_renderer extends plugin_renderer_base {

    function review_list($courseswithreviews) {
        $output = '';
        foreach ($courseswithreviews as $session) {
            if (!empty($session->courses)) {
                $output .= $this->output->heading($session->name, 3);
                $courselinks = array();
                foreach ($session->courses as $course) {
                    if ($course->reviewtype == PROGRESSREVIEW_SUBJECT) {
                        $reviewurl = new moodle_url('/local/progressreview/subjectreview.php', array('sessionid' => $session->id, 'courseid' => $course->id));
                    } else if ($course->reviewtype == PROGRESSREVIEW_TUTOR) {
                        $reviewurl = new moodle_url('/local/progressreview/tutorreview.php', array('sessionid' => $session->id, 'courseid' => $course->id));
                    } else {
                        throw new coding_exception('Each course must have a reviewtype set to either PROGRESSREVIEW_SUBJECT or PROGRESSREVIEW_TUTOR');
                    }
                    $courselinks[] = html_writer::link($reviewurl, $course->fullname);

                }
                $output .= html_writer::alist($courselinks);
            }
        }
        return $output;
    }
}
