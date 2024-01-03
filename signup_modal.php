<div id="signup_modal" class="modal fade" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered shadow-none modal-lg" role="document">
        <div class="modal-content">
            <form id="signup_modal_form">
                <div class="modal-header">
                    <h5 class="modal-title">Sign Up</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-6 form-group">
                            <label class="d-block mb-0">
                                First Name <span class="text-danger">*</span>
                                <input name="firstname" class="form-control form-control-sm shadow-sm mt-2" type="text" placeholder="First Name..." required>
                            </label>
                        </div>

                        <div class="col-lg-6 form-group">
                            <label class="d-block mb-0">
                                Last Name
                                <input name="lastname" class="form-control form-control-sm shadow-sm mt-2" type="text" placeholder="Last Name...">
                            </label>
                        </div>

                        <div class="col-lg-6 form-group">
                            <label class="d-block mb-0">
                                Mobile number <span class="text-danger">*</span>
                                <div class="input-group input-group-sm mt-2">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text shadow-sm bg-white border-right-0 pr-1" style="font-size:0.875rem;font-weight: 400;line-height: 1.55;padding-bottom: .2rem;">
                                            +880
                                        </span>
                                    </div>
                                    <input name="contactno" class="form-control shadow-sm border-left-0 pl-1" type="tel" placeholder="Mobile number..." required>
                                    <div class="invalid-feedback">Please provide a Mobile number.</div>
                                </div>

                            </label>
                        </div>

                        <div class="col-lg-6 form-group">
                            <label class="d-block mb-0">
                                Email <span class="text-danger">*</span>
                                <input name="email" class="form-control form-control-sm shadow-sm mt-2" type="email" placeholder="Email..." required>
                            </label>
                        </div>

                        <div class="col-lg-6">
                            <div class="row mb-2">
                                <div class="col-12 mb-2">Date of birth <span class="text-danger">*</span></div>

                                <div class="col-4">
                                    <select name="dob_date" class="form-control form-control-sm shadow-sm text-dark" required>
                                        <?php
                                        for ($i = 1; $i <= 31; $i++) {
                                            $j = str_pad($i, 2, 0, STR_PAD_LEFT);
                                            echo "<option value='$j'>$j</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="col-4 pl-0">
                                    <select name="dob_month" class="form-control form-control-sm shadow-sm text-dark" required>
                                        <?php
                                        for ($i = 1; $i <= 12; $i++) {
                                            $j = str_pad($i, 2, 0, STR_PAD_LEFT);
                                            $monthName = date("M", mktime(0, 0, 0, $i, 10));
                                            echo "<option value='$j'>$monthName</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="col-4 pl-0">
                                    <select name="dob_year" class="form-control form-control-sm shadow-sm text-dark" required>
                                        <?php
                                        $currentYear = date("Y");
                                        for ($i = $currentYear; $i > 1900; $i--) {
                                            echo "<option value='$i'>$i</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <label class="d-block">Gender <span class="text-danger">*</span></label>

                            <div role="group" class="btn-group-toggle" data-toggle="buttons">
                                <label class="btn btn-outline-primary btn-sm ripple custom_shadow mb-2">
                                    <input name="gender" value="1" type="radio" class="form-check-input" required> Male
                                </label>
                                <label class="btn btn-outline-primary btn-sm ripple custom_shadow mb-2">
                                    <input name="gender" value="2" type="radio" class="form-check-input" required> Female
                                </label>
                                <label class="btn btn-outline-primary btn-sm ripple custom_shadow mb-2">
                                    <input name="gender" value="3" type="radio" class="form-check-input" required> Others
                                </label>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <label class="d-block">Username <span class="text-danger">*</span></label>

                            <div role="group" class="btn-group-toggle" data-toggle="buttons">
                                <label class="btn btn-outline-primary btn-sm ripple custom_shadow mb-2">
                                    <input name="username_type" value="1" type="radio" class="form-check-input" required> Set mobile number as username
                                </label>
                                <label class="btn btn-outline-primary btn-sm ripple custom_shadow mb-2">
                                    <input name="username_type" value="2" type="radio" class="form-check-input" required> Set email as username
                                </label>
                                <label class="btn btn-outline-primary btn-sm ripple custom_shadow mb-2">
                                    <input name="username_type" value="3" type="radio" class="form-check-input" required> Custom username
                                </label>
                                <input name="username" class="form-control form-control-sm shadow-sm mb-2" style="display: none;" type="text" placeholder="Username...">
                            </div>
                        </div>

                        <div class="col-lg-6 mb-3 mb-lg-0">
                            <label class="position-relative d-block mb-0">
                                Password <span class="text-danger">*</span>
                                <input name="password" class="form-control form-control-sm shadow-sm mt-2" type="password" minlength="6" placeholder="Password..." required>
                                <i class="toggle_password far fa-eye position-absolute" style="right: 30px;bottom: 10px;"></i>
                            </label>
                        </div>

                        <div class="col-lg-6">
                            <label class="position-relative d-block mb-0">
                                Re-type Password <span class="text-danger">*</span>
                                <input name="retype_password" class="form-control form-control-sm shadow-sm mt-2" type="password" minlength="6" placeholder="Re-type Password..." required>
                                <i class="toggle_password far fa-eye position-absolute" style="right: 30px;bottom: 10px;"></i>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="submit" class="btn btn-success ripple font-size-lg px-5 custom_shadow">Sign Up</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="forgotten_password_modal" class="modal fade" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="forgotten_password_modal_form">
                <div class="modal-header">
                    <h5 class="modal-title">Forgotten Password?</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <label class="d-block mb-0">
                        Please provide your email <span class="text-danger">*</span>
                        <input name="email" class="form-control shadow-sm mt-2 text-dark" type="email" placeholder="Email..." required>
                    </label>
                </div>
                <div class="modal-footer py-2">
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>