<!-- Signup Modal -->
<div id="signup_modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-dark-900 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

        <!-- This element is to trick the browser into centering the modal contents. -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-dark-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full border border-dark-700">
            <div class="bg-dark-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="flex justify-between items-center mb-6 border-b border-dark-700 pb-4">
                    <h3 class="text-2xl font-heading font-bold text-white" id="modal-title">Sign Up</h3>
                    <button type="button" class="close-modal text-text-muted hover:text-white focus:outline-none">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <form id="signup_modal_form">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-text-muted mb-2">First Name <span class="text-red-500">*</span></label>
                            <input name="firstname" type="text" class="w-full bg-dark-900 border border-dark-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-neon-cyan" placeholder="First Name..." required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-text-muted mb-2">Last Name</label>
                            <input name="lastname" type="text" class="w-full bg-dark-900 border border-dark-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-neon-cyan" placeholder="Last Name...">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-text-muted mb-2">Mobile number <span class="text-red-500">*</span></label>
                            <div class="flex">
                                <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-dark-700 bg-dark-900 text-text-muted text-sm">
                                    +880
                                </span>
                                <input name="primarycontact" type="tel" class="flex-1 w-full bg-dark-900 border border-dark-700 rounded-r-lg px-4 py-2 text-white focus:outline-none focus:border-neon-cyan" placeholder="Mobile number..." required>
                            </div>
                            <div class="invalid-feedback text-red-500 text-xs mt-1 hidden">Please provide a Mobile number.</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-text-muted mb-2">Email <span class="text-red-500">*</span></label>
                            <input name="email" type="email" class="w-full bg-dark-900 border border-dark-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-neon-cyan" placeholder="Email..." required>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-text-muted mb-2">Date of birth <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-3 gap-4">
                                <select name="dob_date" class="bg-dark-900 border border-dark-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-neon-cyan" required>
                                    <?php
                                    for ($i = 1; $i <= 31; $i++) {
                                        $j = str_pad($i, 2, 0, STR_PAD_LEFT);
                                        echo "<option value='$j'>$j</option>";
                                    }
                                    ?>
                                </select>
                                <select name="dob_month" class="bg-dark-900 border border-dark-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-neon-cyan" required>
                                    <?php
                                    for ($i = 1; $i <= 12; $i++) {
                                        $j = str_pad($i, 2, 0, STR_PAD_LEFT);
                                        $monthName = date("M", mktime(0, 0, 0, $i, 10));
                                        echo "<option value='$j'>$monthName</option>";
                                    }
                                    ?>
                                </select>
                                <select name="dob_year" class="bg-dark-900 border border-dark-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-neon-cyan" required>
                                    <?php
                                    $currentYear = date("Y");
                                    for ($i = $currentYear; $i > 1900; $i--) {
                                        echo "<option value='$i'>$i</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-text-muted mb-2">Gender <span class="text-red-500">*</span></label>
                            <div class="flex gap-4">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="gender" value="1" class="form-radio text-neon-cyan bg-dark-900 border-dark-700" required>
                                    <span class="ml-2 text-text-muted">Male</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="gender" value="2" class="form-radio text-neon-cyan bg-dark-900 border-dark-700" required>
                                    <span class="ml-2 text-text-muted">Female</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="gender" value="3" class="form-radio text-neon-cyan bg-dark-900 border-dark-700" required>
                                    <span class="ml-2 text-text-muted">Others</span>
                                </label>
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-text-muted mb-2">Username <span class="text-red-500">*</span></label>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="radio" name="username_type" value="1" class="form-radio text-neon-cyan bg-dark-900 border-dark-700" required>
                                    <span class="ml-2 text-text-muted">Set mobile number as username</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="username_type" value="2" class="form-radio text-neon-cyan bg-dark-900 border-dark-700" required>
                                    <span class="ml-2 text-text-muted">Set email as username</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="username_type" value="3" class="form-radio text-neon-cyan bg-dark-900 border-dark-700" required>
                                    <span class="ml-2 text-text-muted">Custom username</span>
                                </label>
                                <input name="username" type="text" class="w-full bg-dark-900 border border-dark-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-neon-cyan mt-2 hidden" placeholder="Username...">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-text-muted mb-2">Password <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input name="password" type="password" class="w-full bg-dark-900 border border-dark-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-neon-cyan" minlength="6" placeholder="Password..." required>
                                <i class="toggle_password far fa-eye absolute right-3 top-3 text-text-muted cursor-pointer hover:text-white"></i>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-text-muted mb-2">Re-type Password <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input name="retype_password" type="password" class="w-full bg-dark-900 border border-dark-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-neon-cyan" minlength="6" placeholder="Re-type Password..." required>
                                <i class="toggle_password far fa-eye absolute right-3 top-3 text-text-muted cursor-pointer hover:text-white"></i>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 text-center">
                        <button type="submit" class="bg-neon-cyan text-dark-900 font-heading font-bold py-3 px-8 rounded-lg hover:bg-neon-blue transition-colors duration-300 uppercase tracking-widest shadow-lg shadow-neon-cyan/20">
                            Sign Up
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Forgotten Password Modal -->
<div id="forgotten_password_modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-dark-900 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-dark-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-dark-700">
            <div class="bg-dark-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="flex justify-between items-center mb-6 border-b border-dark-700 pb-4">
                    <h3 class="text-xl font-heading font-bold text-white">Forgotten Password?</h3>
                    <button type="button" class="close-modal text-text-muted hover:text-white focus:outline-none">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form id="forgotten_password_modal_form">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-text-muted mb-2">Please provide your email <span class="text-red-500">*</span></label>
                        <input name="email" type="email" class="w-full bg-dark-900 border border-dark-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-neon-cyan" placeholder="Email..." required>
                    </div>
                    <div class="message_div"></div>
                    <div class="mt-6 text-center">
                        <button type="submit" class="bg-neon-cyan text-dark-900 font-heading font-bold py-2 px-6 rounded-lg hover:bg-neon-blue transition-colors duration-300 uppercase tracking-widest shadow-lg shadow-neon-cyan/20">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
