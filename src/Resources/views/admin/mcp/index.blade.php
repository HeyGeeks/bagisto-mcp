<x-admin::layouts>
    <x-slot:title>
        @lang('mcp::app.admin.dashboard.title')
        </x-slot>

        <!-- Header -->
        <div class="flex items-center justify-between p-4 bg-white dark:bg-gray-900 rounded-lg box-shadow mb-4">
            <div class="flex items-center gap-2">
                <h1 class="text-xl font-bold text-gray-800 dark:text-white">
                    @lang('mcp::app.admin.dashboard.title')
                </h1>

                @if($serverEnabled ?? true)
                    <span class="label-active text-[10px] mx-2">
                        Active
                    </span>
                @else
                    <span class="label-pending text-[10px] mx-2">
                        Inactive
                    </span>
                @endif
            </div>

            <div class="text-sm text-gray-500">
                v{{ $version ?? '0.1.0' }}
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4 mb-4">
            <!-- Total Tools -->
            <div class="p-4 bg-white dark:bg-gray-900 rounded-lg box-shadow">
                <div class="flex items-center gap-4">
                    <div class="p-3 rounded-full bg-blue-50 text-blue-600 dark:bg-gray-800 dark:text-gray-300">
                        <span class="icon-product text-2xl"></span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            @lang('mcp::app.admin.dashboard.total-tools')
                        </p>
                        <p class="text-xl font-bold text-gray-800 dark:text-white">
                            {{ $totalTools ?? 0 }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Enabled Tools -->
            <div class="p-4 bg-white dark:bg-gray-900 rounded-lg box-shadow">
                <div class="flex items-center gap-4">
                    <div class="p-3 rounded-full bg-green-50 text-green-600 dark:bg-gray-800 dark:text-gray-300">
                        <span class="icon-done text-2xl"></span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            @lang('mcp::app.admin.dashboard.enabled-tools')
                        </p>
                        <p class="text-xl font-bold text-gray-800 dark:text-white">
                            {{ $enabledTools ?? 0 }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Links & Info -->
        <div class="grid gap-4 lg:grid-cols-2">
            <div class="p-4 bg-white dark:bg-gray-900 rounded-lg box-shadow">
                <h3 class="font-semibold text-gray-800 dark:text-white mb-4">Quick Actions</h3>
                <div class="grid gap-3">
                    <a href="{{ route('admin.mcp.tools.index') }}"
                        class="flex items-center justify-between p-3 rounded border border-gray-200 hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-gray-800 transition-colors">
                        <div class="flex items-center gap-3">
                            <span class="icon-settings text-xl text-gray-500"></span>
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-300">Manage Tools</span>
                        </div>
                        <span class="icon-arrow-right text-gray-400 text-xl"></span>
                    </a>
                </div>
            </div>

            <div class="p-4 bg-white dark:bg-gray-900 rounded-lg box-shadow">
                <h3 class="font-semibold text-gray-800 dark:text-white mb-4">Server Info</h3>
                <div class="space-y-3">
                    <div
                        class="flex items-center justify-between text-sm py-2 border-b border-gray-100 dark:border-gray-800">
                        <span class="text-gray-500">Endpoint</span>
                        <code
                            class="bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded text-gray-600">/{{ config('mcp.endpoint', 'mcp') }}</code>
                    </div>
                    <div
                        class="flex items-center justify-between text-sm py-2 border-b border-gray-100 dark:border-gray-800">
                        <span class="text-gray-500">Rate Limit</span>
                        <span class="font-medium text-gray-800 dark:text-white">{{ config('mcp.rate_limit', 60) }}
                            req/min</span>
                    </div>
                    <div class="flex items-center justify-between text-sm py-2">
                        <span class="text-gray-500">Auth Method</span>
                        <span
                            class="uppercase font-medium text-gray-800 dark:text-white">{{ config('mcp.auth', 'sanctum') }}</span>
                    </div>
                </div>
            </div>
        </div>
</x-admin::layouts>