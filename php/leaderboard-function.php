<?php

/**
 * Handles the actual generation of the leaderboards.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Generates the leaderboads
 *
 * @since 1.0.0
 * @param int $quiz_id int The ID of the quiz.
 * @return string The HTML of the leaderboard
 */
function qsm_addon_leaderboards_generate($quiz_id)
{

    global $wpdb;
    global $mlwQuizMasterNext;
    $quiz_id = intval($quiz_id);

    // Retrieves template, grading system, and name of quiz.
    $mlwQuizMasterNext->pluginHelper->prepare_quiz($quiz_id);
    $template = $mlwQuizMasterNext->pluginHelper->get_section_setting('quiz_leaderboards', 'template');
    $grade_system = $mlwQuizMasterNext->pluginHelper->get_section_setting('quiz_options', 'system');
    $form_type = $mlwQuizMasterNext->pluginHelper->get_section_setting('quiz_options', 'form_type');
    $quiz_name = $wpdb->get_var($wpdb->prepare("SELECT quiz_name FROM {$wpdb->prefix}mlw_quizzes WHERE deleted='0' AND quiz_id=%d", $quiz_id));

    // Prepares SQL for results, then retrieve results.
    $sql = "SELECT * FROM {$wpdb->prefix}mlw_results WHERE quiz_id=%d AND deleted='0'";
    if (!empty($form_type) && ($form_type == 1 || $form_type == 2)) {
        //Do nothing
    } else {
        if (0 == $grade_system) {
            $sql .= ' ORDER BY correct_score DESC';
        }
        if (1 == $grade_system) {
            $sql .= ' ORDER BY point_score DESC';
        }
        if (3 == $grade_system) {
            $sql .= ' ORDER BY correct_score, point_score DESC';
        }
    }
    $sql .= ' LIMIT 10';
    $results = $wpdb->get_results($wpdb->prepare($sql, $quiz_id));

    // Changes variable to quiz name.
    $template = str_replace('%QUIZ_NAME%', $quiz_name, $template);

    // Cycles through each result and use name/points for entry in leaderboard.
    $leader_count = 0;
    foreach ($results as $result) {
        $leader_count++;
        $user_meta = get_userdata($result->user);
        $username = $user_meta->data->user_login;
        $profile_link =  site_url() . '/author/' . $username;


        // Changes name to quiz taker's name.
        if ($leader_count == 1) {
            $template = str_replace("%FIRST_PLACE_NAME%", "<a href='{$profile_link}'>" . $result->name . "</a>", $template);
        }
        if ($leader_count == 2) {
            $template = str_replace("%SECOND_PLACE_NAME%", "<a href='{$profile_link}'>" . $result->name . "</a>", $template);
        }
        if ($leader_count == 3) {
            $template = str_replace("%THIRD_PLACE_NAME%", "<a href='{$profile_link}'>" . $result->name . "</a>", $template);
        }
        if ($leader_count == 4) {
            $template = str_replace("%FOURTH_PLACE_NAME%", $result->name, $template);
        }
        if ($leader_count == 5) {
            $template = str_replace("%FIFTH_PLACE_NAME%", $result->name, $template);
        }

        // Depending on grading system, use either score or points.
        if (!empty($form_type) && ($form_type == 1 || $form_type == 2)) {
            if ($leader_count == 1) {
                $template = str_replace("%FIRST_PLACE_SCORE%", "Not graded", $template);
            }
            if ($leader_count == 2) {
                $template = str_replace("%SECOND_PLACE_SCORE%", "Not graded", $template);
            }
            if ($leader_count == 3) {
                $template = str_replace("%THIRD_PLACE_SCORE%", "Not graded", $template);
            }
            if ($leader_count == 4) {
                $template = str_replace("%FOURTH_PLACE_SCORE%", "Not graded", $template);
            }
            if ($leader_count == 5) {
                $template = str_replace("%FIFTH_PLACE_SCORE%", "Not graded", $template);
            }
        } else {
            if ($grade_system == 0) {
                if ($leader_count == 1) {
                    $template = str_replace("%FIRST_PLACE_SCORE%", $result->correct_score . "%", $template);
                }
                if ($leader_count == 2) {
                    $template = str_replace("%SECOND_PLACE_SCORE%", $result->correct_score . "%", $template);
                }
                if ($leader_count == 3) {
                    $template = str_replace("%THIRD_PLACE_SCORE%", $result->correct_score . "%", $template);
                }
                if ($leader_count == 4) {
                    $template = str_replace("%FOURTH_PLACE_SCORE%", $result->correct_score . "%", $template);
                }
                if ($leader_count == 5) {
                    $template = str_replace("%FIFTH_PLACE_SCORE%", $result->correct_score . "%", $template);
                }
            }
            if ($grade_system == 1) {
                if ($leader_count == 1) {
                    $template = str_replace("%FIRST_PLACE_SCORE%", $result->point_score . " Points", $template);
                }
                if ($leader_count == 2) {
                    $template = str_replace("%SECOND_PLACE_SCORE%", $result->point_score . " Points", $template);
                }
                if ($leader_count == 3) {
                    $template = str_replace("%THIRD_PLACE_SCORE%", $result->point_score . " Points", $template);
                }
                if ($leader_count == 4) {
                    $template = str_replace("%FOURTH_PLACE_SCORE%", $result->point_score . " Points", $template);
                }
                if ($leader_count == 5) {
                    $template = str_replace("%FIFTH_PLACE_SCORE%", $result->point_score . " Points", $template);
                }
            }
            if ($grade_system == 3) {
                if ($leader_count == 1) {
                    $template = str_replace("%FIRST_PLACE_SCORE%", $result->correct_score . "% OR " . $result->point_score . " Points", $template);
                }
                if ($leader_count == 2) {
                    $template = str_replace("%SECOND_PLACE_SCORE%", $result->correct_score . "% OR " . $result->point_score . " Points", $template);
                }
                if ($leader_count == 3) {
                    $template = str_replace("%THIRD_PLACE_SCORE%", $result->correct_score . "% OR " . $result->point_score . " Points", $template);
                }
                if ($leader_count == 4) {
                    $template = str_replace("%FOURTH_PLACE_SCORE%", $result->correct_score . "% OR " . $result->point_score . " Points", $template);
                }
                if ($leader_count == 5) {
                    $template = str_replace("%FIFTH_PLACE_SCORE%", $result->correct_score . "% OR " . $result->point_score . " Points", $template);
                }
            }
        }
    }

    // Removes all variables in case any were missed.
    $template = str_replace("%QUIZ_NAME%", " ", $template);
    $template = str_replace("%FIRST_PLACE_NAME%", " ", $template);
    $template = str_replace("%SECOND_PLACE_NAME%", " ", $template);
    $template = str_replace("%THIRD_PLACE_NAME%", " ", $template);
    $template = str_replace("%FOURTH_PLACE_NAME%", " ", $template);
    $template = str_replace("%FIFTH_PLACE_NAME%", " ", $template);
    $template = str_replace("%FIRST_PLACE_SCORE%", " ", $template);
    $template = str_replace("%SECOND_PLACE_SCORE%", " ", $template);
    $template = str_replace("%THIRD_PLACE_SCORE%", " ", $template);
    $template = str_replace("%FOURTH_PLACE_SCORE%", " ", $template);
    $template = str_replace("%FIFTH_PLACE_SCORE%", " ", $template);

    // Return template
    return wpautop($template);
}
