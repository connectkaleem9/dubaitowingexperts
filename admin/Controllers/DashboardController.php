<?php

declare(strict_types=1);

namespace Admin\Controllers;

use App\Core\Request;
use App\Models\Lead;
use App\Models\Project;
use App\Models\Review;

final class DashboardController extends AdminController
{
    /** Signing in lands on Projects, the first item in the trimmed menu. */
    public function home(Request $request): void
    {
        redirect('/admin/projects/');
    }

    public function index(Request $request): void
    {
        $leads = Lead::countByStatus();
        $this->view('dashboard', [
            'title' => 'Dashboard',
            'projects' => Project::countByStatus(),
            'reviews' => Review::countByStatus(),
            'leads' => $leads,
            'leadsToday' => Lead::countSince(date('Y-m-d 00:00:00')),
            'leadsWeek' => Lead::countSince(date('Y-m-d 00:00:00', strtotime('-6 days'))),
            'recentLeads' => Lead::recent(8),
            'setupWarnings' => $this->setupWarnings(),
        ]);
    }

    /** Things the owner still needs to configure before launch. */
    private function setupWarnings(): array
    {
        $w = [];
        if (!preg_match('/^GTM-/', (string) setting('gtm_id', ''))) {
            $w[] = ['Google Tag Manager is not set up, so calls, WhatsApp clicks and forms are not being tracked.', '/admin/settings/'];
        }
        if ((string) setting('notify_email', '') === '' && (string) config('app.mail_to') === '') {
            $w[] = ['No notification email is set — new leads are saved here but you will not be emailed.', '/admin/settings/'];
        }
        if (config('app.debug') && config('app.env') === 'production') {
            $w[] = ['APP_DEBUG is on in production. Turn it off in .env.', null];
        }
        if (config('app.key') === '') {
            $w[] = ['APP_KEY is empty in .env. Set a random 64-character value.', null];
        }
        return $w;
    }
}
