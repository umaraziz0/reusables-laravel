<?php

declare(strict_types=1);

namespace App\Enums;

use App\Models\User;
use App\Traits\BaseEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;

/**
 * Operator dashboards served by this application.
 *
 * Admin tools need inline scripts that the strict Content-Security-Policy blocks,
 * must not be sampled by Nightwatch, and are only visible locally or to superadmins.
 */
enum AdminTool: string
{
    use BaseEnum;

    case ActivityLogUi = 'activitylog-ui';
    case ApiDocs       = 'api-docs';
    case EmailPreviews = 'email-previews';
    case Health        = 'health';
    case Horizon       = 'horizon';
    case LaravelBrain  = 'laravel-brain';
    case LogViewer     = 'log-viewer';
    case Pulse         = 'pulse';
    case Telescope     = 'telescope';
    case WebTinker     = 'web-tinker';

    /**
     * Find the admin tool serving the request, if any.
     */
    public static function forRequest(Request $request): ?self
    {
        foreach (self::cases() as $tool) {
            $path = $tool->path();

            if ($path !== '' && $request->is($path, "{$path}/*")) {
                return $tool;
            }
        }

        return null;
    }

    public static function canView(?User $user): bool
    {
        return app()->isLocal() || ($user?->hasRole(Role::SUPERADMIN->value) ?? false);
    }

    /**
     * Define the access gate of every admin tool that authorizes through one.
     */
    public static function defineGates(): void
    {
        foreach (self::cases() as $tool) {
            $gate = $tool->gate();

            if ($gate !== null) {
                Gate::define($gate, self::canView(...));
            }
        }
    }

    /**
     * The URL path prefix the tool is served under, without surrounding slashes.
     */
    public function path(): string
    {
        $path = match ($this) {
            self::ActivityLogUi => Config::string('activitylog-ui.route.prefix'),
            self::ApiDocs       => 'docs',
            self::EmailPreviews => 'emails',
            self::Health        => 'health',
            self::Horizon       => Config::string('horizon.path'),
            self::LaravelBrain  => '_laravel-brain',
            self::LogViewer     => Config::string('log-viewer.route_path'),
            self::Pulse         => Config::string('pulse.path'),
            self::Telescope     => Config::string('telescope.path'),
            self::WebTinker     => Config::string('web-tinker.path'),
        };

        return mb_trim($path, '/');
    }

    /**
     * The gate the tool's package checks, or null when the tool is guarded by route middleware or its own config.
     */
    public function gate(): ?string
    {
        return match ($this) {
            self::ActivityLogUi => 'viewActivityLogUi',
            self::Horizon       => 'viewHorizon',
            self::LogViewer     => 'viewLogViewer',
            self::Pulse         => 'viewPulse',
            self::Telescope     => 'viewTelescope',
            self::ApiDocs,
            self::EmailPreviews,
            self::Health,
            self::LaravelBrain,
            self::WebTinker => null,
        };
    }
}
