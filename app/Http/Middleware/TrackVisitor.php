<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\VisitorLog;

class TrackVisitor
{
    public function handle(Request $request, Closure $next)
    {
        $userAgent = $request->userAgent() ?? '';

        // Skip bots and crawlers
        $bots = ['bot', 'crawl', 'spider', 'slurp', 'wget', 'curl', 'facebook', 'twitter'];
        foreach ($bots as $bot) {
            if (stripos($userAgent, $bot) !== false) {
                return $next($request);
            }
        }

        try {
            $ip = $request->ip();
            $location = Http::timeout(3)->get("http://ip-api.com/json/{$ip}")->json();

            VisitorLog::create([
                'ip_address' => $ip,
                'country' => $location['country'] ?? null,
                'city' => $location['city'] ?? null,
                'region' => $location['regionName'] ?? null,
                'url' => $request->fullUrl(),
                'page_name' => $request->path(),
                'user_agent' => $userAgent,
                'browser' => $this->detectBrowser($userAgent),
                'os' => $this->detectOS($userAgent),
                'device_type' => $this->detectDevice($userAgent),
                'referer' => $request->headers->get('referer'),
            ]);
        } catch (\Exception $e) {
            \Log::warning('Visitor tracking failed: ' . $e->getMessage());
        }

        return $next($request);
    }
    
    private function detectBrowser($userAgent)
    {
        if (str_contains($userAgent, 'Chrome'))  return 'Chrome';
        if (str_contains($userAgent, 'Firefox')) return 'Firefox';
        if (str_contains($userAgent, 'Safari'))  return 'Safari';
        if (str_contains($userAgent, 'Edge'))    return 'Edge';
        if (str_contains($userAgent, 'Opera'))   return 'Opera';
        return 'Unknown';
    }

    private function detectOS($userAgent)
    {
        if (str_contains($userAgent, 'Windows')) return 'Windows';
        if (str_contains($userAgent, 'Mac'))     return 'MacOS';
        if (str_contains($userAgent, 'Linux'))   return 'Linux';
        if (str_contains($userAgent, 'Android')) return 'Android';
        if (str_contains($userAgent, 'iPhone') || str_contains($userAgent, 'iPad')) return 'iOS';
        return 'Unknown';
    }

    private function detectDevice($userAgent)
    {
        if (str_contains($userAgent, 'Mobi'))    return 'Mobile';
        if (str_contains($userAgent, 'Tablet') || str_contains($userAgent, 'iPad')) return 'Tablet';
        return 'Desktop';
    }
}

