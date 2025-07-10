<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['text' => __('messages.businesses'), 'url' => route('businesses.index')],
            ['text' => __('messages.settings') . ' - ' . $business->name, 'url' => null]
        ]" />
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        @if (session('success'))
            <div class="mb-4 p-3 bg-frappe-green/20 text-frappe-green rounded">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 p-3 bg-frappe-red/20 text-frappe-red rounded">
                {{ session('error') }}
            </div>
        @endif

        <div class="frosted-card rounded-xl shadow-lg overflow-hidden">
            <form method="POST" action="{{ route('settings.update') }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="business_id" value="{{ $business->id }}">

                <div class="p-6 space-y-6">
                    <!-- Booking Restrictions -->
                    <div class="border-b border-frappe-surface1/30 pb-6">
                        <h3 class="text-lg font-semibold text-frappe-lavender mb-4">
                            <x-heroicon-o-calendar class="w-5 h-5 inline mr-2" />
                            {{ __('messages.booking_restrictions') }}
                        </h3>

                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="booking_advance_hours" :value="__('messages.minimum_hours_in_advance')" />
                                <x-text-input id="booking_advance_hours" name="booking_advance_hours" type="number"
                                    class="block w-full mt-1" :value="old('booking_advance_hours', $settings['booking_advance_hours'])" min="0" max="168"
                                    required />
                                <p class="text-sm text-frappe-subtext1 mt-1">
                                    {{ __('messages.minimum_hours_before_appointment') }}</p>
                                <x-input-error :messages="$errors->get('booking_advance_hours')" class="mt-2 text-frappe-red text-sm" />
                            </div>

                            <div>
                                <x-input-label for="booking_advance_days" :value="__('messages.maximum_days_in_advance')" />
                                <x-text-input id="booking_advance_days" name="booking_advance_days" type="number"
                                    class="block w-full mt-1" :value="old('booking_advance_days', $settings['booking_advance_days'])" min="1" max="365"
                                    required />
                                <p class="text-sm text-frappe-subtext1 mt-1">
                                    {{ __('messages.maximum_days_advance_bookings') }}</p>
                                <x-input-error :messages="$errors->get('booking_advance_days')" class="mt-2 text-frappe-red text-sm" />
                            </div>

                            <div>
                                <x-input-label for="allow_cancellation_hours" :value="__('messages.cancellation_hours_limit')" />
                                <x-text-input id="allow_cancellation_hours" name="allow_cancellation_hours"
                                    type="number" class="block w-full mt-1" :value="old('allow_cancellation_hours', $settings['allow_cancellation_hours'])" min="0"
                                    max="168" required />
                                <p class="text-sm text-frappe-subtext1 mt-1">
                                    {{ __('messages.hours_before_cancellation') }}</p>
                                <x-input-error :messages="$errors->get('allow_cancellation_hours')" class="mt-2 text-frappe-red text-sm" />
                            </div>

                            <div>
                                <x-input-label for="booking_buffer_minutes" :value="__('messages.buffer_time_minutes')" />
                                <x-text-input id="booking_buffer_minutes" name="booking_buffer_minutes" type="number"
                                    class="block w-full mt-1" :value="old('booking_buffer_minutes', $settings['booking_buffer_minutes'])" min="0" max="120"
                                    required />
                                <p class="text-sm text-frappe-subtext1 mt-1">
                                    {{ __('messages.buffer_time_between_appointments') }}</p>
                                <x-input-error :messages="$errors->get('booking_buffer_minutes')" class="mt-2 text-frappe-red text-sm" />
                            </div>
                        </div>
                    </div>

                    <!-- General Settings -->
                    <div class="border-b border-frappe-surface1/30 pb-6">
                        <h3 class="text-lg font-semibold text-frappe-lavender mb-4">
                            <x-heroicon-o-cog-8-tooth class="w-5 h-5 inline mr-2" />
                            {{ __('messages.general_settings') }}
                        </h3>

                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="currency" :value="__('messages.currency')" />
                                <select id="currency" name="currency"
                                    class="block w-full mt-1 bg-frappe-surface0/50 border-frappe-surface1/30 text-frappe-text rounded-md shadow-sm backdrop-blur-sm focus:border-frappe-blue focus:ring-frappe-blue/50"
                                    required>
                                    @foreach ($availableCurrencies as $code => $name)
                                        <option value="{{ $code }}" @selected(old('currency', $settings['currency']) == $code)>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-sm text-frappe-subtext1 mt-1">
                                    {{ __('messages.select_business_currency') }}</p>
                                <x-input-error :messages="$errors->get('currency')" class="mt-2 text-frappe-red text-sm" />
                            </div>

                            <div>
                                <x-input-label for="business_timezone" :value="__('messages.business_timezone')" />
                                <select id="business_timezone" name="business_timezone"
                                    class="block w-full mt-1 bg-frappe-surface0 border-frappe-surface1 text-frappe-text rounded-md">
                                    @foreach (['Europe/Budapest', 'Europe/London', 'Europe/Paris', 'Europe/Berlin', 'America/New_York', 'America/Los_Angeles', 'Asia/Tokyo'] as $tz)
                                        <option value="{{ $tz }}" @selected(old('business_timezone', $settings['business_timezone']) == $tz)>
                                            {{ $tz }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('business_timezone')" class="mt-2 text-frappe-red text-sm" />
                            </div>
                        </div>
                    </div>

                    <!-- Business Status -->
                    <div class="border-b border-frappe-surface1/30 pb-6">
                        <h3 class="text-lg font-semibold text-frappe-lavender mb-4">
                            <x-heroicon-o-shield-check class="w-5 h-5 inline mr-2" />
                            {{ __('messages.business_status') }}
                        </h3>

                        <div class="space-y-4">
                            <div class="flex items-center">
                                <input id="holiday_mode" name="holiday_mode" type="checkbox"
                                    @checked(old('holiday_mode', $settings['holiday_mode']))
                                    class="rounded border-frappe-surface1 text-frappe-blue focus:ring-frappe-blue focus:ring-offset-0">
                                <label for="holiday_mode" class="ml-3 text-frappe-text">
                                    {{ __('messages.holiday_mode') }}
                                    <span
                                        class="block text-sm text-frappe-subtext1">{{ __('messages.temporarily_disable_bookings') }}</span>
                                </label>
                            </div>

                            <div class="flex items-center">
                                <input id="maintenance_mode" name="maintenance_mode" type="checkbox"
                                    @checked(old('maintenance_mode', $settings['maintenance_mode']))
                                    class="rounded border-frappe-surface1 text-frappe-blue focus:ring-frappe-blue focus:ring-offset-0">
                                <label for="maintenance_mode" class="ml-3 text-frappe-text">
                                    {{ __('messages.maintenance_mode') }}
                                    <span
                                        class="block text-sm text-frappe-subtext1">{{ __('messages.show_maintenance_message') }}</span>
                                </label>
                            </div>

                            <div class="flex items-center">
                                <input id="booking_confirmation_required" name="booking_confirmation_required"
                                    type="checkbox" @checked(old('booking_confirmation_required', $settings['booking_confirmation_required']))
                                    class="rounded border-frappe-surface1 text-frappe-blue focus:ring-frappe-blue focus:ring-offset-0">
                                <label for="booking_confirmation_required" class="ml-3 text-frappe-text">
                                    {{ __('messages.booking_confirmation_required') }}
                                    <span
                                        class="block text-sm text-frappe-subtext1">{{ __('messages.require_manual_confirmation') }}</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Notifications -->
                    <div class="pb-6">
                        <h3 class="text-lg font-semibold text-frappe-lavender mb-4">
                            <x-heroicon-o-bell class="w-5 h-5 inline mr-2" />
                            {{ __('messages.notifications') }}
                        </h3>

                        <div class="space-y-4">
                            <div class="flex items-center">
                                <input id="notification_email" name="notification_email" type="checkbox"
                                    @checked(old('notification_email', $settings['notification_email']))
                                    class="rounded border-frappe-surface1 text-frappe-blue focus:ring-frappe-blue focus:ring-offset-0">
                                <label for="notification_email" class="ml-3 text-frappe-text">
                                    {{ __('messages.email_notifications') }}
                                    <span
                                        class="block text-sm text-frappe-subtext1">{{ __('messages.send_email_notifications') }}</span>
                                </label>
                            </div>

                            <div class="flex items-center">
                                <input id="notification_sms" name="notification_sms" type="checkbox"
                                    @checked(old('notification_sms', $settings['notification_sms']))
                                    class="rounded border-frappe-surface1 text-frappe-blue focus:ring-frappe-blue focus:ring-offset-0">
                                <label for="notification_sms" class="ml-3 text-frappe-text">
                                    {{ __('messages.sms_notifications') }}
                                    <span
                                        class="block text-sm text-frappe-subtext1">{{ __('messages.send_sms_notifications') }}</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 bg-frappe-surface0/20 border-t border-frappe-surface1/30 flex justify-between">
                    <button type="button" onclick="showResetModal()"
                        class="px-6 py-2 bg-gradient-to-r from-red-500/30 to-pink-500/30 backdrop-blur-sm border border-red-400/40 text-red-300 rounded-lg hover:from-red-500/40 hover:to-pink-500/40 transition-all">
                        <x-heroicon-o-arrow-path class="w-4 h-4 inline mr-2" />
                        {{ __('messages.reset_to_defaults') }}
                    </button>

                    <x-primary-button class="bg-frappe-blue hover:bg-frappe-sapphire">
                        <x-heroicon-o-check class="w-4 h-4 mr-2" />
                        {{ __('messages.save_settings') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>

    <!-- Reset Confirmation Modal -->
    <div id="resetModal"
        class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-60 backdrop-blur-sm z-50 hidden">
        <div class="frosted-modal p-6 rounded-2xl shadow-2xl w-full max-w-md mx-4">
            <h3 class="text-xl font-semibold mb-4 text-frappe-red">{{ __('messages.reset_settings') }}</h3>
            <p class="mb-6 text-frappe-text opacity-90">
                {{ __('messages.reset_settings_confirmation') }}
            </p>

            <form method="POST" action="{{ route('settings.reset') }}">
                @csrf
                <input type="hidden" name="business_id" value="{{ $business->id }}">

                <div class="flex justify-end gap-3">
                    <button type="button" onclick="hideResetModal()"
                        class="px-6 py-2 bg-gradient-to-r from-gray-500/20 to-gray-600/20 backdrop-blur-sm border border-gray-400/30 text-gray-300 rounded-lg hover:from-gray-500/30 hover:to-gray-600/30 transition-all">
                        {{ __('messages.cancel') }}
                    </button>
                    <button type="submit"
                        class="px-6 py-2 bg-gradient-to-r from-red-500/30 to-pink-500/30 backdrop-blur-sm border border-red-400/40 text-red-300 rounded-lg hover:from-red-500/40 hover:to-pink-500/40 transition-all">
                        {{ __('messages.reset_settings') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showResetModal() {
            document.getElementById('resetModal').classList.remove('hidden');
        }

        function hideResetModal() {
            document.getElementById('resetModal').classList.add('hidden');
        }
    </script>
</x-app-layout>
