<x-admin::layouts>
    <x-slot:title>
        @lang('mcp::app.admin.tools.edit-title', ['tool' => $tool['name']])
        </x-slot>

        <!-- Header -->
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.mcp.tools.index') }}">
                    <span class="icon-arrow-left text-2xl"></span>
                </a>
                <h1 class="text-xl font-bold text-gray-800 dark:text-white">
                    Edit Tool: {{ $tool['name'] }}
                </h1>
            </div>

            <button type="submit" form="tool-settings-form" class="primary-button">
                @lang('admin::app.save')
            </button>
        </div>

        <!-- Form -->
        <form id="tool-settings-form" action="{{ route('admin.mcp.tools.update', $tool['name']) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="flex gap-4 max-xl:flex-wrap">
                <!-- Left Panel -->
                <div class="flex flex-col gap-8 flex-1 max-xl:flex-auto">
                    <div class="p-4 bg-white dark:bg-gray-900 rounded-lg box-shadow">
                        <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                            @lang('mcp::app.admin.tools.configuration')
                        </p>

                        <div class="mb-4">
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                {{ $tool['description'] }}
                            </p>
                        </div>

                        <x-admin::form.control-group class="mb-4">
                            <x-admin::form.control-group.label class="required">
                                @lang('mcp::app.admin.tools.config-json')
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control type="textarea" name="config" :value="$tool['config']"
                                rows="10" class="font-mono text-sm" label="Configuration (JSON)"
                                placeholder='{"key": "value"}' />

                            <x-admin::form.control-group.error control-name="config" />
                        </x-admin::form.control-group>

                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            @lang('mcp::app.admin.tools.config-help')
                        </p>
                    </div>
                </div>

                <!-- Right Panel -->
                <div class="flex flex-col gap-2 w-[360px] max-w-full max-sm:w-full">
                    <div class="bg-white dark:bg-gray-900 rounded-lg box-shadow">
                        <div class="p-4">
                            <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                                @lang('mcp::app.admin.tools.status')
                            </p>

                            <div class="flex items-center justify-between">
                                <label class="text-gray-800 dark:text-white font-medium">
                                    Enabled
                                </label>

                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_enabled" value="1" class="sr-only peer" {{ $tool['is_enabled'] ? 'checked' : '' }}>
                                    <div
                                        class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
</x-admin::layouts>