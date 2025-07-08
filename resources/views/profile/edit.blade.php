<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-frappe-lavender leading-tight">
            {{ __('messages.profile') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <!-- Status Messages -->
            @if (session('status'))
                <div class="mb-4 p-4 bg-frappe-green/20 text-frappe-green border border-frappe-green rounded-lg">
                    {{ session('status') }}
                </div>
            @endif

            <div class="frosted-card overflow-hidden shadow-lg sm:rounded-xl">
                <div class="p-6">
                    <!-- Profile Update Form -->
                    <div class="mb-10">
                        <h3 class="text-lg font-medium text-frappe-blue mb-4">{{ __('messages.profile') }}</h3>

                        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                            @csrf
                            @method('patch')

                            <div class="space-y-4">
                                <!-- Avatar -->
                                <div>
                                    <x-input-label for="avatar" :value="__('messages.avatar')" class="text-frappe-subtext1 mb-3" />

                                    <div class="flex items-start gap-6">
                                        <!-- Avatar Preview -->
                                        <div class="flex flex-col items-center">
                                            <div class="relative group">
                                                <div id="avatar-preview"
                                                    class="w-24 h-24 rounded-full overflow-hidden bg-gradient-to-br from-frappe-blue/20 to-frappe-sapphire/20 border-2 border-frappe-surface2/30 flex items-center justify-center transition-all duration-300 group-hover:border-frappe-blue/50">
                                                    @if ($user->avatar)
                                                        <img id="current-avatar"
                                                            src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar"
                                                            class="w-full h-full object-cover">
                                                    @else
                                                        <div id="avatar-placeholder"
                                                            class="w-full h-full flex items-center justify-center">
                                                            <span
                                                                class="text-2xl font-bold text-frappe-blue">{{ substr($user->name, 0, 1) }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <!-- Loading indicator -->
                                                <div id="avatar-loading"
                                                    class="absolute inset-0 bg-frappe-surface0/80 backdrop-blur-sm rounded-full flex items-center justify-center hidden">
                                                    <div
                                                        class="animate-spin rounded-full h-6 w-6 border-2 border-frappe-blue border-t-transparent">
                                                    </div>
                                                </div>
                                            </div>
                                            <span
                                                class="text-xs text-frappe-subtext1 mt-2 text-center">{{ __('messages.profile_picture') }}</span>
                                        </div>

                                        <!-- Upload Controls -->
                                        <div class="flex-1">
                                            <!-- Custom File Input -->
                                            <div class="relative">
                                                <input id="avatar" name="avatar" type="file" class="hidden"
                                                    accept="image/*" onchange="previewAvatar(this)">
                                                <label for="avatar"
                                                    class="frosted-button inline-flex items-center gap-2 px-4 py-2 rounded-lg cursor-pointer transition-all">
                                                    <x-heroicon-o-photo class="w-5 h-5" />
                                                    {{ __('messages.choose_photo') }}
                                                </label>
                                            </div>

                                            <!-- File Info -->
                                            <div id="file-info" class="mt-2 hidden">
                                                <div
                                                    class="bg-frappe-surface0/30 rounded-lg p-3 border border-frappe-surface2/30">
                                                    <div class="flex items-center gap-2">
                                                        <x-heroicon-o-document-arrow-up
                                                            class="w-4 h-4 text-frappe-green" />
                                                        <span id="file-name"
                                                            class="text-sm text-frappe-text font-medium"></span>
                                                        <span id="file-size"
                                                            class="text-xs text-frappe-subtext1"></span>
                                                    </div>
                                                    <button type="button" onclick="clearFileInput()"
                                                        class="text-xs text-frappe-red hover:text-frappe-red/80 mt-1">
                                                        {{ __('messages.remove') }}
                                                    </button>
                                                </div>
                                            </div>

                                            <!-- Remove Avatar Button -->
                                            @if ($user->avatar)
                                                <div class="mt-3">
                                                    <button type="button" onclick="removeAvatar()"
                                                        class="inline-flex items-center gap-2 px-3 py-2 text-sm border border-frappe-red/30 text-frappe-red rounded-lg hover:bg-frappe-red/10 transition-colors">
                                                        <x-heroicon-o-trash class="w-4 h-4" />
                                                        {{ __('messages.remove_avatar') }}
                                                    </button>
                                                </div>
                                            @endif

                                            <!-- Upload Guidelines -->
                                            <div class="mt-4 text-xs text-frappe-subtext1">
                                                <p>{{ __('messages.avatar_guidelines') }}</p>
                                                <ul class="list-disc list-inside mt-1 space-y-1">
                                                    <li>{{ __('messages.max_file_size_2mb') }}</li>
                                                    <li>{{ __('messages.supported_formats') }}</li>
                                                    <li>{{ __('messages.recommended_size') }}</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <x-input-error :messages="$errors->get('avatar')" class="mt-2 text-frappe-red" />
                                </div>

                                <!-- Name -->
                                <div>
                                    <x-input-label for="name" :value="__('messages.name')" class="text-frappe-subtext1" />
                                    <x-text-input id="name" name="name" type="text" class="block mt-1 w-full"
                                        :value="old('name', $user->name)" required autofocus />
                                    <x-input-error :messages="$errors->get('name')" class="mt-1 text-frappe-red" />
                                </div>

                                <!-- Email -->
                                <div>
                                    <x-input-label for="email" :value="__('messages.email')" class="text-frappe-subtext1" />
                                    <x-text-input id="email" name="email" type="email" class="block mt-1 w-full"
                                        :value="old('email', $user->email)" required />
                                    <x-input-error :messages="$errors->get('email')" class="mt-1 text-frappe-red" />
                                </div>

                                <!-- Phone Number -->
                                <div>
                                    <x-input-label for="phone_number" :value="__('messages.phone')" class="text-frappe-subtext1" />
                                    <x-text-input id="phone_number" name="phone_number" type="tel"
                                        class="block mt-1 w-full" :value="old('phone_number', $user->phone_number)" />
                                    <x-input-error :messages="$errors->get('phone_number')" class="mt-1 text-frappe-red" />
                                </div>

                                <div class="flex items-center justify-end">
                                    <x-primary-button>
                                        {{ __('messages.save_profile') }}
                                    </x-primary-button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Password Update Form -->
                    <div class="border-t border-frappe-surface1 pt-8">
                        <h3 class="text-lg font-medium text-frappe-blue mb-4">{{ __('messages.update_password') }}</h3>

                        <form method="POST" action="{{ route('profile.update-password') }}">
                            @csrf

                            <div class="space-y-4">
                                <!-- Current Password -->
                                <div>
                                    <x-input-label for="current_password" :value="__('messages.current_password')"
                                        class="text-frappe-subtext1" />
                                    <x-text-input id="current_password" name="current_password" type="password"
                                        class="block mt-1 w-full" required />
                                    <x-input-error :messages="$errors->get('current_password')" class="mt-1 text-frappe-red" />
                                </div>

                                <!-- New Password -->
                                <div>
                                    <x-input-label for="password" :value="__('messages.new_password')" class="text-frappe-subtext1" />
                                    <x-text-input id="password" name="password" type="password"
                                        class="block mt-1 w-full" required />
                                    <x-input-error :messages="$errors->get('password')" class="mt-1 text-frappe-red" />
                                </div>

                                <!-- Confirm Password -->
                                <div>
                                    <x-input-label for="password_confirmation" :value="__('messages.confirm_new_password')"
                                        class="text-frappe-subtext1" />
                                    <x-text-input id="password_confirmation" name="password_confirmation"
                                        type="password" class="block mt-1 w-full" required />
                                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-frappe-red" />
                                </div>

                                <div class="flex items-center justify-end">
                                    <x-primary-button>
                                        {{ __('messages.update_password') }}
                                    </x-primary-button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Remove Avatar Confirmation Modal -->
    <div id="removeAvatarModal"
        class="fixed inset-0 items-center justify-center bg-black bg-opacity-60 backdrop-blur-sm z-50 hidden">
        <div class="frosted-modal p-6 rounded-2xl shadow-2xl w-full max-w-md mx-4">
            <h3 class="text-xl font-semibold mb-4 text-frappe-red">{{ __('messages.remove_avatar') }}</h3>
            <p class="mb-6 text-frappe-text opacity-90">{{ __('messages.confirm_remove_avatar') }}</p>
            <form id="removeAvatarForm" method="POST" action="{{ route('profile.remove-avatar') }}">
                @csrf
                @method('DELETE')
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="hideRemoveAvatarModal()"
                        class="px-6 py-2 bg-gradient-to-r from-gray-500/20 to-gray-600/20 backdrop-blur-sm border border-gray-400/30 text-gray-300 rounded-lg hover:from-gray-500/30 hover:to-gray-600/30 transition-all">
                        {{ __('messages.cancel') }}
                    </button>
                    <button type="submit"
                        class="px-6 py-2 bg-gradient-to-r from-red-500/30 to-pink-500/30 backdrop-blur-sm border border-red-400/40 text-red-300 rounded-lg hover:from-red-500/40 hover:to-pink-500/40 transition-all">
                        {{ __('messages.remove_avatar') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function previewAvatar(input) {
            const fileInfo = document.getElementById('file-info');
            const fileName = document.getElementById('file-name');
            const fileSize = document.getElementById('file-size');
            const avatarPreview = document.getElementById('avatar-preview');
            const currentAvatar = document.getElementById('current-avatar');
            const avatarPlaceholder = document.getElementById('avatar-placeholder');
            const avatarLoading = document.getElementById('avatar-loading');

            if (input.files && input.files[0]) {
                const file = input.files[0];

                if (!file.type.match('image.*')) {
                    alert('Please select a valid image file.');
                    input.value = '';
                    return;
                }

                if (file.size > 2 * 1024 * 1024) {
                    alert('File size must be less than 2MB.');
                    input.value = '';
                    return;
                }

                avatarLoading.classList.remove('hidden');

                fileName.textContent = file.name;
                fileSize.textContent = formatFileSize(file.size);
                fileInfo.classList.remove('hidden');

                const reader = new FileReader();
                reader.onload = function(e) {
                    // Clear all existing content in avatar preview
                    avatarPreview.innerHTML = '';

                    // Create new preview image
                    const previewImg = document.createElement('img');
                    previewImg.src = e.target.result;
                    previewImg.alt = 'Avatar Preview';
                    previewImg.className = 'w-full h-full object-cover';
                    previewImg.id = 'preview-image';

                    avatarPreview.appendChild(previewImg);

                    // Hide loading
                    avatarLoading.classList.add('hidden');
                };

                reader.readAsDataURL(file);
            }
        }

        function clearFileInput() {
            const input = document.getElementById('avatar');
            const fileInfo = document.getElementById('file-info');
            const avatarPreview = document.getElementById('avatar-preview');

            // Clear file input
            input.value = '';

            // Hide file info
            fileInfo.classList.add('hidden');

            // Clear all existing content in avatar preview
            avatarPreview.innerHTML = '';

            // Show original avatar if it exists, otherwise show placeholder
            @if ($user->avatar)
                const originalAvatar = document.createElement('img');
                originalAvatar.src = '{{ asset('storage/' . $user->avatar) }}';
                originalAvatar.alt = 'Avatar';
                originalAvatar.className = 'w-full h-full object-cover';
                originalAvatar.id = 'current-avatar';
                avatarPreview.appendChild(originalAvatar);
            @else
                const placeholder = document.createElement('div');
                placeholder.id = 'avatar-placeholder';
                placeholder.className = 'w-full h-full flex items-center justify-center';
                placeholder.innerHTML =
                    '<span class="text-2xl font-bold text-frappe-blue">{{ substr($user->name, 0, 1) }}</span>';
                avatarPreview.appendChild(placeholder);
            @endif
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        function removeAvatar() {
            document.getElementById('removeAvatarModal').classList.remove('hidden');
            document.getElementById('removeAvatarModal').classList.add('flex');
        }

        function hideRemoveAvatarModal() {
            document.getElementById('removeAvatarModal').classList.add('hidden');
            document.getElementById('removeAvatarModal').classList.remove('flex');
        }
    </script>
</x-app-layout>
