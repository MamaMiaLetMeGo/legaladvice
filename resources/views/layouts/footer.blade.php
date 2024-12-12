<footer class="bg-white border-t border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- About -->
            <div>
                <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">
                    About
                </h3>
                <p class="mt-4 text-base text-gray-500">
                    LegalAdvice.ai is a platform that connects you instantly with legal experts to help you with your legal questions.
                </p>
            </div>

            <!-- Navigation -->
            <div>
                <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">
                    Navigation
                </h3>
                <ul class="mt-4 space-y-4">
                    @inject('categories', 'App\Models\Category')
                    @foreach($categories::withCount('posts')->orderByDesc('posts_count')->take(3)->get() as $category)
                        <li>
                            <a href="{{ route('categories.show', $category) }}" class="text-base text-gray-500 hover:text-gray-900">
                                {{ $category->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Connect -->
            <div>
                <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">
                    Connect
                </h3>
                <ul class="mt-4 space-y-4">
                    <li>
                        <a href="{{ route('contact.show') }}" class="text-base text-gray-500 hover:text-gray-900">
                            Contact Us
                        </a>
                    </li>
                    <li class="flex space-x-4">
                        <a href="https://twitter.com/yourprofile" class="inline-flex items-center text-base text-gray-500 hover:text-gray-900">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                            </svg>
                        </a>
                    </li>
                </ul>
            </div> 
        </div>

        <div class="mt-8 border-t border-gray-200 pt-8">
            <p class="text-base text-gray-400 text-center">
                © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </p>
        </div>
    </div>
</footer>