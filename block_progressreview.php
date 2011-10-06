<?php
require_once($CFG->dirroot.'/local/progressreview/lib.php');

class block_progressreview extends block_base {

    public function init() {
        $this->title = get_string('pluginname', 'block_progressreview');
    }

    function instance_allow_multiple() {
        return false;
    }

    function  instance_can_be_hidden() {
        return false;
    }

    function applicable_formats() {
        return array(
            'all' => false,
            'site' => true
        );
    }

    public function get_content() {
        global $CFG;

        $output = $this->page->get_renderer('block_progressreview');
        $currentsessions = $this->get_current_sessions();
        $courseswithreviews = $this->get_my_courses_with_reviews($currentsessions);
        $this->content->text = $output->review_list($currentsessions);

    }

    private function get_current_sessions() {
        $sessions = progressreview_controller::get_sessions();
        $activesessions = array();
        foreach ($sessions as $session) {
            if ($session->deadline_tutor >= strtotime('1 week ago')) {
                $activesessions[] = $session;
            }
        }
        return $activesessions;
    }

    private function get_my_courses_with_reviews($sessions) {
        global $USER;
        $mycourses = enrol_get_my_courses();
        $courseswithreviews = array();
        foreach ($sessions as $session) {
            $reviewcourses = array();
            foreach($mycourses as $course) {
                if(progressreview_controller::get_reviews($session->id, null, $course->id, $USER->id)) {
                    $course->reviewtype = PROGRESSREVIEW_SUBJECT;
                    $reviewcourses[] = $course;
                }
            }
            foreach($mycourses as $course) {
                if(progressreview_controller::get_reviews($session->id, null, $course->id, $USER->id, PROGRESSREVIEW_TUTOR)) {
                    $course->reviewtype = PROGRESSREVIEW_TUTOR;
                    $reviewcourses[] = $course;
                }
            }
            $session->courses = $reviewcourses;
            $courseswithreviews[] = $session; 
        }
    }

}
