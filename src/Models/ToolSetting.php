<?php

namespace HeyGeeks\BagistoMCP\Models;

use Illuminate\Database\Eloquent\Model;

class ToolSetting extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mcp_tool_settings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'tool_name',
        'is_enabled',
        'config',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_enabled' => 'boolean',
        'config' => 'array',
    ];

    /**
     * Check if a tool is enabled.
     *
     * @param  string  $toolName
     * @return bool
     */
    public static function isEnabled(string $toolName): bool
    {
        $setting = static::where('tool_name', $toolName)->first();

        // If no setting exists, tool is enabled by default
        if (!$setting) {
            return true;
        }

        return $setting->is_enabled;
    }

    /**
     * Toggle a tool's enabled status.
     *
     * @param  string  $toolName
     * @return bool  The new enabled status
     */
    public static function toggle(string $toolName): bool
    {
        $setting = static::firstOrCreate(
            ['tool_name' => $toolName],
            ['is_enabled' => true]
        );

        $setting->is_enabled = !$setting->is_enabled;
        $setting->save();

        return $setting->is_enabled;
    }

    /**
     * Enable a tool.
     *
     * @param  string  $toolName
     * @return void
     */
    public static function enable(string $toolName): void
    {
        static::updateOrCreate(
            ['tool_name' => $toolName],
            ['is_enabled' => true]
        );
    }

    /**
     * Disable a tool.
     *
     * @param  string  $toolName
     * @return void
     */
    public static function disable(string $toolName): void
    {
        static::updateOrCreate(
            ['tool_name' => $toolName],
            ['is_enabled' => false]
        );
    }

    /**
     * Get tool configuration.
     *
     * @param  string  $toolName
     * @return array|null
     */
    public static function getConfig(string $toolName): ?array
    {
        $setting = static::where('tool_name', $toolName)->first();

        return $setting?->config;
    }

    public static function enabledCount(): int
    {
        return static::where('is_enabled', true)->count();
    }
}
