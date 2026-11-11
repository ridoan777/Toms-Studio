<x-app-layout>

    @php
        $breadCrumbs = [
            ['label' => 'Settings', 'url' => 'backend_settings_index', 'icon' => 'gearLine'],
            ['label' => 'System'],
        ];
    @endphp

    <x-indicators.breadcrumb :crumbs="$breadCrumbs" />
    <!-- ---------------- APP AREA Starts ---------------- -->

    <!-- Start Main Widgets -->
    <x-indicators.page_header_widget pageName="Settings: System Management" />
    <!-- End Main Widgets -->


    <!-- Alert Component -->
    <x-indicators.alert-component message="Form Submission" />
    <!-- Alert Component -->

    @php
        $timezones = collect(\DateTimeZone::listIdentifiers())
            ->map(function ($tz) {
                $now = new \DateTime('now', new \DateTimeZone($tz));
                $offset = $now->getOffset() / 3600;

                $sign = $offset >= 0 ? '+' : '-';
                $hours = str_pad(abs((int) $offset), 2, '0', STR_PAD_LEFT);
                $minutes = str_pad(abs(($offset - (int) $offset) * 60), 2, '0', STR_PAD_LEFT);

                return [
                    'id' => $tz,
                    'label' => "(UTC {$sign}{$hours}:{$minutes}) {$tz}",
                    'offset' => $offset,
                ];
            })
            ->sortBy('offset')
            ->values();
    @endphp

    @php
        $ACCORDION_1_VISIBILITY = 'hidden'; // hidden or ''
        $ACCORDION_2_VISIBILITY = ''; // hidden or ''
        $ENV_CONTENT = [];
    @endphp

    <!---------------------------------------------------------------------------------------------- accordion -->
    <div class="hs-accordion-group">

        <x-ui_items.accordion.accordion-destroy />

        <div class="displayArea my-5 p-3">
            <form method="POST" action="{{ route('backend_settings_system_default_store') }}"
                class="rounded-lg p-2 dark:bg-neutral-700" enctype="multipart/form-data">
                @csrf

                <div>
                    <!-- -->
                    <div class="mb-5 mt-2 px-8 flex_between_center">
                        <label class="inputLabelMedium">Timezone
                            <span class="redLabel">*</span>
                            <span class="text-sm text-gray-400">(All data are stored on the Database as UTC-0. This
                                option is for display-convertion purpose only. Be cautious⚠️! The conversion may not be 100%
                                correct sometimes.)</span>
                        </label>
                        <div class="flex gap-x-6">
                            <select name="timezone" class="w-full dropdownSelectInputs">
                                @foreach ($timezones->groupBy('offset') as $offset => $zones)
                                    <optgroup label="UTC {{ $offset >= 0 ? '+' : '' }}{{ $offset }}">
                                        @foreach ($zones as $tz)
                                            <option value="{{ $tz['id'] }}" {{ ($defaultEnvVars['DISPLAY_TIMEZONE'] ?? null) === $tz['id'] ? 'selected' : '' }}>{{ $tz['id'] }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <button type="submit" class="saveButton my-4">Save</button>
            </form>
        </div>

        <div id="hs-destroy-heading-one"
            class="hs-accordion-to-destroy hs-accordion hs-accordion-active:border-gray-200 PrelineAccordionItemParent">

            <x-ui_items.accordion.accordion_toggle_button toggleLabel="Mailer Settings" />

            <div id="hs-destroy-collapse-one"
                class="hs-accordion-content {{ $ACCORDION_1_VISIBILITY }} w-full overflow-hidden transition-[height] duration-300"
                role="region" aria-labelledby="hs-destroy-heading-one">
                <div class="p-3">
                    <!-- --------------------------------- -->
                    <section
                        class="displayArea mx-auto my-5 rounded-lg border-none bg-white p-4 shadow-md shadow-gray-400 dark:bg-neutral-700 dark:shadow-none">
                        <div class="my-5">
                            <form method="POST" action="{{ route('backend_settings_system_mailer_store') }}"
                                class="rounded-lg p-2 dark:bg-neutral-700" enctype="multipart/form-data">
                                @csrf

                                <!-- IDENTIFIER INPUTS -->
                                <div class="mb-5 mt-2">
                                </div>
                                <!-- IDENTIFIER INPUTS -->

                                <div>

                                    <div class="mb-5 mt-2">
                                        <label for="mail_mailer" class="inputLabelMedium">Mailer Provider
                                            (MAIL_MAILER)
                                            <span class="redLabel">*</span>
                                            <span class="text-sm text-gray-400">(default: smtp)</span>
                                        </label>
                                        <input type="text" name="mail_mailer" class="normalInput"
                                            value="{{ $emailEnvVars['MAIL_MAILER'] ?? 'smtp' }}">
                                    </div>
                                    <!-- -->
                                    <div class="grid grid-cols-2 gap-4">

                                        <div class="mb-5 mt-2 col-span-1">
                                            <label for="mail_host" class="inputLabelMedium">Host</label>
                                            <input class="normalInput" name="mail_host"
                                                value="{{ $emailEnvVars['MAIL_HOST'] ?? null }}"></input>
                                        </div>

                                        <div class="mb-5 mt-2 col-span-1">
                                            <label for="mail_port" class="inputLabelMedium">Port</label>
                                            <input class="normalInput" name="mail_port"
                                                value="{{ $emailEnvVars['MAIL_PORT'] ?? null }}"></input>
                                        </div>
                                    </div>

                                    <!--  -->
                                    <div class="grid grid-cols-2 gap-4">

                                        <div class="mb-5 mt-2 col-span-1">
                                            <label for="mail_username" class="inputLabelMedium">Username</label>
                                            <input class="normalInput" name="mail_username"
                                                value="{{ $emailEnvVars['MAIL_USERNAME'] ?? null }}"></input>
                                        </div>

                                        <div class="mb-5 mt-2 col-span-1">
                                            <label for="mail_password" class="inputLabelMedium">Password</label>
                                            <input class="normalInput" name="mail_password"
                                                value="{{ $emailEnvVars['MAIL_PASSWORD'] ?? null }}"></input>
                                        </div>
                                    </div>

                                    <!--  -->
                                    <div class="grid grid-cols-2 gap-4">

                                        <div class="mb-5 mt-2 col-span-1">
                                            <label for="mail_encryption" class="inputLabelMedium">Encryption
                                                <span class="text-sm text-gray-400">(default: tls)</span>
                                            </label>
                                            <input class="normalInput" name="mail_encryption"
                                                value="{{ $emailEnvVars['MAIL_ENCRYPTION'] ?? null }}"></input>
                                        </div>

                                        <div class="mb-5 mt-2 col-span-1">
                                            <label for="mail_scheme" class="inputLabelMedium">Mail Scheme
                                                <span class="text-sm text-gray-400">(keep this blank unless you have a
                                                    valid
                                                    value)</span>
                                            </label>
                                            <input class="normalInput" name="mail_scheme"
                                                value="{{ $emailEnvVars['MAIL_SCHEME'] ?? null }}"></input>
                                        </div>
                                    </div>

                                    <!--  -->

                                    <div class="grid grid-cols-2 gap-4">

                                        <div class="mb-5 mt-2 col-span-1">
                                            <label for="mail_from_name" class="inputLabelMedium">Email 'From' Name
                                                <span class="text-sm text-gray-400">(the name of the sender in
                                                    emails)</span>
                                            </label>
                                            <input class="normalInput" name="mail_from_name"
                                                value="{{ $emailEnvVars['MAIL_FROM_NAME'] ?? null }}"></input>
                                        </div>

                                        <div class="mb-5 mt-2 col-span-1">
                                            <label for="mail_from_address" class="inputLabelMedium">Email 'From'
                                                Address
                                                <span class="text-sm text-gray-400">(The email address of the sender.
                                                    Use valid
                                                    address.)</span>
                                            </label>
                                            <input class="normalInput" name="mail_from_address"
                                                value="{{ $emailEnvVars['MAIL_FROM_ADDRESS'] ?? null }}"></input>
                                        </div>
                                    </div>
                                    <!--  -->
                                </div>

                                <button type="submit" class="saveButton my-4">Save</button>
                            </form>
                        </div>
                    </section>
                    <!-- --------------------------------- -->
                </div>
            </div>
        </div>

        <!--  -->

    </div>


    <!------------------------------------------------------------------------------------------------>
    <!-- ---------------- APP AREA Starts ---------------- -->



</x-app-layout>