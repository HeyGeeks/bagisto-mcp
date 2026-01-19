<x-admin::layouts>
    <x-slot:title>
        @lang('mcp::app.admin.settings.title')
        </x-slot>

        <!-- Header with Logo -->
        <div class="flex items-center justify-between gap-4 max-sm:flex-wrap mb-6">
            <div class="flex items-center gap-4">
                <img src="{{ asset('vendor/mcp/images/logo.png') }}" alt="Bagisto MCP" class="h-10 w-auto" />
                <div>
                    <p class="text-xl font-bold text-gray-800 dark:text-white">
                        @lang('mcp::app.admin.settings.title')
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Configure MCP server options
                    </p>
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        @if (session('success'))
            <div
                class="mb-4 flex items-center gap-3 rounded-lg bg-green-50 border border-green-200 p-4 text-green-800 dark:bg-green-900/30 dark:border-green-800 dark:text-green-200">
                <span class="icon-done text-xl"></span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div
                class="mb-4 flex items-center gap-3 rounded-lg bg-red-50 border border-red-200 p-4 text-red-800 dark:bg-red-900/30 dark:border-red-800 dark:text-red-200">
                <span class="icon-cancel text-xl"></span>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Settings Form -->
        <form action="{{ route('admin.mcp.settings.store') }}" method="POST">
            @csrf

            <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                <!-- Section Header -->
                <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
                        @lang('mcp::app.admin.settings.general')
                    </h3>
                </div>

                <!-- Form Fields -->
                <div class="p-6">
                    <div class="grid gap-6 lg:grid-cols-2">
                        <!-- Server Enabled -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                @lang('mcp::app.admin.settings.server-enabled')
                            </label>
                            <select name="enabled"
                                class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-700 transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                                <option value="1" {{ $enabled ? 'selected' : '' }}>
                                    @lang('mcp::app.admin.settings.yes')
                                </option>
                                <option value="0" {{ !$enabled ? 'selected' : '' }}>
                                    @lang('mcp::app.admin.settings.no')
                                </option>
                            </select>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                @lang('mcp::app.admin.settings.server-enabled-help')
                            </p>
                        </div>

                        <!-- Endpoint URL -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                @lang('mcp::app.admin.settings.endpoint')
                            </label>
                            <div class="flex">
                                <span
                                    class="inline-flex items-center rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 px-4 text-sm text-gray-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                    /
                                </span>
                                <input type="text" name="endpoint" value="{{ $endpoint }}" placeholder="mcp"
                                    class="w-full rounded-r-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-700 transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-800 dark:text-white" />
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                @lang('mcp::app.admin.settings.endpoint-help')
                            </p>
                        </div>

                        <!-- Rate Limit -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                @lang('mcp::app.admin.settings.rate-limit')
                            </label>
                            <div class="flex">
                                <input type="number" name="rate_limit" value="{{ $rateLimit }}" min="1" max="1000"
                                    class="w-full rounded-l-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-700 transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-800 dark:text-white" />
                                <span
                                    class="inline-flex items-center rounded-r-lg border border-l-0 border-gray-300 bg-gray-50 px-4 text-sm text-gray-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                    /min
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                @lang('mcp::app.admin.settings.rate-limit-help')
                            </p>
                        </div>

                        <!-- Auth Method -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                @lang('mcp::app.admin.settings.auth-method')
                            </label>
                            <select name="auth_method"
                                class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-700 transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                                <option value="sanctum" {{ $authMethod === 'sanctum' ? 'selected' : '' }}>
                                    Laravel Sanctum
                                </option>
                                <option value="custom" {{ $authMethod === 'custom' ? 'selected' : '' }}>
                                    Custom
                                </option>
                            </select>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                @lang('mcp::app.admin.settings.auth-method-help')
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Form Footer -->
                <div class="flex justify-end border-t border-gray-200 px-6 py-4 dark:border-gray-700">
                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-medium text-white transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500/50">
                        <span class="icon-save text-lg"></span>
                        @lang('mcp::app.admin.settings.save')
                    </button>
                </div>
            </div>
        </form>
</x-admin::layouts>