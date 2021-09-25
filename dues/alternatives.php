<?php
require('../template/top.php');
require(BASE . '/api/discord/bots/admin.php');
head('Pay Dues via Alternatives', true);

$uid = $userinfo['id'];
$term = $untrobotics->get_current_term();
$year = $untrobotics->get_current_year();

// verify the user does not already have a request for this semester

$q_submission_check = $db->query("SELECT * FROM dues_alternative_payments WHERE
    uid = '"  . $db->real_escape_string($uid) . "' AND
    requested_term = '"  . $db->real_escape_string($term) . "' AND
    requested_year = '"  . $db->real_escape_string($year) . "' LIMIT 1
");

$request_already_submitted = false;
if ($q_submission_check && $q_submission_check->num_rows > 0) {
    $request_already_submitted = true;
} else if (!$q_submission_check) {
    // an error occurred running the query
    $fatal_error = "An unexpected error occurred when trying to validate your request. Please notify our web team.";
}

if (isset($_POST['submit'])) {
    $alternative_payment_reason = $_POST['alternative_payment_reason'];
    $other_description = $_POST['other_description'];
    $confirmation = $_POST['confirmation'];
    $term_string = Semester::get_name_from_value($term);

    do {
        if ($confirmation != "1") {
            // user did not talk to an officer first :(
            $error = "You must talk to an officer before you fill out this form. Please join our discord and talk to anyone with the @Officer role.";
            break;
        }

        if ($alternative_payment_reason == "other" && empty($other_description)) {
            $error = "Please enter a description to expand on the reason that you making this request.";
            break;
        }

        if ($request_already_submitted) {
            break;
        }

        // let's create the approval database entry
        $q = $db->query("INSERT INTO dues_alternative_payments
        (
            uid,
            alternative_payment_reason,
            other_description,
            requested_term,
            requested_year
        )
        
        VALUES
        (
            '"  . $db->real_escape_string($uid) . "',
            '"  . $db->real_escape_string($alternative_payment_reason) . "',
            '"  . $db->real_escape_string($other_description) . "',
            '"  . $db->real_escape_string($term) . "',
            '"  . $db->real_escape_string($year) . "'
        )
        ");

        $id = $db->insert_id;

        $other_description = isset($_POST['other_description']) ? $_POST['other_description'] : "no extra description";
        AdminBot::send_message(
        "A user has requested an approval for an alternative dues payment:\n" .
                "- Name: {$userinfo['name']}\n" .
                "- Reason: {$alternative_payment_reason} ({$other_description})\n" .
                "- Semester: {$term_string}, {$year}\n" .
                "To approve/deny this request, please go here: http://untro.bo/admin/alternative-dues?id={$id}"
        );

        $success = "Request sent! You will receive an email update once an officer has approved or denied your request.";

    } while (false);
}

?>

<style>
    label.checkbox-container {
        display: inline-block;
    }
    .dues-payment-button {
        display: inline-block;
    }
    .dues-payment-button.two-semesters {
        display: none;
    }
    .select2-container--bootstrap .select2-selection--single .select2-selection__rendered {
        color: #686868;
        padding: 18px;
    }
    #other_description {
        border: 1px solid gainsboro;
        border-radius: 6px;
        margin-top: 25px;
        padding: 20px;
        display: none;
    }
</style>

<main class="page-content">
    <section class="section-50 section-md-75 section-lg-100">
        <div class="shell">
            <div class="range range-md-justify">
                <div class="cell-md-12">
                    <div class="inset-md-right-30 inset-lg-right-0 text-center">

                        <h1>Pay Dues</h1>
                        <h4><small>(via alternative payment methods)</small></h4>
                        <form action="" method="POST">
                            <?php
                            if (is_current_user_authenticated()) {

                                if (isset($success)) {
                                    ?>
                                    <div class="alert alert-success alert-inline"><?php echo $success; ?></div>
                                    <?php
                                } else if (isset($fatal_error)) {
                                    ?>
                                    <div class="alert alert-danger alert-inline"><?php echo $fatal_error; ?></div>
                                    <?php
                                } else if ($request_already_submitted) {
                                    ?>
                                    <div class="alert alert-info alert-inline">You have already made a request this semester.</div>
                                    <?php
                                } else if (!$untrobotics->is_user_in_good_standing($userinfo)) {

                                    if (isset($error)) {
                                        ?>
                                        <div class="alert alert-danger alert-inline"><?php echo $error; ?></div>
                                        <?php
                                    }

                                    if ($userinfo['discord_id'] != NULL) {
                                        ?>

                                        <p class="offset-top-20">Dues can be paid via alternative methods, or you may request an exemption from paying dues each semester due to your circumstances.</p>
                                        <p class="offset-top-10">Please select the reason below why your are requesting an alternative dues payment. This will be sent to our leadership for approval, so please make sure to communicate with an officer before submitting this form. If you are requesting an exemption, please reach out to our treasurer at <a href="mailto:hello@untrobotics.com">hello@untrobotics.com</a></p>

                                        <div class="row offset-top-10">
                                            <div class="col-lg-offset-4 col-lg-4 col-sm-12">
                                                <select id="alternative_payment_reason" name="alternative_payment_reason" class="">
                                                    <option>Select reason...</option>
                                                    <option value="paid-in-person">Paid in Person</option>
                                                    <option value="circumstances">Extenuating circumstances</option>
                                                    <option value="nasa-sl-volunteer">Senior Design NASA SL Team</option>
                                                    <option value="other">Other</option>
                                                </select>
                                            </div>
                                        </div>

                                        <textarea id="other_description" name="other_description" class="form-control" placeholder="Please enter a description of the reason you are making this request"></textarea>

                                        <div class="offset-top-20">
                                            <div class="form-group">
                                                <label class="checkbox-container"> I have spoken to an officer already
                                                    <input autocomplete="off" name="confirmation" type="checkbox" class="form-control form-control-has-validation form-control-last-child checkbox-custom" value="1"><span class="checkbox-custom-dummy"></span>
                                                    <span class="checkmark"></span>
                                                </label>
                                            </div>
                                        </div>

                                        <button type="submit" class="btn btn-default offset-top-35" name="submit" value="submit">Submit</button>

                                        <?php
                                    } else {
                                        // user must associate with discord first
                                        ?>
                                        <p>Please associate your Discord account with your UNT Robotics account before completing this page by <a href="/join/w/discord?returnto=<?php echo urlencode('/dues/alternatives'); ?>">clicking here</a>.</p>
                                        <?php
                                    }
                                } else {
                                    ?>
                                    <div class="alert alert-info alert-inline">You have already paid your dues for this semester. :&#41;</div>
                                    <?php
                                }
                            } else {
                                ?>
                                <div class="alert alert-info alert-inline">You must <a href="/auth/login?returnto=<?php echo urlencode('/dues/alternatives'); ?>">log in</a> to pay dues.</div>
                                <?php
                            }
                            ?>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
footer(false);
?>

<script>
    $("#alternative_payment_reason").on("change", function(e) {
        const value = this.value;

        if (value === "other") {
            // display the "other" textarea
            $("#other_description").css("display", "block");
        } else {
            $("#other_description").css("display", "none");
        }
    });
</script>