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
        $this->content->text = $output->review_list($courseswithreviews);

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
        //$mycourses = enrol_get_my_courses();
        $courseswithreviews = array();
        foreach ($sessions as $session) {
            $reviewcourses = array();
            $subjectreviews = progressreview_controller::get_reviews($session->id, null, null, $USER->id, PROGRESSREVIEW_SUBJECT);
            $tutorreviews = progressreview_controller::get_reviews($session->id, null, null, $USER->id, PROGRESSREVIEW_TUTOR);
            foreach ($subjectreviews as $review) {
                $course = clone($review->get_course());
                if (!array_key_exists($course->originalid, $reviewcourses)) {
                    $course->id = $course->originalid;
                    unset($course->originalid);
                    $course->reviewtype = PROGRESSREVIEW_SUBJECT;
                    $reviewcourses[$course->id] = $course;
                }
            }
            foreach ($tutorreviews as $review) {
                $course = clone($review->get_course());
                if (!array_key_exists($course->originalid, $reviewcourses)) {
                    $course->id = $course->originalid;
                    unset($course->originalid);
                    $course->reviewtype = PROGRESSREVIEW_TUTOR;
                    $reviewcourses[$course->id] = $course;
                }
            }
            $session->courses = $reviewcourses;
            $courseswithreviews[] = $session;
        }
        return $courseswithreviews;
    }

}
