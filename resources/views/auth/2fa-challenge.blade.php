<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Please enter your authentication code to login.') }}
    </div>

    @if (session('status'))
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('2fa.verify') }}" id="2fa-form">
        @csrf
        {{-- Add a hidden field to preserve the intended URL --}}
        <input type="hidden" name="intended_url" value="{{ session('url.intended') }}">

        <div>
            <x-input-label for="code" :value="__('Authentication Code')" />
            <x-text-input 
                id="code" 
                class="block mt-1 w-full" 
                type="text" 
                name="code" 
                required 
                autofocus 
                autocomplete="one-time-code"
                pattern="[0-9]*"
                inputmode="numeric"
                minlength="6"
                maxlength="6"
            />
            <x-input-error :messages="$errors->get('code')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-primary-button>
                {{ __('Verify') }}
            </x-primary-button>
        </div>

        <div class="mt-4 text-center">
            <a href="{{ route('2fa.recovery') }}" class="text-sm text-gray-600 hover:text-gray-900 underline">
                {{ __('Use a recovery code') }}
            </a>
        </div>
    </form>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-submit when code is complete
            const codeInput = document.getElementById('code');
            codeInput.addEventListener('input', function(e) {
                if (this.value.length === 6) {
                    document.getElementById('2fa-form').submit();
                }
            });

            // Format input to numbers only
            codeInput.addEventListener('keypress', function(e) {
                if (!/[0-9]/.test(e.key)) {
                    e.preventDefault();
                }
            });
        });
    </script>
    @endpush
</x-guest-layout>