<x-admin::layouts>
    <x-slot:title>
        @lang('mcp::app.admin.tools.title')
        </x-slot>

        <!-- Header with Logo -->
        <div class="flex items-center justify-between gap-4 max-sm:flex-wrap mb-6">
            <div class="flex items-center gap-4">
                <img src="{{ asset('vendor/mcp/images/logo.png') }}" alt="Bagisto MCP" class="h-10 w-auto" />
                <div>
                    <p class="text-xl font-bold text-gray-800 dark:text-white">
                        @lang('mcp::app.admin.tools.title')
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Enable or disable individual MCP tools
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

        <!-- Tools Table -->
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="border-b border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800">
                        <tr>
                            <th
                                class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                @lang('mcp::app.admin.tools.name')
                            </th>
                            <th
                                class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                @lang('mcp::app.admin.tools.description')
                            </th>
                            <th
                                class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 text-center">
                                @lang('mcp::app.admin.tools.auth-required')
                            </th>
                            <th
                                class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 text-center">
                                @lang('mcp::app.admin.tools.status')
                            </th>
                            <th
                                class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 text-center">
                                @lang('mcp::app.admin.tools.actions')
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($tools as $tool)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                <td class="px-6 py-4">
                                    <code
                                        class="rounded-md bg-gray-100 px-2.5 py-1 text-sm font-mono text-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                            {{ $tool['name'] }}
                                        </code>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-gray-600 dark:text-gray-400 max-w-xs">
                                        {{ Str::limit($tool['description'], 60) }}
                                    </p>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($tool['requires_auth'])
                                        <span
                                            class="inline-flex items-center gap-1 rounded-full bg-yellow-100 px-2.5 py-1 text-xs font-medium text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-200">
                                            <span class="h-1.5 w-1.5 rounded-full bg-yellow-500"></span>
                                            @lang('mcp::app.admin.tools.yes')
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                                            <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span>
                                            @lang('mcp::app.admin.tools.no')
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($tool['is_enabled'])
                                        <span
                                            class="inline-flex items-center gap-1 rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-800 dark:bg-green-900/50 dark:text-green-200">
                                            <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                                            @lang('mcp::app.admin.tools.enabled')
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1 rounded-full bg-red-100 px-2.5 py-1 text-xs font-medium text-red-800 dark:bg-red-900/50 dark:text-red-200">
                                            <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>
                                            @lang('mcp::app.admin.tools.disabled')
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <form action="{{ route('admin.mcp.tools.toggle', ['tool' => $tool['name']]) }}"
                                        method="POST" class="inline">
                                        @csrf
                                        <button type="submit"
                                            class="inline-flex items-center gap-1.5 rounded-lg px-4 py-2 text-sm font-medium transition-all {{ $tool['is_enabled'] ? 'bg-red-50 text-red-700 hover:bg-red-100 border border-red-200 dark:bg-red-900/30 dark:text-red-200 dark:border-red-800 dark:hover:bg-red-900/50' : 'bg-green-50 text-green-700 hover:bg-green-100 border border-green-200 dark:bg-green-900/30 dark:text-green-200 dark:border-green-800 dark:hover:bg-green-900/50' }}">
                                            @if($tool['is_enabled'])
                                                <span class="icon-cancel text-sm"></span>
                                            @else
                                                <span class="icon-done text-sm"></span>
                                            @endif
                                            {{ $tool['is_enabled'] ? __('mcp::app.admin.tools.disable') : __('mcp::app.admin.tools.enable') }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <span class="icon-product text-4xl text-gray-300 dark:text-gray-600"></span>
                                        <p class="text-gray-500 dark:text-gray-400">
                                            @lang('mcp::app.admin.tools.no-tools')
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
</x-admin::layouts>