<x-guest-layout>
    <div class="frosted-card rounded-xl shadow-lg p-6 max-w-md mx-auto">
        <div class="mb-4 text-sm text-frappe-subtext1">
            {{ __('messages.email_verification_prompt') }}
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="mb-4 font-medium text-sm text-frappe-green">
                {{ __('messages.new_verification_link_sent') }}
            </div>
        @endif

        <div class="mt-4 flex items-center justify-between">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf

                <div>
                    <x-primary-button>
                        {{ __('messages.verification_email_resend') }}
                    </x-primary-button>
                </div>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button type="submit"
                    class="underline text-sm text-frappe-subtext1 hover:text-frappe-text rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-frappe-blue">
                    {{ __('messages.logout') }}
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>
