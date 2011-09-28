<?php

class block_progressreview_renderer extends plugin_renderer_base {

    function review_list($courseswithreviews) {
        $output = '';
        foreach ($courseswithreviews as $session) {
            $output .= $this->output->heading($session->name, 3);
            $courselinks = array();
            foreach ($session->courses as $course) {
                $reviewurl = new moodle_url('/local/progressreview/subjectreview.php', array('sessionid' => $session->id, 'courseid' => $course->id));
                $courselinks[] = html_writer::link($reviewurl, $course->fullname);
            }
            $output .= html_writer::alist($courselinks);
        }
        return $output;
    }
}
