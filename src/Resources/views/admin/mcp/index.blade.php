<x-admin::layouts>
    <x-slot:title>
        @lang('mcp::app.admin.dashboard.title')
        </x-slot>

        <!-- Header with Logo -->
        <div class="flex items-center justify-between gap-4 max-sm:flex-wrap mb-6">
            <div class="flex items-center gap-4">
                <img src="{{ asset('vendor/mcp/images/logo.png') }}" alt="Bagisto MCP" class="h-10 w-auto" />
                <div>
                    <p class="text-xl font-bold text-gray-800 dark:text-white">
                        @lang('mcp::app.admin.dashboard.title')
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        @lang('mcp::app.admin.dashboard.api-description')
                    </p>
                </div>
            </div>
        </div>

        <!-- Server Status Cards -->
        <div class="grid gap-4 lg:grid-cols-4 md:grid-cols-2 sm:grid-cols-1">
            <!-- Status Card -->
            <div class="rounded-lg border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900">
                        <span class="icon-setting text-xl text-blue-600 dark:text-blue-400"></span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            @lang('mcp::app.admin.dashboard.server-status')
                        </p>
                        <p class="text-lg font-semibold {{ $serverEnabled ? 'text-green-600' : 'text-red-600' }}">
                            {{ $serverEnabled ? __('mcp::app.admin.dashboard.enabled') : __('mcp::app.admin.dashboard.disabled') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Version Card -->
            <div class="rounded-lg border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                <div class="flex items-center gap-4">
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-full bg-purple-100 dark:bg-purple-900">
                        <span class="icon-information text-xl text-purple-600 dark:text-purple-400"></span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            @lang('mcp::app.admin.dashboard.version')
                        </p>
                        <p class="text-lg font-semibold text-gray-800 dark:text-white">{{ $version }}</p>
                    </div>
                </div>
            </div>

            <!-- Tools Count Card -->
            <div class="rounded-lg border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                <div class="flex items-center gap-4">
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-full bg-orange-100 dark:bg-orange-900">
                        <span class="icon-product text-xl text-orange-600 dark:text-orange-400"></span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            @lang('mcp::app.admin.dashboard.total-tools')
                        </p>
                        <p class="text-lg font-semibold text-gray-800 dark:text-white">{{ $totalTools }}</p>
                    </div>
                </div>
            </div>

            <!-- Rate Limit Card -->
            <div class="rounded-lg border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-teal-100 dark:bg-teal-900">
                        <span class="icon-sales text-xl text-teal-600 dark:text-teal-400"></span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            @lang('mcp::app.admin.dashboard.rate-limit')
                        </p>
                        <p class="text-lg font-semibold text-gray-800 dark:text-white">{{ $rateLimit }}/min</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- API Endpoint Info -->
        <div class="mt-6 rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
            <h3 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white">
                @lang('mcp::app.admin.dashboard.api-endpoint')
            </h3>
            <div class="flex flex-wrap items-center gap-3">
                <code class="rounded-lg bg-gray-100 px-4 py-2.5 text-sm font-mono dark:bg-gray-800 dark:text-gray-300">
                POST {{ url($endpoint) }}
            </code>
                <span
                    class="rounded-full bg-yellow-100 px-3 py-1 text-xs font-medium text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                    {{ $status }}
                </span>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="mt-6 grid gap-4 lg:grid-cols-2 md:grid-cols-1">
            <a href="{{ route('admin.mcp.tools.index') }}"
                class="group rounded-lg border border-gray-200 bg-white p-6 transition-all hover:border-blue-500 hover:shadow-lg dark:border-gray-800 dark:bg-gray-900 dark:hover:border-blue-400">
                <div class="flex items-center gap-5">
                    <div
                        class="flex h-14 w-14 items-center justify-center rounded-xl bg-blue-100 transition-transform group-hover:scale-110 dark:bg-blue-900">
                        <span class="icon-product text-2xl text-blue-600 dark:text-blue-400"></span>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
                            @lang('mcp::app.admin.dashboard.manage-tools')
                        </h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            @lang('mcp::app.admin.dashboard.manage-tools-desc')
                        </p>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.mcp.settings.index') }}"
                class="group rounded-lg border border-gray-200 bg-white p-6 transition-all hover:border-blue-500 hover:shadow-lg dark:border-gray-800 dark:bg-gray-900 dark:hover:border-blue-400">
                <div class="flex items-center gap-5">
                    <div
                        class="flex h-14 w-14 items-center justify-center rounded-xl bg-blue-100 transition-transform group-hover:scale-110 dark:bg-blue-900">
                        <span class="icon-setting text-2xl text-blue-600 dark:text-blue-400"></span>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
                            @lang('mcp::app.admin.dashboard.configure')
                        </h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            @lang('mcp::app.admin.dashboard.configure-desc')
                        </p>
                    </div>
                </div>
            </a>
        </div>
</x-admin::layouts>