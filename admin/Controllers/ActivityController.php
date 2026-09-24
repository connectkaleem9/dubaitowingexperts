<?php

declare(strict_types=1);

namespace Admin\Controllers;

use App\Core\Request;
use App\Models\ActivityLog;

final class ActivityController extends AdminController
{
    public function index(Request $request): void
    {
        $page = $this->page($request);
        $action = $request->query('action');
        $result = ActivityLog::paginate($page, 50, $action);
        $this->view('activity/index', [
            'title' => 'Activity Logs',
            'items' => $result['items'],
            'pager' => ['page' => $page, 'pages' => max(1, (int) ceil($result['total'] / 50))],
            'action' => $action,
            'actionList' => ActivityLog::actions(),
        ]);
    }
}
