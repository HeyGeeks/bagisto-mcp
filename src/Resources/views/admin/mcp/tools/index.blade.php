<x-admin::layouts>
    <x-slot:title>
        @lang('mcp::app.admin.tools.title')
        </x-slot>

        <div class="flex items-center justify-between mb-4">
            <h1 class="text-xl font-bold text-gray-800 dark:text-white">
                @lang('mcp::app.admin.tools.title')
            </h1>
        </div>

        <div class="bg-white dark:bg-gray-900 rounded-lg box-shadow">
            <div class="table-responsive grid w-full box-shadow rounded bg-white dark:bg-gray-909 overflow-hidden">
                <x-admin::table>
                    <x-admin::table.thead>
                        <x-admin::table.thead.tr>
                            <x-admin::table.th>Name</x-admin::table.th>
                            <x-admin::table.th>Code</x-admin::table.th>
                            <x-admin::table.th>Auth</x-admin::table.th>
                            <x-admin::table.th>Status</x-admin::table.th>
                            <x-admin::table.th class="text-right">Actions</x-admin::table.th>
                        </x-admin::table.thead.tr>
                    </x-admin::table.thead>

                    <x-admin::table.tbody>
                        @forelse($tools as $tool)
                            <x-admin::table.tbody.tr>
                                <!-- Name & Description -->
                                <x-admin::table.td>
                                    <p class="text-gray-800 dark:text-white font-semibold">
                                        {{ ucwords(str_replace('.', ' ', $tool['name'])) }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ Str::limit($tool['description'] ?? 'No description', 60) }}
                                    </p>
                                </x-admin::table.td>

                                <!-- Code -->
                                <x-admin::table.td>
                                    <code class="bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded text-xs">
                                        {{ $tool['name'] }}
                                    </code>
                                </x-admin::table.td>

                                <!-- Auth Status -->
                                <x-admin::table.td>
                                    @if($tool['requires_auth'])
                                        <span class="label-pending">Protected</span>
                                    @else
                                        <span class="label-active">Public</span>
                                    @endif
                                </x-admin::table.td>

                                <!-- Enabled Status -->
                                <x-admin::table.td>
                                    @if($tool['is_enabled'])
                                        <span class="badge badge-lg badge-success">Enabled</span>
                                    @else
                                        <span class="badge badge-lg badge-danger">Disabled</span>
                                    @endif
                                </x-admin::table.td>

                                <!-- Actions -->
                                <x-admin::table.td class="text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <form action="{{ route('admin.mcp.tools.toggle', ['tool' => $tool['name']]) }}"
                                            method="POST">
                                            @csrf
                                            <button type="submit"
                                                class="cursor-pointer p-2 hover:bg-gray-100 dark:hover:bg-gray-900 rounded-md transition-all"
                                                title="Toggle Status">
                                                @if($tool['is_enabled'])
                                                    <span class="icon-cancel text-2xl"></span>
                                                @else
                                                    <span class="icon-done text-2xl"></span>
                                                @endif
                                            </button>
                                        </form>

                                        <a href="{{ route('admin.mcp.tools.edit', $tool['name']) }}"
                                            class="cursor-pointer p-2 hover:bg-gray-100 dark:hover:bg-gray-900 rounded-md transition-all"
                                            title="Edit Configuration">
                                            <span class="icon-edit text-2xl"></span>
                                        </a>
                                    </div>
                                </x-admin::table.td>
                            </x-admin::table.tbody.tr>
                        @empty
                            <x-admin::table.tbody.tr>
                                <x-admin::table.td colspan="5" class="text-center py-8 text-gray-500">
                                    No tools found.
                                </x-admin::table.td>
                            </x-admin::table.tbody.tr>
                        @endforelse
                    </x-admin::table.tbody>
                </x-admin::table>
            </div>
        </div>
</x-admin::layouts>