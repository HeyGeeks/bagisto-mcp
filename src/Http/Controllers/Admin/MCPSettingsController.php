<?php

namespace HeyGeeks\BagistoMCP\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Config;

class MCPSettingsController extends Controller
{
    /**
     * Display the settings form.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('mcp::admin.mcp.settings.index', [
            'enabled' => config('mcp.enabled', true),
            'endpoint' => config('mcp.endpoint', 'mcp'),
            'rateLimit' => config('mcp.rate_limit', 60),
            'authMethod' => config('mcp.auth', 'sanctum'),
        ]);
    }

    /**
     * Save the settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'enabled' => 'required|boolean',
            'endpoint' => 'required|string|max:100',
            'rate_limit' => 'required|integer|min:1|max:1000',
            'auth_method' => 'required|in:sanctum,custom',
        ]);

        // Update the config file
        $configPath = config_path('mcp.php');

        if (file_exists($configPath)) {
            $config = include $configPath;

            $config['enabled'] = (bool) $request->input('enabled');
            $config['endpoint'] = $request->input('endpoint');
            $config['rate_limit'] = (int) $request->input('rate_limit');
            $config['auth'] = $request->input('auth_method');

            $content = "<?php\n\nreturn " . var_export($config, true) . ";\n";
            file_put_contents($configPath, $content);

            // Clear config cache
            if (function_exists('opcache_reset')) {
                opcache_reset();
            }
        }

        session()->flash('success', trans('mcp::app.admin.settings.save-success'));

        return redirect()->route('admin.mcp.settings.index');
    }
}
